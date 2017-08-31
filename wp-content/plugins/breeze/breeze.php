<?php
/**
 * Plugin Name: Breeze
 * Description: Breeze is a WordPress cache plugin with extensive options to speed up your website. All the options including Varnish Cache are compatible with Cloudways hosting.
 * Version: 1.0.3-beta
 * Text Domain: breeze
 * Domain Path: /languages
 * Author: Cloudways
 * Author URI: https://www.cloudways.com
 * License: GPL2
 */

/**
 *  @copyright 2017  Cloudways  https://www.cloudways.com
 *
 *  This plugin is inspired from WP Speed of Light by JoomUnited.
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

defined('ABSPATH') || die('No direct script access allowed!');

if (!defined('BREEZE_PLUGIN_DIR'))
    define('BREEZE_PLUGIN_DIR', plugin_dir_path(__FILE__));
if (!defined('BREEZE_VERSION'))
    define('BREEZE_VERSION','1.0.1');
if (!defined('BREEZE_SITEURL'))
    define('BREEZE_SITEURL', get_site_url());
if (!defined('BREEZE_MINIFICATION_CACHE'))
    define('BREEZE_MINIFICATION_CACHE', WP_CONTENT_DIR . '/cache/breeze-minification/');
if (!defined('BREEZE_CACHEFILE_PREFIX'))
    define('BREEZE_CACHEFILE_PREFIX', 'breeze_');
if (!defined('BREEZE_CACHE_CHILD_DIR'))
    define('BREEZE_CACHE_CHILD_DIR', '/cache/breeze-minification/');
if (!defined('BREEZE_WP_CONTENT_NAME'))
    define('BREEZE_WP_CONTENT_NAME', '/' . wp_basename(WP_CONTENT_DIR));
if (!defined('BREEZE_BASENAME'))
    define('BREEZE_BASENAME',plugin_basename(__FILE__));

define('BREEZE_CACHE_DELAY', true);
define('BREEZE_CACHE_NOGZIP', true);
define('BREEZE_ROOT_DIR', str_replace(BREEZE_WP_CONTENT_NAME, '', WP_CONTENT_DIR));
//action to purge cache
require_once(BREEZE_PLUGIN_DIR . 'inc/cache/purge-varnish.php');
require_once(BREEZE_PLUGIN_DIR . 'inc/cache/purge-cache.php');
require_once(BREEZE_PLUGIN_DIR . 'inc/cache/purge-per-time.php');

// Activate plugin hook
register_activation_hook(__FILE__,array('Breeze_Admin','plugin_active_hook'));
//Deactivate plugin hook
register_deactivation_hook(__FILE__,array('Breeze_Admin','plugin_deactive_hook'));


if(is_admin()){
    require_once(BREEZE_PLUGIN_DIR . 'inc/breeze-admin.php');
    require_once(BREEZE_PLUGIN_DIR . 'inc/breeze-configuration.php');
    //config to cache
    require_once(BREEZE_PLUGIN_DIR . 'inc/cache/config-cache.php');

	//cache when ecommerce installed
	require_once( BREEZE_PLUGIN_DIR . 'inc/cache/ecommerce-cache.php');
	new Breeze_Ecommerce_Cache();
}else{
    $cdn_conf = get_option('breeze_cdn_integration');
    $basic_conf = get_option('breeze_basic_settings');

    if(!empty($cdn_conf['cdn-active']) || !empty($basic_conf['breeze-minify-js']) || !empty($basic_conf['breeze-minify-css']) || !empty($basic_conf['breeze-minify-html'])) {
        // Call back ob start
        ob_start('breeze_ob_start_callback');
    }
}

// Call back ob start - stack
function breeze_ob_start_callback($buffer){
    $conf = get_option('breeze_cdn_integration');
    // Get buffer from minify
    $buffer = apply_filters('breeze_minify_content_return',$buffer);

    if(!empty($conf) || !empty($conf['cdn-active'])){
        // Get buffer after remove query strings
        $buffer = apply_filters('breeze_cdn_content_return',$buffer);
    }
    // Return content
    return $buffer;
}

// Minify
require_once(BREEZE_PLUGIN_DIR . 'inc/minification/breeze-minify-main.php');
require_once(BREEZE_PLUGIN_DIR . 'inc/minification/breeze-minification-cache.php');
new Breeze_Minify();
// CDN Integration
if( !class_exists('Breeze_CDN_Integration')){
    require_once ( BREEZE_PLUGIN_DIR. 'inc/cdn-integration/breeze-cdn-integration.php');
    require_once ( BREEZE_PLUGIN_DIR. 'inc/cdn-integration/breeze-cdn-rewrite.php');
    new Breeze_CDN_Integration();
}