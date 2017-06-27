<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Zoom_Shifts_HC_Controller extends _Front_HC_Controller
{
	protected $form_edit = NULL;
	protected $views_path = 'shifts/zoom';

	function __construct()
	{
		parent::__construct();

		$this->form_edit = HC_Lib::form()
			->set_input( 'status', 'radio' )
			->set_input( 'date', 'date' )
			->set_input( 'time', 'timeframe', array('start' => 'start', 'end' => 'end') )
			->set_input( 'lunch_break', 'duration' )
			->set_input( 'user', 'hidden' )
			->set_input( 'location', 'hidden' )
			;
	}

	function change( $id, $what, $skip = NULL )
	{
		if( is_object($id) ){
			$model = $id;
		}
		else {
			$model = HC_App::model('shift');
			$model
				->where('id', $id)
				->get()
				;
			$this->_check_model( $model );
		}

		$skip = ($skip === NULL) ? array() : array($skip);
 
		switch( $what ){
			case 'location':
				$options = HC_App::model('location');
				if( $skip ){
					$options
						->where_not_in('id', $skip)
						;
				}
				$options
					->get()
					;
				$view_file = $this->views_path . '/change_location';
				break;

			case 'user':
				$options = HC_App::model('user');

				if( $skip ){
					$options
						->where_not_in('id', $skip)
						;
				}

				$options = $options
					// ->where_not_in('id', $model->user_id)
					->get_staff()
					;

				$this->form_edit->set_values(
					array(
						'user'	=> $model->user_id
						)
					);
				$view_file = $this->views_path . '/change_user';
		}

		$content = $this->render(
			$view_file,
			array(
				'object'	=> $model,
				'options'	=> $options,
				'skip'		=> $skip,
				)
			);

		$this->layout->set_partial(
			'content',
			$content
			);

		$this->layout();
	}

	function form( $id, $what, $changed = NULL )
	{
		if( is_object($id) ){
			$model = $id;
		}
		else {
			$model = HC_App::model('shift');
			$model
				->where('id', $id)
				->get()
				;
			$this->_check_model( $model );
		}

		$form_values = $model->to_array();
		$form_values['location'] = $model->location_id;
		$form_values['user'] = $model->user_id;

		if( $changed !== NULL ){
			$form_values[$what] = $changed;
			$model->from_array( $form_values );
		}

		$form_values = $model->to_array();
		$form_values['location'] = $model->location_id;
		$form_values['user'] = $model->user_id;

		$this->form_edit->set_values( $form_values );
		$this->form_edit->set_errors( $model->errors() );

		switch( $what ){
			case 'location':
				$view_file = $this->views_path . '/form_location';
				break;

			case 'user':
				$view_file = $this->views_path . '/form_user';
		}

		$content = $this->render(
			$view_file,
			array(
				'object'	=> $model,
				'form'		=> $this->form_edit,
				)
			);

		$this->layout->set_partial(
			'content',
			$content
			);

		$this->layout();
	}

	function index()
	{
		$args = hc_parse_args( func_get_args(), TRUE );
		if( ! (isset($args['id'])) ){
			echo 'PARAMS MISSING IN shifts/zoom/index<br>';
			return;
		}

	/* PARAMS */
		$id = $args['id'];
		$tab = isset($args['tab']) ? $args['tab'] : 'overview';
		$subtab = isset($args['subtab']) ? $args['subtab'] : '';

		if( is_object($id) ){
			$model = $id;
		}
		else {
			$model = HC_App::model('shift');
			$model
				->where('id', $id)
				->get()
				;
			$this->_check_model( $model );
		}

		$acl = HC_App::acl();
		if( ! $acl->set_object($model)->can('view') ){
			return;
		}

	/* display form */
		$this->form_edit->set_values( $model->to_array() );
		$this->form_edit->set_errors( $model->errors() );

	/* build content */
		$calling_parent = 'shifts/zoom/index/id/' . $id;

		$subheader = NULL;
		$content = '';
		$method = '_content_' . $tab;
		if( method_exists($this, $method) ){
			$content = $this->{$method}( $model, $args );
		}
		else {
			$extensions = HC_App::extensions();
			if( $extensions->has(array('shifts/zoom', $tab)) ){
				$calling_parent = 'shifts/zoom/index/id/' . $id . '/tab/' . $tab;

				$content = $extensions->run(
					array('shifts/zoom', $tab, $calling_parent),
					$model,
					$subtab
					);

				$subheader = $extensions->run(
					array('shifts/zoom/menubar', $tab),
					$model
					);
			}
		}

	/* CONTENT */
		$content = $this->render(
			$this->views_path . '/index',
			array(
				'subheader'	=> $subheader,
				'content'	=> $content,
				)
			);

		$this->layout->set_partial(
			'content',
			$content
			);

		if( ! in_array($tab, array('assign')) ){
		/* HEADER */
			$this->layout->set_partial(
				'header_ajax',
				$this->render(
					$this->views_path . '/_header',
					array(
						'object'	=> $model,
						)
					)
				);

		/* MENUBAR */
			$this->layout->set_partial(
				'sidebar',
				$this->render( 
					$this->views_path . '/_menubar',
					array(
						'tab'		=> $tab,
						'object'	=> $model,
						)
					)
				);
		}

		$this->layout();
	}

	private function _content_overview( $model, $args = array() )
	{
		$acl = HC_App::acl();

		$all = array(
			'location'	=> HC_App::model('location')->get(),
			'user'		=> HC_App::model('user')->get_staff(),
			'status'	=> array( $model->_const('STATUS_DRAFT'), $model->_const('STATUS_ACTIVE') ),
			);
		$can = array(
			'location'	=> array(),
			'user'		=> array(),
			'status'	=> array(),
			'time'		=> FALSE,
			);

		$test_shift = $model->get_clone();

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

	/* time */
		$test_can = $acl
			->set_object($test_shift)
			->can('edit_time')
			;
		if( $test_can ){
			$can['time'] = TRUE;
		}

		if( $acl->set_object($model)->can('edit') ){
		/* check if we have anything changed */
			$changed = array();
			foreach( $args as $k => $v ){
				switch( $k ){
					case 'c_location':
						$changed['location'] = $v;
						break;
					case 'c_user':
						$changed['user'] = $v;
						break;
				}
			}

			$form_values = $model->to_array();
			$form_values['location'] = $model->location_id;
			$form_values['user'] = $model->user_id;

			if( $changed ){
				foreach( $changed as $k => $v ){
					$form_values[$k] = $v;
				}
				$model->from_array( $form_values );
			}

			$form_values = $model->to_array();
			$form_values['location'] = $model->location_id;
			$form_values['user'] = $model->user_id;

			$this->form_edit->set_values( $form_values );
			$this->form_edit->set_errors( $model->errors() );

			$stm = HC_App::model('shift_template');
			$shift_templates = $stm->get_all();

			$can_edit = $acl->set_object($model)->can('edit');
			$can_delete = $acl->set_object($model)->can('delete');

			return $this->render( 
				$this->views_path . '/overview_edit',
				array(
					'form'				=> $this->form_edit,
					'shift_templates'	=> $shift_templates,
					'object'			=> $model,
					'can'				=> $can,
					'can_edit'			=> $can_edit,
					'can_delete'		=> $can_delete,
					)
				);
		}
		else {
			return $this->render( 
				$this->views_path . '/overview_view',
				array(
					'object'			=> $model,
					)
				);
		}
	}

	private function _content_assign( $model )
	{
		$model = clone $model;

		$um = HC_App::model('user');
		$free_staff = $um
			// ->where_not_in('id', $model->user_id)
			->get_staff()
			;
		$this->form_edit->set_values(
			array(
				'user'	=> $model->user_id
				)
			);

		return $this->render(
			$this->views_path . '/assign-link',
			array(
				'object'		=> $model,
				'free_staff'	=> $free_staff,
				'form'			=> $this->form_edit,
				)
			);
	}
}