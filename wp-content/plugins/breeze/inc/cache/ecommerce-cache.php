<?php
defined( 'ABSPATH' ) or die('Not allow!');

/*
 * Class for E-commerce Cache
 */
class Breeze_Ecommerce_Cache {
	public function __construct() {
		add_action( 'activated_plugin', array($this,'detect_ecommerce_activation') );
		add_action( 'deactivated_plugin', array($this,'detect_ecommerce_deactivation') );
		add_action( 'wp_loaded', array($this,'update_ecommerce_activation') );
	}

	// After woocommerce active,merge array disable page config
	public function detect_ecommerce_activation($plugin){
		if( 'woocommerce/woocommerce.php' == $plugin){
			update_option('breeze_ecommerce_detect',1);
		}
	}

	// Delete option detect when deactivate woo
	public function detect_ecommerce_deactivation($plugin){
		if( 'woocommerce/woocommerce.php' == $plugin){
			delete_option('breeze_ecommerce_detect');
		}
	}

	// Update option when Woocimmerce active
	public function update_ecommerce_activation() {
		$check = get_option('breeze_ecommerce_detect');
		if( stripos($_SERVER['REQUEST_URI'],'wc-setup&step=locale') !== false){
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
			}
			Breeze_ConfigCache::write_config_cache();
		}
		if (!empty($check) && $check == 1) {
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
			}
			Breeze_ConfigCache::write_config_cache();
			update_option('breeze_ecommerce_detect', 0);
		}
	}

	// Exclude pages of Ecommerce from cache
	public function ecommerce_exclude_pages() {
		$urls = array();

		// WooCommerce
		if ( function_exists( 'WC' ) && function_exists( 'wc_get_page_id' ) ) {
			if( wc_get_page_id( 'checkout' ) && wc_get_page_id( 'checkout' ) != '-1' ) {
				$checkout_urls = $this->breeze_translated_post_urls( wc_get_page_id( 'checkout' ), 'page', '(.*)' );
				$urls = array_merge( $urls, $checkout_urls );
			}
			if ( wc_get_page_id( 'cart' ) && wc_get_page_id( 'cart' ) != '-1' ) {
				$cart_urls = $this->breeze_translated_post_urls( wc_get_page_id( 'cart' ) );
				$urls = array_merge( $urls, $cart_urls );
			}

			if ( wc_get_page_id( 'myaccount' ) && wc_get_page_id( 'myaccount' ) != '-1' ) {
				$cart_urls = $this->breeze_translated_post_urls( wc_get_page_id( 'myaccount' ), 'page', '(.*)' );
				$urls = array_merge( $urls, $cart_urls );
			}
		}
		return $urls;
	}

	/**
	 * Check whether the plugin is active by checking the active_plugins list.
	 */
	public static function breeze_is_plugin_active( $plugin ) {
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || self::breeze_is_plugin_active_for_network( $plugin );
	}

	/**
	 * Check whether the plugin is active for the entire network.
	 */
	public static function breeze_is_plugin_active_for_network( $plugin ) {
		if ( !is_multisite() ) {
			return false;
		}

		$plugins = get_site_option( 'active_sitewide_plugins');
		if ( isset($plugins[$plugin]) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get all translated path of a specific post with ID.
	 *
	 * @param 	int 	$post_id	Post ID
	 * @param 	string 	$post_type 	Post Type
	 * @param 	string 	$regex 		Regex to include at the end
	 * @return 	array	$urls
	 */
	public function breeze_translated_post_urls( $post_id, $post_type = 'page', $regex = null ) {
		$urls  = array();
		$permark_link = get_option('permalink_structure');
		if(empty($permark_link)){
			if(!empty($regex)){
				$urls[]= get_permalink( $post_id ).'&'. $regex;
			}else{
				$urls[]= get_permalink( $post_id );
			}
			return $urls;
		}
		$path  = parse_url( get_permalink( $post_id ), PHP_URL_PATH );
		$langs = $this->get_all_languages();
		if ( empty( $path ) ) {
			return $urls;
		}

		// WPML
		if ( self::breeze_is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			foreach( $langs as $lang ) {
				$urls[] = parse_url( get_permalink( icl_object_id( $post_id, $post_type, true, $lang ) ), PHP_URL_PATH ) . $regex;
			}
		}

		// qTranslate & qTranslate-x
		if ( self::breeze_is_plugin_active( 'qtranslate/qtranslate.php' ) || self::breeze_is_plugin_active( 'qtranslate-x/qtranslate.php' ) ) {
			$langs  = $GLOBALS['q_config']['enabled_languages'];
			$langs  = array_diff( $langs, array( $GLOBALS['q_config']['default_language'] ) );
			$url    = get_permalink( $post_id );
			$urls[] = parse_url( get_permalink( $post_id ), PHP_URL_PATH ) . $regex;

			foreach( $langs as $lang ) {
				if ( self::breeze_is_plugin_active( 'qtranslate/qtranslate.php' ) ) {
					$urls[] = parse_url( qtrans_convertURL( $url, $lang, true ), PHP_URL_PATH ) . $regex;
				} else if ( self::breeze_is_plugin_active( 'qtranslate/qtranslate.php' ) ) {
					$urls[] = parse_url( qtranxf_convertURL( $url, $lang, true ), PHP_URL_PATH ) . $regex;
				}
			}
		}

		// Polylang
		if ( self::breeze_is_plugin_active( 'polylang/polylang.php' ) || self::breeze_is_plugin_active( 'polylang-pro/polylang.php' ) ) {
			if ( function_exists( 'PLL' ) && is_object( PLL()->model ) ) {
				$translations = pll_get_post_translations( $post_id );
			} else if ( is_object( $GLOBALS['polylang']->model ) ) {
				$translations = $GLOBALS['polylang']->model->get_translations( 'page', $post_id );
			}

			if ( ! empty( $translations ) ) {
				foreach ( $translations as $post_id ) {
					$urls[] = parse_url( get_permalink( $post_id ), PHP_URL_PATH ) . $regex;
				}
			}
		}

		if ( trim( $path, '/' ) != '' ) {
			$urls[] = $path . $regex;
		}
		$urls = array_unique( $urls );

		return $urls;
	}

	/**
	 * Check if a translation plugin is activated
	 *
	 * @return bool True if a plugin is activated
	 */
	public static function check_trans_plugin() {
		if ( self::breeze_is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' )  // WPML
		     || self::breeze_is_plugin_active( 'qtranslate/qtranslate.php' )               // qTranslate
		     || self::breeze_is_plugin_active( 'qtranslate-x/qtranslate.php' )			    // qTranslate-x
		     || self::breeze_is_plugin_active( 'polylang/polylang.php' )                   // Polylang
		     || self::breeze_is_plugin_active( 'polylang-pro/polylang.php' ) ) { 			// Polylang Pro
			return true;
		}

		return false;
	}
	/**
	 * Get info of all active languages
	 *
	 * @return array List of language code
	 */
	public static function get_all_languages() {
		if( ! self::check_trans_plugin() ) {
			return false;
		}

		if ( self::breeze_is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
			return array_keys( $GLOBALS['sitepress']->get_active_languages() );
		}

		if ( self::breeze_is_plugin_active( 'qtranslate/qtranslate.php' ) || self::breeze_is_plugin_active( 'qtranslate-x/qtranslate.php' ) ) {
			return $GLOBALS['q_config']['enabled_languages'];
		}

		if ( self::breeze_is_plugin_active( 'polylang/polylang.php' ) || self::breeze_is_plugin_active( 'polylang-pro/polylang.php' ) ) {
			return pll_languages_list();
		}
	}

	public static function factory() {
		static $instance;

		if ( ! $instance ) {
			$instance = new self();
		}
		return $instance;
	}
}