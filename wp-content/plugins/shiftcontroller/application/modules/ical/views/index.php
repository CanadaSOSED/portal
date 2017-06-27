<?php
$this->layout->set_partial(
	'header', 
	HC_Html::page_header(
		HC_Html_Factory::element('h2')
			->add_child(
				HCM::__('iCal Sync')
			)
		)
	);

$current_user_id = $current_user->id;
$token = $current_user->auth_token();

$link = HC_Lib::link();
$link->set_force_frontend();

if( $current_user->level >= $current_user->_const("LEVEL_MANAGER") ){
	$link_my_schedule = $link->url( array('ical/export', $token, $current_user_id), array() );
	$link_full_schedule = $link->url( array('ical/export', $token), array() );
}
else {
	$link_my_schedule = $link->url( array('ical/export', $token), array() );
}

$out = HC_Html_Factory::widget('list')
	->add_children_style('margin', 'b2')
	;

$out->add_child(
	HC_Html_Factory::element('h3')
		->add_child(
			HCM::__('Local iCalendar Applications')
		)
	);
$out->add_child(
	'help1',
	'Apple Calendar, Microsoft Outlook, Lotus Organizer ...'
	);
$out->add_child_style('help1', 'font-size', -1);
$out->add_child_style('help1', 'mute');

$out->add_child(
	HCM::__('Click one of these links to set it up')
	);

if( $current_user->level >= $current_user->_const("LEVEL_MANAGER") ){
	$this_link_myschedule = preg_replace( '/^https?\:/', 'webcal:', $link_my_schedule );
	$this_link_full_schedule = preg_replace( '/^https?\:/', 'webcal:', $link_full_schedule );

	$out->add_child(
		HC_Html_Factory::widget('list')
			->add_children_style('inline')
			->add_children_style('margin', 'r2')

			->add_child(
				HC_Html_Factory::widget('titled', 'a')
					->add_attr('href', $this_link_myschedule)
					->add_child( HCM::__('My Schedule') )
					->add_style('btn')
					->add_style('btn-submit')
				)
			->add_child(
				HC_Html_Factory::widget('titled', 'a')
					->add_attr('href', $this_link_full_schedule)
					->add_child( HCM::__('Full Schedule') )
					->add_style('btn')
					->add_style('btn-submit')
				)
		);
}
else {
	$this_link_myschedule = preg_replace( '/^https?\:/', 'webcal:', $link_my_schedule );

	$out->add_child(
		HC_Html_Factory::widget('titled', 'a')
			->add_attr('href', $this_link_myschedule)
			->add_child( HCM::__('My Schedule') )
			->add_style('btn')
			->add_style('btn-submit')
		);
}


$out->add_child(
	HC_Html_Factory::element('h3')
		->add_child(
			HCM::__('Google Calendar Or Similar Online Calendars')
		)
	);
$out->add_child(
	'help3',
	HCM::__('Inside your Google Calendar open the Other Calendars menu in the left sidebar and select the Add by URL option. Paste one of these links then click the Add Calendar button. This will automatically add your shifts to Google Calendar.')
	);

$out->add_child(
	'help4',
	HCM::__("Please note that shifts don't sync immediately. It could take up to 12 hours for your shifts to show up (according to Google support website).")
	);
$out->add_child_style('help4', 'border', '');
$out->add_child_style('help4', 'rounded');
$out->add_child_style('help4', 'padding', 2);
$out->add_child_style('help4', 'border-color', 'orange');

$out->add_child(
	HC_Html_Factory::widget('label_row')
		->set_label( HCM::__('Timezone') )
		->set_content( $timezone )
		->set_content_static(TRUE)
	);


// $this->config->set_item('base_url', '/wp/?hc=shiftcontroller&hca=');

if( $current_user->level >= $current_user->_const("LEVEL_MANAGER") ){
	$out->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('My Schedule') )
			->set_content(
				HC_Html_Factory::input('text')
					->set_value( $link_my_schedule )
					->add_attr('onclick', 'this.focus();this.select();')
					->add_attr('style', 'width: 100%;')
					->add_attr('readonly', 'readonly')
				)
		);
	$out->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('Full Schedule') )
			->set_content(
				HC_Html_Factory::input('text')
					->set_value( $link_full_schedule )
					->add_attr('onclick', 'this.focus();this.select();')
					->add_attr('style', 'width: 100%;')
					->add_attr('readonly', 'readonly')
				)
		);
}
else {
	$out->add_child(
		HC_Html_Factory::widget('label_row')
			->set_label( HCM::__('My Schedule') )
			->set_content(
				HC_Html_Factory::input('text')
					->set_value( $link_my_schedule )
					->add_attr('onclick', 'this.focus();this.select();')
					->add_attr('style', 'width: 100%;')
					->add_attr('readonly', 'readonly')
				)
		);
}

echo $out->render();