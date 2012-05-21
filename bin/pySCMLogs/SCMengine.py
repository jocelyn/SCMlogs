#!/usr/bin/python
#
# Usage:  svn_sendlogs.py -f logfile { -u user } { -p filterfile or none } {-html} {-raw}
#

from time import *
import sys
import re
import os
import smtplib
from string import split, replace, rstrip, strip
from SCMconfig import SCMconfig

import SCMsvn
import SCMcvs
import SCMerr
import webAppEngine


# Regexp 
email_regexp = "^(.*)@(.*)$"
pemail = re.compile (email_regexp);

filekey_regexp = "([0-9][0-9][0-9][0-9])-([0-9][0-9])-([0-9][0-9])"
pfilekey = re.compile (filekey_regexp);

# Functions
def isValidEmail (email):
	return pemail.search (email,0)

def html_escape (txt):
	# escape the  '<' and '>' to htmlentities
	result = "%s"  % (txt)
	result = replace (result, "<","&lt;")
	result = replace (result, ">","&gt;")
	return result


def error (mesg):
	print mesg;
	sys.exit ();


def sendMailToFromSubjectOfOn (z_to, z_from_name, z_from, z_mail, z_server) :
	fromaddr = 'From: "' + z_from_name + '" <' + z_from + '>'
	toaddrs = 'To: <' + z_to + '>'
	msg = z_mail
	server = smtplib.SMTP(z_server)
	server.sendmail(fromaddr, toaddrs, msg)
	server.quit()

def hrefNameFor (dir):
	return replace (dir, "/", "_")

# CLasses declaration
class UserProfile:
	def __init__ (self, user, conf):
		self.config = conf

		self.user = user
		self.email = ''
		self.format = 'html'
		self.type = 'filtered'
		self.directories = []
		self.send_email = 1
		self.send_emptylogs = 0

		fn = self.user_pref_filename (user)
		if os.path.exists (fn):
			pref_file = open (fn, 'r')
			for line in pref_file.readlines():
				self.set_values (line)

		if not isValidEmail(self.email):
			if user != '':
				self.email = user + self.config.at_domain_name

	def user_pref_filename (self, user):
		return self.config.cfg_dir + user + self.config.pref_ext

	def user_cfg_filename (self, user):
		return self.config.cfg_dir + user + self.config.cfg_ext

	def set_values (self, line):
		args = (re.split ('\=', line)); #[1:-1];
		if len (args) > 1:
			param = strip (args[0]);
			value = strip (args[1][0:-1]);
#			print "%s %s\n" % (param, value)
			if param == 'email':
				self.email = value
			if param == 'send_format':
				self.format = value
			if param == 'send_type':
				self.type = value
			if param == 'send_email':
				self.send_email = (value != 'off')
			if param == 'send_emptylogs':
				self.send_emptylogs = (value == 'on')

	def load_directories (self, file):
		myusercfgfile = open (file, 'r')
		self.directories = (re.split ('\n', myusercfgfile.read()))[:-1];
		myusercfgfile.close ();

	def get_directories (self):
		fn = self.user_cfg_filename (self.user)
		if os.path.exists (fn):
			self.load_directories (fn);
		else:
			self.directories = [];

###################################
# Declaration
###################################

def htmlStyleCode ():
	return  """
		<style>
		a { text-decoration: none; }
		a:hover { color: red ; background-color: yellow; } 
		td { vertical-align: top; } 
		td.a, td.d { background-color: #ddddee; font-weight: bold; } 
		td.dir { background-color: #aaaacc; font-weight: bold; } 
		td.i { background-color: #ffbb99; font-weight: bold; } 
		td.mo, td.ad, td.re { background-color: #ffddbb; font-weight: bold; } 
		td.l { background-color: #ffffdd; font-weight: bold; } 
		td.lm { font-size: smaller; color: black; background-color: white; padding: 2pt; } 
		td.sep { font-size: 8pt; text-align: right; background-color: #ffffff; } 
		td.sd { font-weight: bold; color: #00ff00; background-color: #000000; } 
		td.sd em { color: yellow; }
		.df, .df a { color: darkred; font-size: 8pt; } 
		div.warning { color: red; font-size: 12pt; font-weight: bold; } 
		</style>
		"""

