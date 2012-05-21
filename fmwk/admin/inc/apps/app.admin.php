<?php

class SiteApp_admin extends SiteApplication {
	var $username;

	Function SiteApp_admin ($app, $site) {
		parent::SiteApplication($app, $site);
	}

	Function checkACL() {
		$this->require_auth = TRUE;
	}
	Function printContent () {
		parent::printContent();
		echo "Administration panel<br/>";
	}
}

?>
