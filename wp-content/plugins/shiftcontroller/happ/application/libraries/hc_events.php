<?php
class Hc_events 
{
	var $events;

	function __construct()
	{
		$CI =& ci_get_instance();
		$CI->config->load( 'events', TRUE );
		$this->events = $CI->config->item( 'events' );
		// _print_r( $this->events );
	}

	function trigger( $event, $payload )
	{
		$CI =& ci_get_instance();

		$check_events = array();
		$check_events[] = $event;

		/* also check events for all objects */
		list( $class, $short_event ) = explode( '.', $event );
		if( $class != '*' ){
			$generic_event = '*.' . $short_event;
			$check_events[] = $generic_event;
		}

		foreach( $check_events as $this_event ){
			if( ! isset($this->events[$this_event]) ){
				continue;
			}

			$args = func_get_args();
			array_shift( $args );

			reset( $this->events[$this_event] );
			foreach( $this->events[$this_event] as $call ){
				if( is_callable($call) ){
					call_user_func_array( $call, $args );
//					$call( $args );
				}
				else {
					if( $CI->load->module_file($call['file']) ){
						if( ! class_exists($call['class']) ){
							// if class doesn't exist check that the function is callable
							// could be just a helper function
							if(is_callable($call['method'])){
								if( isset($call['attr']) ){
									$args[] = $call['attr'];
								}
								call_user_func_array( $call['method'], $args );
							}
							continue;
						}

						$class = new $call['class'];

						if( ! is_callable( array($class, $call['method']) )){
							unset($class);
							continue;
						}

						if( isset($call['attr']) ){
							$args[] = $call['attr'];
						}
						call_user_func_array( array($class, $call['method']), $args );
						unset($class);
					}
					else {
					}
				}
			}
		}
	}
}