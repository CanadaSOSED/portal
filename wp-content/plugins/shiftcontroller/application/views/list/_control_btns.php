<?php
$temp_shift = HC_App::model('shift');

/* create buttons */
$create_btns = array();
$create_btns['shift'] = 
	HC_Html_Factory::element('a')
		->add_attr('href', HC_Lib::link('shifts/add/index')->url(array('type' => $temp_shift->_const('TYPE_SHIFT'))) )
		->add_child( HC_Html::icon(HC_App::icon_for('shift')) )
		->add_attr( 'title', sprintf(HCM::_n('Add New Shift', 'Add %d New Shifts', 1), 1) )

		->add_style('btn')
		->add_style('btn-submit')
		->add_style('color', 'olive')
	;
$create_btns['timeoff'] = 
	HC_Html_Factory::element('a')
		->add_attr('href', HC_Lib::link('shifts/add/index')->url(array('type' => $temp_shift->_const('TYPE_TIMEOFF'))) )
		->add_child( HC_Html::icon(HC_App::icon_for('timeoff')) )
		->add_child( sprintf(HCM::_n('Request New Timeoff', 'Request %d New Timeoffs', 1), 1) )
		->add_attr( 'title', sprintf(HCM::_n('Request New Timeoff', 'Request %d New Timeoffs', 1), 1) )

		->add_style('btn-primary')
	;

$btns = HC_Html_Factory::widget('list')
	->add_children_style('inline')
	->add_children_style('margin', 'r1', 'b1')
	;

$this_user_id = $this->auth->user()->id;
$acl = HC_App::acl();
$test_shift = HC_App::model('shift');
$test_shift->user_id = $this_user_id;

$add_btns = array();
if( 
	isset($fix) && 
	isset($fix['type']) && 
	$fix['type'] && 
	is_array($fix['type']) 
	&& (count($fix['type']) == 1) 
	){
	switch( $fix['type'][0] ){
		case $test_shift->_const('TYPE_TIMEOFF'):
			$test_shift->type = $fix['type'][0];
			if( $acl->set_object($test_shift)->can('validate') ){
				$add_btns[] = $create_btns['timeoff'];
			}
			break;
	}
}

if( $add_btns ){
	foreach( $add_btns as $btn ){
		$btns->add_child( $btn );
	}
}

$items = $btns->children();
if( count($items) ){
	echo $btns->render();
}
?>