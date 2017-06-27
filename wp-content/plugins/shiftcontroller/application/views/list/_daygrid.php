<?php
/* shifts view */
$out = HC_Html_Factory::widget('day_grid');

$iknow = array();
$iknow[] = 'date';
$this_date = $state['date'];

if(
	( ! isset($state['staff']) ) OR 
	( count($state['staff']) == 1 ) OR
	( count($staffs) == 1 ) OR
	( count($all_staffs) == 1 )
	){
	$iknow[] = 'user';
}

if( 
	// ( ! isset($state['location']) ) OR 
	( isset($state['location']) && (count($state['location']) == 1) ) OR
	( count($locations) == 1 ) OR
	( count($all_locations) == 1 )
	){
	$iknow[] = 'location';
}

foreach( $shifts as $sh ){
	$shift_view = HC_Html_Factory::widget('shift_view');
	$shift_view
		->set_iknow($iknow)
		->set_shift($sh)
		->set_wide(0)
		;

	$slot_start = $sh->start;
	$slot_duration = $sh->get_duration();

	if( $sh->date < $this_date ){
		$slot_start = 0;
		$slot_duration = $sh->end;
	}

	$out->add_slot(
		$slot_start,
		$slot_duration,
		$shift_view
		);
}

/* extensions */
$extensions = HC_App::extensions();

$more_content = array();
if( (isset($state['range']) && ($state['range'] == 'day')) ){
	$more_content = $extensions->run(
		'list/day',
		'state', $state
		);
}

if( $more_content ){
	$out = HC_Html_Factory::widget('list')
		->add_children_style('margin', 'b1')
		->add_child( $out )
		;
	foreach( $more_content as $subkey => $subvalue ){
		if( $subvalue ){
			$out->add_child( $subvalue );
		}
	}
}

echo $out->render();
?>