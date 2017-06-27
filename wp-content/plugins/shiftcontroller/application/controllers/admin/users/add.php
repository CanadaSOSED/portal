<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//class Add_Users_Admin_HC_Controller extends _Backend_HC_controller
class Add_Users_Admin_HC_Controller extends _Backend_HC_controller
{
	protected $forms = array();

	function __construct()
	{
		parent::__construct( USER_HC_MODEL::LEVEL_MANAGER );

		$this->forms = array();
		$this->forms['add'] = HC_Lib::form()
			->set_input( 'first_name',		'text' )
			->set_input( 'last_name',		'text' )
			->set_input( 'email',			'text' )
			->set_input( 'username',		'text' )
			->set_input( 'level',			'select' )
			->set_input( 'password',			'password' )
			->set_input( 'confirm_password',	'password' )
			;
	}

	function index( $model = NULL )
	{
		if( $model === NULL ){
			$model = HC_App::model('user');
		}

		$this->forms['add']->set_values( $model->to_array() );
		$this->forms['add']->set_errors( $model->errors() );

	/* content */
		$this->layout->set_partial(
			'content',
			$this->render( 
				'admin/users/add',
				array(
					'object'	=> $model,
					'form'		=> $this->forms['add'],
					)
				)
			);

		$this->layout();
	}

	function insert()
	{
		$model = HC_App::model('user');

	/* supplied as parameters */
		$args = func_get_args();
		$values = hc_parse_args( $args );

	/* if post supplied */
		$post = $this->input->post();
		if( $post ){
			$this->forms['add']->grab( $post );
			$post = $this->forms['add']->values();
			$values = array_merge( $values, $post );
		}

		if( ! $values ){
			$redirect_to = 'admin/users/add';
			$this->redirect( $redirect_to );
			return;
		}

		$related = $model->from_array( $values );
		if( $model->save($related) ){
			/* save and redirect here */
			$msg = HCM::__('User added');
			$this->session->set_flashdata(
				'message',
				$msg
				);

			$redirect_to = 'admin/users/index';
			$this->redirect( $redirect_to );
		}
		else {
		/* final layout */
			$this->layout->set_partial(
				'content',
				Modules::run('admin/users/add/index', $model)
				);
			$this->layout();
		}
	}
}