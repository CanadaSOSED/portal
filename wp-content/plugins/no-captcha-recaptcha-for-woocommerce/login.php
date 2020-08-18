<?php

class WC_Ncr_Login_Captcha extends WC_Ncr_No_Captcha_Recaptcha {

	public static function initialize() {

		// initialize if login is activated
		if ( isset( self::$plugin_options['captcha_wc_login'] ) && self::$plugin_options['captcha_wc_login'] == 'yes' ) {

			// adds the captcha to the login form
			add_filter( 'woocommerce_login_form', array( __CLASS__, 'display_captcha' ) );

			// authenticate the captcha answer
			add_action( 'woocommerce_process_login_errors', array( __CLASS__, 'validate_login_captcha' ), 10, 2 );
		}
	}

	/**
	 * Verify the captcha answer
	 *
	 * @param $user string login username
	 * @param $password string login password
	 *
	 * @return WP_Error|WP_user
	 */
	public static function validate_login_captcha( $user, $password ) {
		remove_action( 'wp_authenticate_user', array( 'Ncr_Login_Captcha', 'validate_captcha' ), 10 );
		if ( ! isset( $_POST['g-recaptcha-response'] ) || ! self::captcha_wc_verification() ) {
			return new WP_Error( 'empty_captcha', self::$error_message );
		}

		return $user;
	}
}