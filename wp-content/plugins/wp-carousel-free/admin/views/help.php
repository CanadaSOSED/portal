<?php
/**
 * The help page for the WP Carousel
 *
 * @package WP Carousel
 * @subpackage wp-carousel-free/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}  // if direct access.

/**
 * The help class for the WP Carousel
 */
class WP_Carousel_Free_Help {

	/**
	 * Wp Carousel Pro single instance of the class
	 *
	 * @var null
	 * @since 2.0.0
	 */
	protected static $_instance = null;

	/**
	 * Main WP_Carousel_Free_Help Instance
	 *
	 * @since 2.0.0
	 * @static
	 * @see sp_wpcp_help()
	 * @return self Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Add admin menu.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function help_admin_menu() {
		add_submenu_page(
			'edit.php?post_type=sp_wp_carousel', __( 'WP Carousel Help', 'wp-carousel-free' ), __( 'Help', 'wp-carousel-free' ), 'manage_options', 'wpcf_help', array(
				$this,
				'help_page_callback',
			)
		);
	}

	/**
	 * The WP Carousel Help Callback.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public function help_page_callback() {
		echo '
        <div class="wrap about-wrap sp-wpcp-help">
        <h1>' . esc_html__( 'Welcome to WordPress Carousel! ', 'wp-carousel-free' ) . '</h1>
        </div>
        <div class="wrap about-wrap sp-wpcp-help">
			<p class="about-text">' . esc_html__( 'Thank you for installing WordPress Carousel! You\'re now running the most popular WordPress Carousel plugin.
This video will help you get started with the plugin.', 'wp-carousel-free' ) . '</p>
			<div class="wp-badge"></div>

			<hr>

			<div class="headline-feature feature-video">
				<iframe width="560" height="315" src="https://www.youtube.com/embed/hCeKn8jmxn4" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
			</div>

			<hr>

			<div class="feature-section three-col">
				<div class="col">
					<div class="sp-wpcp-feature text-center">
						<i class="sp-wpcp-font-icon fa-life-ring"></i>
						<h3>' . esc_html__( 'Need any Assistance?', 'wp-carousel-free' ) . '</h3>
						<p>' . esc_html__( 'Our Expert Support Team is always ready to help you out promptly.', 'wp-carousel-free' ) . '</p>
						<a href="https://shapedplugin.com/support-forum/" target="_blank" class="button button-primary">' . esc_html__( 'Contact Support', 'wp-carousel-free' ) . '</a>
					</div>
				</div>
				<div class="col">
					<div class="sp-wpcp-feature text-center">
						<i class="sp-wpcp-font-icon fa-file-text"></i>
						<h3>' . esc_html__( 'Looking for Documentation?', 'wp-carousel-free' ) . '</h3>
						<p>' . esc_html__( 'We have detailed documentation on every aspects of WordPress Carousel.', 'wp-carousel-free' ) . '</p>
						<a href="https://shapedplugin.com/docs/docs/wordpress-carousel/" target="_blank" class="button button-primary">' . esc_html__( 'Documentation', 'wp-carousel-free' ) . '</a>
					</div>
				</div>
				<div class="col">
					<div class="sp-wpcp-feature text-center">
						<i class="sp-wpcp-font-icon fa-thumbs-up"></i>
						<h3>' . esc_html__( 'Like This Plugin?', 'wp-carousel-free' ) . '</h3>
						<p>' . esc_html__( 'If you like WordPress Carousel, please leave us a 5 star rating.', 'wp-carousel-free' ) . '</p>
						<a href="https://wordpress.org/support/plugin/wp-carousel-free/reviews/?filter=5#new-post" target="_blank" class="button button-primary">' . esc_html__( 'Rate The Plugin', 'wp-carousel-free' ) . '</a>
					</div>
				</div>
			</div>
		</div>';
	}
}
