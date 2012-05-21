<?php

class ScmlogsSiteApplication extends SiteApplication {
	var $tpl_engine;
	var $use_tpl_engine=TRUE;

	Function ScmlogsSiteApplication ($app, $site) {
		parent::SiteApplication($app, $site);
	}
	Function is_signed() {
		$un = $this->site->username();
		return isset($un);
	}
	Function is_authentificated() {
		$un = $this->site->username();
		return isset($un);
	}

	Function getData() {
		global $SCMUSER;
		global $SCMLOGS;
		parent::getData();
		if ($this->use_tpl_engine) {
			$this->initTplEngine();
		}

		if (isset($_GET['repo'])) {
			$asked_repo = $_GET['repo'];
		} elseif (isset ($_SESSION['repo'])) {
			$asked_repo = $_SESSION['repo'];
		}
		if (isset ($asked_repo)) {
			if ($asked_repo == SCMLogs_repository_id()) {
				$_SESSION['repo'] = Null;
				unset ($_SESSION['repo']);
			} else {
				SCMLogs_set_repository_by_id ($asked_repo);
				$_SESSION['repo'] = SCMLogs_repository_id();
			}
		}


		$param =& $this->param;
		if ($this->is_signed()) {
			$param['is_signed'] = TRUE;
			$SCMUSER = $this->site->username();
			$param['DIS_User'] = $SCMUSER;
			$param['DIS_Title'] = " :: Welcome $SCMUSER";
		} else {
			$param['is_signed'] = FALSE;
			$param['DIS_Title'] = " :: Authentification";
			$SCMUSER = NULL;
		}

		include INC_DIR ."users.inc";
//		if (!isset($SCMUSER) && isset ($_GET['user'])) { $SCMUSER = $_GET['user']; }
//		if (!isset($SCMUSER) && isset ($_POST['user'])) { $SCMUSER = $_POST['user']; }
//		if (!isset($SCMUSER) && isset ($_SESSION['user'])) { $SCMUSER = $_SESSION['user']; }

		/// Fill Data
		$repo =& SCMLogs_repository();
		$param['DIS_REPOSITORY'] = $repo->path;
		$param['DIS_REPO_BROWSING'] = SCMLogs_WebBrowsing();

		$html = '';
		if (isset ($SCMLOGS['repositories'])) {
			$repos = $SCMLOGS['repositories'];
			if (count($repos) > 1) {
				$html .= '<form action="'.$this_url.'" method="GET"><input type="submit" value="&gt;&gt;" />';
				$html .= '<select name="repo">';
				foreach ($repos as $k_repoid => $v_repo) {
					$html .= '<option value="'.$v_repo->id.'" ';
					if ($v_repo->id == SCMLogs_repository_id()) {
						$html .= ' SELECTED ';
					}
					$html .= '>'.$v_repo->id.'</option>';
				}
				$html .= '</select></form>';
			} else {
				$html .= "Repository " . SCMLogs_repository_id();
			}
		}		
		$param['DIS_VAR_PAGE_REPO_LIST'] = $html;
	}

	Function prepareTplEngine() {
		$t =& $this->tpl_engine;
		$param =& $this->param;
		$t->setFile("CommonTemplate", "common.html");	
		if ($param['is_signed']) {
			$t->setFile("MenuTemplate", "menu-user.html");
			$t->setVar("VAR_USER", $param['DIS_User']);
		} else {
			$t->setFile("MenuTemplate","menu-login.html");
		}

		$t->setVar("VAR_TITLE", $param['DIS_Title']);
		$t->setVar("VAR_REPOSITORY", $param['DIS_REPOSITORY']);
		$t->setVar("VAR_REPO_BROWSING", $param['DIS_REPO_BROWSING']);
		$t->setVar("VAR_PAGE_REPO_LIST", $param['DIS_VAR_PAGE_REPO_LIST']);

		$t->parse("VAR_PAGE_MENU","MenuTemplate");
	}

	Function initTplEngine() {
		include LIB_DIR."template.inc";
		$this->tpl_engine =& new Template (TPL_DIR);
	}

	Function captureOutput($using_tpl=FALSE) {
		$o = $this->use_tpl_engine;
		$this->use_tpl_engine = FALSE;
		$this->getOutput();
		$res = $this->output;
		$this->output = Null;
		$this->use_tpl_engine = $o;
		return $res;
	}

	Function printOutput() {
		if (empty ($this->output)) {
			if ($this->use_tpl_engine) {
				$this->prepareTplEngine();
				$this->tpl_engine->pparse("Output","CommonTemplate");
			} else {
				parent::printOutput();
			}
		}
	}

}

?>
