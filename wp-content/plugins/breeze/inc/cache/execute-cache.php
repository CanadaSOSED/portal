<?php
/* 
 *  Based on some work of https://github.com/tlovett1/simple-cache/blob/master/inc/dropins/file-based-page-cache.php
 */
defined('ABSPATH') || exit;
// Include and instantiate the class.

require_once 'Mobile-Detect-2.8.25/Mobile_Detect.php';
$detect = new \Cloudways\Breeze\Mobile_Detect\Mobile_Detect;

// Don't cache robots.txt or htacesss
if (strpos($_SERVER['REQUEST_URI'], 'robots.txt') !== false || strpos($_SERVER['REQUEST_URI'], '.htaccess') !== false) {
    return;
}

// Don't cache non-GET requests
if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'GET') {
    return;
}

$file_extension = $_SERVER['REQUEST_URI'];
$file_extension = preg_replace('#^(.*?)\?.*$#', '$1', $file_extension);
$file_extension = trim(preg_replace('#^.*\.(.*)$#', '$1', $file_extension));

// Don't cache disallowed extensions. Prevents wp-cron.php, xmlrpc.php, etc.
if (!preg_match('#index\.php$#i', $_SERVER['REQUEST_URI']) && in_array($file_extension, array('php', 'xml', 'xsl'))) {
    return;
}

$url_path = breeze_get_url_path();
$user_logged = false;
$filename = $url_path . 'guest';
// Don't cache 
if (!empty($_COOKIE)) {
    $wp_cookies = array('wordpressuser_', 'wordpresspass_', 'wordpress_sec_', 'wordpress_logged_in_');

    foreach ($_COOKIE as $key => $value) {
        // Logged in!
        if (strpos($key, 'wordpress_logged_in_') !== false) {
            $user_logged = true;
        }

    }

    if ($user_logged) {
        foreach ($_COOKIE as $k => $v) {
            if (strpos($k, 'wordpress_logged_in_') !== false) {
                $nameuser = substr($v, 0, strpos($v, '|'));
                $filename = $url_path . strtolower($nameuser);
            }
        }
    }
    if (!empty($_COOKIE['breeze_commented_posts'])) {
        foreach ($_COOKIE['breeze_commented_posts'] as $path) {
            if (rtrim($path, '/') === rtrim($_SERVER['REQUEST_URI'], '/')) {
                // User commented on this post
                return;
            }
        }
    }
}

//check disable cache for page
$domain = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];
//decode url with russian language
$current_url = $domain . rawurldecode($_SERVER['REQUEST_URI']);
$opts_config = $GLOBALS['breeze_config'];
$check_exclude = check_exclude_page($opts_config, $current_url);

//load cache
if (!$check_exclude) {
    $devices = $opts_config['cache_options'];
    $X1 = '';
    // Detect devices
    if ($detect->isMobile() && !$detect->isTablet()) {
        //        The first X will be D for Desktop cache
        //                                  M for Mobile cache
        //                                  T for Tablet cache
        if ((int)$devices['breeze-mobile-cache'] == 1) {
            $X1 = 'D';
            $filename .= '_breeze_cache_desktop';
        }
        if ((int)$devices['breeze-mobile-cache'] == 2) {
            $X1 = 'M';
            $filename .= '_breeze_cache_mobile';
        }

    } else {
        if ((int)$devices['breeze-desktop-cache'] == 1) {
            $X1 = 'D';
            $filename .= '_breeze_cache_desktop';
        }
    }

    breeze_serve_cache($filename, $url_path, $X1,$devices);
    ob_start('breeze_cache');

} else {
	header('Cache-Control: no-cache');
}

/**
 * Cache output before it goes to the browser
 *
 * @param  string $buffer
 * @param  int $flags
 * @since  1.0
 * @return string
 */
