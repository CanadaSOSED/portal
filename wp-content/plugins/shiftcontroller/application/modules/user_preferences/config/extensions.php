<?php
$extensions['shifts/insert']['user_preferences'] = array(
	'user_preferences/save',
	'shifts/insert',
	array('status')
	);

$extensions['shifts/insert/defaults'][] = array(
	'user_preferences/get',
	'shifts/insert',
	array('status')
	);
?>