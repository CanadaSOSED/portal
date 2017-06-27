<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Profile_Auth_HC_Controller extends _Front_HC_Controller {
	protected $views_path = 'auth/profile';
	protected $form_edit = NULL;

	function __construct()
	{
		parent::__construct();

		$this->form_edit = HC_Lib::form()
			->set_input( 'email', 'text' )
			;
		$this->form_password = HC_Lib::form()
			->set_input( 'password', 'password' )
			->set_input( 'confirm_password', 'password' )
			;
	}

	function index()
	{
		if ( ! $this->auth->check()){
			$this->redirect('auth/login');
		}

		$args = hc_parse_args( func_get_args(), TRUE );
		// if( ! (isset($args['id'])) ){
			// echo 'PARAMS MISSING IN admin/users/zoom/index<br>';
			// return;
		// }

	/* PARAMS */
		$tab = isset($args['tab']) ? $args['tab'] : 'edit';
		$model = isset($args['id']) ? $args['id'] : clone $this->auth->user();

	/* build content */
		$subheader = NULL;
		$content = '';
		$method = '_content_' . $tab;

		if( method_exists($this, $method) ){
			$content = $this->{$method}( $model );
		}
		else {
			$extensions = HC_App::extensions();
			if( $extensions->has(array('auth/profile', $tab)) ){
				$calling_parent = 'auth/profile/index/tab/' . $tab;

				$pass_arg = isset($args['_pass']) ? $args['_pass'] : array();

				array_unshift(
					$pass_arg,
					array('auth/profile', $tab, $calling_parent)
					);

				$pass_arg[] = 'user';
				$pass_arg[] = $model->id;

				$content = call_user_func_array(
					array($extensions, 'run'),
					$pass_arg
					);

				$subheader = $extensions->run(
					array('auth/profile/menubar', $tab),
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

		$this->layout();
	}

	function update()
	{
		$model = $this->auth->user();
		$this->_check_model( $model );

	/* supplied as parameters */
		// $values = hc_parse_args( $args );
		$values = array();

	/* if post supplied */
		$post = $this->input->post();
		if( $post ){
			$this->form_edit->grab( $post );
			$post = $this->form_edit->values();
			$values = array_merge( $values, $post );
		}

		if( ! $values ){
			$redirect_to = 'auth/profile/index';
			$this->redirect( $redirect_to );
			return;
		}

		$model->email = $values['email'];
		// $related = $model->from_array( $values );

		if( $model->save() ){
			/* save and redirect here */
			$msg = HCM::__('User updated');
			$this->session->set_flashdata(
				'message',
				$msg
				);

			$redirect_to = 'auth/profile/index';
			$this->redirect( $redirect_to );
		}
		else {
		/* final layout */
			return $this->index( 'id', $model );
		}
	}

	function password()
	{
		$ri = HC_Lib::ri();
		if( $ri ){
			Modules::run( $ri . '/auth/password' );
			return;
		}

		$model = $this->auth->user();
		$this->_check_model( $model );
		$model = clone $model;

	/* supplied as parameters */
		// $values = hc_parse_args( $args );
		$values = array();

	/* if post supplied */
		$post = $this->input->post();
		if( $post ){
			$this->form_password->grab( $post );
			$post = $this->form_password->values();
			$values = array_merge( $values, $post );
		}

		if( ! $values ){
			$redirect_to = 'auth/profile/index/tab/password';
			$this->redirect( $redirect_to );
			return;
		}

		$model->password = $values['password'];
		$model->confirm_password = $values['confirm_password'];

		if( $model->save() ){
			/* save and redirect here */
			$msg = HCM::__('Password changed');
			$this->session->set_flashdata(
				'message',
				$msg
				);

			$redirect_to = 'auth/profile/index';
			$this->redirect( $redirect_to );
		}
		else {
		/* final layout */
			return $this->index( 'id', $model, 'tab', 'password' );
		}
	}

	private function _content_edit( $model )
	{
		$ri = HC_Lib::ri();

		if( $ri ){
			return $this->render( 
				'admin/users/zoom/view',
				array(
					'object'	=> $model,
					)
				);
		}
		else {
			$this->form_edit->set_values( $model->to_array() );
			$this->form_edit->set_errors( $model->errors() );

			return $this->render( 
				$this->views_path . '/edit',
				array(
					'form'		=> $this->form_edit,
					'object'	=> $model,
					)
				);
		}
	}

	private function _content_password( $model )
	{
		// $this->form_password->set_values( $model->to_array() );
		$this->form_password->set_errors( $model->errors() );

		return $this->render( 
			$this->views_path . '/password',
			array(
				'form'		=> $this->form_password,
				'object'	=> $model,
				)
			);
	}
}