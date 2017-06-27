<?php
$t = HC_Lib::time();

$acl = HC_App::acl();
$current_user_id = $acl->user() ? $acl->user()->id : 0;
$real_current_user_id = $this->auth->check();

$test_shift = HC_App::model('shift');

$is_print = (isset($state['print']) && $state['print']) ? 1 : 0;
$can_add = TRUE;

if( $is_print ){
	$can_add = FALSE;
}
else {
	if( ! $current_user_id ){
		$can_add = FALSE;
	}
	if( $real_current_user_id ){
		$can_add = TRUE;
	}
}

if( $can_add ){
	$acl2 = clone $acl;
	$auth_user = $this->auth->user();

	if( ! 
		$acl2
			->set_user( $auth_user )
			->set_object( 
				$test_shift
					->set('type',		$test_shift->_const('TYPE_SHIFT'))
					->set('user_id',	$real_current_user_id)
					)
			->can('add')
		){
		$can_add = FALSE;
	}
}

$t->setDateDb( $state['date'] );
$dates = $t->getDates( $state['range'] );
if( ! $dates ){
	$dates = array( $state['date'] );
}

/* date labels */
$DATE_LABELS = array();
reset( $dates );
foreach( $dates as $date ){
	$t->setDateDb( $date );
	$date_label = HC_Html_Factory::element('h4')
		->add_style('text-align', 'center')
		->add_child(
			HC_Html_Factory::widget('list')
				->add_style('nowrap')
				->add_child( $t->formatWeekdayShort() )
				->add_child(
					HC_Html_Factory::element('small')
						->add_child( $t->formatDate() )
				)
		)
		;
	$DATE_LABELS[ $date ] = $date_label;
}

/* titles */
$TITLES = array();
reset( $locations );
foreach( $locations as $location ){
	$entity_title = $location->present_title();
	$entity_title = HC_Html_Factory::element('h4')
		->add_child( $entity_title )
		->add_style('margin', 0)
		->add_style('padding', 0)
		;
	$TITLES[$location->id] = $entity_title;
}

/* compile the cells content */
$CELLS = array();
$QUICKHEADER = array();
$LINKS = array();

$has_shifts = array();
reset( $locations );
foreach( $locations as $location ){
	$entity_id = $location->id;

	$entity_shifts = array();
	reset($shifts);
	foreach( $shifts as $sh ){
		if( $sh->location_id != $entity_id ){
			continue;
		}
		$has_shifts[ $entity_id ] = 1;
		$entity_shifts[] = $sh;
	}

	$this_state = $state;
	$this_state['location'] = array($entity_id);

	/* header view */
	$quickheader_view = HC_Html_Factory::widget('module')
		->set_url( $rootlink . '/quickheader' )
		->pass_arg( array($entity_shifts, $this_state) )
		->set_params( $this_state )
		->set_param( 'location', $entity_id )
		->set_show_empty( TRUE )
		->add_attr('class', 'hcj-rfr-' . 'loc-' . $entity_id)
		;
	foreach( $this_state as $k => $v ){
		if( $v OR ($v === 0) ){
			$quickheader_view->set_param( $k, $v );
		}
	}
	$QUICKHEADER[$entity_id] = $quickheader_view;

	$this_date = $dates[0];
	$t->setDateDb( $this_date );

	$this_shifts = array();
	reset($entity_shifts);
	foreach( $entity_shifts as $sh ){
		$this_shifts[] = $sh;
	}

	$date_content = NULL;
	$this_state['range'] = 'day';
	$this_state['date'] = $this_date;

	$date_content = HC_Html_Factory::widget('module')
		->set_url( $rootlink . '/daygrid' )
		->pass_arg( array($this_shifts, $this_state) )
		->set_param( 'date', $this_date )
		->set_param( 'location', $entity_id )
		->add_attr('class', 'hcj-rfr-' . 'dat-' . $this_date . '-loc-' . $entity_id)
		;

	$cell_key = $entity_id . '_' . $this_date;
	$CELLS[$cell_key] = $date_content;

/* links */
	$LINKS[$cell_key] = NULL;
	if( $can_add ){
		$btns = HC_Html_Factory::widget('titled', 'a')
			->add_attr('href', 
				HC_Lib::link('shifts/add/index')
					->url(
						array(
							'date'		=> $this_date,
							'location'	=> $entity_id,
							'type'		=> $test_shift->_const('TYPE_SHIFT'),
							)
						)
					)
			->add_attr('class', 'hcj-flatmodal-loader')

			->add_style('btn')
			->add_style('padding', 'y1')
			->add_style('display', 'block')
			->add_style('border')
			->add_style('rounded')
			->add_style('text-align', 'center')

			->add_child( HC_Html::icon('plus') )
			->add_child( HCM::__('Add') )
			;
		$LINKS[$cell_key] = $btns;
	}
}

/* now display */
$out = HC_Html_Factory::widget('schedule_calendar')
	;
$out->set_dates( $dates );

$rid = 0;
reset( $locations );
foreach( $locations as $location ){
	$entity_id = $location->id;

	if( ! $current_user_id ){
		if( ! ( isset($has_shifts[$entity_id]) && $has_shifts[$entity_id] ) ){
			continue;
		}
	}

	if( $current_user_id ){
		$title = HC_Html_Factory::widget('list')
			->add_children_style('margin', 'b1')
			;
		$title->add_child( $TITLES[$entity_id] );
		$title->add_child( $QUICKHEADER[$entity_id] );
	}
	else {
		$title = $TITLES[$entity_id];
	}

	$out->set_title( $rid, $title );

	reset( $dates );
	foreach( $dates as $date ){
		$cell_key = $entity_id . '_' . $date;

		$cell_content = array(
			$CELLS[$cell_key],
			);

		if( $LINKS[$cell_key] ){
			$links = HC_Html_Factory::element('div')
				->add_attr('class', 'hc-hover-visible')
				->add_child($LINKS[$cell_key])
				;
			$cell_content[] = $links;
		}
		$out->set_cell( $rid, $date, $cell_content );
	}
	$rid++;
}
echo $out->render();
?>