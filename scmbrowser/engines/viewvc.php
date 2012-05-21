<?php
require_once "inc/class.misc.php";


class viewvcUrlEngine extends webappUrlEngine {
	Function viewvcUrlEngine ($baseurl, $repository_name) {
		parent::webappUrlEngine ($baseurl, $repository_name);
	}
	function urlTmp ($dir, $file='') {
		$url = $this->baseurl . $this->cleaned_path($dir)."/$file";
		if (!empty($this->reponame)) {
			$url .= "?root=" . $this->reponame;
		} else {
			$url .= "?noroot";
		}
		return $url;
	}
	function urlBrowser () {
		return $this->urlTmp ('');
	}
	function urlShowFile ($file, $dir, $r1) {
		$url = $this->urlTmp ($dir, $file);
		if ($r1 >= 0) { $url .= "&rev=$r1"; }
		$url .= "&content-type=text/vnd.viewcvs-markup";
		return $url;
	}
	function urlBlameFile ($file, $dir, $r1) {
		$url = $this->urlShowFile ($file, $dir, $r1);
		$url .= "&view=annotate";
		return $url;
	}
	function urlDiffFile ($file, $dir, $r1, $r2) {
		if (count($file) > 0) {
			if ($r1 == 'NONE') {
				$url = $this->urlShowFile ($file, $dir, $r2);
			} elseif ($r2 == 'NONE') {
				$url = $this->urlShowFile ($file, $dir, $r1);
			} elseif ($r1 != 0 and $r2 != 0) {
				$url = $this->urlTmp($dir, $file);
				$url .= "&r1=$r1&r2=$r2";
			}
		} else {
			$url = $this->urlShowDir ($dir, $r1);
		}
		return $url;
	}
	function urlShowDir ($dir, $r1) {
		$url = $this->urlTmp ($dir);
		if ($r1 >= 0) { $url .= "&rev=$r1"; }
		return $url;
	}
	function urlDiffDir ($dir, $r1, $r2) {
		return $this->urlShowDir ($dir, $r1);
	}
	function urlRevSet ($rev) {
		$url = $this->urlTmp ('');
		$url .= "&view=rev";
		$url .= "&revision=$rev";
		return $url;
	}
}

?>
