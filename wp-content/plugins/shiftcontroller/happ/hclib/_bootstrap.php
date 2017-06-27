<?php
include_once( dirname(__FILE__) . '/hc_lib.php' );
include_once( dirname(__FILE__) . '/hc_html.php' );
include_once( dirname(__FILE__) . '/form2.php' );
include_once( dirname(__FILE__) . '/hc_validator.php' );

if( file_exists(dirname(__FILE__) . '/hc_time.php') ){
	include_once( dirname(__FILE__) . '/hc_time.php' );
}
if( file_exists(dirname(__FILE__) . '/extensions.php') ){
	include_once( dirname(__FILE__) . '/extensions.php' );
}
if( file_exists(dirname(__FILE__) . '/acl.php') ){
	include_once( dirname(__FILE__) . '/acl.php' );
}
if( file_exists(dirname(__FILE__) . '/filter_ui.php') ){
	include_once( dirname(__FILE__) . '/filter_ui.php' );
}