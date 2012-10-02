#!/usr/bin/env python
## ---------------------------------------------------------------------------
## This software is in the public domain, furnished "as is", without technical
## support, and with no warranty, express or implied, as to its usefulness for
## any purpose.
## ---------------------------------------------------------------------------
##
## PURPOSE:
##
##   This is a python program that links CAPS hurricane forecast images.
##
## ---------------------------------------------------------------------
##
## REQUIREMENT:
##
########################################################################

import os, sys, getopt
import re, time
import subprocess

##======================================================================
## USAGE
##======================================================================

def usage( istatus = 0 ) :
  '''-------------------------------------------------------------------
  Print Usage and then exit
  -------------------------------------------------------------------'''
  print >> sys.stderr, '''
    \tUsage: %s [option] dates\n
    \t\tdate\t Date string to be checked for new file, for example 20100906
    \n\tOPTIONs:\n
    \t\tOption \t\tDefault \tInstruction
    \t\t-------\t\t--------\t-----------
    \t\t-h, --help \t\t \tPrint this help
    \t\t-v, --verbose\tNone \t\tVerbose
    \t\t-d, --debug\tNone \t\tVerbose
    \t\t-c, --chsrc\tNone \t\tChange image src
    \t\t-r, --range\t0 \t\tDate range in days to be checked
    \t\t           \t>0\t\tLook forward
    \t\t           \t<0\t\tLook backward
    \t\t           \t\tNOTE: \tApply only when one date string is given
    ''' % (cmd)
  sys.exit(istatus)
#enddef

##======================================================================
## Parse command line arguments
##======================================================================
def parseArgv(argv) :
  '''-------------------------------------------------------------------
  Parse command line arguments
  -------------------------------------------------------------------'''
  global _debug, _runmod, srcdir

  _range = 0
  try:
    opts, args = getopt.getopt(argv,'hvdc:r:',           \
           ['help','verbose','debug','chsrc','range'])

    for opt, arg in opts :
      if opt in ('-h','--help'):
        usage(0)
      elif opt in ( '-v', '--verbose'):
        _debug = 1
      elif opt in ( '-d', '--debug'):
        _debug = 1
      elif opt in ( '-c', '--chsrc'):
        srcdir = arg
        _runmod = 1
      elif opt in ( '-r', '--range'):
        _range = int(arg)

  except getopt.GetoptError:
    print >> sys.stderr, 'ERROR: Unknown option.'
    usage(2)

  ##--------------------------------------------------------------------
  ## Check date strings
  ##--------------------------------------------------------------------

  dateRe = re.compile(r'(\d\d\d\d)(\d\d)(\d\d)')

  dateargs = []
  for datestrin in args :
    dayMatch = dateRe.match(datestrin)
    if dayMatch :
      dateargs.append(datestrin)
    elif (datestrin == 'now') :
      (curryear,currmon,currday,currh,currm,currs,currw,curry,curri) = time.gmtime()
      dateargs.append('%4d%02d%02d'%(curryear,currmon,currday))
    else :
      print >> sys.stderr, 'WARNING: Unknown argument - %s, ignored.' % datestrin


  ##--------------------------------------------------------------------
  ## Date string range only when one date string is provided
  ##--------------------------------------------------------------------
  if abs(_range) and len(dateargs) == 1 > 0 :
    datestrin = dateargs[0]

    asgn = 1
    if _range < 0 : asgn = -1
    arange = abs(_range)
    if arange > 0 :
      currtime = time.strptime(datestrin+' UTC','%Y%m%d %Z')
      currsec  = time.mktime(currtime)
      currsec -= time.timezone             ## convert to GMT time in seconds

      for i in range(1,arange+1) :
        newsec = currsec+ asgn * i * 3600 * 24    ## range in days
        newtime = time.gmtime(newsec)
        newstr  = time.strftime('%Y%m%d',newtime)
        dateargs.append(newstr)

  if len(dateargs) < 1 :
    print >> sys.stderr, 'ERROR: wrong number of command line arguments.'
    usage(1)

  return dateargs
#enddef

