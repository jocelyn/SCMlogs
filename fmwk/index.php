<?php

/* Includes */
require_once 'inc'.DIRECTORY_SEPARATOR.'fmwk.inc';

/* Configuration */
$_sitecfg =& new SiteConfiguration();
$_sitecfg->set_value('site.baseurl', 'index.php');
$_sitecfg->set_value('site.title', 'Test framework');
$_sitecfg->set_value('site.default_application', 'index');

/* WebSite */
//FMWK_require_once ('auth/BasicAuth.php');
//$_siteauth =& new BasicAuth(&$_sitecfg);
FMWK_require_once ('auth/AuthHtpasswd.php');
$_siteauth =& new SiteAuthHtpasswd(&$_sitecfg, FMWK_ROOT_DIR.'.secret');
$_sitemgr =& new SiteManager(&$_sitecfg);

/* Main Application */

if (!isset($application)) {
	$application = value_from_POST_GET (FMWK_PARAM_APP, $_sitecfg->value('site.default_application'));
}

$_sitemgr->initialize(&$_siteauth);
$_sitemgr->registerApplication($application);
$_sitemgr->prepareData();
//echo 'No data should be posted before this<br/>';
$_sitemgr->printOutput();

?>
