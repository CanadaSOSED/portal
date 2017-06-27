<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Auth_Wordpress_HC_Controller extends _Front_HC_Controller
{
	function __construct()
	{
	// redirect to wp login page
/*
		$app = $this->config->item('nts_app');

		$return_to = isset( $GLOBALS['NTS_CONFIG'][$app]['FRONTEND_WEBPAGE'] ) ? $GLOBALS['NTS_CONFIG'][$app]['FRONTEND_WEBPAGE'] : get_bloginfo('wpurl');
		$to = wp_login_url( $return_to );
		$this->redirect($to);
		exit;
*/
	}

	public function login_url()
	{
		$app = $this->config->item('nts_app');
		$return_to = isset( $GLOBALS['NTS_CONFIG'][$app]['FRONTEND_WEBPAGE'] ) ? $GLOBALS['NTS_CONFIG'][$app]['FRONTEND_WEBPAGE'] : get_bloginfo('wpurl');
		$to = wp_login_url( $return_to );
		return $to;
	}

	public function login()
	{
		$to = $this->login_url();
		$this->redirect($to);
		exit;
	}

	public function logout_url()
	{
		$app = $this->config->item('nts_app');
		$return_to = isset( $GLOBALS['NTS_CONFIG'][$app]['FRONTEND_WEBPAGE'] ) ? $GLOBALS['NTS_CONFIG'][$app]['FRONTEND_WEBPAGE'] : get_bloginfo('wpurl');
		$to = wp_logout_url( $return_to );
		return $to;
	}

	public function logout()
	{
		$to = $this->logout_url();
		$this->redirect($to);
		exit;
	}

	public function password_url()
	{
		$app = $this->config->item('nts_app');
		$return_to = isset( $GLOBALS['NTS_CONFIG'][$app]['FRONTEND_WEBPAGE'] ) ? $GLOBALS['NTS_CONFIG'][$app]['FRONTEND_WEBPAGE'] : get_bloginfo('wpurl');
		$to = get_edit_user_link() . '#password';
		return $to;
	}

	public function password()
	{
		$to = $this->password_url();
		$this->redirect($to);
		exit;
	}
}
