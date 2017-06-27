<?php
$extensions['notifications_email/insert']['user_preferences'] = array(
	'user_preferences/save',
	'',
	array('notifications_email_skip')
	);

$extensions['notifications_email/insert/defaults'][] = array(
	'user_preferences/get',
	'',
	array('notifications_email_skip')
	);
?>