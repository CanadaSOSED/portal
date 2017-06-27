<?php
/**
 * @package ShiftController
 * @author HitCode
 */
/*
Plugin Name: ShiftController
Plugin URI: http://www.shiftcontroller.com/
Description: Staff scheduling plugin.
Author: HitCode
Version: 3.2.4
Author URI: http://www.hitcode.com/
Text Domain: shiftcontroller
*/

if( file_exists(dirname(__FILE__) . '/db.php') )
{
	$nts_no_db = TRUE;
	include_once( dirname(__FILE__) . '/db.php' );
}

if( defined('NTS_DEVELOPMENT') )
	$happ_path = NTS_DEVELOPMENT;
else
	$happ_path = dirname(__FILE__) . '/happ';
include_once( $happ_path . '/hclib/hcWpBase.php' );

register_uninstall_hook( __FILE__, array('ShiftController', 'uninstall') );

class ShiftController extends hcWpBase5
{
	public function __construct()
	{
		parent::__construct(
			strtolower(get_class()),
			__FILE__,
			'',
			'ci'
			);
		$this->query_prefix = '?/';
	}

	public function admin_menu()
	{
		parent::admin_menu();

		$menu_title = ucfirst($this->app);
		$page = add_menu_page(
			$menu_title,
			$menu_title,
			'read',
			$this->slug,
			array( $this, 'admin_view' ),
			'dashicons-calendar'
			);
	}

	static function uninstall( $prefix = 'shiftcontroller', $watch_other = array() )
	{
		$prefix = 'shiftcontroller';
		$watch_other = array('shiftcontroller-pro.php');
		hcWpBase5::uninstall( $prefix, $watch_other );
	}
}

$sh = new ShiftController();

/*
Can pass the following arguments for the shortcode:
	location:
		one, comma separated list

	staff:
		one, comma separated list, _current_user_id_

	date:
		date like 20150712, or range like 20150712_20150719

	range:
		week, month
*/

?>