<?php
$this->layout->set_partial(
	'header', 
	HC_Html::page_header(
		HC_Html_Factory::element('h2')
			->add_child( HCM::__('Lost your password?') )
		)
	);

$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', HC_Lib::link('auth/forgot_password')->url() )
	;

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Email') )
		->set_content( 
			$form->input('email')
				->add_attr('placeholder', HCM::__('Email'))
			)
		->set_error( $form->input('email')->error() )
	);

$display_form->add_child(
	HC_Html_Factory::widget('label_row')
		->set_content( 
			HC_Html_Factory::element('input')
				->add_attr('type', 'submit')
				->add_attr('title', HCM::__('Get New Password') )
				->add_attr('value', HCM::__('Get New Password') )
				->add_style('btn-primary')
			)
	);

$out = HC_Html_Factory::widget('list')
	;

$out->add_child( $display_form );

echo $out->render();
?>