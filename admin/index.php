<?php

include "../conf/config.inc";
include INC_DIR."require.inc";
include INC_DIR."datamanager.inc";
include INC_DIR."users.inc";
global $SCMLOGS;

?>
<html>
<head>
<title>Administration</title>
<script language="javascript" type="text/javascript" src="../styles/block_control.js"></script>
<style>
a {
	text-decoration: none; 
	color: #009;
}
a:hover {
	text-decoration: underline; 
}
.title {
	font-weight: bold;
}
.box {
	margin-left: 40px; padding: 5px;
}
.box ul {
	margin: 0 0 10px 0;
}
.nota {
	font-size: smaller;
	font-style: italic;
}
.valid { padding-left: 10px; color: #090; font-weight: bold; }
.error { padding-left: 10px; color: #f00; font-weight: bold; }
.warning { padding-left: 10px; color: #f80; font-weight: bold; }
div.question { 
	margin-left: 10%;
	width: 200px;
	padding: 10px; 
	color: #000; 
	background-color: #ccccff;
	border: solid 2px #009;
	font-weight: bold;
}
</style>
</head>
<body>
<?php

if (isset($_GET['repo'])) {
	$asked_repo = $_GET['repo'];
}
if (isset($_POST['op'])) {
	$op = $_POST['op'];
} elseif (isset($_GET['op'])) {
	$op = $_GET['op'];
} else {
	$op = 'admin';
}
if (isset($_POST['user'])) {
	$w_user = $_POST['user'];
} elseif (isset($_GET['user'])) {
	$w_user = $_GET['user'];
}
if (isset($_GET['confirm'])) {
	$w_confirm = $_GET['confirm'] == 'yes';
} else {
	$w_confirm = false;
}
if (isset($_POST['action'])) {
	$w_action = $_POST['action'];
}
if (isset ($asked_repo)) {
	if ($asked_repo == SCMLogs_repository_id()) {
	} else {
		SCMLogs_set_repository_by_id ($asked_repo);
	}
}

$repo =& SCMLogs_repository();
$msg_exists = "<span class=\"valid\">EXISTS</span>"; 
$msg_missing = "<span class=\"error\">MISSING</span>"; 
$msg_not_found = "<span class=\"warning\">NOT FOUND</span>"; 

$last_check_had_error = false;
function adminCheckPath($path,$required=true) {
	global $msg_exists, $msg_not_found, $msg_missing;
	global $last_check_had_error;
	$last_check_had_error = false;
	$res = "$path";
	if (file_exists($path)) { 
		$res .= $msg_exists; 
	} else { 
		if ($required) {
			$res .= $msg_missing; 
			$last_check_had_error = true;
		} else {
			$res .= $msg_not_found; 
		}
	}
	return $res;
}

echo "<h1>Administration</h1>";
switch ($op) {
	case 'edit':
		if (isset ($w_user)) {
			echo "Editing ..." . $w_user;
			if ($w_action == 'save') {
				$userprof =& new UserProfile ($w_user);
				$w_prefs =& $_POST['prefs'];
				foreach ($w_prefs as $kp => $vp) {
					$userprof->prefs[$kp] = $vp;
				}
				$userprof->save_prefs();
				echo "Preferences saved:<br/><pre>".$userprof->to_text()."</pre>\n";
			} else {
				if (hasUserProfile($w_user)) {
					$userprof =& new UserProfile ($w_user);
					$prefs =& $userprof->prefs;
					echo '<form action="" method="post">';
					echo '<input type="hidden" name="op" value="edit" />';
					echo '<input type="hidden" name="user" value="'.$w_user.'" />';
					echo '<table>';
					foreach ($prefs as $kp => $vp) {
						echo '<tr>';
						echo "<td>$kp</td>";
						echo "<td><input type=\"text\" name=\"prefs[$kp]\" value=\"$vp\"</td>\n";
						echo '</tr>';
					}
					echo '</table>';
					echo '<input type="reset" value="Reset"/>';
					echo '<input type="submit" value="save" name="action"/>';
					echo '</form>';
				} else {
					echo 'No profile found !' . $w_user;
				}
			}
		}
		break;
	case 'remove':
		if (isset ($w_user)) {
			echo "Removing ..." . $w_user;
			echo '<br/>';
		}
		if ($w_confirm) {
			deleteUserFile($w_user);
			if (hasUserProfile($w_user)) {
				echo "operation failed";
				echo '<br/>';
			} else {
				echo "[$w_user] successfully removed";
				echo '<br/>';
			}
		} else {
			echo '<div class="question">';
			echo 'Do you confirm ? ';
			echo '<a href="?op='.$op.'&user='.$w_user.'&confirm=yes">Yes</a>';
			echo ' | ';
			echo '<a href="index.php">No</a>';
			echo '</div>';

		}
		break;
	case 'create':
		if (isset ($w_user)) {
			echo "creating ..." . $w_user;
			echo '<br/>';
		}
		if ($w_confirm) {
			createUserFile($w_user);
			if (hasUserProfile($w_user)) {
				echo "[$w_user] successfully created";
				echo '<br/>';
			} else {
				echo "operation failed";
				echo '<br/>';
			}
		} else {
			echo '<div class="question">';
			echo 'Do you confirm ? ';
			echo '<a href="?op='.$op.'&user='.$w_user.'&confirm=yes">Yes</a>';
			echo ' | ';
			echo '<a href="index.php">No</a>';
			echo '</div>';
		}
		break;
	case 'admin':
		echo "<a href=\"..\">Back to application</a><br/><br/>";
		echo "<strong>Selected repository</strong> : " . $repo->id . "<br/>";
		echo '<div class="box">';
		echo '- <strong>data dir</strong> : ' . adminCheckPath(dataDirectory()) . '<br/>';
		echo '- <strong>repository type dir</strong> : ' . adminCheckPath(repositoryTypeDirectory()) . '<br/>';
		echo '- <strong>repository config dir</strong> : ' . adminCheckPath(repositoryCfgDirectory()) . '<br/>';
		if (!$last_check_had_error) {
			$users = listOfUsers();
			echo '- <strong>default user config</strong> : ' . adminCheckPath(userCfgDefaultFilename()) . '<br/>';
			echo '- <strong>default user pref</strong> : ' . adminCheckPath(userPrefDefaultFilename()) . '<br/>';
			echo '<br/>';
			echo '<form action="" method="GET">';
			echo '- <strong>Users ('.count($users).') </strong> : ';
			echo '<script>StartBlockControl("users","+","-",false);</script>';
			echo '<ul><input type="text" name="user" value=""/>';
			echo '<input type="submit" name="op" value="create"/>';
			echo '<input type="submit" name="op" value="remove"/>';
			echo '</ul>';
			echo '</form>';
			echo '<ul>';
				foreach ($users as $u ) {
					echo '<li>';
					echo $u;
					echo ' [ <a href="?op=remove&user='.$u.'">remove</a> ]';
					echo ' [ <a href="?op=edit&user='.$u.'">edit</a> ]';
					echo '<ul>';
					echo adminCheckPath(userConfigFilename($u));
					echo '<br/>';
					echo adminCheckPath(userPrefFilename($u),false);
					$userprof =& new UserProfile ($u);
					echo '<br/><script>StartBlockControl("pref'.$u.'","Show prefs","Hide pref",false);</script>';
					echo "<div style=\"white-space: pre; font-size:80%; padding-left: 20px\">".$userprof->to_text()."</div>\n";
					echo '<script>EndBlockControl("pref'.$u.'")</script>';
					echo '</ul>';
					echo '</li>';
					}
			echo '</ul>';
			echo '<script>EndBlockControl("users")</script>';
		}
		echo '</div>';

		foreach ($SCMLOGS['repositories'] as $k_id => $repo) {
			echo '- <a class="title" href="?repo='.$repo->id.'">'.$repo->id . "</a>";
			echo ' (<a class="nota" href="?repo='.$repo->id.'">browse</a>)';
			echo '<div style="margin-left: 40px; padding: 5px;">';
			echo '- <strong>type</strong> : ' . $repo->mode . '<br/>';
			echo '- <strong>path</strong> : ' . adminCheckPath($repo->path) . '<br/>';
			echo '- <strong>logs</strong> : ' . adminCheckPath($repo->logsdir) . '<br/>';
			echo '</div>';
		}
		break;
	default:
		break;
}
echo '<br/><br/><a href="index.php">Back to administration</a>';


?>
</body>
</html>
