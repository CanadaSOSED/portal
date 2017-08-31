<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/* 
 *  Based on some work of autoptimize plugin 
 */
class Breeze_MinificationCache {
    private $filename;
	private $mime;
	private $cachedir;
	private $delayed;
	
	public function __construct($md5,$ext='php') {
		$this->cachedir = BREEZE_MINIFICATION_CACHE;
        if(is_multisite()){
            $blog_id = get_current_blog_id();
            $this->cachedir = BREEZE_MINIFICATION_CACHE.$blog_id . '/';
        }
		$this->delayed = BREEZE_CACHE_DELAY;
		$this->nogzip = BREEZE_CACHE_NOGZIP;
		if($this->nogzip == false) {
			$this->filename = BREEZE_CACHEFILE_PREFIX.$md5.'.php';
		} else {
			if (in_array($ext, array("js","css")))	 {
				$this->filename = $ext.'/'.BREEZE_CACHEFILE_PREFIX.$md5.'.'.$ext;
			} else {
				$this->filename = '/'.BREEZE_CACHEFILE_PREFIX.$md5.'.'.$ext;
			}
		}


	}
        
      public function check() {
		if(!file_exists($this->cachedir.$this->filename)) {
			// No cached file, sorry
			return false;
		}
		// Cache exists!
		return true;
	}
     public function retrieve() {
		if($this->check()) {
			if($this->nogzip == false) {
				return file_get_contents($this->cachedir.$this->filename.'.none');
			} else {
				return file_get_contents($this->cachedir.$this->filename);
			}
		}
		return false;
	}
     public function cache($code,$mime) {
		if($this->nogzip == false) {
			$file = ($this->delayed ? 'delayed.php' : 'default.php');
			$phpcode = file_get_contents(BREEZE_PLUGIN_DIR.'/inc/minification/config/'.$file);
			$phpcode = str_replace(array('%%CONTENT%%','exit;'),array($mime,''),$phpcode);
			file_put_contents($this->cachedir.$this->filename,$phpcode, LOCK_EX);
			file_put_contents($this->cachedir.$this->filename.'.none',$code, LOCK_EX);
			if(!$this->delayed) {
				// Compress now!
				file_put_contents($this->cachedir.$this->filename.'.deflate',gzencode($code,9,FORCE_DEFLATE), LOCK_EX);
				file_put_contents($this->cachedir.$this->filename.'.gzip',gzencode($code,9,FORCE_GZIP), LOCK_EX);
			}
		} else {
			// Write code to cache without doing anything else
			file_put_contents($this->cachedir.$this->filename,$code, LOCK_EX);
		}
	}
     public function getname() {
	        apply_filters('breeze_filter_cache_getname',breeze_CACHE_URL.$this->filename);
		return $this->filename;
	} 
    //create folder cache
     public static function create_cache_minification_folder(){
            if(!defined('BREEZE_MINIFICATION_CACHE')) {
                          // We didn't set a cache
                          return false;
                  }
             if(is_multisite()){
                 $blog_id = get_current_blog_id();
                 foreach (array("","js","css") as $checkDir) {
                     if(!Breeze_MinificationCache::checkCacheDir(BREEZE_MINIFICATION_CACHE. $blog_id .'/'.$checkDir)) {
                         return false;
                     }
                 }

                 /** write index.html here to avoid prying eyes */
                 $indexFile=BREEZE_MINIFICATION_CACHE . $blog_id .'/index.html';
                 if(!is_file($indexFile)) {
                     @file_put_contents($indexFile,'<html><head><meta name="robots" content="noindex, nofollow"></head><body></body></html>');
                 }

                 /** write .htaccess here to overrule wp_super_cache */
                 $htAccess=BREEZE_MINIFICATION_CACHE. $blog_id .'/.htaccess';
             }else{
                 foreach (array("","js","css") as $checkDir) {
                     if(!Breeze_MinificationCache::checkCacheDir(BREEZE_MINIFICATION_CACHE.$checkDir)) {
                         return false;
                     }
                 }
                 /** write index.html here to avoid prying eyes */
                 $indexFile=BREEZE_MINIFICATION_CACHE.'/index.html';
                 if(!is_file($indexFile)) {
                     @file_put_contents($indexFile,'<html><head><meta name="robots" content="noindex, nofollow"></head><body></body></html>');
                 }

                 /** write .htaccess here to overrule wp_super_cache */
                 $htAccess=BREEZE_MINIFICATION_CACHE.'/.htaccess';
             }

	        if(!is_file($htAccess)) {
			/** 
			 * create wp-content/AO_htaccess_tmpl with 
			 * whatever htaccess rules you might need
			 * if you want to override default AO htaccess
			 */
			$htaccess_tmpl=WP_CONTENT_DIR."/AO_htaccess_tmpl";
			if (is_file($htaccess_tmpl)) { 
				$htAccessContent=file_get_contents($htaccess_tmpl);
			} else if (is_multisite()) {
				$htAccessContent='<IfModule mod_headers.c>
        Header set Vary "Accept-Encoding"
        Header set Cache-Control "max-age=10672000, must-revalidate"
</IfModule>
<IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType text/css A30672000
        ExpiresByType text/javascript A30672000
        ExpiresByType application/javascript A30672000
</IfModule>
<IfModule mod_deflate.c>
        <FilesMatch "\.(js|css)$">
        SetOutputFilter DEFLATE
    </FilesMatch>
</IfModule>
<IfModule mod_authz_core.c>
    <Files *.php>
        Require all granted
    </Files>
</IfModule>
<IfModule !mod_authz_core.c>
    <Files *.php>
        Order allow,deny
        Allow from all
    </Files>
</IfModule>';
			} else {
                	        $htAccessContent='<IfModule mod_headers.c>
        Header set Vary "Accept-Encoding"
        Header set Cache-Control "max-age=10672000, must-revalidate"
</IfModule>
<IfModule mod_expires.c>
        ExpiresActive On
        ExpiresByType text/css A30672000
        ExpiresByType text/javascript A30672000
        ExpiresByType application/javascript A30672000
</IfModule>
<IfModule mod_deflate.c>
    <FilesMatch "\.(js|css)$">
        SetOutputFilter DEFLATE
    </FilesMatch>
</IfModule>
<IfModule mod_authz_core.c>
    <Files *.php>
        Require all denied
    </Files>
</IfModule>
<IfModule !mod_authz_core.c>
    <Files *.php>
        Order deny,allow
        Deny from all
    </Files>
</IfModule>';
			}

			@file_put_contents($htAccess,$htAccessContent);
		}
                 // All OK
        return true;
                  
      }
//      check dir cache
      static function checkCacheDir($dir) {
		// Check and create if not exists
		if(!file_exists($dir))	{
			@mkdir($dir,0775,true);
			if(!file_exists($dir))	{
				return false;
			}
		}

		// check if we can now write
		if(!is_writable($dir))	{
			return false;
		}

		// and write index.html here to avoid prying eyes
		$indexFile=$dir.'/index.html';
		if(!is_file($indexFile)) {
			@file_put_contents($indexFile,'<html><head><meta name="robots" content="noindex, nofollow"></head><body></body></html>');
		}
		
		return true;
	}
      public static function clear_minification() {
		if(!Breeze_MinificationCache::create_cache_minification_folder()) {
			return false;
		}
	    if(is_multisite()){
            $blog_id = get_current_blog_id();
            // scan the cachedirs
            foreach (array("","js","css") as $scandirName) {
                $scan[$scandirName] = scandir(BREEZE_MINIFICATION_CACHE.$blog_id.'/'.$scandirName);
            }
            // clear the cachedirs
            foreach ($scan as $scandirName=>$scanneddir) {
                $thisAoCacheDir=rtrim(BREEZE_MINIFICATION_CACHE.$blog_id.'/'.$scandirName,"/")."/";
                foreach($scanneddir as $file) {
                    if(!in_array($file,array('.','..')) && strpos($file,BREEZE_CACHEFILE_PREFIX) !== false && is_file($thisAoCacheDir.$file)) {
                        @unlink($thisAoCacheDir.$file);
                    }
                }
            }
            @unlink(BREEZE_MINIFICATION_CACHE.$blog_id."/.htaccess");
        }else{
            // scan the cachedirs
            foreach (array("","js","css") as $scandirName) {
                $scan[$scandirName] = scandir(BREEZE_MINIFICATION_CACHE.$scandirName);
            }
            // clear the cachedirs
            foreach ($scan as $scandirName=>$scanneddir) {
                $thisAoCacheDir=rtrim(BREEZE_MINIFICATION_CACHE.$scandirName,"/")."/";
                foreach($scanneddir as $file) {
                    if(!in_array($file,array('.','..')) && strpos($file,BREEZE_CACHEFILE_PREFIX) !== false && is_file($thisAoCacheDir.$file)) {
                        @unlink($thisAoCacheDir.$file);
                    }
                }
            }

            @unlink(BREEZE_MINIFICATION_CACHE."/.htaccess");
        }
		return true;
	}
      
      public static function factory() {

		static $instance;

		if ( ! $instance ) {
			$instance = new self();
			$instance->set_action();
		}

		return $instance;
	}
}

