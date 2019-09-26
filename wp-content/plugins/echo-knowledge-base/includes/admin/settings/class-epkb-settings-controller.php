<?php

/**
 * Handle saving feature settings.
 */
class EPKB_Settings_Controller {

	const EPKB_DEBUG = 'epkb_debug';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'download_debug_info' ) );
		add_action( 'wp_ajax_epkb_save_wpml_settings', array( $this, 'save_wpml_settings' ) );
		add_action( 'wp_ajax_nopriv_epkb_save_wpml_settings', array( $this, 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epkb_toggle_debug', array( $this, 'toggle_debug' ) );
		add_action( 'wp_ajax_nopriv_epkb_toggle_debug', array( $this, 'user_not_logged_in' ) );
	}

	/**
	 * User changes WPML settings.
	 */
	public function save_wpml_settings() {

		// verify that request is authentic
		if ( ! isset( $_REQUEST['_wpnonce_epkb_save_wpml_settings'] ) || !wp_verify_nonce( $_REQUEST['_wpnonce_epkb_save_wpml_settings'], '_wpnonce_epkb_save_wpml_settings' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'Refresh your page', 'echo-knowledge-base' ) );
		}

		// ensure user has correct permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			EPKB_Utilities::ajax_show_error_die( __( 'You do not have permission.', 'echo-knowledge-base' ) );
		}

		// retrieve KB ID we are saving
		$kb_id = empty($_POST['epkb_kb_id']) ? '' : EPKB_Utilities::sanitize_get_id( $_POST['epkb_kb_id'] );
		if ( empty($kb_id) || is_wp_error( $kb_id ) ) {
			EPKB_Logging::add_log( "invalid kb id", $kb_id );
			EPKB_Utilities::ajax_show_error_die(__( 'This page is outdated. Please refresh your browser', 'echo-knowledge-base' ));
		}

		// retrieve current KB configuration
		$kb_config = epkb_get_instance()->kb_config_obj->get_kb_config( $kb_id );
		if ( is_wp_error( $kb_config ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'Error occurred. Please refresh your browser and try again (A1).', 'echo-knowledge-base' ));
		}

		// save WPML configuration
		$is_wpml_on = EPKB_Utilities::post('epkb_wpml_is_enabled');
		$is_wpml_on = $is_wpml_on === 'true' ? 'on' : 'off';
		epkb_get_instance()->kb_config_obj->set_value( $kb_id, 'wpml_is_enabled', $is_wpml_on );
		if ( is_wp_error( $kb_config ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'Error occurred. Please refresh your browser and try again (A2).', 'echo-knowledge-base' ));
		}

		// we are done here
		EPKB_Utilities::ajax_show_info_die( __( 'WPML is now ' . ( $is_wpml_on == 'on' ? 'on' : 'off' ), 'echo-knowledge-base' ) );
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

		nocache_headers();

		header( 'Content-Type: text/plain' );
		header( 'Content-Disposition: attachment; filename="echo-debug-info.txt"' );

		$output = EPKB_Settings_Page::display_debug_data();
		echo wp_strip_all_tags( $output );

		die();
	}

	public function user_not_logged_in() {
		EPKB_Utilities::ajax_show_error_die( '<p>' . __( 'You are not logged in. Refresh your page and log in.', 'echo-knowledge-base' ) . '</p>', 'Cannot save your changes' );
	}
}