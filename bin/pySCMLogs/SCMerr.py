#!/usr/bin/python

from SCMlog import *;

# class Declaration
class ErrLogEntry(SCMLogEntry):
	def __init__ (self, conf, webUrl_engine):
		SCMLogEntry.__init__ (self, conf, webUrl_engine)
		self.info = '';
		self.tag = '';

	def load_log (self, log):
		### Analyse log
		self.directory = '.::[ Errors ]::.'
		self.author = ''
		self.date = ''
		self.revision = 0
		self.error_message = log
		self.info = "Error occurred while parsing this log message."

	def info_to_text (self, offset):
		return "%s[ Info       ] %s\n"  %(offset, self.info)

	def info_to_html (self):
		result = ""
		if self.info:
			result = "%s<tr><td class=info >Info</td><td colspan=3>%s</td></tr>\n" % (result, self.info)
		return result
