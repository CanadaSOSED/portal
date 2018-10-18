<?php
/*
Plugin Name: Announce from the Dashboard
Description: Announcement to the dashboard screen for users.
Plugin URI: http://wordpress.org/extend/plugins/announce-from-the-dashboard/
Version: 1.5.1
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/
Text Domain: afd
Domain Path: /languages
*/

/*  Copyright 2012 gqevu6bsiz (email : gqevu6bsiz@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( !defined( 'ABSPATH' ) ) {
	
	exit; // Exit if accessed directly
	
}

if ( !class_exists( 'Afd' ) ) :

final class Afd
{

	public $name;
	public $ver;
	public $plugin_slug;
	public $main_slug;
	public $ltd;

	public $plugin_dir;
	public $plugin_url;

	public $Plugin;
	public $Form;
	public $Site;
	public $Env;
	public $User;
	public $Links;
	public $ThirdParty;

	public $Api;
	public $Helper;

    public function __construct() {
		
		$this->Plugin     = new stdClass;
		$this->Form       = new stdClass;
		$this->Site       = new stdClass;
		$this->Env        = new stdClass;
		$this->User       = new stdClass;
		$this->Links      = new stdClass;
		$this->ThirdParty = new stdClass;

		$this->Api        = new stdClass;
		$this->Helper     = new stdClass;
		
	}

	public function init()
	{
		
		add_action( 'plugins_loaded' , array( $this , 'plugins_loaded' ) , 20 );
		add_action( 'setup_theme' , array( $this , 'setup_theme' ) , 20 );
		add_action( 'after_setup_theme' , array( $this , 'after_setup_theme' ) , 20 );
		add_action( 'init' , array( $this , 'wp_init' ) , 20 );
		add_action( 'wp_loaded' , array( $this , 'wp_loaded' ) , 20 );
		
	}

	public function plugins_loaded()
	{
		
		$this->define_constants();
		$this->includes();

		do_action( $this->ltd . '_plugins_loaded' );
		
	}

	private function define_constants()
	{
		
		$this->name        = 'Announce from the Dashboard';
		$this->ver         = '1.5.1';
		$this->plugin_slug = 'announce-from-the-dashboard';
		$this->main_slug   = 'announce_from_the_dashboard';
		$this->ltd         = 'afd';

        $this->plugin_dir  = plugin_dir_path( __FILE__ );
        $this->plugin_url  = plugin_dir_url( __FILE__ );
		
		load_plugin_textdomain( $this->ltd , false , $this->plugin_slug . '/languages' );

		include_once( $this->plugin_dir . 'core/api.php' );
		include_once( $this->plugin_dir . 'core/helper.php' );

		$this->Api    = new Afd_Api();
		$this->Helper = new Afd_Helper();
		
	}

	private function includes()
	{

		$includes = array(
			'core/init.php',
			'core/init_add.php',
			'third_party/third_party.php',
			'admin/master.php',
		);
		
		$this->Helper->includes( $includes );
		
	}

	public function setup_theme()
	{
		
		do_action( $this->ltd . '_setup_theme' );

	}
	
	public function after_setup_theme()
	{
		
		do_action( $this->ltd . '_after_setup_theme' );

	}
	
	public function wp_init()
	{
		
		do_action( $this->ltd . '_init' );
		
	}
	
	public function wp_loaded()
	{
		
		do_action( $this->ltd . '_after_init' );
		
	}
	
}

$GLOBALS['Afd'] = new Afd();
$GLOBALS['Afd']->init();

endif;

