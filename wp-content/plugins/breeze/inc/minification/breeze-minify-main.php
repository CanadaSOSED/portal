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
defined('ABSPATH') || die('No direct script access allowed!');

class Breeze_Minify {

    public function __construct()
    {
        //check disable cache for page
        $domain = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') || $_SERVER['SERVER_PORT']==443) ? 'https://':'http://' ).$_SERVER['HTTP_HOST'];
        $current_url = $domain.$_SERVER['REQUEST_URI'];

        $check_url = $this->check_exclude_url($current_url);

        //load config file when redirect template
        if (!$check_url) {
            //cache html
            //cache minification
            if (Breeze_MinificationCache::create_cache_minification_folder()) {
                $conf = get_option('breeze_basic_settings');
                if ( !empty($conf['breeze-minify-html']) || !empty($conf['breeze-minify-css']) || !empty($conf['breeze-minify-js']) ) {
                    if (defined('breeze_INIT_EARLIER')) {
                        add_action('init', array($this,'breeze_start_buffering'), -1);
                    } else {
                        add_action('template_redirect',  array($this,'breeze_start_buffering'), 2);
                    }
                }
            }
        }

    }
    /*
     * Start buffer
     */
    public function breeze_start_buffering(){
        $ao_noptimize = false;

        // check for DONOTMINIFY constant as used by e.g. WooCommerce POS
        if (defined('DONOTMINIFY') && (constant('DONOTMINIFY') === true || constant('DONOTMINIFY') === "true")) {
            $ao_noptimize = true;
        }
        // filter you can use to block autoptimization on your own terms
        $ao_noptimize = (bool) apply_filters('breeze_filter_noptimize', $ao_noptimize);
        if (!is_feed() && !$ao_noptimize && !is_admin()) {
            // Config element
            $conf = get_option('breeze_basic_settings');
            // Load our base class
            include_once(BREEZE_PLUGIN_DIR . 'inc/minification/breeze-minification-base.php');

            // Load extra classes and set some vars
            if (!empty($conf['breeze-minify-html'])) {
                include_once(BREEZE_PLUGIN_DIR . 'inc/minification/breeze-minification-html.php');
                // BUG: new minify-html does not support keeping HTML comments, skipping for now
                if(!class_exists('Minify_HTML')){
                    @include(BREEZE_PLUGIN_DIR . 'inc/minification/minify/minify-html.php');
                }
            }

            if (!empty($conf['breeze-minify-js'])) {
                include_once(BREEZE_PLUGIN_DIR . 'inc/minification/breeze-minification-scripts.php');
                if (!class_exists('JSMin')) {
                    if (defined('breeze_LEGACY_MINIFIERS')) {
                        @include(BREEZE_PLUGIN_DIR . 'inc/minification/minify/jsmin-1.1.1.php');
                    } else {
                        @include(BREEZE_PLUGIN_DIR . 'inc/minification/minify/minify-2.1.7-jsmin.php');
                    }
                }
                if (!defined('CONCATENATE_SCRIPTS')) {
                    define('CONCATENATE_SCRIPTS', false);
                }
                if (!defined('COMPRESS_SCRIPTS')) {
                    define('COMPRESS_SCRIPTS', false);
                }
            }
            if (!empty($conf['breeze-minify-css'])) {
                include_once(BREEZE_PLUGIN_DIR . 'inc/minification/breeze-minification-styles.php');
                if (defined('breeze_LEGACY_MINIFIERS')) {
                    if (!class_exists('Minify_CSS_Compressor')) {
                        @include(BREEZE_PLUGIN_DIR . 'inc/minification/minify/minify-css-compressor.php');
                    }
                } else {
                    if (!class_exists('CSSmin')) {
                        @include(BREEZE_PLUGIN_DIR . 'inc/minification/minify/yui-php-cssmin-2.4.8-4_fgo.php');
                    }
                }
                if (!defined('COMPRESS_CSS')) {
                    define('COMPRESS_CSS', false);
                }
            }
            // Now, start the real thing!
            add_filter('breeze_minify_content_return',array($this,'breeze_end_buffering'));
        }
    }

    /*
     * Minify css , js and optimize html when start
     */

