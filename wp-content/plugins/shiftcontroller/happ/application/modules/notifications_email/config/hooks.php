<?php
$hook['post_controller_constructor'][] = create_function( '', '
	$messages = HC_App::model("messages");
	$messages->add_engine( "email", HC_App::model("notifications_email") );
');