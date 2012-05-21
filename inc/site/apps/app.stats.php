<?php

class SiteApp_stats extends ScmlogsSiteApplication {
	Function SiteApp_stats ($app, $site) {
		$this->require_auth = TRUE;
		$this->use_tpl_engine = TRUE;
		parent::ScmlogsSiteApplication($app, $site);
	}

	Function getData() {
		parent::getData();
		$param =& $this->param;

		ob_start ();
		include "inc/stats.inc";
		$param['output'] = ob_get_contents();
		ob_end_clean();  
		
	}

	Function prepareTplEngine() {
		parent::prepareTplEngine();
		$t =& $this->tpl_engine;
		$param =& $this->param;

		$t->setFile ("MainTemplate", "stats.html");
		$t->setVar ("VAR_PAGE_STATS", $param['output']);

		$t->parse("VAR_PAGE_MAIN","MainTemplate");
		
	}

}

?>
