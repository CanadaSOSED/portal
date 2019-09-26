<?php
/**
 * Plugin Name: Knowledge Base for Documents and FAQs
 * Plugin URI: https://www.echoknowledgebase.com
 * Description: Echo Knowledge Base is super easy to configure, works well with themes and can handle a variety of article hierarchies.
 * Version: 4.6.1
 * Author: Echo Plugins
 * Author URI: https://www.echoknowledgebase.com
 * Text Domain: echo-knowledge-base
 * Domain Path: languages
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Knowledge Base for Documents and FAQs is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Knowledge Base for Documents and FAQs is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Knowledge Base for Documents and FAQs. If not, see <http://www.gnu.org/licenses/>.
 *
*/

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Echo_Knowledge_Base' ) && ! epkb_is_amag_conflict() ) :

/**
 * Main class to load the plugin.
 *
 * Singleton
 */
final class Echo_Knowledge_Base {

	/* @var Echo_Knowledge_Base */
	private static $instance;

	public static $version = '4.6.1';
	public static $plugin_dir;
	public static $plugin_url;
	public static $plugin_file = __FILE__;
	public static $needs_min_add_on_version = array( 'LAY' => '1.2.1', 'MKB' => '1.5.0', 'RTD' => '1.0.0', 'IDG' => '1.0.0', 'BLK' => '1.0.0', 'SEA' => '1.0.0' );

	/* @var EPKB_KB_Config_DB */
	public $kb_config_obj;

	/* @var EPKB_Settings_DB */
	public $settings_obj;

	/**
	 * Initialise the plugin
	 */
	private function __construct() {
		self::$plugin_dir = plugin_dir_path(  __FILE__ );
		self::$plugin_url = plugin_dir_url( __FILE__ );
	}

	/**
	 * Retrieve or create a new instance of this main class (avoid global vars)
	 *
	 * @static
	 * @return Echo_Knowledge_Base
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Echo_Knowledge_Base ) ) {
			self::$instance = new Echo_Knowledge_Base();

			self::$instance->setup_system();
			self::$instance->setup_plugin();

			add_action( 'plugins_loaded', array( self::$instance, 'load_text_domain' ) );
			add_action( 'init', array( self::$instance, 'epkb_stop_heartbeat' ), 1 );
		}
		return self::$instance;
	}

	/**
	 * Setup class auto-loading and other support functions. Setup custom core features.
	 */
	private function setup_system() {

		// autoload classes ONLY when needed by executed code rather than on every page request
		require_once self::$plugin_dir . 'includes/system/class-epkb-autoloader.php';

		// load non-classes
		require_once self::$plugin_dir . 'includes/system/plugin-setup.php';
		require_once self::$plugin_dir . 'includes/system/scripts-registration.php';
		require_once self::$plugin_dir . 'includes/system/plugin-links.php';

		// register settings
		self::$instance->settings_obj = new EPKB_Settings_DB();
		self::$instance->kb_config_obj = new EPKB_KB_Config_DB();

		new EPKB_Upgrades();

		// setup custom core features
		new EPKB_Articles_CPT_Setup();
		new EPKB_Articles_Admin();
	}

	/**
	 * Setup plugin before it runs. Include functions and instantiate classes based on user action
	 */
	private function setup_plugin() {

		$action = EPKB_Utilities::get('action', '', false);

		// process action request if any
		if ( ! empty($action) ) {
			$this->handle_action_request( $action );
		}

		// handle AJAX front & back-end requests (no admin, no admin bar)
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$this->handle_ajax_requests( $action );
			return;
		}

		// ADMIN or CLI
		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {	// || ( defined( 'REST_REQUEST' ) && REST_REQUEST )
			$this->setup_backend_classes();
			return;
		}

		// catch saving of Post in Gutenberg
		if ( ! empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/wp-admin/post.php') !== false ) {
			require_once self::$plugin_dir . 'includes/admin/admin-functions.php';
		}

		// FRONT-END with admin-bar
		add_action( 'wp_enqueue_scripts', array($this, 'handle_admin_bar_front') );

