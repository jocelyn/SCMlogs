<?php

class SiteApp_sign extends SiteApplication {
	var $asked_app;
	var $wop, $wusername, $wpassword;
	var $message="";
	var $user_signed = FALSE;

	Function SiteApp_sign ($app, $site) {
		parent::SiteApplication($app, $site);
	}

	function setAskedApplication ($appname) {
		$this->asked_app = $appname;
	}
	function setMessage ($m) {
		$this->message = $m;
	}
	Function getData() {
		parent::getData();
		$this->wop = value_from_POST_GET(FMWK_PARAM_OP, 'sign');
		if (empty($this->asked_app)) {
			$this->asked_app = value_from_POST('asked_app', Null);
		}
		$op = $this->wop;
		$siteuser = $this->site->username();
		$this->user_signed = isset($siteuser);
		if ($this->user_signed) {
			$this->wusername = $siteuser;
		} else {
			$this->wusername = value_from_POST('username', Null);
			$this->wpassword = value_from_POST('password', Null);
		}
		switch ($op) {
			case 'logout':
				$auth = $this->site->auth;
				$auth->logoutUser ($this->wusername);
				$this->site->redirectToApp ($this->asked_app);
				exit();
				break;
			case 'login':
				if ($this->user_signed) {
					$this->message .= "Already authentificated in";
					$this->wop = 'info';
				} else {
					$is_ok = FALSE;
					if (!empty($this->wusername)) {
						$auth = $this->site->auth;
						if (isset ($auth)) {
							$is_ok = $auth->loginUser ($this->wusername, $this->wpassword);
						}
					}
					if ($is_ok) {
						$this->message .= "Welcome";
						$this->wop = 'login';
						$this->site->redirectToApp ($this->asked_app);
					} else {
						$this->message .= "Invalid login or password";
						$this->wop = 'sign';
					}
				}
				break;
			default:
				$this->wop = 'info';
				break;
		}
	}

	/* Protected */
	Function printContent() {
		echo "<h2>ADMINISTRATION</h2>";
		echo "Back to [<a href=\"" . $this->site->applicationUrl($this->asked_app) ."\">Back</a>] <br/>";
		$op = $this->wop;
		switch ($op) {
			case 'logout':
				echo "You are not logged out<br/>";

				break;
			case 'login':
				echo "You are now logged in as [".$this->wusername."]<br/>";
				break;
			case 'info':
				if ($this->user_signed) {
?>
<form action="<?php echo $this->site->applicationUrl("sign")?>" method="post">
<div style="border: solid 1px #ccc; padding: 5px; margin: 5px; background-color: #ffeecc;">
<?php if (!empty($this->message)) { echo $this->message; }?>
<p>Username : <input type="text" name="username" value="<?php echo $this->wusername; ?>" /></p>
<input type="submit" name="<?php echo FMWK_PARAM_OP ?>" value="logout" />
</form>
<?php
					break;
				}
			case 'sign':
			default:
?>
	<form action="<?php echo $this->site->applicationUrl("sign")?>" method="post">
<input type="hidden" name="asked_app" value="<?php echo $this->asked_app; ?>" />
<div style="border: solid 1px #ccc; padding: 5px; margin: 5px; background-color: #ffeecc;">
<?php if (!empty($this->message)) { echo $this->message; }?>
<p>Username : <input type="text" name="username" value="<?php echo $this->wusername; ?>" /></p>
<p>Password : <input type="password" name="password" value="" /></p>
<input type="submit" name="<?php echo FMWK_PARAM_OP ?>" value="login" />
</div>
</form>
<?php
				break;
		}
		parent::printContent();
	}
}

?>
