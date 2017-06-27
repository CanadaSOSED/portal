<?php
$menubar = HC_Html_Factory::widget('list')
	->add_children_style('display', 'inline-block')
	->add_children_style('margin', 'r1')
	;
$menubar->add_child(
	'add',
	HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', HC_Lib::link('admin/locations/add'))
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
		->add_children_attr('style', 'vertical-align: middle;')
		->add_child(
			HC_Html_Factory::element('h1')
				->add_child(
					HC_Html::icon(HC_App::icon_for('location')) . HCM::__('Locations')
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
	$wrap = HC_Html_Factory::widget('grid')
		->add_style('padding', 1)
		;

	$wrap->add_child(
		HC_Html_Factory::widget('list')
			->add_child(
				HC_Html_Factory::element('a')
					->add_attr('href', HC_Lib::link('admin/locations/edit/' . $e->id) )
					->add_child($e->present_title())
				)
			->add_child(
				HC_Html_Factory::element('span')
					->add_child('id: ' . $e->id)
					->add_style('mute')
					->add_style('font-size', -1)
				)
		, 3
		);

	$wrap->add_child(
		HC_Html_Factory::element('span')
			->add_style('font-style', 'italic')
			->add_child($e->present_description())
		, 7
		);

	$btns = HC_Html_Factory::widget('button_group')
		->add_style('border', 0)
		->add_children_style('padding', 1)
		->add_child(
			HC_Html_Factory::element('a')
				->add_child( HC_Html::icon('arrow-up') )
				->add_attr('href', HC_Lib::link('admin/locations/up/' . $e->id) )
				->add_attr('title', HCM::__('Move Up') )
				->add_style('btn')
			)
		->add_child(
			HC_Html_Factory::element('a')
				->add_child( HC_Html::icon('arrow-down') )
				->add_attr('href', HC_Lib::link('admin/locations/down/' . $e->id) )
				->add_attr('title', HCM::__('Move Down') )
				->add_style('btn')
			)
		;

	if( $entries->result_count() > 1 ){
		$btns->add_child(
			HC_Html_Factory::element('a')
				->add_child(
					HC_Html::icon('times')
						->add_style('color', 'maroon')
					)
				->add_attr('href', HC_Lib::link('admin/locations/delete/' . $e->id) )
				->add_attr('title', HCM::__('Delete'))
				->add_attr('class', array('hcj-confirm'))
				// ->add_style('closer')
			);
	}

	$wrap->add_child(
		'btns',
		$btns
		, 2
		);
	$wrap->add_child_style('btns', 'text-align', 'sm-right');

	$final_wrap = HC_Html_Factory::element('div')
		->add_style('border')
		->add_style('rounded')
		->add_style('padding', 0)
		->add_style('margin', 'b2')
		;

	$final_wrap->add_attr('style', 'background-color: ' . $e->present_color());
	$wrap->add_style('bg-lighten', 2);
	$final_wrap->add_child( $wrap );

	$view_entries[] = $final_wrap;
}

$listing = HC_Html_Factory::widget('list')
	->set_children( $view_entries )
	;

$out = HC_Html_Factory::widget('container');
$out->add_child( $listing );

echo $out->render();
?>