#!/usr/bin/python

from SCMlog import * ;
import string;

class LogEntryDetails:
	def __init__ (self, dir):
		self.directory = dir;
		self.added = [];
		self.removed = [];
		self.modified = [];

class SvnLogEntry(SCMLogEntry):
	def __init__ (self, conf, webUrl_engine):
		SCMLogEntry.__init__(self, conf, webUrl_engine)
		self.revision = 0;

	def info_to_text (self, offset):
		return "%s[ Revision   ] %d\n"  %(offset, self.revision)

	def info_to_html (self):
		result = ""
		if self.revision > 0:
			result =  "%s<tr><td class=i >Revision</td><td colspan=3>" %(result)
			result = "%s<a href=\"%s\" target=\"_MyLogs_\">%d</a> " %(result, \
					self.webappUrlForRevSet (self.revision), \
					self.revision \
				)
			result = "%s</td></tr>\n"  %(result);
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
				tmp = "<a href=\"%s\" target=\"_MyLogs_\">r%d</a>" %( \
						self.webappUrlForShowFileInDirectory (file, self.directory, self.revision), \
						self.revision \
					)
				if lst_id != self.added_id:
					tmp = "%s | <a href=\"%s\" target=\"_MyLogs_\">diff</a>" %(tmp, \
							self.webappUrlForDiffFileInDirectory (file, self.directory, self.revision, self.revision - 1) \
						)
					tmp = "%s | <a href=\"%s\" target=\"_MyLogs_\">blame</a>" %(tmp, \
							self.webappUrlForBlameFileInDirectory (file, self.directory, self.revision) \
						)
				if len (tmp) > 0:
					result = "%s (<small class=df>%s</small>)" % (result, tmp)
				tmp =''
				result = "%s<br/>\n" % (result)
			result = "%s</td></tr>\n" % (result)
		return result

class SvnLogEntries:
	def __init__ (self, conf, webUrl_engine):
		self.config = conf;
		self.webUrl_engine = webUrl_engine;
		self.date = '';
		self.author = '';
		self.directories = {};
		self.logmessage = '';
		self.revision = 0;

	def load_log (self, log):
		#print "<pre style='border: 1px solid blue;'>" + log + "</pre>"
		if len(log) >4:
			t = log[:len("Date:")+1].strip()
			if t == "Date:":
				self.load_normal_log (log)
			else:
				self.load_svnlog (log)

	def load_svnlog (self, log):
		#print "load_svnlog"
		lines = string.split (log, "\n")
		if len (lines) > 1:
			details = string.split (lines[1], '|')
			self.date = details[2].strip()
			self.author = details[1].strip()
			self.revision = atoi (details[0][1:].strip())

			strNb = details[3]
			s = string.split (strNb,' ', 2)[1]
			try:
				nb = string.atoi(s)
			except:
				nb = 1
			# Get changed path
			index = 3;
			for line in lines[index:]:
				t_line = line.strip()
				if len(t_line) == 0:
					break;
				ch = t_line[0]
				if ch == 'A':
					self.append_added (strip(t_line[1:]))
				elif ch == 'U' or ch =='M':
					self.append_modified (strip(t_line[1:]))
				elif ch == 'D':
					self.append_removed (strip(t_line[1:]))
				index = index + 1

			# Get log message
			index = index + 1
			self.logmessage = ""
			for line in lines[index:index + nb] :
				self.logmessage = "%s%s\n" % (self.logmessage, line)

	def load_normal_log (self, log):
		### Analyse log
		#print "<pre style='border: 1px solid red;'>" + log + "</pre>"
		loglines = (re.split ('\n', log))[1:-1];

		message = '';
		tag = '';
		cursor = 0;
		self.date = (loglines[cursor])[len("Date:"):].strip();
		cursor = cursor + 1
		self.author = (loglines[cursor])[len("Author:"):].strip();
		if len (self.author) == 0:
			self.author = "unknown"
		cursor = cursor + 1
		line = loglines[cursor];
		tmp = line[len("Revision:"):].strip()
		self.revision = atoi(tmp)
		#print "[[%d]]<br/>" % (self.revision);
		cursor = cursor + 1
		line = loglines[cursor];
		if line[:len("DirChanged:")] == "DirChanged:":
			dirchanged_nb = atoi ((loglines[cursor])[len("DirChanged:"):])
			cursor = cursor + 1
			if dirchanged_nb > 0:
				for i in range (1, dirchanged_nb + 1):
					self.add_dir (loglines[cursor])
					cursor = cursor + 1
		else:
			cursor = cursor
		changed_nb = atoi ((loglines[cursor])[len("Changed:"):])
		cursor = cursor + 1
