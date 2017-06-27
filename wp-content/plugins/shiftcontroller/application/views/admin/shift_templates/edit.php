<?php
$this->layout->set_partial(
	'header', 
	HC_Html::page_header(
		HC_Html_Factory::element('h1')
			->add_child(HCM::__('Shift Template'))
		)
	);

$link = HC_Lib::link('admin/shift_templates/update/' . $id);
$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', $link->url() )
	;

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Name') )
		->set_content( 
			$form->input('name')
				->add_attr('size', 32)
			)
		->set_error( $form->input('name')->error() )
	);

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Time') )
		->set_content( 
			$form->input('time')
			)
		->set_error( $form->input('time')->error() )
	);

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Break') )
		->set_content( 
			$form->input('lunch_break')
			)
		->set_error( $form->input('lunch_break')->error() )
	);

$buttons = HC_Html_Factory::widget('list')
	->add_children_style('inline')
	->add_children_style('margin', 'r2')
	;
$buttons->add_child(
	HC_Html_Factory::element('input')
		->add_attr('type', 'submit')
		->add_attr('title', HCM::__('Save') )
		->add_attr('value', HCM::__('Save') )
		->add_style('btn-primary')
	);
$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_content( $buttons )
	);

echo $display_form->render();
?>