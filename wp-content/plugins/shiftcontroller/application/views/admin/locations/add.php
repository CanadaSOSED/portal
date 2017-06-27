<?php
$this->layout->set_partial(
	'header', 
	HC_Html::page_header(
		HC_Html_Factory::element('h1')
			->add_child( HCM::__('Add New Location') )
		)
	);

$link = HC_Lib::link('admin/locations/insert');
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
		->set_label( HCM::__('Description') )
		->set_content(
			$form->input('description')
				->add_attr('cols', 40)
				->add_attr('rows', 3)
			)
		->set_error( $form->input('description')->error() )
	);

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Color') )
		->set_content( 
			$form->input('color')
			)
		->set_error( $form->input('color')->error() )
	);

$buttons = HC_Html_Factory::widget('list')
	->add_style('submit-bar')
	;
$buttons->add_child(
	HC_Html_Factory::element('input')
		->add_attr('type', 'submit')
		->add_attr('title', HCM::__('Add New Location') )
		->add_attr('value', HCM::__('Add New Location') )
		->add_style('btn-primary')
	);

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_content( $buttons )
	);

$out = HC_Html_Factory::widget('container');
$out->add_child( $display_form );

echo $out->render();
?>