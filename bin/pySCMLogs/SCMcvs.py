#!/usr/bin/python

from SCMlog import *;

# class Declaration
class CvsLogEntry(SCMLogEntry):
	def __init__ (self, conf, webUrl_engine):
		SCMLogEntry.__init__ (self, conf, webUrl_engine)

		self.info = '';
		self.tag = '';

	def load_log (self, log):
		### Analyse log
		message = '';
		tag = '';
		loglines = (re.split ('\n', log))[1:-1];

		cursor = 0;
		self.date = (loglines[cursor])[len("Date:	"):];
		cursor = cursor + 1
		self.author = (loglines[cursor])[len("Author:	"):];
		cursor = cursor + 1
		self.info = (loglines[cursor])[len("Info:	"):];
		if self.info != '':
			cursor = cursor + 1

		cursor = cursor + 1
		self.directory = self.repository ((loglines[cursor])[len("Update of "):])
		
		cursor = cursor + 2

		line = loglines[cursor]
		mesg = ''
		while line[:len("Log Message")] != "Log Message":
			mesg = mesg + line + "\n"
			line = loglines[cursor]
			cursor = cursor + 1

		self.logmessage = ''
		for line in loglines[cursor:]:
			line = rstrip (line)
			if len(line) > 0:
				self.logmessage = self.logmessage + line + "\n"

		message = mesg
		if message:
			try:
				(self.modified, tag) = self.getFilesAndTagFor ("Modified Files", message)
				if tag: self.tag = tag
				(self.added, tag) = self.getFilesAndTagFor ("Added Files", message)
				if tag: self.tag = tag
				(self.removed, tag) = self.getFilesAndTagFor ("Removed Files", message)
				if tag: self.tag = tag
			except:
				self.modified = [];
				self.added = [];
				self.removed = [];
				self.tag = "";
				self.error_message = "Error: issue raised while processing this log:\n%s" % (message);

	def repository (self, directory):
		return directory[len(self.config.repository_path):]

	def getFilesAndTagFor (self, type, mesg):
		text = ''
		tag = ''
		msg_regexp = "^(.|\n)*%s:\s*\n((\s.+\n)+)" % (type);
		pmsg = re.compile (msg_regexp);
		result = pmsg.match (mesg,0)
		if result:
			text = result.group (2)
			files_regexp = "^\s*Tag:\s+(.*)\s+((.|\n)*)$";
			pfiles = re.compile (files_regexp);
			result = pfiles.search (text,0)
			if result:
				tag = result.group (1)
				text = result.group (2)
		return (split (text), tag)


	def to_logEntries(self):
		return [self]

	def info_to_text (self, offset):
		result = "%s[ Info       ] %s\n"  %(offset, self.info);
		if self.tag:
			result =  "%s%s[ Tag        ] %s\n"  %(result, offset, self.tag);
		return result

	def info_to_html (self):
		result = "";
		if self.info:
			infos = split (self.info)
			result = "%s<tr><td class=i >Info/Diff</td><td colspan=3>" % (result)
			infos_text = ""
			for diff in infos:
				diff_info = split (diff, ',')
				if len (diff_info) > 1:
					infos_text = "%s\n\t<a href=\"%s\" target=\"_MyLogs_\">%s</a>" % (infos_text, \
							self.webappUrlForDiffFileInDirectory (diff_info[0], self.directory, diff_info[1], diff_info[2]), \
								diff_info[0] )
					infos_text = "%s<small class=df> (%s|%s)</small>, " % (infos_text, \
							diff_info[1], diff_info[2])
			if len (infos_text) == 0:
				#print self.directory + " " + self.info + "<BR>"
				### FIXME: manage the "path - Info" context !!!
				infos_text = self.info
			result = "%s%s</td></tr>\n"  %(result, infos_text);
			result = "%s\n" % (result)
		if self.tag:
			result =  "%s<tr><td class=i >Tag</td><td colspan=3>%s</td></tr>\n"  %(result, self.tag);
		return result

	def list_to_html (self, lst_id, lst, title, cssclass):
		result = ""
		if len(lst) > 0:
			result = "%s<tr><td class=%s >%s</td><td colspan=3>" % (result, cssclass, title)
			for file in lst: 
				result = "%s\n\t - <a href=\"%s\" target=\"_MyLogs_\">%s</a> " %(result, \
						self.webappUrlForShowFileInDirectory (file, self.directory), \
						file \
					)
				result = "%s<br/>\n" % (result)
			result = "%s</td></tr>\n" % (result)
		return result
