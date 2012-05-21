<TABLE width="100%"><TR><TD STYLE="text-align:center;">
<FORM ACTION='<?php echo $PHP_SELF?>' NAME='USERFORM'>
<TABLE bgcolor=#ddaa88 cellpadding=5>
<TR><TD STYLE='background-color: #ffddaa' COLSPAN=2>Authentification</TD></TR>
<TR><TD >login</TD><TD><INPUT TYPE=text NAME=user SIZE=8 ></INPUT></TD></TR>
</TABLE>
</FORM>
</TD><TD class=tree width=1>
<?php
	if (isset ($show_users) and ($show_users != "")) {
		$list_of_users = listOfUsers ();
		while (list($key, $usr) = each ($list_of_users)) {
			echo "<A HREF='$PHP_SELF?user=$usr'>$usr</A><BR>\n";
		}
	}
?>
</TR></TABLE>";
