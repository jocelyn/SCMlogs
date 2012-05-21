#!/usr/bin/python
#
# Usage:  svn_sendlogs.py -f logfile { -u user } { -p filterfile or none } {-html}
#

from pySCMLogs.SCMengine import *

###############################################
### Main Program							###
###############################################

# Globals
class SCMLogsApplicationParameters:
	def __init__ (self):
		self.parameters = {}
		self.options = {}
		self.add_option ("user",		0, None, 	"-u", 		"User")
		self.add_option ("logfile", 	1, None, 	"-f", 		"Logs file: path")
		self.add_option ("keyfile", 	0, None, 	"-k", 		"Key file: YYYY-MM-DD")
		self.add_option ("profil", 		0, None, 	"-p", 		 "Profil to use (none, profil, path) file")
		self.add_option ("config", 		0, None, 	"-cfg", 		"Configuration file (path)")
		self.add_option ("repo", 		0, None, 	"-repo", 	"Repository id")
		self.add_option ("mesg", 		0, None, 	"-mesg", 	"Special message to include in email")
		self.add_option ("subject", 	0, None, 	"-subject", 	"Special subject to use in email")
		self.add_option ("output_format", 		0, 'html', 	"-html", 	"generate HTML")
		self.add_option ("output_format", 		0, 'text', 	"-text", 	"generate TEXT")
		self.add_option ("output_type", 		0, 'filtered', 	"--filtered", 	"filter LOGs")
		self.add_option ("output_type", 		0, 'raw', 	"--raw", 	"raw LOGs")
		self.add_option ("output", 		0, 'out', 	"-out", 		"Display output in stdout")
		self.add_option ("output", 		0, 'mail',	"-mail", 	"Send output by email")
		self.add_option ("only_user", 	0, None, 	"-only_user","Use only_user's commits")
		self.add_option ("only_tag", 	0, None, 	"-only_tag",	"Use only_tag's commits (cvs only)")

	def add_option (self, name, required, value, opt, desc):
		# opt= -f desc=log file to ..
		self.options[opt] = [name, opt, required, value, desc]

	def usage_option (self, opt):
		n = self.options[opt][2]
		r = self.options[opt][3]
		rs = ""
		if r == 1: rs = "[required] "
		v = self.options[opt][4]
		vs = ""
		if v == 1: vs = n
		d = self.options[opt][4]
		return "%s %s:(%s) %s" % (n,vs,rs,d)
	def usage_option (self, opt):
		return self.options[opt][2];
	def required_option (self, opt):
		return self.options[opt][3] == 1;
	def valued_option (self, opt):
		return self.options[opt][4] != None
	def usage(self):
		res = "Usage:\n"
		res = "%s%s" %(res, " script (-k logskey |-f logfile) -u user {-p none or filterfile} (-filtered | -raw) (-text | -html) (-out | -mail) -only_user a_user -only_tag a_tag -subject subject -mesg message \n")

		for n,o,r,v,d in self.options:
			print o
			res = "%s\t%s\n" % (res, self.usage_option(o))
		return res
	def load_from_args(self, args):
		self.load_from_argv (args.split())
	def load_from_argv(self, argv):
		for i in range (1, len (argv)):
			arg = argv[i]
			if self.options.has_key (arg):
				n,o,r,v,d = self.options[arg]
				i = i + 1
				if v == None:
					v = argv[i]
					i = i + 1
				if self.parameters.has_key (n):
					self.usage();
					sys.stderr.write ("Error: opt [%s] already has a value [%s]\n" %(n, self.parameters[n]))
					sys.exit(2)
				else:
					self.parameters[n] = v
#					sys.stdout.write ("[[%s = %s]]<br>\n" %(n, self.parameters[n]))

	def check_parameters (self):
		for n,o,r,v,d in self.options:
			if r == 1:
				if not self.parameters.has_key (n):
					self.usage();
					sys.stderr.write ("Error: opt [%s] is required\n" %(n))
					sys.exit(2)



def processMain():
	param = SCMLogsApplicationParameters()
	param.load_from_argv (sys.argv)
	app = SCMLogsApplication(param.parameters)
	app.execute()

if __name__ == '__main__':
	processMain()

