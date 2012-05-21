<DIV class=warning> ADMIN ZONE </DIV>
<?php

	if (isset($password) and ($password == "admin!")) {
		$access = True;
	} else {
		echo "Access Restricted<BR>";
		echo "<TABLE bgcolor=#ddaa88 cellpadding=5><TR><TD STYLE='background-color: #ffddaa;'>Password</TD></TR><TR><TD>";
		echo "<FORM ACTION=\"$PHP_SELF\" METHOD=\"POST\">\n";
		echo "<INPUT TYPE=hidden NAME=goto VALUE=$goto >\n";
		echo "<INPUT TYPE=password NAME=password >";
		echo "</FORM>";
		echo "</TD></TR></TABLE>\n";
		$access = False;
		echo "(password = [admin!])<BR>";
	}

	if ($access == True) {

		include "inc/show_config_tree.inc";
		$list_of_users = listOfUsers ();
		sort ($list_of_users);
		include "inc/stats.inc";
		echo "<TABLE>";
		while (list($key, $usr) = each ($list_of_users)) {
			echo "<TR><TD class=smalltitle >$usr</TD>\n";
			echo "<TD>";
			$user_modules = userModules ($usr);
			showUserTree ($user_modules);
			echo "</TD></TR>\n";
		}
		echo "</TABLE>\n";
		echo "</TD></TR></TABLE>\n";
	}
?>
