<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       briancaicco.com
 * @since      1.0.0
 *
 * @package    Sos_Woo_Quick
 * @subpackage Sos_Woo_Quick/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Sos_Woo_Quick
 * @subpackage Sos_Woo_Quick/includes
 * @author     SOS Development Team <briancaicco@gmail.com>
 */
class Sos_Woo_Quick_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'sos-woo-quick',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
