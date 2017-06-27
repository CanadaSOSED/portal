<?php
$link = HC_Lib::link('shifts/add/insert-time');
$test_shift = HC_App::model('shift');

$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', $link->url($params->to_array()) )
	;

/* time labels with shift templates */
$templates_label = '';
if( count($shift_templates) && ($params->get('type') == $test_shift->_const('TYPE_SHIFT'))){
	$templates_label = HC_Html_Factory::element('a')
		->add_child( HCM::__('Shift Templates') )
		->add_attr('style', 'font-weight: normal;')
		;

	$t = HC_Lib::time();
	$templates_content = HC_Html_Factory::widget('list')
		->add_children_style('box')
		->add_children_style('margin', 'b1')
		;
	foreach( $shift_templates as $sht ){
		$end = ($sht->end > 24*60*60) ? ($sht->end - 24*60*60) : $sht->end;
		$templates_content->add_child(
			'item-' . $sht->id,
			HC_Html_Factory::element('a')
				->add_attr('class', 'hcj-shift-templates')
				->add_attr('class', 'hcj-collapse-closer')

				->add_attr('href', '#')
				->add_attr('data-start', $sht->start)
				->add_attr('data-end', $end)
				->add_attr('data-lunch-break', $sht->lunch_break)

				->add_attr('data-start-display', $t->formatTimeOfDay($sht->start))
				->add_attr('data-end-display', $t->formatTimeOfDay($sht->end))

				->add_child( $sht->present_name() )
				->add_child( '<br/>' )
				->add_child( $sht->present_time() )

				->add_style('btn')
				->add_style('padding', 0)
			);
		$templates_content->add_child_attr(
			'item-' . $sht->id, 'class', array('alert', 'alert-default-o')
			);
	}
}

if( count($locations) ){
	$location_input = $form->input('location');
	$location_options = array();
	$location_options[0] = ' - ' . HCM::__('Please Select') . ' - ';
	foreach( $locations as $loc ){
		$location_options[ $loc->id ] = $loc->present_title( HC_PRESENTER::VIEW_RAW );
	}
	$location_input->set_options( $location_options );

	$display_form->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Location') )
			->set_content( $location_input )
			->set_error( $form->input('location')->error() )
		);
}

$time_label = HCM::__('Time');
if( ! $templates_label ){
	$time_content = $form->input('time');
}

/* VER 2 WITH COLLAPSER */
if( $templates_label ){
	$collapse = HC_Html_Factory::widget('collapse')
		;
	$collapse->set_content( $templates_content );
	$collapse->set_title( $templates_label );

	$time_label = HC_Html_Factory::widget('list')
		;
	$time_label->add_child( HCM::__('Time') );
	$time_label->add_child(
		$collapse->render_trigger()
		);

	$time_content = HC_Html_Factory::widget('list')
		;
	$time_content->add_child( $collapse->render_content() );
	$time_content->add_child( $form->input('time') );
}
/* END OF VER 2 WITH COLLAPSER */

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_label( $time_label )
		->set_content( $time_content )
		->set_error( $form->input('time')->error() )
	);

/* BREAK */
if( $params->get('type') != $test_shift->_const('TYPE_TIMEOFF') ){
	$display_form
		->add_child(
			HC_Html_Factory::widget('label_row')
				->set_label( HCM::__('Break') )
				->set_content(
					$form->input('lunch_break')
					)
				->set_error( $form->input('lunch_break')->error() )
			)
		;
}

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Date') )
		->set_content( $form->input('date') )
		->set_error( $form->input('date')->error() )
	);

$buttons = HC_Html_Factory::widget('list')
	->add_style('submit-bar')
	;
$buttons->add_child(
	HC_Html_Factory::element('input')
		->add_attr('type', 'submit')
		->add_attr('title', HCM::__('Proceed') )
		->add_attr('value', HCM::__('Proceed') )
		->add_style('btn-primary')
	);
$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_content( $buttons )
	);

$out = HC_Html_Factory::widget('list')
	->add_children_style('margin', 'b1')
	;
$out->add_child( $display_form );

echo $out->render();
?>