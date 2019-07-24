<?php
/**
 * Plugin Name: Responsive Tabs
 * Plugin URI: https://wpdarko.com/responsive-tabs/
 * Description: A responsive, simple and clean way to display your content. Create new tabs in no-time (custom type) and copy-paste the shortcode into any post/page. Find help and information on our <a href="https://wpdarko.com/ask-for-support/">support site</a>. This free version is NOT limited and does not contain any ad. Check out the <a href='https://wpdarko.com/responsive-tabs/'>PRO version</a> for more great features.
 * Version: 4.0.0
 * Author: WP Darko
 * Author URI: https://wpdarko.com
 * Text Domain: responsive-tabs
 * Domain Path: /lang/
 *License: GPL2
 */


/* Defines plugin's root folder. */
define( 'RTBS_PATH', plugin_dir_path( __FILE__ ) );


/* Defines plugin's text domain. */
define( 'RTBS_TXTDM', 'responsive-tabs' );


/* General. */
require_once('inc/rtbs-text-domain.php');
require_once('inc/rtbs-pro-version-check.php');


/* Scripts. */
require_once('inc/rtbs-front-scripts.php');
require_once('inc/rtbs-admin-scripts.php');


/* Tabs. */
require_once('inc/rtbs-post-type.php');


/* Shortcode. */
require_once('inc/rtbs-shortcode-column.php');
require_once('inc/rtbs-shortcode.php');


/* Registers metaboxes. */
require_once('inc/rtbs-metaboxes-tabs.php');
require_once('inc/rtbs-metaboxes-settings.php');
require_once('inc/rtbs-metaboxes-help.php');
require_once('inc/rtbs-metaboxes-pro.php');


/* Saves metaboxes. */
require_once('inc/rtbs-save-metaboxes.php');

?>