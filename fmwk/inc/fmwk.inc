<?php

if (!defined('FMWK_ROOT_DIR')) {
	define('FMWK_ROOT_DIR', realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR);
}

/*
 * Default defines, can be defined before this point
 */

define('FMWK_SITE_DIR', FMWK_ROOT_DIR.'inc' .DIRECTORY_SEPARATOR. 'sites'. DIRECTORY_SEPARATOR . 'default' .DIRECTORY_SEPARATOR);
define('FMWK_PARAM_APP', 'app');
define('FMWK_PARAM_OP', 'op');

function FMWK_include_once ($fn) { include_once (FMWK_ROOT_DIR. 'inc' .DIRECTORY_SEPARATOR. $fn); }
function FMWK_require_once ($fn) { require_once (FMWK_ROOT_DIR. 'inc' .DIRECTORY_SEPARATOR. $fn); }
function FMWK_site_require_once ($fn) { require_once (FMWK_SITE_DIR.$fn); }

FMWK_require_once ('common'.DIRECTORY_SEPARATOR.'misc.inc.php');
FMWK_require_once ('core'.DIRECTORY_SEPARATOR.'siteconfiguration.class.php');
FMWK_require_once ('core'.DIRECTORY_SEPARATOR.'siteauthentification.class.php');
FMWK_require_once ('core'.DIRECTORY_SEPARATOR.'sitemanager.class.php');


?>
