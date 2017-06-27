<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require( dirname(__FILE__) . '/_app-common.php' );

/* base stuff */
$config['nts_app_title'] = 'ShiftController';
$config['nts_app_url'] = 'http://www.shiftcontroller.com';

$config['nts_promo_url'] = 'http://www.shiftcontroller.com/order/';
$config['nts_promo_title'] = 'ShiftController Pro';
$config['nts_track_setup'] = '16:2';

$config['modules'] = array_merge( $config['modules'], array(
	'wordpress',
	)
);