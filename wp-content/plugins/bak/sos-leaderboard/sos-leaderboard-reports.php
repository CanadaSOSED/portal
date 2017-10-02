<?php
/**
 * Plugin Name: SOS Leaderboard
 * Plugin URI: 
 * Description: Derp
 * Author: SOS Development Team
 * Author URI:
 * Version: 1.0.0
 * Text Domain: sos-leaderboard
 *
 *
 *
 * @package   SOS-Leaderboard
 * @author    SOS Development Team
 * @category  Admin
 *
 */

defined( 'ABSPATH' ) or exit;

// WC version check
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || version_compare( get_option( 'woocommerce_db_version' ), '2.4.0', '<' ) ) {

	function sos_leaderboard_chart_outdated_version_notice() {

		$message = sprintf(
		/* translators: Placeholders: %1$s and %2$s are <strong> tags. %3$s and %4$s are <a> tags */
			esc_html__( '%1$sDisplays the tops performing schools by revenue and orders%2$s This plugin requires WooCommerce 2.4 or newer. Please %3$supdate WooCommerce to version 2.4 or newer%4$s.', 'sos-leaderboard-chart' ),
			'<strong>',
			'</strong>',
			'<a href="' . admin_url( 'plugins.php' ) . '">',
			'&nbsp;&raquo;</a>'
		);

		echo sprintf( '<div class="error"><p>%s</p></div>', $message );
	}

	add_action( 'admin_notices', 'sos_leaderboard_chart_outdated_version_notice' );
	return;
}



if ( ! class_exists( 'SOS_Leaderboard' ) ) :

add_action( 'plugins_loaded', 'sos_leaderboard_chart' );

/**
 * Sets up the plugin and loads the reporting class
 *
 * @since 1.0.0
 */
class SOS_Leaderboard {


	const VERSION = '1.1.0';

	/** @var SOS_Leaderboard single instance of this plugin */
	protected static $instance;


	/**
	 * SOS_Leaderboard constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// load translations
		add_action( 'init', array( $this, 'load_translation' ) );


		// add styles
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );

		// any frontend actions

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {

			// any admin actions
			add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

			// add plugin links
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_links' ) );

			// run every time
			$this->install();

		}

	}


	/** Plugin methods ***************************************/


	public function admin_styles(){
		wp_register_style( 'sos_leaderboard_styles', plugin_dir_url( __FILE__ ) .'/assets/css/admin.css', array(), '' );
		wp_enqueue_style( 'sos_leaderboard_styles' );
	}

	/**
	 *
	 * @since 1.0.0
	 * @param array $core_reports
	 * @return array the updated reports
	 */

	public function add_plugin_admin_menu() {

	    /*
	     * Add a settings page for this plugin to the Settings menu.
	     *
	     * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
	     *
	     *        Administration Menus: http://codex.wordpress.org/Administration_Menus
	     *
	     */
	    add_menu_page( 'SOS Leaderboard', 'Leaderboard', 'manage_options', 'sos-leaderboard', array($this, 'load_leaderboard'), 'dashicons-chart-bar', 25 );
	}



	/** Helper methods ***************************************/


	/**
	 * Main SOS_Leaderboard Instance, ensures only one instance is/can be loaded
	 *
	 * @since 1.0.0
	 * @see sos_leaderboard_chart()
	 * @return SOS_Leaderboard
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Cloning instances is forbidden due to singleton pattern.
	 *
	 * @since 1.1.0
	 */
	public function __clone() {
		/* translators: Placeholders: %s - plugin name */
		_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot clone instances of %s.', 'sos-leaderboard-chart' ), 'WooCommerce New Customer Report' ), '1.1.0' );
	}


	/**
	 * Unserializing instances is forbidden due to singleton pattern.
	 *
	 * @since 1.1.0
	 */
	public function __wakeup() {
		/* translators: Placeholders: %s - plugin name */
		_doing_it_wrong( __FUNCTION__, sprintf( esc_html__( 'You cannot unserialize instances of %s.', 'sos-leaderboard-chart' ), 'WooCommerce New Customer Report' ), '1.1.0' );
	}

	/**
	 * Adds plugin page links
	 *
	 * @since 1.0.0
	 * @param array $links all plugin links
	 * @return array $links all plugin links + our custom links (i.e., "Settings")
	 */
	public function add_plugin_links( $links ) {

		$plugin_links = array(
			//'<a href="' . admin_url( 'admin.php?page=wc-reports&tab=customers&report=new_customers' ) . '">' . __( 'View Report', 'sos-leaderboard-chart' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}


	/** Lifecycle methods ***************************************/


	/**
	 * Run every time.  Used since the activation hook is not executed when updating a plugin
	 *
	 * @since 1.0.0
	 */
	private function install() {

		// get current version to check for upgrade
		$installed_version = get_option( 'sos_leaderboard_version' );

		// force upgrade to 1.0.0
		if ( ! $installed_version ) {
			$this->upgrade( '1.0.0' );
		}

		// upgrade if installed version lower than plugin version
		if ( -1 === version_compare( $installed_version, self::VERSION ) ) {
			$this->upgrade( self::VERSION );
		}

	}


	/**
	 * Perform any version-related changes.
	 *
	 * @since 1.0.0
	 * @param int $installed_version the currently installed version of the plugin
	 */
	private function upgrade( $version ) {

		// update the installed version option
		update_option( 'sos_leaderboard_version', $version );
	}


}


/**
 * Returns the One True Instance of SOS_Leaderboard
 *
 * @since 1.0.0
 * @return SOS_Leaderboard
 */
function sos_leaderboard_chart() {
	return SOS_Leaderboard::instance();
}


endif;
