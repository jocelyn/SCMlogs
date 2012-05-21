<?php

class SiteAuthentification {
	var $config;
	var $signed_username;
	var $auth_name="fmwk_cookies";
	var $expireTime;

	function SiteAuthentification($cfg) {
		$this->config =& $cfg;
		$this->expireTime = 60*60*24*100;
	}

	function initialize() {
		session_set_cookie_params($this->expireTime);
		session_name($this->auth_name);
		session_start();

		if (ini_get("session.use_trans_sid") != 1) {
			setcookie(session_name(),session_id(), time()+$this->expireTime, "/");
		}
	}

	Function authentificate() {
	}

	function user() {
		return $this->signed_username;
	}

	function loginUser($u, $p) {
		return FALSE;
	}

	function logoutUser($user=NULL) {
		$signed_username = Null;
	}

}


?>
