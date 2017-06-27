<?php
$link = HC_Lib::link('shifts/add/insert');

/* count how many shifts will be created */
$pa = $params->to_array();
$shifts_count = 1;
$shifts_count *= count($dates);

$check = array('location', 'user');
foreach( $check as $ch ){
	if( is_array($pa[$ch]) )
		$shifts_count *= count($pa[$ch]);
}

$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', $link->url($params->to_array()) )
	;

$out = HC_Html_Factory::widget('list')
	->add_children_style('margin', 'b1')
	;

/* STATUS CHECKBOX */
$status_input = NULL;
$statuses = $params->get_options('status');
$status_view_static = 0;

if( count($statuses) > 1 ){
	$status_input = $form->input('status');
	$status_input
		->set_inline( TRUE )
		;
	foreach( $statuses as $status_option ){
		$status_input->add_option( 
			$status_option,
			$model->set('status', $status_option)->present_status()
			);
	}
}
elseif( count($statuses) == 1 ){
	reset( $statuses );
	$status_input = $model->set('status', key($statuses))->present_status();
	$status_view_static = 1;
}

if( $status_input !== NULL ){
	$out->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Status') )
			->set_content(
				$status_input
				)
			->set_content_static($status_view_static)
		);
}

/* ADD NOTE IF POSSIBLE */
$new_shift = HC_App::model('shift');
$extensions = HC_App::extensions();
$more_content = $extensions->run('shifts/add/confirm', $new_shift);
if( $more_content ){
	$more_holder = HC_Html_Factory::widget('list')
		->add_children_style('margin', 'b1')
		;

	foreach($more_content as $mc ){
		$more_holder->add_child( $mc );
	}

	$out->add_child(
		HC_Html_Factory::widget('label_row')
			->set_content(
				$more_holder
				)
	);
}

$add_btn_label = HCM::__('Add');
$type = $params->get('type');
if( $type !== NULL ){
	$shift = HC_App::model('shift');

	switch( $type ){
		case $shift->_const('TYPE_TIMEOFF'):
			$add_btn_label = sprintf( HCM::_n('Request New Timeoff', 'Request %d New Timeoffs', $shifts_count), $shifts_count );
			break;

		case $shift->_const('TYPE_SHIFT'):
			$add_btn_label = sprintf( HCM::_n('Add New Shift', 'Add %d New Shifts', $shifts_count), $shifts_count );
			break;
	}
}

$out->add_child(
	HC_Html_Factory::widget('label_row')
		->set_content(
			HC_Html_Factory::element('list')
				->add_style('submit-bar')
				->add_child(
					HC_Html_Factory::element('input')
						->add_attr('type', 'submit')
						->add_attr('title', $add_btn_label )
						->add_attr('value', $add_btn_label )
						->add_style('btn-primary')
					)
			)
	);

$display_form->add_child( $out );
echo $display_form->render();
?>