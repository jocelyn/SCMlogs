<?php

class SiteApp_index extends SiteApplication {
	Function SiteApp_index ($app, $site) {
		parent::SiteApplication($app, $site);
	}

	Function printContent() {
		echo "<h1>Index page</h1>";
		echo $this->site;
		$user = $this->site->username();
		if (isset($user)) {
			echo " <a href='index.php?".FMWK_PARAM_APP."=sign&".FMWK_PARAM_OP."=logout&asked_app=".$this->name."'>sign out</a>";
		} else {
			echo " <a href='index.php?".FMWK_PARAM_APP."=sign&".FMWK_PARAM_OP."=login&asked_app=".$this->name."'>sign in</a>";
		}
		echo "<br/>";
		parent::printContent();
	}
}

?>
