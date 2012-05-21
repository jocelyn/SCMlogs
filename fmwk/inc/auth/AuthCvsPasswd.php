<?php

class SiteAuthCvsPasswd extends SiteAuthentification {
	var $expireTime;
	var $passwd_filename;

	function SiteAuthCvsPasswd($cfg, $passwd_filename) {
		parent::SiteAuthentification (&$cfg);
		$this->passwd_filename = $passwd_filename;
	}

	function initialize() {
		parent::initialize();
	}

	Function Authentificate() {
		global $_SESSION;
//		echo "<pre>"; print_r ($_SESSION); echo "</pre>";
		if (isset($_SESSION['username'])) {
			$this->signed_username = $_SESSION['username'];
		}
	}

	function loginUser($u, $p) {
		global $_SESSION;

		if (!file_exists($this->passwd_filename)) {
			$this->signed_username = Null;
			unset($_SESSION['username']);
			return FALSE;
		} else {
			FMWK_include_once("pear/pear.inc.php");
			include_once "File/Passwd.php";

			$passwd = &File_Passwd::factory('Cvs');
			$passwd->setFile($this->passwd_filename);
			$passwd->load();
			$res = $passwd->verifyPasswd($u, $p);
			if ((!is_object($res)) & $res) {
				$_SESSION['username'] = $u;
				$this->signed_username = $u;
				return TRUE;
			} else {
				$this->signed_username = Null;
				unset($_SESSION['username']);
				return FALSE;
			}
		}
	}

	function logoutUser($user=NULL) {
		// check $user == $this->user();
		$_SESSION['username'] = Null;
		$_COOKIE['username'] = Null;
		unset($_SESSION['username']);
		$this->signed_username = Null;
	}

}


?>
