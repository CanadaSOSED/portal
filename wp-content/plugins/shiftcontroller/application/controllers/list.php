<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class List_HC_Controller extends _Front_HC_Controller
{
	protected $form = NULL;
	protected $views_path = 'list';
	protected $rootlink = 'list';
	protected $fix = array(
		'location'	=> array(),
		'staff'		=> array(),
		);

	function __construct()
	{
		parent::__construct();

		if( ($test_user = $this->auth->user()) && $test_user->id ){
		}
		else {
			$this->fix['filter'] = NULL;
		}

		$acl = HC_App::acl();
		if( $this->hc_modules->exists('shift_groups') ){
			$this->form = HC_Lib::form()
				->set_input( 'id', 'checkbox' )
				;
		}
	}

	function quickheader()
	{
		$args = func_get_args();
		/* supplied from command line */
		if( (count($args) >= 1) && is_array($args[0]) ){
			list( $shifts, $state ) = $args[0];
		}
		else {
			$state = $this->_grab_state( func_get_args(), 'browse' );
			$shifts = $this->_init_shifts( $state );
		}
		// return;
		$this->_display( $shifts, $state, 'browse', 'quickheader' );
		return;
	}

	function quickstats()
	{
		$args = func_get_args();
		/* supplied from command line */
		if( (count($args) >= 1) && is_array($args[0]) ){
			list( $shifts, $state ) = $args[0];
		}
		else {
			$state = $this->_grab_state( func_get_args(), 'browse' );
			$shifts = $this->_init_shifts( $state );
		}
		// return;
		$this->_display( $shifts, $state, 'browse', 'quickstats' );
		return;
	}

	function quickform()
	{
		$is_module = ( $this->input->is_ajax_request() OR $this->is_module() ) ? 1 : 0;

		$args = func_get_args();
		/* supplied from command line */
		if( (count($args) >= 1) && is_array($args[0]) ){
			list( $shifts, $state ) = $args[0];
		}
		else {
			$state = $this->_grab_state( func_get_args(), 'browse' );
			$shifts = $this->_init_shifts( $state );
		}

		$content = Modules::run('shift_groups/form',
			'shifts', $shifts, 'state', $state
			);

		if( $is_module ){
			echo $content;
		}
		else {
			$this->layout->set_partial(
				'content', 
				$content
				);
			$this->layout();
		}
	}

/* LIST SHIFTS */
	function index()
	{
		$tab = 'calendar';

		if( ($current_user = $this->auth->user()) && $current_user->id ){
			$model = HC_App::model('user_preferences');
			$saved_pref = $model->get( 'calendar_view' );
			if( isset($saved_pref['tab']) ){
				$tab = $saved_pref['tab'];
			}
		}

		if( isset($this->fix['tab']) ){
			$tab = $this->fix['tab'];
		}

		return $this->_index( func_get_args(), $tab );
	}

	public function save_default()
	{
		$args = func_get_args();

		$need = array('range', 'by', 'location', 'staff', 'tab', 'status', 'type');
		$state = $this->_grab_state( $args );

		foreach( $need as $n ){
			if( array_key_exists($n, $state) ){
				$save[$n] = $state[$n];
			}
		}

		if( isset($save['staff']) ){
			$save['staff'] = HC_Lib::remove_from_array( $save['staff'], -1 );
			$save['staff'] = HC_Lib::remove_from_array( $save['staff'], '_1' );
		}
		if( isset($save['location']) ){
			$save['location'] = HC_Lib::remove_from_array( $save['location'], -1 );
			$save['location'] = HC_Lib::remove_from_array( $save['location'], '_1' );
		}

		$model = HC_App::model('user_preferences');
		$model->set( 'calendar_view', $save );

		$msg = HCM::__('Settings updated');
		$this->session->set_flashdata( 'message', $msg );

		$referrer = ( ! isset($_SERVER['HTTP_REFERER']) OR $_SERVER['HTTP_REFERER'] == '') ? '' : trim($_SERVER['HTTP_REFERER']);
		$this->redirect( $referrer );
		return;
	}

	function browse()
	{
		return $this->_index( func_get_args(), 'browse' );
	}

	function report()
	{
		$this->enqueue_asset('js', 'happ/assets/js/underscore-min.js');
		$this->enqueue_asset('js', 'happ/assets/js/backbone-min.js');
		$this->enqueue_asset('js', 'happ/assets/js/jquery-ui-sortable.min.js');
		$this->enqueue_asset('js', 'happ/assets/js/hc/sorted-table.js');

		return $this->_index( func_get_args(), 'report' );
	}

	function calendar()
	{
		return $this->_index( func_get_args(), 'calendar' );
	}

	function _index( $args, $tab )
	{
		$state = $this->_grab_state( $args, $tab );

	/* do some cleanup */
		if( ! in_array($state['range'], array('week', 'month', 'day')) ){
			if( $tab == 'calendar' ){
				$tab = 'browse';
			}
		}

		// if( ($tab == 'calendar') && $state['by'] && ($state['range'] == 'month')){
			// $state['by'] = NULL;
		// }


		if( in_array($state['range'], array('day')) ){
			$state['include_yesterday'] = 1;
		}
		$shifts = $this->_init_shifts( $state );
		unset( $state['include_yesterday'] );

		switch( $tab ){
			case 'calendar':
				$view_file_prefix = 'calendar' . '_' . $state['range'];
				break;
			case 'browse':
				$view_file_prefix = 'table';
				break;
			case 'report':
				$view_file_prefix = 'report';
				break;
		}

		switch( $state['by'] ){
			case 'staff':
				$view_file = $view_file_prefix . '_by_staff';
				break;
			case 'location':
				$view_file = $view_file_prefix . '_by_location';
				break;
			default:
				$view_file = $view_file_prefix . '';
				break;
		}
		$this->_display( $shifts, $state, $tab, $view_file );
	}

/* DAY SHIFTS */
	function day()
	{
		$args = func_get_args();

		/* supplied from command line */
		if( (count($args) >= 1) && is_array($args[0]) ){
			list( $shifts, $state ) = $args[0];
			$state['range'] = 'day';
		}
		else {
			$state = $this->_grab_state( 
				func_get_args(),
				'browse',
				array(
					'range' => 'day'
					)
				);
			$shifts = $this->_init_shifts( $state );
		}
		$this->_display( $shifts, $state, 'browse', 'list' );
	}

	function daygrid()
	{
		$args = func_get_args();

		/* supplied from command line */
		if( (count($args) >= 1) && is_array($args[0]) ){
			list( $shifts, $state ) = $args[0];
			$state['range'] = 'day';
		}
		else {
			$state = $this->_grab_state( 
				func_get_args(),
				'browse',
				array(
					'range' => 'day'
					)
				);
			$state['include_yesterday'] = 1;
			$shifts = $this->_init_shifts( $state );
			unset( $state['include_yesterday'] );
		}
		$this->_display( $shifts, $state, 'browse', 'daygrid' );
	}

	function listing()
	{
		$args = func_get_args();

		/* supplied from command line */
		if( (count($args) >= 1) && is_array($args[0]) ){
			list( $shifts, $state ) = $args[0];
		}
		else {
			$state = $this->_grab_state( func_get_args(), 'browse' );
			$shifts = $this->_init_shifts( $state );
		}
		$this->_display( $shifts, $state, 'browse', 'list' );
	}

/* DOWNLOAD SHIFTS */
	function download()
	{
		$state = $this->_grab_state( func_get_args(), 'index' );
		$shifts = $this->_init_shifts( $state );
		return $this->_download( $shifts );
	}

/* SHIFTS OF A GROUP */
	function group( $model )
	{
		if( ! is_object($model) ){
			$id = $model;
			$model = HC_App::model('shift');
			$model->where('id', $id)->get();
		}
		$group_id = $model->group_id;

		$state = array();
		$shifts = array();
		if( $group_id > 0 ){
			$shifts = HC_App::model('shift');
			$shifts->where('group_id', $group_id); 
			$shifts->get();

			$acl = HC_App::acl();
			$shifts = $acl->filter( $shifts, 'view' );

			if( $shifts ){
				$state['range'] = 'custom';
				$start_date = $shifts[0]->date;
				$end_date = $shifts[ count($shifts)-1 ]->date;
				$state['date'] = $start_date . '_' . $end_date;
			}
		}

		$this->_display( $shifts, $state, 'browse', 'table' );
	}

	/* renders output */
	private function _prepare_display( $state )
	{
		static $params = array();

	/* load locations */
		if( ! isset($params['all_locations']) ){
			$params['all_locations'] = array();

			if( $this->fix['location'] ){
				if( 
					is_array($this->fix['location']) &&
					(count($this->fix['location']) == 1) &&
					($this->fix['location'][0] == 0)
					){
						/* don't show locations */
						$params['all_locations'] = array();
					}
				else {
					$model = HC_App::model('location');
					$model->where_in('id', $this->fix['location'] );
					$model->get();
					foreach( $model as $obj ){
						$params['all_locations'][ $obj->id ] = $obj;
					}
				}
			}
			else {
				$model = HC_App::model('location');
				$params['all_locations'] = $model->get_all();
			}
		}

	/* load users */
		if( ! isset($params['all_staffs']) ){
			$params['all_staffs'] = array();

			if( $this->fix['staff'] ){
				$model = HC_App::model('user');
				$model->where_in('id', $this->fix['staff'] );
				$model->get();
				foreach( $model as $obj ){
					$params['all_staffs'][ $obj->id ] = $obj;
				}
			}
			else {
				$nastaff = HC_App::model('user');
				$nastaff->id = 0;
				$params['all_staffs'][0] = $nastaff;

				$model = HC_App::model('user');
				$model->get_staff();
				foreach( $model as $obj ){
					$params['all_staffs'][ $obj->id ] = $obj;
				}
			}
		}

	/* filtered staffs */
		if( array_key_exists('staff', $state) && $state['staff'] ){
			if( (count($state['staff']) == 1) && ( ($state['staff'][0] == -1) OR ($state['staff'][0] == '_1') ) ){
				$params['staffs'] = $params['all_staffs'];
			}
			else {
				$params['staffs'] = array();
				foreach( $state['staff'] as $oid ){
					$params['staffs'][$oid] = $params['all_staffs'][$oid];
				}
			}
		}
		else {
			$params['staffs'] = $params['all_staffs'];
		}

	/* filtered locations */
		if( array_key_exists('location', $state) && $state['location'] ){
			if( (count($state['location']) == 1) && ( ($state['location'][0] == -1) OR ($state['location'][0] == '_1') ) ){
				$params['locations'] = $params['all_locations'];
			}
			else {
				$params['locations'] = array();
				foreach( $state['location'] as $oid ){
					$params['locations'][$oid] = $params['all_locations'][$oid];
				}
			}
		}
		else {
			$params['locations'] = $params['all_locations'];
		}

		unset($params['staffs']['_1']);
		unset($params['staffs'][-1]);
		unset($params['locations']['_1']);
		unset($params['locations'][-1]);

		return $params;
	}

	private function _display( $shifts, $state, $tab = 'list', $display = 'table' )
	{
		$extensions = HC_App::extensions();

		$is_module = ( $this->input->is_ajax_request() OR $this->is_module() ) ? 1 : 0;
		$is_print = (isset($state['print']) && $state['print']) ? 1 : 0;
		if( $is_module ){
			$is_print = 0;
		}

		$display_params = $this->_prepare_display( $state );
		if( $is_print ){
			$this->form = NULL;
		}

	// remove no user if no open shifts
		if( isset($display_params['staffs']) && is_array($display_params['staffs']) && (count($display_params['staffs']) > 1) ){
			if( isset($display_params['staffs'][0]) ){
				$show_no_user = FALSE;
				foreach( $shifts as $sh ){
					if( ! $sh->user->id ){
						$show_no_user = TRUE;
						break;
					}
				}
				if( ! $show_no_user ){
					unset($display_params['staffs'][0]);
				}
			}
		}

	/* render */
		$rootlink = $this->rootlink;
		$layout = clone $this->layout;

	/* performance sensitive */
		if( isset($this->fix['tab']) && $this->fix['tab'] ){
			$enabled_tabs = is_array($this->fix['tab']) ? $this->fix['tab'] : array($this->fix['tab']);
		}
		else {
			$enabled_tabs = array('calendar', 'browse', 'report');
		}

		if( ! $is_module ){
			if( ! in_array($display, array('list')) ){
				$layout->set_partial(
					'control', 
					$this->render( 
						$this->views_path . '/_control',
						array(
							'fix'			=> $this->fix,
							'rootlink'		=> $rootlink,
							'tab'			=> $tab,
							'enabled_tabs'	=> $enabled_tabs,
							'state'			=> $state,
							'all_staffs'	=> $display_params['all_staffs'],
							'all_locations'	=> $display_params['all_locations'],
							'staffs'		=> $display_params['staffs'],
							'locations'		=> $display_params['locations'],
							)
						)
					);
			}
		}

		$display_file = '_' . $display;

		$form = $this->form;

		if( count($shifts) ){
			$acl = HC_App::acl();
			$can_edit_shifts = $acl->filter( $shifts, 'edit' );
			if( ! $can_edit_shifts ){
				$form = NULL;
			}
		}

		$list_view = $this->render(
			$this->views_path . '/' . $display_file,
			array(
				'rootlink'	=> $rootlink,
				'state'		=> $state,
				'shifts'	=> $shifts,
				'form'		=> $form,

				'all_staffs'	=> $display_params['all_staffs'],
				'all_locations'	=> $display_params['all_locations'],
				'staffs'		=> $display_params['staffs'],
				'locations'		=> $display_params['locations'],
				)
			);

		if( $is_module ){
			echo $list_view;
		}
		else {
			$layout->set_partial(
				'list',
				$list_view
				);

			$this->layout->set_partial(
				'content', 
				$this->render(
					$this->views_path . '/index',
					array(
						'layout'	=> $layout,
						'is_module'	=> $is_module,
						'is_print'	=> $is_print,
						)
					)
				);
			if( $is_print ){
				$this->layout('print');
			}
			else {
				$this->layout();
			}
		}
	}

/* init state from supplied params */
	private function _grab_state( $args, $tab = 'browse', $more_defaults = array() )
	{
		$t = HC_Lib::time();

		$state = array(
			'range'		=> 'week',
			'date'		=> $t->formatDate_Db(),
			'by'		=> NULL,
			// 'by'		=> 'staff',
			'location'	=> array(),
			'staff'		=> array(),
			'type'		=> NULL,
			'wide'		=> NULL,
			'filter'	=> NULL,
			'hide-ui'	=> NULL,
			'status'	=> NULL,
			);

		if( ($current_user = $this->auth->user()) && $current_user->id ){
			$model = HC_App::model('user_preferences');
			$saved_pref = $model->get( 'calendar_view' );

			if( $saved_pref && is_array($saved_pref) ){
				foreach( $saved_pref as $k => $v ){
					$state[$k] = $v;
				}
			}
		}

		foreach( $more_defaults as $k => $v ){
			$state[$k] = $v;
		}

		$default_params = $this->default_params;
/*
		$default_params['hide-ui'] = array(
			'login',
			'filter-staff',
			'filter-location',
			'print',
			'download',
			'view-type',
			'group-by',
			'date-navigation'
			);
*/
		$supplied = hc_parse_args( $args );
		$supplied = array_merge( $default_params, $supplied );

		if( isset($supplied['hide-ui']) ){
			$filter_ui = HC_App::filter_ui();
			if( ! is_array($supplied['hide-ui']) ){
				$supplied['hide-ui'] = explode( ',', $supplied['hide-ui'] );
				$supplied['hide-ui'] = array_map( 'trim', $supplied['hide-ui'] );
			}
			foreach( $supplied['hide-ui'] as $h ){
				$filter_ui->disable($h);
			}
		}

		foreach( $supplied as $k => $v ){
			if( in_array($k, array('staff', 'location', 'type')) ){
				if( strpos($v, '.') !== FALSE ){
					$v = explode('.', $v);
				}
				elseif( strpos($v, ',') !== FALSE ){
					$v = explode(',', $v);
				}
				else {
					$v = array($v);
				}
			}
			$state[$k] = $v;
		}

		/* check _current_user_id_ */
		if( isset($state['staff']) ){
			$check_current_user = array('_current_user_id_', '_current_user_id', 'current_user_id', 'current_user', '_current_user_');
			$current_user_key = '';
			foreach( $check_current_user as $cuk ){
				if( in_array($cuk, $state['staff']) ){
					$current_user_key = $cuk;
					break;
				}
			}

			if( $current_user_key ){
				$current_user_id = 0;
				if( $test_user = $this->auth->user() ){
					$current_user_id = $test_user->id;
				}
				$state['staff'] = HC_Lib::replace_in_array($state['staff'], $current_user_key, $current_user_id);
			}
		}

	/* fixed ? */
		$force_fixed = array( 'location', 'staff' );

		foreach( $default_params as $k => $v ){
			if( in_array($k, $force_fixed) ){
				$this->fix[$k] = $state[$k];
			}
		}

	/* if custom dates supplied */
		if( isset($supplied['customdates']) && $supplied['customdates'] ){
			$post = $this->input->post();
			if( isset($post['start_date']) && isset($post['end_date']) ){
				if( $post['end_date'] <= $post['start_date'] ){
					$post['end_date'] = $post['start_date'];
				}
				$state['date'] = $post['start_date'] . '_' . $post['end_date'];
				$state['range'] = 'custom';
				unset( $state['customdates'] );

				$link = HC_Lib::link( $this->views_path . '/' . $tab, $state );
				$redirect_to = $link->url();
				$this->redirect( $redirect_to );
				return;
			}
			elseif( isset($post['date']) ){
				$state['date'] = $post['date'];
				$state['range'] = 'day';
				unset( $state['customdates'] );

				$link = HC_Lib::link( $this->views_path . '/' . $tab, $state );
				$redirect_to = $link->url();
				$this->redirect( $redirect_to );
				return;
			}
		}

	/* if custom dates supplied */
		if( isset($supplied['updateview']) && $supplied['updateview'] ){
			$post = $this->input->post();

			if( isset($post['by']) ){
				$state['by'] = $post['by'];
			}
			if( isset($post['tab']) ){
				$tab = $post['tab'];
			}
			unset( $state['updateview'] );

			$link = HC_Lib::link( $this->views_path . '/' . $tab, $state );
			$redirect_to = $link->url();
			$this->redirect( $redirect_to );
			return;
		}

		$t = HC_Lib::time();
		list( $start_date, $end_date ) = $t->getDatesRange( $state['date'], $state['range'] );

		switch( $state['range'] ){
			case 'all':
				$shifts = HC_App::model('shift');
				$min_date = $shifts->select_min('date')->get_slim()->date;
				if( $min_date ){
					$state['date'] = $min_date;
				}
				else {
					$t->setNow();
					$state['date'] = $t->formatDate_Db();
				}
				break;

			case 'upcoming':
				$t->setNow();
				$state['date'] = $t->formatDate_Db();
				break;

			case 'day':
				break;

			case 'week':
			case 'month':
				$state['date'] = $start_date;
				break;

			default:
				$state['date'] = $start_date . '_' . $end_date;
				$state['range'] = 'custom';
		}

	/* something fixed ? */
		foreach( $this->fix as $fk => $fv ){
			if( $fv ){
				unset( $state[$fk] );
			}
		}

		if( isset($args['tab']) && strlen($args['tab']) ){
			$state['tab'] = $args['tab'];
		}
		return $state;
	}

/* find appropriate shifts */
	private function _init_shifts( $state )
	{
	/* additional change state by fixed params */
		if( $this->fix['staff'] ){
			$state['staff'] = $this->fix['staff'];
		}
		if( isset($this->fix['type']) ){
			$state['type'] = $this->fix['type'];
		}
		if( isset($this->fix['filter']) ){
			$state['filter'] = $this->fix['filter'];
		}
		if( $this->fix['location'] ){
			if(
				is_array($this->fix['location']) &&
				(count($this->fix['location']) == 1) &&
				($this->fix['location'][0] == 0)
				){
					/* all locations */
				}
			else {
				$state['location'] = $this->fix['location'];
			}
		}

		$state['staff'] = HC_Lib::remove_from_array( $state['staff'], -1 );
		$state['staff'] = HC_Lib::remove_from_array( $state['staff'], '_1' );
		$state['location'] = HC_Lib::remove_from_array( $state['location'], -1 );
		$state['location'] = HC_Lib::remove_from_array( $state['location'], '_1' );
		$state['type'] = HC_Lib::remove_from_array( $state['type'], -1 );
		$state['type'] = HC_Lib::remove_from_array( $state['type'], '_1' );

		if( in_array($state['status'], array(-1, '_1')) ){
			unset( $state['status'] );
		}
		if( in_array($state['type'], array(-1, '_1')) ){
			unset( $state['type'] );
		}

		$shifts = HC_App::model('shift');
		$return = $shifts->load( $state );

		$acl = HC_App::acl();
		$return = $acl->filter( $return, 'view' );

		return $return;
	}

/* pushes download */
	private function _download( $shifts )
	{
		$app_conf = HC_App::app_conf();
		$separator = $app_conf->get( 'csv_separator' );

	// header
		$headers = array(
			HCM::__('Type'),
			HCM::__('Date'),
			HCM::__('Time'),
			HCM::__('Duration'),
			HCM::__('Staff'),
			HCM::__('Location'),
			HCM::__('Status')
			);

		$data = array();
		$data[] = join( $separator, $headers );

		$t = HC_Lib::time();

	// shifts
		foreach( $shifts as $sh )
		{
			$values = array();

		// type
			$values[] = $sh->present_type(HC_PRESENTER::VIEW_RAW);

		// date
			$values[] = $sh->present_date(HC_PRESENTER::VIEW_RAW);

		// time
			$values[] = $sh->present_time(HC_PRESENTER::VIEW_RAW);

		// duration
			$values[] = $t->formatPeriodExtraShort($sh->get_duration(), 'hour');

		// staff
			$values[] = $sh->present_user(HC_PRESENTER::VIEW_RAW);

		// location
			$values[] = $sh->present_location(HC_PRESENTER::VIEW_RAW);

		// status
			$values[] = $sh->present_status(HC_PRESENTER::VIEW_RAW);

		/* add csv line */
			$data[] = HC_Lib::build_csv( array_values($values), $separator );
		}

	// output
		$out = join( "\n", $data );

		$file_name = isset( $this->conf['export'] ) ? $this->conf['export'] : 'export';
		$file_name .= '-' . date('Y-m-d_H-i') . '.csv';

		$this->load->helper('download');
		force_download($file_name, $out);
		return;
	}
}