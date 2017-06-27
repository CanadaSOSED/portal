<?php
$link_to = 'shifts/zoom/index/id/' . $object->id;

$menubar = HC_Html_Factory::element('div')
	->add_children_style('padding', 1)
	// ->add_children_style('btn')
	->add_children_style('display', 'block')
	;

$overview_text = HCM::__('Overview');

/* OVERVIEW */
$menubar->add_child(
	'overview',
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', HC_Lib::link($link_to))
		->add_attr('title', $overview_text)
		->add_child( HC_Html::icon('info') . $overview_text )
	);

/* EXTENDED TABS */
$extensions = HC_App::extensions();
$more_content = $extensions->run(
	'shifts/zoom/menubar',
	$object
	);
foreach( $more_content as $subtab => $subtitle ){
	if( $subtitle ){
		$menubar->add_child(
			$subtab,
			HC_Html_Factory::widget('titled', 'a')
				->add_attr('href', HC_Lib::link($link_to, array('tab' => $subtab)))
				->add_child( $subtitle )
			);
	}
}

$items = $menubar->children();
if( count($items) < 2 ){
	return;
}

$menubar->add_child_style($tab, 'btn-primary');
echo $menubar->render();
?>