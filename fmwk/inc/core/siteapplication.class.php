<?php
	
class SiteApplication {
	var $name;
	var $site;
	var $param=array();
	var $output;
	var $require_auth=FALSE;

	Function SiteApplication($app, $site) {
		$this->name = $app;
		$this->site = $site;
		if (!$this->require_auth) {
			$this->checkACL();
		}
	}

	Function checkACL() {
		$this->require_auth = FALSE;
	}
	Function getData() {
		$base_url = $this->site->base_url;
		$param =& $this->param;
	}
	Function getOutput() {
		ob_start();
		$this->printOutput();
		$this->output =& ob_get_contents();
		ob_end_clean();
	}
	Function printOutput() {
		if (empty ($this->output)) {
			$this->printHeader();
			$this->printContent();
			$this->printFooter();
		}
	}

	/* Protected */

	Function printHeader() {
		echo "<div style=\"background-color: #009; color: #fff; padding: 2px 2px 2px 10px;\">";
		echo "<a style=\"color: #ccf; text-decoration: none;\" href=\"".$this->site->applicationUrl()."\">[Home]</a> :: ";
		echo $this->name;
		$username = $this->site->username();
		if (!empty($username)) {
			echo " :: <a href=\"".$this->site->applicationUrl("sign")."&op=logout\">logout</a>";
		}
		echo "</div>";
		echo "<div style=\"border: dotted 1px #ccf; padding: 10px; margin-bottom: 15px;\">\n";
	}
	Function printContent() {
		echo "Application=" . $this->name ."<br/>----<br/>";
		$apps = $this->site->allApplicationNames();
		echo ".::";
		foreach ($apps as $ka => $va) {
			if (!in_array($va, array ("sign", "notfound", "accessdenied"))) {
				echo " <a href=\"".$this->site->applicationUrl($va)."\">" . $va ."</a> ::";
			}
		}
		echo ".";
	}
	Function printFooter() {
		echo "</div>\n";
		echo "<div style=\"color: #999; font-style: italic; text-align: right;\">Built by Jocelyn Fiat (c)</div>";
	}
}

?>
