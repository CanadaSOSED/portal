<?php
if( ! count($entries) ){
	return;
}

$list = HC_Html_Factory::widget('list')
	->add_children_style('margin', 'b1')
	// ->add_children_style('border', 'top')
	->add_children_style('padding', 1)
	;

foreach( $entries as $e ){
	$item_view = HC_Html_Factory::widget('list')
		->add_children_style('margin', 'b1')
		;
	$item_view->add_child( $e->present_type() );
	$item_view->add_child( $e->present_details() );

	$list->add_child( $item_view );
}

echo $list->render();
?>