#!/usr/local/bin/python

# /usr/bin/python /home/jfiat/public_html/Berlios/djocewebtools/SCMLogs/bin/svn_log.py -rev $REV -svnrepo $REPOS -l /home/jfiat/svndir/LOGS/commits.txt
#
# Usage:  svn_log.py -rev revision -svnrepo path -l logfile 
#
#	-rev revision
#	-svnrepo repository_path
#	-l logfile	- for the logfile to append to
#
#
# here is what the output looks like:
#

import os;
import sys;
import re;
from time import strftime, localtime, time;
from string import split, replace, rstrip, strip;
import smtplib;
import popen2;

# Configuration
debug_enabled = 0


organization_name    = "Eiffel Software"
smtp_server          = "smtp.ise"
at_domain_name       = "@eiffel.com"
sender_name          = "SCMLogs [ES]"
sender_email         = "postmaster" + at_domain_name
superuser_email      = "jfiat" + at_domain_name

def dprint (txt):
	if debug_enabled:
		sys.stderr.write (txt)
		alogfile = open ("/tmp/svn_log_add_del_.logs", 'a')
		alogfile.write (txt)
		alogfile.flush ()
		alogfile.close ()

def sendMailToFromSubjectOfOn (z_to_emails, z_from_name, z_from, z_mail, z_server) :
        fromaddr = 'From: "' + z_from_name + '" <' + z_from + '>'
        msg = z_mail
        server = smtplib.SMTP(z_server)
        for m in z_to_emails :
                toaddrs = 'To: <' + m + '>'
                server.sendmail(fromaddr, toaddrs, msg)
        server.quit()

def usage ():
	return """
Usage:  svn_monitor_add_del.py -rev revision -svnrepo path 
	-rev revision   - revision
	-svnrepo path 	- repository path
	-e email	    - for the logfile to append to
"""

def output_of (cmd):
#	output = subprocess.Popen(string.split (cmd), stdout=subprocess.PIPE).communicate()[0]
#	dprint ("Exec: " + cmd + "\n")
	(std_output, std_input) = popen2.popen2(cmd);
	output = std_output.read();
	return output[:-1]

def process_main():
	dprint ("\n\n")
	svnlook_cmd = "svnlook"
	revision = ''
	repository = ''
	to_emails = []
	argc = len (sys.argv)
	i = 1
	while i < argc:
		arg = sys.argv[i]
		i = i + 1
		if arg == '-rev' :
			revision = sys.argv[i]
			i = i + 1
		elif arg == '-svnrepo' :
			repository = sys.argv[i]
			i = i + 1
		elif arg == '-e' :
			to_emails.append (sys.argv[i])
			i = i + 1
		#else :
			#nop
			#print "? Ignored : [%s] ?" %(arg)

	dprint ("Monitoring commit %s@%s\n" % (repository, revision))

	if to_emails.count == 0:
		to_emails.append (superuser_email)

	if repository == '' or revision == '':
		sys.stderr.write (usage ())
		sys.exit (2)

	text_changed = output_of ("%s changed %s -r %s" %(svnlook_cmd, repository, revision))
	if len (text_changed) > 0:
		dprint(text_changed + "\n")
		changed_lines = text_changed.split ('\n')
		additions = []
		deletions = []
		for line in changed_lines:
			if len (line) > 0:
				ch = line[0]
				if ch == 'A':
					additions.append (line [4:])
				elif ch == 'D':
					deletions.append (line [4:])

		if len(additions) + len(deletions) > 0:
			### Let's get the message content 
			text = ''
			date = output_of ("%s date %s -r %s" % (svnlook_cmd, repository, revision))
			#short_date = date[:10]
			text_logs = output_of ("%s log %s -r %s" %(svnlook_cmd, repository, revision))
			login = output_of ("%s author %s -r %s" % (svnlook_cmd, repository, revision))
			if len (login) == 0:
				if os.environ.has_key('USER'):
					login = os.environ['USER'] + "?"
				else:
					login = "unknown"
			text = "%s\n" % (text)

			### Build the log text
			text = "-"*72 + "\n"
			text = "%sRepository: %s\n" % (text, repository)
			text = "%sRevision:   %s\n" % (text, revision)
			text = "%sDate:       %s\n" % (text, date)
			text = "%sAuthor:     %s\n" % (text, login)

			if len(deletions) > 0:
				text = "%s\nDeletions: %d entries\n" % (text, len(deletions))
				for i in deletions:
					text = "%s - %s\n" % (text, i)

			if len(additions) > 0:
				text = "%s\nAdditions: %d entries\n" % (text, len(additions))
				for i in additions:
					text = "%s + %s\n" % (text, i)

			text = "%s\nMessage:\n%s\n" % (text, text_logs)
			text = "%s\n" % (text)
			dprint (text)

			z_today = strftime ("%A %d %B %Y (%H:%M:%S)", localtime(time()))
			cc_emails_str = ''

			message_header = ""
			message_header = message_header +  "X-Mailer: PyJoceMailer\n"
			message_header = message_header +  "Reply-To: <%s>\n" % (sender_email)
			message_header = message_header +  'From: "' + sender_name + ' - ' + login + '" <' + sender_email + '>\n'
			to_emails_str = "%s" % (to_emails[0]);
			if len(to_emails) > 1:
				cc_emails_str = " <%s> " % (to_emails[1]);
				if len(to_emails) > 2:
					for m in to_emails [2:]:
						cc_emails_str = "%s, <%s> " % (cc_emails_str, m)
			dprint ("\nTO:" + to_emails_str)
			dprint ("\nCC:" + cc_emails_str)
			message_header = message_header +  "To: <%s>\n" % (to_emails_str)
			if len(cc_emails_str) > 0:
				message_header = "%sCc: %s\n" % (message_header, cc_emails_str)
			message_header = message_header +  "Subject: [SCM:added=%d deleted=%d] by %s on %s\n" % (len(additions), len(deletions), login, date)
			message_header = message_header +  "Organization: %s \n" % (organization_name)

			message = "%s\n%s" % (message_header, text)
			dprint("\nMESSAGE=\n" + message)
			sendMailToFromSubjectOfOn (to_emails, sender_name, sender_email, message, smtp_server)

if __name__ == '__main__':
	process_main()
	sys.exit();
