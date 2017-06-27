<?php
$t = HC_Lib::time();

$acl = HC_App::acl();
$current_user_id = $acl->user() ? $acl->user()->id : 0;
$real_current_user_id = $this->auth->check();
$auth_user = $this->auth->user();

/* TEMPLATES */
$_template = array();
require( dirname(__FILE__) . '/_calendar_templates.php' );
/* END OF TEMPLATES */

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

/* date labels */
$DATE_LABELS = array();
reset( $dates );
foreach( $dates as $date ){
	$t->setDateDb( $date );
	$date_label = str_replace(
		array(
			'{FORMAT_WEEKDAY_SHORT}',
			'{FORMAT_DATE}'
			),
		array(
			$t->formatWeekdayShort(),
			$t->formatDate()
			),
		$_template['date_label']
		);
	$DATE_LABELS[ $date ] = $date_label;
}

/* titles */
$TITLES = array();
reset( $locations );
foreach( $locations as $location ){
	$entity_title = $location->present_title();
	$entity_title = str_replace(
		array(
			'{ENTITY_TITLE}'
			),
		array(
			$entity_title
			),
		$_template['entity_title']
		);
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

	reset( $dates );
	foreach( $dates as $this_date ){
		$t->setDateDb( $this_date );

		$this_shifts = array();
		reset($entity_shifts);
		foreach( $entity_shifts as $sh ){
			if( $sh->date > $this_date ){
				break;
			}
			if( $sh->date < $this_date ){
				continue;
			}
			$this_shifts[] = $sh;
		}

		$date_content = NULL;
		$this_state['range'] = 'day';
		$this_state['date'] = $this_date;

		$date_content = HC_Html_Factory::widget('module')
			->set_url( $rootlink . '/day' )
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
			$btns = str_replace(
				array(
					'{HREF}'
					),
				array(
					HC_Lib::link('shifts/add/index')
						->url(
							array(
								'date'		=> $this_date,
								'location'	=> $entity_id,
								'type'		=> $test_shift->_const('TYPE_SHIFT'),
								)
							)
					),
				$_template['btns']
				);

			$LINKS[$cell_key] = $btns;
		}
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

	$title = str_replace(
		array(
			'{TITLE}',
			'{QUICKHEADER}'
			),
		array(
			$TITLES[$entity_id],
			$QUICKHEADER[$entity_id]
			),
		$_template['title']
		);

	$out->set_title( $rid, $title );

	reset( $dates );
	foreach( $dates as $date ){
		$cell_key = $entity_id . '_' . $date;

		$cell_content = array(
			$CELLS[$cell_key],
			);

		if( $LINKS[$cell_key] ){
			$links = str_replace(
				array(
					'{LINK}',
					),
				array(
					$LINKS[$cell_key],
					),
				$_template['links']
				);
			$cell_content[] = $links;
		}
		$out->set_cell( $rid, $date, $cell_content );
	}
	$rid++;
}
echo $out->render();
?>