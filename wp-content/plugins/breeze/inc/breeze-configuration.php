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

class Breeze_Configuration{
    public function __construct()
    {
        add_action( 'load-settings_page_breeze', array($this,'afterLoadConfigPage') );
    }


    /*
     * function to save settings
     */
    public function afterLoadConfigPage()
    {
        // Basic options tab
        if (isset($_REQUEST['breeze_basic_action']) && $_REQUEST['breeze_basic_action'] == 'breeze_basic_settings') {
            if (isset($_POST['breeze_settings_basic_nonce']) || wp_verify_nonce($_POST['breeze_settings_basic_nonce'], 'breeze_settings_basic')) {
                WP_Filesystem();

                $basic = array(
                    'breeze-active' =>(isset($_POST['cache-system']) ? '1' : '0'),
                    'breeze-ttl' => (int)$_POST['cache-ttl'],
                    'breeze-minify-html' => (isset($_POST['minification-html']) ? '1' : '0'),
                    'breeze-minify-css' => (isset($_POST['minification-css']) ? '1' : '0'),
                    'breeze-minify-js' => (isset($_POST['minification-js']) ? '1' : '0'),
                    'breeze-gzip-compression' => (isset($_POST['gzip-compression']) ? '1' : '0'),
                    'breeze-browser-cache' => (isset($_POST['browser-cache']) ? '1' : '0'),
                    'breeze-desktop-cache' => (int)$_POST['desktop-cache'],
                    'breeze-mobile-cache' => (int)$_POST['mobile-cache'],
                    'breeze-disable-admin' => '1',
                    'breeze-display-clean' => '1'
                );
                update_option('breeze_basic_settings',$basic);

                // Storage infomation to cache pages
                Breeze_ConfigCache::factory()->write();
                Breeze_ConfigCache::factory()->write_config_cache();

                // Turn on WP_CACHE to support advanced-cache file
                if (isset($_POST['cache-system'])) {
                    Breeze_ConfigCache::factory()->toggle_caching(true);
                } else {
                    Breeze_ConfigCache::factory()->toggle_caching(false);
                }

                // Reschedule cron events
                if(isset($_POST['cache-system'])){
                    Breeze_PurgeCacheTime::factory()->unschedule_events();
                    Breeze_PurgeCacheTime::factory()->schedule_events();
                }
                // Add expires header
                if(isset($_POST['gzip-compression'])){
                    self::add_gzip_htacess(true);
                }else{
                    self::add_gzip_htacess(false);
                }
                // Add expires header
                if(isset($_POST['browser-cache'])){
                    self::add_expires_header(true);
                }else{
                    self::add_expires_header(false);
                }

                //delete minify
                Breeze_MinificationCache::clear_minification();
                Breeze_PurgeCache::breeze_cache_flush();
                // Clear varnish cache after settings
                $this->clear_varnish();
            }
        }
        // Advanced options tab
        if (isset($_REQUEST['breeze_advanced_action']) && $_REQUEST['breeze_advanced_action'] == 'breeze_advanced_settings') {
            if (isset($_POST['breeze_settings_advanced_nonce']) || wp_verify_nonce($_POST['breeze_settings_advanced_nonce'], 'breeze_settings_advanced')) {
                $exclude_urls = $this->string_convert_arr(sanitize_textarea_field($_POST['exclude-urls']));
                $exclude_css = $this->string_convert_arr(sanitize_textarea_field($_POST['exclude-css']));
                $exclude_js = $this->string_convert_arr(sanitize_textarea_field($_POST['exclude-js']));
                $advanced = array(
                    'breeze-exclude-urls' => $exclude_urls,
                    'breeze-group-css' => (isset($_POST['group-css']) ? '1' : '0'),
                    'breeze-group-js' => (isset($_POST['group-js']) ? '1' : '0'),
                    'breeze-exclude-css' => $exclude_css,
                    'breeze-exclude-js' => $exclude_js
                );
                update_option('breeze_advanced_settings',$advanced);

                WP_Filesystem();
                // Storage infomation to cache pages
                Breeze_ConfigCache::factory()->write_config_cache();

                //delete minify
                Breeze_MinificationCache::clear_minification();
                Breeze_PurgeCache::breeze_cache_flush();
                // Clear varnish cache after settings
                $this->clear_varnish();
            }
        }

        // Database option tab
        if (isset($_REQUEST['breeze_database_action']) && $_REQUEST['breeze_database_action'] == 'breeze_database_settings') {
            if (isset($_POST['breeze_settings_database_nonce']) || wp_verify_nonce($_POST['breeze_settings_database_nonce'], 'breeze_settings_database')) {
                if(isset($_POST['clean'])){
                    foreach ($_POST['clean'] as $item){
                        $this->cleanSystem($item);
                    }

                    //return current page
                    if (!empty($_REQUEST['_wp_http_referer'])) {
                        wp_redirect($_REQUEST['_wp_http_referer'].'&database-cleanup=success');
                        exit;
                    }
                }
            }
        }

        // Cdn option tab
        if (isset($_REQUEST['breeze_cdn_action']) && $_REQUEST['breeze_cdn_action'] == 'breeze_cdn_settings') {
            if (isset($_POST['breeze_settings_cdn_nonce']) || wp_verify_nonce($_POST['breeze_settings_cdn_nonce'], 'breeze_settings_cdn')) {
                $cdn_content = array();
                $exclude_content = array();
                if(!empty($_POST['cdn-content'])){
                    $cdn_content = explode(',',sanitize_text_field($_POST['cdn-content']));
                    $cdn_content = array_unique($cdn_content);
                }
                if(!empty($_POST['cdn-exclude-content'])){
                    $exclude_content = explode(',',sanitize_text_field($_POST['cdn-exclude-content']));
                    $exclude_content = array_unique($exclude_content);
                }

                $cdn = array(
                    'cdn-active' => (isset($_POST['activate-cdn']) ? '1' : '0'),
                    'cdn-url' =>(isset($_POST['cdn-url']) ? sanitize_text_field($_POST['cdn-url']) : ''),
                    'cdn-content' => $cdn_content,
                    'cdn-exclude-content' => $exclude_content,
                    'cdn-relative-path' =>(isset($_POST['cdn-relative-path']) ? '1' : '0'),
                );

                update_option('breeze_cdn_integration', $cdn);

                //delete minify && normal cache
                Breeze_MinificationCache::clear_minification();
                Breeze_PurgeCache::breeze_cache_flush();
                // Clear varnish cache after settings
               $this->clear_varnish();
            }
        }

        // Varnish option tab
        if (isset($_REQUEST['breeze_varnish_action']) && $_REQUEST['breeze_varnish_action'] == 'breeze_varnish_settings') {
            if (isset($_POST['breeze_settings_varnish_nonce']) || wp_verify_nonce($_POST['breeze_settings_varnish_nonce'], 'breeze_settings_varnish')) {
                $varnish = array(
                    'auto-purge-varnish' => (isset($_POST['auto-purge-varnish']) ? '1' : '0'),
                    'breeze-varnish-server-ip' => preg_replace('/[^a-zA-Z0-9\-\_\.]*/','',$_POST['varnish-server-ip'])
                );
                update_option('breeze_varnish_cache',$varnish);

                // Clear varnish cache after settings
                $this->clear_varnish();
            }
        }


        //return current page
        if (!empty($_REQUEST['_wp_http_referer'])) {
            wp_redirect($_REQUEST['_wp_http_referer'].'&save-settings=success');
            exit;
        }

        return true;
    }