class SCMLogsFactory:
	def __init__ (self, conf, wurl):
		self.config = conf
		self.mode = self.config.SCMmode
		self.webUrl_engine = wurl

	def logs_from (self, log):
		try:
			if self.mode == 'cvs' :
				o = SCMcvs.CvsLogEntry(self.config, self.webUrl_engine);
			elif self.mode == 'svn':
				o = SCMsvn.SvnLogEntries(self.config, self.webUrl_engine);
			o.load_log (log)
			return o.to_logEntries();
		except:
			print "Error occurred <br>"
			o = SCMerr.ErrLogEntry(self.config, self.webUrl_engine);
			o.load_log (log)
			return [o]

class SCMLogsApplication:
	def __init__(self, param, cfg=''):
		self.raw_logsfile_content = None
		self.all_logs = None

		self.user = '';
		self.only_user = '';
		self.only_tag = '';
		self.logskey = '';
		self.logsfile = '';

		self.opt_mesg = '';
		self.opt_subject = '';
		self.opt_filter = 'profil'; # or 'none' or 'file' then opt_filter_fn;
		self.opt_filter_fn = '';
		self.opt_output = 'mail';
		self.opt_output_format = '';
		self.opt_output_type = '';

		self.opt_SCM_repo = '';
		if len(cfg) == 0:
			self.opt_cfg = 'SCMlogs.conf';
		else:
			self.opt_cfg = cfg;

			# Get arguments
		#print param
		self.load_parameters(param)
		self.check_parameters()

			# Get config
		self.load_config (self.opt_SCM_repo)

		if (len(self.logsfile) == 0) and (len (self.logskey) > 0):
			self.logsfile = self.commitsFileFor (self.logskey)

		self.abs_appurl = self.config.SCMlogs_appurl;
		self.use_basetag = (self.opt_output == 'mail') and (self.opt_output_format == 'html')
		if self.use_basetag:
			self.webUrlEngine = webAppEngine.webscmlogs(self.config.webapp_script, self.config.repository_name)
		else:
			self.webUrlEngine = webAppEngine.webscmlogs(self.config.webapp_url, self.config.repository_name)
		self.webUrlEngine.set_default_webapp (self.config.browsing);
		self.logs_factory = SCMLogsFactory (self.config, self.webUrlEngine)

	def load_parameters(self, param):
