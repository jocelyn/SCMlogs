<?php

class SitePearAuthDB extends SiteAuthentification {
	function SitePearAuthDB($cfg) {
		parent::SiteAuthentification (&$cfg);
	}

	function initialize() {
	}

	Function Authentificate() {
		$expireTime = 60*60*24*100; // 100 days
		session_set_cookie_params($expireTime);
		$auth =& new Auth ("DB", 
						array (	
								"dsn" => $this->config['db.dsn'], 
								"table" => $this->config['db.members.table'],
								"usernamecol" => "login" ,
								"passwordcol" => "password",
								"sessionName" => $this->config['Auth.SessionName'],
								"cryptType" => "PlainPassword_to_CryptedPassword",
								"db_fields" => array('id', 'groups', 'activation_code')
								) ,
								NULL, FALSE
				);
		$this->auth =& $auth;
		$auth->setExpire($expireTime, TRUE); // good place ???
		$auth->start ();
		if ( $auth->checkAuth ()) {

			$this->username = $auth->getUsername();
			if ($this->is_signing_out) {
				$this->logout_user();
			}
			if (isset ($this->username)) {
				$this->exptime = $auth->sessionValidThru();
				$this->user =& new SiteUser ($this->username);
				$this->user->set_groups_from_string ($auth->getAuthData('groups'));
				$this->logintime =& $auth->getAuthData('login.time');
				$auth->setAuthData ('login.lastaccessed', time(), TRUE);
				if ($this->is_signing_in) {
					$this->user->set_activation_code ($auth->getAuthData('activation_code'));
//					echo "[[".$this->user->activation_code."]]<br>";
				}
			}
		}
	}

	function user() {
	}

	function loginUser($user, $pass) {
		return $user == 'jfiat';
	}

	function logoutUser($user) {
		// check $user == $this->user();
	}

}


?>
