<?php
/**
 * Handle frontend scripts
 *
 * @since     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPGens_RAF_Assets {

    /**
     * Hook in methods.
     */
    public static function init() {
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
        add_action( 'wp_print_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
    }

    /**
     * Register/queue frontend scripts.
     */
    public static function load_scripts() {
        wp_enqueue_script( 'gens-raf_cookieJS', WPGENS_RAF_URL. 'assets/js/cookie.min.js', array( 'jquery' ), WPGENS_RAF_VERSION, false );
        wp_enqueue_script( 'gens-raf-js', WPGENS_RAF_URL. 'assets/js/gens-raf-public.js', array( 'jquery' ), WPGENS_RAF_VERSION, false );
        wp_enqueue_style(  'gens-raf', WPGENS_RAF_URL. 'assets/css/gens-raf.css', array(), WPGENS_RAF_VERSION, 'all' );
    }

    /**
     * Localise frontend scripts.
     */
    public static function localize_printed_scripts() {
        $time = get_option( 'gens_raf_cookie_time' );
        $cookies = array( 'timee' => $time, 'ajax_url' => admin_url( 'admin-ajax.php' ), 'success_msg' => __('Invitation has been sent!', 'gens-raf') );
        wp_localize_script('gens-raf-js', 'gens_raf', $cookies );
    }
}


WPGens_RAF_Assets::init();