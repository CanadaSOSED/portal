<?php
$current_user_id = $this->auth->check();

$display_form = HC_Html_Factory::widget('container')
	;

$display_form
	->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('First Name') )
			->set_content( $object->present_first_name() )
			->set_content_static(1)
		)
	->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Last Name') )
			->set_content( $object->present_last_name() )
			->set_content_static(1)
		)
	->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Email') )
			->set_content( $object->present_email() )
			->set_content_static(1)
		)
	;

$app_conf = HC_App::app_conf();
if( $app_conf->get('login_with') == 'username' ){
	$display_form
		->add_child(
			HC_Html_Factory::widget('label_row')
				->set_label( HCM::__('Username') )
				->set_content( $object->present_username() )
				->set_content_static(1)
			)
		;
}

$display_form
	->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('User Level') )
			->set_content( $object->present_level() )
			->set_content_static(1)
		)
	;

echo $display_form->render();
?>