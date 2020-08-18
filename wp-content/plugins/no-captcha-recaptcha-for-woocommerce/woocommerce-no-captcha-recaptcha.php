<?php

/*
Plugin Name: No CAPTCHA reCAPTCHA for WooCommerce
Plugin URI: https://mailoptin.io
Description: Add the No CAPTCHA reCAPTCHA to WooCommerce login and registration form
Version: 1.2.6
Author: MailOptin Team
Author URI: https://mailoptin.io
License: GPL2
Text Domain: wc-no-captcha
Domain Path: /lang/
*/


require_once dirname(__FILE__) . '/base-class.php';
require_once dirname(__FILE__) . '/registration.php';
require_once dirname(__FILE__) . '/login.php';
require_once dirname(__FILE__) . '/lost-password.php';
require_once dirname(__FILE__) . '/settings.php';
require_once dirname(__FILE__) . '/mo-admin-notice.php';


WC_Ncr_No_Captcha_Recaptcha::initialize();
WC_Ncr_Login_Captcha::initialize();
WC_Ncr_Registration_Captcha::initialize();
WC_Ncr_Lost_Password_Captcha::initialize();
WC_Ncr_Settings_Page::initialize();
