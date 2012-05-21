<?php

class SiteApp_index extends ScmlogsSiteApplication {
	Function SiteApp_index ($app, $site) {
		$this->use_tpl_engine = TRUE;
		parent::ScmlogsSiteApplication($app, $site);
	}

	Function getData() {
		global $subapp;
		parent::getData();
		$param =& $this->param;
		$param['is_signed'] = $this->is_signed();
		$param['user'] = $this->site->username();

//		if ($this->is_signed()) {
//			if ($is_registring) {
//				createUserFile ($param['SCMUSER']);
//				$_SESSION = $param['SCMUSER'];
//			} elseif ($is_exiting) {
//				$_SESSION['user'] = Null;
//				unset ($_SESSION['user']);
//			}
//			$param['DIS_Application'] = "login";
//		} else {
//			$param['DIS_Application'] = "login";
//		}
	}

	Function prepareTplEngine() {
		global $subapp;
		parent::prepareTplEngine();
		$t =& $this->tpl_engine;
		$param =& $this->param;

		switch ($subapp) {
			case "doc":
				$t->setFile ("MainTemplate", "doc.html");
				break;
			case "auth":
			default:
				if ($param['is_signed']) {
					$t->setFile ("MainTemplate", "welcome.html");
				} else {
					$t->setFile ("MainTemplate", "welcome-anonymous.html");
				}
				break;
		}
		$t->parse("VAR_PAGE_MAIN","MainTemplate");
	}

}

?>
