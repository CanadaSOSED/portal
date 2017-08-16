<?php
/**
 * Plugin Name: SOS Hide The Admin Bar
 * Plugin URI: 
 * Description: Hides the admin bar from the frontend. 
 * Version: 1.0 
 * Author: SOS Development Team <briancaicco@gmail.com>
 * Author URI: 
 * License: GPL2
 *
 * Text Domain: customize-customizer
 * Domain Path: /languages/
 *
 */

add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
    if( current_user_can('edit_pages') ) { 
    
    } else {
    	add_filter('show_admin_bar', '__return_false');
    }
}