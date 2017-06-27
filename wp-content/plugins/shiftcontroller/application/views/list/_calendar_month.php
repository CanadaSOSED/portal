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
$t->setStartMonth();
$month_matrix = $t->getMonthMatrix();

$t->setDateDb( $state['date'] );
$dates = $t->getDates( $state['range'] );

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

/* compile the cells content */
$CELLS = array();
$LINKS = array();

reset( $dates );
foreach( $dates as $this_date ){
	$t->setDateDb( $this_date );

	$this_shifts = array();
	reset($shifts);
	foreach( $shifts as $sh ){
		if( $sh->date > $this_date ){
			break;
		}
		if( $sh->date < $this_date ){
			continue;
		}
		$this_shifts[] = $sh;
	}

	$this_state = $state;
	$date_content = NULL;
	$this_state['range'] = 'day';
	$this_state['date'] = $this_date;
	// $this_state['wide'] = 'mini';

	$date_content = HC_Html_Factory::widget('module')
		->set_url( $rootlink . '/day' )
		->pass_arg( array($this_shifts, $this_state) )
		->set_params( $this_state )
		->set_param( 'date', $this_date )
		// ->set_param( 'wide', 'mini' )
		->add_attr('class', 'hcj-rfr-' . 'dat-' . $this_date)
		;

	$CELLS[$this_date] = $date_content;

/* links */
	$LINKS[$this_date] = NULL;
	if( $can_add ){
		$href_details = array(
			'date'	=> $this_date,
			'type'	=> $test_shift->_const('TYPE_SHIFT'),
			);
		if( isset($state['staff']) && $state['staff'] && (count($state['staff']) == 1) ){
			$href_details['user'] = $state['staff'][0];
		}
		if( isset($state['location']) && $state['location'] && (count($state['location']) == 1) ){
			$href_details['location'] = $state['location'][0];
		}

		$btns = str_replace(
			array(
				'{HREF}'
				),
			array(
				HC_Lib::link('shifts/add/index')
					->url( $href_details )
				),
			$_template['btns']
			);

		$LINKS[$this_date] = $btns;
	}
}

/* stats view */
$this_state['wideheader'] = 1;
$quickheader_view = HC_Html_Factory::widget('module')
	->set_url( $rootlink . '/quickheader' )
	->pass_arg( array($shifts, $this_state) )
	->set_show_empty( TRUE )
	->add_attr('class', 'hcj-rfr')
	;
foreach( $this_state as $k => $v ){
	if( $v OR ($v === 0) ){
		$quickheader_view->set_param( $k, $v );
	}
}
$QUICKHEADER = $quickheader_view;

/* now display */
$out = HC_Html_Factory::widget('schedule_calendar')
	;
$out->set_dates( $dates );

$rid = 0;

reset( $dates );
foreach( $dates as $date ){
	$cell_key = $date;

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

$full_out = HC_Html_Factory::widget('list')
	->add_style('margin', 'b1')
	->add_attr('class', 'hcj-ajax-parent')
	;

$full_out->add_child( $QUICKHEADER );
$full_out->add_child(
	HC_Html_Factory::element('div')
		->add_attr('class', 'hcj-ajax-container')
	);
$full_out->add_child( $out );

echo $full_out->render();
?>