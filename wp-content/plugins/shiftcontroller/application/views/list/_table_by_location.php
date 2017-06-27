<?php
$current_user_id = $this->auth->user()->id;

$list = HC_Html_Factory::widget('list')
	->add_style('margin', 'b1')
	;

$total_count = 0;

reset( $locations );
foreach( $locations as $location ){
	$this_list = HC_Html_Factory::widget('list')
		->add_style('margin', 'b3')
		->add_attr('class', 'hcj-collector-wrap')
		->add_attr('class', 'hcj-ajax-parent')
		;

	$entity_id = $location->id ? $location->id : 0;

	$this_shifts = array();
	reset( $shifts );
	foreach( $shifts as $sh ){
		$total_count++;
		if( $sh->location_id != $location->id ){
			continue;
		}
		$this_shifts[] = $sh;
	}

	if( ! $this_shifts ){
		continue;
	}

	$this_list->add_child( 
		HC_Html_Factory::element('h2')
			->add_child( $location->present_title() )
		);

	$this_state = $state;
	$this_state['wide'] = 1;
	$this_state['location'] = array($entity_id);
	$this_state['form'] = TRUE;

	/* stats view */
	if( $current_user_id ){
		/* header view */
		$this_state['wideheader'] = 1;
		$quickheader_view = HC_Html_Factory::widget('module')
			->set_url( $rootlink . '/quickheader' )
			->pass_arg( array($this_shifts, $this_state) )
			->set_param( 'location', $entity_id )
			->set_show_empty( TRUE )
			->add_attr('class', 'hcj-rfr-' . 'loc-' . $entity_id)
			;
		foreach( $this_state as $k => $v ){
			if( $v OR ($v === 0) ){
				$quickheader_view->set_param( $k, $v );
			}
		}
		$this_list->add_child( $quickheader_view );
	}

	$content = HC_Html_Factory::widget('module')
		->set_url( $rootlink . '/listing' )
		->pass_arg( array($this_shifts, $this_state) )
		// ->set_param( 'date', $this_date )
		->set_param( 'location', $entity_id )
		->add_attr('class', 'hcj-rfr-' . 'loc-' . $entity_id)
		;
	foreach( $this_state as $k => $v ){
		if( $v OR ($v === 0) ){
			$content->set_param( $k, $v );
		}
	}

	$this_list->add_child(
		HC_Html_Factory::element('div')
			->add_attr('class', 'hcj-ajax-container')
		);
	$this_list->add_child( $content );

	$list->add_child( $this_list );
}

if( ! $total_count ){
	$list->add_child( HCM::__('Nothing') );
}

echo $list->render();
