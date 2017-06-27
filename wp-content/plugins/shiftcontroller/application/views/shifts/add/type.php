<?php
$list = HC_Html_Factory::widget('tiles')
	->set_per_row(4)
	;

$link = HC_Lib::link('shifts/add/index');
$shift = HC_App::model('shift');

foreach( $types as $type ){
	$add_params = $params->to_array();
	$add_params['type'] = $type;

	$title_title = $shift->set('type', $type)->present_type(HC_PRESENTER::VIEW_RAW);
	$title_label = $shift->set('type', $type)->present_label(HC_PRESENTER::VIEW_HTML) . $title_title;
	$title_class = $shift->set('type', $type)->set('status', $shift->_const('STATUS_ACTIVE'))->present_status_class();

	$item = HC_Html_Factory::element('a')
		->add_attr('href', $link->url($add_params))
		->add_attr('title', $title_title )
		->add_child( $title_label )
		;

	foreach( $title_class as $tc ){
		$item->add_attr( 'class', 'alert-' . $tc );
	}

	$item
		->add_attr('class', 'hcj-action')
		->add_style('box')
		;

	$list->add_child( $item );
}

echo $list->render();
?>