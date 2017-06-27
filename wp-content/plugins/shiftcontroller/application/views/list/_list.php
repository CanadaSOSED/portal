<?php
$t = HC_Lib::time();

if( $shifts ){
	$shift_view = HC_Html_Factory::widget('shift_view');
	$shift_view->set_wide( $state['wide'] );

	if( array_key_exists('form', $state) ){
	}
	else {
		$form = NULL;
	}

	$iknow = array();
	if( isset($state['range']) && ($state['range'] == 'day') ){
		$iknow[] = 'date';
	}

	if(
		( ! isset($state['staff']) ) OR 
		( count($state['staff']) == 1 ) OR
		( count($staffs) == 1 ) OR
		( count($all_staffs) == 1 )
		){
		$iknow[] = 'user';
	}

	if( 
		// ( ! isset($state['location']) ) OR 
		( isset($state['location']) && (count($state['location']) == 1) ) OR
		( count($locations) == 1 ) OR
		( count($all_locations) == 1 )
		){
		$iknow[] = 'location';
	}

	$fold_by = 'time';
	if( in_array('user', $iknow) ){
		$fold_by = '';
	}
	if( isset($state['wide']) && ($state['wide'] === 'mini') ){
		$fold_by = '';
	}
	if( ! in_array('date', $iknow) ){
		$fold_by = 'date';
	}

	$folded = array();
	$folded_labels = array();

	switch( $fold_by ){
		case 'time':
			$iknow[] = 'time';
			break;
		case 'location':
			$iknow[] = 'location';
			break;
		case 'date':
			$iknow[] = 'date';
			break;
	}

	$shift_view->set_iknow($iknow);

	/* folded */
	if( $fold_by ){
		foreach( $shifts as $sh ){
			if( (isset($state['location']) && $state['location']) && ( (! isset($state['staff'])) OR (! $state['staff']) ) ){
				if( ! in_array($sh->location_id, $state['location']) ){
					continue;
				}
			}

			$folding_key = array();

			switch( $fold_by ){
				case 'time':
					$folding_key[] = $sh->start . '-' . $sh->end;
					break;
				case 'location':
					$folding_key[] = $sh->location_id;
					break;
				case 'date':
					$folding_key[] = $sh->date;
					break;
			}

			$folding_key = join( '-', $folding_key );
			if( ! isset($folded[$folding_key]) ){
				$folded[$folding_key] = array();
				$folded_labels[$folding_key] = array();

				switch( $fold_by ){
					case 'time':
						$folded_labels[$folding_key][] = $sh->present_time(HC_PRESENTER::VIEW_RAW, FALSE, FALSE);
						break;
					case 'location':
						$folded_labels[$folding_key][] = $sh->present_location();
						break;
					case 'date':
						$folded_labels[$folding_key][] = $sh->present_date();
						break;
				}
			}
			$folded[$folding_key][] = $sh;
		}
	}
}

$out = HC_Html_Factory::widget('list')
	;

if( $shifts ){
	$out_shifts = HC_Html_Factory::widget('list')
		->add_children_style('margin', 'b1')
		;

	if( $fold_by ){
		foreach( $folded as $fk => $fshifts ){
		// label
			$this_label = $folded_labels[$fk][0];

		// shifts
			$list_shifts = HC_Html_Factory::widget('list')
				->add_children_style('margin', 't1')
				;
			reset( $fshifts );
			foreach( $fshifts as $sh ){
				$shift_view->set_shift( $sh );

				if( $form ){
					$this_view = HC_Html_Factory::widget('table')
						;

					$this_view->set_cell( 0, 0, 
						$form->input('id')
							// ->add_option( $sh->id )
							// ->render_one( $sh->id, FALSE )
							->set_my_value( $sh->id )
							->add_attr('class', 'hcj-collect-me')
							->add_style('hidden', 'print')
							->render()
						);
					$this_view->set_cell( 0, 1, 
						$shift_view->render()
						);

					$this_view->add_cell_attr(0, 0,
						array('style' => 'width: 1em; padding: 0;')
						);
					$this_view->add_cell_attr(0, 1,
						array('style' => 'padding: 0;')
						);
				}
				else {
					$this_view = $shift_view->render();
				}

				$list_shifts->add_child(
					// $shift_view->render()
					$this_view
					);
			}

			$folder = HC_Html_Factory::widget('collapse')
				->set_default_in(TRUE)
				->add_style('margin', 'b2')
				;

			$toggler = HC_Html_Factory::element('a')
				->add_child( $this_label )

				->add_style('nowrap')
				->add_style('btn')
				->add_style('display', 'block')
				->add_style('padding', 1)
				->add_style('rounded')
				->add_style('color', 'white')
				->add_style('bg-color', 'gray')
				;

			$list_shifts
				->add_style('margin', 'l2')
				;

			$folder
				->set_title( $toggler )
				->set_content( $list_shifts )
				;

			$out_shifts->add_child( $folder );
		}
	}
	else{
		foreach( $shifts as $sh ){
			if( (isset($state['location']) && $state['location']) && (! (isset($state['staff']) && $state['staff']) ) ){
				if( ! in_array($sh->location_id, $state['location']) ){
					continue;
				}
			}

			$shift_view->set_shift($sh);

			if( $form ){
				$this_view = HC_Html_Factory::widget('table')
					;

				$this_view->set_cell( 0, 0, 
					$form->input('id')
						// ->add_option( $sh->id )
						// ->render_one( $sh->id, FALSE )
						->set_my_value( $sh->id )
						->render()
					);

				$this_view->set_cell( 0, 1, 
					$shift_view->render()
					);

				$this_view->add_cell_attr(0, 0, array('style' => 'width: 1em;'));
			}
			else {
				$this_view = $shift_view->render();
			}

			$out_shifts->add_child(
				// $shift_view->render()
				$this_view
				);
		}
	}

	$out->add_child( $out_shifts );
}

/* extensions */
$extensions = HC_App::extensions();

$more_content = array();
if( (isset($state['range']) && ($state['range'] == 'day')) ){
	$more_content = $extensions->run(
		'list/day',
		'state', $state
		);
}

if( $more_content ){
	$out = HC_Html_Factory::widget('list')
		->add_children_style('margin', 'b1')
		->add_child( $out )
		;
	foreach( $more_content as $subkey => $subvalue ){
		if( $subvalue ){
			$out->add_child( $subvalue );
		}
	}
}

echo $out->render();
?>