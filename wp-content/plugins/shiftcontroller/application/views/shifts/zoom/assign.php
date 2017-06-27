<?php
$extensions = HC_App::extensions();

$out = HC_Html_Factory::widget('tiles')
	->set_per_row(3)
	;

if( ! $free_staff ){
	$out = HC_Html_Factory::element('span')
		->add_style('box')
		->add_style('border-color', 'red')
		->add_attr('title', HCM::__('No staff available for this shift') )
		->add_child( 
			HC_Html::icon('exclamation') . HCM::__('No staff available for this shift')
			)
		;
}
else{
	$out = HC_Html_Factory::widget('list')
		->add_style('margin', 'b1')
		;

	$link = HC_Lib::link('shifts/update/index/' . $object->id);

	/* unassign */
	if( $object->user_id ){
		$out2 = HC_Html_Factory::widget('tiles')
			->set_per_row(2)
			;

		$wrap = HC_Html_Factory::element('div')
			->add_style('box')
			->add_style('nowrap')
			->add_style('border-color', 'orange')
			;

		$radio = clone $form->input('user');
		$wrap->add_child(
			$radio
				->add_option(0, HC_Html::icon('sign-out') . HCM::__('Release Shift'))
			);
		$out2->add_child( $wrap );

		$out->add_child($out2);
	}

	$link = HC_Lib::link('shifts/update/index/' . $object->id);

	$out2 = HC_Html_Factory::widget('tiles')
		->set_per_row(3)
		;

	foreach( $free_staff as $st ){
		$item = HC_Html_Factory::element('a')
			->add_attr('href', $link->url( array('user' => $st->id) ))
			->add_attr('title', HCM::__('Assign Staff') )
			->add_child( $st->present_title() )
			;
		$item->add_attr('class', 'hcj-action');

		$wrap = HC_Html_Factory::element('div')
			->add_style('box')
			->add_style('nowrap')
			;

		$radio = clone $form->input('user');
		$wrap->add_child(
			$radio
				->add_option($st->id, $st->present_title(HC_PRESENTER::VIEW_RAW))
			);

	/* EXTENSIONS */
		$object->user_id = $st->id;
		$more_content = $extensions->run('shifts/assign/quickview', $object);
		if( $more_content ){
			$more_wrap = HC_Html_Factory::widget('list')
				->add_children_style('margin', 'b1')
				->add_children_style('font-size', -1)
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

		$out2->add_child( $wrap );
	}

	$out->add_child( $out2 );
}

echo $out->render();
?>