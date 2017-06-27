<?php
$my_own = FALSE;
if( ! $wrap ){
	$my_own = TRUE;
	$wrap = HC_Html_Factory::widget('list')
		->add_children_style('inline')
		->add_children_style('margin', 'r1', 'b1')
		;
}

if( $count_fail ){
	$title = sprintf( HCM::_n('%d Conflict', '%d Conflicts', $count_fail), $count_fail );

	$item = HC_Html_Factory::element('span')
		->add_child( HC_Html::icon(HC_App::icon_for('conflict')) )
		->add_child( $count_fail )
		->add_attr('title', $title)
		// ->add_style('badge')
		->add_style('box', 1, 0)
		->add_style('bg-color', 'red')
		->add_style('color', 'white')
		;

	$wrap->add_child( $item );
}

if( $my_own ){
	if( $wrap->children() ){
		echo $wrap->render();
	}
}
?>