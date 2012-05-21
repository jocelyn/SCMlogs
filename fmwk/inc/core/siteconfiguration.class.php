<?php

class SiteConfiguration {
	var $values;

	function SiteConfiguration() {
		$this->values=array();
	}
	function set_value ($name, $value) {
		$this->values[$name] = &$value;
	}
	function &value ($name) {
		return $this->values[$name];
	}
}

?>
