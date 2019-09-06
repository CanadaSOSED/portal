<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.


if ( ! function_exists( 'sp_wpcp_admin_enqueue_scripts' ) ) {
	/**
	 *
	 * Framework admin enqueue style and scripts
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 * @return void
	 */
	function sp_wpcp_admin_enqueue_scripts() {
		$current_screen        = get_current_screen();
		$the_current_post_type = $current_screen->post_type;
		if ( 'sp_wp_carousel' === $the_current_post_type ) {

			// Admin utilities.
			wp_enqueue_media();

			// wp core styles.
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'wp-jquery-ui-dialog' );

			// Framework core styles.
			wp_enqueue_style( 'sp-wpcp-framework', WPCAROUSELF_URL . 'admin/views/meta-box/assets/css/sp-framework.css', array(), WPCAROUSELF_VERSION, 'all' );
			wp_enqueue_style( 'sp-wpcp-custom', WPCAROUSELF_URL . 'admin/views/meta-box/assets/css/sp-custom.css', array(), WPCAROUSELF_VERSION, 'all' );
			wp_enqueue_style( 'wpcp-mb-style', WPCAROUSELF_URL . 'admin/views/meta-box/assets/css/sp-style.css', array(), WPCAROUSELF_VERSION, 'all' );
			wp_enqueue_style( 'wpcp-font-awesome', WPCAROUSELF_URL . 'public/css/font-awesome.min.css', array(), WPCAROUSELF_VERSION, 'all' );

			if ( is_rtl() ) {
				wp_enqueue_style( 'sp-framework-rtl', WPCAROUSELF_URL . 'admin/views/meta-box/assets/css/sp-framework-rtl.css', array(), WPCAROUSELF_VERSION, 'all' );
			}

			// wp core scripts.
			wp_enqueue_script( 'wp-color-picker' );

			// framework core scripts.
			wp_enqueue_script( 'sp-wpcp-dependency', WPCAROUSELF_URL . 'admin/views/meta-box/assets/js/dependency.js', array( 'jquery' ), WPCAROUSELF_VERSION, true );
			wp_enqueue_script( 'sp-wpcp-plugins', WPCAROUSELF_URL . 'admin/views/meta-box/assets/js/sp-plugins.js', array(), WPCAROUSELF_VERSION, true );
			wp_enqueue_script( 'sp-wpcp-framework', WPCAROUSELF_URL . 'admin/views/meta-box/assets/js/sp-framework.js', array( 'sp-wpcp-plugins' ), WPCAROUSELF_VERSION, true );
		}

	}

	add_action( 'admin_enqueue_scripts', 'sp_wpcp_admin_enqueue_scripts' );
}
