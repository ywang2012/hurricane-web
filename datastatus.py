#!/home/ywang/Python-2.7.3/bin/python
## ---------------------------------------------------------------------------
## This software is in the public domain, furnished "as is", without technical
## support, and with no warranty, express or implied, as to its usefulness for
## any purpose.
## ---------------------------------------------------------------------------
##
## PURPOSE:
##
##   Monitor job status
##
## HISTORY:
##   Yunheng Wang (10/03/2012)
##   Improved with option "-n" and convenience of changeing number of
##   processors.
##
## ---------------------------------------------------------------------
##
## REQUIREMENT:
##
########################################################################

import os, sys, getopt, re, time
import subprocess, sqlite3

##======================================================================
## USAGE
##======================================================================
def usage( istatus = 0 ) :
  '''-------------------------------------------------------------------
  Print Usage and then exit
  -------------------------------------------------------------------'''
  print >> sys.stderr, '''
    Usage: %s [option] [YYYYMMDD] [CASEhhZ] [FIELDS]\n
    \tYYYYMMDD\t Date to be processed, for example 20100915
    \tCASE\t Case to be processed. Valid are 'gfs' or 'ekf'
    \thhZ\t Time to be processed, for example, 00Z or 12Z
    \tFIELDS\t FIELD to be checked, for example, slpcrf, comref etc
    OPTIONs:\n
    \tOption \t\tDefault \tInstruction
    \t-------\t\t--------\t-----------
    \t-d, --verbose\tNone\t\tVerbose
    \t-h, --help         \t\tPrint this help
    \t-r, --range\t0 \t\tDate range in days to be checked
    \t           \t>0\t\tLook forward
    \t           \t<0\t\tLook backward
    \t           \t\tNOTE: \tApply only when one date string is given
    \t-p, --print\t \t\tDo not print to standard output, but
    \t           \t\t \tmodify database directly
    \t-l, --link2d\t \t\tLink 2d data
    \t-k, --link3d\t \t\tDo not link 3d data
    ''' % cmd
  sys.exit(istatus)
#enddef

##======================================================================
## Parse command line arguments
##======================================================================
def parseArgv(argv) :
  '''-------------------------------------------------------------------
  Parse command line arguments
  -------------------------------------------------------------------'''
  global _debug, _range, _print

  _range = -8

  try:
    opts, args = getopt.getopt(argv,'hdr:plk',           \
           ['help','verbose','range','print','link2d','link3d'])

    for opt, arg in opts :
      if opt in ('-h','--help'):
        usage(0)
      elif opt in ( '-d', '--verbose'):
        _debug = True
      elif opt in ( '-r', '--range'):
        _range = int(arg)
      elif opt in ( '-p', '--print'):
        _print = True
      elif opt in ( '-l', '--link2d'):
        _link2d = True
      elif opt in ( '-k', '--link3d'):
        _link3d = False

  except getopt.GetoptError:
    print >> sys.stderr, 'ERROR: Unknown option.'
    usage(2)

  argvs = {'dates' : [], 'cases' : [], 'fields' : [] }
  def_date  = False
  def_case  = False
  def_field = False
  if ( len(args) >=  1 ) :
    ## get date argument
    dates = args[0].split(',')
    for date in dates :
      oneargs = re.match('(\d{4}\d{2}\d{2})',date)
      if oneargs:
        date = oneargs.group(1)
        argvs['dates'].append( date )
      else :
        print >> sys.stderr, 'Argument %s is in wrong format.' % (args[0])
        usage(1)

    ## get case argument
    if len(args) >= 2 :
      cases = args[1].split(',')
      for case in cases :
        oneargs = re.match('(gfs|ekf)(00Z|12Z)',case)
        if oneargs:
          case = ''.join(oneargs.group(1,2))
          argvs['cases'].append( case )
        else :
          print >> sys.stderr, 'Argument %s is in wrong format.' % (args[0])
          usage(1)
    else :
      def_case = True

    ## get field argument
    if len(args) >= 3 :
      argvs['fields'] = args[2].split(',')
    else :
      def_field = True

  else :
    def_date = True
    def_case = True
    def_field = True

  if def_date :
    (curryear,currmon,currday,currh,currm,currs,currw,curry,curri) = time.gmtime()
    argvs['dates'].append('%4d%02d%02d'%(curryear,currmon,currday))

  if def_case :
    argvs['cases'] = CONST_CASES

  if def_field :
    argvs['fields'] = CONST_3DFIELDS + CONST_2DFIELDS

  if len(argvs['dates']) > 1 :
    _range = 0
  else :

    datestrin = argvs['dates'][0]

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
        argvs['dates'].append(newstr)

  return argvs

