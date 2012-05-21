<?php
ini_set ("session.use_trans_sid", 1);
//error_reporting (55);
//ini_set('magic_quotes_runtime', 0);
//ini_set('magic_quotes_gpc', 0);

/* Defines */
define('ROOT_DIR', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

/* Include FMWK */
define('FMWK_ROOT_DIR', ROOT_DIR.'fmwk'.DIRECTORY_SEPARATOR);
define('FMWK_SITE_DIR', ROOT_DIR.'inc'.DIRECTORY_SEPARATOR.'site'.DIRECTORY_SEPARATOR);
require_once FMWK_ROOT_DIR.'inc'.DIRECTORY_SEPARATOR.'fmwk.inc';

/* Configuration */
include "conf/config.inc";

$_sitecfg =& new SiteConfiguration();
$_sitecfg->set_value('site.baseurl', 'index.php');
$_sitecfg->set_value('site.title', 'SCMlogs');
$_sitecfg->set_value('site.default_application', 'index');
$_sitecfg->set_value('site.passwd', $SCMLOGS['passwd.filename']);

/* WebSite */
FMWK_require_once('auth/AuthHtpasswd.php');
$_siteauth =& new SiteAuthHtpasswd(&$_sitecfg, $_sitecfg->value('site.passwd'));
$_sitemgr =& new SiteManager(&$_sitecfg);

/* Main Application */

include "inc/require.inc";
include "inc/datamanager.inc";
FMWK_site_require_once ('apps'.DIRECTORY_SEPARATOR."scmapp.php");

if (!isset($application)) {
	$application = value_from_POST_GET (FMWK_PARAM_APP, $_sitecfg->value('site.default_application'));
}

$_sitemgr->initialize(&$_siteauth);
$_sitemgr->registerApplication($application);
$_sitemgr->prepareData();
//echo 'No data should be posted before this<br/>';
$_sitemgr->printOutput();

?>
