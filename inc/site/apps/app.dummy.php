<?php

class SiteApp_index extends ScmlogsSiteApplication {
	Function SiteApp_index ($app, $site) {
		$this->use_tpl_engine = FALSE;
		parent::ScmlogsSiteApplication($app, $site);
	}

	Function getData() {
		parent::getData();
		$param =& $this->param;
	}

	Function prepareTplEngine() {
		parent::prepareTplEngine();
		$t =& $this->tpl_engine;
		$param =& $this->param;
	}

}

?>
