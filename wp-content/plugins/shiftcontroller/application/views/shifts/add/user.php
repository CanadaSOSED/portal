<?php
$extensions = HC_App::extensions();
$link = HC_Lib::link('shifts/add/insert-user');

$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', $link->url($params->to_array()) )
	;

$display_form_open = HC_Html_Factory::widget('form')
	->add_attr('action', $link->url($params->to_array()) )
	;

if( $params->get('type') != SHIFT_HC_MODEL::TYPE_TIMEOFF ){
	$add_params = $params->to_array();
	$add_params['user+'] = 0;

	$item_view = HC_Html_Factory::widget('list')
		// ->add_children_style('inline')
		->add_children_style('margin', 'b1')
		;

	// $item_view->add_child( HCM::__('Select Later') );

	$open_options = array();
	for( $ii = 1; $ii <= 10; $ii++ ){
		$open_options[$ii] = $ii;
	}
	$item_view->add_child(
		$form_open
			->input('open')
			->set_options( $open_options )
		);

	$item_view->add_child(
		HC_Html_Factory::element('input')
			->add_attr('type', 'submit')
			->add_attr('title', HCM::__('Create Open Shifts') )
			->add_attr('value', HCM::__('Create Open Shifts') )

			->add_style('btn-primary')
		);

	$display_form_open->add_child( $item_view );
}

$out2 = HC_Html_Factory::widget('tiles')
	->set_per_row(2)
	;

if( ! $free_staff ){
	$out2->add_child(
		HC_Html_Factory::element('span')
			->add_style('box')
			->add_style('border-color', 'red')
			->add_attr('title', HCM::__('No staff available for this shift') )
			->add_child( 
				HC_Html::icon('exclamation') . HCM::__('No staff available for this shift')
				)
		);
}
else {
	reset( $free_staff );
	foreach( $free_staff as $st ){
		$add_params = $params->to_array();
		$add_params['user+'] = $st->id;

		$main = HC_Html_Factory::element('a')
			->add_attr('href', $link->url($add_params))
			->add_attr('title', HCM::__('Assign Staff') )
			->add_attr('class', 'hcj-action')
			->add_child( $st->present_title() )
			;

		$main = HC_Html_Factory::widget('list')
			->add_children_style('inline')
			->add_children_style('margin', 'r1', 'b1')
			;
		$main->add_child( 
			'checkbox',
			$form->input('user')
				->add_option( $st->id, $st->present_title() )
				->render_one( $st->id )
			);

		$item = HC_Html_Factory::element('div')
			->add_style('box')
			;

		$item->add_child( $main );

	/* EXTENSIONS SUCH AS CONFLICTS */
		for( $mi = 0; $mi < count($models); $mi++ ){
			$models[$mi]->user_id = $st->id;
		}

		$more_content = $extensions->run('shifts/assign/quickview', $models);
		if( $more_content ){
			$more_wrap = HC_Html_Factory::widget('list')
				->add_style('margin', 'b1')
				->add_style('font-size', -1)
				;
			$added = 0;
			foreach($more_content as $mck => $mc ){
				$more_wrap->add_child($mc);
				$added++;
			}
			if( $added ){
				$item->add_child( $more_wrap );
			}
		}

		$item
			->add_style('margin', 'r2', 'b1')
			;
		$out2->add_child( $item );
	}
}

$display_form->add_child( $out2 );

$buttons = HC_Html_Factory::widget('list')
	->add_style('submit-bar')
	;

if( $free_staff ){
	$buttons->add_child(
		HC_Html_Factory::element('input')
			->add_attr('type', 'submit')
			->add_attr('title', HCM::__('Assign Selected Staff') )
			->add_attr('value', HCM::__('Assign Selected Staff') )
			->add_style('btn-primary')
		);
}
else {
	$buttons->add_child(
		HC_Html_Factory::widget('titled', 'a')
			->add_attr( 'href', HC_Lib::link('shifts/add/index')->url( $params->to_array() ) )
			->add_child( HC_Html::icon('arrow-left') )
			->add_child( HCM::__('Back') )
			->add_style('btn')
			->add_style('btn-submit')
		);

}

$display_form->add_child( $buttons );

$tabs = HC_Html_Factory::widget('tabs');
$tabs_id = 'nts' . hc_random();
$tabs->set_id( $tabs_id );

$tabs->add_tab(
	'assign',
	HCM::__('Assign Staff'),
	$display_form
	);

if( $params->get('type') != SHIFT_HC_MODEL::TYPE_TIMEOFF ){
	$tabs->add_tab(
		'open',
		HCM::__('Create Open Shifts'),
		$display_form_open
		);
}

$out = HC_Html_Factory::widget('label_row')
	->set_label( HCM::__('Staff') )
	->set_content( 
		$tabs
		)
	;

echo $out;