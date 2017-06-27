<?php

/**
 * @link              http://ran.ge
 * @since             2.0.0
 * @package           Shiftee Basic
 *
 * @wordpress-plugin
 * Plugin Name:       Shiftee Basic
 * Plugin URI:        https://shiftee.co
 * Description:       Complete staff schedule management system: create and display schedule, let employees clock in and out, report expenses.
 * Version:           2.1.0
 * Author:            Range
 * Author URI:        http://ran.ge
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       employee-scheduler
 * Domain Path:       /languages
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 */
function activate_shiftee_basic() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-shiftee-basic-activator.php';
	Shiftee_Basic_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_shiftee_basic() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-shiftee-basic-deactivator.php';
	Shiftee_Basic_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_shiftee_basic' );
register_deactivation_hook( __FILE__, 'deactivate_shiftee_basic' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-shiftee-basic.php';

/**
 * Begins execution of the plugin.
 *
 * @since    2.0.0
 */
function run_shiftee_basic() {

	$plugin = new Shiftee_Basic();
	$plugin->run();

}
run_shiftee_basic();

/**
 * Check for WPP2P.
 *
 * Check whether the WP Posts 2 Posts functionality is already running on the site, and if not, load WPP2P and define constants.
 *
 * @since 1.0
 *
 */
add_action( 'admin_init', 'shiftee_p2p_check' );

function shiftee_p2p_check() {
	if ( !is_plugin_active( 'posts-to-posts/posts-to-posts.php' ) ) {
		if ( !class_exists( 'P2P_Autoload' ) ) {
			require_once dirname( __FILE__ ) . '/libraries/wpp2p/autoload.php';
		}
		if( !defined( 'P2P_PLUGIN_VERSION') ) {
			define( 'P2P_PLUGIN_VERSION', '1.6.3' );
		}
		if( !defined( 'P2P_TEXTDOMAIN') ) {
			define( 'P2P_TEXTDOMAIN', 'employee-scheduler' );
		}
	}
}


/**
 * Load P2P.
 *
 * Load and initialize the classes for WP Posts to Posts.
 *
 * @since 1.0
 *
 * @see P2P_Autoload
 * @link https://github.com/scribu/wp-posts-to-posts/blob/master/posts-to-posts.php
 *
 */
function shiftee_p2p_load() {
	if ( !class_exists( 'P2P_Autoload' ) ) {
		//load_plugin_textdomain( P2P_TEXTDOMAIN, '', basename( dirname( __FILE__ ) ) . '/languages' );
		if ( !function_exists( 'p2p_register_connection_type' ) ) {
			require_once dirname( __FILE__ ) . '/libraries/wpp2p/autoload.php';
		}
		P2P_Storage::init();
		P2P_Query_Post::init();
		P2P_Query_User::init();
		P2P_URL_Query::init();
		P2P_Widget::init();
		P2P_Shortcodes::init();
		register_uninstall_hook( __FILE__, array( 'P2P_Storage', 'uninstall' ) );
		if ( is_admin() )
			shiftee_load_admin();
	}
}

/**
 * Load WPP2P Admin.
 *
 * Load and initialize the classes for WP Posts to Posts.
 *
 * @since 1.0
 *
 * @see P2P_Autoload
 * @link https://github.com/scribu/wp-posts-to-posts/blob/master/posts-to-posts.php
 *
 */
function shiftee_load_admin() {
	P2P_Autoload::register( 'P2P_', dirname( __FILE__ ) . '/libraries/wpp2p/admin' );

	new P2P_Box_Factory;
	new P2P_Column_Factory;
	new P2P_Dropdown_Factory;

	new P2P_Tools_Page;
}

/**
 * Initialize WPP2P.
 *
 * Load and initialize WP Posts to Posts.
 *
 * @since 1.0
 *
 * @see P2P_Autoload
 * @link https://github.com/scribu/wp-posts-to-posts/blob/master/posts-to-posts.php
 *
 */
function shiftee_p2p_init() {
	// Safe hook for calling p2p_register_connection_type()
	do_action( 'p2p_init' );
}

require dirname( __FILE__ ) . '/libraries/wpp2p/scb/load.php';
scb_init( 'shiftee_p2p_load' );
add_action( 'wp_loaded', 'shiftee_p2p_init' );

/**
 * Create connections.
 *
 * Use WPP2P to create connections between shifts, jobs, expenses, and employees.
 *
 * @since 1.0
 *
 * @see WPP2P
 * @link https://github.com/scribu/wp-posts-to-posts/blob/master/posts-to-posts.php
 *
 */
add_action( 'p2p_init', 'shiftee_create_connections' );

function shiftee_create_connections() {
	// create the connection between shifts and employees (users)
	p2p_register_connection_type( array(
		'name' => 'shifts_to_employees',
		'from' => 'shift',
		'to' => 'user',
		'cardinality' => 'many-to-one',
		'admin_column' => 'from',
		'to_labels' => array(
			'singular_name' => __( 'Staff', 'employee-scheduler' ),
			'search_items' => __( 'Search staff', 'employee-scheduler' ),
			'not_found' => __( 'No staff members found.', 'employee-scheduler' ),
			'create' => __( 'Choose staff member', 'employee-scheduler' ),
		),
		'title' => array(
			'from' => __( 'Assigned Staff', 'my-textdomain' ),
		),
	) );
	// create the connection between expenses and employees (users)
	p2p_register_connection_type( array(
		'name' => 'expenses_to_employees',
		'from' => 'expense',
		'to' => 'user',
		'cardinality' => 'many-to-one',
		'admin_column' => 'from',
		'to_labels' => array(
			'singular_name' => __( 'Staff', 'employee-scheduler' ),
			'search_items' => __( 'Search staff', 'employee-scheduler' ),
			'not_found' => __( 'No staff members found.', 'employee-scheduler' ),
			'create' => __( 'Choose staff member', 'employee-scheduler' ),
		),
		'title' => array(
			'from' => __( 'Connected Staff', 'my-textdomain' ),
		),
	) );
	// create the connection between shifts and jobs
	p2p_register_connection_type( array(
		'name' => 'shifts_to_jobs',
		'from' => 'shift',
		'to' => 'job',
		'admin_box' => array(
			'show' => 'from',
			'context' => 'side'
		),
		'cardinality' => 'many-to-one',
		'admin_column' => 'from',
		'to_labels' => array(
			'singular_name' => __( 'Job', 'employee-scheduler' ),
			'search_items' => __( 'Search jobs', 'employee-scheduler' ),
			'not_found' => __( 'No jobs found.', 'employee-scheduler' ),
			'create' => __( 'Choose connected job', 'employee-scheduler' ),
		),
	) );

	// create the connection between expenses and jobs
	p2p_register_connection_type( array(
		'name' => 'expenses_to_jobs',
		'from' => 'expense',
		'to' => 'job',
		'cardinality' => 'many-to-one',
		'admin_column' => 'from',
		'to_labels' => array(
			'singular_name' => __( 'Job', 'employee-scheduler' ),
			'search_items' => __( 'Search jobs', 'employee-scheduler' ),
			'not_found' => __( 'No jobs found.', 'employee-scheduler' ),
			'create' => __( 'Choose connected job', 'employee-scheduler' ),
		),
	) );
}
