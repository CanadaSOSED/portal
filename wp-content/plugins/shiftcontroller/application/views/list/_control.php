<?php
$filter_ui = HC_App::filter_ui();

$link = HC_Lib::link( 'list/' . $tab, $state );
$rootlink_this = HC_Lib::link( $rootlink . '/' . $tab, $state );
$rootlink = HC_Lib::link( $rootlink, $state );
$current_user_id = $this->auth->user()->id;

/* FILTERS */
/* FILTER STAFF */
$filter1 = NULL;

if( ! $filter_ui->is_disabled('filter-staff') ){
	if( count($all_staffs) > 1 ){
		$filter1 = HC_Html_Factory::widget('filter');
		$filter1
			->set_link( $rootlink_this )
			->set_param_name( 'staff' )
			->set_title( HCM::__('Staff') )
			->set_panel('default')
			;
		foreach( $all_staffs as $oid => $obj ){
			$filter1->set_option( $oid, $obj->present_title() );
		}

		if( isset($state['staff']) ){
			if( ! (count($state['staff']) == 1 && ( ($state['staff'][0] == '_1') OR ($state['staff'][0] == -1))) ){
				$filter1->set_selected( $state['staff'] );
			}
		}
	}
}

/* FILTER LOCATIONS */
$filter2 = NULL;
if( ! $filter_ui->is_disabled('filter-location') ){
	if( count($all_locations) > 1 ){
		$filter2 = HC_Html_Factory::widget('filter');
		$filter2
			->set_link( $rootlink_this )
			->set_param_name( 'location' )
			->set_title( HCM::__('Location') )
			->set_panel('default')
			;
		foreach( $all_locations as $oid => $obj ){
			$attr = array(
				'style'	=> 'background-color: ' . $obj->present_color() . ';'
				);
			// $filter2->set_option( $oid, $obj->present_title(), $attr );
			$filter2->set_option( $oid, $obj->present_title() );
		}

		if( isset($state['location']) ){
			if( ! (count($state['location']) == 1 && ( ($state['location'][0] == '_1') OR ($state['location'][0] == -1))) ){
				$filter2->set_selected( $state['location'] );
			}
		}
	}
}

/* CUSTOM FILTER */
$filter3 = NULL;
if( array_key_exists('filter', $fix) && ($fix['filter'] === NULL) ){
	$filter3 = NULL;
}
elseif(1){
// else {
	$fixed_filter = array_key_exists('filter', $fix) ? $fix['filter'] : NULL;

	$filter3 = HC_Html_Factory::widget('filter');
	$filter3
		->set_allow_multiple( FALSE )
		->set_link( $rootlink_this )
		->set_param_name( 'filter' )
		->set_title( HCM::__('More Filters') )
		->set_panel('default')
		->set_readonly(TRUE)
		;
	$extensions = HC_App::extensions();

	$more_content = $extensions->run('list/filter', 'label');
	foreach( $more_content as $subtab => $subtitle ){
		if( $subtitle ){
			if( $fixed_filter && ($subtab != $fixed_filter) ){
				continue;
			}
			$filter3->set_option(
				$subtab,
				$subtitle
				);
		}
	}

	if( $fixed_filter ){
		$filter3->set_selected( $fixed_filter );
		$filter3->set_fixed( TRUE );
	}
	elseif( isset($state['filter']) ){
		$filter3->set_selected( $state['filter'] );
	}

	if( ! $filter3->options() ){
		$filter3 = NULL;
	}
}

/* FILTER STATUS */
$filter4 = NULL;
if( ! $filter_ui->is_disabled('filter-status') ){
	$can_use_this = FALSE;
	$auth_user = $this->auth->user();
	if( $auth_user->level >= $auth_user->_const('LEVEL_MANAGER') ){
		$can_use_this = TRUE;
	}

	if( $can_use_this ){
		$filter4 = HC_Html_Factory::widget('filter');
		$filter4
			->set_link( $rootlink_this )
			->set_param_name( 'status' )
			->set_title( HCM::__('Status') )
			->set_panel('default')
			;

		$shift = HC_App::model('shift');
		$options = array( $shift->_const('STATUS_DRAFT'), $shift->_const('STATUS_ACTIVE') );

		foreach( $options as $option ){
			$filter4->set_option( $option, $shift->set('status', $option)->present_status() );
		}

		if( isset($state['status']) ){
			if( ! in_array($state['status'], array(-1, '_1')) ){
				$filter4->set_selected( $state['status'] );
			}
		}
	}
}

