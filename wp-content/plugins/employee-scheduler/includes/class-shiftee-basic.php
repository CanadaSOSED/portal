<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.0.0
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/includes
 * @author     Range <support@shiftee.co>
 */
class Shiftee_Basic {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      Employee_Scheduler_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

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
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'employee-scheduler';
		$this->version = '2.1.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->run_updates();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Employee_Scheduler_Loader. Orchestrates the hooks of the plugin.
	 * - Employee_Scheduler_i18n. Defines internationalization functionality.
	 * - Employee_Scheduler_Admin. Defines all hooks for the admin area.
	 * - Employee_Scheduler_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-shiftee-basic-loader.php';

		/**
		 * The class containing functions and variables used throughout the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-shiftee-helper.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-shiftee-basic-i18n.php';

		/**
		 * The class responsible for updates
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-shiftee-basic-updater.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-shiftee-basic-admin.php';

		/**
		 * The class responsible for the options page.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-shiftee-basic-options.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-shiftee-basic-public.php';

		/**
		 * The class responsible for sending emails.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-shiftee-email.php';

		$this->loader = new Shiftee_Basic_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Employee_Scheduler_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Shiftee_Basic_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Employee_Scheduler_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function run_updates() {

		$plugin_updater = new Shiftee_Basic_Updater( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_notices', $plugin_updater, 'check_for_updates' );
		$this->loader->add_action( 'wp_ajax_upgrade_shift_meta', $plugin_updater, 'upgrade_shift_meta' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_settings = new Shiftee_Basic_Options();

		$this->loader->add_action( 'admin_menu', $plugin_settings, 'add_options_page' );
		$this->loader->add_action( 'admin_init', $plugin_settings, 'options_page_init' );

		$plugin_admin = new Shiftee_Basic_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_admin, 'register_tax_shift_type', 0 );
		$this->loader->add_action( 'init', $plugin_admin, 'register_tax_shift_status', 0 );
		$this->loader->add_action( 'init', $plugin_admin, 'register_tax_location', 0 );
		$this->loader->add_action( 'init', $plugin_admin, 'register_tax_job_category', 0 );
		$this->loader->add_action( 'init', $plugin_admin, 'register_tax_expense_category', 0 );
		$this->loader->add_action( 'init', $plugin_admin, 'register_tax_expense_status', 0 );
		$this->loader->add_action( 'init', $plugin_admin, 'register_cpt_shift', 0 );
		$this->loader->add_action( 'init', $plugin_admin, 'register_cpt_job', 0 );
		$this->loader->add_action( 'init', $plugin_admin, 'register_cpt_expense', 0 );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_shift_capabilities' );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'announce_shiftee' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'deactivate_employee_scheduler_pro' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'upgrade_to_shiftee' );

		if ( file_exists(  plugin_dir_path( dirname( __FILE__ ) ) . 'libraries/cmb2/init.php' ) ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'libraries/cmb2/init.php';
		}
		$this->loader->add_action( 'cmb2_admin_init', $plugin_admin, 'define_metaboxes' );
		$this->loader->add_action( 'cmb2_localized_data', $plugin_admin, 'cmb2_datetimepicker_options' );

		$this->loader->add_action( 'save_post', $plugin_admin, 'default_shift_status', 100, 2 );
		$this->loader->add_action( 'save_post', $plugin_admin, 'run_that_action_last', 0 );

		$this->loader->add_action( 'show_user_profile', $plugin_admin, 'employee_profile_fields' );
		$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'employee_profile_fields' );
		$this->loader->add_action( 'personal_options_update', $plugin_admin, 'save_employee_profile_fields' );
		$this->loader->add_action( 'edit_user_profile_update', $plugin_admin, 'save_employee_profile_fields' );

		$this->loader->add_filter( 'manage_shift_posts_columns', $plugin_admin, 'shift_overview_columns_headers', 10 );
		$this->loader->add_action( 'manage_shift_posts_custom_column', $plugin_admin, 'shift_overview_columns', 10, 2 );
		$this->loader->add_filter( 'manage_edit-shifts_sortable_columns', $plugin_admin, 'shift_sortable_columns' );

		$this->loader->add_action( 'shiftee_notify_employee_about_shift', $plugin_admin, 'send_notification_email', 10, 3 );
		$this->loader->add_action( 'shiftee_options_sidebar', $plugin_admin, 'admin_sidebar', 10 );
		$this->loader->add_action( 'shiftee_save_employee_note_action', $plugin_admin, 'employee_note_admin_notification', 10, 3 );
		$this->loader->add_action( 'shiftee_clock_out_action', $plugin_admin, 'clock_out_notification', 10, 2 );
		$this->loader->add_action( 'shiftee_clock_out_action', $plugin_admin, 'calculate_wage_on_clock_out', 10, 2 );
		$this->loader->add_action( 'shiftee_clock_out_action', $plugin_admin, 'calculate_duration_on_clock_out', 10, 2 );
		$this->loader->add_action( 'shiftee_add_extra_work_action', $plugin_admin, 'notify_admin_extra_shift', 10, 3 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Shiftee_Basic_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_filter( 'the_title', $plugin_public, 'single_shift_title', 10, 2 );
		$this->loader->add_filter( 'the_content', $plugin_public, 'single_shift_view' );

		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );
		$this->loader->add_action( 'init', $plugin_public, 'output_buffer');

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    2.0.0
	 */
	public function run() {
		$this->loader->run();
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
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0.0
	 * @return    Employee_Scheduler_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
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



}
