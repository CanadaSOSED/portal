<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Update_Users_Admin_HC_Controller extends _Backend_HC_controller
{
	protected $forms = array();

	function __construct()
	{
		parent::__construct( USER_HC_MODEL::LEVEL_MANAGER );

		$this->forms = array();
		$this->forms['edit'] = HC_Lib::form()
			->set_input( 'first_name',	'text' )
			->set_input( 'last_name',	'text' )
			->set_input( 'email',		'text' )
			->set_input( 'username',	'text' )
			->set_input( 'level',		'select' )
			;
		$this->forms['password'] = HC_Lib::form()
			->set_input( 'password',			'password' )
			->set_input( 'confirm_password',	'password' )
			;
	}

	private function _update( $tab, $args )
	{
		$id = array_shift($args);

		$model = HC_App::model('user');
		$model
			->where('id', $id)
			->get()
			;
		$this->_check_model( $model );

		$original_model = clone $model;

	/* supplied as parameters */
		$values = hc_parse_args( $args );

	/* if post supplied */
		$post = $this->input->post();
		if( $post ){
			$this->forms[$tab]->grab( $post );
			$post = $this->forms[$tab]->values();
			$values = array_merge( $values, $post );
		}

		if( ! $values ){
			$redirect_to = 'admin/users/zoom/index/id/' . $id . '/tab/' . $tab;
			$this->redirect( $redirect_to );
			return;
		}

		$related = $model->from_array( $values );
		if( $model->save($related) ){
			/* save and redirect here */
			$msg = HCM::__('User updated');
			$this->session->set_flashdata(
				'message',
				$msg
				);

			$redirect_to = 'admin/users/zoom/index/id/' . $id . '/tab/' . $tab;
			$this->redirect( $redirect_to );
		}
		else {
		/* final layout */
			$this->layout->set_partial(
				'content',
				Modules::run('admin/users/zoom/index', 'id', $model, 'tab', $tab)
				);
			$this->layout();
		}
	}

	function password( $id )
	{
		$args = func_get_args();
		return $this->_update( 'password', $args );
	}

	function index( $id )
	{
		$args = func_get_args();
		return $this->_update( 'edit', $args );
	}
}