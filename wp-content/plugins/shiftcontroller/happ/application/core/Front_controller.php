<?php
abstract class _Front_HC_Controller extends MY_HC_Controller 
{
	function __construct( $check_active = TRUE )
	{
		parent::__construct();
		$this->_do_init();
	}

	private function _do_init( $check_active = TRUE )
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

		if( $check_active ){
			$user = $this->auth->user();
			$user_active = 1;
			if( $user && $user->id && strlen($user->active) ){
				$user_active = $user->active;
			}

			if( ! $user_active ){
				$to = 'auth/notallowed';
				$this->redirect( $to );
				exit;
			}
		}

		$did = TRUE;
	}
}