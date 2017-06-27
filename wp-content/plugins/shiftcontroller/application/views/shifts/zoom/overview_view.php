<?php
$object = clone $object;
$display_form = HC_Html_Factory::widget('list')
	// ->add_children_style('margin', 'b1')
	;

if( $object->type != $object->_const('TYPE_TIMEOFF') ){
	if( strlen($object->location->description) ){
		$location_view = HC_Html_Factory::widget('list')
			;
		$location_view->add_child( $object->present_location() );
		$location_view->add_child(
			HC_Html_Factory::element('span')
				->add_child($object->location->present_description())
				->add_style('font-size', -1)
				->add_style('font-style', 'italic')
			);
	}
	else {
		$location_view = $object->present_location();
	}

	$display_form->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Location') )
			->set_content( $location_view )
		->set_content_static()
		);
}

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Date') )
		->set_content(
			$object->present_date()
			)
		->set_content_static()
	);

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Time') )
		->set_content( $object->present_time() )
		->set_content_static()
	);

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Staff') )
		->set_content(
			$object->present_user()
			)
		->set_content_static()
	);

if( $object->status == $object->_const('STATUS_DRAFT') ){
	$display_form->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Status') )
			->set_content(
				$object->present_status()
				)
		->set_content_static()
		);
}

echo $display_form->render();
?>