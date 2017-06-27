<?php
/* copy some base stuff from CI_Model not to explicitely initialize MY_Model */

//class App_conf_model extends CI_model
class App_Conf_HC_Model
{
	private $with_db = FALSE;
	private $saved = array();
	private $db = NULL;
	private $config = NULL;

	public static function get_instance()
	{
		static $instance = null;
		if( null === $instance ){
			$instance = new App_Conf_HC_Model;
		}
		return $instance;
	}

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		$CI =& ci_get_instance();
		$this->db = $CI->db;
		$this->config = $CI->config;

		if( $this->db->table_exists('conf') ){
			$this->with_db = TRUE;
			$this->saved = $this->_get_all();
		}

		$this->config->load('settings', TRUE);
		$this->config->load('conf', TRUE );
	}

	public function conf( $pname )
	{
		// $return = $this->config->item( $pname, 'settings' );
		$return = $this->config->item( $pname, 'conf' );
		return $return;
	}

	public function get( $pname )
	{
		if( isset($this->saved[$pname]) ){
			$return = $this->saved[$pname];
		}
		else {
			$setting = $this->config->item( $pname, 'settings' );
			$return = isset($setting['default']) ? $setting['default'] : NULL;
		}
		return $return;
	}

	public function set( $pname, $pvalue )
	{
		return $this->_save( $pname, $pvalue );
	}

	public function reset( $pname )
	{
		return $this->_delete( $pname );
	}

	private function _get_all( )
	{
		$return	= array();
		if( ! $this->with_db ){
			return $return;
			}

		$this->db->select('name, value');
		$result	= $this->db->get('conf');

		foreach($result->result_array() as $i){
			if( isset($return[$i['name']]) ){
				if( ! is_array($return[$i['name']]) )
					$return[$i['name']] = array( $return[$i['name']] );
				if( ! in_array($i['value'], $return[$i['name']]) )
					$return[$i['name']][] = $i['value'];
			}
			else {
				$return[$i['name']] = $i['value'];
			}
		}
		return $return;
	}

	private function _save( $pname, $pvalue )
	{
		$return	= TRUE;
		if( ! $this->with_db ){
			return $return;
			}

		if( is_array($pvalue) ){
			$this->db->where( 'name', $pname );
			$this->db->select('name, value');
			$result	= $this->db->get('conf');

			$current = array();
			foreach($result->result_array() as $i){
				$current[] = $i['value'];
			}

			$to_delete = array_diff( $current, $pvalue );
			$to_add = array_diff( $pvalue, $current );
			foreach( $to_add as $v ){
				$item = array(
					'name'	=> $pname,
					'value'	=> $v
					);
				$this->db->insert('conf', $item);
			}
			foreach( $to_delete as $v ){
				$this->db->where('name', $pname);
				$this->db->where('value', $v);
				$this->db->delete('conf');
			}
		}
		else
		{
			if( $this->db->get_where('conf', array('name'=>$pname))->row_array() ){
				$item = array(
					'value'	=> $pvalue
					);
				$this->db->where('name', $pname);
				$this->db->update('conf', $item);
			}
			else {
				$item = array(
					'name'	=> $pname,
					'value'	=> $pvalue
					);
				$this->db->insert('conf', $item);
			}
		}
	}

	private function _delete( $pname )
	{
		$return	= TRUE;
		if( ! $this->with_db ){
			return $return;
			}

		$this->db->where('name', $pname);
		$this->db->delete('conf', $item);
	}
}