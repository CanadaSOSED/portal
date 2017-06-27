<?php
$t = HC_Lib::time();
$view_entries = array();
$current_user_id = $this->auth->check();

$extensions = HC_App::extensions();

foreach( $entries as $e ){
	$wrap = HC_Html_Factory::element('div')
		->add_style('border')
		->add_style('rounded')
		->add_style('padding', 2)
		->add_style('margin', 'r3', 'b3')
		;

	if( $e->id != $current_user_id ){
		$wrap->add_child(
			HC_Html_Factory::element('a')
				->add_attr('href', HC_Lib::link('admin/users/delete/' . $e->id) )
				->add_child(
					HC_Html::icon('times')
						->add_style('color', 'maroon')
					)
				->add_attr('title', HCM::__('Delete'))
				->add_attr('class', array('hcj-confirm'))
				->add_style('closer')
			);
	}

	$details_view = HC_Html_Factory::widget('list')
		->add_child(
			HC_Html_Factory::element('a')
				->add_child( $e->present_title() )
				->add_attr('href', HC_Lib::link('admin/users/zoom/index/id/' . $e->id) )
			)
		;

	$active = strlen($e->active) ? $e->active : $e->_const('STATUS_ACTIVE');
	switch( $active ){
		case $e->_const('STATUS_ACTIVE'):
			$wrap
				->add_style('border-color', 'olive')
				;
			break;

		case $e->_const('STATUS_ARCHIVE'):
/*
			$details_view
				->add_child( $e->present_status() )
				;
*/
			$wrap
				->add_style('bg-color', 'silver')
				;
			break;
	}

	$details_view
		->add_child( $e->present_level() )
		->add_child( 
			HC_Html_Factory::element('span')
				->add_style('mute')
				->add_child( $e->present_email() )
			)
		;

	$wrap->add_child( $details_view );

	/* EXTENSIONS */
	$more_content = $extensions
		->run(
			'admin/users/quickview',
			$e
		);

	if( $more_content ){
		$more_wrap = HC_Html_Factory::widget('list')
			// ->add_children_style('font-size', -1)
			;
		foreach($more_content as $mck => $mc ){
			$more_wrap->add_child($mc);
		}
		$wrap->add_child($more_wrap);
	}

	$view_entries[] = $wrap;
}

$tiles = HC_Html_Factory::widget('tiles')
	->set_per_row(3)
	;
$tiles->set_children( $view_entries );

$out = HC_Html_Factory::widget('container');
$out->add_child( $tiles );

echo $out->render();
?>