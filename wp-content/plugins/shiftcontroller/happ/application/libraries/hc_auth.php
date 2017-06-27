<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if( ! defined('HC_USER_LOGIN_HASH') ){
	define('HC_USER_LOGIN_HASH', 'hc_user_hash');
}

class Hc_auth
{
	var $user = NULL;
	var $error = NULL;
	var $auth_model = NULL;

	function __construct()
	{
		$this->load->library( array('session') );
		if( $this->db->table_exists('users') ){
			$this->auth_model = HC_App::model('user');
		}
	}

	public function __get($var)
	{
		return ci_get_instance()->$var;
	}

	public function check()
	{
		$user_id = 0;
		$user_id = $this->session->userdata('user_id');
		if( is_array($user_id) ){
			$user_id = array_shift( $user_id );
		}

		if( ! $user_id ){
			if( isset($_COOKIE[HC_USER_LOGIN_HASH]) ){
				$hash = $_COOKIE[HC_USER_LOGIN_HASH];
				if( $this->auth_model ){
				/* find user with this hash */
					$this->auth_model
						->where('login_hash', $hash)
						->limit(1)
						->get()
					;
					if( $this->auth_model->exists() ){
						$user_id = $this->auth_model->id;
						$this->login( $user_id, TRUE );
					}
				}
			}
		}

		if( ! isset($_SESSION['NTS_SESSION_REF']) ){
			$user_id = 0;
			return $user_id;
		}

		return $user_id;
	}

	public function forgotten_password( $email )
	{
		$this->auth_model->get_by_email( $email );
		if( $this->auth_model->exists() ){
			$new_password = mt_rand( 100000, 999999 );
			$user = $this->auth_model->all[0];
			$user->password = $new_password;
			$user->confirm_password = $new_password;

			if( $user->save() ){
				$msg = array();
				$msg['email'] = HCM::__('Email') . ': ' . $email;
				$msg['password'] = HCM::__('Password') . ': ' . $new_password;

				$messages = HC_App::model('messages');
				$messages->send( 
					'user.password_changed',
					$user,
					array("msg" => $msg)
					);
				return TRUE;
			}
			else {
				$this->error = $this->auth_model->string;
				return FALSE;
			}
		}
		else {
			$this->error = sprintf( HCM::__('This email address %s was not found'), $email );
			return FALSE;
		}
	}

	public function change_password( $new_password )
	{
		$user = $this->user();
		$user->password = $new_password;
		$user->confirm_password = $new_password;

		if( $user->save() ){
			return TRUE;
		}
		else {
			$this->error = $this->auth_model->string;
			return FALSE;
		}
	}

	public function attempt( $identity, $password, $remember = FALSE )
	{
		$app_conf = HC_App::app_conf();
		$login_with = $app_conf->get('login_with');

		if( $login_with != 'username' ){
			$identity_name = 'email';
		}
		else {
			$identity_name = 'username';
		}
		$where = array(
			$identity_name	=> $identity,
			);

		$this->auth_model->from_array( $where );
		if( $this->auth_model->check_password($password) ){
			$this->login( $this->auth_model->id, $remember );
			return TRUE;
		}
		else {
			return FALSE;
		}
	}

	public function login( $user_id, $remember = FALSE )
	{
		$current_user_id = $this->session->userdata('user_id');
		if( is_array($current_user_id) ){
			$current_user_id = array_shift( $current_user_id );
		}

		if( 
			$user_id
			&&
			(
				$user_id != $current_user_id
				OR
				(! isset($_SESSION['NTS_SESSION_REF']))
			)
			)
		{
			$session_data = array(
				'user_id'	=> $user_id
				);
			$this->session->set_userdata($session_data);
			$_SESSION['NTS_SESSION_REF'] = hc_random(16);
			$this->user = NULL;

			if( $remember ){
				$rand = hc_random(12);
				$login_cookie_hash = md5(sha1($user_id . $rand));
				setcookie( HC_USER_LOGIN_HASH, $login_cookie_hash, time() + 365*24*60*60 );
				$this->auth_model->login_hash = $login_cookie_hash;
				$this->auth_model->save();
			}

			if( method_exists($this->auth_model, 'trigger_event') ){
				$this->auth_model->trigger_event( 'after_login' );
			}
		}
		return TRUE;
	}

	public function user( $force_id = NULL )
	{
		if( $force_id !== NULL ){
			$return = $this->auth_model;
			$return->clear();
			$return->get_by_id( $force_id );
			return $return;
		}

		if( NULL == $this->user ){
			$user_id = $this->check();
			if( $user_id && $this->auth_model ){
				$this->auth_model->get_by_id( $user_id );
				if( $this->auth_model->exists() ){
					$this->user = $this->auth_model->all[0];
				}
			}
			else {
				$this->user = $this->auth_model;
			}
		}

		if( NULL == $this->user ){
			if( $this->auth_model ){
				$this->user = $this->auth_model->get_by_id(0);
			}
		}

		return $this->user;
	}

	public function reset_user()
	{
		$this->user = NULL;
	}

	public function logout()
	{
		$user = $this->user();
		$user->login_hash = '';
		$user->save();

		$this->session->unset_userdata('user_id');
	}
}