function breeze_cache($buffer, $flags)
{

    require_once 'Mobile-Detect-2.8.25/Mobile_Detect.php';
    $detect = new \Cloudways\Breeze\Mobile_Detect\Mobile_Detect;
    //not cache per administrator if option disable optimization for admin users clicked
    if (!empty($GLOBALS['breeze_config']) && (int)$GLOBALS['breeze_config']['disable_per_adminuser']) {
        $current_user = wp_get_current_user();
        if (in_array('administrator', $current_user->roles)) {
            return $buffer;
        }
    }

    if (strlen($buffer) < 255) {
        return $buffer;
    }

    // Don't cache search, 404, or password protected
    if (is_404() || is_search() || post_password_required()) {
        return $buffer;
    }
    global $wp_filesystem;
    if (empty($wp_filesystem)) {
        require_once(ABSPATH . '/wp-admin/includes/file.php');
        WP_Filesystem();
    }
    $url_path = breeze_get_url_path();

    // Make sure we can read/write files and that proper folders exist
    if (!$wp_filesystem->exists(untrailingslashit(WP_CONTENT_DIR) . '/cache')) {
        if (!$wp_filesystem->mkdir(untrailingslashit(WP_CONTENT_DIR) . '/cache')) {
            // Can not cache!
            return $buffer;
        }
    }

    if (!$wp_filesystem->exists(untrailingslashit(WP_CONTENT_DIR) . '/cache/breeze')) {
        if (!$wp_filesystem->mkdir(untrailingslashit(WP_CONTENT_DIR) . '/cache/breeze')) {
            // Can not cache!
            return $buffer;
        }
    }

    if (!$wp_filesystem->exists(untrailingslashit(WP_CONTENT_DIR) . '/cache/breeze/' . md5($url_path))) {
        if (!$wp_filesystem->mkdir(untrailingslashit(WP_CONTENT_DIR) . '/cache/breeze/' . md5($url_path))) {
            // Can not cache!
            return $buffer;
        }
    }

    $path = untrailingslashit(WP_CONTENT_DIR) . '/cache/breeze/' . md5($url_path) . '/';

    $modified_time = time(); // Make sure modified time is consistent

    if (preg_match('#</html>#i', $buffer)) {
        $buffer .= "\n<!-- Cache served by breeze CACHE - Last modified: " . gmdate('D, d M Y H:i:s', $modified_time) . " GMT -->\n";
    }
    $headers = array(
        array(
            'name' => 'Content-Length',
            'value' => strlen($buffer)
        ),
        array(
            'name' => 'Content-Type',
            'value' => 'text/html; charset=utf-8'
        ),
        array(
            'name' => 'Last-Modified',
            'value' => gmdate('D, d M Y H:i:s', $modified_time) . ' GMT'
        )
    );

    if(!isset($_SERVER['HTTP_X_VARNISH'])) {
        $headers = array_merge(array(
            array(
                'name' => 'Expires',
                'value' => 'Wed, 17 Aug 2005 00:00:00 GMT'
            ),
            array(
                'name' => 'Cache-Control',
                'value' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0'
            ),
            array(
                'name' => 'Pragma',
                'value' => 'no-cache'
            )
        ));
    }

    $data = serialize(array('body' => $buffer, 'headers' => $headers));
    //cache per users
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        if ($current_user->user_login) {
            $url_path .= $current_user->user_login;
        }
    } else {
        $url_path .= 'guest';
    }
    $devices = $GLOBALS['breeze_config']['cache_options'];
    // Detect devices
    if ($detect->isMobile() && !$detect->isTablet()) {
        if ($devices['breeze-mobile-cache'] == 1) {
            $X1 = 'D';
            $url_path .= '_breeze_cache_desktop';
        }
        if ($devices['breeze-mobile-cache'] == 2) {
            $X1 = 'M';
            $url_path .= '_breeze_cache_mobile';
        }
    } else {
        if ($devices['breeze-desktop-cache'] == 1) {
            $X1 = 'D';
            $url_path .= '_breeze_cache_desktop';
        }
    }

    if (strpos($url_path, '_breeze_cache_') !== false) {
        if (!empty($GLOBALS['breeze_config']['cache_options']['breeze-gzip-compression']) && function_exists('gzencode')) {
            $wp_filesystem->put_contents($path . md5($url_path . '/index.gzip.html') . '.php', $data);
            $wp_filesystem->touch($path . md5($url_path . '/index.gzip.html') . '.php', $modified_time);
        } else {
            $wp_filesystem->put_contents($path . md5($url_path . '/index.html') . '.php', $data);
            $wp_filesystem->touch($path . md5($url_path . '/index.html') . '.php', $modified_time);
        }
    } else {
        return $buffer;
    }
    //set cache provider header if not exists cache file
    header('Cache-Provider:CLOUDWAYS-CACHE-' . $X1 . 'C');

    // Do not send this header in case we are behind a varnish proxy
    if(!isset($_SERVER['HTTP_X_VARNISH'])) {
        header('Cache-Control: no-cache'); // Check back every time to see if re-download is necessary
    }

    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $modified_time) . ' GMT');

    if (function_exists('ob_gzhandler') && !empty($GLOBALS['breeze_config']['cache_options']['breeze-gzip-compression'])) {
        $ini_output_compression = ini_get('zlib.output_compression');
        $array_values = array('1', 'On', 'on');
        if (in_array($ini_output_compression, $array_values)) {
            return $buffer;
        } else {
            return ob_gzhandler($buffer, $flags);
        }
    } else {
        return $buffer;
    }
}

