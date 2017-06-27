<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require NTS_SYSTEM_APPPATH."third_party/MX/Router.php";

//class MY_Router extends CI_Router {
class MY_Router extends MX_Router {
	function get_controller_class( $class )
	{
		$class = explode('/', $class);
		$short_class = $class[ count($class)-1 ];
		$class = join( '_', array_reverse($class) );
		return array( $class, $short_class );
	}

	function _set_request( $segments = array() )
	{
		$segments = str_replace('-', '_', $segments);

		$segments = $this->_validate_request($segments);
		if (count($segments) == 0){
			return $this->_set_default_controller();
		}

		$class = $segments[0];
		if( strpos($class, '/') !== FALSE ){
			list( $class, $short_class ) = $this->get_controller_class($class);
			$segments[0] = $short_class;
			}

		$this->set_class($class);

		if (isset($segments[1])){
			// A standard method request
			$this->set_method($segments[1]);
		}
		else {
			// This lets the "routed" segment array identify that the default
			// index method is being used.
			$segments[1] = 'index';
		}

		// Update our "routed" segment array to contain the segments.
		// Note: If there is no custom routing, this array will be
		// identical to $this->uri->segments
		$this->uri->rsegments = $segments;
	}

/* returns the controller's file name */
	function controller_file_name()
	{ 
		$return = $this->class;
		$base_suffix = $this->config->item('controller_suffix');

		$remove_suffix = '';

		$this_prefix = array();
		if( strlen($this->module) ){
			$this_prefix = array_merge($this_prefix, array_filter(explode('/', $this->module)));
			if( strlen($this->reldirectory) ){
				$this_prefix = array_merge($this_prefix, array_filter(explode('/', $this->reldirectory)));
			}
		}
		elseif( strlen($this->directory) ){
			$this_prefix = array_merge($this_prefix, array_filter(explode('/', $this->directory)));
		}

/*
		if( strlen($this->module) ){
			$this_prefix = array_filter( explode('/', $this->module) );
		}
		else if( strlen($this->directory) ){
			$this_prefix = array_filter( explode('/', $this->directory) );
		}
*/

		$this_prefix = join('_', array_reverse($this_prefix));

		if( strlen($this_prefix) ){
			$remove_suffix = '_' . $this_prefix;
		}

		if( strlen($base_suffix) ){
			$remove_suffix = $remove_suffix . $base_suffix;
		}

		if( $remove_suffix ){
			$return = substr( $return, 0, -strlen($remove_suffix) );
			if( ! strlen($return) && strlen($this->module) ){ // default controller of a module
				$return = $this->module;
			}
		}
		return $return;
	}
}
