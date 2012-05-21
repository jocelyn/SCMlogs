<?php

class SiteApp_edit extends ScmlogsSiteApplication {
	Function SiteApp_edit ($app, $site) {
		$this->require_auth = TRUE;
		$this->use_tpl_engine = TRUE;
		parent::ScmlogsSiteApplication($app, $site);
	}

	Function getData() {
		parent::getData();
		$param =& $this->param;
		include INC_DIR . "show_config_tree.inc";

		if (!isset ($optree) && isset ($_POST['optree'])) { $optree = $_POST['optree']; }
		if (!isset ($optext) && isset ($_POST['optext'])) { $optext = $_POST['optext']; }
		if (!isset ($modules) && isset ($_POST['modules'])) { $modules = $_POST['modules']; }

		$user = $this->site->username();

		$param['DIS_EditMessage'] = "";
		if (isset ($optree) or isset ($optext)) {
			if (isset ($optree) and ($optree == 'SaveTree')) {
				if (!isset ($modules)) { 
					$param['DIS_EditMessage'] .= "Error: modules tree data are not present\n"; 
			   	} else {
					#displayModules ($modules);
					saveModulesFor ($modules, $user);
				}
			} 
			if (isset ($optext) and ($optext == 'SaveText')) {
				if (!isset ($modules)) { 
					$param['DIS_EditMessage'] .= "Error: modules text data are not present\n"; 
				} else {
					saveTextModulesFor ($modules, $user);
				}
			}
			$param['DIS_EditMessage'] .= "Changes saved for user [<STRONG> $user </STRONG>] ... ";
		}

		$user_modules = userModules ($user);
		$all_modules = allModules ();

		ob_start ();
		showAllModulesWithSelectionTree ($all_modules, $user_modules);
		$param['DIS_output_user_tree'] = ob_get_contents();
		ob_end_clean();

		ob_start ();
		include INC_DIR. "show_config.inc"; 
		$param['DIS_output_modules'] = ob_get_contents();
		ob_end_clean();
	}

	Function prepareTplEngine() {
		parent::prepareTplEngine();
		$t =& $this->tpl_engine;
		$param =& $this->param;

		$t->setFile ("MainTemplate", "edit.html");

		$t->setVar ("VAR_PAGE_EDIT_MESSAGE", $param['DIS_EditMessage']);
		$t->setVar ("VAR_PAGE_EDIT_TREE", $param['DIS_output_user_tree']);
		$t->setVar ("VAR_PAGE_EDIT_MODULES", $param['DIS_output_modules']);
		
		$t->parse("VAR_PAGE_MAIN","MainTemplate");
	}

}

?>
