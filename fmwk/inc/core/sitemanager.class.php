<?php

FMWK_require_once ('core'.DIRECTORY_SEPARATOR.'siteapplication.class.php');

class SiteManager {
	var $config;
	var $appname;
	var $application;
	var $baseurl;
	var $title;
	var $auth;
	function SiteManager (&$cfg) {
		$this->config =& $cfg;
	}
	function initialize($auth=Null) {
		$this->baseurl = $this->config->value('site.baseurl');
		$this->title = $this->config->value('site.title');
		if (isset($auth)) {
			$auth->initialize();
			$auth->authentificate();
		}
		$this->auth =& $auth;
	}
	function registerApplication($appname) {
		$this->appname = $appname;
	}
	function prepareData() {
		$this->loadApplication ($this->appname);
		if ($this->application->require_auth) {
			$auth =& $this->auth;
			if (isset($auth)) {
			   $user = $auth->user();
			}
			if (isset($user)) {
//				$this->loadApplication ("accessdenied");
//				$this->application->setAskedApplication ($this->appname);
			} else {
				$this->loadApplication ("sign");
				$this->application->setAskedApplication ($this->appname);
				$this->application->setMessage ("You need to be logged in to access [".$this->appname."]");
			}
		}
		$this->application->getData();
	}
	function prepareOutput() {
		$this->application->getOutput();
	}
	function printOutput() {
		$this->application->printOutput();
	}

	Function redirectToApp($appname=NULL, $op=NULL) {
		FMWK_require_once ('common'.DIRECTORY_SEPARATOR.'redirect.inc.php');
		redirect ($this->applicationUrl($appname, $op));
	}

	/* Access */

	function username() {
		if (isset($this->auth)) {
			return $this->auth->user();
		}
	}

	/* Protected */
	function allApplicationNames() {
		FMWK_require_once ('common'.DIRECTORY_SEPARATOR.'file.inc.php');
		$appsdirs =& $this->appsPaths($real_app);
		$res = array ();
		foreach ($appsdirs as $dn) {
			$lst = listFilesFilter ($dn, "^app\..*\.php$");
			foreach ($lst as $kf => $vf) {
				$s = substr($kf, 4, strlen($kf) - 8);
				$res[$s] = $s; // Only once instance of the same appname
			}
		}
		return $res;
	}
	function applicationUrl($appname=Null, $op=Null){
		$url = $this->baseurl;
		if (!empty($appname)) {
			$url .= '?'.FMWK_PARAM_APP.'=' . $appname;
		} else {
			$url .= '?'.FMWK_PARAM_APP.'=' . $this->config->value ('site.default_application');
		}
		if (!empty($op)) {
			$url .= '&'.FMWK_PARAM_OP.'=' . $op;
		}
		return $url;
	}
	Function appsPaths($app=Null) {
		$res = array ();
		$res[] = FMWK_SITE_DIR.'apps';
		if (isset($app)) { $res[] = FMWK_SITE_DIR.'apps'.DIRECTORY_SEPARATOR.$app; }
		$res[] = FMWK_ROOT_DIR.'inc'.DIRECTORY_SEPARATOR.'apps';
		if (isset($app)) { $res[] = FMWK_ROOT_DIR.'inc'.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.$app; }
		return $res;
		}
	Function loadApplication ($app) {
		$real_app = $app;
		$classname = "SiteApp_" . $real_app;
		$appsdirs =& $this->appsPaths($real_app);
		foreach ($appsdirs as $dn) {
			$fn = $dn . DIRECTORY_SEPARATOR . "app.$real_app.php";
			if (file_exists($fn)) {
				require_once ($fn);
				break;
			}
		}
		if (class_exists ($classname)) {
			$this->application =& new $classname($real_app, $this);
		} else {
			$this->loadApplication("notfound");
			$this->application->setAskedApplication($app);
		}
	}	
}

?>
