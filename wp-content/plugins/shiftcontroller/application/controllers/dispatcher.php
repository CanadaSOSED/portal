<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Dispatcher_HC_Controller extends _Front_HC_Controller
{
	public function index()
	{
		$app = $this->config->item('nts_app');
		$app_conf = HC_App::app_conf();

		if( isset($GLOBALS['NTS_CONFIG'][$app]['FORCE_LOGIN_ID']) ){
			$id = $GLOBALS['NTS_CONFIG'][$app]['FORCE_LOGIN_ID'];
			$this->auth->login( $id );
		}

	// sync user account
		$ri = $this->remote_integration();
		if( $ri ){
			$id = $this->auth->check();
			if( $id ){
				$model_name = $ri . '_User';
				$um = HC_App::model($model_name);
				$um->sync( $id );
				$this->auth->reset_user();
			}
		}

	// check user level
		$user_level = 0;
		$user_id = 0;
		if( $this->auth->check() ){
			if( $test_user = $this->auth->user() ){
				$user_id = $test_user->id;
				$user_level = $test_user->level;
			}
		}

		if( $ri ){
			$wall_schedule_display = 0;
		}
		else {
			$wall_schedule_display = $app_conf->get('wall:schedule_display');
		}

		$default_params = $this->default_params;
		$allowed = FALSE;
		switch( $user_level ){
			case 0:
				if( $wall_schedule_display <= $user_level )
					$to = 'list';
				else{
					if( $user_id )
						$to = 'auth/notallowed';
					else
						$to = 'auth/login';
				}
				break;
			case USER_HC_MODEL::LEVEL_ADMIN:
			case USER_HC_MODEL::LEVEL_MANAGER:
				$to = isset($default_params['route']) ? $default_params['route'] : 'list';
				break;
			case USER_HC_MODEL::LEVEL_STAFF:
				$to = isset($default_params['route']) ? $default_params['route'] : 'listme';
				break;
		}
		
		$this->redirect( $to );
		exit;
	}
}
