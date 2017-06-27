<?php
$this->layout->set_partial(
	'header', 
	HC_Html::page_header(
		HC_Html_Factory::element('h2')
			->add_child( HCM::__('Add New User') )
		)
	);

$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', HC_Lib::link('admin/users/add/insert') )
	;

/* BUTTONS */
$buttons = HC_Html_Factory::widget('list')
	->add_children_style('margin', 'r1')
	->add_children_style('inline')
	;
$buttons->add_child(
	HC_Html_Factory::element('input')
		->add_attr('type', 'submit')
		->add_attr('title', HCM::__('Add New User') )
		->add_attr('value', HCM::__('Add New User') )
		->add_style('btn-primary')
	);

$display_form
	->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('First Name') )
			->set_content( 
				$form->input('first_name')
					->add_attr('size', 24)
				)
			->set_error( $form->input('first_name')->error() )
		)
	->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Last Name') )
			->set_content( 
				$form->input('last_name')
					->add_attr('size', 24)
				)
			->set_error( $form->input('last_name')->error() )
		)
	->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Email') )
			->set_content( 
				$form->input('email')
					->add_attr('size', 32)
				)
			->set_error( $form->input('email')->error() )
		)
	;

$app_conf = HC_App::app_conf();
if( $app_conf->get('login_with') == 'username' ){
	$display_form
		->add_child(
			HC_Html_Factory::widget('label_row')
				->set_label( HCM::__('Username') )
				->set_content( 
					$form->input('username')
						->add_attr('size', 24)
					)
				->set_error( $form->input('username')->error() )
			)
		;
}

$input_level = $form->input('level')
	->set_options(
		array(
			USER_HC_MODEL::LEVEL_STAFF		=> HCM::__('Staff'),
			USER_HC_MODEL::LEVEL_MANAGER	=> HCM::__('Manager'),
			USER_HC_MODEL::LEVEL_ADMIN		=> HCM::__('Admin'),
			)
		)
	;

/* can't edit remotely integrated accounts */
$ri = HC_Lib::ri();
if( $ri ){
	$input_level->add_attr('disabled', 'disabled');
}

$display_form
	->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('User Level') )
			->set_content( $input_level )
			->set_error( $input_level->error() )
		)
	;

$display_form
	->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Password') )
			->set_content( 
				$form->input('password')
					->add_attr('size', 24)
				)
			->set_error( $form->input('password')->error() )
		)
	->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Confirm Password') )
			->set_content( 
				$form->input('confirm_password')
					->add_attr('size', 24)
				)
			->set_error( $form->input('confirm_password')->error() )
		)
	;

$display_form
	->add_child(
		HC_Html_Factory::widget('label_row')
			->set_content( $buttons )
		)
	;

echo $display_form->render();
?>