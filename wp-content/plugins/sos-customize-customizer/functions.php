<?php
/**
 * Plugin Name: SOS Customize Customizer
 * Plugin URI: 
 * Description: Remove Widgets and Nav Menus panels from the customizer page
 * Version: 1.0 
 * Author: SOS Development Team
 * Author URI: 
 * License: GPL2
 *
 * Text Domain: customize-customizer
 * Domain Path: /languages/
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Removes the core 'Widgets' panel from the Customizer.
 *
 * @param array $components Core Customizer components list.
 *
 */
function wpdocs_remove_widgets_panel( $components ) {
    $i = array_search( 'widgets', $components );
    if ( false !== $i ) {
        unset( $components[ $i ] );
    }
    return $components;
}
add_filter( 'customize_loaded_components', 'wpdocs_remove_widgets_panel' );

/**
 * Removes the core 'Menus' panel from the Customizer.
 *
 * @param array $components Core Customizer components list.
 * @return array (Maybe) modified components list.
 */
function wpdocs_remove_nav_menus_panel( $components ) {
    $i = array_search( 'nav_menus', $components );
    if ( false !== $i ) {
        unset( $components[ $i ] );
    }
    return $components;
}
add_filter( 'customize_loaded_components', 'wpdocs_remove_nav_menus_panel' );
?>