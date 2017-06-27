<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Auth_HC_Controller extends _Front_HC_Controller {
	protected $views_path = 'auth';

	function __construct()
	{
		parent::__construct( FALSE );

		$ri = HC_Lib::ri();
		$app = $this->config->item('nts_app');
		if( $ri ){
			$user_id = 0;
			if( $test_user = $this->auth->user() ){
				$user_id = $test_user->id;
			}
			if( ! $user_id ){
				Modules::run( $ri . '/auth/login' );
			}
		}
	}

	function index()
	{
		if( ! $this->auth->check() ){
			//redirect them to the login page
			$this->redirect('auth/login');
		}
		return;
	}

	function login()
	{
		$post = $this->input->post();

		$validator = new HC_Validator;
		$validator->set_rules('identity', 'required');
		$validator->set_rules('password', 'required');

		if( $post && ($validator->run($post) == TRUE) ){
			$remember = (bool) $this->input->post('remember');

			if( $this->auth->attempt($post['identity'], $post['password'], $remember) ){
				// check if not archived
				if( ! $this->auth->user()->active ){
					$this->auth->logout();
					$this->session->set_flashdata(
						'error',
						HCM::__('This user account is disabled')
						);
					$this->redirect('auth/login');
				}
				else {
					$this->redirect('');
				}
			}
			else {
				//if the login was un-successful
				//redirect them back to the login page
				$this->session->set_flashdata(
					'error', 
					HCM::__('Wrong username or password')
					);
				$this->redirect('auth/login');
			}
		}

		$errors = $validator->error();

		$form = HC_Lib::form();
		$form->set_inputs(
			array(
				'identity'	=> 'text',
				'password'	=> 'password',
				'remember'	=> 'checkbox',
				)
			);
		$form->set_values( $post );
		$form->set_errors( $errors );

		$this->layout->set_partial(
			'content',
			$this->render(
				'auth/login',
				array(
					'form'	=> $form
					)
				)
			);
		$this->layout();
	}

	function logout()
	{
		$ri = HC_Lib::ri();
		if( $ri ){
			Modules::run( $ri . '/auth/logout' );
			return;
		}

		$logout = $this->auth->logout();
		$this->session->set_flashdata('message', HCM::__('You have been logged out'));
		$this->redirect();
	}

	function notallowed()
	{
		$this->layout->set_partial(
			'content', 
			$this->render(
				'auth/notallowed',
				array(
					)
				)
			);
		$this->layout();
	}

	function forgot_password()
	{
		$post = $this->input->post();

		$form = HC_Lib::form();
		$form->set_inputs(
			array(
				'email'	=> 'text',
				)
			);

		$validator = new HC_Validator;
		$validator->set_rules('email', 'required');

		if( $post && ($validator->run($post) == TRUE) ){
			$form->grab( $post );
			$values = $form->values();

			$forgotten = $this->auth->forgotten_password( $values['email'] );

			if( $forgotten ){
				//if there were no errors
				$this->session->set_flashdata('message', HCM::__('Password reset message has been sent to your email'));
				$this->redirect('auth/login');
			}
			else {
				$this->session->set_flashdata('error', $this->auth->error);
				$this->redirect('auth/forgot_password');
			}
		}

		$errors = $validator->error();

		$form->set_values( $post );
		$form->set_errors( $errors );

		$this->layout->set_partial(
			'content',
			$this->render(
				'auth/forgot_password',
				array(
					'form'	=> $form
					)
				)
			);
		$this->layout();
	}
}