<?php
/**
 * A carousel plugin for WordPress.
 *
 * @link              https://shapedplugin.com/
 * @since             2.0.0
 * @package           WP_Carousel_Free
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Carousel
 * Plugin URI:        https://shapedplugin.com/plugin/wordpress-carousel-pro/
 * Description:       The Most Powerful and User-friendly WordPress Carousel Plugin. Create beautiful carousels in minutes using Images, Posts, WooCommerce Products etc.
 * Version:           2.1.9
 * Author:            ShapedPlugin
 * Author URI:        https://shapedplugin.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-carousel-free
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-carousel-free-activator.php
 */
function activate_wp_carousel_free() {
	require_once WPCAROUSELF_PATH . '/includes/class-wp-carousel-free-activator.php';
	WP_Carousel_Free_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_wp_carousel_free' );


/**
 * Main class of the plugin
 *
 * @package WP_Carousel_Free
 * @author Shamim Mia <shamhagh@gmail.com>
 */
class SP_WP_Carousel_Free {


	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      WP_Carousel_Free_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	public $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Plugin textdomain.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	public $domain = 'wp-carousel-free';

	/**
	 * Minimum PHP version required
	 *
	 * @since 2.0.0
	 * @var   string
	 */
	private $min_php = '5.4.0';

	/**
	 * Plugin file.
	 *
	 * @var string
	 */
	private $file = __FILE__;

	/**
	 * Holds class object
	 *
	 * @var   object
	 * @since 2.0.0
	 */
	private static $instance;

	/**
	 * Initialize the SP_WP_Carousel_Free() class
	 *
	 * @since  2.0.0
	 * @return object
	 */
	public static function init() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof SP_WP_Carousel_Free ) ) {
			self::$instance = new SP_WP_Carousel_Free();
			self::$instance->setup();
		}
		return self::$instance;
	}

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since 2.0.0
	 */
	public function setup() {
		$this->plugin_name = 'wp-carousel-free';
		$this->version     = '2.1.9';
		$this->define_constants();
		$this->includes();
		$this->load_dependencies();
		$this->set_locale();
		$this->define_common_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Define plugin constants.
	 *
	 * @since  2.0.0
	 * @return void
	 */
	private function define_constants() {
		$this->define( 'WPCAROUSELF_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'WPCAROUSELF_VERSION', $this->version );
		$this->define( 'WPCAROUSELF_PATH', plugin_dir_path( __FILE__ ) );
		$this->define( 'WPCAROUSELF_INCLUDES', WPCAROUSELF_PATH . '/includes' );
		$this->define( 'WPCAROUSELF_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Define constant if not already set.
	 *
	 * @param  string      $name Constant name.
	 * @param  string|bool $value Constant Value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Included required files.
	 *
	 * @since  2.0.0
	 * @return void
	 */
	public function includes() {

		require_once WPCAROUSELF_INCLUDES . '/class-wp-carosuel-free-updates.php';
		require_once WPCAROUSELF_INCLUDES . '/class-wp-carousel-free-loader.php';
		require_once WPCAROUSELF_INCLUDES . '/class-wp-carousel-free-post-types.php';
		require_once WPCAROUSELF_PATH . '/admin/views/wpcfree-metabox/classes/setup.class.php';
		require_once WPCAROUSELF_PATH . '/admin/views/notices/review.php';
		require_once WPCAROUSELF_PATH . '/admin/views/metabox-config.php';
		require_once WPCAROUSELF_PATH . '/admin/views/option-config.php';
		require_once WPCAROUSELF_INCLUDES . '/class-wp-carousel-free-shortcode.php';
		require_once WPCAROUSELF_PATH . '/public/shortcode-deprecated.php';
		require_once WPCAROUSELF_INCLUDES . '/class-wp-carousel-free-i18n.php';
		require_once WPCAROUSELF_PATH . '/public/class-wp-carousel-free-public.php';
		// if ( is_admin() ) {
			require_once WPCAROUSELF_PATH . '/admin/class-wp-carousel-free-admin.php';
			require_once WPCAROUSELF_PATH . '/admin/views/tmce-button.php';
			require_once WPCAROUSELF_PATH . '/admin/views/help.php';
			require_once WPCAROUSELF_PATH . '/admin/views/premium.php';
		// }
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WP_Carousel_Free_Loader. Orchestrates the hooks of the plugin.
	 * - WP_Carousel_Free_I18n. Defines internationalization functionality.
	 * - WP_Carousel_Free_Admin. Defines all hooks for the admin area.
	 * - WP_Carousel_Free_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		$this->loader = new WP_Carousel_Free_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_Carousel_Free_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new WP_Carousel_Free_I18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register common hooks.
	 *
	 * @since 2.0.0
	 * @access private
	 */
	private function define_common_hooks() {
		$plugin_cpt           = new WP_Carousel_Free_Post_Type( $this->get_plugin_name(), $this->get_version() );
		$plugin_review_notice = new WP_Carousel_Free_Review( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'init', $plugin_cpt, 'wp_carousel_post_type', 11 );
		$this->loader->add_action( 'admin_notices', $plugin_review_notice, 'display_admin_notice' );
		$this->loader->add_action( 'wp_ajax_sp-wpcfree-never-show-review-notice', $plugin_review_notice, 'dismiss_review_notice' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WP_Carousel_Free_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_admin_styles' );
		$this->loader->add_filter( 'post_updated_messages', $plugin_admin, 'wpcp_carousel_updated_messages', 10, 2 );
		$this->loader->add_filter( 'manage_sp_wp_carousel_posts_columns', $plugin_admin, 'filter_carousel_admin_column' );
		$this->loader->add_action( 'manage_sp_wp_carousel_posts_custom_column', $plugin_admin, 'display_carousel_admin_fields', 10, 2 );
		$this->loader->add_filter( 'plugin_action_links', $plugin_admin, 'add_plugin_action_links', 10, 2 );
		$this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'plugin_row_meta', 10, 2 );
		$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'sp_wpcp_review_text', 10, 2 );
		$this->loader->add_action( 'activated_plugin', $plugin_admin, 'sp_wpcf_redirect_after_activation', 10, 2 );

		// Help Page.
		$help_page = new WP_Carousel_Free_Help( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_menu', $help_page, 'help_admin_menu', 40 );

		// Premium Page.
		$upgrade_page = new WP_Carousel_Free_Upgrade( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_menu', $upgrade_page, 'upgrade_admin_menu', 35 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WP_Carousel_Free_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$plugin_shortcode = new WP_Carousel_Free_Shortcode( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_shortcode( 'sp_wpcarousel', $plugin_shortcode, 'sp_wp_carousel_shortcode' );

	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0.0
	 * @return    WP_Carousel_Free_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    2.0.0
	 */
	public function run() {
		$this->loader->run();
	}

} // SP_WP_Carousel_Free

/**
 * Main instance of WP Carousel Free
 *
 * Returns the main instance of the WP Carousel Free.
 *
 * @since 2.0.0
 * @return void
 */
function sp_wpcf() {
	$plugin = SP_WP_Carousel_Free::init();
	$plugin->loader->run();
}
// Launch it out .
sp_wpcf();
