<?php

require "conf/config.inc";
include "lib/file.inc";
include "inc/query.inc";


class SiteApp_show extends ScmlogsSiteApplication {
	Function SiteApp_show ($app, $site) {
		$this->use_tpl_engine = TRUE;
		parent::ScmlogsSiteApplication($app, $site);
	}

	Function getData() {
		parent::getData();
		$param =& $this->param;
		$user = $this->site->username();
		if (empty($user)) { $user = "none"; }
		$error = false;

	//	echo "<pre>"; print_r ($_POST); echo "</pre>";
		if (isset($_GET['key'])) {
			$data_src = 'key';
			$data_key = $_GET['key'];
		} else {
			$data_src = $_POST['datasrc'];	
		}
		$data_source_is_dates = ($data_src == "dates");
		if ($data_source_is_dates) {
			$start_date = $_POST['startdate'];
			$end_date = $_POST['enddate'];
			$error = !isValidDate($start_date) or (
					!empty($end_date) and !isValidDate($end_date)
				);
		} elseif ($data_src == 'key') {
			$commitsfiles = array ($data_key); 
		} else {
			@$commitsmonthfiles = $_POST['commitsmonthfiles'];
			if (isset ($commitsmonthfiles)) { 
				$commitsfiles = array (); 
				while (list ($y, $v_yfiles) = each ($commitsmonthfiles)) {
					while (list ($m, $v_mfiles) = each ($v_yfiles)) {
						while (list ($d, $v_file) = each ($v_mfiles)) {
							$commitsfiles[] = $v_file;
						}
					}
				}
			}
			$error = count($commitsfiles) == 0;
		}
	//	@$commitsfiles = $_POST['commitsfiles'];

		$param['DIS_GET_SelectedYear'] = "";
		if (isset ($_POST['selected_years'])) {
			$selected_years = $_POST['selected_years'];
			while (list ($k, $y) = each ($selected_years)) {
				$param['DIS_GET_SelectedYear'] .= "$y:";
			}
		}

		if (!isset ($commitsfiles)) { $commitsfiles = array (); }
		// if (isset ($_GET['key'])) { $commitsfiles[] = $_GET['key']; };

		$param['DIS_Application'] = "show";

		$param['DIS_Command'] = "cmd";
		$param['DIS_Result'] = "Result";

		if (!$error) {
			@$operation = $_POST['show'];
			if (!isset ($operation)) { $operation = 'ShowLogs'; }

			$param['DIS_Parameters'] = "Login used = $user <BR>";

			@$filter = $_POST['filter'];
			if (!isset ($filter) or (strlen ($filter) == 0)) { 
				$filter = 'profil'; 
			} else {
				$param['DIS_Parameters'] .= "Filter used = $filter <BR>";
				if ($filter == 'text') {
					@$filter_text = $_POST['textfilters'];
					$filter_text = cleanedTextModule ($filter_text);

					$filter_file_tempo_name = tempnam ($SCMLOGS['tmpdir'], "FILTER_TEMPO_");
					$filter_file_tempo = fopen ($filter_file_tempo_name, "w");
					fwrite ($filter_file_tempo, $filter_text);
					fclose ($filter_file_tempo);

					$param['DIS_Parameters'] .= "Filter text = $filter_text <BR>";
				}
			}
			@$format = $_POST['format'];
			if (!isset ($format) or (strlen ($format) == 0)) { 
				$format = 'html'; 
			} else {
				$param['DIS_Parameters'] .= "Formating used = $format <BR>";
			}
			@$type = $_POST['type'];
			if (!isset ($type) or (strlen ($type) == 0)) { 
				$type = 'filtered'; 
			} else {
				$param['DIS_Parameters'] .= "Output type used = $type <BR>";
			}
			@$only_user = $_POST['only_user'];
			if (!isset ($only_user) or (strlen ($only_user) == 0)) { 
				$only_user = ''; 
			} else {
				$param['DIS_Parameters'] .= "Only commits from user = $only_user <BR>";
			}
			@$only_tag = $_POST['only_tag'];
			if (!isset ($only_tag) or (strlen ($only_tag) == 0)) { 
				$only_tag = ''; 
			} else {
				$param['DIS_Parameters'] .= "Only commits about TAG = $only_tag <BR>";
			}

			$is_mail_operation = FALSE;
			switch ($operation) {
				case 'EmailLogs':
					$is_mail_operation = TRUE;
					$param['DIS_Message'] = "Email $user all the logs <BR>(in the selected files)<BR>\n";
					$processing_fct = "EmailLogsAction";
					break;
				case 'ShowRawLogs':
					$param['DIS_Message'] = "Show the RAW logs file (selected files)<BR>\n";
					$processing_fct = "ShowRawLogsAction";
					break;
				case 'EmailMyLogs':
					$is_mail_operation = TRUE;
					$only_user = $user;
					$param['DIS_Message'] = "Email $user all the logs (in the selected files) \n";
					$param['DIS_Message'] .= " from <STRONG>$user</STRONG><BR>";
					$processing_fct = "EmailMyLogsAction";
					break;
				case 'ShowMyLogs':
					$only_user = $user;
					$param['DIS_Message'] = "Show $user all the logs (in the selected files) \n";
					$param['DIS_Message'] .= " from <STRONG>$user</STRONG><BR>\n";
					$processing_fct = "ShowMyLogsAction";
					break;
				case 'EmailOnlyLogsFor':
					$is_mail_operation = TRUE;
					$param['DIS_Message'] = "Email $user all the logs (in the selected files)\n";
					$param['DIS_Message'] .= " from user : <STRONG>$only_user</STRONG>\n";
					$param['DIS_Message'] .= " with tag  : <STRONG>$only_tag</STRONG><BR>\n";
					$processing_fct = "EmailOnlyLogsForAction";
					break;
				case 'ShowOnlyLogsFor':
					$param['DIS_Message'] = "Show $user all the logs (in the selected files)\n";
					$param['DIS_Message'] .= " from user : <STRONG>$only_user</STRONG>\n";
					$param['DIS_Message'] .= " with tag&nbsp;  : <STRONG>$only_tag</STRONG><BR>\n";
					$processing_fct = "ShowOnlyLogsForAction";
					break;
				case 'ShowLogs':
				default:
					$param['DIS_Message'] = "Show $user all the logs (in the selected files)<BR>\n";
					$processing_fct = "ShowLogsAction";
					break;
			}

			if ($is_mail_operation and $user == 'none') {
				$error = TRUE;
				$param['DIS_Message'] = "Operation not allowed";
				$param['DIS_Result'] = "Email operation is only for authentified users.";
			}
		}
		if (!$error) {
			$file_tempo_name = tempnam ($SCMLOGS['tmpdir'], "TEMPO_");
			$file_tempo = fopen ($file_tempo_name, "w");
			$param['DIS_Data'] ="";
			$repo = SCMLogs_repository();
			if ($data_source_is_dates) {
				$datesforsvn = "{" . $start_date . "}";
				$param['DIS_Data'] .= "from " . $start_date . " ";
				if (empty($end_date)) {
					$datesforsvn .= ":HEAD";
					$param['DIS_Data'] .= " to HEAD";
				} elseif (isValidDate($end_date)) {
					$datesforsvn .= ":{" . $end_date . "}";
					$param['DIS_Data'] .= " to " . $end_date . " ";
				} else {
					$datesforsvn .= ":HEAD";
					$param['DIS_Data'] .= " to HEAD";
				}
				$datesforsvn = str_replace ("/", "-", $datesforsvn);
				$ccmd = $SCMLOGS['svn_bin_path'] . 'svn log --config-dir . -v -r "' . $datesforsvn . '" ' . $repo->svnfile_root();
				ob_start();
				$res = system ($ccmd);
				$logs = ob_get_contents();
				fwrite ($file_tempo, $logs);
				ob_end_clean();
			} else {
				$logsdir = $repo->logsdir;
				while (list ($k, $v_file) = each ($commitsfiles)) {
					$param['DIS_Data'] .= "<li>$v_file";
					if (preg_match("/^([0-9][0-9][0-9][0-9])-([0-9][0-9])-([0-9][0-9])$/", $v_file, $matches)) {
						$v_file = $logsdir .'/'.$matches[1].'/'.$matches[2].'/'. $v_file;
					}
					if (preg_match("/^(".SCMLogs_CurrentCommitFile().")$/", $v_file, $matches)) {
						$v_file = $logsdir .'/'.$v_file;
					}

					//$param['DIS_Data'] .= " :: <em>$v_file</em>";
					$param['DIS_Data'] .= "</li>\n";
					fwrite ($file_tempo, ContentOfFile ($v_file));
				}
			}
			fclose ($file_tempo);
			if ($processing_fct != '') {
				ob_start ();
				$param['DIS_Format'] = $format;
				$param['DIS_Type'] = $type;

				if ($filter == 'text') {
					$param_filter = $filter_file_tempo_name;
				} else {
					$param_filter = $filter;
				}
				set_time_limit(150);
				$param['DIS_Command'] = $processing_fct ($file_tempo_name, $user, $param_filter, $only_user, $only_tag, $format, $type);
				$param['DIS_Result'] = ob_get_contents();
				ob_end_clean();
			} else {
				$param['DIS_Format'] = '';
				$param['DIS_Command'] = "Not Yet Implemented";
			}
			if (isset ($filter_file_tempo_name)) {
				RemoveFile ($filter_file_tempo_name);
			}
			RemoveFile ($file_tempo_name);
		} else {
			$param['DIS_Format'] = '';
			$param['DIS_Parameters'] = "...";
			$param['DIS_Command'] = "...";
			if (empty($param['DIS_Message'])) {
				$param['DIS_Message'] = "Please select at least one file or valid dates!!!";
			}
			if (empty($param['DIS_Data'])) {
				$param['DIS_Data'] = "no file or valid dates selected";
			}
			if (empty($param['DIS_Result'])) {
				$param['DIS_Result'] = "...";
			}
		}
		$param['only_user'] =& $only_user;
		$param['only_tag'] =& $only_tag;
		$param['commitsfiles'] =& $commitsfiles;
	}

