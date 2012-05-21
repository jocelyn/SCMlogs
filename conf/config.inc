<?php
	/*
	 * You should not modify anything in this file,
	 * please edit SCMconf.conf
	 */

if (! defined("_CONFIG_")) { define ("_CONFIG_", 1);
	// Php.ini settings
//	ini_set ("session.use_trans_sid", 1);
	ini_set('magic_quotes_runtime', 0);
	ini_set('magic_quotes_gpc', 0);

	// Defines
	define('ROOT_DIR', realpath (dirname(__FILE__) . DIRECTORY_SEPARATOR . '..').DIRECTORY_SEPARATOR);
	define ('INC_DIR', ROOT_DIR.'inc'.DIRECTORY_SEPARATOR);
	define ('LIB_DIR', ROOT_DIR.'lib'.DIRECTORY_SEPARATOR);
	define ('TPL_DIR', ROOT_DIR.'tpl'.DIRECTORY_SEPARATOR);

	$cfg_filename = ROOT_DIR.DIRECTORY_SEPARATOR.'conf'.DIRECTORY_SEPARATOR.'SCMlogs.conf';
	$cfg = parse_ini_file ($cfg_filename, true);

	$SCMLOGS['cfg.filename'] = $cfg_filename;
	$SCMLOGS['cfg.data'] = $cfg;

	$SCMLOGS['style.css'] = 'base.css';
	$SCMLOGS['app.url'] = $cfg['global']['SCMlogs_appurl'];
	$SCMLOGS['passwd.filename'] = $cfg['global']['SCMlogs_passwd'];

	class RepositoryProfile {
		var $id;
		var $mode;
		var $logsdir;
		var $path;

		function RepositoryProfile ($id) {
			global $SCMLOGS;
			$this->id = trim($id);

			$cfgsection = $SCMLOGS['cfg.data']['repo:'.$this->id];
			$this->mode = $cfgsection['mode'];
			$this->logsdir = $cfgsection['logs_dir'];
			$this->path = $cfgsection['repository_path'];
		}

		function svnfile_root() {
			$res = "file://" . $this->path;
			return str_replace ("\\", "/", $res);
		}
	}

	if (isset ($cfg['global']['SCM_repositories'])) {
		$repo_list_id = split (',' , $cfg['global']['SCM_repositories']);
		$repositories = array ();
		foreach ($repo_list_id as $krid => $rid) {
			$repositories[trim($rid)] =& new RepositoryProfile ($rid);
		}
		$repo_id = $repo_list_id[1];
	}
	if (isset ($cfg['global']['SCM_default_repository'])) {
		$repo_id = $cfg['global']['SCM_default_repository'];
	}
	$SCMLOGS['repositories'] =& $repositories;


	//// Dir to store users profiles
	$SCMLOGS['tmpdir'] = $SCMLOGS['datadir'].'tmp';
	$SCMLOGS['currentcommitfile'] = 'commits.txt';
	$SCMLOGS['appdir'] = $cfg['global']['SCMlogs_appdir'];
	$SCMLOGS['datadir'] = $SCMLOGS['appdir'].'data'.DIRECTORY_SEPARATOR;
	if (isset ($cfg['global']['svn_bin_path'])) {
		$SCMLOGS['svn_bin_path'] = $cfg['global']['svn_bin_path'] . DIRECTORY_SEPARATOR;
	} else {
		$SCMLOGS['svn_bin_path'] = '' ;
	}
	if (isset ($cfg['global']['python_exec_path'])) {
		$SCMLOGS['python_exec'] = $cfg['global']['python_exec_path'];
	} else {
		$SCMLOGS['python_exec'] = '' ;
	}

	/// Misc
	if (isset($cfg['users']['cfg_extension'])) {
		$SCMLOGS['user.cfg.ext'] = $cfg['users']['cfg_extension'];
	} else {
		$SCMLOGS['user.cfg.ext'] = '.cfg';
	}
	if (isset($cfg['users']['pref_extension'])) {
		$SCMLOGS['user.pref.ext'] = $cfg['users']['pref_extension'];
	} else {
		$SCMLOGS['user.pref.ext'] = '.pref';
	}

	//// Integration to WebBrowsing
	$SCMLOGS['webBrowsingUrl'] = $SCMLOGS['app.url'].'webscmlogs.php';
	if (isset ($cfg['global']['browsing'])) {
		$SCMLOGS['webBrowsingApp'] = $cfg['global']['browsing'];
	} else {
		$SCMLOGS['webBrowsingApp'] = $cfg['global']['browsing'];
	}


	function &SCMLogs_repository () {
		global $SCMLOGS;
		return $SCMLOGS['repository'];
	}
	function SCMLogs_repository_id () {
		$repo =& SCMLogs_repository();
		return $repo->id;
	}
	function SCMLogs_set_repository_by_id ($repo_id) {
		global $SCMLOGS;
		$SCMLOGS['repository'] = $SCMLOGS['repositories'][$repo_id];
	}

	function SCMLogs_sendlogs_cmd ($repo_id=NULL) {
		global $SCMLOGS;
		$cmd = $SCMLOGS['python_exec'] . ' ' . $SCMLOGS['appdir'].'bin/scm_sendlogs.py ';
		$cmd .= ' -cfg '.$SCMLOGS['cfg.filename'];
		if (isset ($repo_id)) {
			$cmd .= ' -repo ' . $repo_id;
		} else {
			$cmd .= ' -repo ' . SCMLogs_repository_id();
		}
		return $cmd;
	}
	function SCMLogs_CurrentCommitFile () {
		global $SCMLOGS;
		return $SCMLOGS['currentcommitfile'];
	}
	function SCMLogs_WebBrowsing () {
		global $SCMLOGS;
		$url = $SCMLOGS['webBrowsingUrl'];
		$url .= "?repname=".SCMLogs_repository_id();
		if (isset ($SCMLOGS['webBrowsingApp'])) {
			$url .= "&webapp=".$SCMLOGS['webBrowsingApp'];
		}
		return $url;
	}

	SCMLogs_set_repository_by_id ($repo_id);
} 

?>
