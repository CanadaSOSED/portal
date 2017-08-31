<?php
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

class Breeze_Admin {
    public function __construct(){
        add_action('init', function(){
            load_plugin_textdomain('breeze', false, dirname(plugin_basename(__FILE__)) . '/languages/');
        });

        // Add our custom action to clear cache
        add_action('breeze_clear_all_cache', array($this, 'breeze_clear_all_cache'));
        add_action('breeze_clear_varnish', array($this, 'breeze_clear_varnish'));

        add_action('admin_init', array($this, 'admin_init'));
        //register menu
        add_action('admin_menu', array($this, 'register_menu_page'));

        // Add notice when installing plugin
	    $first_install = get_option('breeze_first_install');
	    if ($first_install === false) {
	    	add_option('breeze_first_install', 'yes');
	    }
	    if ($first_install == 'yes') {
		    add_action('admin_notices', array($this, 'installing_notices'));
	    }

        $config = get_option('breeze_basic_settings');

        if(isset($config['breeze-display-clean']) && $config['breeze-display-clean']){
            //register top bar menu
            add_action('admin_bar_menu', array($this, 'register_admin_bar_menu'), 999);
        }

        /** Load admin js * */
        add_action('admin_enqueue_scripts', array($this, 'loadAdminScripts'));

        add_action('wp_head', array($this,'define_ajaxurl'));
        $this->ajaxHandle();

        add_filter( 'plugin_action_links_'.BREEZE_BASENAME, array($this,'breeze_add_action_links') );
    }

    /**
     * Admin Init
     *
     */
    public function admin_init()
    {
        //Check plugin requirements
        if (version_compare(PHP_VERSION, '5.3', '<')) {
            if (current_user_can('activate_plugins') && is_plugin_active(plugin_basename(__FILE__))) {
                deactivate_plugins(__FILE__);
                add_action('admin_notices', array($this, 'breeze_show_error'));
                unset($_GET['activate']);
            }
        }
        //Do not load anything more
        return;
    }

    //define ajaxurl
    function define_ajaxurl() {
        if(current_user_can('manage_options')){
            echo '<script type="text/javascript">
           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
             </script>';
        }
    }

    // Add notice message when install plugin
	public function installing_notices() {
		$class = 'notice notice-success';
		$message = __('Thanks for installing Breeze. It is always recommended not to use more than one caching plugin at the same time. We recommend you to purge cache if necessary.', 'breeze');

		printf( '<div class="%1$s"><p>%2$s <button class="button" id="breeze-hide-install-msg">'.__("Hide message", "breeze").'</button></p></div>', esc_attr( $class ), esc_html( $message ));
		update_option('breeze_first_install', 'no');
	}


	function loadAdminScripts() {
        wp_enqueue_script('jquery');
        wp_enqueue_script('breeze-backend', plugins_url('assets/js/breeze-backend.js', dirname(__FILE__)), array('jquery'), BREEZE_VERSION, true);
        $current_screen = get_current_screen();
        if($current_screen->base == 'settings_page_breeze'){
            //add css
            wp_enqueue_style('breeze-style', plugins_url('assets/css/style.css', dirname(__FILE__)));
            //js
            wp_enqueue_script('breeze-configuration', plugins_url('assets/js/breeze-configuration.js', dirname(__FILE__)), array('jquery'), BREEZE_VERSION, true);
        }

        $token_name = array(
            'breeze_purge_varnish' => wp_create_nonce("_breeze_purge_varnish"),
            'breeze_purge_database' => wp_create_nonce("_breeze_purge_database"),
            'breeze_purge_cache' => wp_create_nonce("_breeze_purge_cache")
        );

        wp_localize_script('breeze-backend','breeze_token_name',$token_name);
    }

    /**
     * Register menu
     *
     */
    function register_menu_page()
    {
        //add submenu for cloudsway
        add_submenu_page( 'options-general.php',  __('Breeze', 'breeze'),   __('Breeze', 'breeze'),  'manage_options',  'breeze', array($this, 'breeze_load_page')  );
    }


