#!/usr/local/bin/python

import os;
import sys;
import re;
#import shutil;
import datetime;
import popen2;
from string import atoi;

# Usage:
#
# script path_to_config_file
#
# Config file will be formatted like that:
#
#	logfile=commits.txt                                      
#	logdir=/home/svn/logdir
#	repository=https://svn.origo.ethz.ch/eiffelstudio
#	revision=70630
#


def output_of (cmd):
	(std_output, std_input) = popen2.popen2(cmd);
	output = std_output.read();
	return output[:-1]

def saveFile (filecontent, filename):
	outputfile = open (filename, 'w');
	outputfile.write (filecontent)
	outputfile.close ()

def appendToFile (filecontent, filename):
	outputfile = open (filename, 'a+');
	outputfile.write (filecontent)
	outputfile.close ()

def valuesFromString (text, sep):
	result = {}
	lines = re.split("\n", text)
	for l in lines:
		l = l.lstrip();
		if len(l) > 0:
			(n,v) = re.split (sep, l)
			result[n] = v;
	return result

def valuesFromIniConfigFile (filename):
	inifile = open (filename, 'r');
	text = inifile.read()
	inifile.close ()
	return valuesFromString (text, '=')

def saveIniConfigFile (filename, conf):
	inifile = open (filename, 'w');
	keys = conf.keys()
	for n in keys:
		inifile.write ("%s=%s\n" % (n, conf[n]))
	inifile.close ()

def svnHeadRevision (repo):
	result = 0
	cmd = "svn info %s" % (repo)
	out = output_of (cmd)
	details = valuesFromString (out, ': ')
	r = details['Revision']
	return atoi (r)

########################

inifn = sys.argv[1]
if not os.path.exists (inifn):
	sys.stderr.write ("Config not found [%s] !\n" % (inifn))
	sys.exit()

try:
	config = valuesFromIniConfigFile (inifn)
	repo = config['repository']
	logdir = config['logdir']
	logfile = config['logfile']
	lastrev = atoi(config['revision'])
except:
	sys.stderr.write ("Unable to get required information from config file [%s] !\n" % (inifn))
	sys.exit()

try:
	headrev = svnHeadRevision (repo)
except:
	sys.stderr.write ("Unable to get HEAD revision number of repository [%s] !\n" % (config['repository']))
	sys.exit()

if headrev > lastrev:
	cmd = "svn log %s -v --incremental -r%d:HEAD" % (repo, lastrev + 1)
	out = output_of (cmd)
	if len (out) > 5:
		logpath = os.path.join (logdir, logfile)
		appendToFile (out, logpath) 
			# Update the last revision from config
		config['revision'] = "%d" % (headrev)
		saveIniConfigFile (inifn,config)

