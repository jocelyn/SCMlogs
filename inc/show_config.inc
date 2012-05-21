<?php

	include "conf/config.inc";
	include INC_DIR."datamanager.inc";

	if (!isset ($user)) { echo "ERROR: user is not set !"; exit(); }
	$user_cfg = userModulesText ($user);
	echo "<center><br><textarea name='modules' cols='60' rows='30' >".$user_cfg."</textarea></center>\n";

?>
