<?php

class SiteApp_test extends SiteApplication {
	var $username;

	Function SiteApp_test ($app, $site) {
//		$this->use_tpl_engine = FALSE;
		parent::SiteApplication($app, $site);
	}

	Function printContent () {
		echo "Page de test<br/>";
		parent::printContent();
	}
}

?>