#		for p in param:
#			print p + "=" + param[p] + "<br>"
		if param.has_key ('repo'): 
			self.opt_SCM_repo = param['repo']
		if param.has_key ('config'): 
			self.opt_cfg = param['config']
		if param.has_key ('keyfile'): 
			self.logskey = param ['keyfile']
		if param.has_key ('logfile'):
			self.logsfile = param['logfile']
		if param.has_key ('user'): 
			self.user = param['user']
		if param.has_key ('profil'): 
			self.opt_filter = param['profil']
			if self.opt_filter == 'none' or self.opt_filter == 'profil':
				self.opt_filter_fn = '';
			else:
				self.opt_filter_fn = self.opt_filter;
				self.opt_filter = 'file';
		if param.has_key ('only_user'): 
			self.only_user = param['only_user']
			if self.only_user == 'none':
				self.only_user = ''
		if param.has_key ('only_tag'): 
			self.only_tag = param['only_tag']
		if param.has_key ('mesg'): 
			self.opt_mesg = param['mesg']
		if param.has_key ('subject'): 
			self.opt_subject = param['subject']
		if param.has_key ('output_format'): 
			self.opt_output_format = param['output_format']
		if param.has_key ('output_type'): 
			self.opt_output_type = param['output_type']
		if param.has_key ('output'): 
			self.opt_output = param['output']

	def check_parameters (self):
		if self.logsfile == '' and self.logskey == '':
			print "Usage: script (-k logskey |-f logfile) -u user {-p none or filterfile} {-html|-text} {-filtered|-raw} {-mail|-out} -only_user a_user -only_tag a_tag -mesg message \n"
			sys.exit ();

	def load_config (self, repo):
		self.config= SCMconfig(self.opt_cfg, repo)

	def commitsFileFor (self,key):
		result = pfilekey.search (key,0)
		if result:
			l_year = result.group (1)
			l_month = result.group (2)
		return "%s/%s/%s/%s" % (self.config.logs_dir, l_year, l_month, key)

	def getLogsFrom (self,log):
		return self.logs_factory.logs_from (log)

	def formatedTextLog (self, log_obj):
		return log_obj.to_text();

	def formatedHtmlLog (self, log_obj):
		return log_obj.to_html();

	def formatedFilteredLogs (self, dirs_listed, format='html'):
		if format == 'html':
			return self.formatedHtmlFilteredLogs (dirs_listed)
		elif format == 'text':
			return self.formatedTextFilteredLogs (dirs_listed)

	def formatedTextFilteredLogs (self, dirs_listed):
		separatorLog = "="*72+"\n";
		logs_text = ""
		dirs_changed = ""
		list_keys = dirs_listed.keys ();
		list_keys.sort ()
		for d in list_keys:
			logs = dirs_listed[d]
			dirs_changed = "%s - %s :: (%d)\n" % (dirs_changed, d, len (logs) )
			logs_text = "%s%s.:: %s  --  %d change(s) \n%s" % (logs_text, separatorLog, d, len(logs), separatorLog);
			for log in logs:
				logs_text = "%s%s\n%s" % (logs_text, self.formatedTextLog (log), "")
		logs_text = "%s\n" %(logs_text)
		return (logs_text, dirs_changed)

	def formatedHtmlFilteredLogs (self, dirs_listed):
		separatorHtmlLog = "<tr><td class=sep colspan=4><a href=\"#top\">top</a></td></tr>\n";
		logs_text = "<table border=1 width=\"100%\" cellpadding=1 cellspacing=0 >\n"
		dirs_changed = ""
		list_keys = dirs_listed.keys ();
		list_keys.sort ()
		for d in list_keys:
			logs = dirs_listed[d]
			dirs_changed = "%s<li><a href=\"#%s\">%s</a> (%d)</li>\n" % (dirs_changed, \
					hrefNameFor(d), d, len (logs) )
			logs_text = "%s<tr><td class=sd colspan=4><a name=\"%s\"></a>directory %s <em > --  %d change(s)</em></td></tr>\n%s" % (logs_text, \
					hrefNameFor (d) ,d, len(logs), separatorHtmlLog);
			for log in logs:
				logs_text = "%s\n%s\n%s" % (logs_text, self.formatedHtmlLog (log), separatorHtmlLog)
		logs_text = "%s</table>\n" %(logs_text)
		return (logs_text, dirs_changed)

	def logDirectorySelected (self, log, mydir):
		if len (mydir) == 0 :
			result = 1;
		else:
			result = 0
			log_dir = log.directory
			#if len(log_dir) == 0:
				#print "Erreur ..."
			if len(log_dir) > 0:
				if log_dir[0] == '/':
					log_dir = log_dir[1:]
				for dir in mydir:
					#print "%s %s" % (dir, (log_dir)[:len(dir)])
					result = result or (dir == (log_dir)[:len(dir)])
		return result;

	def sendLogByEmailTo (self, logs, user_profile, logs_count, all_logs_count, a_subject='', is_html=0):
		#today = strftime ("%A %Y/%b/%d (%H:%M:%S)", localtime(time()))
		today = strftime ("%A %Y/%b/%d", localtime(time()))
		if a_subject != '':
			subject = "[SCM:%s] " % (a_subject)
		else:
			subject = "[SCM:Commits @ %s] " % (user_profile.user)
		subject = "%s<%s> " % (subject, self.config.repository_name)
		subject = "%s <%d/%d logs> %s" % (subject, logs_count, all_logs_count, today)
		header = ""
		header = header +  "X-Mailer: PyJoceMailer\n"
		header = header +  "Reply-To: <%s>\n" % (self.config.superuser_email)
		header = header +  'From: "' + self.config.superuser_name + '" <' + self.config.superuser_email + '>\n'
		header = header +  "To: <%s>\n" % (user_profile.email)
		header = header +  "Subject: %s \n" % (subject)
		header = header +  "Organization: %s \n" % (self.config.organization_name)
		#header = header +  "Importance: %s\n" % ('normal')
		#header = header +  "X-Priority: %s\n" % ('3 (Normal)')

		if is_html:
			header = header + "MIME-Version: 1.0\n"
			header = header + "Content-Type: text/html;\n\n"
			header = header + '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">'
		else:
			#header = header + "MIME-Version: 1.0\n"
			#header = header + "Content-Type: text/text;\n"
			header = header + "\n\n"
		
		text = "%s" %(header)
		text = "%s%s" % (text, logs)
		sendMailToFromSubjectOfOn (user_profile.email, self.config.superuser_name, \
				self.config.superuser_email, \
				text, self.config.smtp_server)

	def listOfUsers (self):
		dir_list = os.listdir (self.config.cfg_dir)
		regexp = "^([a-zA-Z][a-zA-Z0-9_\.\-]*)%s$" % (self.config.cfg_ext)
		puser = re.compile (regexp);
		users = []
		for file in dir_list:
			result = puser.search (file,0)
			if result:
				users.append (result.group (1))
		return users

	def processUser (self, a_user, a_filter, a_filter_fn, a_logskey):
		if self.use_basetag:
			basetag_url = "%s" % (self.config.SCMlogs_appurl)
			rel_appurl = ""
		else:
			basetag_url = ""
			rel_appurl = "%s/" % (self.config.SCMlogs_appurl)

		user_profile = UserProfile(a_user, self.config)
		#DBG# print "user: email=%s" % (user_profile.email)

		if self.opt_output == 'mail' and not user_profile.send_email:
			return 
		if self.opt_output == 'mail' and a_user == '':
			return

		if a_filter == 'profil':
			user_profile.get_directories()
			mydirectories = user_profile.directories
		elif a_filter == 'none':
			mydirectories = [];
		elif a_filter == 'file':
			user_profile.load_directories(a_filter_fn)
			mydirectories = user_profile.directories
			
		if len (self.opt_output_format) > 0:
			output_format = self.opt_output_format
		else:
			output_format = user_profile.format

		if len (self.opt_output_type) > 0:
			output_type = self.opt_output_type
		else:
			output_type = user_profile.type

		if output_type == 'raw':
			nb_logs = self.raw_logsfile_count()
			nb_all_logs = nb_logs
			filtred_logs_text = self.raw_logsfile_content;
			dirs_changed = ""
		else:
			self.get_all_logs()
			nb_all_logs = len (self.all_logs)

			# Build a dict containing the logs indexed by directory name
			nb_logs = 0
			dirs_listed = {}
			for log_obj in self.all_logs:
				is_selected = 1;
				has_error = len (log_obj.error_message) > 0;
				if has_error:
					is_selected = 1
				else:
					if (is_selected and self.only_user != ''):
						is_selected = is_selected and log_obj.author == self.only_user
					if (is_selected and self.only_tag != ''):
						is_selected = is_selected and log_obj.tag == self.only_tag
					is_selected = is_selected and self.logDirectorySelected (log_obj, mydirectories)
				if is_selected:
					if not dirs_listed.has_key (log_obj.directory):
						dirs_listed[log_obj.directory] = [];
					(dirs_listed[log_obj.directory]).append (log_obj)
					nb_logs = nb_logs + 1

			# Build the mail text content regarding logs
			(filtred_logs_text, dirs_changed) = self.formatedFilteredLogs (dirs_listed, output_format)

		###########################################################
		# send the mail only if there are some logs events.       #
		###########################################################

		#DBG#print "-> %s \t[%d/%d logs]" % (self.user, nb_logs, nb_all_logs);
		if nb_logs > 0 or user_profile.send_emptylogs :
			### Top Text
			header_text = "";
			top_text = "";
			bottom_text = "";
			if output_format == 'text':
				if a_logskey != '':
					top_text = "Check online commits  :: [%s/show.php?key=%s] :: [%s]\n%s" % (self.abs_appurl, a_logskey, a_logskey, top_text)
				if len(dirs_changed) > 0:
					top_text = "%sYour selection containing changes : \n\n%s\n" % (top_text, dirs_changed)
				top_text = "%sTotal :: %d / %d logs\n" % (top_text, nb_logs, nb_all_logs)

				### Bottom Text
				if output_type != 'raw':
					bottom_text = "%s\nYou are viewing only the following directory (and their subdirectories) : \n" % (bottom_text)
					mydirectories_text = ""
					for m in mydirectories:
						mydirectories_text = "%s :: %s \n" % (mydirectories_text, m)
					bottom_text = "%s%s\n\n" % (bottom_text, mydirectories_text)
				bottom_text = "%sIf you want to change your preferences, (like not receiving SCMLogs emails), go to [%s]\n\n" % (bottom_text, self.abs_appurl)
			else :
				if self.use_basetag:
					header_text = "<BASE href=\"%s/\">\n" % (basetag_url)
				else:
					header_text = ""

				if a_logskey != '':
					top_text = "Check online commits  ::<a href=\"%sshow.php?key=%s\">[%s]</a><br>\n%s" % (rel_appurl, a_logskey, a_logskey, top_text)
				if len(dirs_changed) > 0:
					top_text = "%s<a name=\"TOP\"></a>Your selection containing changes : \n<br><ul>%s</ul>" % (top_text, dirs_changed)
				top_text = "%s<br><b>Total</b> :: %d / %d logs" % (top_text, nb_logs, nb_all_logs)
				top_text = "%s<br><br>\n" % (top_text)

				### Bottom Text
				bottom_text = "";
				if output_type != 'raw':
					bottom_text = "%s<br/>\nYou are viewing only the following directories (and their subdirectories) : <br>\n" % (bottom_text)
					mydirectories_text = ""
					for m in mydirectories:
						mydirectories_text = "%s :: %s \n" % (mydirectories_text, m)
					bottom_text = "%s<em>%s</em><br><br>\n" % (bottom_text, mydirectories_text)
				bottom_text = "%sIf you want to change your preferences, (like not receiving SCMLogs emails), go to <a href=\"%s\">%s</a><br>\n\n" % (bottom_text, rel_appurl, self.abs_appurl)

			if self.opt_output == 'out':
				if output_format == 'text':
					output_text = ""
					output_text = "%s\n%s" % (output_text, header_text)
					output_text = "%s\n%s" % (output_text, top_text)
					output_text = "%s\n%s" % (output_text, filtred_logs_text)
					output_text = "%s\n%s" % (output_text, bottom_text)
					print "%s" % (output_text)
				else:
					output_text = htmlStyleCode();
					output_text = "%s\n%s" % (output_text, header_text)
					output_text = "%s\n%s" % (output_text, top_text)
					if output_type == 'raw':
						output_text = "%s\n<pre>%s</pre>" % (output_text, filtred_logs_text)
					else:
						output_text = "%s\n%s" % (output_text, filtred_logs_text)
					output_text = "%s<br>\n%s" % (output_text, bottom_text)
					print "%s" % (output_text)
			else :
				mail_text = ''
				if output_format == 'text':
					if self.opt_mesg != '':
						mail_text = "%sMESSAGE: %s\n" % (mail_text, self.opt_mesg)
					if self.only_user != '':
						mail_text = "%sWARNING: filter on author=%s\n" % (mail_text, self.only_user)
					if self.only_tag != '':
						mail_text = "%sWARNING: filter on tag=%s\n" % (mail_text, self.only_tag)
					mail_text = "%s\n%s" % (mail_text, header_text)
					mail_text = "%s\n%s" % (mail_text, top_text)
					mail_text = "%s\n%s" % (mail_text, filtred_logs_text)
					mail_text = "%s\n%s" % (mail_text, bottom_text)
				else:
					mail_text = mail_text + "<html><head>\n"
					mail_text = mail_text + "<title>SCMLogs email</title>\n"
					mail_text = mail_text + htmlStyleCode();
					mail_text = mail_text + "\n</head>\n";
					mail_text = mail_text + "<body>\n";
					if self.opt_mesg != '':
						mail_text = "%s<div class=warning>MESSAGE: %s</div>\n" % (mail_text, self.opt_mesg)
					if self.only_user != '':
						mail_text = "%s<div class=warning>WARNING: filter on author=%s</div>\n" % (mail_text, self.only_user)
					if self.only_tag != '':
						mail_text = "%s<div class=warning>WARNING: filter on tag=%s</div>\n" % (mail_text, self.only_tag)
					mail_text = "%s\n%s" % (mail_text, header_text)
					mail_text = "%s\n%s" % (mail_text, top_text)

					if output_type == 'raw':
						mail_text = "%s<br>\n<pre>%s</pre>" % (mail_text, filtred_logs_text)
					else:
						mail_text = "%s<br>\n%s" % (mail_text, filtred_logs_text)
					mail_text = "%s<br>\n%s" % (mail_text, bottom_text)
					mail_text = mail_text + "</body></html>\n";


				#DBG# print "Sending mail to %s " % (user_profile)
				self.sendLogByEmailTo (mail_text, user_profile, nb_logs, nb_all_logs, self.opt_subject, (output_format == 'html'))
	
	def get_raw_logsfile_content(self):
		mylogsfile = open (self.logsfile, 'r')
		res = mylogsfile.read()
		mylogsfile.close ()
		self.raw_logsfile_content = res

	def raw_logsfile_count(self):
		reg = '^[-|\*]{40,}$'
		p = re.compile(reg, re.MULTILINE)
		mylogs = p.split (self.raw_logsfile_content);
		return len(mylogs);

	def get_all_logs(self):
		if self.all_logs == None:
			reg = '^[-|\*]{40,}$'
			p = re.compile(reg, re.MULTILINE)
			mylogs = p.split (self.raw_logsfile_content);
			self.all_logs = []
			for log in mylogs[1:] :
				try:
					log_objs = self.getLogsFrom (log);
					#print log_obj.directory + "   <BR>\n"
					self.all_logs.extend (log_objs)
				except:
					print ("\n\n[!] Unable to create object for: \n<pre>[%s]</pre>\n" %(log) );

	def execute(self):
		try:
			# Read the cvs log file
			self.get_raw_logsfile_content()
			# and parse and create a list of Cvs logs objects.
			if self.user != '':
				self.processUser (self.user, self.opt_filter, self.opt_filter_fn, self.logskey)
			elif self.user == 'none':
				self.processUser ('', self.opt_filter, self.opt_filter_fn, self.logskey)
			else:
				# For each user (thoses who have a .cfg file in the correct directory
				# send an email regarding the directories affected.
				for user in self.listOfUsers ():
					#print user
					try:
						self.processUser (user, 'profil', '', self.logskey)
					except:
						print "Error while processing user [%s] \n" % (user)
						print "Unexpected error: %s" %( sys.exc_info()[0])
						einfo = sys.exc_info()
						sys.excepthook(einfo[0], einfo[1], einfo[2]);
		except:
			print "Error while processing execute \n"
			print "Unexpected error: %s" %( sys.exc_info()[0])
			einfo = sys.exc_info()
			sys.excepthook(einfo[0], einfo[1], einfo[2]);
			sys.exit(-1)
