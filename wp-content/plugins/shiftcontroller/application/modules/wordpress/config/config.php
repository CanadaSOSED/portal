<?php
// set timezone
if( function_exists('get_option') )
{
	$tz = get_option('timezone_string');
	if( strlen($tz) ){
		date_default_timezone_set( $tz );
	}
}
?>