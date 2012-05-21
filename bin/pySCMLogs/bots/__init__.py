"""module bots

"""
__revision__ = "$Id: __init__.py 155 2007-02-08 12:17:34Z jfiat $"
__version__ = "1.0"

import re;
from string import replace;

def bot_compiled_regexp():
       return re.compile ("(([a-z][a-zA-Z_]+) ?#([a-zA-Z0-9_]+))")
       #return re.compile ("(\[([a-zA-Z]+)#([a-zA-Z0-9]+)\])")

# Global
p_bot = bot_compiled_regexp()

def html_url_for(bot,id,name):
	bot_name = "%s.%s" % (__name__, bot)
	try:
		__import__(bot_name)
		cmd = "%s.html_url_for(\"%s\",\"%s\")" % (bot, id, name)
		return eval(cmd)
	except:
		return ""

def url_for(bot,id,name):
	bot_name = "%s.%s" % (__name__, bot)
	try:
		__import__(bot_name)
		cmd = "%s.url_for(\"%s\")" % (bot, id)
		return eval(cmd)
	except:
		return ""

def bots_html_replace (html):
	global p_bot
#issue if more than one link with same substring .. 123 and 12345
	results = p_bot.findall (html)
	offset = 0
	if results:
		for res in results:
			url = html_url_for(res[1],res[2],res[0])
			if len(url) > 0:
				html = replace (html, res[0], url)
				offset = offset + len(url) - len(res[0])
	return html

def bots_extracted_values (txt):
	global p_bot
#issue if more than one link with same substring .. 123 and 12345
	result = []
	results = p_bot.findall (txt)
	if results:
		for res in results:
			url = url_for(res[1],res[2],res[0])
			if len(url) > 0:
				result.append ([res[0], url])
	return result

def bots_html (html):
	global p_bot
	iterator = p_bot.finditer( html)
	offset = 0
	i1 = i2 = 0
	for res in iterator:
		url = html_url_for(res.group(2),res.group(3),res.group(0))
		if len(url) > 0:
			i1 = offset + res.start(0)
			i2 = offset + res.end(0)
			html = "%s%s%s" % (html[:i1], url, html[i2:])
			offset = offset + len(url) - len(res.group(0))
	return html

