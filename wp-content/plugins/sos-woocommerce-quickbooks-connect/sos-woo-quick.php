<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              briancaicco.com
 * @since             1.0.0
 * @package           Sos_Woo_Quick
 *
 * @wordpress-plugin
 * Plugin Name:       SOS-Woocommerce-Quickbooks
 * Plugin URI:        http://canadasos.org
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            SOS Development Team
 * Author URI:        briancaicco.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sos-woo-quick
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sos-woo-quick-activator.php
 */
function activate_sos_woo_quick() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sos-woo-quick-activator.php';
	Sos_Woo_Quick_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sos-woo-quick-deactivator.php
 */
function deactivate_sos_woo_quick() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sos-woo-quick-deactivator.php';
	Sos_Woo_Quick_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sos_woo_quick' );
register_deactivation_hook( __FILE__, 'deactivate_sos_woo_quick' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sos-woo-quick.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sos_woo_quick() {

	$plugin = new Sos_Woo_Quick();
	$plugin->run();

}
run_sos_woo_quick();
