<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$config = array();

/* this file defines model relationships runtime if needed */
$CI =& ci_get_instance();
if( $CI->hc_modules->exists('notes') ){
	$config['note']['has_one']['shift'] = array(
		'class'			=> 'shift',
		'other_field'	=> 'note',
		);
	$config['shift']['has_many']['note'] = array(
		'class'			=> 'note',
		'other_field'	=> 'shift',
		);

	$config['user']['has_many']['note'] = array(
		'class'			=> 'note',
		'other_field'	=> 'author',
		);
}