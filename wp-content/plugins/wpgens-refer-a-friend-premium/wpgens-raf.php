<?php
/**
 * Plugin Name: Refer a Friend for WooCommerce PREMIUM
 * Plugin URI: https://wpgens.com/downloads/refer-a-friend-for-woocommerce-premium/
 * Description: PREMIUM Refer a friend by WPGENS. Go to WooCommerce -> Settings -> Refer a friend tab to set it up.
 * Version: 2.3.11
 * Author: WPGens
 * Author URI: https://wpgens.com
 * Text Domain: gens-raf
 * Domain Path: /languages
 * WC requires at least: 2.6
 * WC tested up to: 3.9
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( !class_exists( 'WPGens_RAF' ) ) :

	final class WPGens_RAF {
		
		/**
		 * Plugin version.
		 *
		 * @var string
		 * @since 2.0
		 */
		private $version = '2.3.11';

		/**
		 * Product Tab instance.
		 *
		 * @var WC_Cart
		 */
		public $product_tab = null;

		/**
		 * Order instance.
		 *
		 * @var WC_Cart
		 */
		public $order_object = null;

		/**
		 * Order instance.
		 *
		 * @var WPGens_RAF_DB
		 */
		public $db_object = null;

		/**
		 * My Account instance.
		 *
		 * @var WC_Cart
		 */
		public $my_account = null;

		/**
		 * The single instance of the class.
		 *
		 * @var WPGens_RAF
		 * @since 2.0
		 */
		protected static $_instance = null;

		/**
		 * Main WooCommerce Instance.
		 *
		 * Ensures only one instance of WooCommerce is loaded or can be loaded.
		 *
		 * @since 2.0
		 * @static
		 * @return Refer a friend - Main instance.
		 */
		public static function instance()
		{
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Plugin Constructor.
		 * @since 2.0
		 */
		public function __construct()
		{
			$this->define_constants();
			$this->includes();
			$this->init_hooks();
		}

		/**
		 * Define RAF Constants.
		 * @since 2.0
		 */
		private function define_constants()
		{
			$this->define( 'WPGENS_RAF_URL', plugin_dir_url( __FILE__ ) );
			$this->define( 'WPGENS_RAF_ABSPATH', dirname( __FILE__ ) . '/' );
			$this->define( 'WPGENS_RAF_VERSION', $this->get_version() );
			$this->define( 'WPGENS_RAF_PLUGIN_LICENSE_PAGE', 'gens-raf' ); // mozda promjenitit
			$this->define( 'WPGENS_RAF_ITEM_NAME', 'Refer a Friend for WooCommerce PREMIUM' );
			$this->define( 'WPGENS_RAF_STORE_URL', 'https://wpgens.com' );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @since  2.0
		 * @param  string $name
		 * @param  string|bool $value
		 */
		private function define( $name, $value )
		{
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Returns Plugin version for global
		 * @since  2.0
		 */
		private function get_version()
		{
			return $this->version;
		}

		/**
		 * Include required core files used in admin and on the frontend. 
		 * @since  2.0
		 * @Ttodo Should switch to Autoloader
		 */
		public function includes()
		{
			// Admin Classes
			include_once( WPGENS_RAF_ABSPATH . 'includes/admin/class-wpgens-raf-db.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/admin/class-wpgens-raf-activator.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/admin/edd_licence/class-wpgens-raf-licence.php' );
		//	include_once( WPGENS_RAF_ABSPATH . 'includes/admin/class-wpgens-raf-options.php' ); need to move all options to this separate class.
			include_once( WPGENS_RAF_ABSPATH . 'includes/admin/class-wpgens-raf-tools.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/admin/class-wpgens-raf-user-meta.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/admin/class-wpgens-raf-menu.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/admin/class-wpgens-raf-events.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/admin/class-wpgens-raf-admin-order.php' );
			
			if(isset($_GET['page']) && $_GET['page'] === 'gens-raf') {
				include_once( WPGENS_RAF_ABSPATH . 'includes/admin/class-wpgens-raf-list-table.php' );
			}
			if(version_compare( WC_VERSION, '3.0', '>' )) {
				include_once( WPGENS_RAF_ABSPATH . 'includes/admin/reports/class-wpgens-raf-report.php' );				
			}

			// Front Classes
			include_once( WPGENS_RAF_ABSPATH . 'includes/class-wpgens-raf-assets.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/class-wpgens-raf-order.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/class-wpgens-raf-product.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/class-wpgens-raf-user.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/class-wpgens-raf-email.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/class-wpgens-raf-myaccount.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/class-wpgens-raf-shortcodes.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/class-wpgens-raf-checkout.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/class-wpgens-raf-coupons.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/class-wpgens-raf-share.php' );

			// Extensions
			include_once( WPGENS_RAF_ABSPATH . 'includes/extensions/class-wpgens-raf-subscription.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/extensions/class-wpgens-raf-pointsrewards.php' );
			include_once( WPGENS_RAF_ABSPATH . 'includes/extensions/class-wpgens-raf-popupmaker.php' );
		}
		
		/**
		 * Hook into Actions & Filters.
		 * @since  2.0
		 */
		private function init_hooks()
		{
			register_activation_hook( __FILE__, array( 'Gens_RAF_Activator', 'activate' ) );
            add_action( 'init', array( $this, 'init' ), 0 );
            add_action( 'plugins_loaded', array($this, 'wpgens_event_action_init'), 0 );
 			add_action( 'init', array( 'WPGens_RAF_Shortcodes', 'init' ) );
 			add_action( 'wpcf7_init', array('WPGens_RAF_Shortcodes','gens_raf_add_cf7_form_tag') );
 			add_action( 'user_register', array( 'WPGens_RAF_User', 'new_user_add_referral_id') ); // new user registration hook
        }

		/**
		 * Initialize do_action for saving events.
		 * @since  2.3
		 */
        public function wpgens_event_action_init()
        {
			$this->db_object = new WPGens_RAF_DB();
        }

		/**
		 * Init Refer a Friend plugin when WordPress Initialises.
		 * @since  2.0
		 */
		public function init()
		{
			// Before init action.
			do_action( 'before_wpgens_raf_init' );

			// Init My Account Tab Class
			$this->my_account   = new WPGens_RAF_MyAccount();
			// Init Product Tab Class
			$this->product_tab  = new WPGens_RAF_Product();
			// Init Order Class
			$this->order_object = new WPGENS_RAF_Order();

			// Set up localisation.
			$this->load_plugin_textdomain();

			// After init action.
			do_action( 'after_wpgens_raf_init' );
		}

		/**
		 * Load Localisation files.
		 * @since  2.0
		 */
		public function load_plugin_textdomain()
		{
			load_plugin_textdomain( 'gens-raf', false, basename( dirname( __FILE__ ) ) . '/languages' );
		}

	    /**
	     * Get the path of PHP template for WPGens RAF Views
	     *
	     * @return string
	     */
	    public static function get_template_path($template_name, $template_path = '', $guest_view = FALSE) 
	    {
	    	// Default Template Path
	    	$default_path = WPGENS_RAF_ABSPATH. 'templates' .trailingslashit($template_path);

	    	// Append "guest" to guest templates.
	    	if($guest_view === TRUE) {
	    		if(!is_user_logged_in()) {
	    			$template_name = 'guest-'.$template_name;
	    		}
	    	}

			// Look within passed path within the theme - this is priority.
			$template = locate_template(
				array(
					'wpgens-raf'. trailingslashit( $template_path ) . $template_name
				)
			);

			// Get default template/
			if ( ! $template ) {
				$template = $default_path . $template_name;
			}

			// Return what we found.
			return apply_filters( 'wpgens_raf_locate_template', $template, $template_path, $guest_view );
	    }

	}

	WPGens_RAF::instance();

endif;

?>
