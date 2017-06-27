<?php
$out = HC_Html_Factory::widget('container');

$out->add_child( $form->input('location') );

$view = HC_Html_Factory::element('div')
	->add_child(
		HC_Html_Factory::element('a')
			->add_attr('class', 'hcj-flatmodal-loader' )
			->add_attr('href', HC_Lib::link('shifts/zoom/change/' . $object->id . '/location/' . $form->input('location')->value()) )

			->add_child( $object->present_location() )
			->prepend_child(
				HC_Html::icon('caret-down')
					->add_style('right')
					->add_style('padding', 1)
				)
			->add_style('btn')
			->add_style('display', 'block')
			->add_style('padding', 0)
		)
	->add_style('box')
	;

$out->add_child( $view );
echo $out->render();
