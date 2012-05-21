<?php

if (!defined ("__USERS_INC__")) {
define ("__USERS_INC__", True);

	include INC_DIR."datamanager.inc";

class UserProfile {
	var $name;
	var $prefs;
	Function UserProfile($name) {
		$this->name = $name;
		$this->prefs = array();
		$this->load_prefs();
	}
	Function load_prefs () {
		$fn = $this->pref_filename();
		if (!file_exists ($fn)) {
			$fn = userPrefDefaultFilename ();
		}
		$tmp_pref = split ("\n", ContentOfFile ($fn));
		while (list ($k, $pref) = each ($tmp_pref)) {
			$pos = strpos ($pref, "=");
			if ($pos > 0) {
				$var = trim (substr($pref, 0, $pos));
				$val = trim (substr($pref, $pos + 1));
				$this->prefs[$var]=$val;
			}
		}
	}	

	Function to_text() {
		$txt = '';
		foreach ($this->prefs as $kp => $vp) {
			$txt .= "$kp=$vp\n";
		}
		return $txt;
	}
	Function save_prefs() {
		SaveTextIntoFile($this->to_text(), $this->pref_filename());
	}

	function pref_filename() {
		return userPrefFilename ($this->name);
	}
	function cfg_filename() {
		return userCfgFilename ($this->name);
	}
}

function user_exists ($user) {
	return file_exists (userConfigFilename ($user));
}

}

?>
