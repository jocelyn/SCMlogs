<?php

class SiteApp_notfound extends SiteApplication {
	var $asked_app;
	Function SiteApp_notfound ($app, $site) {
		parent::SiteApplication($app, $site);
	}

	function setAskedApplication ($appname) {
		$this->asked_app = $appname;
	}		

	/* Protected */
	Function printContent() {
		echo "Application [" . $this->asked_app ."] Not Found<br/>----<br/>";
		parent::printContent();
	}
}

?>
