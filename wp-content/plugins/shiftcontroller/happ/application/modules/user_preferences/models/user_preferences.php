<?php
include_once( NTS_SYSTEM_APPPATH . '/core/MY_Model.php' );
class User_Preferences_HC_Model extends MY_Model_Virtual
{
	protected $cookie_name = 'hc_user_pref';
	protected $cookie_expire = 3600;
	protected $data = array();
	protected $changed = array();

	public function __construct()
	{
		$this->cookie_expire = 365*24*60*60;
		$this->data = $this->get_all();
	}

	public static function get_instance()
	{
		static $instance = null;
		if( null === $instance ){
			$my_class = get_class();
			$instance = new $my_class();
		}
		return $instance;
	}

	public function get_all()
	{
		$return = array();

		if( isset($_COOKIE[$this->cookie_name]) ){
			$str = $_COOKIE[$this->cookie_name];

			if (
				( ! is_php('5.4') && get_magic_quotes_gpc() )
				OR
				( isset($GLOBALS['NTS_IS_PLUGIN']) && ($GLOBALS['NTS_IS_PLUGIN'] == 'wordpress') )
				){
				$str = stripslashes($str);
			}
			$return = @unserialize( $str );
		}
		if( ! is_array($return) ){
			$return = array();
		}
		return $return;
	}

	public function get( $name )
	{
		$return = NULL;
		if( array_key_exists($name, $this->data) ){
			$return = $this->data[$name];
		}
		return $return;
	}

	public function set( $name, $value )
	{
		if( 
			(! array_key_exists($name, $this->data)) OR
			($this->data[$name] !== $value)
		){
			$this->changed[$name] = 1;
		}
		if( $value === NULL ){
			unset( $this->data[$name] );
		}
		else {
			$this->data[$name] = $value;
		}
	}

	public function changed(){
		return array_keys($this->changed);
	}

	public function write(){
		$changed = $this->changed();
		if( ! $changed ){
			return TRUE;
		}

		$save = serialize( $this->data );
		if( ! headers_sent() ){
			setcookie( $this->cookie_name, $save, time() + $this->cookie_expire );
		}
		return TRUE;
	}
}