    /*
 * function add expires header to .htaccess
 */
    public static function add_expires_header($check) {
        $expires = "#Expires headers configuration added by BREEZE WP CACHE plugin" . PHP_EOL .
            "<IfModule mod_expires.c>" . PHP_EOL .
            "   ExpiresActive On" . PHP_EOL .
            "   ExpiresDefault A2592000" . PHP_EOL .
            "   ExpiresByType application/javascript \"access plus 7 days\"" . PHP_EOL .
            "   ExpiresByType text/javascript \"access plus 7 days\"" . PHP_EOL .
            "   ExpiresByType text/css \"access plus 7 days\"" . PHP_EOL .
            "   ExpiresByType image/jpeg \"access plus 7 days\"" . PHP_EOL .
            "   ExpiresByType image/png \"access plus 7 days\"" . PHP_EOL .
            "   ExpiresByType image/gif \"access plus 7 days\"" . PHP_EOL .
            "   ExpiresByType image/ico \"access plus 7 days\"" . PHP_EOL .
            "   ExpiresByType image/x-icon \"access plus 7 days\"" . PHP_EOL .
            "   ExpiresByType image/svg+xml \"access plus 7 days\"" . PHP_EOL .
            "   ExpiresByType image/bmp \"access plus 7 days\"" . PHP_EOL .
            "</IfModule>" . PHP_EOL .
            "#End of expires headers configuration" . PHP_EOL ;

        if ($check) {
            if (!is_super_admin()) {
                return FALSE;
            }
            //open htaccess file
            $htaccessContent = file_get_contents(ABSPATH . DIRECTORY_SEPARATOR . '.htaccess');
            if (empty($htaccessContent)) {
                return FALSE;
            }
            //if isset expires header in htacces
            if (strpos($htaccessContent, 'mod_expires') !== false || strpos($htaccessContent, 'ExpiresActive') !== false || strpos($htaccessContent, 'ExpiresDefault') !== false || strpos($htaccessContent, 'ExpiresByType') !== false) {
                return FALSE;
            }

            $htaccessContent = $expires.$htaccessContent;
            file_put_contents(ABSPATH . DIRECTORY_SEPARATOR . '.htaccess', $htaccessContent);
            return TRUE;

        } else {
            if (!is_super_admin()) {
                return FALSE;
            }
            //open htaccess file
            $htaccessContent = file_get_contents(ABSPATH . DIRECTORY_SEPARATOR . '.htaccess');
            if (empty($htaccessContent)) {
                return FALSE;
            }

            $htaccessContent = str_replace($expires,'',$htaccessContent);
            file_put_contents(ABSPATH . DIRECTORY_SEPARATOR . '.htaccess', $htaccessContent);
            return TRUE;
        }
    }
    /*
   * function add gzip header to .htaccess
   */
    public static function add_gzip_htacess($check){
        $data = "# Begin GzipofBreezeWPCache".PHP_EOL.
            "<IfModule mod_deflate.c>".PHP_EOL.
            "AddType x-font/woff .woff".PHP_EOL.
            "AddOutputFilterByType DEFLATE image/svg+xml".PHP_EOL.
            "AddOutputFilterByType DEFLATE text/plain".PHP_EOL.
            "AddOutputFilterByType DEFLATE text/html".PHP_EOL.
            "AddOutputFilterByType DEFLATE text/xml".PHP_EOL.
            "AddOutputFilterByType DEFLATE text/css".PHP_EOL.
            "AddOutputFilterByType DEFLATE text/javascript".PHP_EOL.
            "AddOutputFilterByType DEFLATE application/xml".PHP_EOL.
            "AddOutputFilterByType DEFLATE application/xhtml+xml".PHP_EOL.
            "AddOutputFilterByType DEFLATE application/rss+xml".PHP_EOL.
            "AddOutputFilterByType DEFLATE application/javascript".PHP_EOL.
            "AddOutputFilterByType DEFLATE application/x-javascript".PHP_EOL.
            "AddOutputFilterByType DEFLATE application/x-font-ttf".PHP_EOL.
            "AddOutputFilterByType DEFLATE application/vnd.ms-fontobject".PHP_EOL.
            "AddOutputFilterByType DEFLATE font/opentype font/ttf font/eot font/otf".PHP_EOL.
            "</IfModule>".PHP_EOL.
            "# End GzipofBreezeWPCache" . PHP_EOL ;
        if ($check) {
            if (!is_super_admin()) {
                return FALSE;
            }
            //open htaccess file
            $htaccessContent = file_get_contents(ABSPATH . DIRECTORY_SEPARATOR . '.htaccess');
            if (empty($htaccessContent)) {
                return FALSE;
            }
            //if isset Gzip access
            if (strpos($htaccessContent, 'mod_deflate') !== false || strpos($htaccessContent, 'AddOutputFilterByType') !== false || strpos($htaccessContent, 'AddType') !== false || strpos($htaccessContent,'GzipofBreezeWPCache') !== false) {
                return FALSE;
            }

            $htaccessContent = $data.$htaccessContent;
            file_put_contents(ABSPATH . DIRECTORY_SEPARATOR . '.htaccess', $htaccessContent);
            return TRUE;

        } else {
            if (!is_super_admin()) {
                return FALSE;
            }
            //open htaccess file
            $htaccessContent = file_get_contents(ABSPATH . DIRECTORY_SEPARATOR . '.htaccess');
            if (empty($htaccessContent)) {
                return FALSE;
            }

            $htaccessContent = str_replace($data,'',$htaccessContent);
            file_put_contents(ABSPATH . DIRECTORY_SEPARATOR . '.htaccess', $htaccessContent);
            return TRUE;
        }
    }
    /*
    * Database clean tab
    * funtion to clean in database
    */
    public static function cleanSystem($type){
        global $wpdb;
        $clean = "";

        switch ($type){
            case "revisions":
                $clean = "DELETE FROM `$wpdb->posts` WHERE post_type = 'revision';";
                $revisions = $wpdb->query( $clean );

                $message = "All post revisions";
                break;
            case "drafted":
                $clean = "DELETE FROM `$wpdb->posts` WHERE post_status = 'auto-draft';";
                $autodraft = $wpdb->query( $clean );

                $message = "All auto drafted content";
                break;
            case "trash":
                $clean = "DELETE FROM `$wpdb->posts` WHERE post_status = 'trash';";
                $posttrash = $wpdb->query( $clean );

                $message = "All trashed content";
                break;
            case "comments":
                $clean = "DELETE FROM `$wpdb->comments` WHERE comment_approved = 'spam' OR comment_approved = 'trash' ;";
                $comments = $wpdb->query( $clean );

                $message = "Comments from trash & spam";
                break;
            case "trackbacks":
                $clean = "DELETE FROM `$wpdb->comments` WHERE comment_type = 'trackback' OR comment_type = 'pingback' ;";
                $comments = $wpdb->query( $clean );

                $message = "Trackbacks and pingbacks";
                break;
            case "transient":
                $clean = "DELETE FROM `$wpdb->options` WHERE option_name LIKE '%\_transient\_%' ;";
                $comments = $wpdb->query( $clean );

                $message = "Transient options";
                break;
        }

        return true;
    }

