<?php
$out = HC_Html_Factory::widget('list')
	;

$out->add_child(
	$form->input('notifications_email_skip')
		->set_label( HCM::__('Skip Notification Email') )
	);

echo $out->render();
?>