    /**
     * Register bar menu
     *
     */
    function register_admin_bar_menu(WP_Admin_Bar $wp_admin_bar)
    {
        if (current_user_can('manage_options')) {
            // add a parent item
            $args = array(
                'id' => 'breeze-topbar',
                'title' => esc_html(__('Breeze', 'breeze')),
                'meta' => array(
                    'classname' => 'breeze',
                ),
            );
            $wp_admin_bar->add_node( $args );

            // add purge all item
            $args = array(
                'id'     => 'breeze-purge-all',
                'title'  => esc_html(__('Purge All Cache', 'breeze')),
                'href' => wp_nonce_url(add_query_arg('breeze_purge', 1), 'breeze_purge_cache'),
                'parent' => 'breeze-topbar',
                'meta'   => array( 'class' => 'breeze-toolbar-group' ),
            );
            $wp_admin_bar->add_node( $args );

            // add purge modules group
            $args = array(
                'id'     => 'breeze-purge-modules',
                'title'  => esc_html(__('Purge Modules', 'breeze')),
                'href' => '#',
                'parent' => 'breeze-topbar',
                'meta'   => array( 'class' => 'breeze-toolbar-group' ),
            );
            $wp_admin_bar->add_node( $args );


            // add child item (Purge Modules)
            $args = array(
                'id'     => 'breeze-purge-varnish-group',
                'title'  => esc_html(__('Purge Varnish Cache', 'breeze')),
                'href' => '#',
                'parent' => 'breeze-purge-modules',
            );
            $wp_admin_bar->add_node( $args );

            // add child item (Purge Modules)
            $args = array(
                'id'     => 'breeze-purge-file-group',
                'title'  => esc_html(__('Purge Internal Cache', 'breeze')),
                'href' => '#',
                'parent' => 'breeze-purge-modules',
            );
            $wp_admin_bar->add_node( $args );

            // add settings item
            $args = array(
                'id'     => 'breeze-settings',
                'title'  => esc_html(__('Settings', 'breeze')),
                'parent' => 'breeze-topbar',
                'href' => 'options-general.php?page=breeze',
                'meta'   => array( 'class' => 'breeze-toolbar-group' ),
            );
            $wp_admin_bar->add_node( $args );

            // add support item
            $args = array(
                'id'     => 'breeze-support',
                'title'  => esc_html(__('Support', 'breeze')),
                'href' => 'https://support.cloudways.com/breeze-wordpress-cache-configuration',
                'parent' => 'breeze-topbar',
                'meta'   => array( 'class' => 'breeze-toolbar-group',
                    'target' => '_blank'),
            );
            $wp_admin_bar->add_node( $args );

	        // add feedback item
	        $args = array(
		        'id'     => 'breeze-feedback',
		        'title'  => esc_html(__('Feedback', 'breeze')),
		        'href' => 'https://www.surveymonkey.com/r/YNV2XVL',
		        'parent' => 'breeze-topbar',
		        'meta'   => array( 'class' => 'breeze-toolbar-group',
		                           'target' => '_blank'),
	        );
	        $wp_admin_bar->add_node( $args );

        }
    }

    function breeze_load_page()
    {
        if (isset($_GET['page']) && $_GET['page'] == 'breeze') {
            require_once (BREEZE_PLUGIN_DIR . 'views/breeze-setting-views.php');
        }
    }

