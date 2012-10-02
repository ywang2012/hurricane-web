#!/usr/bin/env python
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
##   Yunheng Wang (09/01/2012)
##   Improved with option "-n" and convenience of changeing number of
##   processors.
##
## ---------------------------------------------------------------------
##
## REQUIREMENT:
##
########################################################################

import os, sys, getopt, re, time
import subprocess, cgi
import cgitb

##======================================================================
## USAGE
##======================================================================
def usage( istatus = 0 ) :
  '''-------------------------------------------------------------------
  Print Usage and then exit
  -------------------------------------------------------------------'''
  print >> sys.stderr, '''
    Usage: %s [option] YYYYMMDD CASEhhZ\n
    \tDATE\t Date to be processed, for example 20100915
    \tCASE\t Case to be processed. Valid are 'gfs' or 'ekf'
    \tTime\t Time to be processed, for example, 00Z or 12Z
    OPTIONs:\n
    \tOption \t\tDefault \tInstruction
    \t-------\t\t--------\t-----------
    \t-d, --verbose\tNone\t\tVerbose
    \t-h, --help         \t\tPrint this help
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
  global _debug

  try:
    opts, args = getopt.getopt(argv,'hvd',           \
           ['help','verbose'])

    for opt, arg in opts :
      if opt in ('-h','--help'):
        usage(0)
      elif opt in ( '-v','-d', '--verbose'):
        _debug = 1

  except getopt.GetoptError:
    print >> sys.stderr, 'ERROR: Unknown option.'
    usage(2)

  argvs = []
  if ( len(args) >=  1 ) :
    oneargs = re.match('(\d{4}\d{2}\d{2})',args[0])
    if oneargs:
      date = oneargs.group(1)
      argvs.append( date )
    else :
      print >> sys.stderr, 'Argument %s is in wrong format.' % (args[0])
      usage(1)

    if len(args) == 2 :
      oneargs = re.match('(gfs|ekf)(00Z|12Z)',args[1])
      if oneargs:
        case = ''.join(oneargs.group(1,2))
        argvs.append( case )
      else :
        print >> sys.stderr, 'Argument %s is in wrong format.' % (args[0])
        usage(1)
    else :
    	argvs.append(False)

  else :
    print >> sys.stderr, 'ERROR: wrong number of command line arguments.'
    usage(1)

  ##print argvs
  return argvs
#enddef

##======================================================================
## Parse Query string
##======================================================================
def parseQS() :

  cgitb.enable()

  form = cgi.FieldStorage()

  datein = form.getfirst('date','20120901')

  casein = form.getfirst('case',False)

  return (datein,casein)

#enddef

##======================================================================
## MAIN program here
##======================================================================
def main(datein,casein=False) :

  cmdlist = ["ssh", "ywang@boomer.oscer.ou.edu",
             "/home/kwthomas/wat/watcher %s" % datein]

  checker=subprocess.Popen(cmdlist,shell=False,
                           stderr=subprocess.PIPE,stdout=subprocess.PIPE)
  (outdata,errdata) = checker.communicate()

  outlist = outdata.splitlines()
  newlist = []
  for lstr in outlist :
  	newstr = lstr.strip()
  	if newstr.endswith('__') :
  		newstr = ' &'
  	if not newstr.isspace() :
  		newlist.append(newstr)
  newlist.append(' &')

  outnew = '\n'.join(newlist)
  ##print outnew

  if not casein :
  	casenames = ['WRF-GSIA %s 0000Z'  % datein, 'WRF-GSIA %s 1200Z' % datein,
  	             'WRF-ENKFA %s 0000Z' % datein, 'WRF-ENKFA %s 1200Z' % datein]
  else :
  	casenames = []
  	casemth = re.match('(gfs|ekf)(\d{2})Z',casein)
  	if casemth :
  		if casemth.group(1) == "gfs" :
  			casenames.append('WRF-GSIA %s %s00Z' % (datein,casemth.group(2)))
  		else :
  			casenames.append('WRF-ENKFA %s %s00Z' % (datein,casemth.group(2)))

  ##print """\
  ##Content-Type: text/html\n
  ##<html><body>
  ##<pre>"""

  ##casestatus = []
  for case in casenames :
  		restr = '%s ([^&]*) &' % (case)
   		casemth = re.search(restr,outnew,re.M)
  		if casemth :
  			print "%s %s" % (case,casemth.group(1))
  			##casestatus.append(casemth.group(1))
  		else :
  			print "%s - unavailable" % (case)
  			##casestatus.append('unavailable')

  ##print """</pre></body></html>"""

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
  _debug = 0

  cmdin = sys.argv[0]
  (cmdpath,cmd) = os.path.split(cmdin)

  args  = parseArgv(sys.argv[1:])
  ##args = parseQS()

  main(args[0],args[1])
  ##main('20120831',False)
  ##print args[0],args[1]
  ##wp = subprocess.Popen("whoami",shell=True,stdout=subprocess.PIPE,stderr=subprocess.PIPE)
  ##(out,err) = wp.communicate()
  ##print out