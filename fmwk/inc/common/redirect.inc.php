<?php

if (!defined ("__LIB_REDIRECT_INC__")) {
define ("__LIB_REDIRECT_INC__", True);

Function redirect ($url) {
	// redirection 
	header("Request-URI: $url");  
	header("Content-Location: $url");  
	header("Location: $url"); 
}

Function redirect_now ($url) {
	?>
	<script language=javascript> 
	<!-- 
	window.location = "<?php echo $url; ?>"; 
	//--> 
	</script> 
	<?php
}

Function redirect_soon ($url, $delay, $mesg="Please wait, you are being redirected to <a href=\"%URL%\">%URL%</a> in %DELAY% second(s)") {
	?>
	<script language=javascript> 
	<!-- 
	setTimeout("window.location='<?php echo $url; ?>'",<?php echo 1000 * $delay; ?>) ;
	//--> 
	</script> 
	<?php

		$outmesg = $mesg;
		$outmesg = str_ireplace("%URL%", $url, $outmesg); 
		$outmesg = str_ireplace("%DELAY%", $delay, $outmesg); 
		echo $outmesg ."\n";
}

} // end if defined
?>
