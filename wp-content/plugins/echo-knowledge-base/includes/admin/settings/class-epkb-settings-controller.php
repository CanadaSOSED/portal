<?php

/**
 * Handle saving feature settings.
 */
class EPKB_Settings_Controller {

	const EPKB_DEBUG = 'epkb_debug';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'download_debug_info' ) );
		add_action( 'wp_ajax_epkb_toggle_debug', array( $this, 'toggle_debug' ) );
		add_action( 'wp_ajax_nopriv_epkb_toggle_debug', array( $this, 'user_not_logged_in' ) );
	}

	/**
	 * Triggered when user clicks to toggle debug.
	 */
	public function toggle_debug() {

		// verify that request is authentic
		if ( ! isset( $_REQUEST['_wpnonce_epkb_toggle_debug'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce_epkb_toggle_debug'], '_wpnonce_epkb_toggle_debug' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Refresh your page', 'echo-knowledge-base' ) );
		}

		// ensure user has correct permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission.', 'echo-knowledge-base' ) );
		}

		$is_debug_on = EPKB_Utilities::get_wp_option( EPKB_Settings_Controller::EPKB_DEBUG, false );

		$is_debug_on = empty($is_debug_on) ? 1 : 0;

		EPKB_Utilities::save_wp_option( EPKB_Settings_Controller::EPKB_DEBUG, $is_debug_on, true );

		// we are done here
		EPKB_Utilities::ajax_show_info_die( __( 'Debug is now ' . ( $is_debug_on ? 'on' : 'off' ), 'echo-knowledge-base' ) );
	}

	/**
	 * Generates a System Info download file
	 */
	public function download_debug_info() {

		if ( EPKB_Utilities::post('action') != 'epkb_download_debug_info' ) {
			return;
		}

		// verify that the request is authentic
		if ( ! isset( $_REQUEST['_wpnonce_epkb_download_debug_info'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_epkb_download_debug_info'], '_wpnonce_epkb_download_debug_info' ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'Debug not loaded. First refresh your page', 'echo-knowledge-base' ));
		}

		// ensure user has correct permissions - only admin can download info
		if ( ! current_user_can( 'manage_options' ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'You do not have permission to access this page', 'echo-knowledge-base' ));
		}

		EPKB_Utilities::save_wp_option( EPKB_Settings_Controller::EPKB_DEBUG, false, true);

		nocache_headers();

		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition: attachment; filename="echo-debug-info.txt"' );

		$output = EPKB_Add_Ons_Page::display_debug_data();
		echo wp_strip_all_tags( $output );

		die();
	}

	public function user_not_logged_in() {
		EPKB_Utilities::ajax_show_error_die( '<p>' . __( 'You are not logged in. Refresh your page and log in.', 'echo-knowledge-base' ) . '</p>', __( 'Cannot save your changes', 'echo-knowledge-base' ) );
	}
}