<?php
$config['date_format'] = array(
	'default' 	=> 'j M Y',
	'label'		=> HCM::__('Date Format'),
	'type'		=> 'dropdown',
	'options'	=> array(
		'd/m/Y'	=> date('d/m/Y'),
		'd-m-Y'	=> date('d-m-Y'),
		'n/j/Y'	=> date('n/j/Y'),
		'Y/m/d'	=> date('Y/m/d'),
		'd.m.Y'	=> date('d.m.Y'),
		'j M Y'	=> date('j M Y')
		),
	);

$config['time_format'] = array(
	'default' 	=> 'g:ia',
	'label'		=> HCM::__('Time Format'),
	'type'		=> 'dropdown',
	'options'	=> array(
		'g:ia'	=> date('g:ia'),
		'g:i A'	=> date('g:i A'),
		'H:i'	=> date('H:i'),
		),
	);

$config['week_starts'] = array(
	'default' 	=> 0,
	'label'		=> HCM::__('Week Starts On'),
	'type'		=> 'dropdown',
	'options'	=> array(
		0	=> HCM::__('Sun'),
		1	=> HCM::__('Mon'),
		2	=> HCM::__('Tue'),
		3	=> HCM::__('Wed'),
		4	=> HCM::__('Thu'),
		5	=> HCM::__('Fri'),
		6	=> HCM::__('Sat'),
		),
	);

$config['disable_weekdays'] = array(
	'default' 	=> array(),
	'label'		=> HCM::__('Days Of Week Disabled'),
	'type'		=> 'checkbox_set',
	'options'	=> array(
		0	=> HCM::__('Sun'),
		1	=> HCM::__('Mon'),
		2	=> HCM::__('Tue'),
		3	=> HCM::__('Wed'),
		4	=> HCM::__('Thu'),
		5	=> HCM::__('Fri'),
		6	=> HCM::__('Sat'),
		),
	);

$config['time_min'] = array(
	'default' 	=> 0,
	'label'		=> HCM::__('Min Start Time'),
	'type'		=> 'time',
	);

$config['time_max'] = array(
	'default' 	=> 24 * 60 * 60,
	'label'		=> HCM::__('Max End Time'),
	'type'		=> 'time',
	);

$config['show_end_time'] = array(
	'default' 	=> 1,
	'label'		=> HCM::__('Show Shift End Time'),
	'type'		=> 'checkbox',
	);

$config['email_from'] = array(
	'default' 	=> '',
	'label'		=> HCM::__('Send Email Notifications From Address'),
	'type'		=> 'text',
	'rules'		=> 'trim|required|valid_email'
	);

$config['email_from_name'] = array(
	'default' 	=> '',
	'label'		=> HCM::__('Send Email Notifications From Name'),
	'type'		=> 'text',
	'rules'		=> 'trim|required'
	);

$config['disable_email'] = array(
	'default' 	=> 0,
	'label'		=> HCM::__('Disable Email'),
	'type'		=> 'checkbox',
	);

$config['bcc_email'] = array(
	'default' 	=> '',
	'label'		=> HCM::__('Send a Copy Of All Email Notifications To'),
	'help'		=> HCM::__('Separate by comma'),
	'type'		=> 'text',
	'rules'		=> 'trim',
	);

$config['csv_separator'] = array(
	'default' 	=> ',',
	'label'		=> HCM::__('Separator in csv files'),
	'type'		=> 'dropdown',
	'options'	=> array(
		','	=> ',',
		';'	=> ';',
		),
	);

$config['login_with'] = array(
	'default' 	=> 'email',
	'label'		=> HCM::__('Log In With'),
	'type'		=> 'dropdown',
	'options'	=> array(
		'email'		=> HCM::__('Email'),
		'username'	=> HCM::__('Username'),
		),
	);

/*
$config['calendar_ajax'] = array(
	'default' 	=> 1,
	'label'		=> 'Ajax in admin calendar',
	'type'		=> 'checkbox',
	);
*/

$config['timeoff:approval_required'] = array(
	'default' 	=> 1,
	'label'		=> HCM::__('Admin Approval Required'),
	'type'		=> 'checkbox',
	);

$ri = HC_Lib::ri();

if( $ri ){
	$config['wall:schedule_display'] = array(
		'default' 	=> USER_HC_MODEL::LEVEL_STAFF,
		'label'		=> HCM::__('Staff Can See Others Schedule'),
		'type'		=> 'dropdown',
		'options'	=> array(
			USER_HC_MODEL::LEVEL_STAFF		=> HCM::__('Yes'),
			USER_HC_MODEL::LEVEL_ADMIN		=> HCM::__('No'),
			),
		);

	$config['wall:show_open'] = array(
		'default' 	=> 0,
		'label'		=> HCM::__('Display Open Shifts'),
		'type'		=> 'checkbox',
		);
}
else {
	$config['wall:schedule_display'] = array(
		'default' 	=> USER_HC_MODEL::LEVEL_STAFF,
		'label'		=> HCM::__('Who Can See Full Schedule'),
		'type'		=> 'dropdown',
		'options'	=> array(
			0								=> HCM::__('Everyone'),
			USER_HC_MODEL::LEVEL_STAFF		=> HCM::__('Staff'),
			USER_HC_MODEL::LEVEL_ADMIN		=> HCM::__('Nobody'),
			),
		);

	$config['wall:show_open'] = array(
		'default' 	=> 0,
		'label'		=> HCM::__('Display Open Shifts'),
		'type'		=> 'checkbox',
		);
}

$config['staff:working_levels'] = array(
	'default' 	=> array(USER_HC_MODEL::LEVEL_STAFF, USER_HC_MODEL::LEVEL_MANAGER, USER_HC_MODEL::LEVEL_ADMIN),
	'label'		=> HCM::__('Who Can Work'),
	'type'		=> 'checkbox_set',
	'options'	=> array(
/* translators: User level */
		USER_HC_MODEL::LEVEL_STAFF		=> HCM::__('Staff'),
/* translators: User level */
		USER_HC_MODEL::LEVEL_MANAGER	=> HCM::__('Manager'),
/* translators: User level */
		USER_HC_MODEL::LEVEL_ADMIN		=> HCM::__('Admin'),
		),
	'rules'		=> 'required'
	);

$config['staff:shift_acl'] = array(
	'default' 	=> array('view_active'),
	'label'		=> HCM::__('Employees Can'),
	'type'		=> 'checkbox_set',
	'inline'	=> 0,
	'options'	=> array(
		'view_active'		=> HCM::__('View Active Shifts'),
		'view_draft'		=> HCM::__('View Draft Shifts'),
		'create_draft'		=> HCM::__('Create Draft Shifts'),
		'confirm_draft'		=> HCM::__('Confirm Draft Shifts'),
		'create_active'		=> HCM::__('Create Active Shifts'),
		),
	'dependencies'	=> array(
		'view_draft'		=> array('view_active'),
		'create_draft'		=> array('view_draft'),
		'confirm_draft'		=> array('view_active', 'view_draft'),
		'create_active'		=> array('confirm_draft', 'view_active', 'create_draft'),
		),
	);
