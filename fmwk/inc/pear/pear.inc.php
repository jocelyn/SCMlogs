<?php

define ('PEARDIR', dirname(__FILE__));
require_once PEARDIR."/PEAR.php";

$incpath = ini_get('include_path');
if ($incpath) {
    ini_set('include_path', PEARDIR . PATH_SEPARATOR.$incpath);
} else {
    ini_set('include_path', './' . PATH_SEPARATOR. PEARDIR);
}

?>