##======================================================================
## MAIN program here
##======================================================================
def main(datestrs,sourcebase) :
  '''-------------------------------------------------------------------
  Links images from "sourcebase" to "casedir"
  sourcebase = '/import/arpsdata2/www2/wx/p/'
  casedir    = data/20110822/gfs00Z   &  data/20110822/gfs00Z_big

  -------------------------------------------------------------------'''

  ##sourcebase = '/import/arpsdata/www/wx/p/'                  ## fixed source directory
  ##sourcebase = '/import/arpsdata2/www2/wx/p/'                ## fixed source directory
  dataBase = '/vol0/www/html/forecast/ywang/hurricane2/data/'   ## fixed destination
  cases   = ("gfs00Z","gfs12Z","ekf00Z","ekf12Z")              ## Case names
  ##casesrc = ('hurr1','hurr1','hurr2','hurr2')                ## corresponding source subdirectory
  casesrc = ('GSIA','GSIA','EnKFA','EnKFA')           ## corresponding source subdirectory
  ##casesrc = ('atl_hurr_gfs2012','atl_hurr_gfs2012','atl_hurr_enkf2012','atl_hurr_enkf2012')           ## corresponding source subdirectory
  fields = ("streamline", "wspd", "500_rh","slpcrf")           ## Fields to be shown

  ##--------------------------------------------------------------------
  ## Process date in the datestr array
  ##--------------------------------------------------------------------
  new_data = 0
  for datestr in datestrs :
    currday = int(datestr[-2:])
    datedir = dataBase+datestr
    if not os.path.exists(datedir) :
      os.mkdir(datedir)
    sourcedir = sourcebase+datestr+'/r/'

    n = 0
    for case in cases :
      print '\n------------------ CASE %s %s -----------------------'%(datestr,case)
      casedir = datedir+'/'+case
      if not os.path.exists(casedir):
        os.mkdir(casedir)
      fieldsrc = sourcedir + casesrc[n]
      fhour = int(case[-3:-1])      ## decode forecast hour from case name
      for field in fields :
        fielddir = fieldsrc+'/wrf_%02d.%02d00Z'%(currday,fhour)+'/anim/'+field
        if (_debug) : print 'fielddir = %s\n'%(fielddir)
        if os.path.isdir(fielddir) :
          i = 0
          for file in os.listdir(fielddir) :
            fieldRe = re.compile(field+'(\d+).png')
            fileMatch = fieldRe.match(file)
            if fileMatch :
              order = int(fileMatch.group(1))
              destfile = casedir+'/'+field+'%02d.png'%order
              if not os.path.exists(destfile) :
                if (_debug) : print 'Linking %s ...' % file
                os.symlink(fielddir+'/'+file,destfile)
                i = i+1
              else :
                if (_debug) : print 'File (%s) already exist.' % (destfile)
            else :
              if (_debug) : print 'file (%s) does not match.'%(file)
          print 'Totally %d file(s) linked for %s.'%(i,field)
          if (i > 0) : new_data += 1
        else :
          if (_debug) : print 'fielddir (%s) does not exist.'%(fielddir)

      if not os.listdir(casedir) :   # If directory is empty, removed it
        os.rmdir(casedir)
      else :
        if not os.path.exists(casedir+'_big'):
          os.mkdir(casedir+'_big')
        fieldsrc = sourcedir + casesrc[n]
        fhour = int(case[-3:-1])      # decode forecast hour from case name
        for field in fields :
          fielddir = fieldsrc+'/wrf_%02d.%02d00Z'%(currday,fhour)+'/anim/'+field+'_big'
          if os.path.isdir(fielddir) :
            i = 0
            for file in os.listdir(fielddir) :
              fieldRe = re.compile(field+'_big(\d+).png')
              fileMatch = fieldRe.match(file)
              if fileMatch :
                order = int(fileMatch.group(1))
                destfile = casedir+'_big/'+field+'%02d.png'%order
                if not os.path.lexists(destfile) :
                  if (_debug) : print 'Linking %s ...' % file
                  os.symlink(fielddir+'/'+file,destfile)
                  i = i+1
            print 'Totally %d file(s) linked for %s_big'%(i,field)

      n = n+1

    if not os.listdir(datedir) :   # delete empty date directory
      os.rmdir(datedir)

  if new_data > 0 :
    print '\n--------------- Executing preprocessor.php ...'
    subprocess.Popen(['php','preprocessor.php'],cwd='/vol0/www/html/forecast/ywang/hurricane2/').wait()

  return
