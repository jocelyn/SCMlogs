<?php

class webappUrlEngine {
	Function webappUrlEngine ($baseurl, $reponame) {
		$this->baseurl = $baseurl;
		$this->reponame = $reponame;
	}
	function urlBrowser () { return $this->baseurl; }
	function urlShowFile ($file, $dir, $r1) { return ''; }
	function urlBlameFile ($file, $dir, $r1) { return ""; }
	function urlDiffFile ($file, $dir, $r1, $r2) { return ""; }
	function urlShowDir ($dir, $r1) { return ""; }
	function urlDiffDir ($dir, $r1, $r2) { return ""; }
	function cleaned_path ($path) {
		$res = $path;
		while (substr($res, 0,1) === "/") {
			$res = substr ($res, 1);
		}
		return $res;
	}
}

?>
