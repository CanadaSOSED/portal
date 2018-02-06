<?php
/**
 * Setup Menu Pages
 * @author    WPGens
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPGENS_RAF_Menu {

	public function __construct() {
		// Add submenu items
		add_action( 'admin_menu', array( $this, 'register_stats_menu') );
		// TODO: Temporary settings screen
		add_filter( 'woocommerce_get_settings_pages', array( $this, 'temporary_settings_page') );
		// Add links under plugin page.
		add_filter( 'plugin_action_links_wpgens-refer-a-friend-premium/wpgens-raf.php', array($this, 'add_settings_link') );
		add_filter( 'plugin_action_links_wpgens-refer-a-friend-premium/wpgens-raf.php', array($this, 'docs_link') );
	}


	/**
	 * Define submenu page under Woocommerce Page
	 *
	 * @since 2.0.0
	 */
	public function register_stats_menu() {
		add_submenu_page( 'woocommerce', __('Refers Stats', 'gens-raf'), __('Refer a Friend Data', 'gens-raf'), 'manage_woocommerce', 'gens-raf', array($this, 'display_plugin_admin_page'));
	}


	/**
	 * Init the view part.
	 *
	 * @since 2.0.0
	 */
	public function display_plugin_admin_page() {
		
		$license = get_option( 'gens_raf_license_key' );
		$status  = get_option( 'gens_raf_license_status' );

		include( WPGENS_RAF_ABSPATH . 'includes/admin/views/html-admin-stats.php' );
	}


	/**
	 * Temporary Settings page untill we finish separate page.
	 *
	 * @since 2.0.0
	 */
	public function temporary_settings_page( $settings ) {
		$settings[] = require_once( WPGENS_RAF_ABSPATH . 'includes/admin/class-wpgens-raf-settings.php' );
		return $settings;
	}


	/**
	 * Plugin Settings Link on plugin page
	 *
	 * @since 		2.0.0
	 */
	function add_settings_link( $links ) {
		$mylinks = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=gens_raf' ) . '">Settings</a>',
		);
		return array_merge( $links, $mylinks );
	}


	/**
	 * Plugin Documentation Link on plugin page
	 *
	 * @since 		2.0.0
	 */
	function docs_link( $links ) {
		$mylinks = array(
			'<a target="_blank" href="http://wpgens.helpscoutdocs.com">Docs</a>',
		);
		return array_merge( $links, $mylinks );
	}

}

new WPGENS_RAF_Menu();