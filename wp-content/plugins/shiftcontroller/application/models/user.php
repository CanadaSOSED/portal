<?php
include_once( NTS_SYSTEM_APPPATH . '/core/MY_Model.php' );
class User_HC_Model extends MY_model
{
	const LEVEL_STAFF = 1;
	const LEVEL_MANAGER = 2;
	const LEVEL_ADMIN = 3;

	const STATUS_ACTIVE = 1;
	const STATUS_ARCHIVE = 0;

	var $salt_length = 10;
	var $table = 'users';
	var $default_order_by = array('active' => 'DESC', 'last_name' => 'ASC', 'first_name' => 'ASC');

	var $has_many = array(
		'shift' => array(
			'class'			=> 'shift',
			'other_field'	=> 'user',
			),
		);

	var $validation = array(
		'first_name'	=> array('required', 'trim', 'max_length' => 50),
		'last_name'		=> array('trim', 'max_length' => 50),
		'email'			=> array('required', 'trim', 'valid_email', 'unique'),
		'username'		=> array('default_username', 'required', 'trim', 'unique'),
		'password'		=> array('required', 'trim', 'hash_password'),
		'confirm_password'	=> array('required', 'trim', 'hash_password', 'matches' => 'password'),
		'token'			=> array('make_token'),
		'level'	=> array(
			'enum' => array(
				self::LEVEL_STAFF,
				self::LEVEL_MANAGER,
				self::LEVEL_ADMIN
				)
			),
		'active'	=> array(
			'enum' => array(
				self::STATUS_ACTIVE,
				self::STATUS_ARCHIVE
				)
			),
		);

	public function get_admins( $shift = NULL )
	{
		$this
			->where_in( 'level', array($this->_const('LEVEL_MANAGER'), $this->_const('LEVEL_ADMIN')) )
			->where( 'active', $this->_const('STATUS_ACTIVE') )
			;
		$this->get();
		return $this;
	}

	public function auth_token()
	{
		if( ! strlen($this->token) ){
			$this->token = HC_Lib::generate_rand();
			$this->save();
		}
		return $this->token;
	}

	public function get_staff()
	{
		$app_conf = HC_App::app_conf();
		$working_levels = $app_conf->get('staff:working_levels');

		$this->clear();
	/* get those users who can be assigned to shifts */
		$this->where('active', self::STATUS_ACTIVE);
		if( $working_levels ){
			if( ! is_array($working_levels) )
				$working_levels = array( $working_levels );
			$this->where_in('level', $working_levels);
		}
		$this->get();
		return $this;
	}

	public function delete($object = '', $related_field = '')
	{
	// if something is given, then just pass it over, the caller must be knowing what he's doing
		if( $object ){
			return parent::delete( $object, $related_field );
		}
	// if empty then delete all has_many and has_one
		else {
			$has = array_merge( array_keys($this->has_one), array_keys($this->has_many) );
			foreach ( $has as $rfield ){
				$this->{$rfield}->get()->delete_all();
			}
			return parent::delete();
		}
	}

/* check password */
	function check_password( $pass )
	{
		if( isset($this->username) && strlen($this->username) ){
			$this->get_by_username( $this->username );
		}
		else {
			$this->get_by_email( $this->email );
		}

		if ( ! $this->exists() ){
			return FALSE;
		}

		$this->salt = substr($this->password, 0, $this->salt_length);
		$this->password = $pass;

		$this->validate();
		$this->get();

		if ( ! $this->exists() ){
			return FALSE;
		}
		return TRUE;
	}

/* validation methods */
	function _hash_password( $field )
	{
		if (!empty($this->{$field})){
			// Generate a random salt if empty
			if (empty($this->salt)){
				$this->salt = substr( md5(uniqid(rand(), true)), 0, $this->salt_length );
			}
			$this->{$field} =  $this->salt . substr( sha1($this->salt . $this->{$field}), 0, -$this->salt_length );
		}
	}

	function _make_token( $field )
	{
		if( empty($this->{$field}) ){
			$this->{$field} = HC_Lib::generate_rand();
		}
	}

	function _default_username( $field )
	{
		if( empty($this->{$field}) ){
			$this->{$field} = $this->email;
		}
	}
}