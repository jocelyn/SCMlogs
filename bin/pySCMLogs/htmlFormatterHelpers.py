#!/usr/bin/python

from string import replace;
import bots;

def processed_formatted_html (html):
	return bots.bots_html (html);

def text_to_formated_html_escape (txt):
	# escape the  '<' and '>' to htmlentities
	result = "%s"  % (txt)
	result = replace (result, "<","&lt;")
	result = replace (result, ">","&gt;")
	result = replace (result, "\n", "<br/>\n")
	#result = replace (result, "\ ", "&nbsp;")
	result = replace (result, "    ", "&nbsp;&nbsp;&nbsp;&nbsp;")
	result = replace (result, "\t", "&nbsp;&nbsp;&nbsp;&nbsp;")
	return result