/* FILTER STATUS */
$filter5 = NULL;
if( ! $filter_ui->is_disabled('filter-type') ){
	$can_use_this = FALSE;
	$auth_user = $this->auth->user();
	if( $auth_user->level >= $auth_user->_const('LEVEL_MANAGER') ){
		$can_use_this = TRUE;
	}

	if( $can_use_this ){
		$filter5 = HC_Html_Factory::widget('filter');
		$filter5
			->set_link( $rootlink_this )
			->set_param_name( 'type' )
			->set_title( HCM::__('Type') )
			->set_panel('default')
			;

		$shift = HC_App::model('shift');
		$options = array( $shift->_const('TYPE_SHIFT'), $shift->_const('TYPE_TIMEOFF') );

		foreach( $options as $option ){
			$filter5->set_option( $option, $shift->set('type', $option)->present_type() );
		}

		if( isset($state['type']) ){
			if( ! (count($state['type']) == 1 && ( ($state['type'][0] == '_1') OR ($state['type'][0] == -1))) ){
				$filter5->set_selected( $state['type'] );
			}
		}
	}
}

$show_filters = array();
if( $filter1 ){
	$show_filters[] = $filter1;
}
if( $filter2 ){
	$show_filters[] = $filter2;
}
/*
if( $filter3 ){
	$show_filters[] = $filter3;
}
*/

if( $filter4 ){
	$show_filters[] = $filter4;
}
if( $filter5 ){
	$show_filters[] = $filter5;
}

if( ! $show_filters ){
	// return;
}

$filter = HC_Html_Factory::widget('filter_group');
$filter
	->set_panel('default')
	->add_style('hidden', 'print')
	// ->add_style('border-color', 'orange')
	->set_title( 
		HC_Html_Factory::element('span')
			->add_child( HC_Html::icon('filter') )
			->add_attr('title', HCM::__('Filter'))
		)
	->set_subtitle( 
		HC_Html_Factory::element('span')
			->add_child( HCM::__('Filter') )
			->add_style('font-style', 'bold')
		)
	;

if( $filter1 ){
	$filter->add_filter( 'f1', $filter1 );
}
if( $filter2 ){
	$filter->add_filter( 'f2', $filter2 );
}
if( $filter4 ){
	$filter->add_filter( 'f4', $filter4 );
}
if( $filter5 ){
	$filter->add_filter( 'f5', $filter5 );
}

$filter_selected = $filter->selected();

$filter_view = '';
if( $filter3 ){
	$filter3->add_style('bg-color', 'silver');

	$filter_view = HC_Html_Factory::widget('list')
		->add_children_style('margin', 'b1')
		;

	if( $filter_selected ){
		$filter_view->add_child( $filter );
	}
	$filter_view->add_child( $filter3 );
}
else {
	if( $filter_selected ){
		$filter_view = $filter;
	}
}

/* DATE */
$date_nav = HC_Html_Factory::widget('date_nav');
$date_nav->set_link( $rootlink_this );
$date_nav->set_range( $state['range'] );
if( isset($state['date']) ){
	$date_nav->set_date( $state['date'] );
}
$date_nav->set_submit_to( $link->url(array('customdates' => 1)) );

$enabled_date_options = array('day', 'week', 'month');
if( $current_user_id ){
	$enabled_date_options[] = 'custom';
	$enabled_date_options[] = 'upcoming';
	$enabled_date_options[] = 'all';
}
$date_nav->set_enabled( $enabled_date_options );

if( $filter_ui->is_disabled('date-navigation') ){
	$date_nav = NULL;
}

/* BUTTONS */
$add_btns = $this->render(
	'list/_control_btns',
	array(
		'tab'		=> $tab,
		'rootlink'	=> $rootlink,
		'state'		=> $state,
		)
	);

$control_details_content = $this->render(
	'list/_control_details',
	array(
		'tab'			=> $tab,
		'enabled_tabs'	=> $enabled_tabs,
		'rootlink'		=> $rootlink,
		'state'			=> $state,
		)
	);

