<?php
$menubar = HC_Html_Factory::widget('list')
	->add_children_style('display', 'inline-block')
	->add_children_style('margin', 'r1')
	;
$menubar->add_child(
	'add',
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', HC_Lib::link('admin/shift_templates/add'))
		->add_child(
			HC_Html::icon('plus')
			)
		->add_child( HCM::__('Add') )
		->add_style('btn-success')
	);

$header = HC_Html_Factory::widget('list')
	->add_children_style('display', 'inline-block')
	->add_children_style('margin', 'r1')
	;

$header->add_child(
	HC_Html_Factory::widget('list')
		->add_children_style('display', 'inline-block')
		->add_children_style('margin', 'r1')
		->add_child(
			HC_Html_Factory::element('h1')
				->add_child(
					HC_Html::icon(HC_App::icon_for('time')) . HCM::__('Shift Templates')
					)
			)
		->add_child( $menubar )
	);

$this->layout->set_partial(
	'header', 
	HC_Html::page_header( $header )
	);

$t = HC_Lib::time();
$view_entries = array();

foreach( $entries as $e ){
	$wrap = HC_Html_Factory::element('div')
		->add_style('border')
		->add_style('rounded')
		->add_style('padding', 2)
		->add_style('margin', 'r3', 'b3')
		;

	$wrap
		->add_child(
			HC_Html_Factory::element('a')
				->add_child(
					HC_Html::icon('times')
						->add_style('color', 'maroon')
					)
				->add_attr('href', HC_Lib::link('admin/shift_templates/delete/' . $e->id) )
				->add_attr('title', HCM::__('Delete'))
				->add_attr('class', array('hcj-confirm'))
				->add_style('closer')
			)
		->add_child(
			HC_Html_Factory::widget('list')
				->add_children_style('margin', 'b1')
				->add_child(
					HC_Html_Factory::element('a')
						->add_child( $e->name )
						->add_attr('href', HC_Lib::link('admin/shift_templates/edit/' . $e->id) )
					)
				->add_child(
					$e->present_time()
					)
			);

	$view_entries[] = $wrap;
}

$tiles = HC_Html_Factory::widget('tiles')
	->set_per_row(3)
	;
$tiles->set_children( $view_entries );

$out = HC_Html_Factory::widget('container');
$out->add_child( $tiles );

echo $out->render();
?>