<?php
$out = HC_Html_Factory::widget('table')
	->add_style('table', 'border')
	->add_children_style('padding', 'y1')
	;

$t = HC_Lib::time();
$total_qty = array();
$total_hours = array();

reset( $shifts );
foreach( $shifts as $sh ){
	if( $sh->type != $sh->_const('TYPE_SHIFT') ){
		continue;
	}

	$entity_id = 0;
	if( ! isset($total_qty[$entity_id]) ){
		$total_qty[$entity_id] = 0;
		$total_hours[$entity_id] = 0;
	}
	$total_qty[$entity_id] += 1;
	$total_hours[$entity_id] += $sh->get_duration();
}

$out->set_cell( 0, 0, HCM::__('Shifts') );
$out->set_cell( 0, 1, HCM::__('Hours') );

$entity_id = 0;

$ri = 1;
$out->set_cell( $ri, 0, isset($total_qty[$entity_id]) ? $total_qty[$entity_id] : 0 );
$out->set_cell( $ri, 1, isset($total_hours[$entity_id]) ? $t->formatPeriodExtraShort($total_hours[$entity_id], 'hour') : 0 );

echo $out->render();
?>