#enddef

##======================================================================
## change symbolic link source
##======================================================================
def change_src(datestrin,srcnew) :
  '''-------------------------------------------------------------------

  change the symbolic link source to "srcnew" for case "datestrin"

  -------------------------------------------------------------------'''

  dateRe = re.compile(r'(\d\d\d\d)(\d\d)(\d\d)')
  dayMatch = dateRe.match(datestrin)
  if dayMatch :
    (curryear,currmon,currday) = dayMatch.group(1,2,3)
    curryear = int(curryear)
    currmon  = int(currmon)
    currday  = int(currday)
  else :
    print >> sys.stderr, 'ERROR: Wrong argument datestrin = %s.\n' % datestrin
    return

  dataBase = '/vol0/www/html/forecast/ywang/hurricane/data/'   ## fixed destination
  cases    = ("gfs00Z","gfs12Z","ekf00Z","ekf12Z")             ## Case names
  #casesrc = ('hurr1','hurr1','hurr2','hurr2')                 ## Old source subdirectory in 2010
  casesrc = ('atl_hurr_gfs','atl_hurr_gfs','atl_hurr_enkf','atl_hurr_enkf')
                                                               ## corresponding source subdirectory
  fields  = ("streamline", "wspd", "500_rh","slpcrf")          ## Field images to be linked

  datestr = '%04d%02d%02d' % (curryear, currmon, currday)

  new_data = 0
  currday = int(datestr[-2:])
  datedir = dataBase+datestr
  if not os.path.exists(datedir) :
    print >> sys.stderr, 'ERROR: Datedir = %s does not exist.\n' % datedir
    return

  sourcedir = srcnew+datestr+'/r/'

  n = 0
  for case in cases :
    casedir = datedir+'/'+case
    if not os.path.exists(casedir):
      print >> sys.stderr, 'WARNING: casedir = %s does not exist.\n' % casedir
      n = n+1
      continue

    fieldsrc = sourcedir + casesrc[n]
    fhour = int(case[-3:-1])      # decode forecast hour from case name
    for field in fields :
      fielddir = fieldsrc+'/wrf_%02d.%02d00Z'%(currday,fhour)+'/anim/'+field
      ##print 'fielddir = %s\n'%(fielddir)
      if os.path.isdir(fielddir) :
        i = 0
        j = 0
        for nfile in os.listdir(fielddir) :
          fieldRe = re.compile(field+'(\d+).png')
          fileMatch = fieldRe.match(nfile)
          if fileMatch :
            order = int(fileMatch.group(1))
            destfile = casedir+'/'+field+'%02d.png'%order
            if os.path.exists(destfile) :
              os.unlink(destfile)
              j = j+1
            else :
              print >> sys.stderr, 'WARNING: %s not exist.' % destfile
            if (_debug) : print 'Linking %s ...' % nfile
            os.symlink(fielddir+'/'+nfile,destfile)
            i = i+1

        print 'Totally %d file(s) linked (%d new) for %s of \t\t %s %s'%(i,j,field,datestr,case)
        if (j > 0) : new_data += 1
      else:
        print >> sys.stderr, 'WARNING: %s not exist.\n' % fielddir

    if os.listdir(casedir) :   ## If directory is not empty
      if not os.path.exists(casedir+'_big'):
        print >> sys.stderr, 'WARNING: %s_big not exist. Creating ...' % casedir
        os.mkdir(casedir+'_big')

      fieldsrc = sourcedir + casesrc[n]
      fhour = int(case[-3:-1])      # decode forecast hour from case name
      for field in fields :
        fielddir = fieldsrc+'/wrf_%02d.%02d00Z'%(currday,fhour)+'/anim/'+field+'_big'
        if os.path.isdir(fielddir) :
          i = 0
          j = 0
          for nfile in os.listdir(fielddir) :
            fieldRe = re.compile(field+'_big(\d+).png')
            fileMatch = fieldRe.match(nfile)
            if fileMatch :
              order = int(fileMatch.group(1))
              destfile = casedir+'_big/'+field+'%02d.png'%order
              if os.path.exists(destfile) :
                os.unlink(destfile)
                j = j+1
              else :
                print >> sys.stderr, 'WARNING: %s not exist.' % destfile
              if (_debug) : print 'Linking %s ...' % nfile
              os.symlink(fielddir+'/'+nfile,destfile)
              i = i+1
          print 'Totally %d file(s) linked (%d new) for %s of %s %s'%(i,j,field,datestr,case)

    n = n+1

  if new_data > 0 :
    if (_debug) : print 'Executing preprocessor.php ...'
    subprocess.Popen(['php','preprocessor.php'],cwd='/vol0/www/html/forecast/ywang/hurricane/').wait()

  return
