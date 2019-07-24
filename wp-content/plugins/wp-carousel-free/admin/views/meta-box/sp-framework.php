<?php
/**
 * The main file for the SP meta-box framework.
 *
 * @package WP_Carousel_Free
 * @subpackage WP_Carousel_Free/admin/views/meta-box
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.

// ------------------------------------------------------------------------------------------------
require_once plugin_dir_path( __FILE__ ) . '/sp-framework-path.php';
// ------------------------------------------------------------------------------------------------
if ( ! function_exists( 'sp_wpcp_framework_init' ) && ! class_exists( 'SP_WPCP_Framework' ) ) {

	/**
	 * SP meta box framework for ShapedPlugin
	 *
	 * @since 2.0.0
	 * @return void
	 */
	function sp_wpcp_framework_init() {

		// Active modules.
		defined( 'SP_WPCP_F_ACTIVE_METABOX' ) || define( 'SP_WPCP_F_ACTIVE_METABOX', true );
		defined( 'SP_WPCP_F_ACTIVE_FRAMEWORK' ) || define( 'SP_WPCP_F_ACTIVE_FRAMEWORK', true );

		// Helpers.
		sp_wpcp_locate_template( 'functions/fallback.php' );
		sp_wpcp_locate_template( 'functions/helpers.php' );
		sp_wpcp_locate_template( 'functions/actions.php' );
		sp_wpcp_locate_template( 'functions/enqueue.php' );
		sp_wpcp_locate_template( 'functions/sanitize.php' );
		sp_wpcp_locate_template( 'functions/validate.php' );

		// Classes.
		sp_wpcp_locate_template( 'classes/abstract.class.php' );
		sp_wpcp_locate_template( 'classes/options.class.php' );
		sp_wpcp_locate_template( 'classes/metabox.class.php' );
		sp_wpcp_locate_template( 'classes/framework.class.php' );

		// Configs.
		sp_wpcp_locate_template( 'config/metabox.config.php' );
		sp_wpcp_locate_template( 'config/framework.config.php' );

	}
	add_action( 'init', 'sp_wpcp_framework_init', 10 );
}
