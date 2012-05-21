<?php

class SiteApp_accessdenied extends SiteApplication {
	var $asked_app;
	Function SiteApp_accessdenied ($app, $site) {
		parent::SiteApplication($app, $site);
	}

	function setAskedApplication ($appname) {
		$this->asked_app = $appname;
	}

	/* Protected */
	Function printContent() {
		echo "Application [" . $this->asked_app ."] Access denied<br/>----<br/>";
		echo "Please <a href=\"" . $this->site->applicationUrl("sign") ."?op=login\">login</a><br/>";
		echo "Back to [<a href=\"" . $this->site->applicationUrl($this->asked_app) ."\">Back</a>] <br/>";
		parent::printContent();
	}
}

?>
