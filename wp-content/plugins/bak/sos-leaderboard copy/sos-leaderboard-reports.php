<?php
/**
 * Plugin Name: SOS Leaderboard Reports
 * Plugin URI: 
 * Description: Don't Derp
 * Author: SOS Development Team
 * Author URI:
 * Version: 1.0.0
 * Text Domain: sos-leaderboard
 *
 *
 *
 * @package   SOS-Leaderboard-Reports
 * @author    SOS Development Team
 * @category  Admin
 *
 */

defined( 'ABSPATH' ) or exit;

/**
 * SOS_Leaderboard_Reports Version Check.
 */
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || version_compare( get_option( 'woocommerce_db_version' ), '2.4.0', '<' ) ) {

	function sos_leaderboard_reports_outdated_version_notice() {

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

	add_action( 'admin_notices', 'sos_leaderboard_reports_outdated_version_notice' );
	return;
}



if ( ! class_exists( 'SOS_Leaderboard_Reports' ) ) :

add_action( 'plugins_loaded', 'sos_leaderboard_reports' );


/**
 * SOS_Leaderboard_Reports Class.
 */
class SOS_Leaderboard_Reports {

	const VERSION = '1.1.0';

	/** @var SOS_Leaderboard single instance of this plugin */
	protected static $instance;

	/**
	 * SOS_Leaderboard constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// add styles
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );

		

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {

			// any admin actions
			add_action( 'admin_menu', array( $this, 'add_admin_menu_item' ) );

			// add plugin links
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'add_plugin_links' ) );

			// run every time
			$this->install();

		}

	}

	public function admin_styles(){
		wp_register_style( 'sos_leaderboard_styles', plugin_dir_url( __FILE__ ) .'/assets/css/admin.css', array(), '' );
		wp_enqueue_style( 'sos_leaderboard_styles' );
	}

	public function add_admin_menu_item() {
		add_menu_page( 'SOS Leaderboard', 'Leaderboard', 'manage_options', 'sos-leaderboard', array($this, 'output'), 'dashicons-chart-bar', 25 );
	}


	/**
	 * Handles output of the reports page in admin.
	 */
	public static function output() {
		$reports        = self::get_reports();
		$first_tab      = array_keys( $reports );
		$current_tab    = ! empty( $_GET['tab'] ) ? sanitize_title( $_GET['tab'] ) : $first_tab[0];
		$current_report = isset( $_GET['report'] ) ? sanitize_title( $_GET['report'] ) : current( array_keys( $reports[ $current_tab ]['reports'] ) );

		include_once( dirname( __FILE__ ) . '/reports/class-sos-leaderboard-report.php' );
		include_once( dirname( __FILE__ ) . '/views/html-sos-leaderboard-page-reports.php' );
	}

	/**
	 * Returns the definitions for the reports to show in admin.
	 *
	 * @return array
	 */
	public static function get_reports() {
		$reports = array(
			'orders'     => array(
				'title'  => __( 'Orders', 'woocommerce' ),
				'reports' => array(
					"sales_by_date" => array(
						'title'       => __( 'Sales by date', 'woocommerce' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( __CLASS__, 'get_report' ),
					),
					"sales_by_product" => array(
						'title'       => __( 'Sales by product', 'woocommerce' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( __CLASS__, 'get_report' ),
					),
					"sales_by_category" => array(
						'title'       => __( 'Sales by category', 'woocommerce' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( __CLASS__, 'get_report' ),
					),
					"coupon_usage" => array(
						'title'       => __( 'Coupons by date', 'woocommerce' ),
						'description' => '',
						'hide_title'  => true,
						'callback'    => array( __CLASS__, 'get_report' ),
					),
				),
			),
		);

		// if ( wc_tax_enabled() ) {
		// 	$reports['taxes'] = array(
		// 		'title'  => __( 'Taxes', 'woocommerce' ),
		// 		'reports' => array(
		// 			"taxes_by_code" => array(
		// 				'title'       => __( 'Taxes by code', 'woocommerce' ),
		// 				'description' => '',
		// 				'hide_title'  => true,
		// 				'callback'    => array( __CLASS__, 'get_report' ),
		// 			),
		// 			"taxes_by_date" => array(
		// 				'title'       => __( 'Taxes by date', 'woocommerce' ),
		// 				'description' => '',
		// 				'hide_title'  => true,
		// 				'callback'    => array( __CLASS__, 'get_report' ),
		// 			),
		// 		),
		// 	);
		// }

		$reports = apply_filters( 'woocommerce_admin_reports', $reports );
		$reports = apply_filters( 'woocommerce_reports_charts', $reports ); // Backwards compat

		foreach ( $reports as $key => $report_group ) {
			if ( isset( $reports[ $key ]['charts'] ) ) {
				$reports[ $key ]['reports'] = $reports[ $key ]['charts'];
			}

			foreach ( $reports[ $key ]['reports'] as $report_key => $report ) {
				if ( isset( $reports[ $key ]['reports'][ $report_key ]['function'] ) ) {
					$reports[ $key ]['reports'][ $report_key ]['callback'] = $reports[ $key ]['reports'][ $report_key ]['function'];
				}
			}
		}

		return $reports;
	}

	/**
	 * Get a report from our reports subfolder.
	 *
	 * @param string $name
	 */
	public static function get_report( $name ) {
		$name  = sanitize_title( str_replace( '_', '-', $name ) );
		$class = 'SOS_Leaderboard_Report_' . str_replace( '-', '_', $name );

		include_once( apply_filters( 'sos_admin_reports_path', 'reports/class-sos-leaderboard-report-' . $name . '.php', $name, $class ) );

		if ( ! class_exists( $class ) ) {
			return;
		}

		$report = new $class();
		$report->output_report();
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
 * Returns the One True Instance of SOS_Leaderboard_Reports
 *
 * @since 1.0.0
 * @return SOS_Leaderboard_Reports
 */
function sos_leaderboard_reports() {
	return SOS_Leaderboard_Reports::instance();
}

endif;