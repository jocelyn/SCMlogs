<?php

if (!defined ("__SHOW_CONFIG_TREE__INC__")) {
	define ("__SHOW_CONFIG_TREE__INC__", True);
	include "conf/config.inc";
	include "inc/datamanager.inc";

	Function addToFullTree ($path,$resps, $tree) {
		$pos = strpos ($path, '/', 0) ;
		if ($pos > 0) {
			$dir = substr ($path , 0, $pos);
			if (isset ($tree[$dir])) {
				$next = $tree[$dir];
			}
			else {
				$next = array();
			}
			$rest = substr ($path , $pos + 1);
			$tree[$dir] = addToFullTree ($rest, $resps, $next);
		} else {
			$tree[$path]= array();
		}
		return $tree;
	}

	Function isSelected ($path, $mods) {
		$result = False;
		while (list ($mod, $resps) = each ($mods)) {
			$result = ($result) || ($mod ==  substr ($path, 0, strlen ($mod)));
		}
		return $result;
	}


	Function displaySelectedTree ($ztree, $level=0) {
		$tab = "<IMG ALT='TAB' SRC='empty.png' height='1px' width='".($level * 20)."' >";
		while (list ($key, $val) = each ($ztree)) {
			if (isset ($val)) {
				echo "$tab";
				if ($val == '*') { 
					echo "<FONT STYLE='font-weight: bold; color: DarkBlue;'>$key ...</FONT><BR>"; 
				} else {
					echo "<EM>$key</EM><BR>"; 
				}
				if (is_array ($val)) {
					displaySelectedTree ($val, $level + 1);
				}
			} else {
				echo "ERROR";
			}
		}
	}

	Function displayAllTreeWithSelection ($fulldirname, $tree, $selection, $level=0) {
		$tab = "<IMG ALT='TAB' SRC='empty.png' height='1px' width='".($level * 20)."' >";
		while (list ($key, $val) = each ($tree)) {
			if (isset ($val)) {
				echo "$tab";
				$key_fulldirname = "$fulldirname$key/";
				$selected = isSelected ($key_fulldirname, $selection);
				if ($selected) {
					$checkbox_status ='CHECKED';
				} else {
					$checkbox_status ='';
				}
				echo "<INPUT TYPE=CHECKBOX NAME='modules[]' VALUE ='$key_fulldirname' $checkbox_status ></INPUT>";
				if ($selected) { 
					echo "<FONT STYLE='font-weight: bold; color: blue;'>$key</FONT>"; 
				} else {
					echo "<EM>$key</EM>"; 
				}
				echo "<BR>\n";
				if (is_array ($val)) {
					displayAllTreeWithSelection ($key_fulldirname, $val, $selection, $level + 1);
				}
			} else {
				echo "ERROR";
			}
		}
	}

	Function showUserTree ($user_modules) {
		$selectedtree = array ();
		while (list ($mod, $val) = each ($user_modules)) {
			if ($mod !=  '') { 
				$selectedtree = addToSelectionTree ($mod, $selectedtree);
			}
		}
		reset ($selectedtree);
		ksort ($selectedtree);
		echo "<DIV class='tree' >";
		displaySelectedTree ($selectedtree, 0);
		echo "</DIV>";
	}

	Function showAllModulesWithSelectionTree ($all_modules, $user_modules) {
		global $op;
		global $user;
		global $PHP_SELF;
		$zetree = array ();
		while (list ($mod, $resps) = each ($all_modules)) {
			if ($mod !=  '') { 
				$zetree = addToFullTree ($mod, $resps, $zetree);
			}
		}
		reset ($zetree); ksort ($zetree);
		echo "<FORM ACTION=\"$PHP_SELF\" NAME=\"SELECTIONTREEFORM\" METHOD=\"POST\" >";
		echo "<DIV STYLE='background-color: #000000; ' >\n";
		echo "<INPUT TYPE='hidden' NAME='op' VALUE='$op' >\n";
		echo "<INPUT TYPE='submit' NAME='optree' VALUE='SaveTree' >\n";
		echo "<INPUT TYPE='reset' NAME='optree' VALUE='Reset' >\n";
		echo "<INPUT TYPE='hidden' NAME='user' VALUE='$user' >\n";
		echo "</DIV>";
		echo "<DIV class=\"tree\" >\n";
		displayAllTreeWithSelection ("", $zetree, $user_modules, 0);
		echo "</DIV>";
		echo "</FORM>";
	}
}

?>
