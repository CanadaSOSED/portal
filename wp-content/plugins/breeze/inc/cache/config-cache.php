<?php
/**
 *  @copyright 2017  Cloudways  https://www.cloudways.com
 *
 *  Original development of this plugin by JoomUnited https://www.joomunited.com/
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
//Based on some work of simple-cache
if ( ! defined( 'ABSPATH' ) ) exit;

class Breeze_ConfigCache {

    /*
     * Create advanced-cache file
     */
    public function write(){
        global $wp_filesystem;

        $file = untrailingslashit( WP_CONTENT_DIR )  . '/advanced-cache.php';

        $config = get_option('breeze_basic_settings');

        $file_string = '';

        if (!empty($config) && !empty( $config['breeze-active'] ) ) {

            $file_string = '<?php ' .
                "\n\r" . "defined( 'ABSPATH' ) || exit;" .
                "\n\r" . "define( 'BREEZE_ADVANCED_CACHE', true );" .
                "\n\r" . 'if ( is_admin() ) { return; }' .
                "\n\r" . "if ( ! @file_exists( '" . BREEZE_PLUGIN_DIR . "breeze.php' ) ) { return; }" .
                "\n\r" . "if ( ! @file_exists( '". WP_CONTENT_DIR . "/breeze-config/breeze-config.php' ) ) { return; }" .
                "\n\r" . "\$GLOBALS['breeze_config'] = include('". WP_CONTENT_DIR . "/breeze-config/breeze-config.php' );" .
                "\n\r" . "if ( empty( \$GLOBALS['breeze_config'] ) || empty( \$GLOBALS['breeze_config']['cache_options']['breeze-active'] ) ) { return; }" .
                "\n\r" . "if ( @file_exists( '". BREEZE_PLUGIN_DIR . "inc/cache/execute-cache.php' ) ) { include_once( '". BREEZE_PLUGIN_DIR  . "inc/cache/execute-cache.php' ); }" . "\n\r";

        }

        if ( ! $wp_filesystem->put_contents( $file, $file_string ) ) {
            return false;
        }

        return true;
    }

    /**
     * Function write parameter to breeze-config
     * @return breeze_Cache
     */
    public static function write_config_cache(){
        $settings = get_option('breeze_basic_settings');
        $config = get_option('breeze_advanced_settings');
	    $ecommerce_exclude_urls = array();

        $storage = array(
            'homepage' => get_site_url(),
            'cache_options' => $settings,
            'disable_per_adminuser' => 0,
            'exclude_url' => array(),
        );

        if( class_exists('WooCommerce')){
		    $ecommerce_exclude_urls = Breeze_Ecommerce_Cache::factory()->ecommerce_exclude_pages();
	    }
        if(!empty($settings['breeze-disable-admin'])){
            $storage['disable_per_adminuser'] = $settings['breeze-disable-admin'];
        }

        $storage['exclude_url'] = array_merge($ecommerce_exclude_urls, $config['breeze-exclude-urls']);

        if(! self::write_config($storage)){
            return false;
        }
        return true;
    }

    /*
     *    create file config storage parameter used for cache
     */
    public static function write_config( $config ) {

        global $wp_filesystem;

        $config_dir = WP_CONTENT_DIR  . '/breeze-config';

        $site_url_parts = parse_url( site_url() );

        $config_file = $config_dir  . '/breeze-config.php';

        $wp_filesystem->mkdir( $config_dir );

        $config_file_string = '<?php ' . "\n\r" . "defined( 'ABSPATH' ) || exit;" . "\n\r" . 'return ' . var_export( $config, true ) . '; ' . "\n\r";
        if ( ! $wp_filesystem->put_contents( $config_file, $config_file_string ) ) {
            return false;
        }

        return true;
    }
    //turn on / off wp cache
    public function toggle_caching( $status ) {

        global $wp_filesystem;
        if ( defined( 'WP_CACHE' ) && WP_CACHE === $status ) {
            return;
        }

        // Lets look 4 levels deep for wp-config.php
        $levels = 4;

        $file = '/wp-config.php';
        $config_path = false;

        for ( $i = 1; $i <= 3; $i++ ) {
            if ( $i > 1 ) {
                $file = '/..' . $file;
            }

            if ( $wp_filesystem->exists( untrailingslashit( ABSPATH )  . $file ) ) {
                $config_path = untrailingslashit( ABSPATH )  . $file;
                break;
            }
        }

        // Couldn't find wp-config.php
        if ( ! $config_path ) {
            return false;
        }

        $config_file_string = $wp_filesystem->get_contents( $config_path );

        // Config file is empty. Maybe couldn't read it?
        if ( empty( $config_file_string ) ) {
            return false;
        }

        $config_file = preg_split( "#(\n|\r)#", $config_file_string );
        $line_key = false;

        foreach ( $config_file as $key => $line ) {
            if ( ! preg_match( '/^\s*define\(\s*(\'|")([A-Z_]+)(\'|")(.*)/', $line, $match ) ) {
                continue;
            }

            if ( $match[2] == 'WP_CACHE' ) {
                $line_key = $key;
            }
        }

        if ( $line_key !== false ) {
            unset( $config_file[ $line_key ] );
        }

        $status_string = ( $status ) ? 'true' : 'false';

        array_shift( $config_file );
        array_unshift( $config_file, '<?php', "define( 'WP_CACHE', $status_string ); " );

        foreach ( $config_file as $key => $line ) {
            if ( '' === $line ) {
                unset( $config_file[$key] );
            }
        }

        if ( ! $wp_filesystem->put_contents( $config_path, implode( PHP_EOL, $config_file ) ) ) {
            return false;
        }

        return true;
    }
    //delete file for clean up

    public function clean_up() {

        global $wp_filesystem;
        $file = untrailingslashit( WP_CONTENT_DIR )  . '/advanced-cache.php';

        $ret = true;

        if ( ! $wp_filesystem->delete( $file ) ) {
            $ret = false;
        }

        $folder = untrailingslashit( WP_CONTENT_DIR )  . '/cache/breeze';

        if ( ! $wp_filesystem->delete( $folder, true ) ) {
            $ret = false;
        }

        $folder = untrailingslashit( WP_CONTENT_DIR )  . '/cache/breeze-minification';

        if ( ! $wp_filesystem->delete( $folder, true ) ) {
            $ret = false;
        }

        return $ret;
    }

    //delete config file
    public function clean_config() {

        global $wp_filesystem;

        $folder = untrailingslashit( WP_CONTENT_DIR )  . '/breeze-config';
        if ( ! $wp_filesystem->delete( $folder, true ) ) {
            return false;
        }

        return true;
    }


    public static function factory() {

        static $instance;

        if ( ! $instance ) {
            $instance = new self();
        }

        return $instance;
    }
}