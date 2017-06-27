<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Hc_modules
{
	var $modules = array();

	function __construct()
	{
		$CI =& ci_get_instance();
		$this->modules = $CI->config->get_modules();
	}

	function exists( $path )
	{
		$return = 
			in_array($path, $this->modules) && 
			$this->module_dir( $path )
//			Modules::exists($path)
			;
		return $return;
	}

	function module_dir( $module )
	{
		$return = NULL;
		$CI =& ci_get_instance(); 
		$modules_locations = $CI->config->item('modules_locations');
		reset( $modules_locations );
		foreach( $modules_locations as $ml ){
			$mod_dir = $ml . $module;
			if( file_exists($mod_dir) ){
				$return = $mod_dir;
				break;
			}
		}
		return $return;
	}
}
