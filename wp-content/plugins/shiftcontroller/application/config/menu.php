<?php defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& ci_get_instance();
$config = array();

$config = array(
	USER_HC_MODEL::LEVEL_ADMIN . '/calendar' =>
		array(
			'title'	=> HCM::__('Full Schedule'),
			'icon'	=> 'calendar',
			'link'	=> 'list',
			),
	USER_HC_MODEL::LEVEL_ADMIN . '/calendarme' =>
		array(
			'title'	=> HCM::__('My Schedule'),
			'icon'	=> 'calendar',
			'link'	=> 'listme',
			),
	USER_HC_MODEL::LEVEL_ADMIN . '/timeoffs' =>
		array(
			'title'	=> HCM::__('Timeoff Requests'),
			'icon'	=> HC_Html::icon(HC_App::icon_for('timeoff')),
			'link'	=> 'list-toff',
			),
	USER_HC_MODEL::LEVEL_ADMIN . '/todo' =>
		array(
			'title'	=> HCM::__('Todo'),
			'icon'	=> 'todo',
			'link'	=> 'admin/todo',
			),
	USER_HC_MODEL::LEVEL_ADMIN . '/conf' => 
		array(
			'title'	=> HCM::__('Configuration'),
			'icon'	=> 'cog',
			'link'	=> '',
			'order'	=> 100,
			),
		USER_HC_MODEL::LEVEL_ADMIN . '/conf/users' => array(
			'title'	=> HCM::__('Users'),
			'icon'	=> 'user',
			'link'	=> 'admin/users',
			),
		USER_HC_MODEL::LEVEL_ADMIN . '/conf/locations'	=> array( 
			'title'	=> HCM::__('Locations'),
			'icon'	=> 'home',
			'link'	=> 'admin/locations',
			),
		USER_HC_MODEL::LEVEL_ADMIN . '/conf/templates'	=> array( 
			'title'	=> HCM::__('Shift Templates'),
			'icon'	=> 'clock',
			'link'	=> 'admin/shift-templates',
			),
		USER_HC_MODEL::LEVEL_ADMIN . '/conf/settings'	=> array( 
			'title'	=> HCM::__('Settings'),
			'icon'	=> 'cog',
			'link'	=> 'conf/admin',
			'order'	=> 100
			),
	);

$config[ USER_HC_MODEL::LEVEL_MANAGER . '/calendar' ] = array(
	'title'	=> HCM::__('Full Schedule'),
	'icon'	=> 'calendar',
	'link'	=> 'list',
	);
$config[ USER_HC_MODEL::LEVEL_MANAGER . '/calendarme' ] = array(
	'title'	=> HCM::__('My Schedule'),
	'icon'	=> 'calendar-o',
	'link'	=> 'listme',
	);
$config[ USER_HC_MODEL::LEVEL_MANAGER . '/timeoffs' ] = array(
	'title'	=> HCM::__('Timeoff Requests'),
	'icon'	=> HC_Html::icon(HC_App::icon_for('timeoff')),
	'link'	=> 'list-toff',
	);
	
$config[ USER_HC_MODEL::LEVEL_STAFF . '/calendarme' ] = array(
	'title'	=> HCM::__('My Schedule'),
	'icon'	=> 'calendar',
	'link'	=> 'listme',
	);
$config[ USER_HC_MODEL::LEVEL_STAFF . '/timeoffs' ] = array(
	'title'	=> HCM::__('Timeoff Requests'),
	'icon'	=> HC_Html::icon(HC_App::icon_for('timeoff')),
	'link'	=> 'list-toff',
	);

$app_conf = HC_App::app_conf();
$wall_schedule_display = $app_conf->get('wall:schedule_display');

if( $wall_schedule_display <= USER_HC_MODEL::LEVEL_STAFF ){
	$config[ USER_HC_MODEL::LEVEL_STAFF . '/calendar' ] = array(
		'title'	=> HCM::__('Full Schedule'),
		'icon'	=> 'calendar',
		'link'	=> 'list',
		);
}

$promo_url = $CI->config->item( 'nts_promo_url' );
if( $promo_url ){
	$promo_title = $CI->config->item( 'nts_promo_title' );
	$config[USER_HC_MODEL::LEVEL_ADMIN . '/promo'] = array(
		'title'	=> $promo_title,
		'link'	=> $promo_url,
		'external'	=> TRUE,
		'order'	=> 200
		);
}

/* End of file menu.php */
/* Location: ./application/config/menu.php */