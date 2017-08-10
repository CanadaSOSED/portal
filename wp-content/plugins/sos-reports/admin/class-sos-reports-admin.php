<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       canadasos.com
 * @since      1.0.0
 *
 * @package    Sos_Reports
 * @subpackage Sos_Reports/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Sos_Reports
 * @subpackage Sos_Reports/admin
 * @author     SOS Development Team <briancaicco@gmail.com>
 */
class Sos_Reports_Admin {

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
		 * Adds a settings page link to a menu
		 *
		 * @link 		https://codex.wordpress.org/Administration_Menus
		 * @since 		1.0.0
		 * @return 		void
		 */
	public function add_menu() {

		add_menu_page( 'SOS Reports', 'SOS Reports', 'manage_sites', 'sos-reports', array( $this, 'sos_reports_panel'), 'dashicons-chart-bar', 75 );
		//add_submenu_page( 'sos-reports', 'Reports', 'Reports', 'manage_sites', 'sos-reports');
	}



	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sos_Reports_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sos_Reports_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/sos-reports-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Sos_Reports_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Sos_Reports_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/sos-reports-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add reports panel content
	 *
	 * @since 1.0.0
	 */
	public function sos_reports_panel() {
		include_once 'partials/sos-reports-admin-display.php';
	}

}
