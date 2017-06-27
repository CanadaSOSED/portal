<?php
$out = HC_Html_Factory::widget('list')
	;

/* ok */
if( $count_ok ){
	$item = HC_Html_Factory::element('span')
		->add_style('badge')
		->add_style('bg-color', 'olive')
		->add_style('color', 'white')
		;

	$icon = HC_Html::icon('check-circle')
		;

	$item->add_child( $icon );
	$title = ($count_fail + $count_ok) > 1 ? $count_ok : HCM::__('OK');
	$item->add_child( $title );

	$item->add_attr('title', HCM::__('OK'));

	if( ! $count_fail ){
		$out->add_child( $item );
	}
}

$t = HC_Lib::time();

/* failed */
if( $count_fail ){
	$title = sprintf( HCM::_n('%d Conflict', '%d Conflicts', $count_fail), $count_fail );

	if( $wrap ){
	/* red border for wrap */
		$wrap
			->add_style('border')
			->add_style('border-color', 'red')
			// ->add_attr('style', 'border-width: 2px;')
			->add_attr('style', 'position: relative;')
			;

		$icon = HC_Html::icon(HC_App::icon_for('conflict'))
			->add_attr('title', $title)
			->add_attr('style', 'position: absolute; right: .125em; top: .125em;')

			->add_style('bg-color', 'red')
			->add_style('color', 'white')
			// ->add_style('color', 'red')
			// ->add_style('bg-color', 'white')

			->add_style('rounded')
			// ->add_style('border')
			// ->add_style('border-color', 'red')
			;
		$wrap->prepend_child( $icon );

// $input = '<input type="checkbox" style="position: absolute; left: 0; top: 0;">';
// $wrap->prepend_child( $input );

	}
	else {
		/* link to detailed conflicts view */
		$item = HC_Html_Factory::element('span')
			->add_style('badge')
			->add_style('bg-color', 'red')
			->add_style('color', 'white')
			;

		$icon = HC_Html::icon(HC_App::icon_for('conflict'));
		$item->add_child( $icon );
		$item->add_child( $title );

		$item
			->add_child( ' ' )
			->add_child(
				HC_Html::icon('caret-down')
				)
			;

		$conflict_list = HC_Html_Factory::widget('list')
			->add_style('margin', 'b1')
			;

		foreach( $conflicts as $date => $date_conflicts ){
			if( ! $date_conflicts ){
				continue;
			}
			$t->setDateDb( $date );
			$conflict_list->add_child( 
				HC_Html_Factory::element('strong')
					->add_child( $t->formatDateFull() )
				);

			foreach( $date_conflicts as $e ){
				$item_view = HC_Html_Factory::widget('list')
					->add_children_style('margin', 'b1')
					;
				$item_view->add_child( $e->present_type() );
				$item_view->add_child( $e->present_details() );
			}

			$conflict_list->add_child( $item_view );
		}

		$conflicts_view = HC_Html_Factory::widget('collapse')
			->set_title( $item )
			->set_content( $conflict_list )
			// ->set_panel( array('danger', 'condensed') )
			;

		$out->add_child( $conflicts_view );
		echo $out->render();
	}
}
?>