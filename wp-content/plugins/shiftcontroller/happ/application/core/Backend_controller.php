<?php
abstract class _Backend_HC_controller extends MY_HC_Controller 
{
	function __construct( $user_level = 0 )
	{
		parent::__construct();
		$this->_do_init( $user_level );
	}

	private function _do_init( $user_level = 0 )
	{
		static $did = FALSE;
		if( $did ){
			return;
		}

		$this->load->library('migration');
		if ( ! $this->migration->current()){
//			show_error($this->migration->error_string());
			return false;
		}

		$nts_config = HC_Lib::nts_config();
		if( isset($nts_config['FORCE_LOGIN_ID']) ){
			$id = $nts_config['FORCE_LOGIN_ID'];
			$this->auth->login( $id );
		}

		if ( ! $this->auth->check() ){
			$this->redirect('auth/login');
			exit;
		}

	/* check user active */
		$user_active = 1;
		if( $test_user = $this->auth->user() ){
			if( strlen($test_user->active) ){
				$user_active = $test_user->active;
			}
		}

		if( ! $user_active ){
			$to = 'auth/notallowed';
			$this->redirect( $to );
			exit;
		}

	/* check user level */
		if( $user_level ){
			$this->check_level( $user_level );
		}

	/* check license code */
		if( $this->hc_modules->exists('license') ){
			$license_model = HC_App::model('hitcode_license');
			$code = $license_model->get();
			if( ! $code ){
				$to = 'license/admin';

				$current_slug = $this->get_current_slug();
				if( $current_slug != $to ){
					$this->session->set_flashdata( 
						'error', 
						'license_code_required'
						);

					$this->redirect( $to );
					exit;
				}
			}
		}

		$did = TRUE;
	}
}
