<DIV align='left'>
<PRE>
	Overview
	Feature
   		Get your profile account
   		Build your profile
   		Change you preferences
	How to
	Need help ?
</PRE>

<HR>
<H2>Overview</H2>
<UL>
The main goal of the SCMLogs application is to provide the developper,<BR>
a better way to be notified about CVS commits.<BR>
<BR>
The way it is done is simple:
<UL>
<LI>Each time a commit is done, CVS will add the related cvs information in a log file (<EM>/export/cvs/logs/commits.txt</EM>).</LI>
<LI>Every half day, a script is launched via the cron manager. This script process the commits.txt file, and send it to each developer.</LI>
</UL>
<BR>
But the SCMLogs application provide a better way to inform the developer. <BR>
Indeed each developer can register himself to receive <STRONG>only commits information about the modules they want</STRONG>.<BR>
Basically, each developer can set his profile to fit his needs. This can be done via the web interface of SCMLogs.<BR>
<BR>
Then the developer will receive half daily email with the cvs Logs. The form used is HTML, and the mail contains the U.R.L pointing to more advanced information, like full history, diff, and so on.<BR>
</UL>


<HR>
<H2>Features</H2>
<UL>
	<LI>receive email containing the last group of CVS commits.</LI>
	<LI>filter on the logs, to get only those which fit your needs</LI>
	<LI>more to come ...</LI>
</UL>


<HR>
<H2>How To ...</H2>
<UL>
<h3>Get your profile account</h3>
	<UL>
	Go to the authentification web page.<BR>
	Enter your login<BR>
	- if your account already exists, your will get your account web page.<BR>
	- if not, you will be asked if you want to create it or not.<BR>
	Be carefull, your login must be your email address (at login@domain).<BR>
	Otherwise you won't receive the emails.
	
	</UL>
<h3>Build your profile</h3>
	<UL>
	Once you are logged in.<BR>
	Go to the "Edit" section.<BR>
	You will get the modules tree.<BR>
	And also the text form representing your choice.<BR>
	<BR>
	Let's assume we have :
<PRE>
 [X] a
     [ ] b
         [ ] b1
         [ ] b2
     [ ] c

</PRE>
	Basically, if you have "a", you will receive all the information concerning the "a" module, and all its submodules (a,b,b1,b2). The equivalent would be "rainbow.*".<BR>
	If you want to receive only "a/b/b1" and "a/c", you will need to set your tree profile as following:
<PRE>
 [ ] a
     [ ] b
         [X] b1
         [ ] b2
     [X] c
</PRE>
if you checked the "b" checkbox, for instance, you will receive "a/b" and all the submodules, that means "a/b", "a/b/b1", and "a/b/b2"<BR>
	
	</UL>
<h3>Change your preferences</h3>
	<UL> Not yet ready ... </UL>
</UL>
<HR>
<H2>Need help ?</H2>
	<UL>
	Ask around you ;-)<BR>
But if you have any question: <A HREF="mailto:jfiat@djoce.net?subject=[SCMLogs] HowTo ?">email me</A>.<BR>
	</UL>
<BR><BR>
</DIV>
