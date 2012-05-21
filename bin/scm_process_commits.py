#!/usr/bin/python

import sys;
import os;
from time import *
from pySCMLogs.SCMconfig import SCMconfig

class CommitsManager:
	def __init__ (self, cfg, repo):
		self.repo = repo
		self.load_config (cfg)

	def load_config (self, cfg):
		config = SCMconfig(cfg, self.repo)
		self.config = config
		self.SCMlogs_appdir = config.SCMlogs_appdir
		self.dirname = config.logs_dir
		self.sendlogs_cmd = "/usr/bin/python %sbin/scm_sendlogs.py" % (self.SCMlogs_appdir);

	def execute(self, log_filename):
		if len(log_filename) > 0:
			self.send_email_on_log (log_filename)
		else:
			filename = "%s/%s" %(self.dirname, "commits.txt");
			file = open (filename, 'r');
			commits = file.read();
			size = len (commits);
			file.close ();

			if size > 5: 
				file_key = strftime ("%Y-%m-%d", localtime(time()))
				self.send_email_on_log_for (filename, file_key)

				year = strftime ("%Y", localtime(time()))
				month = strftime ("%m", localtime(time()))

				yearcommitdirname = "%s/%s" %(self.dirname, year)
				commitdirname = "%s/%s" %(yearcommitdirname, month)

				if not os.path.exists (yearcommitdirname) :
					os.mkdir (yearcommitdirname);
				if not os.path.exists (commitdirname) :
					os.mkdir (commitdirname);

				destfilename = "%s/%s" %(commitdirname, file_key)

				destfile = open (destfilename, 'a+');
				destfile.write (commits);
				destfile.close ()

				file = open (filename, 'w');
				file.close ();

				#self.send_email (file_key)

	def send_email (self, file_key):
		# sending email
		cmd = "%s -k %s -mail " % (self.sendlogs_cmd, file_key)
		if len (self.config._filename) > 0:
			cmd = "%s -cfg %s " % (cmd, self.config._filename)
		if len (self.repo) > 0:
			cmd = "%s -repo %s" % (cmd, self.repo);
		self.process_command (cmd);

	def send_email_on_log_for (self, log_filename, file_key):
# sending email
		cmd = "%s -f %s -k %s -mail " % (self.sendlogs_cmd, log_filename, file_key)
		if len (self.config._filename) > 0:
			cmd = "%s -cfg %s " % (cmd, self.config._filename)
		if len (self.repo) > 0:
			cmd = "%s -repo %s" % (cmd, self.repo);
		self.process_command (cmd);

	def send_email_on_log (self, log_filename):
		# sending email
		cmd = "%s -f %s -mail " % (self.sendlogs_cmd, log_filename)
		if len (self.config._filename) > 0:
			cmd = "%s -cfg %s " % (cmd, self.config._filename)
		if len (self.repo) > 0:
			cmd = "%s -repo %s" % (cmd, self.repo);
		self.process_command (cmd);

	def process_command (self, cmd):
		#print "Executing: %s \n" % (cmd);
		os.system (cmd);

###############################################
### Main Program                            ###
###############################################

if __name__ == '__main__':
	cfg_fn = ''
	repository = ''
	log_fn = ''

	argc = len (sys.argv)
	i = 1
	while i < argc:
		arg = sys.argv[i]
		i = i + 1
		if arg == '-cfg' :
			cfg_fn = sys.argv[i]
			i = i + 1
		elif arg == '-log' :
			log_fn = sys.argv[i]
			i = i + 1
		elif arg == '-repo' :
			repository = sys.argv[i]
			i = i + 1
		#else :
			#nop
			#print "? Ignored : [%s] ?" %(arg)

	if cfg_fn == '':
		cfg_fn = "%s/../conf/%s" % (os.getcwd(), 'SCMlogs.conf')
	if repository == '':
		repository = None
	obj = CommitsManager(cfg_fn, repository)
	obj.execute(log_fn);