/**
 * Get URL path for caching
 *
 * @since  1.0
 * @return string
 */
function breeze_get_url_path()
{

    $host = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';

    return "http://" . rtrim($host, '/') . $_SERVER['REQUEST_URI'];
}

/**
 * Optionally serve cache and exit
 *
 * @since 1.0
 */
function breeze_serve_cache($filename, $url_path, $X1,$opts)
{
    if (strpos($filename, '_breeze_cache_') === false) {
        return;
    }

    if (function_exists('gzencode') && !empty($GLOBALS['breeze_config']['cache_options']['breeze-gzip-compression'])) {
        $file_name = md5($filename . '/index.gzip.html') . '.php';
    } else {
        $file_name = md5($filename . '/index.html') . '.php';
    }


    $path = rtrim(WP_CONTENT_DIR, '/') . '/cache/breeze/' . md5($url_path) . '/' . $file_name;

    $modified_time = (int)@filemtime($path);

    if (!empty($opts['breeze-browser-cache']) &&!empty($modified_time) && !empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) === $modified_time) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 304 Not Modified', true, 304);
        exit;
    }

    if (@file_exists($path)) {

        $cacheFile = file_get_contents($path);


        if ($cacheFile != false) {
            $datas = unserialize($cacheFile);
            foreach ($datas['headers'] as $data) {
                header($data['name'] . ': ' . $data['value']);
            }
            //set cache provider header
            header('Cache-Provider:CLOUDWAYS-CACHE-' . $X1 . 'E');

            $client_support_gzip = true;

            //check gzip request from client
            if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') === false || strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') === false)) {
                $client_support_gzip = false;
            }

            if ($client_support_gzip && function_exists('gzdecode') && !empty($GLOBALS['breeze_config']['cache_options']['breeze-gzip-compression'])) {
                //if file is zip

                $content = gzencode($datas['body'], 9);
                header('Content-Encoding: gzip');
                header("cache-control: must-revalidate");
                header('Content-Length: ' . strlen($content));
                header('Vary: Accept-Encoding');
                echo $content;
            } else {
                //render page cache
                echo $datas['body'];
            }
            exit;
        }
    }
}

function check_exclude_page($opts_config,$current_url){
	//check disable cache for page
	if (!empty($opts_config['exclude_url'])) {
		foreach ($opts_config['exclude_url'] as $v) {
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
				if($v[0] == '/'){
					// A path of exclude
					if((@preg_match( '@'.$v.'@', $current_url, $matches ) > 0)){
						return true;
					}
				} else { // Whole path
					if($v == $current_url){
						return true;
					}
				}
			}
		}
	}

	return false;
}