#enddef
##======================================================================
## MAIN program here
##======================================================================
def main(dates,cases,fields) :

  if not _print :
    dbcon = sqlite3.connect(DATABASE)
    dbcur = dbcon.cursor()

  for date in dates :

    if _print :
      print "\n%s ==="%date
    else :
      dmatch = re.match('(\d{4})(\d{2})(\d{2})',date)
      dbdstr = "%s-%s-%s" % (dmatch.group(1),dmatch.group(2),dmatch.group(3))

    if _link3d or _link2d : link_data(date)

    for case in cases :

      if _print :
        print '    %s : ' % case,
        dbstatus = "unknown"
      else :
        sql = "select %s from status where date=?" %(CONST_COLUMNS[case])
        dbcur.execute(sql,(dbdstr,))
        dbrow = dbcur.fetchone()
        if dbrow is not None:
          dbstatus = dbrow[0]
        else :
          dbstatus = ''
          dbcur.execute("insert into status values (?, ?,?,?,?,?)",
                (dbdstr,dbstatus,dbstatus,dbstatus,dbstatus,dbstatus))
          dbcon.commit()

      cstatus = ''
      fmissed3d = []
      fmissed2d = []
      fmissing3d = {}
      fmissing2d = {}

      if dbstatus == "Done" :
        continue

      dbnewstatus = ''

      for field in fields :
        fmiss3d = []
        fmiss2d = []
        if field in CONST_3DFIELDS :
          for seq in range(1,74) :
            frm = "%s%02d.png" %(field,seq)
            fam = os.path.join(dataDirBase,date,case,frm)
            if not os.path.exists(fam) :
              fmiss3d.append(seq-1)
              ##print fam
              ##print "%s missing, forecast hour %d." % (frm, seq)

            fam = os.path.join(dataDirBase,date,case+'_big',frm)
            if not os.path.exists(fam) :
              if (seq-1) not in fmiss3d :
                fmiss3d.append(seq-1)

        elif field in CONST_2DFIELDS :

          for seq in range(1,866) :
            frm = "%s%03d.png" %(field,seq)
            fam = os.path.join(dataDirBase,date,case,frm)
            if not os.path.exists(fam) :
              fsec = (seq-1)*300
              fhr = fsec//3600
              if fhr not in fmiss2d :
                fmiss2d.append(fsec//3600)
              ##print fam
              ##print "%s missing, forecast hour %d, %d seconds" % (frm, fsec//3600, fsec%3600)

            fam = os.path.join(dataDirBase,date,case+'_big',frm)
            if not os.path.exists(fam) :
              fsec = (seq-1)*300
              fhr = fsec//3600
              if fhr not in fmiss2d :
                fmiss2d.append(fsec//3600)
              ##print fam
              ##print "%s missing in Big directory, forecast hour %d, %d seconds" % (fam, fsec//3600, fsec%3600)


        ## find missing 3D fields and time in string format
        if fmiss3d :
          smiss3d= fmiss3d[0]
          if len(fmiss3d) > 1 :
            fpr = fmiss3d[0]
            fsp = ','
            for fhr in fmiss3d[1:] :
              if fhr == fpr + 1 :
                fsp = '-'
              else :
                if fsp == '-' :
                  smiss3d = "%s %s %02d, %02d" % (smiss3d, fsp,fpr,fhr)
                  fsp = ','
                else :
                  smiss3d = "%s %s %02d" % (smiss3d, fsp, fhr)
              fpr = fhr

            if fsp == '-' :
              smiss3d = '%s - %02d' % (smiss3d, fpr)
        else :
          smiss3d = ''

        ## find missing 2D fields
        if fmiss2d :
          ##print field, fmiss2d
          smiss2d= fmiss2d[0]
          if len(fmiss2d) > 1 :
            fpr = fmiss2d[0]
            fsp = ','
            for fhr in fmiss2d[1:] :
              if fhr == fpr + 1 :
                fsp = '-'
              else :
                if fsp == '-' :
                  smiss2d = "%s %s %02d, %02d" % (smiss2d, fsp,fpr,fhr)
                  fsp = ','
                else :
                  smiss2d = "%s %s %02d" % (smiss2d, fsp, fhr)
              fpr = fhr

            if fsp == '-' :
              smiss2d = '%s - %02d' % (smiss2d, fpr)
        else :
          smiss2d = ''

        if smiss3d == "0 - 72":
           smiss3d = 'missed'
           fmissed3d.append(field)
        elif smiss3d :
           fmissing3d[field] = smiss3d

        if smiss2d == "0 - 72":
           smiss2d = 'missed'
           fmissed2d.append(field)
        elif smiss2d :
           fmissing2d[field] = smiss2d


      ##
      ## Set case status
      ##
      if len(fmissed3d) == len(CONST_3DFIELDS):
        cstatus = 'Not ready, '
      else :
        if fmissed3d :
          cstatus = 'Missed 3D %s, ' % (','.join(fmissed3d))

      if fmissing3d :
        for key in fmissing3d :
          cstatus += '%s missing %s, ' % (key,fmissing3d[key])

      if len(fmissed2d) == len(CONST_2DFIELDS) :
        cstatus += 'Not start, '
      else :
        if fmissed2d :
          cstatus += 'Missed 2D %s' % (','.join(fmissed2d))

      if fmissing2d :
        dbnewstatus = 'Running'
        for key in fmissing2d :
          cstatus += '%s missing %s, ' % (key,fmissing2d[key])


      if (not fmissed2d) and (not fmissing2d) and (fmissed3d or fmissing3d)  :
        if fmissed3d :
          misstr = "[%s]" % (','.join(fmissed3d))
        else :
          misstr = "[%s]" % fmissing3d[fmissing3d.keys()[0]]
        dbnewstatus = 'Missing images %s<br/>Done' % misstr

      if not cstatus :
        dbnewstatus = 'Done'
        cstatus = 'Done'

      ##
      ## Update database or print
      ##
      if _print :
        print '%s' % cstatus
      else :

        updatestatus = False

        if dbnewstatus :
          if re.match("Done",dbnewstatus) and dbnewstatus != dbstatus :
            updatestatus = True

          if not dbstatus  :
            updatestatus = True
          else :
            if dbstatus.split()[0] == "Scheduled" :
              updatestatus = True

        if updatestatus :
          ##print "%s%s : %s => %s" %(date,case,dbstatus,dbnewstatus)
          dbcur.execute( "update status set %s='%s' where date=?"%(
                         CONST_COLUMNS[case],dbnewstatus),(dbdstr,) )
          dbtime = time.strftime("%Y-%m-%d %H:%M:%S %Z")
          dbcur.execute("INSERT INTO updates VALUES ( NULL, ?, ?, ?, ? )",
                        (dbtime,'crontab','129.15.108.155',
                         "update %s SET %s='%s'"%(dbdstr,CONST_COLUMNS[case],dbnewstatus)
                         ) )
          dbcon.commit()

  if not _print :
    dbcon.close()

#enddef

##======================================================================
## link images
##======================================================================
def link_data(datestr) :
  '''-------------------------------------------------------------------
  Links images from "sourcebase" to "casedir"
  sourcebase = '/import/arpsdata2/www2/wx/p/'
  casedir    = data/20110822/gfs00Z   &  data/20110822/gfs00Z_big

  -------------------------------------------------------------------'''

  ##--------------------------------------------------------------------
  ## Process 3d data
  ##--------------------------------------------------------------------

  currday = int(datestr[-2:])

  new_data = 0

  datedir = os.path.join(dataDirBase,datestr)
  if not os.path.exists(datedir) :
    os.mkdir(datedir)
  sourcedir = os.path.join(srcdir3d,datestr,'r')

  for case in CONST_CASES :
    if _debug : print '\n------------------ CASE %s %s -----------------------'%(datestr,case)

    casedir = os.path.join(datedir,case)
    if not os.path.exists(casedir):
      os.mkdir(casedir)

    if not os.path.exists(casedir+'_big'):
      os.mkdir(casedir+'_big')

    fieldsrc = os.path.join(sourcedir, casesrc[case])
    fhour = int(case[-3:-1])          ## decode forecast hour from case name

    if _link3d :
      for field in CONST_3DFIELDS :
        fielddir = os.path.join(fieldsrc,'wrf_%02d.%02d00Z'%(currday,fhour),'anim',field)
        if (_debug) : print 'fielddir = %s\n'%(fielddir)
        if os.path.isdir(fielddir) and os.path.getctime(fielddir) < time.time()-10:
          i = 0
          for filename in os.listdir(fielddir) :
            fieldRe = re.compile(field+'(\d+).png')
            fileMatch = fieldRe.match(filename)
            if fileMatch :
              order = int(fileMatch.group(1))
              destfile = os.path.join(casedir,'%s%02d.png'%(field,order))
              if not os.path.exists(destfile) :
                if (_debug) : print 'Linking %s ...' % filename
                os.symlink(fielddir+'/'+filename,destfile)
                i = i+1
              else :
                if (_debug) : print 'File (%s) already exist.' % (destfile)
            else :
              if (_debug) : print 'file (%s) does not match.'%(filename)
          if _print and i> 0 : print '    Linking %16s (%d) for %s%s.'%(field,i,datestr,case)
          if (i > 0) : new_data += 1
        else :
          if (_debug) : print 'fielddir (%s) does not exist.'%(fielddir)

        ## Big images
        fielddir = os.path.join(fieldsrc,'wrf_%02d.%02d00Z'%(currday,fhour),'anim',field+'_big')
        if os.path.isdir(fielddir) and os.path.getctime(fielddir) < time.time()-10 :
          j = 0
          for filename in os.listdir(fielddir) :
            fieldRe = re.compile(field+'_big(\d+).png')
            fileMatch = fieldRe.match(filename)
            if fileMatch :
              order = int(fileMatch.group(1))
              destfile = casedir+'_big/'+field+'%02d.png'%order
              if not os.path.lexists(destfile) :
                if (_debug) : print 'Linking %s ...' % filename
                os.symlink(fielddir+'/'+filename,destfile)
                j += 1
          if _print and j>0: print '    Linking %16s (%d) for %s%s.'%(field+'_big',j,datestr,case)

    if _link2d :
      for field in CONST_2DFIELDS :
        dirsrc2d = os.path.join(srcdir2d,datestr,case,field)
        if os.path.isdir(dirsrc2d) and os.path.getctime(dirsrc2d) < time.time()-10 :
          i = 0
          for filename in os.listdir(dirsrc2d) :
            filesrc = os.path.join(dirsrc2d,filename)
            filedes = os.path.join(casedir,filename)
            if not os.path.lexists(filedes) :
              os.symlink(filesrc,filedes)
              i += 1
          if _print and i>0 : print '    Linking %16s (%d) for %s%s.'%(field,i,datestr,case)

        dirsrc2d = os.path.join(srcdir2d,datestr,case,field+'_big')
        if os.path.isdir(dirsrc2d) and os.path.getctime(dirsrc2d) < time.time()-10 :
          j = 0
          for filename in os.listdir(dirsrc2d) :
            filesrc = os.path.join(dirsrc2d,filename)
            filedes = os.path.join(casedir+'_big',filename)
            if not os.path.lexists(filedes) :
              os.symlink(filesrc,filedes)
              j += 1
          if _print and j > 0: print '    Linking %16s (%d) for %s%s.'%(field+'_big',j,datestr,case)

    if not os.listdir(casedir) :   ## If directory is empty, removed it
      os.rmdir(casedir)

    if not os.listdir(casedir+'_big') :   ## If directory is empty, removed it
      os.rmdir(casedir+'_big')

  if not os.listdir(datedir) :     ##delete empty date directory
    os.rmdir(datedir)

  if new_data > 0 :
    if _print : print '--------------- Executing preprocessor.php ...'
    subprocess.Popen(['php','preprocessor.php'],cwd='/vol0/www/html/forecast/ywang/hurricane2/').wait()

  return
#enddef

########################################################################
## Entance Point
########################################################################

if (__name__ == '__main__') :
##-----------------------------------------------------------------------
## Defined global variables
##
## cmd
## args
## _debug
##
##-----------------------------------------------------------------------
  _debug = False
  _print = False
  _link3d = True
  _link2d = False

  srcdir3d = '/import/arpsdata/www/wx/p/'
  srcdir2d = '/import/animdata_2d/hurricane2/'

  dataDirBase = '/vol0/www/html/forecast/ywang/hurricane2/data'   ## fixed destination
  casesrc = { "gfs00Z" : 'GSIA',
              "gfs12Z" : 'GSIA',
              "ekf00Z" : 'EnKFA',
              "ekf12Z" : 'EnKFA' }           ## corresponding source subdirectory
  CONST_CASES   = ("gfs00Z","gfs12Z","ekf00Z","ekf12Z")              ## Case names
  CONST_3DFIELDS = ("streamline", "wspd", "500_rh","slpcrf")           ## Fields to be shown
  CONST_2DFIELDS = ("comref", "maxspd", "maxsfc")

  DATABASE = '/vol0/www/html/forecast/ywang/hurricane2/database/datastatus.db'
  CONST_COLUMNS = {'gfs00Z' : 'GFS00Z',
                   'gfs12Z' : 'GFS12Z',
                   'ekf00Z' : 'EnKF00Z',
                   'ekf12Z' : 'EnKF12Z'
                  }

  ##@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@

  cmdin = sys.argv[0]
  (cmdpath,cmd) = os.path.split(cmdin)

  args  = parseArgv(sys.argv[1:])

  main(**args)
