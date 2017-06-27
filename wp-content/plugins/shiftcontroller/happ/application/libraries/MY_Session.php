<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Session extends CI_Session {
	/* here we overwrite flash data operations */
	var $my_prefix = 'nts_';
	var $builtin_props = array(
		'session_id',
		'ip_address',
		'user_agent',
		'last_activity',
		'user_data'
		);

	public function __construct($params = array())
	{
		if( session_id() == '' )
		{
			@session_start();
		}
		parent::__construct( $params );
	}

	function all_userdata()
	{
		$return = array();
		/* get flash data we store in _SESSION */
		if( is_array($_SESSION) ){
			foreach( $_SESSION as $key => $v ){
				if( ! (substr($key, 0, strlen($this->my_prefix)) == $this->my_prefix) )
					continue;
				$my_key = substr($key, strlen($this->my_prefix) );
				$return[ $my_key ] = $v;
			}
		}

		$parent_return = parent::all_userdata();
		$return = array_merge( $return, $parent_return );
		return $return;
	}

	function userdata($item)
	{
		$my_key = $this->my_prefix . $item;
		if( isset($_SESSION[$my_key]) )
			return $_SESSION[$my_key];
		return parent::userdata($item);
	}

	function unset_userdata($newdata = array())
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => '');
		}

		$parent_newdata = array();
		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
//				if( substr($key, 0, strlen($this->flashdata_key)) == $this->flashdata_key )
				if( ! in_array($key, $this->builtin_props) )
				{
					$my_key = $this->my_prefix . $key;
					unset($_SESSION[$my_key]);
				}
				else
				{
					$parent_newdata[ $key ] = $val;
				}
			}
		}

		if( $parent_newdata )
		{
			parent::unset_userdata( $parent_newdata );
		}
	}

	function add_flashdata($newdata = array(), $newval = '')
	{
		return $this->set_flashdata( $newdata, $newval, TRUE );
	}

	function set_flashdata($newdata = array(), $newval = '', $append = FALSE)
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0)
		{
			foreach ($newdata as $key => $val)
			{
				$flashdata_key = $this->flashdata_key.':new:'.$key;
				$this->set_userdata($flashdata_key, $val, $append);
			}
		}
	}

	function set_userdata($newdata = array(), $newval = '', $append = FALSE)
	{
		if (is_string($newdata))
		{
			$newdata = array($newdata => $newval);
		}

		$parent_newdata = array();
		if (count($newdata) > 0)
		{
			$parent_newdata = array();
			foreach ($newdata as $key => $val)
			{
				if( ! in_array($key, $this->builtin_props) )
				{
					$my_key = $this->my_prefix . $key;
					if( $append )
					{
						if( ! isset($_SESSION[$my_key]) )
							$_SESSION[$my_key] = array();
						if( ! is_array($_SESSION[$my_key]) )
							$_SESSION[$my_key] = array( $_SESSION[$my_key] );
						$_SESSION[$my_key][] = $val;
					}
					else
					{
						$_SESSION[$my_key] = $val;
					}
				}
				else
				{
					$parent_newdata[ $key ] = $val;
				}
			}
		}

		if( $parent_newdata )
		{
			parent::set_userdata( $parent_newdata );
		}
	}
}
