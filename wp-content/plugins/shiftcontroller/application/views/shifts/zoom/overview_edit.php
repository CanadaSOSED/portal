<?php
$can_save = FALSE;

$object = clone $object;
$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', HC_Lib::link('shifts/update/index/' . $object->id) )
	;

$display_form
	->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( 'ID' )
			->set_content( 
				HC_Html_Factory::element('span')
					->add_child( $object->id )
					->add_style('mute')
				)
			->set_content_static(1)
		)
	;

/* LOCATION */
if( $object->type != $object->_const('TYPE_TIMEOFF') ){
	$content_static = 0;
	if( count($can['location']) > 1 ){
		$can_save = TRUE;
		$location_view = HC_Html_Factory::widget('module')
			->set_url('shifts/zoom/form')
			->pass_arg( $object )
			->pass_arg( 'location' )
			->set_self_target( TRUE )
			->set_skip_src( TRUE )
		;
	}
	else {
		$location_view = $object->present_location();
		$content_static = 1;
	}

	$display_form
		->add_child(
			HC_Html_Factory::widget('label_row')
				->set_label( HCM::__('Location') )
				->set_content( $location_view )
				->set_content_static( $content_static )
			)
		;
}

/* date */
$date_label = HCM::__('Date');
if( $can['time'] ){
	$can_save = TRUE;
	$display_form
		->add_child(
			HC_Html_Factory::widget('label_row')
				->set_label( $date_label )
				->set_content( $form->input('date') )
				->set_error( $form->input('date')->error() )
			)
		;
}
else {
	$display_form
		->add_child(
			HC_Html_Factory::widget('label_row')
				->set_label( $date_label )
				->set_content( $object->present_date() )
				->set_content_static()
			)
		;
}

/* time labels with shift templates */
$templates_label = '';
if( count($shift_templates) && ($object->type == $object->_const('TYPE_SHIFT'))){
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
	}
}

$time_label = HCM::__('Time');
if( ! $templates_label ){
	$time_content = $form->input('time');
}

if( $can['time'] ){
	$can_save = TRUE;
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
			->add_children_style('margin', 'b1')
			;
		$time_content->add_child( $collapse->render_content() );
		$time_content->add_child( $form->input('time') );
	}
	/* END OF VER 2 WITH COLLAPSER */

	$display_form
		->add_child(
			HC_Html_Factory::widget('label_row')
				->set_label( $time_label )
				->set_content( $time_content )
				->set_error( $form->input('time')->error() )
			)
		;
}
else {
	$display_form
		->add_child(
			HC_Html_Factory::widget('label_row')
				->set_label( $time_label )
				->set_content( $object->present_time() )
				->set_content_static()
			)
		;
}

/* BREAK */
if( $object->type != $object->_const('TYPE_TIMEOFF') ){
	if( $can['time'] ){
		$can_save = TRUE;
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
	else {
		/* nothing as the break will be shown in the time view */
	}
}

/* STAFF */
$display_form
	->add_child( $form->input('user') )
	;

if( count($can['user']) > 1 ){
	$can_save = TRUE;
	$content_static = 0;
	$staff_view = HC_Html_Factory::widget('module')
		->set_url('shifts/zoom/form')
		->pass_arg( $object )
		->pass_arg( 'user' )
		->set_self_target( TRUE )
		->set_skip_src( TRUE )
	;
}
else {
	$content_static = 1;
	$staff_view = HC_Html_Factory::element('div')
		// ->add_style('box')
		->add_style('padding', 'y1')
		->add_style('display', 'block')
		->add_child( $object->present_user() )
	;
}

$display_form
	->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Staff') )
			->set_content( $staff_view )
			->set_content_static( $content_static )
		)
	;

/* STATUS */
$status_input = $form->input('status')
	->set_inline( TRUE )
	;

if( count($can['status']) > 1 ){
	$can_save = TRUE;
}
foreach( $can['status'] as $status ){
	$status_input
		->add_option(
			$status,
			$object->set('status', $status)->present_status()
			)
		;
}

$display_form
	->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Status') )
			->set_content(
				$status_input
					->render()
				)
			->set_error( $status_input->error() )
		)
	;

/* ADD NOTE IF POSSIBLE */
$extensions = HC_App::extensions();
$more_content = $extensions->run('shifts/zoom/confirm', $object, (! $can_save) );
if( $more_content ){
	$more_holder = HC_Html_Factory::widget('list')
		->add_children_style('margin', 'b1')
		;
	foreach($more_content as $mc ){
		$more_holder->add_child( $mc );
	}
	// $display_form->add_child( $more_holder );
	$display_form
		->add_child(
			HC_Html_Factory::widget('label_row')
				// ->set_label( HCM::__('Staff') )
				->set_content(
					$more_holder
					)
			)
		;
}

/* BUTTONS */
if( $can_save ){
	$buttons = HC_Html_Factory::widget('list')
		->add_style('submit-bar')
		;

	$buttons->add_child(
		HC_Html_Factory::element('input')
			->add_attr('type', 'submit')
			->add_attr('title', HCM::__('Save') )
			->add_attr('value', HCM::__('Save') )
			->add_style('btn-primary')
		);

	/* delete */
	if( $can_delete ){
		$link = HC_Lib::link('shifts/delete/index/' . $object->id);
		$btn = HC_Html_Factory::element('a')
			->add_attr( 'href', $link->url() )
			->add_attr( 'title', HCM::__('Delete') )
			->add_attr( 'class', 'hcj-confirm' )
			->add_child(
				HC_Html::icon('times') . HCM::__('Delete')
				)
			->add_style('btn-danger')
			;
		$buttons->add_child( 'delete', $btn );
		$buttons->add_child_style('delete', 'right' );
	}

	$display_form
		->add_child(
			HC_Html_Factory::widget('label_row')
				->set_content( $buttons )
		);
}

// echo $display_form->render();

$out = HC_Html_Factory::widget('flatmodal');
$out->set_content( $display_form );
echo $out->render();
?>