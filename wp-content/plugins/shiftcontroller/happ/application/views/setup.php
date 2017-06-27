<?php
$this->layout->set_partial(
	'header', 
	HC_Html::page_header(
		HC_Html_Factory::element('h2')
			->add_child( $page_title )
		)
	);

$out = HC_Html_Factory::widget('list')
	->add_children_style('margin', 'b1')
	;

if( $offer_upgrade ){
	$link = HC_Lib::link('setup/upgrade');
	$out->add_child(
		HC_Html_Factory::element('a')
			->add_attr('href', $link->url())
			->add_child('You seem to have an older version already installed. Please click here to upgrade.')
		);
	$out->add_child(
		'Or continue below to install from scratch.'
		);
}

$link = HC_Lib::link('setup/run');
$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', $link->url() )
	;

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_content(
			HC_Html_Factory::element('h4')
				->add_child( HCM::__('Admin') )
			)
		->set_content_static()
	);

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('First Name') )
		->set_content( 
			$form->input('first_name')
				->add_attr('size', 24)
			)
		->set_error( $form->input('first_name')->error() )
	);

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Last Name') )
		->set_content( 
			$form->input('last_name')
				->add_attr('size', 24)
			)
		->set_error( $form->input('last_name')->error() )
	);

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Email') )
		->set_content( 
			$form->input('email')
				->add_attr('size', 48)
			)
		->set_error( $form->input('email')->error() )
	);

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Password') )
		->set_content( 
			$form->input('password')
				->add_attr('size', 24)
			)
		->set_error( $form->input('password')->error() )
	);

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Confirm Password') )
		->set_content( 
			$form->input('confirm_password')
				->add_attr('size', 24)
			)
		->set_error( $form->input('confirm_password')->error() )
	);

$buttons = HC_Html_Factory::widget('list')
	->add_style('submit-bar')
	;
$buttons->add_child(
	HC_Html_Factory::element('input')
		->add_attr('type', 'submit')
		->add_attr('title', 'Proceed To Setup' )
		->add_attr('value', 'Proceed To Setup' )
		->add_style('btn-primary')
	);
$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_content( $buttons )
	);

$out->add_child( $display_form );
echo $out->render();
?>