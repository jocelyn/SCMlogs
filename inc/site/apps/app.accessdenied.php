<?php

class SiteApp_accessdenied extends ScmlogsSiteApplication {
	Function SiteApp_accessdenied ($app, $site) {
		$this->use_tpl_engine = TRUE;
		parent::ScmlogsSiteApplication($app, $site);
	}

	var $asked_app;
	function setAskedApplication ($appname) {
		$this->asked_app = $appname;
	}		

	Function getData() {
		parent::getData();
		$param =& $this->param;
	}

	Function prepareTplEngine() {
		parent::prepareTplEngine();
		$t =& $this->tpl_engine;
		$param =& $this->param;

		$t->setVar("VAR_PAGE_MAIN","<div style=\"padding: 20px; font-weight: bold; color: #c00;\">Access denied to [".$this->asked_app."]</div>");
	}	
}

?>
