<?php
$out = HC_Html_Factory::widget('list')
	->add_children_style('border', 'bottom')
	;

foreach( $options as $option ){
	$wrap = HC_Html_Factory::widget('container');

	$link = HC_Lib::link('shifts/zoom/form/' . $object->id . '/location/' . $option->id);

	$item = HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', $link->url())
		->add_child( $option->present_title() )
		->add_attr('class', 'hcj-flatmodal-return-loader')
		->add_style('btn')
		->add_style('padding', 'y2')
		;

	$wrap->add_child( $item );
	$out->add_child( $wrap );
}

echo $out->render();
?>