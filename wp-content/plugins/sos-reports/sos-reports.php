<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              canadasos.com
 * @since             1.0.0
 * @package           Sos_Reports
 *
 * @wordpress-plugin
 * Plugin Name:       SOS Reports
 * Plugin URI:        This plugin pulls custom data from various plugins and turns it into downloadable .csv files and pretty charts and graphs and stuff
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            SOS Development Team
 * Author URI:        canadasos.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sos-reports
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sos-reports-activator.php
 */
function activate_sos_reports() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sos-reports-activator.php';
	Sos_Reports_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sos-reports-deactivator.php
 */
function deactivate_sos_reports() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-sos-reports-deactivator.php';
	Sos_Reports_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_sos_reports' );
register_deactivation_hook( __FILE__, 'deactivate_sos_reports' );



/**
 * Include woocommerce api
 */

require plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-sos-reports.php';



/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sos_reports() {

	$plugin = new Sos_Reports();
	$plugin->run();


}
run_sos_reports();
