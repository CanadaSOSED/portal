<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       soscampus.com
 * @since      1.0.0
 *
 * @package    Sos_Leaderboard
 * @subpackage Sos_Leaderboard/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sos_Leaderboard
 * @subpackage Sos_Leaderboard/admin
 * @author     SOS Development Team <briancaicco@gmail.com>
 */
class Sos_Leaderboard_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {


		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sos-leaderboard-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sos-leaderboard-admin.js', array( 'jquery' ), $this->version, false );

	}


	/**
	 * Register the menu for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function add_menu_item() {

		add_menu_page( 'SOS Leaderboard', 'Leaderboard', 'manage_options', 'sos-leaderboard', '', 'dashicons-chart-bar', 25 );

	}



}