    /*
     * Database clean tab
     * funtion to get number of element to clean in database
     */
    public static function getElementToClean($type){
        global $wpdb;
        $return = 0;
        switch ($type){
            case "revisions":
                $element = "SELECT ID FROM `$wpdb->posts` WHERE post_type = 'revision';";
                $return = $wpdb->query( $element );
                break;
            case "drafted":
                $element = "SELECT ID FROM `$wpdb->posts` WHERE post_status = 'auto-draft';";
                $return = $wpdb->query( $element );
                break;
            case "trash":
                $element = "SELECT ID FROM `$wpdb->posts` WHERE post_status = 'trash';";
                $return = $wpdb->query( $element );
                break;
            case "comments":
                $element = "SELECT comment_ID FROM `$wpdb->comments` WHERE comment_approved = 'spam' OR comment_approved = 'trash' ;";
                $return = $wpdb->query( $element );
                break;
            case "trackbacks":
                $element = "SELECT comment_ID FROM `$wpdb->comments` WHERE comment_type = 'trackback' OR comment_type = 'pingback' ;";
                $return = $wpdb->query( $element );
                break;
            case "transient":
                $element = "SELECT option_id FROM `$wpdb->options` WHERE option_name LIKE '%\_transient\_%' ;";
                $return = $wpdb->query( $element );
                break;
        }
        return $return;
    }

