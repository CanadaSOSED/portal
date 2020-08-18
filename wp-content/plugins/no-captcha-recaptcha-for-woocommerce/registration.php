<?php

class WC_Ncr_Registration_Captcha extends WC_Ncr_No_Captcha_Recaptcha
{

    /** Initialize actions and filters */
    public static function initialize()
    {
        /* Remove captcha check on checkout */
        add_action('woocommerce_before_checkout_process', function () {
            remove_filter('woocommerce_registration_errors', array('WC_Ncr_Registration_Captcha', 'validate_captcha_wc_registration'), 10);
        });

        // initialize if login is activated
        if (isset(self::$plugin_options['captcha_wc_registration']) && self::$plugin_options['captcha_wc_registration'] == 'yes') {
            // adds the captcha to the registration form
            add_action('woocommerce_register_form', array(__CLASS__, 'display_captcha'));

            // authenticate the captcha answer
            add_filter('woocommerce_registration_errors', array(
                __CLASS__,
                'validate_captcha_wc_registration'
            ), 10, 3);
        }
    }


    /**
     * Verify the captcha answer
     *
     * @param $validation_errors
     * @param $username
     * @param $email
     *
     * @return WP_Error
     */
    public static function validate_captcha_wc_registration($validation_errors, $username, $email)
    {
        if (!isset($_POST['g-recaptcha-response']) || !self::captcha_wc_verification()) {
            $validation_errors = new WP_Error('failed_verification', self::$error_message);
        }

        return $validation_errors;
    }
}