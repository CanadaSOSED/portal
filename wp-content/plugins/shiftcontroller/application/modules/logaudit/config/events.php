<?php
$config['shift.after_save'][] = create_function( '$object', '
	$logaudit = HC_App::model("logaudit");
	$logaudit->log( $object, array("user_id", "location_id", "start", "end", "lunch_break", "date", "status", "id") );
');
