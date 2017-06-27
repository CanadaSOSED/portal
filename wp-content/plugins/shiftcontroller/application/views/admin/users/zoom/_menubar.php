<?php
$link_to = 'admin/users/zoom/index/id/' . $object->id;

$menubar = HC_Html_Factory::element('div')
	->add_children_style('padding', 1)
	->add_children_style('display', 'block')
	;

/* EDIT */
$menubar->add_child(
	'edit',
	HC_Html_Factory::widget('titled', 'a')
		// ->add_style('btn')
		->add_attr('href', HC_Lib::link($link_to))
		->add_child( HC_Html::icon('edit') . HCM::__('Overview') )
	);

/* PASSWORD */
$menubar->add_child(
	'password',
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', HC_Lib::link($link_to, array('tab' => 'password')))
		->add_child( HC_Html::icon('lock') . HCM::__('Password') )
	);

/* EXTENDED TABS */
$extensions = HC_App::extensions();
$subextensions = $extensions->run('admin/users/zoom/menubar', $object);

foreach( $subextensions as $subtab => $subtitle ){
	if( $subtitle ){
		if( is_object($subtitle) ){
			$menubar->add_child(
				$subtab,
				HC_Html_Factory::element('div')
					->add_child( $subtitle ) 
				);
		}
		else {
			$menubar->add_child(
				$subtab,
				HC_Html_Factory::element('a')
					->add_attr('href', HC_Lib::link($link_to, array('tab' => $subtab)))
					->add_child( $subtitle )
				);
		}
	}
	else {
		$menubar->remove_child( $subtab );
	}
}

$menubar->add_child_style($tab, 'btn-primary');
echo $menubar->render();
?>