#!/usr/bin/python

class webappUrlEngine:
	def __init__ (self, baseurl, reponame):
		self.baseurl = baseurl;
		self.reponame = reponame;

	def urlShowFile (self, file, dir, r1): return ""
	def urlBlameFile (self, file, dir, r1): return ""
	def urlDiffFile (self, file, dir, r1, r2): return ""
	def urlShowDir (self, dir, r1): return ""
	def urlDiffDir (self, dir, r1, r2): return ""
	def urlChangeSet (self, r1, dir, file): return ""
	def urlRevSet(self, rev): return ""

class webscmlogs (webappUrlEngine):
	def set_default_webapp (self, wapp):
		self.webapp = wapp
	def urlTmp (self, op):
		url = "%s?op=%s&repname=%s" % (self.baseurl, op, self.reponame)
		if len (self.webapp) > 0:
			url = "%s&webapp=%s" % (url, self.webapp);
		return url;
	def urlShowFile (self, file, dir, r1):
		url = self.urlTmp ('fileshow')
		if r1 >= 0: url = "%s&rev=%s" % (url, r1)
		url = "%s&path=%s/%s" % (url, dir,file)
		return url
	def urlBlameFile (self, file, dir, r1):
		url = self.urlTmp ('fileblame')
		if r1 >= 0: url = "%s&rev=%s" % (url, r1)
		url = "%s&path=%s/%s" % (url, dir,file)
		return url
	def urlDiffFile (self, file, dir, r1, r2):
		url = self.urlTmp ('filediff')
		if r1 >= 0: url = "%s&r1=%s&r2=%s" % (url, r1, r2)
		url = "%s&path=%s/%s" % (url, dir,file)
		return url
	def urlShowDir (self, dir, r1):
		url = self.urlTmp ('dirshow')
		if r1 >= 0: url = "%s&rev=%s" % (url, r1)
		url = "%s&path=%s" % (url, dir)
		return url
	def urlDiffDir (self, dir, r1, r2):
		url = self.urlTmp ('dirdiff')
		url = "%s&path=%s&r1=%s&r2=%s" % (url, dir, r1, r2)
#		url = "%s&compare[]=/%s@%s&compare[]=/%s@%s" % (url, dir, r1, dir, r2)
		return url
	def urlChangeSet (self, r1, dir, file):
		url = self.urlTmp ('changeset')
		url = "%s&path=%s/%s&r1=%s" % (url, dir, file, r1)
		return url
	def urlRevSet (self, rev):
		url = self.urlTmp ('revset')
		url = "%s&rev=%s" % (url, rev)
		return url

