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

/* compile the cells content */
$CELLS = array();
$LINKS = array();

$this_date = $state['date'];
$this_state = $state;
$this_state['range'] = 'day';

$date_content = NULL;
$total_count = count($shifts);
if( $total_count ){
	$date_content = HC_Html_Factory::widget('module')
		->set_url( $rootlink . '/daygrid' )
		->pass_arg( array($shifts, $this_state) )
		->set_params( $this_state )
		->set_param( 'date', $this_date )
		->add_attr('class', 'hcj-rfr-' . 'dat-' . $this_date)
		;
}

/* header view */
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

$cell_content = array();
if( $date_content ){
	$cell_content[] = $date_content;
}

if( $can_add ){
	$btns = HC_Html_Factory::widget('titled', 'a')
		->add_attr('href', 
			HC_Lib::link('shifts/add/index')
				->url(
					array(
						'date'		=> $this_date,
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

	$links = HC_Html_Factory::element('div')
		->add_attr('class', 'hc-hover-visible')
		->add_child($btns)
		;
	$cell_content[] = $links;
}

if( $cell_content ){
	$out = HC_Html_Factory::widget('schedule_calendar')
		;
	$out->set_dates( $dates );
	$rid = 0;
	$out->set_cell( $rid, $dates[0], $cell_content );
}
else {
	$out = HCM::__('Nothing');
}

/* now display */
$full_out = HC_Html_Factory::widget('list')
	->add_style('margin', 'b1')
	->add_children_style('margin', 'b2')
	->add_attr('class', 'hcj-ajax-parent')
	;

$full_out->add_child( $QUICKHEADER );
$full_out->add_child(
	HC_Html_Factory::element('div')
		->add_attr('class', 'hcj-ajax-container')
	);
$full_out->add_child( $out );
// $full_out->add_child( $date_content );

echo $full_out->render();
?>