    // Convert string to array
    protected function string_convert_arr($input){
        $output = array();
        if(!empty($input)){
            $input = rawurldecode($input);
            $input = trim($input);
            $input = str_replace(' ', '', $input);
            $input = explode("\n",$input);

            foreach ($input as $k => $v){
                $output[] = trim($v);
            }
        }
        return $output;
    }
    //ajax clean cache
    public static function breeze_clean_cache() {
        $size_cache = 0;
        $size_css_cache = 0;
        $size_js_cache = 0;
        $result = 0;
        // analysis size cache
        $cachepath = rtrim(WP_CONTENT_DIR, '/') . '/cache/breeze';

        if (is_dir($cachepath))
            $cachedirs = scandir($cachepath);
        if (!empty($cachedirs)) {
            foreach ($cachedirs as $cachedir) {
                if ($cachedir != '.' && $cachedir != '..') {
                    $filepath = $cachepath . '/' . $cachedir;
                    if(is_dir($filepath))
                        $filedirs = scandir($filepath);
                    foreach($filedirs as $filedir){
                        if ($filedir != '.' && $filedir != '..') {
                            if (@file_exists($filepath)) {
                                $dir_path = $filepath.'/'.$filedir;
                                $size_cache += filesize($dir_path);
                            }
                        }
                    }

                }
            }
        }

        // analysis size css cache
        if(is_multisite()){
            $blog_id = get_current_blog_id();
            $css_path = rtrim(WP_CONTENT_DIR, '/') . '/cache/breeze-minification/'.$blog_id.'/css';
        }else{
            $css_path = rtrim(WP_CONTENT_DIR, '/') . '/cache/breeze-minification/css';
        }
        if (is_dir($css_path))
            $file_in_css = scandir($css_path);
        if (!empty($file_in_css)) {
            foreach ($file_in_css as $v) {
                if ($v != '.' && $v != '..' && $v != 'index.html') {
                    $path = $css_path . '/' . $v;
                    $size_css_cache += filesize($path);
                }
            }
        }

        // analysis size js cache
        if(is_multisite()){
            $blog_id = get_current_blog_id();
            $js_path = rtrim(WP_CONTENT_DIR, '/') . '/cache/breeze-minification/'.$blog_id.'/js';
        }else{
            $js_path = rtrim(WP_CONTENT_DIR, '/') . '/cache/breeze-minification/js';
        }
        if (is_dir($js_path))
            ;
        $file_in_js = scandir($js_path);
        if (!empty($file_in_js)) {
            foreach ($file_in_js as $v) {
                if ($v != '.' && $v != '..' && $v != 'index.html') {
                    $path = $js_path . '/' . $v;
                    $size_js_cache += filesize($path);
                }
            }
        }

        $total_size_cache = $size_cache + $size_css_cache + $size_js_cache;

        $result = self::formatBytes($total_size_cache);

        //delete minify file
        Breeze_MinificationCache::clear_minification();
       //delete all cache
        Breeze_PurgeCache::breeze_cache_flush();

        return $result;
    }

