<?php
$this->layout->set_partial(
	'header', 
	HC_Html::page_header(
		HC_Html_Factory::element('h2')
			->add_child( $app_title . ': Installation' )
			->add_child( '<br>' )
			->add_child( 
				HC_Html_Factory::element('small')
					->add_child( HCM::__('Import User Accounts') )
				)
		)
	);

$out = HC_Html_Factory::widget('list')
	->add_style('margin', 'b1')
	->add_children_style('margin', 'b1')
	;

if( isset($offer_upgrade) && $offer_upgrade ){
	$link = HC_Lib::link('setup/upgrade');
	$out->add_child(
		HC_Html_Factory::element('a')
			->add_attr('href', $link->url())
			->add_child('You seem to have an older version already installed. Click here to upgrade.')
		);
	$out->add_child(
		'continue',
		'Or continue below to install from scratch.'
		);
	$out->add_child_attr(
		'continue',
		'style', 'margin-bottom: 1em;'
		);
}

$help = HC_Html_Factory::element('div')
	->add_style('box')
	->add_style('border-color', 'olive')
	;
$help->add_child(
	'You need to run this action only if you want to change WordPress Role to ' . $app_title . ' User Level relation configuration. ' . 
	'Normally user accounts in ' . $app_title . ' are automatically syncronized with WordPress as soon as they get added, updated or deleted in WordPress.'
	);
if( ! $is_setup ){
	$out->add_child( $help );
}

$link = HC_Lib::link($post_to);

$display_form = HC_Html_Factory::widget('form')
	->add_attr('action', $link->url() )
	;

$table = HC_Html_Factory::widget('table')
	->add_style('table', 'border')
	;
$table->set_header(
	array(
		'WordPress',
		$app_title
		)
	);

foreach( $wordpress_roles as $role_value => $role_name ){
	$this_role_count = ( isset($wordpress_count_users['avail_roles'][$role_value]) ) ? $wordpress_count_users['avail_roles'][$role_value] : 0;
	$row = array();
	$row[] = $role_name . ' [' . $this_role_count . ']';

	if( $role_value ){
		if( $role_value == 'administrator' ){
			$default = USER_HC_MODEL::LEVEL_ADMIN;
		}
		else {
			if( $this_role_count )
				$default = USER_HC_MODEL::LEVEL_STAFF;
			else
				$default = 0;
		}
	}
	else {
		$default = 0;
	}

	$field_name = 'role_' . $role_value;
	$options = array(
		USER_HC_MODEL::LEVEL_ADMIN		=> HCM::__('Admin'),
		USER_HC_MODEL::LEVEL_MANAGER	=> HCM::__('Manager'),
		USER_HC_MODEL::LEVEL_STAFF		=> HCM::__('Staff'),
		0								=> HCM::__('Do Not Import'),
		);

	$row[] = $form->input($field_name)
		->set_options($options)
		->set_default($default)
		;
	$table->add_row($row);
}

/*
$row = array();
$row[] = '';
$row[] = $form->input('append_role_name') . 'Append Original Role Name To Staff Name (Like "Subscriber John Doe")';
$table->add_row($row);
*/

$buttons = HC_Html_Factory::widget('list')
	->add_children_style('inline')
	->add_children_style('margin', 'r1')
	;

$btn_label = $is_setup ? HCM::__('Import User Accounts') : HCM::__('Sync Users');
$buttons->add_child(
	HC_Html_Factory::element('input')
		->add_attr('type', 'submit')
		->add_attr('title', $btn_label )
		->add_attr('value', $btn_label )
		->add_style('btn-primary')
	);

$row = array();
$row[] = '';
$row[] = $buttons;
$table->add_row($row);

$display_form->add_child( $table );

$out->add_child( $display_form );
echo $out->render();
?>