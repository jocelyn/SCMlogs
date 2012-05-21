<?php

Function displayFullTreeWithUsers ($list_of_users) {
	$users = $list_of_users;
	sort ($users);
	$megausers = array ();
	while (list ($uk,$uv) = each ($users)) {
		$megausers[$uv] = userModules ($uv);
	}

	$modules = allModules ();
	//sort ($modules);
	$error = "";
	$linebgcolor = "#ffffff";
	echo "<TABLE bgcolor=#7777FF border=0 cellpadding=4 cellspacing=1>\n";
	while (list ($mod, $resps) = each ($modules)) {
		if ($linebgcolor == "#ffffff") {
			$linebgcolor = "#eeeeee";
		} else {
			$linebgcolor = "#ffffff";
		}
		echo "<TR>";
		echo "<TD STYLE=\"background-color: $linebgcolor; \" >$mod</TD>";
		reset ($megausers);
		$nb = 0;
		while (list ($uk, $uv) = each ($megausers)) {
			$is_selected = isSelected ($mod, $uv);
			$is_resp = in_array ($uk, $resps);

			if ($is_resp and !$is_selected) {
				$error .= "Warning : $uk is not checking $mod !!!<BR>\n";
			}

			if ($is_resp) {
				if (!$is_selected) {
					$bgcolor = "Red";
				} else {
					$bgcolor = "#bbbbff";
				}
			} elseif ($is_selected) {
				$bgcolor = "#aaffcc";
			} else {
				$bgcolor = $linebgcolor;
			}
			if ($is_selected) {
				$nb = $nb + 1;
				$style = "STYLE=\"font-weight: bold; background-color: $bgcolor;\" ";
			} else {
				$style = "STYLE=\"color: #888888; background-color: $bgcolor; \" ";
			}

			echo "<TD $style >";
			echo $uk;
			echo "</TD>";
		}
		echo "<TD STYLE=\"background-color: $linebgcolor; \">$nb</TD>";
		echo "<TD STYLE=\"background-color: $linebgcolor; \">";
		while (list ($k, $usr) = each ($resps)) {
			echo "$usr &middot; ";
		}
		echo "</TD>";
		echo "</TR>\n";
	}
	echo "</TABLE>\n";
	echo "<BR>";
	echo $error;
}

	include "inc/show_config_tree.inc";
	$list_of_users = listOfUsers ();
	sort ($list_of_users);
	displayFullTreeWithUsers ($list_of_users);

?>