    public function breeze_show_error()
    {
        echo '<div class="error"><p><strong>Breeze</strong> need at least PHP 5.3 version, please update php before installing the plugin.</p></div>';
    }
    //ajax admin
    function ajaxHandle() {
        add_action('wp_ajax_breeze_purge_varnish', array('Breeze_Configuration', 'purge_varnish_action'));
        add_action('wp_ajax_breeze_purge_file', array('Breeze_Configuration', 'breeze_ajax_clean_cache'));
        add_action('wp_ajax_breeze_purge_database', array('Breeze_Configuration', 'breeze_ajax_purge_database'));
    }
    /*
     * Register active plugin hook
     */
    public static function plugin_active_hook(){
        WP_Filesystem();
        // Default basic
        $basic = get_option('breeze_basic_settings');
        if(empty($basic)) $basic = array();
        $default_basic = array(
            'breeze-active' => '1',
            'breeze-ttl' => '',
            'breeze-minify-html' => '0',
            'breeze-minify-css' => '0',
            'breeze-minify-js' => '0',
            'breeze-gzip-compression' => '1',
            'breeze-desktop-cache' => '1',
            'breeze-browser-cache' => '1',
            'breeze-mobile-cache' => '1',
            'breeze-disable-admin' => '1',
            'breeze-display-clean' => '1',
        );
        $basic= array_merge($default_basic,$basic);

        // Default Advanced
        $advanced = get_option('breeze_advanced_settings');
        if(empty($advanced)) $advanced = array();
        $default_advanced = array(
            'breeze-exclude-urls' => array(),
            'breeze-group-css' => '0',
            'breeze-group-js' => '0',
            'breeze-exclude-css' => array(),
            'breeze-exclude-js' => array()
        );
        $advanced= array_merge($default_advanced,$advanced);

        //CDN default
        $cdn = get_option('breeze_cdn_integration');
        if(empty($cdn)) $cdn = array();
        $wp_content = substr(WP_CONTENT_DIR,strlen(ABSPATH));
        $default_cdn = array(
            'cdn-active' => '0',
            'cdn-url' =>'',
            'cdn-content' => array('wp-includes',$wp_content),
            'cdn-exclude-content' => array('.php'),
            'cdn-relative-path' =>'1',
        );
        $cdn= array_merge($default_cdn,$cdn);

        // Varnish default
        $varnish = get_option('breeze_varnish_cache');
        if(empty($varnish)) $varnish = array();
        $default_varnish = array(
            'auto-purge-varnish' => '1',
        );
        $varnish= array_merge($default_varnish,$varnish);

        if(is_multisite()){
            $blogs = get_sites();
            foreach ($blogs as $blog){
                update_blog_option((int)$blog->blog_id,'breeze_basic_settings', $basic);
                update_blog_option((int)$blog->blog_id,'breeze_advanced_settings', $advanced);
                update_blog_option((int)$blog->blog_id,'breeze_cdn_integration', $cdn);
                update_blog_option((int)$blog->blog_id,'breeze_varnish_cache', $varnish);
            }
        }else{
            update_option('breeze_basic_settings', $basic);
            update_option('breeze_advanced_settings', $advanced);
            update_option('breeze_cdn_integration', $cdn);
            update_option('breeze_varnish_cache', $varnish);
        }

        //add header to htaccess if setting is enabled or by default if first installed
	    if ($basic['breeze-browser-cache'] == 1) {
		    Breeze_Configuration::add_expires_header( true );
	    }
	    if ($basic['breeze-gzip-compression'] == 1) {
		    Breeze_Configuration::add_gzip_htacess( true );
	    }
        //automatic config start cache
        Breeze_ConfigCache::factory()->write();
        Breeze_ConfigCache::factory()->write_config_cache();

        if ( !empty($basic) && !empty($basic['breeze-active'] )) {
            Breeze_ConfigCache::factory()->toggle_caching( true );
        }
    }

    /*
     * Register deactive plugin hook
     */
    public static function plugin_deactive_hook(){
        WP_Filesystem();
        Breeze_ConfigCache::factory()->clean_up();
        Breeze_ConfigCache::factory()->clean_config();
        Breeze_ConfigCache::factory()->toggle_caching(false);
	    Breeze_Configuration::add_expires_header(false);
	    Breeze_Configuration::add_gzip_htacess(false);
    }

    /*
     * Render tab
     */
    public static function render($tab){
        require_once (BREEZE_PLUGIN_DIR . 'views/tabs/'.$tab.'.php');
    }

    // Check varnish cache exist
    public static function check_varnish(){
        if(isset($_SERVER['HTTP_X_VARNISH'])){
            return true;
        }
        return false;
    }

    // Applied to the list of links to display on the plugins page
    public function breeze_add_action_links($links){

        $mylinks = array(
            '<a href="' . admin_url( 'options-general.php?page=breeze' ) . '">Settings</a>',
        );
        return array_merge( $mylinks,$links );
    }

	// Clear all cache action
	public function breeze_clear_all_cache() {
		//delete minify
		Breeze_MinificationCache::clear_minification();
		//clear normal cache
		Breeze_PurgeCache::breeze_cache_flush();
		//clear varnish cache
		$this->breeze_clear_varnish();
	}

	// Clear all varnish cache action
	public function breeze_clear_varnish() {
		$homepage = home_url().'/?breeze';
		$main = new Breeze_PurgeVarnish();
		$main->purge_cache($homepage);
	}
}

$admin = new Breeze_Admin();
