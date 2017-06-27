<?php
$link_to = 'auth/profile/index';

$menubar = HC_Html_Factory::element('div')
	->add_children_style('padding')
	->add_children_style('btn')
	->add_children_style('display', 'block')
	;

/* PROFILE */
$menubar->add_child(
	'edit',
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', HC_Lib::link($link_to))
		->add_attr('title', HCM::__('Edit My Profile'))
		->add_child( HC_Html::icon('user') . HCM::__('Edit My Profile') )
	);

/* PASSWORD */
$ri = HC_Lib::ri();
if( $ri ){
	$password_url = Modules::run( $ri . '/auth/password_url' );
}
else {
	$password_url = HC_Lib::link($link_to, array('tab' => 'password'));
}

$menubar->add_child(
	'password',
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', $password_url)
		->add_child( HC_Html::icon('lock') . HCM::__('Change Password') )
	);

/* EXTENDED TABS */
$extensions = HC_App::extensions();
$subextensions = $extensions->run('auth/profile/menubar', $object);

foreach( $subextensions as $subtab => $subtitle ){
	if( $subtitle ){
		$menubar->add_child(
			$subtab,
			HC_Html_Factory::element('a')
				->add_attr('href', HC_Lib::link($link_to, array('tab' => $subtab)))
				->add_child( $subtitle )
			);
	}
	else {
		$menubar->remove_child( $subtab );
	}
}

$menubar->add_child_style($tab, 'btn-primary');
echo $menubar->render();
?>