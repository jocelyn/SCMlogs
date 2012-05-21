#!/usr/local/bin/python

# jfiat: 	This is a port/evolution of the contrib/log.pl 
# 			from the cvs 1.11 distribution
#
# Usage:  log.py [-u user] [-i cvsinfo] [-s] -f logfile 
#
#	-u user		- $USER passed from loginfo
#	-i cvsinfo	- to pass the %{sVv} information : file,old,new .
#	-s			- to prevent "cvs status -v" messages
#	-f logfile	- for the logfile to append to
#
#
# here is what the output looks like:
#
#
#	 ****************************************
#    Date: Wednesday November 23, 1994 @ 14:15
#    Author: woods
#	 Info:	 test3,1.12,1.13 test6,NONE,1.3 test4,1.6,NONE
#
#    Update of /local/src-CVS/testmodule
#    In directory kuma:/home/kuma/woods/work.d/testmodule
#    
#    Modified Files:
#    	test3 
#    Added Files:
#    	test6 
#    Removed Files:
#    	test4 
#    Log Message:
#    wow, what a test

import os;
import sys;
from time import strftime, localtime, time;

def usage ():
	return """
Usage:  log.py [-u user] [-i cvsinfo] [-s] -f logfile 

	-u user		- $USER passed from loginfo
	-i cvsinfo	- to pass the %{sVv} information : file,old,new .
	-s			- to prevent "cvs status -v" messages
	-f logfile	- for the logfile to append to
"""

if __name__ == '__main__':
	login = '';

#	if os.environ.has_key ('CVSROOT'):
#		cvsroot = os.environ['CVSROOT'];
	dostatus = 0;
	logfile = '';

	argc = len (sys.argv) ;
	i = 1;
	while i < argc:
		arg = sys.argv[i];
		i = i + 1;
		if arg == '-m' :
			users = "%s %s" % (users, sys.argv[i])
			i = i + 1;
		elif arg == '-u' :
			login = sys.argv[i]
			i = i + 1;
		elif arg == '-l' :
			logfile = sys.argv[i]
			i = i + 1;
		elif arg == '-i' :
			info = sys.argv[i]
			i = i + 1;
		elif arg == '-s' :
			dostatus = 1;
		#else :
			#nop
			#print "? Ignored : [%s] ?" %(arg);

	if logfile == '':
		print "You must specify at least the logfile"
		print usage ();
		sys.exit ();
	if login == '': login = os.environ['USER'];

	date = strftime ("%A %B %d, %Y @ %H:%M:%S", localtime(time()))
	### Let's get the message content (from CVS)
	msg = sys.stdin.read ()
	text = ''
	text = "%s\n" 				% (text)

	### Build the log text
	text = "------------------------------------------------------------------------\n";
	text = "%sDate:\t%s\n" 		% (text, date)
	text = "%sAuthor:\t%s\n" 	% (text, login)
	text = "%sInfo:\t%s\n" 		% (text, info)
	text = "%s\n" 				% (text)
	text = "%s%s\n" 			% (text, msg)
	text = "%s\n" 				% (text)

	#systemcall = os.popen ('command.. cvs log diff ...', 'r').read ()
	#text = "%s%s\n"				% (text, systemcall)


	### Save the log text in the log file
	z_logfile = open (logfile, 'a');
	z_logfile.write (text)
	z_logfile.close ();

	sys.exit();

