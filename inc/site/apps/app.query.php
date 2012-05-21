<?php

class SiteApp_query extends ScmlogsSiteApplication {
	Function SiteApp_query ($app, $site) {
		$this->use_tpl_engine = TRUE;
		parent::ScmlogsSiteApplication($app, $site);
	}

	Function getData() {
		parent::getData();
		$param =& $this->param;
		if ($this->is_signed()) {
			$SCMUSER = $this->site->username();
		}

		include INC_DIR."query.inc";

		@$commitsfiles = $_POST['commitsfiles'];
		if (!isset ($commitsfiles)) { $commitsfiles = array (); }
		@$only_user = $_POST['only_user'];
		@$only_tag = $_POST['only_tag'];

		if (isset ($_POST['selected_years'])) {
			$selected_years = $_POST['selected_years'];
			if (!is_array ($selected_years)) {
				$selected_years = split (':', $selected_years);
			}
		}
		if (isset ($_GET['selected_years'])) {
			$selected_years = split (':', $_GET['selected_years']);
		}
		if (!isset ($selected_years)) { $selected_years = array (strftime ("%Y")); }

		$param['DIS_GET_SelectedYear'] = "";
		while (list ($k, $y) = each ($selected_years)) {
			if (strlen ($y) > 0) {
				$param['DIS_GET_SelectedYear'] .= "$y:";
			}
		}
		$param['selected_years'] =& $selected_years;

		$param['DIS_Application'] = "query";

		// Commit files
		$param['DIS_CurrentCommitFile'] = SCMLogs_CurrentCommitFile();
		$param['DIS_CurrentCommitFileChecked'] = (in_array ($param['DIS_CurrentCommitFile'], $commitsfiles));
		$param['DIS_OnlyUser'] = $only_user;
		$param['DIS_OnlyTag'] = $only_tag;

		$param['DIS_UserFilter'] = userModulesText ($SCMUSER);
		
		$param['DIS_CommitFiles'] = array (); // [years => [month => [key=>files]]]
		$repo = SCMLogs_repository();
		$list_years = listOfDir ($repo->logsdir);
		reset ($list_years);
		krsort ($list_years);
		while (list ($k_year, $v_year_dir) = each ($list_years)) {
			if (eregi("^[0-9]+$", $k_year)) {
				$TMP_months = array ();
				$list_months = listOfDir ($v_year_dir);
				arsort ($list_months);
				while (list ($k_month, $v_month_dir) = each ($list_months)) {
					$list_files = listOfFiles ($v_month_dir);
					if (count ($list_files) > 0) {
						$TMP_files = array ();
						reset ($list_files);
						arsort ($list_files);
						while (list ($k_file, $v_filename) = each ($list_files)) {
							if (in_array ($k_file, $commitsfiles)) {
								$TMP_files[$k_file] = True;
							} else {
								$TMP_files[$k_file] = False;
							}
						}
						$TMP_months[$k_month] = $TMP_files;
					}
				}
				$param['DIS_CommitFiles'][$k_year] = $TMP_months;
			}
		}
		
	}

	Function prepareTplEngine() {
		parent::prepareTplEngine();
		$t =& $this->tpl_engine;
		$param =& $this->param;

		$t->setFile ("MainTemplate", "query.html");
		$t->setFile ("FilesTemplate", "query-files.html");
		$t->setFile ("FilesSelectedTemplate", "query-files-selected.html");
		$t->setFile ("FilesBrowseTemplate", "query-files-browse.html");
		$t->setFile ("CommandsTemplate", "query-commands.html");

		// Commit Files ////////////////////////////////////////////////
//		$t->setBlock ("FilesSelectedTemplate", "ListYearsBlock", "LY_B");
		$t->setBlock ("FilesSelectedTemplate", "ListMonthsBlock", "LM_B");
		$t->setBlock ("ListMonthsBlock", "ListFilesBlock", "LF_B");

		$t->setVar ("VAR_USER_FILTER", $param['DIS_UserFilter']);
		$t->setVar ("VAR_CURRENT_COMMITS_FILENAME", $param['DIS_CurrentCommitFile']);
		if ($param['DIS_CurrentCommitFileChecked']) {
			$t->setVar ("VAR_CURRENT_FILE_CHECKED", "CHECKED");
		} else {
			$t->setVar ("VAR_CURRENT_FILE_CHECKED", "");
		}

		$t->setVar ("VAR_SELECTED_YEARS_GET", $param['DIS_GET_SelectedYear']);
		while (list ($k_year, $v_months) = each ($param['DIS_CommitFiles'])) {
			$t->setVar ("VAR_ITEM_YEAR", "$k_year");
			if (in_array ($k_year, $param['selected_years'])) {
				while (list ($k_month, $v_files) = each ($v_months)) {
					$t->setVar ("VAR_ITEM_MONTH", "$k_month");
					while (list ($k_file, $v_checked) = each ($v_files)) {
						if ($v_checked) {
							$t->setVar ("VAR_FILE_CHECKED", "CHECKED");
						} else {
							$t->setVar ("VAR_FILE_CHECKED", "");
						}
						list ($iy, $im, $id) = split ("-", "$k_file");
						$k_day = $im."/".$id."/".$iy;
						$k_day = strtotime ($k_day);
						$k_week = strftime ("%W", $k_day);
						$k_day = date ("D", $k_day);
						$t->setVar ("VAR_FILE_ITEM_WEEK", $k_week);
						$t->setVar ("VAR_FILE_ITEM_DAY", $k_day);
						$t->setVar ("VAR_FILE_KEY_ITEM", "$k_file");
						$t->setVar ("VAR_FILE_NAME_ITEM", "$k_file");
						$t->parse ("LF_B", "ListFilesBlock", True);
					}
					$t->parse ("LM_B", "ListMonthsBlock", True);
					$t->unsetVar ("LF_B");
				}
				$t->parse ("VAR_PAGE_YEAR", "FilesSelectedTemplate", True);
				$t->unsetVar ("LM_B");
			} else {
				$t->parse ("VAR_PAGE_YEAR", "FilesBrowseTemplate", True);
			}
		}
		$t->parse ("VAR_PAGE_COMMITS_FILES", "FilesTemplate");

		// Commands ////////////////////////////////////////////////////
		$t->setVar ("VAR_ONLY_USER", $param['DIS_OnlyUser']);
		$t->setVar ("VAR_ONLY_TAG", $param['DIS_OnlyTag']);
		$t->parse ("VAR_PAGE_COMMANDS", "CommandsTemplate");

		$t->parse("VAR_PAGE_MAIN","MainTemplate");
	}

}

?>
