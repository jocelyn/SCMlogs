<?php

class SiteApp_identify extends ScmlogsSiteApplication {
	Function SiteApp_identify ($app, $site) {
		$this->use_tpl_engine = TRUE;
		parent::ScmlogsSiteApplication($app, $site);
	}

	Function checkACL() {
		$this->require_auth = TRUE;
	}

	Function getData() {
		parent::getData();
		$param =& $this->param;
		$param['is_signed'] = $this->is_signed();
	}

	Function prepareTplEngine() {
		parent::prepareTplEngine();
		$t =& $this->tpl_engine;
		$p =& $this->param;

		if ($p['is_signed']) {
			$t->setFile ("MainTemplate", "Welcome.html");
		} else {
			$t->setFile ("MainTemplate", "login-box.html");
		}

		$t->parse("VAR_PAGE_MAIN","MainTemplate");
//		$t->setVar("VAR_PAGE_MAIN","Tutu");
	}

//	Function printHeader() {
//	}
//	Function printContent() {
//		$p =& $this->param;
//		if ($p['is_signed']) {
//			echo "<h1>Your informations</h1>";
//		} else {
//			echo "<h1>Please sign in</h1>";
//<?php
//		}
//	}
//	Function printFooter() {
//	}

}

?>
