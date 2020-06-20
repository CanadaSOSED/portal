<?php
/**
 * Fired during plugin activation
 *
 * @link       https://shapedplugin.com
 * @since      3.0.0
 *
 * @package    WP_Carousel_Pro
 * @subpackage WP_Carousel_Pro/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.1.5
 * @package    WP_Carousel_Free
 * @subpackage WP_Carousel_Free/includes
 * @author     ShapedPlugin<shapedplugin@gmail.com>
 */
class WP_Carousel_Free_Activator {

	/**
	 * The carousels.
	 *
	 * @var array
	 */
	private $carousels;

	/**
	 * WP Carousel activator.
	 *
	 * Deactivate the pro version during the activation of the WP Carousel.
	 *
	 * @since  2.1.5
	 * @return void
	 */
	public static function activate() {
		deactivate_plugins( 'wp-carousel-pro/wp-carousel-pro.php' );
	}
}
