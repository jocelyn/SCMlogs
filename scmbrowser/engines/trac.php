<?php
require_once "inc/class.misc.php";

class tracUrlEngine extends webappUrlEngine {
	Function tracUrlEngine ($baseurl, $repository_name) {
		/* No repo name support */
		parent::webappUrlEngine ($baseurl, $repository_name);
	}
	function urlTmp ($op) {
		$res = $this->baseurl;
		if (strlen($op) > 0) {
			$res = $res . $op .'/' ;
		}
		return $res;
	}
	function urlBrowser () {
		return $this->urlTmp ('browser');
	}
	function urlShowFile ($file, $dir, $r1) {
		$url = $this->urlBrowser() . $this->cleaned_path($dir).'/'.$file;
		if ($r1 >= 0) { $url .= "?rev=$r1"; }
		return $url;
	}
	function urlBlameFile ($file, $dir, $r1) {
		return $this->urlShowFile ($file, $dir, $r1);
	}
	function urlDiffFile ($file, $dir, $r1, $r2) {
		if ($r2 >= 0) {
			$url = $this->urlTmp ('');
			$url .= "anydiff?_";
			$url .= "&new_path=$dir/$file";
			$url .= "&old_path=$dir/$file";
			if ($r1 >= 0) { $url .= "&new_rev=".$r1; }
			if ($r2 >= 0) { $url .= "&old_rev=".$r2; }
		} else {
			$url = $this->urlTmp ('changeset');
			$url .= "$r1/";
			$url .= $this->cleaned_path($dir) .'/'.$file;
		}
		return $url;
	}
	function urlShowDir ($dir, $r1) {
		$url = $this->urlBrowser(). $this->cleaned_path ($dir);
		if ($r1 >= 0) { $url .= "?rev=$r1"; }
		return $url;
	}
	function urlDiffDir ($dir, $r1, $r2) {
		return $this->urlDiffFile('', $dir, $r1, $r2);
	}
	function urlRevSet ($rev) {
		$url = $this->urlTmp('changeset');
		$url .= "$rev";
		return $url;
	}
	
}

?>
