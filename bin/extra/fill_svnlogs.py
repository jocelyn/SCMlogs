#!/usr/local/bin/python

import os;
import sys;
#import shutil;
import datetime;
import popen2;
from string import atoi;


def output_of (cmd):
        (std_output, std_input) = popen2.popen2(cmd);
        output = std_output.read();
        return output[:-1]

def saveFile (filecontent, filename):
	outputfile = open (filename, 'w');
	outputfile.write (filecontent)
	outputfile.close ()

########################

repo = sys.argv[1]
logdir = sys.argv[2]

sday = sys.argv[3]
spday = sday.split ('-')
day = datetime.datetime (atoi(spday[0]), atoi(spday[1]), atoi(spday[2]))

sday = sys.argv[4]
spday = sday.split ('-')
stopday = datetime.datetime (atoi(spday[0]), atoi(spday[1]), atoi(spday[2]))

print "Repository  : " + repo
print "Logs folder : " + logdir
print "Generate log between " + day.strftime("%Y-%m-%d") + " and " + stopday.strftime("%Y-%m-%d")
sys.stderr.write ("Continue  (Y|n) ? ")
reply = sys.stdin.readline()[0]
if reply == '\n' or reply == 'y' or reply == 'Y':
	while day <= stopday:
		logfn = os.path.join (logdir, "%02d" % (day.year))
		if not os.path.exists (logfn):
			os.mkdir (logfn)
		logfn = os.path.join (logfn, "%02d" % (day.month))
		if not os.path.exists (logfn):
			os.mkdir (logfn)
		logfn = os.path.join (logfn, day.strftime("%Y-%m-%d"))

		cmd = "svn log " + repo + " -v --incremental "
		cmd = cmd + ' -r "{' + day.strftime("%Y-%m-%d") + 'T09:00}'
		day = day + datetime.timedelta(1)
		cmd = cmd + ':{' + day.strftime("%Y-%m-%d") + 'T09:00}" '
		#cmd = cmd + ' > ' + logfn
		print cmd
		os.system (cmd)
		out = output_of (cmd)
		if len (out) > 5:
			saveFile (out, logfn) 
else:
	print "Bye..."

