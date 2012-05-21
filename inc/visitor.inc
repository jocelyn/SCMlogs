<?php

global $SERVER_NAME;
global $REMOTE_HOST;
global $REMOTE_ADDR;
global $REQUEST_URI;

$visitors_file = "visitors.html";
echo "<A HREF=\"$visitors_file\">visitors</A>";

if ($REMOTE_HOST != $SERVER_NAME) {
    if ($REMOTE_HOST == "") {
        $info_visitor = $REMOTE_ADDR;
        $info_visitor = @gethostbyaddr ("$info_visitor");
    }
    else {
        $info_visitor = $REMOTE_HOST;
    }

    if (file_exists ($visitors_file)) {
	    $fp = fopen($visitors_file,  "a");
	    fputs($fp, date("Y/m/d H:i")." : <STRONG>".$info_visitor."</STRONG> @ $REQUEST_URI <BR>\n");

	    fclose($fp);
    }; //else { echo "not found ..."; }
}

?>
