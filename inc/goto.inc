<?php
$GOTO['auth_pid'] = "auth";
$GOTO['doc_pid'] = "doc";
$GOTO['admin_pid'] = "admin";
$GOTO['stats_pid'] = "stats";
$GOTO['query_pid'] = "query";

if (!isset($goto)) { $goto = $GOTO['auth_pid']; }
switch ($goto) {
	default:
	case $GOTO['doc_pid']:
		$goto_title = "Documentation";
		$goto_src = "inc/doc.inc";
		break;
	case $GOTO['auth_pid']:
		$goto_title = "Please authentificate";
		$goto_src = "inc/auth.inc";
		break;
	case $GOTO['admin_pid']:
		$goto_title = "Administration";
		$goto_src = "inc/admin.inc";
		break;
	case $GOTO['stats_pid']:
		$goto_title = "Stats";
		$goto_src = "inc/stats.inc";
		break;
	case $GOTO['query_pid']:
		$goto_title = "Query";
		$goto_src = "inc/query.inc";
		break;
}

?>
