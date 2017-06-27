<?php
$link_to = 'admin/users/index';

$menubar = HC_Html_Factory::element('div')
	->add_children_style('padding')
	->add_children_style('display', 'block')
	;

/* LIST */
$menubar->add_child(
	'list',
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', HC_Lib::link($link_to))
		->add_child( HCM::__('All Users') )
	);

/* ADD */
$menubar->add_child(
	'add',
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', HC_Lib::link($link_to . '/add'))
		->add_child( HCM::__('Add New User') )
	);

/* EXTENDED TABS */
$extensions = HC_App::extensions();
$subextensions = $extensions->run('admin/users/index/menubar', $object);

foreach( $subextensions as $subtab => $subtitle ){
	if( $subtitle ){
		$menubar->add_child(
			$subtab,
			HC_Html_Factory::element('a')
				->add_attr('href', HC_Lib::link($link_to . '/' . $subtab))
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