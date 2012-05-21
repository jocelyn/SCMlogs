<?php
	// Classes
//

Function url_for_operation_on_browser ($op, $appUrlEngine){
	switch ($op) {
		case 'fileshow':
		case 'fileblame':
			$path = $_GET['path'];
			$dir = dirname ($path);
			$file = basename ($path);
			$rev = $_GET['rev'];
			if ($op == 'fileblame') {
				$url = $appUrlEngine->urlBlameFile($file, $dir, $rev);
			} else {
				$url = $appUrlEngine->urlShowFile($file, $dir, $rev);
			}
			break;
		case 'filediff':
			$path = $_GET['path'];
			$dir = dirname ($path);
			$file = basename ($path);
			$r1 = $_GET['r1'];
			$r2 = $_GET['r2'];
			$url = $appUrlEngine->urlDiffFile($file, $dir, $r1, $r2);
			break;
		case 'dirshow':
			$dir = $_GET['path'];
			$rev = $_GET['rev'];
			$url = $appUrlEngine->urlShowDir($dir, $rev);
			break;
		case 'dirdiff':
			$dir = $_GET['path'];
			$r1 = $_GET['r1'];
			$r2 = $_GET['r2'];
			$url = $appUrlEngine->urlDiffDir($dir, $r1, $r2);
			break;
		case 'revset':
			$rev = $_GET['rev'];
			$url = $appUrlEngine->urlRevSet($rev);
			break;
		default:
			$url = $appUrlEngine->urlBrowser();
			break;
		}
	return $url;
}

Function url_for_browser ($appUrlEngine){
	//echo "<PRE>"; print_r ($_SERVER); echo "</PRE>";
	return $_SERVER['REQUEST_URI'] . "&webapp=" . $appUrlEngine . "&remindwebapp=1";
}

///////////////////////////////////////////////////
// Config /////////////////////////////////////////
///////////////////////////////////////////////////

require_once "inc/config.php";
	global $scmlogs_conf;
	
	$delay = $scmlogs_conf['delay.short'];
	$cookie_id = $scmlogs_conf['cookie'];

	if (!isset ($_GET['webapp'])) {
		if (isset ($_COOKIE[$cookie_id])) {
			$cookie = $_COOKIE[$cookie_id];
			@$webapp = $cookie['webapp'];
		}
	} else {
		$delay = 0;
		$webapp = $_GET['webapp'];
	}
	if (isset ($webapp)) {
		if (isset ($_GET['remindwebapp'])) {
			setcookie($cookie_id."[webapp]", $webapp, time() + 24 * 3600 * 7);
		}
	} else {
		$delay = $scmlogs_conf['delay.long'];
		// Default web application
		$webapp = $scmlogs_conf['engines.default'];
	}
	
	// Start operation
	@$op = $_GET['op'];
	@$reponame = $_GET['repname'];
	$urlEngineIds = array ();
	foreach ($scmlogs_conf['engines'] as $engine_name => $engine_url) {
		$classname = $engine_name . "UrlEngine";
		require_once "engines/$engine_name.php";
		$urlEngineIds[$engine_name] = new $classname($engine_url, $reponame);
	}

	if (!isset($urlEngineIds[$webapp])) {
		$webapp = $scmlogs_conf['engines.default'];
	}
	$appUrlEngine = $urlEngineIds[$webapp];
	$url = url_for_operation_on_browser ($op, $appUrlEngine);
	echo '<meta http-equiv="refresh" content="'.$delay.'; url='.$url.'">';
	echo '<div style="text-align: center;">';
	if ($delay > 0) {
		echo 'In '.$delay.' seconds, you will be redirected to <br/>';
	} else {
		echo 'Redirected to <br/>';
	}
	echo '<a href="'.$url.'">"'.$url . '</a>';
	echo '</div><br/>';

	echo '<ul>';
	foreach ($urlEngineIds as $ids => $eng) {
		$url = url_for_browser ($ids);
		//$url = url_for_operation_on_browser ($op, $urlEngineIds[$ids]);
		echo '<li>Click to use  <a href="'.$url.'">'.$ids . '</a> ';
		if ($ids == $webapp) { echo "(default)"; }
		echo '</li>';
	}
	echo '</ul>';
//	echo $url;
	#header ("Location: $url");

	exit;

?>
