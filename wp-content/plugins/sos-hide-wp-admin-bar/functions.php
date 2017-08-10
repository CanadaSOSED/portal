<?php
/**
 * Plugin Name: SOS Customize Customizer
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
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}