    /*
     *Ajax clean cache
     *
     */
    public static function breeze_ajax_clean_cache(){
        //check security nonce
        check_ajax_referer( '_breeze_purge_cache', 'security' );
        $result = self::breeze_clean_cache();

        echo json_encode($result);
        exit;
    }
    /*
     * Ajax purge varnish
     */
    public static function purge_varnish_action(){
        //check security
        check_ajax_referer( '_breeze_purge_varnish', 'security' );

        $homepage = home_url().'/?breeze';
        $main = new Breeze_PurgeVarnish();
        $main->purge_cache($homepage);

        echo json_encode(array('clear' => true));
        exit;
    }
    /*
     * Ajax purge database
     */
    public static function breeze_ajax_purge_database(){
        //check security
        check_ajax_referer( '_breeze_purge_database', 'security' );

        $type = array('revisions','drafted','trash','comments','trackbacks','transient');
        foreach ($type as $item){
            self::cleanSystem($item);
        }

        echo json_encode(array('clear' => true));
        exit;
    }
    public static function formatBytes($bytes, $precision = 2) {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2);
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2);
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2);
        } elseif ($bytes > 1) {
            $bytes = $bytes;
        } elseif ($bytes == 1) {
            $bytes = $bytes;
        } else {
            $bytes = '0';
        }

        return $bytes;
    }

    /*
     * Clear varnish after settings
     */

    public function clear_varnish(){
        // Clear varnish cache after settings
        $varnish = get_option('breeze_varnish_cache');
        if(!empty($varnish['auto-purge-varnish'])){
            $homepage = home_url().'/?breeze';
            $main = new Breeze_PurgeVarnish();
            $main->purge_cache($homepage);
        }

        return true;
    }

}

//init configuration object
new Breeze_Configuration();
