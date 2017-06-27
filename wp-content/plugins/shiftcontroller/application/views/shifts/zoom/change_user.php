<?php
$extensions = HC_App::extensions();

$out = HC_Html_Factory::widget('list')
	->add_children_style('border', 'bottom')
	;

if( ! $options ){
	$out = HC_Html_Factory::element('span')
		->add_style('box')
		->add_style('border-color', 'red')
		->add_attr('title', HCM::__('No staff available for this shift') )
		->add_child(
			HC_Html::icon('exclamation') . HCM::__('No staff available for this shift')
			)
		;
}
else {
	$link = HC_Lib::link('shifts/zoom/index/' . $object->id . '/overview');

	/* unassign */
	if( $skip ){
		$link = HC_Lib::link('shifts/zoom/form/' . $object->id . '/user/' . 0);

		$out->add_child(
			HC_Html_Factory::element('a')
				->add_attr('href', $link->url())
				->add_child( HC_Html::icon('sign-out') )
				->add_child( HCM::__('Release Shift') )
				->add_attr('class', 'hcj-flatmodal-closer')
				->add_attr('class', 'hcj-flatmodal-return-loader')
				->add_style('btn')
				->add_style('padding', 'y2')
			);
	}

	foreach( $options as $option ){
		$wrap = HC_Html_Factory::widget('container');

		$link = HC_Lib::link('shifts/zoom/form/' . $object->id . '/user/' . $option->id);

		$item = HC_Html_Factory::element('a')
			->add_attr('href', $link->url())
			->add_attr('title', HCM::__('Assign Staff') )
			->add_child( $option->present_title() )
			->add_attr('class', 'hcj-flatmodal-return-loader')
			->add_style('btn')
			->add_style('padding', 'y2')
			;

		$item->add_attr('class', 'hcj-action');
		$wrap->add_child( $item );

	/* EXTENSIONS */
		$object->user_id = $option->id;
		$more_content = $extensions->run(
			'shifts/assign/quickview',
			$object
			);
		if( $more_content ){
			$more_wrap = HC_Html_Factory::widget('list')
				->add_children_style('margin', 'b2')
				;
			$added = 0;
			foreach($more_content as $mck => $mc ){
				$more_wrap->add_child($mc);
				$added++;
			}
			if( $added ){
				$wrap->add_child($more_wrap);
			}
		}

		$out->add_child($wrap);
	}
}

echo $out->render();
?>