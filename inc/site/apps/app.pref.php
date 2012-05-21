<?php

class SiteApp_pref extends ScmlogsSiteApplication {
	Function SiteApp_pref ($app, $site) {
		$this->require_auth = TRUE;
		$this->use_tpl_engine = TRUE;
		parent::ScmlogsSiteApplication($app, $site);
	}

	Function getData() {
		parent::getData();
		$param =& $this->param;
		require_once INC_DIR . "users.inc";


		if (!isset($op) && isset ($_GET['op'])) { $op = $_GET['op']; }
		if (!isset($op) && isset ($_POST['op'])) { $op = $_POST['op']; }
		if (!isset($op)) { $op = ''; }
		if (!isset($oppref) && isset ($_GET['oppref'])) { $oppref = $_GET['oppref']; }
		if (!isset($oppref) && isset ($_POST['oppref'])) { $oppref = $_POST['oppref']; }

		$user = $this->site->username();
		ob_start ();
			loadUserPref ($user);

			if (isset ($oppref) and ($oppref == 'SavePref')) {
				if (isset ($_POST['email'])) { $email = $_POST['email']; }
				if (isset ($_POST['activate'])) { $activate = $_POST['activate']; }
				if (isset ($_POST['sendemptylogs'])) { $sendemptylogs = $_POST['sendemptylogs']; }
				if (isset ($_POST['sendtype'])) { $sendtype = $_POST['sendtype']; }
				if (isset ($_POST['format'])) { $format = $_POST['format']; }
				$tmp = "";
				$tmp .= "email=".$email."\n";
				if (!isset ($activate) or ($activate != 'on')) { $activate = 'off'; }
				$tmp .= "send_email=".$activate."\n";
				if (!isset ($sendemptylogs) or ($sendemptylogs != 'on')) { $sendemptylogs = 'off'; }
				$tmp .= "send_emptylogs=".$sendemptylogs."\n";
				if ($sendtype != 'raw') { $sendtype = 'filtered'; }
				$tmp .= "send_type=".$sendtype."\n";
				if ($format != 'html') { $format = 'text'; }
				$tmp .= "send_format=".$format."\n";

				echo "<div class=info>";
				echo "Pref Saved for $user";
	//			echo "<pre style=\"text-align: left; font-size: smaller;\">$tmp</pre>";
				echo "</div>";
				saveUserPref ($tmp, $user);
				loadUserPref ($user);
				echo "<br/>";
			} 

			if (True) {
				echo "<form action='$PHP_SELF' name='PREF-FORM' method='POST' >\n";
				echo "<input type='hidden' name='op' value='$op' >\n";
				echo "<input type='hidden' name='user' value='$user' >\n";

				global $SCMLOGS;
				if ($SCMLOGS['email'] == '') { $SCMLOGS['email'] = $user; }
				echo "Email address <input type=text name=email value=\"".$SCMLOGS['email']."\" ><br><br>";

				echo "<h3>Sending or not ?</h3>";
				echo "<ul>";
				if ($SCMLOGS['send_email'] == 'on') { $is_checked = 'CHECKED'; } else { $is_checked = '';}
				echo "<input type=checkbox name=activate $is_checked >Email sending <br>";
				if ($SCMLOGS['send_emptylogs'] == 'on') { $is_checked = 'CHECKED'; } else { $is_checked = '';}
				echo "<input type=checkbox name=sendemptylogs $is_checked >Email empty logs <br>";
				echo "</ul>";

				echo "<h3>Output message type:</h3>";
				echo "<ul>";
				if ($SCMLOGS['send_type'] == 'filtered') { $is_checked = 'CHECKED'; } else { $is_checked = '';}
				echo "<INPUT TYPE=radio NAME=sendtype VALUE=\"separate\" $is_checked >Filtered logs <br/>";
				if ($SCMLOGS['send_type'] == 'raw') { $is_checked = 'CHECKED'; } else { $is_checked = '';}
				echo "<INPUT TYPE=radio NAME=sendtype VALUE=\"raw\" $is_checked >Raw logs <br/>";
				echo "</ul>";

				echo "<h3>Commits log message format:</h3>";
				echo "<ul>";
				if ($SCMLOGS['send_format'] == 'html') { $is_checked = 'CHECKED'; } else { $is_checked = '';}
				echo "<INPUT TYPE=radio NAME=format VALUE=\"html\" $is_checked >HTML <br/>";
				if ($SCMLOGS['send_format'] == 'text') { $is_checked = 'CHECKED'; } else { $is_checked = '';}
				echo "<input type=radio name=format value=\"text\" $is_checked >TEXT <br/> ";
				echo "</ul>";

				echo "<input type=submit name=oppref value=\"SavePref\" > ";
				echo "<input type=reset name=oppref value=\"Reset\" > ";
				echo "</FORM>\n";
			}	
		$param['output'] = ob_get_contents();
		ob_end_clean();  
	}

	Function prepareTplEngine() {
		parent::prepareTplEngine();
		$t =& $this->tpl_engine;
		$param =& $this->param;

		$t->setFile ("MainTemplate", "pref.html");
		$t->setVar ("VAR_PAGE_PREF", $param['output']);
		$t->parse("VAR_PAGE_MAIN","MainTemplate");
		
	}

}

?>
