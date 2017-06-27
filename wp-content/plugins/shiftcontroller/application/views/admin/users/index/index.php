<?php
if( $layout->has_partial('header') ){
	echo $layout->partial('header');
}
if( $layout->has_partial('menubar') && $layout->has_partial('content') ){
	$sublayout = HC_Html_Factory::widget('grid')
		->set_scale('md')
		;
	$sublayout->add_child(
		$layout->partial('content'),
		9
		);
	$sublayout->add_child(
		$layout->partial('menubar'),
		3
		);
	echo $sublayout->render();
}
else {
	if( $layout->has_partial('content') ){
		echo $layout->partial('content');
	}
}
?>