	Function prepareTplEngine() {
		parent::prepareTplEngine();
		$t =& $this->tpl_engine;
		$param =& $this->param;
		$only_user =& $param['only_user'];
		$only_tag =& $param['only_tag'];
		$commitsfiles =& $param['commitsfiles'];

		$t->setFile ("MainTemplate", "show.html");

		$t->setVar ("VAR_PAGE_SHOW_MESSAGE", $param['DIS_Message']);
		$t->setVar ("VAR_PAGE_SHOW_PARAM", $param['DIS_Parameters']);
		$t->setVar ("VAR_SELECTED_YEARS_GET", $param['DIS_GET_SelectedYear']);
		$t->setVar ("VAR_PAGE_SHOW_DATA", $param['DIS_Data']);
		$t->setVar ("VAR_PAGE_SHOW_COMMAND", $param['DIS_Command']);
		if ($param['DIS_Format'] == 'text') {
			$t->setVar ("VAR_PAGE_SHOW_RESULT", "<pre>".$param['DIS_Result']."</pre>");
		} else {
			$t->setVar ("VAR_PAGE_SHOW_RESULT", $param['DIS_Result']);
		}

		$t->setVar ("VAR_FORMAT", $param['DIS_Format']);
		$t->setVar ("VAR_TYPE", $param['DIS_Type']);

		if (isset ($only_user)) {
			$t->setVar ("VAR_ONLY_USER", $only_user);
		} else {
			$t->setVar ("VAR_ONLY_USER", '');
		}
		if (isset ($only_tag)) {
			$t->setVar ("VAR_ONLY_TAG", $only_tag);
		} else {
			$t->setVar ("VAR_ONLY_TAG", '');
		}

		$t->setBlock ("MainTemplate", "HiddenCommitFilesBlock", "HCF_B");
		reset ($commitsfiles);
		while (list ($k, $v_key) = each ($commitsfiles)) {
			$t->setVar ("VAR_ITEM_FILE_KEY", $v_key);
			$t->parse ("HCF_B", "HiddenCommitFilesBlock", True);
		}

		$t->parse("VAR_PAGE_MAIN","MainTemplate");
	}

}

function isValidDate($s){
	$year = (int)substr($s, 0,4);
	$month = (int)substr($s, 5,7);
	$day = (int)substr($s, 8,10);
	return checkdate($month, $day, $year);
}

?>
