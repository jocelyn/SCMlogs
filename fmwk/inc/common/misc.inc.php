<?php

	// Variables
	function value_from_LIST (&$list, $param_name, $default_value=NULL){
		if (isset ($list[$param_name])) {
			return $list[$param_name];
		} else {
			return $default_value;
		}
	}
	function value_from_SetOfLIST (&$setOflists, $param_name, $default_value=NULL){
		foreach ($setOflists as $list) {
			$res = value_from_LIST (&$list, $param_name, NULL);
			if (isset ($res)) {
				return $res;
			}
		}
		return $default_value;
	}

	function value_from_GLOBAL ($param_name, $default_value=NULL){
		return value_from_LIST (&$GLOBALS, $param_name, $default_value);
	}
	function value_from_GET ($param_name, $default_value=NULL){
		return value_from_LIST (&$_GET, $param_name, $default_value);
	}

	function value_from_POST ($param_name, $default_value=NULL){
		return value_from_LIST (&$_POST, $param_name, $default_value);
	}

	function value_from_GET_POST ($param_name, $default_value=NULL){
		$lst = array (&$_GET, &$_POST);
		return value_from_SetOfLIST (&$lst, $param_name, $default_value);
	}
	function value_from_POST_GET ($param_name, $default_value=NULL){
		$lst = array (&$_POST, &$_GET);
		return value_from_SetOfLIST (&$lst, $param_name, $default_value);
	}

	function value_from_GLOBALS_GET_POST ($param_name, $default_value=NULL){
		$lst = array (&$GLOBALS, &$_GET, &$_POST);
		return value_from_SetOfLIST (&$lst, $param_name, $default_value);
	}

	function value_from_GLOBALS_POST_GET ($param_name, $default_value=NULL){
		$lst = array (&$GLOBALS, &$_POST, &$_GET);
		return value_from_SetOfLIST (&$lst, $param_name, $default_value);
	}

?>