$control_details = HC_Html_Factory::widget('collapse')
	// ->set_title( HC_Html::icon('caret-down-double') )
	->set_title( HC_Html::icon('cog') )
	->set_content( $control_details_content )
	;

$control_details_trigger = $control_details->render_trigger()
	// ->add_style('bg-color', 'silver')
	// ->add_style('color', 'black')
	->add_style('text-align', 'center')
	->add_attr('title', HCM::__('Configure View'))

	->add_style('btn')
	// ->add_style('btn-submit')
	->add_style('border')
	->add_style('rounded')
	->add_style('font-size', -1)
	->add_style('padding', 1) 
	;

if( $show_filters ){
	if( ! $filter_selected ){
		$filter_trigger = $filter->render_trigger()
			->add_style('btn')
			// ->add_style('btn-submit')
			->add_style('border')
			->add_style('rounded')
			->add_style('font-size', -1)
			->add_style('padding', 1) 
			;
	}
	$filter_trigger = $filter->render_trigger()
		->add_style('btn')
		// ->add_style('btn-submit')
		->add_style('border')
		->add_style('rounded')
		->add_style('font-size', -1)
		->add_style('padding', 1) 
		;
}

$full_out = HC_Html_Factory::widget('list')
	->add_children_style('margin', 'b1')
	;

$controls = HC_Html_Factory::widget('list')
	->add_children_style('display', 'inline-block')
	->add_children_style('margin', 'r1')
	->add_style('hidden', 'print')
	;

if( $show_filters ){
	$controls
		->add_child( $filter_trigger )
		;
}

if( $control_details_content ){
	$controls
		->add_child( $control_details_trigger )
		;
}

// SAVE VIEW
if( $current_user_id ){
	$this_state = $state;
	$this_state['tab'] = $tab;

	$save_details = HC_Html_Factory::element('a')
		->add_attr('href', HC_Lib::link('list')->url('save-default', $this_state))
		->add_child( HC_Html::icon('check') )
		->add_attr( 'title', HCM::__('Save This View As Default') )

		->add_style('btn')
		// ->add_style('btn-submit')
		->add_style('border')
		->add_style('rounded')
		// ->add_style('btn-secondary')
		->add_style('font-size', -1)
		->add_style('padding', 1) 
		;

	$controls
		->add_child( $save_details )
		;
}

/* print view, download */
if( ! $filter_ui->is_disabled('print') ){
	$controls->add_child(
		HC_Html_Factory::element('a')
			->add_attr( 'href', ' ' )
			->add_child( HC_Html::icon('print') )
			->add_attr('target', '_blank')
			// ->add_attr('onclick', 'hc_print_page(); return false;')
			->add_attr('onclick', 'window.print(); return false;')
			->add_attr( 'title', HCM::__('Print') )

			->add_style('btn')
			// ->add_style('btn-submit')
			->add_style('border')
			->add_style('rounded')
			->add_style('font-size', -1)
			->add_style('padding', 1)
		);
}
if( ! $filter_ui->is_disabled('download') ){
	$controls->add_child(
		HC_Html_Factory::element('a')
			->add_attr( 'href', $rootlink->url('download', $state) )
			->add_child( HC_Html::icon('download') )
			->add_attr( 'title', HCM::__('Download') )

			->add_style('btn')
			// ->add_style('btn-submit')
			->add_style('border')
			->add_style('rounded')
			->add_style('font-size', -1)
			->add_style('padding', 1)
		);
}

$full_out
	->add_child( $controls )
	;

if( $control_details_content ){
	$full_out
		->add_child( 
			'control_details',
			$control_details->render_content()
				->add_style('border')
				->add_style('padding', 2)
				->add_style('rounded')
			)
		;
}

if( $show_filters ){
	$full_out
		->add_child( 
			'filter_content',
			$filter->render_content()
				->add_style('border')
				->add_style('padding', 2)
				->add_style('rounded')
			)
		;
}

if( $filter_selected ){
	$full_out
		->add_child( 
			$filter->render_selected()
				->add_style('border')
				->add_style('padding', 1)
				->add_style('rounded')
				->add_style('border-color', 'blue')
			)
		;
}
$full_out
	->add_child( $date_nav )
	;

if( $add_btns ){
	$full_out
		->add_child( $add_btns )
		;
}

echo $full_out->render();
?>