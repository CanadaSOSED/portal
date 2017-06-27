<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Add_Shifts_HC_Controller extends _Front_HC_Controller
{
	protected $form_add_time = NULL;
	protected $form_confirm = NULL;

	function __construct()
	{
		parent::__construct();

		$this->form_add_time = HC_Lib::form()
			->set_input( 'location', 'select' )
			->set_input( 'time', 'timeframe', array('start' => 'start', 'end' => 'end') )
			->set_input( 'lunch_break', 'duration' )
			->set_input( 'date', 'recurring_date' )
			;

		if( $this->hc_modules->exists('shift_groups') ){
			$this->form_add_time->input_call( 'date', 'set_enabled', array(array('single', 'recurring')) );
		}
		else {
			$this->form_add_time->input_call( 'date', 'set_enabled', array(array('single')) );
		}

		$this->form_add_user = HC_Lib::form()
			->set_input( 'user', 'checkbox_set' )
			;
		$this->form_add_open_shift = HC_Lib::form()
			->set_input( 'open', 'select' )
			;

		$this->form_confirm = HC_Lib::form()
			->set_input( 'status',	'radio' )
			;
	}

	private function _init_add_params( $args )
	{
		$params = new HC_Page_Params;

		$args = hc_parse_args( $args, TRUE );
		foreach( $args as $k => $v ){
			$params->set( $k, $v );
		}

	/* possible options */
		$acl = HC_App::acl();

		$shift = HC_App::model('shift');
		$all = array(
			'type'		=> array( $shift->_const('TYPE_SHIFT'), $shift->_const('TYPE_TIMEOFF') ),
			'location'	=> HC_App::model('location')->get(),
			'user'		=> HC_App::model('user')->get_staff(),
			'status'	=> array( $shift->_const('STATUS_DRAFT'), $shift->_const('STATUS_ACTIVE') ),
			);
		$can = array(
			'type'		=> array(),
			'location'	=> array(),
			'user'		=> array(),
			'status'	=> array(),
			);

		$test_shift = $shift->get_clone();

		$supplied_type = $params->get('type');
		if( $supplied_type ){
			$test_shift->type = $supplied_type;
		}

	/* run tests */
	/* location */
		foreach( $all['location'] as $location ){
			$test_can = $acl
				->set_object($test_shift)
				->can('validate_location', $location->id)
				;
			if( $test_can ){
				$can['location'][$location->id] = $location;
			}
		}

	/* status */
		foreach( $all['status'] as $status ){
			$test_can = $acl
				->set_object($test_shift)
				->can('validate_status', $status)
				;
			if( $test_can ){
				$can['status'][$status] = $status;
			}
		}

	/* user */
		foreach( $all['user'] as $user ){
			$test_can = $acl
				->set_object($test_shift)
				->can('validate_user', $user->id)
				;
			if( $test_can ){
				$can['user'][$user->id] = $user;
			}
		}

	/* type */

/*
		foreach( $all['type'] as $type ){
			foreach( $all['location'] as $location ){
				foreach( $all['user'] as $user ){
					foreach( $all['status'] as $status ){
						$shift = HC_App::model('shift');
						$shift->type = $type;
						$shift->location_id = $location->id;
						$shift->user_id = $user->id;
						$shift->status = $status;
						if( $acl->set_object($shift)->can('validate') ){
							$can['type'][$type] = $type;
							$can['location'][$location->id] = $location;
							$can['user'][$user->id] = $user;
							$can['status'][$status] = $status;
						}
					}
				}
			}
		}
*/
		if( count($can['type']) == 1 ){
			reset($can['type']);
			$params->set('type', key($can['type']));
		}
		if( count($can['location']) == 1 ){
			reset($can['location']);
			$params->set('location', key($can['location']));
		}

		$shift = HC_App::model('shift');

		if( count($can['user']) == 1 ){
			if( $params->get('type') == $shift->_const('TYPE_TIMEOFF') ){
				reset($can['user']);
				$params->set('user', key($can['user']));
			}
		}
		if( count($can['status']) == 1 ){
			reset($can['status']);
			$params->set('status', key($can['status']));
		}

	/* set location for timeoff */
		if( $params->get('type') == $shift->_const('TYPE_TIMEOFF') ){
			$params->set( 'location', 0 );
		}

	/* user params as array */
		if( $params->get('user') !== NULL ){
			$param_users = $params->get('user');
			if( ! is_array($param_users) ){
				$param_users = array( $param_users );
			}
			$params->set( 'user', $param_users );
		}

		$params->set_options( 'type', $can['type'] );
		$params->set_options( 'user', $can['user'] );
		$params->set_options( 'location', $can['location'] );
		$params->set_options( 'status', $can['status'] );

		return $params;
	}

	function index()
	{
		$params = $this->_init_add_params( func_get_args() );
		return $this->_add( $params );
	}

	function add_user()
	{
		$params = $this->_init_add_params( func_get_args() );
		return $this->_add( $params, 'more_user' );
	}

	private function _add( $params, $tab = '' )
	{
		$layout = clone $this->layout;
		$model = HC_App::model('shift');

		if( ! $tab ){
			if( $params->get('type') === NULL ){
				$tab = 'type';
			}
			elseif( $params->get('start') === NULL ){
				$tab = 'time';
			}
			elseif( $params->get('location') === NULL ){
				$tab = 'location';
			}
			elseif( $params->get('user') === NULL ){
				$tab = 'user';
			}
			// elseif( $params->get('status') === NULL ){
				// $tab = 'status';
			// }
			else {
				$tab = 'confirm';
			}
		}

		$method = '_add_' . $tab;
		if( ! method_exists($this, $method) ){
			$tab = 'location';
			$method = '_add_' . $tab;
		}

	/* header */
		$layout->set_partial(
			'header', 
			$this->render( 
				'shifts/add/_header',
				array(
					'params'	=> $params,
					)
				)
			);

	/* menubar */
		$dates = array();

	/* if already users */
		$models = array();
		$allowed_user_ids = array();
		$free_staff = array();

		if( $params->get('user') ){
			$shift_array = $params->to_array();
			if( isset($shift_array['location']) ){
				$shift_array['location_id'] = $shift_array['location'];
				unset( $shift_array['location'] );
			}

			if( isset($shift_array['start']) ){
				$date_input = $this->form_add_time->input('date');
				if( isset($shift_array['date']) ){
					$date_input->set_value( $shift_array['date'] );
				}
				$dates = $date_input->dates();

				$already_users = isset($shift_array['user']) ? $shift_array['user'] : array();
				unset( $shift_array['user'] );
				foreach( $dates as $date ){
					$this_model = HC_App::model('shift');
					$shift_array['date'] = $date;
					$this_model->from_array( $shift_array );
					$models[] = $this_model;
				}
			}

		/* choose only those that I can add shifts for */
			$acl = HC_App::acl();
			$all_staff = HC_App::model('user')->get_staff();

			foreach( $all_staff as $this_staff ){
				$shift = HC_App::model('shift');
				$shift->user_id = $this_staff->id;
				if( $acl->set_object($shift)->can('add') ){
					$free_staff[] = $this_staff;
				}
			}
		}

		$layout->set_partial(
			'menubar', 
			$this->render( 
				'shifts/add/_menubar',
				array(
					'params'	=> $params,
					'tab'		=> $tab,
					'model'		=> $model,
					'models'	=> $models,
					'dates'		=> $dates,
					'free_staff'=> $free_staff,
					)
				)
			);

	/* build content */
		$layout = $this->{$method}( $layout, $params );

	/* final layout */
		$this->layout->set_partial(
			'content',
			$this->render(
				'shifts/add/index',
				array(
					'layout'	=> $layout,
					)
				)
			);
		$this->layout();
	}

	private function _add_type( $layout, $params )
	{
		$types = $params->get_options('type');

		$layout->set_partial(
			'content', 
			$this->render( 
				'shifts/add/type',
				array(
					'params'	=> $params,
					'types'		=> $types,
					)
				)
			);
		return $layout;
	}

	private function _add_status( $layout, $params )
	{
		$statuses = $params->get_options('status');

		$layout->set_partial(
			'content', 
			$this->render( 
				'shifts/add/status',
				array(
					'params'		=> $params,
					'statuses'		=> $statuses,
					)
				)
			);
		return $layout;
	}

	private function _add_location( $layout, $params )
	{
		$locations = $params->get_options('location');

		$layout->set_partial(
			'content', 
			$this->render( 
				'shifts/add/location',
				array(
					'locations'	=> $locations,
					'params'	=> $params,
					)
				)
			);
		return $layout;
	}

	private function _add_user( $layout, $params )
	{
		$params_array = $params->to_array();

		$date_input = $this->form_add_time->input('date');
		$date_input->set_value( $params_array['date'] );
		$dates = $date_input->dates();

		$shift_array = $params->to_array();
		unset( $shift_array['date'] );
		$shift_array['location_id'] = $shift_array['location'];
		unset( $shift_array['location'] );

	/* choose only those that I can add shifts for */
		$acl = HC_App::acl();
		$all_staff = HC_App::model('user')->get_staff();
		$free_staff = array();

		foreach( $all_staff as $this_staff ){
			$test_shift = HC_App::model('shift');
			$test_shift->user_id = $this_staff->id;
			if( isset($params_array['type']) && $params_array['type'] ){
				$test_shift->type = $params_array['type'];
			}
			if( isset($params_array['location']) && $params_array['location'] ){
				$test_shift->location_id = $params_array['location'];
			}

			if( $acl->set_object($test_shift)->can('add') ){
				$free_staff[] = $this_staff;
			}
			else {
				// echo 'USER ' . $this_staff->id . ' IS NOT OK!';
			}
		}

	/* choose only those that I can add shifts for */
		if( count($free_staff) == 1 ){
			$link = HC_Lib::link('shifts/add/index');
			$params_array = $params->to_array();
			$params_array['user'] = $free_staff[0]->id;

			$redirect_to = $link->url( $params_array );
			$this->redirect( $redirect_to );
			return;
		}

	/* models */
		$models = array();
		foreach( $dates as $date ){
			$model = HC_App::model('shift');
			$shift_array['date'] = $date;
			$model->from_array( $shift_array );
			$models[] = $model;
		}

		$layout->set_partial(
			'content', 
			$this->render( 
				'shifts/add/user',
				array(
					'free_staff'	=> $free_staff,
					'params'		=> $params,
					'models'		=> $models,
					'form'			=> $this->form_add_user,
					'form_open'		=> $this->form_add_open_shift,
					)
				)
			);
		return $layout;
	}

	private function _add_more_user( $layout, $params )
	{
		$model = HC_App::model('shift');
		$shift_array = $params->to_array();
		$already_users = isset($shift_array['user']) ? $shift_array['user'] : array();

		$date_input = $this->form_add_time->input('date');
		$date_input->set_value( $shift_array['date'] );
		$dates = $date_input->dates();

		unset( $shift_array['user'] );
		unset( $shift_array['date'] );
		$shift_array['location_id'] = $shift_array['location'];
		unset( $shift_array['location'] );
		$model->from_array( $shift_array );

	/* choose only those that I can add shifts for */
		$acl = HC_App::acl();
		$all_staff = HC_App::model('user')
			->where_not_in('id', $already_users)
			->get_staff();
		$free_staff = array();

		foreach( $all_staff as $this_staff ){
			$shift = HC_App::model('shift');
			$shift->user_id = $this_staff->id;
			if( $acl->set_object($shift)->can('add') ){
				$free_staff[] = $this_staff;
			}
		}

		if( 0 && (! $free_staff) ){
			$link = HC_Lib::link('shifts/add/index');
			$params_array = $params->to_array();
			$redirect_to = $link->url( $params_array );
			$this->redirect( $redirect_to );
			return;
		}

	/* models */
		$models = array();
		foreach( $dates as $date ){
			$model = HC_App::model('shift');
			$shift_array['date'] = $date;
			$model->from_array( $shift_array );
			$models[] = $model;
		}

		$layout->set_partial(
			'content', 
			$this->render( 
				'shifts/add/user',
				array(
					'free_staff'	=> $free_staff,
					'models'		=> $models,
					'params'		=> $params,
					'dates'			=> $dates,
					'form'			=> $this->form_add_user,
					'form_open'		=> $this->form_add_open_shift,
					)
				)
			);
		return $layout;
	}

	private function _add_time( $layout, $params )
	{
		$default_date = $params->get('date') ? $params->get('date') : HC_Lib::time()->setNow()->formatDate_Db();
		if( $default_date ){
			$already_value = $this->form_add_time->input('date')->value();

			if( ! $already_value ){
				$t = HC_Lib::time();
				$date_end = $t
					->setDateDb( $default_date )
					->modify('+1 month')
					->formatDate_Db()
					;
				$this->form_add_time->set_values(
					array(
						// 'date'	=> $default_date,
						'date'	=> array(
							'recurring'		=> 'single',
							'datesingle'	=> $default_date,

							'datestart'		=> $default_date,
							'dateend'		=> $date_end,
							'repeat'		=> 'daily'
							),
						)
					);
			}
		}

		$stm = HC_App::model('shift_template');
		$shift_templates = $stm->get_all();

		$locations = array();
		if( $params->get('location') === NULL ){
			$locations = $params->get_options('location');
		}

		$layout->set_partial(
			'content', 
			$this->render( 
				'shifts/add/time',
				array(
					'form'				=> $this->form_add_time,
					'shift_templates'	=> $shift_templates,
					'params'			=> $params,
					'locations'			=> $locations,
					)
				)
			);
		return $layout;
	}

	private function _add_confirm( $layout, $params )
	{
		$extensions = HC_App::extensions();

		$params_array = $params->to_array();
		$date_input = $this->form_add_time->input('date');
		$date_input->set_value( $params_array['date'] );
		$dates = $date_input->dates();

		$model = HC_App::model('shift');
		$default_values = array(
			'status'	=> $model->_const('STATUS_DRAFT')
			);

	/* extensions */
		$change_values = $extensions->run(
			'shifts/insert/defaults'
			);
		foreach( $change_values as $change_array ){
			foreach( $change_array as $k => $v ){
				$default_values[$k] = $v;
			}
		}

		$this->form_confirm->set_values( $default_values );

		$model->type = $params->get('type');
		$layout->set_partial(
			'content', 
			$this->render( 
				'shifts/add/confirm',
				array(
					'model'		=> $model,
					'form'		=> $this->form_confirm,
					'params'	=> $params,
					'dates'		=> $dates,
					)
				)
			);
		return $layout;
	}

	public function insert_time()
	{
		$params = $this->_init_add_params( func_get_args() );
		$values = $params->to_array();

		$model = HC_App::model('shift');

	/* if post supplied */
		$post = $this->input->post();

		if( $post ){
			$this->form_add_time->grab( $post );
			$form_values = $this->form_add_time->values();

			$values = array_merge( $values, $form_values );
		}

		$shift_values = $values;
		unset( $shift_values['date'] );
		unset( $shift_values['user'] );

		if( $params->get('location') !== NULL ){
			$shift_values['location'] = $params->get('location');
			$values['location'] = $params->get('location');
		}

		$model->remove_validation('date');
		$related = $model->from_array( $shift_values );

		$model->validate( $related );
		$errors = $model->errors();

		$this->form_add_time->set_errors( $errors );
		$form_errors = $this->form_add_time->errors();
		if( $form_errors ){
			// reset date
			$params->reset('date');
			return $this->_add( $params, 'time' );
		}
		else {
			foreach( $values as $k => $v ){
				$params->set( $k, $v );
			}
			$link = HC_Lib::link('shifts/add/index');
			$redirect_to = $link->url( $params->to_array() );
			$this->redirect( $redirect_to );
		}
	}

	public function insert_user()
	{
		$params = $this->_init_add_params( func_get_args() );
		$values = $params->to_array();

		$model = HC_App::model('shift');

	/* if post supplied */
		$post = $this->input->post();
		if( $post ){
		/* open shifts */
			if( isset($post['open']) ){
				$form_values['user'] = array_fill( 0, $post['open'], 0 );
			}
		/* staff supplied */
			else {
				$this->form_add_user->grab( $post );
				$form_values = $this->form_add_user->values();
			}

			if( isset($form_values['user']) && is_array($form_values['user']) ){
				if( ! isset($values['user']) ){
					$values['user'] = array();
				}
				foreach( $form_values['user'] as $uid ){
					$values['user'][] = $uid;
				}

			/* move open shifts to bottom */
				$final_user_values = array();
				$opens = array();
				reset( $values['user'] );
				foreach( $values['user'] as $uid ){
					if( $uid ){
						if( ! in_array($uid, $final_user_values) ){
							$final_user_values[] = $uid;
						}
					}
					else {
						$opens[] = $uid;
					}
				}
				$final_user_values = array_merge( $final_user_values, $opens );
				$values['user'] = $final_user_values;
			}
		}

		$shift_values = $values;
		// unset( $shift_values['date'] );
		unset( $shift_values['user'] );

		if( $params->get('location') !== NULL ){
			$shift_values['location'] = $params->get('location');
			$values['location'] = $params->get('location');
		}

		if( ! (isset($values['user']) && $values['user']) ){
			$params->reset('user');
			return $this->_add( $params, 'user' );
		}

		foreach( $values as $k => $v ){
			$params->set( $k, $v );
		}

		$link = HC_Lib::link('shifts/add/index');
		$redirect_to = $link->url( $params->to_array() );
		$this->redirect( $redirect_to );
	}

/* add new */
	function insert()
	{
		$extensions = HC_App::extensions();

		$params = $this->_init_add_params( func_get_args() );
		$params_array = $params->to_array();

		$model = HC_App::model('shift');

		$date_input = $this->form_add_time->input('date');
		$date_input->set_value( $params_array['date'] );
		$dates = $date_input->dates();

	/* if post supplied */
		$post = $this->input->post();
		$this->form_confirm->grab( $post );
		$form_values = $this->form_confirm->values();

		$possible_statuses = $params->get_options('status');
		if( count($possible_statuses) > 1 ){
			if( array_key_exists('status', $form_values) ){
				$params_array['status'] = $form_values['status'];
			}
		}
		elseif( count($possible_statuses) == 1 ){
			$params_array['status'] = array_shift($possible_statuses);
		}

		$success_count = 0;
		$users_ids = $params_array['user'];

		// $publish_now = ($form_values['status'] == $model->_const('STATUS_ACTIVE')) ? TRUE : FALSE;
		$publish_now = ($params_array['status'] == $model->_const('STATUS_ACTIVE')) ? TRUE : FALSE;

		unset( $params_array['user'] );
		if( ! $params_array['location'] ){
			unset( $params_array['location'] );
		}

	/* group id */
		$group_id = 0;
		if( count($dates) * count($users_ids) > 1 ){
			$max_group_id = $model->select_max('group_id')->get_slim()->group_id;
			if( ! $max_group_id ){
				$max_group_id = 0;
			}
			$group_id = $max_group_id + 1;
		}

		$result_models = array();

		foreach( $dates as $date ){
			foreach( $users_ids as $uid ){
				$this_params = $params_array;
				$this_params['date'] = $date;
				if( $uid ){
					$this_params['user'] = $uid;
				}

				$model->clear();
				$related = $model->from_array( $this_params );
				$model->group_id = $group_id;

			/* create shift */
				if( ! $model->save($related) ){
					$errors = $model->errors();
					$this->form_confirm->set_errors( $errors );
					$orphan_errors = $this->form_confirm->orphan_errors();
					return $this->_add( $params, 'confirm' );
				}
				$success_count++;

			$result_models[] = clone $model;

		/* extensions */
			$extensions->run(
				'shifts/insert',
				$post,
				$model
				);
			}
		}

		switch( $model->type ){
			case $model->_const('TYPE_SHIFT'):
				$msg = sprintf( HCM::_n('%d shift added', '%d shifts added', $success_count), $success_count );
				break;
			case $model->_const('TYPE_TIMEOFF'):
				$msg = sprintf( HCM::_n('%d timeoff added', '%d timeoffs added', $success_count), $success_count );
				break;
		}

		$this->session->set_flashdata( 'message', $msg );

		$redirect_to = 'list/calendar';

		$parent_refresh = array();
		foreach( $result_models as $o ){
			$this_parent_refresh = $o->present_calendar_refresh();
			$parent_refresh = array_merge($parent_refresh, $this_parent_refresh);
		}
		$parent_refresh = array_keys($parent_refresh);

		$this->redirect( $redirect_to, $parent_refresh );
	}
}