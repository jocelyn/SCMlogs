<?php

require_once "inc/class.misc.php";

class websvnUrlEngine extends webappUrlEngine {
	Function websvnUrlEngine ($baseurl, $repository_name) {
		parent::webappUrlEngine ($baseurl, $repository_name);
	}
	function urlTmp ($op) {
		return $this->baseurl . $op.".php?repname=" . $this->reponame;
	}
	function urlBrowser () {
		return $this->urlTmp ('listing');
	}
	function urlShowFile ($file, $dir, $r1) {
		$url = $this->urlTmp ('filedetails');
		if ($r1 >= 0) { $url .= "&rev=$r1"; }
		$url .= "&sc=1";
		$url .= "&path=/" . $this->cleaned_path($dir) ."/$file";
		return $url;
	}
	function urlBlameFile ($file, $dir, $r1) {
		$url = $this->urlTmp ('blame');
		if ($r1 >= 0) { $url .= "&rev=$r1"; }
		$url .= "&sc=1";
		$url .= "&path=/".$this->cleaned_path($dir)."/$file";
		return $url;
	}
	function urlDiffFile ($file, $dir, $r1, $r2) {
		$url = $this->urlTmp ('diff');
		if ($r1 >= 0) { $url .= "&rev=$r1"; }
		$url .= "&sc=1";
		$url .= "&path=/".$this->cleaned_path($dir)."/$file";
		return $url;
	}
	function urlShowDir ($dir, $r1) {
		$url = $this->urlTmp ('listing');
		if ($r1 >= 0) { $url .= "&rev=$r1"; }
		$url .= "&sc=1";
		$url .= "&path=/".$this->cleaned_path($dir);
		return $url;
	}
	function urlDiffDir ($dir, $r1, $r2) {
		$url = $this->urlTmp ('comp');
		$url .= "&compare[]=/".$this->cleaned_path($dir)."@$r1&compare[]=/".$this->cleaned_path($dir)."@$r2";
		return $url;
	}
	function urlRevSet ($rev) {
		$url = $this->urlTmp ('listing');
		$url .= "&rev=$rev";
		$url .= "&path=/&sc=1";
		return $url;
	}
	
}

?>
