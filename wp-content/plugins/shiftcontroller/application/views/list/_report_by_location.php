<?php
$t = HC_Lib::time();
$total_qty = array();
$total_hours = array();

reset( $shifts );
foreach( $shifts as $sh ){
	if( $sh->type != $sh->_const('TYPE_SHIFT') ){
		continue;
	}

	$entity_id = $sh->location_id ? $sh->location_id : 0;
	if( ! isset($total_qty[$entity_id]) ){
		$total_qty[$entity_id] = 0;
		$total_hours[$entity_id] = 0;
	}
	$total_qty[$entity_id] += 1;
	$total_hours[$entity_id] += $sh->get_duration();
}

$entries = array();
reset( $locations );
foreach( $locations as $location ){
	$entry = array();

	$entity_id = $location->id ? $location->id : 0;
	if( ! $entity_id ){
		continue;
	}
	$entity_title = $location->present_title()->render();
	$entity_sort = $location->show_order;

	$entry['label']			= $entity_sort;
	$entry['label_view']	= $entity_title;
	$entry['qty']			= isset($total_qty[$entity_id]) ? $total_qty[$entity_id] : 0;
	$entry['hours']			= isset($total_hours[$entity_id]) ? $total_hours[$entity_id] : 0;
	$entry['hours_view']	= isset($total_hours[$entity_id]) ? $t->formatPeriodExtraShort($total_hours[$entity_id], 'hour') : 0;
	$entries[] = $entry;
}

$columns = array(
	array(
		'prop'	=> 'label',
		'sort'	=> 1,
		'label'	=> HCM::__('Location'),
		),
	array(
		'prop'	=> 'qty',
		'sort'	=> 1,
		'label'	=> HCM::__('Shifts'),
		),
	array(
		'prop'	=> 'hours',
		'sort'	=> 1,
		'label'	=> HCM::__('Hours'),
		),
	);

$sorted_table = HC_Html_Factory::widget('sorted_table')
	->set_entries( $entries )
	->set_columns( $columns )
	->set_sortby( array('hours', 'dsc') )
	->add_children_style('padding', 'y1')
	;
echo $sorted_table->render();
?>