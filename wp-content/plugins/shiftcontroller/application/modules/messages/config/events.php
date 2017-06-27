<?php
/* shift notifications */
$config['shift.published'][] = create_function( '$object', '
	$messages = HC_App::model("messages");
	$messages->send( "shift.owner.published", $object->user, array("shift" => $object) );
	return;
');

$config['shift.cancelled'][] = create_function( '$object', '
	$messages = HC_App::model("messages");
	$messages->send( "shift.owner.cancelled", $object->user, array("shift" => $object) );
	return;
');

$config['shift.assigned'][] = create_function( '$object', '
	$messages = HC_App::model("messages");
	$messages->send( "shift.owner.published", $object->user, array("shift" => $object) );
	return;
');

$config['shift.unassigned'][] = create_function( '$object', '
	$changes = $object->get_changes();
	$old_user = HC_App::model("user")
		->where("id", $changes["user_id"])
		->get()
		;

	$messages = HC_App::model("messages");
	$messages->send( "shift.owner.cancelled", $old_user, array("shift" => $object) );
	return;
');

$config['shift.changed'][] = create_function( '$object', '
	$messages = HC_App::model("messages");
	$messages->send( "shift.owner.changed", $object->user, array("shift" => $object) );
	return;
');

$config['shift.deleted'][] = create_function( '$object', '
	$messages = HC_App::model("messages");
	$copy_object = clone $object;
	$messages->send( "shift.owner.cancelled", $object->user, array("shift" => $copy_object) );
	return;
');

/* timeoff notifications */
$config['timeoff.published'][] = create_function( '$object', '
	$messages = HC_App::model("messages");
	$messages->send( "timeoff.owner.published", $object->user, array("timeoff" => $object) );
	return;
');

$config['timeoff.pending'][] = create_function( '$object', '
	$messages = HC_App::model("messages");

/* owner */
	$messages->send( "timeoff.owner.pending", $object->user, array("timeoff" => $object) );

/* admins */
	$admins = HC_App::model("user")->get_admins( $object );
	foreach( $admins as $adm ){
	/* hack - only admins, no managers */
		if( $adm->level != $adm->_const("LEVEL_ADMIN") ){
			// continue;
		}
		$messages->send( "timeoff.admin.pending", $adm, array("timeoff" => $object) );
	}
	return;
');

$config['timeoff.cancelled'][] = create_function( '$object', '
	$messages = HC_App::model("messages");
	$messages->send( "timeoff.owner.cancelled", $object->user, array("timeoff" => $object) );
	return;
');
