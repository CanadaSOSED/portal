<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_Preferences_HC_Controller extends _Front_HC_Controller
{
	function get( $prefix, $keys = array() )
	{
		$return = array();
		$model = HC_App::model('user_preferences');
		if( ! is_array($keys) ){
			$keys = array( $keys );
		}
		foreach( $keys as $k ){
			$key = strlen($prefix) ? $prefix . '/' . $k : $k;
			$value = $model->get( $key );
			if( $value !== NULL ){
				$return[$k] = $value;
			}
		}
		return $return;
	}

	function save( $prefix, $watch, $post )
	{
		$model = HC_App::model('user_preferences');

		foreach( $watch as $k ){
			$key = strlen($prefix) ? $prefix . '/' . $k : $k;
			$value = NULL;
			if( array_key_exists($k, $post) ){
				$value = $post[$k];
			}
			$model->set( $key, $value );
		}

		$return = TRUE;
		return $return;
	}
}