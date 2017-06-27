<?php
/*
$hook['pre_controller'][] = create_function( '', '
	if( ! function_exists("__") ){
		$locale = "it_IT";

		$app = HC_App::app();
		if( $app == "shiftexec" ){
			$app = "shiftcontroller";
		}

		$modir = $GLOBALS["NTS_APPPATH"] . "/../languages";
		$mofile = $modir . "/" . $app . "-" . $locale . ".mo";
		// echo "mofile = $mofile<br>";

		global $NTS_GETTEXT_OBJ;
		$NTS_GETTEXT_OBJ = new Gettext_PHP( $mofile );
	}
');
*/

$hook['post_controller'][] = array(
	'function' => 'hc_run_notifier',
	'filename' => 'hc_lib.php',
	'filepath' => '../hclib',
	);
