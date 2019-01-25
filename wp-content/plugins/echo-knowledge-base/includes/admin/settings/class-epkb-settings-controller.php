<?php

/**
 * Handle saving feature settings.
 */
class EPKB_Settings_Controller {

	const EPKB_DEBUG = 'epkb_debug';
	const EPKB_WPML_ON = 'epkb_wpml_enabled';

	public function __construct() {
		add_action( 'admin_init', array( $this, 'download_debug_info' ) );
		add_action( 'wp_ajax_epkb_send_feedback', array( $this, 'send_feedback' ) );
		add_action( 'wp_ajax_nopriv_epkb_send_feedback', array( $this, 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epkb_save_wpml_settings', array( $this, 'save_wpml_settings' ) );
		add_action( 'wp_ajax_nopriv_epkb_save_wpml_settings', array( $this, 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epkb_toggle_debug', array( $this, 'toggle_debug' ) );
		add_action( 'wp_ajax_nopriv_epkb_toggle_debug', array( $this, 'user_not_logged_in' ) );
		add_action( 'wp_ajax_epkb_close_welcome_header', array( $this, 'close_welcome_header' ) );
	}

	/**
	 * Triggered when user submits feedback. Send email to the Echo Plugin team.
	 */
	public function send_feedback() {

		// verify that request is authentic
		if ( ! isset( $_REQUEST['_wpnonce_epkb_send_feedback'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce_epkb_send_feedback'], '_wpnonce_epkb_send_feedback' ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'Refresh your page', 'echo-knowledge-base' ));
		}

		// ensure user has correct permissions
		if ( ! current_user_can( 'manage_options' ) ) {
			EPKB_Utilities::ajax_show_error_die(__( 'You do not have permission.', 'echo-knowledge-base' ));
		}

		// retrieve user input
		$user_email = sanitize_email( $_POST['email'] );
		$user_email = empty($user_email) ? '[email missing]' : substr( $user_email, 0, 50 );
		$user_name = sanitize_text_field( $_POST['name'] );
		$user_name = empty($user_name) ? '[name missing]' : substr( $user_name, 0, 50 );
		$user_feedback = sanitize_text_field( $_POST['feedback'] );
		$user_feedback = empty($user_feedback) ? '[user feedback missing]' : substr( $user_feedback, 0, 1000 );

		// send feedback
		$api_params = array(
			'epkb_action'       => 'epkb_process_user_feedback',
			'user_email' 	    => $user_email,
			'user_name' 	    => $user_name,
			'user_feedback'	    => $user_feedback, // the name of our product in EDD
			'plugin_name'       => 'Echo Knowledge Base'
		);

		// Call the API
		$response = wp_remote_post(
			esc_url_raw( add_query_arg( $api_params, 'https://www.echoknowledgebase.com' ) ),
			array(
				'timeout'   => 15,
				'body'      => $api_params,
				'sslverify' => false
			)
		);
		if ( is_wp_error( $response ) ) {
			EPKB_Utilities::ajax_show_error_die( sprintf(__( 'Please contact us at: %s', 'echo-knowledge-base' ), 'https://www.echoknowledgebase.com/contact-us/'), __('An error occurred', 'echo-knowledge-base' ) );
		}

		// we are done here
		EPKB_Utilities::ajax_show_info_die( __( 'Feedback sent. We will get back to you in a day or two. Thank you!', 'echo-knowledge-base' ) );
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

		$is_wpml_on = EPKB_Utilities::post('epkb_wpml_enabled');
		$is_wpml_on = $is_wpml_on === 'true' ? 'true' : 'false';

		EPKB_Utilities::save_wp_option( EPKB_Settings_Controller::EPKB_WPML_ON, $is_wpml_on, true );

		// we are done here
		EPKB_Utilities::ajax_show_info_die( __( 'WPML is now ' . ( $is_wpml_on ? 'on' : 'off' ), 'echo-knowledge-base' ) );
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

		$is_debug_on = ! $is_debug_on;

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

	/**
	 * Record that user closed the welcome header or update message on the settings page
	 */
	public function close_welcome_header() {
		delete_option('epkb_show_welcome_header');
	}

	public function user_not_logged_in() {
		EPKB_Utilities::ajax_show_error_die( '<p>' . __( 'You are not logged in. Refresh your page and log in.', 'echo-knowledge-base' ) . '</p>', 'Cannot save your changes' );
	}
}