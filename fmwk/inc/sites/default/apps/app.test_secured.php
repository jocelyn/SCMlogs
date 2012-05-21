<?php

class SiteApp_test_secured extends SiteApplication {
	var $username;

	Function SiteApp_test_secured ($app, $site) {
		parent::SiteApplication($app, $site);
	}

	Function checkACL() {
		$this->require_auth = TRUE;
	}
	Function printContent () {
		parent::printContent();
		echo "Page de test<br/>";
	}
}

?>
