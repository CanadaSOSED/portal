<?php
$hook['post_controller'][] = create_function( '', '
	$model = HC_App::model("user_preferences");
	if( 
		$model && 
		is_object($model) && 
		method_exists($model, "write") 
		){
		$model->write();
	}
');