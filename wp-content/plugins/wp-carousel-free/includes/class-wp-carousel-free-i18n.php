<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://shapedplugin.com/
 * @since      2.0.0
 *
 * @package    WP_Carousel_Free
 * @subpackage WP_Carousel_Free/includes
 */

/**
 * WP_Carousel_Free_I18n define the internationalization functionality.
 *
 * @since      2.0.0
 * @author     ShapedPlugin <shapedplugin@gmail.com>
 */
class WP_Carousel_Free_I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 2.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wp-carousel-free',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
