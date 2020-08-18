<?php

/**
 * Class WC_Ncr_Lost_Password_Captcha
 */
class WC_Ncr_Lost_Password_Captcha extends WC_Ncr_No_Captcha_Recaptcha {

	public static function initialize() {

		// initialize if login is activated
		if ( isset( self::$plugin_options['captcha_wc_lost_password'] ) || self::$plugin_options['captcha_wc_lost_password'] == 'yes' ) {

			// adds the captcha to the login form
			add_filter( 'woocommerce_lostpassword_form', array( __CLASS__, 'display_captcha' ) );

			// authenticate the captcha answer
			add_filter( 'allow_password_reset', array( __CLASS__, 'validate_lost_password_captcha' ) );
		}
	}

	/**
	 * Verify the captcha answer.
	 *
	 * @return WP_Error
	 */
	public static function validate_lost_password_captcha( $allow ) {
		if ( isset( $_POST['wc_reset_password'] ) ) {
			if ( ! isset( $_POST['g-recaptcha-response'] ) || ! self::captcha_wc_verification() ) {
				return new WP_Error( 'empty_captcha', self::$error_message );
			}
		}

		return $allow;
	}
}