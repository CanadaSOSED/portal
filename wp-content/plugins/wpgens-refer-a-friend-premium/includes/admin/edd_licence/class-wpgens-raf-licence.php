<?php

// Import main class file.
if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
	require_once WPGENS_RAF_ABSPATH . 'includes/admin/edd_licence/EDD_SL_Plugin_Updater.php';
}

/**
 * EDD Licence - WPGens RAF Automatic Update
 *
 * @version 1.1
 */
class WPGens_RAF_Licence {


	function __construct() {
		add_action( 'admin_init', array( $this, 'gens_raf_auto_update'), 0 );
		add_action( 'admin_init', array( $this, 'gens_raf_activate_licence') );
		add_action( 'admin_init', array( $this, 'gens_raf_deactivate_licence') );
		add_action( 'admin_init', array( $this, 'gens_raf_licence_options'));
	}
	
	/**
	 * EDD: Plugin auto update
	 *
	 * @since 		1.1.0
	 */
	public function gens_raf_auto_update() {

		$license_key = trim( get_option( 'gens_raf_license_key' ) );

		// setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater( WPGENS_RAF_STORE_URL, WPGENS_RAF_ABSPATH.'/wpgens-raf.php', array(
				'version' 	=> WPGENS_RAF_VERSION, // current version - change on every plugin change
				'license' 	=> $license_key,
				'item_name' => WPGENS_RAF_ITEM_NAME,
				'author' 	=> 'WPGens',
				'beta'		=> false
			)
		);
	}

	/**
	 * EDD Plugin activate licence
	 *
	 * @since 		1.1.0
	 */
	public function gens_raf_activate_licence() {

		// listen for our activate button to be clicked
		if( isset( $_POST['gens_raf_license_activate'] ) ) {

			// run a quick security check
			if( ! check_admin_referer( 'gens_raf_nonce', 'gens_raf_nonce' ) )
				return; // get out if we didn't click the Activate button

			// retrieve the license from the database
			$license = trim( get_option( 'gens_raf_license_key' ) );

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => $license,
				'item_name'  => urlencode( WPGENS_RAF_ITEM_NAME ), // the name of our product in EDD
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post( WPGENS_RAF_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.' );
				}

			} else {

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				if ( false === $license_data->success ) {

					switch( $license_data->error ) {

						case 'expired' :

							$message = sprintf(
								__( 'Your license key expired on %s.' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
							);
							break;

						case 'revoked' :

							$message = __( 'Your license key has been disabled.' );
							break;

						case 'missing' :

							$message = __( 'Invalid license.' );
							break;

						case 'invalid' :
						case 'site_inactive' :

							$message = __( 'Your license is not active for this URL.' );
							break;

						case 'item_name_mismatch' :

							$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), WPGENS_RAF_ITEM_NAME );
							break;

						case 'no_activations_left':

							$message = __( 'Your license key has reached its activation limit.' );
							break;

						default :

							$message = __( 'An error occurred, please try again.' );
							break;
					}

				}

			}

			// Check if anything passed on a message constituting a failure
			if ( ! empty( $message ) ) {
				$base_url = admin_url( 'admin.php?page=' . WPGENS_RAF_PLUGIN_LICENSE_PAGE );
				$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

				wp_redirect( $redirect );
				exit();
			}

			// $license_data->license will be either "valid" or "invalid"

			update_option( 'gens_raf_license_status', $license_data->license );
			wp_redirect( admin_url( 'admin.php?page=' . WPGENS_RAF_PLUGIN_LICENSE_PAGE ) );
			exit();
		}
	}

	function gens_raf_deactivate_licence() {

		// listen for our activate button to be clicked
		if( isset( $_POST['gens_raf_license_deactivate'] ) ) {

			// run a quick security check
			if( ! check_admin_referer( 'gens_raf_nonce', 'gens_raf_nonce' ) )
				return; // get out if we didn't click the Activate button

			// retrieve the license from the database
			$license = trim( get_option( 'gens_raf_license_key' ) );


			// data to send in our API request
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $license,
				'item_name'  => urlencode( WPGENS_RAF_ITEM_NAME ), // the name of our product in EDD
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post( WPGENS_RAF_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.' );
				}

				$base_url = admin_url( 'admin.php?page=' . WPGENS_RAF_PLUGIN_LICENSE_PAGE );
				$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );

				wp_redirect( $redirect );
				exit();
			}

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			// $license_data->license will be either "deactivated" or "failed"
			if( $license_data->license == 'deactivated' ) {
				delete_option( 'gens_raf_license_status' );
			}

			wp_redirect( admin_url( 'admin.php?page=' . WPGENS_RAF_PLUGIN_LICENSE_PAGE ) );
			exit();

		}
	}

	function gens_raf_licence_options() {
		// creates our settings in the options table
		register_setting('gens_raf_license', 'gens_raf_license_key',array($this,'edd_sanitize_license') );
	}

	function edd_sanitize_license( $new ) {
		$old = get_option( 'gens_raf_license_key' );
		if( $old && $old != $new ) {
			delete_option( 'gens_raf_license_status' ); // new license has been entered, so must reactivate
		}
		return $new;
	}

}

new WPGens_RAF_Licence();