		// FRONT-END (no ajax, possibly admin bar)
		new EPKB_Layouts_Setup();      // KB Main page shortcode, list of themes
		new EPKB_Articles_Setup();
		new EPKB_Templates();
	}

	/**
	 * Handle plugin actions here such as saving settings
	 * @param $action
	 */
	private function handle_action_request( $action ) {

		if ( empty($action) || ! EPKB_KB_Handler::is_kb_request() ) {
			return;
		}

		if ( $action == 'add-tag' ) {  // adding category term
			new EPKB_Categories_Admin();
			return;
		}

		if ( $action == 'epkb_download_debug_info' ) {
			new EPKB_Settings_Controller();
			return;
		}
	}

	/**
	 * Handle AJAX requests coming from front-end and back-end
	 * @param $action
	 */
	private function handle_ajax_requests( $action ) {

        if ( empty($action) ) {
            return;
        }

		if ( $action == 'epkb-search-kb' ) {  // user searching KB
			new EPKB_KB_Search();
			return;
		} else if ( in_array($action,
			array( 'epkb_change_main_page_config_ajax', 'epkb_change_article_page_config_ajax',
				'epkb_change_one_config_param_ajax', 'epkb_save_kb_config_changes', 'epkb_change_article_category_sequence',
				'epkb_close_upgrade_message') ) ) {
			new EPKB_KB_Config_Controller();
			return;
		} else if ( in_array($action,
				array( 'epkb_send_feedback', 'epkb_toggle_debug', 'epkb_save_wpml_settings' ) ) ) {
			new EPKB_Settings_Controller();
			return;
		}

		$epkb_taxonomy = empty($action) ? '' : preg_replace('/[^A-Za-z0-9 \-_]/', '', $action);
		$epkb_taxonomy = empty($epkb_taxonomy) ? '' :  str_replace('add-', '', $epkb_taxonomy);

		if ( $action == 'delete-tag' || $action == 'inline-save-tax' || EPKB_KB_Handler::is_kb_taxonomy( $epkb_taxonomy ) ) {
			new EPKB_Categories_Admin();
			return;
		}
	}

	/**
	 * Setup up classes when on ADMIN pages
	 */
	private function setup_backend_classes() {
		global $pagenow;

		$is_kb_request = EPKB_KB_Handler::is_kb_request();

		// include our admin scripts on our admin pages (submenus of KB menu)
		if ( $is_kb_request || $pagenow == 'post.php' ) {
			add_action( 'admin_enqueue_scripts', 'epkb_load_admin_plugin_pages_resources' );
			new EPKB_Categories_Admin( $pagenow );
			new EPKB_Admin_Notices();
		}

		// KB Config page needs front-page CSS resources
		if ( $is_kb_request && isset($_REQUEST['page']) && $_REQUEST['page'] == 'epkb-kb-configuration' ) {
			add_action( 'admin_enqueue_scripts', 'epkb_load_admin_kb_config_script' );
			add_action( 'admin_enqueue_scripts', 'epkb_kb_config_load_public_css' );
		}

		// admin core classes
		require_once self::$plugin_dir . 'includes/admin/admin-menu.php';
		require_once self::$plugin_dir . 'includes/admin/admin-functions.php';

		// admin other classes
		$classes = array(
			// class name=0, loading=1 (backend/admin-bar), pages=2, admin_action=3, KB post type required=4
		);

		foreach( $classes as $class_info ) {

			if ( $class_info[4] && ! $is_kb_request ) {
				continue;
			}

			// INDIVIDUAL PAGES: if feature available on a specific page then ensure the page is being loaded
			if ( ( ( ! empty($class_info[2]) && ! in_array($pagenow, $class_info[2]) )  ||
			       ( ! empty($_REQUEST['page']) && ! in_array($_REQUEST['page'], $class_info[2]) )
				 ) &&
			     ( empty($class_info[3]) || empty($_REQUEST['action']) || ! in_array($_REQUEST['action'], $class_info[3]) ) ) {
				continue;
			}

			$new_class = $class_info[0];
			if ( class_exists($new_class) ) {
				new $new_class();
			}
		}
	}

	/**
	 * Invoked on the FRONT-END and checks if admin bar is showing
	 */
	public function handle_admin_bar_front() {
		/* if ( function_exists( 'is_admin_bar_showing' ) && function_exists( 'is_user_logged_in' ) && is_admin_bar_showing() ) {
			//epkb_load_admin_bar_resources();
		} */
	}

	/**
	 * Loads the plugin language files
	 *
	 * Note: the first-loaded translation file overrides any following files if they both have the same translation
	 */
	public function load_text_domain() {
		global $wp_version;

		// Set filter for plugin's languages directory
		$plugin_lang_dir = 'echo-knowledge-base/languages/';
		$plugin_lang_dir = apply_filters( 'epkb_wp_languages_directory', $plugin_lang_dir );

		// Traditional WordPress plugin locale filter
		$user_locale = $wp_version >= 4.7 && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
		$locale = apply_filters( 'plugin_locale', $user_locale, 'echo-knowledge-base' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'echo-knowledge-base', $locale );

		// Setup paths to current locale file
		$mofile_local  = $plugin_lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;

		// does WP provide language pack?  (only 100% translated packs will be downloaded ??)
		if ( file_exists( $mofile_global ) ) {
			// in global /wp-content/languages/<plugin-name>/ folder
			load_textdomain( 'echo-knowledge-base', $mofile_global );
		}

		// complement with our own language packs
		if ( file_exists( WP_PLUGIN_DIR . '/' . $mofile_local ) ) {
			// in /wp-content/plugins/<plugin-name>/languages/ folder
			load_plugin_textdomain( 'echo-knowledge-base', false, $plugin_lang_dir );
		}
	}

	// Don't allow this singleton to be cloned.
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, 'Invalid (#1)', '4.0' );
	}

	// Don't allow un-serializing of the class except when testing
	public function __wakeup() {
		if ( strpos($GLOBALS['argv'][0], 'phpunit') === false ) {
			_doing_it_wrong( __FUNCTION__, 'Invalid (#1)', '4.0' );
		}
	}

	/**
	 * When developing and debugging we don't need heartbeat
	 */
	public function epkb_stop_heartbeat() {
		if ( defined( 'RUNTIME_ENVIRONMENT' ) && RUNTIME_ENVIRONMENT == 'DEV' ) {
			wp_deregister_script( 'heartbeat' );
			// EPKB_Utilities::save_wp_option( EPKB_Settings_Controller::EPKB_DEBUG, true, true );
		}
	}
}

/**
 * Returns the single instance of this class
 *
 * @return Echo_Knowledge_Base - this class instance
 */
function epkb_get_instance() {
	return Echo_Knowledge_Base::instance();
}
epkb_get_instance();

endif; // end class_exists() check

function epkb_is_amag_conflict() {
	/** @var $wpdb Wpdb */
	global $wpdb;
	$table = $wpdb->prefix . 'am'.'gr_kb_groups';
	$result = $wpdb->get_var( "SHOW TABLES LIKE '" . $table ."'" );
	return ( ! empty($result) && ( $table == $result ) );
}
