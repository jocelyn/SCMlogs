<?php

class SiteApp_profile extends ScmlogsSiteApplication {
	Function SiteApp_profile ($app, $site) {
		$this->require_auth = TRUE;
		$this->use_tpl_engine = TRUE;
		parent::ScmlogsSiteApplication($app, $site);
	}

	Function getData() {
		parent::getData();
		$param =& $this->param;

		include INC_DIR . "show_config_tree.inc";
		$SCMUSER = $this->site->username();

		$user_modules = userModules ($SCMUSER);
		ob_start ();
		showUserTree ($user_modules);
		$param['output_user_tree'] = ob_get_contents();
		ob_end_clean();

		ob_start ();
		displayModules ($user_modules);
		$param['output_modules'] = ob_get_contents();
		ob_end_clean();
	}

	Function prepareTplEngine() {
		parent::prepareTplEngine();
		$t =& $this->tpl_engine;
		$param =& $this->param;

		$t->setFile ("MainTemplate", "profile.html");
		$t->setVar ("VAR_PROFILE_TREE", $param['output_user_tree']);
		$t->setVar ("VAR_PROFILE_MODULES", $param['output_modules']);

		$t->parse("VAR_PAGE_MAIN","MainTemplate");
	}

}

?>
