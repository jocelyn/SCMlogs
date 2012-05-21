<?php

define('TRAC_URL', 'https://origo.ethz.ch/cgi-bin/trac.fcgi/eiffelstudio/');
define('WEBSVN_URL', 'http://eiffelsoftware.origo.ethz.ch/apps/websvn/');
define('VIEWVC_URL', 'http://www.ise/viewvc/');

$scmlogs_conf = array();

$scmlogs_conf['delay.short'] = 1;
$scmlogs_conf['delay.long'] = 3;
$scmlogs_conf['cookie'] = 'scmlogs_webapp_cookie';
$scmlogs_conf['engines.default'] = 'websvn';
$scmlogs_conf['engines']['websvn'] = WEBSVN_URL;
$scmlogs_conf['engines']['trac'] = TRAC_URL;
$scmlogs_conf['engines']['viewvc'] = VIEWVC_URL;

?>
