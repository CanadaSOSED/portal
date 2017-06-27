<?php
if( ! $count ){
	return;
}

$temp_shift = HC_App::model('shift');
$linkto = HC_Lib::link('list-toff/index', 
	array(
		'filter'	=> 'pending-timeoffs',
		)
	);

$title = HC_Html_Factory::widget('list')
	->add_children_style('inline')
	->add_children_style('margin')
	->add_child( HC_Html::icon( HC_App::icon_for('timeoff')) )
	->add_child('title',
		HC_Html_Factory::widget('titled', 'a')
			->add_attr('href', $linkto)
			->add_style('btn')
			->add_child( HCM::__('Pending Timeoff Requests') )
		)
	->add_child('count', 
		HC_Html_Factory::element('span')
			->add_child( $count )
			->add_style('label')
		)
	;

$out = HC_Html_Factory::element('div')
	->add_child( $title )
	->add_style('box')
	;

$color = '#eee';
$color1 = HC_Lib::adjust_color_brightness( $color, 0 );
$color2 = HC_Lib::adjust_color_brightness( $color, 20 );

$out->add_attr('style',
	"background: repeating-linear-gradient(
		-45deg,
		$color1,
		$color1 6px,
		$color2 6px,
		$color2 12px
		);
	"
	);

echo $out->render();