    public function breeze_end_buffering($content) {
        if (stripos($content, "<html") === false || stripos($content, "<html amp") !== false || stripos($content, "<html ⚡") !== false || stripos($content, "<xsl:stylesheet") !== false) {
            return $content;
        }
        // load URL constants as late as possible to allow domain mapper to kick in
        if (function_exists("domain_mapping_siteurl")) {
            define('breeze_WP_SITE_URL', domain_mapping_siteurl(get_current_blog_id()));
            define('breeze_WP_CONTENT_URL', str_replace(get_original_url(breeze_WP_SITE_URL), breeze_WP_SITE_URL, content_url()));
        } else {
            define('breeze_WP_SITE_URL', site_url());
            define('breeze_WP_CONTENT_URL', content_url());
        }
        if (is_multisite() && apply_filters('breeze_separate_blog_caches', true)) {
            $blog_id = get_current_blog_id();
            define('breeze_CACHE_URL', breeze_WP_CONTENT_URL . BREEZE_CACHE_CHILD_DIR . $blog_id . '/');
        } else {
            define('breeze_CACHE_URL', breeze_WP_CONTENT_URL . BREEZE_CACHE_CHILD_DIR);
        }
        define('breeze_WP_ROOT_URL', str_replace(BREEZE_WP_CONTENT_NAME, '', breeze_WP_CONTENT_URL));

        define('breeze_HASH',wp_hash(breeze_CACHE_URL));
        // Config element
        $conf = get_option('breeze_basic_settings');
        $minify = get_option('breeze_advanced_settings');

        // Choose the classes
        $classes = array();
        if (!empty($conf['breeze-minify-js']))
            $classes[] = 'Breeze_MinificationScripts';
        if (!empty($conf['breeze-minify-css']))
            $classes[] = 'Breeze_MinificationStyles';
        if (!empty($conf['breeze-minify-html']))
            $classes[] = 'Breeze_MinificationHtml';
        $groupcss = false;
        $groupjs = false;
        if (!empty($minify['breeze-group-css'])){
            $groupcss = true;
        }
        if (!empty($minify['breeze-group-js'])){
            $groupjs = true;
        }
        // Set some options
        $classoptions = array(
            'Breeze_MinificationScripts' => array(
                'justhead' => false,
                'forcehead' => false,
                'trycatch' => false,
                'js_exclude' => "s_sid, smowtion_size, sc_project, WAU_, wau_add, comment-form-quicktags, edToolbar, ch_client, seal.js",
                'cdn_url' => "",
                'include_inline' => true,
                'group_js' => $groupjs,
                'custom_js_exclude' => $minify['breeze-exclude-js']
            ),
            'Breeze_MinificationStyles' => array(
                'justhead' => false,
                'datauris' => false,
                'defer' => false,
                'defer_inline' => false,
                'inline' => false,
                'css_exclude' => "admin-bar.min.css, dashicons.min.css",
                'cdn_url' => "",
                'include_inline' => true,
                'nogooglefont' => false,
                'groupcss' => $groupcss,
                'custom_css_exclude' => $minify['breeze-exclude-css']
            ),
            'Breeze_MinificationHtml' => array(
                'keepcomments' => false
            )
        );

        $content = apply_filters('breeze_filter_html_before_minify', $content);

        if (!empty($conf) && $conf['breeze-disable-admin'] && current_user_can('manage_options')) {
            $content = apply_filters('breeze_html_after_minify', $content);
        }else{
            // Run the classes
            foreach ($classes as $name) {
                $instance = new $name($content);

                if ($instance->read($classoptions[$name])) {
                    $instance->minify();
                    $instance->cache();
                    $content = $instance->getcontent();
                }
                unset($instance);
            }
            $content = apply_filters('breeze_html_after_minify', $content);
        }
        return $content;
    }
    /*
     * check url from Never cache the following pages area
     */
    public function check_exclude_url($current_url){
        $opts_config = get_option('breeze_advanced_settings');
	    //check disable cache for page
	    if (!empty($opts_config['breeze-exclude-urls'])) {
		    foreach ($opts_config['breeze-exclude-urls'] as $v) {
			    // Clear blank character
			    $v = trim($v);
			    if( preg_match( '/(\&?\/?\(\.?\*\)|\/\*|\*)$/', $v , $matches)){
				    // End of rules is *, /*, [&][/](*) , [&][/](.*)
				    $pattent = substr($v , 0, strpos($v,$matches[0]));
				    if($v[0] == '/'){
					    // A path of exclude url with regex
					    if((@preg_match( '@'.$pattent.'@', $current_url, $matches ) > 0)){
						    return true;
					    }
				    }else{
					    // Full exclude url with regex
					    if(strpos( $current_url,$pattent) !== false){
						    return true;
					    }
				    }

			    }else{
				    // Whole path
				    if($v == $current_url){
					    return true;
				    }
			    }
		    }
	    }

	    return false;

    }
}