#enddef

##======================================================================
## Generate list files
##======================================================================
def genfilelist(datestrin) :
  '''-------------------------------------------------------------------
  Generate image file list for each field in the case directory, for
  example, 20110822/gfs00Z/wspd.list.

  It is obsolete now.
  -------------------------------------------------------------------'''

  dateRe = re.compile(r'(\d\d\d\d)(\d\d)(\d\d)')
  dayMatch = dateRe.match(datestrin)
  if dayMatch :
    (curryear,currmon,currday) = dayMatch.group(1,2,3)
    curryear = int(curryear)
    currmon  = int(currmon)
    currday  = int(currday)
  else :
    (curryear,currmon,currday,currh,currm,currs,currw,curry,curri) = time.gmtime()

  webbase = '/vol0/www/html/forecast'                   ##  Web site base dir
  dataBase = '/vol0/www/html/forecast/ywang/hurricane/data/'
                                                        ## fixed destination
  webdir = '/ywang/hurricane/data'                      ## Web absolute path

  cases   = ("gfs00Z","gfs12Z","ekf00Z","ekf12Z")       ## Case names
  fields = ("wspd", "500_rh","slpcrf","streamline","comref","maxspd","maxsfc")
                                                        ## Fields to be processed

  datestr = '%04d%02d%02d' % (curryear, currmon, currday)

  datedir = dataBase+datestr
  if not os.path.exists(datedir) :
    return

  n = 0
  for case in cases :
    casedir = datedir+'/'+case
    if not os.path.exists(casedir):
      continue

    for field in fields :
      i = 0
      lists = []
      for imgfile in os.listdir(casedir) :
        fieldRe = re.compile(field+'(\d+).png')
        fileMatch = fieldRe.match(imgfile)
        if fileMatch :
          destfilename = os.path.join(webdir,datestr,case,imgfile)
          lists.append(destfilename)
          if (_debug) : print 'Adding %s ...' % imgfile
          i = i+1
      print 'Totally %d file(s) will be added to file list for %s on %s'%(i,case,datestr)

      lists.sort()
      filelist = '%s.list' % field
      fh = open(os.path.join(casedir,filelist),'w')
      for imgfile in lists :
        fh.write(imgfile+'\n')
      fh.close()
      if (i <= 0) :
        os.unlink(os.path.join(casedir,filelist))
    n += 1

  return
#enddef

########################################################################
## Entance Point
########################################################################
if (__name__ == '__main__') :
#-----------------------------------------------------------------------
# Defined global variables
#
# cmd
# args
# _debug
#
#-----------------------------------------------------------------------
  _debug  = 0
  _runmod = 0
  srcdir = '/import/arpsdata/www/wx/p/'             ## fixed source directory

  cmd  = sys.argv[0]
  args = parseArgv(sys.argv[1:])
  if _runmod == 0 :
    main(args,srcdir)
  elif _runmod == 1 :
    change_src(args[0],srcdir)
  else :
    print >> sys.stderr, 'ERROR: Wrong runmod or command line arguments.\n'
