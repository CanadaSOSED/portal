<?php
class MY_HC_Controller extends MY_HC_Base_Controller
{
	function __construct()
	{
		parent::__construct();

	// if we need to simulate user - in WP shortcut page */
		$app = HC_App::app();
		if( isset($GLOBALS['NTS_CONFIG'][$app]['SIMULATE_USER_ID']) ){
			$acl = HC_App::acl();
			$simulate_id = $GLOBALS['NTS_CONFIG'][$app]['SIMULATE_USER_ID'];
			$auth_user = $this->auth->user();
			$acl_user = $this->auth->user($simulate_id);

			if( $auth_user->level >= $auth_user->_const('LEVEL_MANAGER') ){
				$acl->set_user( $acl_user );
			}
		}
	}
}