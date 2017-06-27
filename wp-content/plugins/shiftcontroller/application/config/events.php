<?php
/* define our events after save */
$config['shift.after_save'][] = create_function( '$object', '
	$changes = $object->get_changes();
	if( ! $changes ){
		return;
	}

	/* CHECK IF TIMEOFF */
	if( 
		$object->type == $object->_const("TYPE_TIMEOFF")
		){
			$object->trigger_event("after_save", "timeoff");
			return;
		}

	/* NEW ONE */
	if( array_key_exists("id", $changes) ){
		/* shift.published */
		if( 
			$object->status == $object->_const("STATUS_ACTIVE")
			){
				$object->trigger_event("published");
			}
	}
	/* EXISTING */
	else {
		/* shift.published */
		if( 
			( array_key_exists("status", $changes) )
			&& 
			( $object->status == $object->_const("STATUS_ACTIVE") )
			){
				$object->trigger_event("published");
				return;
			}

		/* shift.cancelled */
		if( 
			array_key_exists("status", $changes)
			&& 
			( $object->status == $object->_const("STATUS_DRAFT") )
			&&
			( ! array_key_exists("user_id", $changes) )
			){
				$object->trigger_event("cancelled");
				return;
			}

		/* shift.assigned */
		if( 
			array_key_exists("user_id", $changes)
			&&
			$object->user_id
			&&
			( $object->status == $object->_const("STATUS_ACTIVE") )
			){
				$object->trigger_event("assigned");
				return;
			}

		/* shift.unassigned */
		if( 
			array_key_exists("user_id", $changes)
			&&
			$changes["user_id"]
			&& (
				( $object->status == $object->_const("STATUS_ACTIVE") )
				OR (
					array_key_exists("status", $changes)
					&&
					( $changes["status"] == $object->_const("STATUS_ACTIVE") )
				)
			)
			){
				$object->trigger_event("unassigned");
				return;
			}

	/* shift.changed */
		$track_changes = array("location_id", "start", "end", "lunch_break", "date");
		if( 
			array_intersect($track_changes, array_keys($changes))
			&&
			($object->status == $object->_const("STATUS_ACTIVE"))
			){
			$object->trigger_event("changed");
			return;
		}
	}
');

$config['timeoff.after_save'][] = create_function( '$object', '
	$changes = $object->get_changes();
	if( ! $changes ){
		return;
	}

	/* NEW ONE */
	if( array_key_exists("id", $changes) ){
		/* timeoff.published */
		if(
			$object->status == $object->_const("STATUS_ACTIVE")
			){
				$object->trigger_event("published", "timeoff");
			}
		/* timeoff.pending */
		else if(
			$object->status == $object->_const("STATUS_DRAFT")
			){
				$object->trigger_event("pending", "timeoff");
			}
		return;
	}
	/* EXISTING */
	else {
		/* timeoff.published */
		if( 
			( array_key_exists("status", $changes) )
			&& 
			( $object->status == $object->_const("STATUS_ACTIVE") )
			){
				$object->trigger_event("published", "timeoff");
				return;
			}

		/* timeoff.cancelled */
		if( 
			array_key_exists("status", $changes)
			&& 
			( $object->status == $object->_const("STATUS_DRAFT") )
			){
				$object->trigger_event("cancelled", "timeoff");
				return;
			}

	/* timeoff.changed */
		$object->trigger_event("changed", "timeoff");
		return;
	}
');

$config['shift.before_delete'][] = create_function( '$object', '
	/* shift.published */
	if( 
		$object->status == $object->_const("STATUS_ACTIVE")
		){
			$object->trigger_event("deleted");
		}
');
