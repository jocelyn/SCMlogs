<?php

/* Defines */
define('FMWK_SITE_DIR', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR);
define('SITE_PARAM_APP', 'app');
define('SITE_PARAM_OP', 'appop');

/* Includes */
require_once '..'.DIRECTORY_SEPARATOR.'inc'.DIRECTORY_SEPARATOR.'fmwk.inc';

/* Configuration */
$_sitecfg =& new SiteConfiguration();
$_sitecfg->set_value('site.baseurl', 'index.php');
$_sitecfg->set_value('site.title', 'Test admin');
$_sitecfg->set_value('site.default_application', 'admin');

/* WebSite */
FMWK_require_once ('auth/BasicAuth.php');
$_siteauth =& new BasicAuth(&$_sitecfg);
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