#		print ">>> %s\n" % (loglines[cursor])
		if changed_nb > 0:
			for i in range (1, changed_nb + 1):
				line = loglines[cursor]
				ch = line[0]
				if ch == 'A':
					self.append_added (strip(line[1:]))
				elif ch == 'U' or ch =='M':
					if line[2] == 'U':
						# Files AND Prop changes
						self.append_modified (strip(line[2:]))
					else:
						self.append_modified (strip(line[1:]))
				elif ch == 'D':
					self.append_removed (strip(line[1:]))
				elif ch == '_':
					# Property
					if line[2] == 'U':
						self.append_modified (strip(line[2:]))
				cursor = cursor + 1

		loglines_nb = atoi ((loglines[cursor])[len("Logs:"):]) + 2
		cursor = cursor + 1
		for line in loglines[cursor:]:
			loglines_nb = loglines_nb - 1
			#print "#%d[%s]<br/>" % (loglines_nb, line)
			cursor = cursor + 1
			self.logmessage = "%s%s\n" % (self.logmessage, line)
		if loglines_nb != 0:
			print "Warning: Issue while processing logmessage: delta = %d <br/>" % (loglines_nb)
		if len(self.logmessage) > 0:
			rstrip(self.logmessage)
			while len(self.logmessage) > 0 and (self.logmessage[-1] == '\n' or self.logmessage[-1] == ' '):
				self.logmessage = self.logmessage[:-1]
				rstrip(self.logmessage)
		#print "[[%d]] completed<br/>" % (self.revision);

	def to_logEntries(self):
		result = [];
		for d in self.directories:
			#print "<pre>" + d + "</pre>"
			o = SvnLogEntry(self.config, self.webUrl_engine)

			o.date = self.date;
			o.author = self.author;
			o.revision = self.revision;
			o.directory = d;
			ldir = self.directories[d]
			o.added = ldir.added;
			o.modified = ldir.modified;
			o.removed = ldir.removed;
			o.logmessage = self.logmessage;

			#print "<pre>" + o.to_text() + "</pre>"
			result.append (o)
		return result

	def dirname_from (self, path):
		d = os.path.dirname (path)
#		print ">>> dirname: %s -> %s" % (path, d)
		return d
	def filename_from (self, path):
		f = os.path.basename (path)
		return f

	def add_dir (self, d):
		dir = d
		if dir[-1] == '/':
			dir = dir[:-1]
		self.directories[dir] = LogEntryDetails(dir)
	def path_to_stripped_path (self, path):
		p = path.split ('(')[0]
		return strip (p)
	def append_added (self, path):
		p = self.path_to_stripped_path (path);
		d = self.dirname_from (p)
		if not self.directories.has_key (d):
			self.add_dir(d)
		f = self.filename_from (p)
		self.directories[d].added.append (f)
	def append_modified (self, path):
		p = self.path_to_stripped_path (path);
		d = self.dirname_from (p)
		if not self.directories.has_key (d):
			self.add_dir(d)
		f = self.filename_from (p)
		self.directories[d].modified.append (f)
	def append_removed (self, path):
		p = self.path_to_stripped_path (path);
		d = self.dirname_from (p)
		if not self.directories.has_key (d):
			self.add_dir(d)
		f = self.filename_from (p)
		self.directories[d].removed.append (f)


