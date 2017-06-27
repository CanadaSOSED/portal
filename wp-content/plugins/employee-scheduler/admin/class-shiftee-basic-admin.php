<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/admin
 * @author     Range <support@shiftee.co>
 */
class Shiftee_Basic_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The plugin settings.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    options    The settings for this plugin.
	 */
	private $options;

	/**
	 * The plugin helper.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    options    The helper class.
	 */
	private $helper;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$helper = new Shiftee_Helper();
		$this->helper = $helper;
		$this->options = $helper->shiftee_options();

		include_once plugin_dir_path( dirname( __FILE__ ) ) . 'libraries/Tax-meta-class/Tax-meta-class.php';
		$this->define_taxonomy_meta();

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    2.0.0
	 */
	public function enqueue_styles( $hook ) {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/shiftee-admin.css', array(), $this->version, 'all' );

		if( 'shift_page_view-schedules' == $hook ) {
		    wp_enqueue_style( 'cmb2', WP_PLUGIN_URL . '/employee-scheduler/libraries/cmb2/css/cmb2.min.css' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    2.0.0
	 *
	 * @param $hook  Page hook
	 */
	public function enqueue_scripts( $hook ) {

		global $post;

		if( 'shift_page_view-schedules' == $hook ) {
			wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );
            wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/shiftee-basic-admin.js', array( 'jquery', 'jquery-ui-datepicker' ), $this->version, false );
			$datetimepicker_options = $this->helper->get_datetimepicker_options();
			wp_localize_script( $this->plugin_name, 'datetimepicker_options', $datetimepicker_options );
		}

		if( 'dashboard_page_shiftee-upgrades' == $hook ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/shiftee-basic-admin.js', array( 'jquery' ), $this->version, false );
			wp_localize_script( $this->plugin_name, 'shiftee_update_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		}

	}

	/**
	 * Register the "Shift Type" custom taxonomy and add a few default shift types.
	 *
	 * @since 1.0.0
	 */
	public function register_tax_shift_type() {

		$labels = array(
			'name'                       => _x( 'Shift Types', 'Taxonomy General Name', 'employee-scheduler' ),
			'singular_name'              => _x( 'Shift Type', 'Taxonomy Singular Name', 'employee-scheduler' ),
			'menu_name'                  => __( 'Shift Types', 'employee-scheduler' ),
			'all_items'                  => __( 'All Shift Types', 'employee-scheduler' ),
			'parent_item'                => __( 'Parent Shift Type', 'employee-scheduler' ),
			'parent_item_colon'          => __( 'Parent Shift Type:', 'employee-scheduler' ),
			'new_item_name'              => __( 'New  Shift Type', 'employee-scheduler' ),
			'add_new_item'               => __( 'Add New Shift Type', 'employee-scheduler' ),
			'edit_item'                  => __( 'Edit Shift Type', 'employee-scheduler' ),
			'update_item'                => __( 'Update Shift Type', 'employee-scheduler' ),
			'separate_items_with_commas' => __( 'Separate Shift Types with commas', 'employee-scheduler' ),
			'search_items'               => __( 'Search Shift Types', 'employee-scheduler' ),
			'add_or_remove_items'        => __( 'Add or remove Shift Types', 'employee-scheduler' ),
			'choose_from_most_used'      => __( 'Choose from the most used Shift Types', 'employee-scheduler' ),
			'not_found'                  => __( 'Not Found', 'employee-scheduler' ),
		);

		$labels = apply_filters( 'shiftee_filter_shift_type_labels', $labels );

		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
			'capabilities'       => array(
				'manage_terms'  => 'edit_shifts',
				'edit_terms'    => 'edit_shifts',
				'delete_terms'  => 'edit_shifts',
				'assign_terms'  => 'edit_shifts'
			)
		);
		register_taxonomy( 'shift_type', array( 'shift' ), $args );

		// Create default types
		wp_insert_term(
			__( 'Extra', 'employee-scheduler' ),
			'shift_type',
			array(
				'description'=> __( 'Work done outside of a scheduled shift', 'employee-scheduler' ),
				'slug' => 'extra',
			)
		);

		wp_insert_term(
			__( 'Paid Time Off', 'employee-scheduler' ),
			'shift_type',
			array(
				'description'=> __( 'Paid time that is not a work shift', 'employee-scheduler' ),
				'slug' => 'pto',
			)
		);

	}

	/**
	 * Register the "Shift Status" custom taxonomy and add a few default shift statuses.
	 *
	 * @since 1.0.0
	 */
	public function register_tax_shift_status() {

		$labels = array(
			'name'                       => _x( 'Shift Statuses', 'Taxonomy General Name', 'employee-scheduler' ),
			'singular_name'              => _x( 'Shift Status', 'Taxonomy Singular Name', 'employee-scheduler' ),
			'menu_name'                  => __( 'Shift Statuses', 'employee-scheduler' ),
			'all_items'                  => __( 'All Shift Statuses', 'employee-scheduler' ),
			'parent_item'                => __( 'Parent Shift Status', 'employee-scheduler' ),
			'parent_item_colon'          => __( 'Parent Shift Status:', 'employee-scheduler' ),
			'new_item_name'              => __( 'New Shift Status', 'employee-scheduler' ),
			'add_new_item'               => __( 'Add New Shift Status', 'employee-scheduler' ),
			'edit_item'                  => __( 'Edit Shift Status', 'employee-scheduler' ),
			'update_item'                => __( 'Update Shift Status', 'employee-scheduler' ),
			'separate_items_with_commas' => __( 'Separate Shift Statuses with commas', 'employee-scheduler' ),
			'search_items'               => __( 'Search Shift Statuses', 'employee-scheduler' ),
			'add_or_remove_items'        => __( 'Add or remove Shift Statuses', 'employee-scheduler' ),
			'choose_from_most_used'      => __( 'Choose from the most used Shift Statuses', 'employee-scheduler' ),
			'not_found'                  => __( 'Not Found', 'employee-scheduler' ),
		);

		$labels = apply_filters( 'shiftee_filter_shift_status_labels', $labels );

		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
			'capabilities'       => array(
				'manage_terms'  => 'edit_shifts',
				'edit_terms'    => 'edit_shifts',
				'delete_terms'  => 'edit_shifts',
				'assign_terms'  => 'edit_shifts'
			)
		);
		register_taxonomy( 'shift_status', array( 'shift' ), $args );

		wp_insert_term(
			__( 'Unassigned', 'employee-scheduler' ),
			'shift_status',
			array(
				'description'=> 'No one has been assigned to work this shift',
				'slug' => 'unassigned',
			)
		);

		wp_insert_term(
			__( 'Assigned', 'employee-scheduler' ),
			'shift_status',
			array(
				'description'=> 'Default status for a shift, indicates that this shift has been assigned to a staff member',
				'slug' => 'assigned',
			)
		);

		wp_insert_term(
			__( 'Worked', 'employee-scheduler' ),
			'shift_status',
			array(
				'description'=> 'Staff member has worked this shift',
				'slug' => 'worked',
			)
		);

		// if admin must approve extra shifts, then we need a "pending approval" status and a "not approved" status
		if( isset( $this->options['extra_shift_approval'] ) && '1' == $this->options['extra_shift_approval'] ) {
			wp_insert_term(
				__( 'Pending Approval', 'employee-scheduler' ),
				'shift_status',
				array(
					'description'=> 'Staff member has worked the shift, but it is pending admin approval',
					'slug' => 'pending-approval',
				)
			);

			wp_insert_term(
				__( 'Not Approved', 'employee-scheduler' ),
				'shift_status',
				array(
					'description'=> 'Staff member reported an extra shift, but admin did not approve it',
					'slug' => 'not-approved',
				)
			);
		}

	}

	/**
	 * Register "Location" custom taxonomy
	 *
	 * @since 1.0.0
	 */
	function register_tax_location() {

		$labels = array(
			'name'                       => _x( 'Locations', 'Taxonomy General Name', 'employee-scheduler' ),
			'singular_name'              => _x( 'Location', 'Taxonomy Singular Name', 'employee-scheduler' ),
			'menu_name'                  => __( 'Locations', 'employee-scheduler' ),
			'all_items'                  => __( 'All Locations', 'employee-scheduler' ),
			'parent_item'                => __( 'Parent Location', 'employee-scheduler' ),
			'parent_item_colon'          => __( 'Parent Location:', 'employee-scheduler' ),
			'new_item_name'              => __( 'New Item Location', 'employee-scheduler' ),
			'add_new_item'               => __( 'Add New Location', 'employee-scheduler' ),
			'edit_item'                  => __( 'Edit Location', 'employee-scheduler' ),
			'update_item'                => __( 'Update Location', 'employee-scheduler' ),
			'view_item'                  => __( 'View Location', 'employee-scheduler' ),
			'separate_items_with_commas' => __( 'Separate Locations with commas', 'employee-scheduler' ),
			'add_or_remove_items'        => __( 'Add or remove Locations', 'employee-scheduler' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'employee-scheduler' ),
			'popular_items'              => __( 'Popular Locations', 'employee-scheduler' ),
			'search_items'               => __( 'Search Locations', 'employee-scheduler' ),
			'not_found'                  => __( 'Not Found', 'employee-scheduler' ),
		);

		$labels = apply_filters( 'shiftee_filter_location_labels', $labels );

		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
			'capabilities'       => array(
				'manage_terms'  => 'edit_shifts',
				'edit_terms'    => 'edit_shifts',
				'delete_terms'  => 'edit_shifts',
				'assign_terms'  => 'edit_shifts'
			)
		);
		register_taxonomy( 'location', array( 'shift' ), $args );

	}

	/**
	 * Register "Job Category" custom taxonomy
	 *
	 * @since 1.0.0
	 */
	function register_tax_job_category() {

		$labels = array(
			'name'                       => _x( 'Job Category', 'Taxonomy General Name', 'employee-scheduler' ),
			'singular_name'              => _x( 'Job Category', 'Taxonomy Singular Name', 'employee-scheduler' ),
			'menu_name'                  => __( 'Job Categories', 'employee-scheduler' ),
			'all_items'                  => __( 'All Job Categories', 'employee-scheduler' ),
			'parent_item'                => __( 'Parent Job Category', 'employee-scheduler' ),
			'parent_item_colon'          => __( 'Parent Job Category:', 'employee-scheduler' ),
			'new_item_name'              => __( 'New Job Category', 'employee-scheduler' ),
			'add_new_item'               => __( 'Add New Job Category', 'employee-scheduler' ),
			'edit_item'                  => __( 'Edit Job Category', 'employee-scheduler' ),
			'update_item'                => __( 'Update Job Category', 'employee-scheduler' ),
			'separate_items_with_commas' => __( 'Separate Job Categories with commas', 'employee-scheduler' ),
			'search_items'               => __( 'Search Job Categories', 'employee-scheduler' ),
			'add_or_remove_items'        => __( 'Add or remove Job Categories', 'employee-scheduler' ),
			'choose_from_most_used'      => __( 'Choose from the most used Job Categories', 'employee-scheduler' ),
			'not_found'                  => __( 'Not Found', 'employee-scheduler' ),
		);

		$labels = apply_filters( 'shiftee_filter_job_category_labels', $labels );

		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
			'capabilities'       => array(
				'manage_terms'  => 'edit_shifts',
				'edit_terms'    => 'edit_shifts',
				'delete_terms'  => 'edit_shifts',
				'assign_terms'  => 'edit_shifts'
			)
		);
		register_taxonomy( 'job_category', array( 'job' ), $args );

	}

	/**
	 * Register "Expense Category" custom taxonomy
	 *
	 * @since 1.0.0
	 */
	public function register_tax_expense_category() {

		$labels = array(
			'name'                       => _x( 'Expense Categories', 'Taxonomy General Name', 'employee-scheduler' ),
			'singular_name'              => _x( 'Expense Category', 'Taxonomy Singular Name', 'employee-scheduler' ),
			'menu_name'                  => __( 'Expense Categories', 'employee-scheduler' ),
			'all_items'                  => __( 'All Expense Categories', 'employee-scheduler' ),
			'parent_item'                => __( 'Parent Expense Category', 'employee-scheduler' ),
			'parent_item_colon'          => __( 'Parent Expense Category:', 'employee-scheduler' ),
			'new_item_name'              => __( 'New Expense Category', 'employee-scheduler' ),
			'add_new_item'               => __( 'Add Expense Category', 'employee-scheduler' ),
			'edit_item'                  => __( 'Edit Expense Category', 'employee-scheduler' ),
			'update_item'                => __( 'Update Expense Category', 'employee-scheduler' ),
			'separate_items_with_commas' => __( 'Separate Expense Categories with commas', 'employee-scheduler' ),
			'search_items'               => __( 'Search Expense Categories', 'employee-scheduler' ),
			'add_or_remove_items'        => __( 'Add or remove Expense Categories', 'employee-scheduler' ),
			'choose_from_most_used'      => __( 'Choose from the most used Expense Categories', 'employee-scheduler' ),
			'not_found'                  => __( 'Not Found', 'employee-scheduler' ),
		);

		$labels = apply_filters( 'shiftee_filter_expense_category_labels', $labels );

		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
			'capabilities'       => array(
				'manage_terms'  => 'edit_shifts',
				'edit_terms'    => 'edit_shifts',
				'delete_terms'  => 'edit_shifts',
				'assign_terms'  => 'edit_shifts'
			)
		);
		register_taxonomy( 'expense_category', array( 'expense' ), $args );

		wp_insert_term(
			'Mileage', // the term
			'expense_category', // the taxonomy
			array(
				'description'=> 'Mileage to be reimbursed',
				'slug' => 'mileage',
			)
		);

		wp_insert_term(
			'Receipt', // the term
			'expense_category', // the taxonomy
			array(
				'description'=> 'Receipts to be reimbursed',
				'slug' => 'receipt',
			)
		);

	}

	/**
	 * Register "Expense Status" custom taxonomy and create some defaults.
	 *
	 * @since 1.0.0
	 */
	public function register_tax_expense_status() {

		$labels = array(
			'name'                       => _x( 'Expense Statuses', 'Taxonomy General Name', 'employee-scheduler' ),
			'singular_name'              => _x( 'Expense Status', 'Taxonomy Singular Name', 'employee-scheduler' ),
			'menu_name'                  => __( 'Expense Statuses', 'employee-scheduler' ),
			'all_items'                  => __( 'All Expense Statuses', 'employee-scheduler' ),
			'parent_item'                => __( 'Parent Expense Status', 'employee-scheduler' ),
			'parent_item_colon'          => __( 'Parent Expense Status:', 'employee-scheduler' ),
			'new_item_name'              => __( 'New Expense Status', 'employee-scheduler' ),
			'add_new_item'               => __( 'Add Expense Status', 'employee-scheduler' ),
			'edit_item'                  => __( 'Edit Expense Status', 'employee-scheduler' ),
			'update_item'                => __( 'Update Expense Status', 'employee-scheduler' ),
			'separate_items_with_commas' => __( 'Separate Expense Statuses with commas', 'employee-scheduler' ),
			'search_items'               => __( 'Search Expense Statuses', 'employee-scheduler' ),
			'add_or_remove_items'        => __( 'Add or remove Expense Statuses', 'employee-scheduler' ),
			'choose_from_most_used'      => __( 'Choose from the most used Expense Statuses', 'employee-scheduler' ),
			'not_found'                  => __( 'Not Found', 'employee-scheduler' ),
		);

		$labels = apply_filters( 'shiftee_filter_expense_status_labels', $labels );

		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
			'capabilities'       => array(
				'manage_terms'  => 'edit_shifts',
				'edit_terms'    => 'edit_shifts',
				'delete_terms'  => 'edit_shifts',
				'assign_terms'  => 'edit_shifts'
			)
		);
		register_taxonomy( 'expense_status', array( 'expense' ), $args );

		wp_insert_term(
			'Reimbursed', // the term
			'expense_status', // the taxonomy
			array(
				'description'=> 'Expenses for which employee has been reimbursed',
				'slug' => 'reimbursed',
			)
		);

	}

	/**
	 * Register "Shift" custom post type
	 *
	 * @since 1.0.0
	 */
	function register_cpt_shift() {

		$labels = array(
			'name'                => _x( 'Shifts', 'Post Type General Name', 'employee-scheduler' ),
			'singular_name'       => _x( 'Shift', 'Post Type Singular Name', 'employee-scheduler' ),
			'menu_name'           => __( 'Shifts', 'employee-scheduler' ),
			'parent_item_colon'   => __( 'Parent Shift:', 'employee-scheduler' ),
			'all_items'           => __( 'All Shifts', 'employee-scheduler' ),
			'view_item'           => __( 'View Shift', 'employee-scheduler' ),
			'add_new_item'        => __( 'Add New Shift', 'employee-scheduler' ),
			'add_new'             => __( 'Add New', 'employee-scheduler' ),
			'edit_item'           => __( 'Edit Shift', 'employee-scheduler' ),
			'update_item'         => __( 'Update Shift', 'employee-scheduler' ),
			'search_items'        => __( 'Search Shifts', 'employee-scheduler' ),
			'not_found'           => __( 'Not found', 'employee-scheduler' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'employee-scheduler' ),
		);

		$labels = apply_filters( 'shiftee_filter_shift_labels', $labels );

		$args = array(
			'label'               => __( 'shift', 'employee-scheduler' ),
			'description'         => __( 'Shifts you can assign to employees', 'employee-scheduler' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor' ),
			'taxonomies'          => array( 'shift_type' ),
			'hierarchical'        => true,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_position'       => 70,
			'menu_icon'           => 'dashicons-calendar',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
//			'map_meta_cap'        => true,
			'capability_type'     => 'shift',
			'capabilities' => array(
				'publish_posts' => 'publish_shifts',
				'edit_posts' => 'edit_shifts',
				'edit_others_posts' => 'edit_others_shifts',
				'read_private_posts' => 'read_private_shifts',
				'edit_post' => 'edit_shift',
				'delete_post' => 'delete_shift',
				'read_post' => 'read_shift',
				'delete_posts' => 'delete_shifts'
			),
		);
		register_post_type( 'shift', $args );

	}

	/**
	 * Register "Job" custom post type
	 *
	 * @since 1.0.0
	 */
	public function register_cpt_job() {

		$labels = array(
			'name'                => _x( 'Jobs', 'Post Type General Name', 'employee-scheduler' ),
			'singular_name'       => _x( 'Job', 'Post Type Singular Name', 'employee-scheduler' ),
			'menu_name'           => __( 'Jobs', 'employee-scheduler' ),
			'parent_item_colon'   => __( 'Parent Job:', 'employee-scheduler' ),
			'all_items'           => __( 'All Jobs', 'employee-scheduler' ),
			'view_item'           => __( 'View Job', 'employee-scheduler' ),
			'add_new_item'        => __( 'Add New Job', 'employee-scheduler' ),
			'add_new'             => __( 'Add New', 'employee-scheduler' ),
			'edit_item'           => __( 'Edit Job', 'employee-scheduler' ),
			'update_item'         => __( 'Update Job', 'employee-scheduler' ),
			'search_items'        => __( 'Search Jobs', 'employee-scheduler' ),
			'not_found'           => __( 'Not found', 'employee-scheduler' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'employee-scheduler' ),
		);

		$labels = apply_filters( 'shiftee_filter_job_labels', $labels );

		$args = array(
			'label'               => __( 'job', 'employee-scheduler' ),
			'description'         => __( 'jobs', 'employee-scheduler' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail', 'revisions', ),
			'taxonomies'          => array( 'job_category' ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => 70,
			'menu_icon'           => 'dashicons-hammer',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
//			'map_meta_cap'        => true,
			'capability_type'     => 'shift',
		);
		register_post_type( 'job', $args );

	}

	/**
	 * Register "Expense" custom post type.
	 *
	 * @since 1.0.0
	 */
	public function register_cpt_expense() {

		$labels = array(
			'name'                => _x( 'Expenses', 'Post Type General Name', 'employee-scheduler' ),
			'singular_name'       => _x( 'Expense', 'Post Type Singular Name', 'employee-scheduler' ),
			'menu_name'           => __( 'Expenses', 'employee-scheduler' ),
			'parent_item_colon'   => __( 'Parent Expense:', 'employee-scheduler' ),
			'all_items'           => __( 'All Expenses', 'employee-scheduler' ),
			'view_item'           => __( 'View Expense', 'employee-scheduler' ),
			'add_new_item'        => __( 'Add New Expense', 'employee-scheduler' ),
			'add_new'             => __( 'Add New', 'employee-scheduler' ),
			'edit_item'           => __( 'Edit Expense', 'employee-scheduler' ),
			'update_item'         => __( 'Update Expense', 'employee-scheduler' ),
			'search_items'        => __( 'Search Expenses', 'employee-scheduler' ),
			'not_found'           => __( 'Not found', 'employee-scheduler' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'employee-scheduler' ),
		);

		$labels = apply_filters( 'shiftee_filter_expense_labels', $labels );

		$args = array(
			'label'               => __( 'expense', 'employee-scheduler' ),
			'description'         => __( 'Expenses submitted by staff', 'employee-scheduler' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'thumbnail' ),
			'taxonomies'          => array( 'expense_category' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'menu_position'       => 70,
			'menu_icon'           => 'dashicons-chart-area',
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
//			'map_meta_cap'        => true,
			'capability_type'     => 'shift',
		);
		register_post_type( 'expense', $args );

	}

	/**
	 * Create custom capabilities for shifts.
	 *
	 * @since 1.9.0
	 */
	public function admin_shift_capabilities() {
		$role = get_role( 'administrator' );

		$role->add_cap( 'edit_posts' );
		$role->add_cap( 'create_users' );
		$role->add_cap( 'edit_users' );
		$role->add_cap( 'list_users' );
		$role->add_cap( 'publish_shifts' );
		$role->add_cap( 'edit_shifts' );
		$role->add_cap( 'edit_others_shifts' );
		$role->add_cap( 'read_private_shifts' );
		$role->add_cap( 'edit_shift' );
		$role->add_cap( 'delete_shift' );
		$role->add_cap( 'read_shift' );
		$role->add_cap( 'delete_shifts' );
	}

	/**
	 * Announce Shiftee
	 *
	 * Each user will see a splash sceen announcing that Employee Scheduler is now Shiftee
	 *
	 * @since 2.0.0
	 */
	public function announce_shiftee() {

		if( !isset( $this->options['db_version'] ) || '0' == $this->options['db_version'] ) {

			if( $this->helper->check_user_role( 'administrator' ) || $this->helper->check_user_role( 'shiftee_manager' ) ) {

				$user      = get_current_user_id();
				$dismissed = get_user_meta( $user, 'shiftee_dismiss_shiftee_announcement', true );

				// user has seen this already, so don't show it
				if ( 'dismissed' == $dismissed ) {
					return;
				}

				// save user meta so we know this user has viewed this page
				add_user_meta( $user, 'shiftee_dismiss_shiftee_announcement', 'dismissed' );

				wp_redirect( admin_url( '/admin.php?page=about-shiftee' ) );
				exit;

			}

		}
	}

	/**
	 * Deactivate Employee Scheduler Pro
	 *
	 * Employee Scheduler Pro won't work with Shiftee Basic, so deactivate it and tell the user.
	 *
	 * @since 2.0.0
	 */
	public function deactivate_employee_scheduler_pro() {

		if ( is_admin() && is_plugin_active( 'employee-scheduler-pro/employee-scheduler-pro.php' ) ) {
			deactivate_plugins( 'employee-scheduler-pro/employee-scheduler-pro.php' );

		}

	}

	/**
	 * Tell the user Employee Scheduler Pro is deactivated
	 *
	 * @since 2.0.0
	 */
	public function upgrade_to_shiftee() {
		if( is_admin() && file_exists( WP_PLUGIN_DIR . 'employee-scheduler-pro/employee-scheduler-pro.php' ) && !is_plugin_active( 'shiftee/shiftee.php' ) ) {
			?>
			<div class="error">
			<p>
				<?php printf(
					__( 'Employee Scheduler Pro is now Shiftee Basic!  Employee Scheduler Pro has been deactivated because it is not compatible with Shiftee Basic.  Please <a href="%s">upgrade to Shiftee</a>.', 'employee-scheduler' ),
					'https://shiftee.co/upgrade'
				); ?>
			</p>
			</div><?php
		}
	}

	/**
	 * Default shift status.
	 *
	 * When a shift is saved, if no other shift status has been selected, it will default to "assigned."
	 *
	 * @since 1.0.0
	 *
	 * @link http://wordpress.mfields.org/2010/set-default-terms-for-your-custom-taxonomies-in-wordpress-3-0/
	 *
	 * @param int  $post_id ID of the post being saved
	 * @param int  $post ID of post object
	 *
	 */
	public function default_shift_status( $post_id, $post ) {
		if ( 'publish' === $post->post_status && 'shift' == $post->post_type ) {

			if( !$this->helper->get_shift_connection( $post_id, 'employee' ) ) {
				$defaults = array(
					'shift_status' => array( 'unassigned' ),
				);
			} else {
				$defaults = array(
					'shift_status' => array( 'assigned' ),
				);
			}
			$taxonomies = get_object_taxonomies( $post->post_type );
			foreach ( (array) $taxonomies as $taxonomy ) {
				$terms = wp_get_post_terms( $post_id, $taxonomy );
				if ( empty( $terms ) && array_key_exists( $taxonomy, $defaults ) ) {
					wp_set_object_terms( $post_id, $defaults[$taxonomy], $taxonomy );
				}
			}
		}
	}

	/**
	 * Display a form with additional user profile fields.
	 *
	 * @since 1.0.0
	 *
	 * @param $user
	 */
	public function employee_profile_fields( $user ) {
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/user-profile.php';
	}

	/**
	 * Save additional user profile fields.
	 *
	 * @since 1.0.0
	 *
	 * @param $user_id
	 *
	 * @return bool
	 */
	public function save_employee_profile_fields( $user_id ) {

		if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }

		if( isset( $_POST['address'] ) ) {
			update_user_meta( $user_id, 'address', sanitize_text_field( $_POST['address'] ) );
		}
		if( isset( $_POST['city'] ) ) {
			update_user_meta( $user_id, 'city', sanitize_text_field( $_POST['city'] ) );
		}
		if( isset( $_POST['state'] ) ) {
			update_user_meta( $user_id, 'state', sanitize_text_field( $_POST['state'] ) );
		}
		if( isset( $_POST['zip'] ) ) {
			update_user_meta( $user_id, 'zip', sanitize_text_field( $_POST['zip'] ) );
		}
		if( isset( $_POST['phone'] ) ) {
			update_user_meta( $user_id, 'phone', sanitize_text_field( $_POST['phone'] ) );
		}

		do_action( 'shiftee_save_additional_user_profile_fields', $user_id );
	}


	/**
	 * Create custom metaboxes with CMB2
     *
     * @since 2.1.0
	 */
	public function define_metaboxes() {
		$prefix = '_shiftee_';

		// Shift Details Metabox
		$shift_meta = new_cmb2_box( array(
			'id'            => 'shift_metabox',
			'title'         => __( 'Shift Details', 'employee-scheduler' ),
			'object_types'  => array( 'shift', ),
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names'    => true,
		) );

		$shift_meta->add_field( array(
			'name' => __( 'Scheduled Start Date/Time', 'employee-scheduler' ),
			'id'   => $prefix . 'shift_start',
			'type' => 'text_datetime_timestamp',
		) );

		$shift_meta->add_field( array(
			'name' => __( 'Scheduled End Date/Time', 'employee-scheduler' ),
			'id'   => $prefix . 'shift_end',
			'type' => 'text_datetime_timestamp',
			'after_field'    => array( $this, 'display_scheduled_duration' ),
		) );

		$shift_meta->add_field( array(
			'name' => __( 'Scheduled Duration', 'employee-scheduler' ),
			'id'   => $prefix . 'scheduled_duration',
			'type' => 'hidden',
			'sanitization_cb' => array( $this, 'calculate_scheduled_duration' ),
		) );

		$shift_meta->add_field( array(
			'name' => __( 'Clock In Time', 'employee-scheduler' ),
			'desc' => __( 'As reported by staff', 'employee-scheduler' ),
			'id'   => $prefix . 'clock_in',
			'type' => 'text_datetime_timestamp',
		) );

		$shift_meta->add_field( array(
			'name' => __( 'Clock Out Time', 'employee-scheduler' ),
			'desc' => __( 'As reported by staff', 'employee-scheduler' ),
			'id'   => $prefix . 'clock_out',
			'type' => 'text_datetime_timestamp',
			'after_field'    => array( $this, 'display_worked_duration' ),
		) );

		$shift_meta->add_field( array(
			'name' => __( 'Worked Duration', 'employee-scheduler' ),
			'id'   => $prefix . 'worked_duration',
			'type' => 'hidden',
			'sanitization_cb' => array( $this, 'calculate_worked_duration' ),
		) );

		$shift_meta->add_field( array(
			'name' => __( 'Shift Wage', 'employee-scheduler' ),
			'id'   => $prefix . 'wage',
			'type' => 'hidden',
			'sanitization_cb' => array( $this, 'calculate_wage' ),
		) );

		$shift_meta->add_field( array(
			'name' => __( 'Clock In Location', 'employee-scheduler' ),
			'desc' => __( 'If "Record Geolocation" is enabled on the settings page, the staff member\'s approximate location will be automatically recorded', 'employee-scheduler' ),
			'id'   => $prefix . 'location_clock_in',
			'type' => 'text',
			'attributes'  => array(
				'readonly' => 'readonly',
				'disabled' => 'disabled',
			),
		) );

		$shift_meta->add_field( array(
			'name' => __( 'Clock Out Location', 'employee-scheduler' ),
			'desc' => __( 'If "Record Geolocation" is enabled on the settings page, the staff member\'s approximate location will be automatically recorded', 'employee-scheduler' ),
			'id'   => $prefix . 'location_clock_out',
			'type' => 'text',
			'attributes'  => array(
				'readonly' => 'readonly',
				'disabled' => 'disabled',
			),
		) );

		$shift_meta->add_field( array(
			// @todo - update run_that_action_last, or use a callback here?
			'name' => __( 'Notify Staff', 'employee-scheduler' ),
			'desc' => __( 'If checked, the assigned staff member will receive an email notification when you create or edit this shift.', 'employee-scheduler' ),
			'id'   => $prefix . 'notify_employee',
			'type' => 'checkbox',
		) );

		$shift_meta->add_field( array(
			'name' => __( 'Internal Note', 'employee-scheduler' ),
			'desc' => __( 'This will only be seen by site admins and managers', 'employee-scheduler' ),
			'id'   => $prefix . 'admin_note',
			'type' => 'textarea_small',
		) );

		// Shift Notes metabox
		$shift_notes = new_cmb2_box( array(
			'id'            => 'shift_notes',
			'title'         => __( 'Shift Notes', 'employee-scheduler' ),
			'object_types'  => array( 'shift', ),
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names'    => true,
		) );

		$shift_notes_group = $shift_notes->add_field( array(
			'id'          => $prefix . 'shift_notes',
			'type'        => 'group',
			'description' => __( 'Staff can leave notes on their shifts.  They can use these notes to report incorrect clock in/out times, or whatever information they need.', 'employee-scheduler' ),
			'options'     => array(
				'group_title'   => __( 'Note {#}', 'employee-scheduler' ),
				'add_button'    => __( 'Add Another Note', 'employee-scheduler' ),
				'remove_button' => __( 'Remove Note', 'employee-scheduler' ),
			),
		) );

		$shift_notes->add_group_field( $shift_notes_group, array(
			'name' => __( 'Note Date', 'employee-scheduler' ),
			'id'   => 'notedate',
			'type' => 'text_date_timestamp',
		) );

		$shift_notes->add_group_field( $shift_notes_group, array(
			'name' => __( 'Note Content', 'employee-scheduler' ),
			'id'   => 'notetext',
			'type' => 'textarea_small',
		) );

		$shift_notes->add_group_field( $shift_notes_group, array(
			'name' => __( 'Resolved', 'employee-scheduler' ),
			'desc' => __( 'Check this box if you have resolved the issue described in the note', 'employee-scheduler' ),
			'id'   => 'resolved',
			'type' => 'checkbox',
		) );

		// Expense Details Metabox
		$expense_meta = new_cmb2_box( array(
			'id'            => 'expense_metabox',
			'title'         => __( 'Expense Details', 'employee-scheduler' ),
			'object_types'  => array( 'expense', ),
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names'    => true,
		) );

		$expense_meta->add_field( array(
			'name' => __( 'Date', 'employee-scheduler' ),
			'id'   => $prefix . 'date',
			'type' => 'text_date_timestamp',
		) );

		$expense_meta->add_field( array(
			'name' => __( 'Amount (miles or currency)', 'employee-scheduler' ),
			'desc' => __( 'Numbers only, no currency symbol', 'employee-scheduler' ),
			'id'   => $prefix . 'amount',
			'type' => 'text_small',
			'attributes' => array(
				'type' => 'number',
                'step' => '.01',
                'min' => '0'
			),
		) );


	}

	/**
     * Calculate the worked duration of a shift and save it to a hidden meta field
     *
     * @since 2.1.0
     *
	 * @param $field_args
	 * @param $field
	 *
	 * @return string
	 */
	public function calculate_worked_duration( $field_args, $field ) {
		$shift_id = $field['render_row_cb'][0]->data_to_save['post_ID'];
		$duration = $this->helper->get_shift_duration( $shift_id, 'worked', 'hours' );
		return $duration;
	}

	/**
     * Calculate the scheduled duration of a shift and save it to a hidden meta field.
     *
	 * @param $field_args
	 * @param $field
	 *
	 * @return string
	 */
	public function calculate_scheduled_duration( $field_args, $field ) {
		$shift_id = $field['render_row_cb'][0]->data_to_save['post_ID'];
		$duration = $this->helper->get_shift_duration( $shift_id, 'scheduled', 'hours' );
		return $duration;
	}


	/**
	 * Display the shift's scheduled duration
	 *
	 * @param $field_args
	 * @param $field
	 */
	public function display_scheduled_duration( $field_args, $field ) {
		// @todo - make JS to update this dynamically
		$shift_id = $field->object_id;
		$scheduled = get_post_meta( $shift_id, '_shiftee_scheduled_duration', true );
		if( $scheduled && '' !== $scheduled ) {
			?>
			<p><?php printf( __( 'Scheduled duration: %s hours', 'employee-scheduler' ), $scheduled ); ?></p>
			<?php
		}
	}

	/**
	 * Display the shift's worked duration
	 *
	 * @param $field_args
	 * @param $field
	 */
	public function display_worked_duration( $field_args, $field ) {
		// @todo - make JS to update this dynamically
		$shift_id = $field->object_id;
		$worked = get_post_meta( $shift_id, '_shiftee_worked_duration', true );
		if( $worked && '' !== $worked ) {
			?>
			<p><?php printf( __( 'Worked duration: %s hours', 'employee-scheduler' ), $worked ); ?></p>
			<?php
		}
	}

	/**
     * Calculate the employee's pay for a shift
     *
     * @since 2.1.0
     *
	 * @param $field_args
	 * @param $field
	 *
	 * @return mixed|void
	 */
	public function calculate_wage( $field_args, $field ) {
		// @todo - make JS to update this dynamically
		$shift_id = $field->object_id;
		$wage = $this->helper->calculate_shift_wage( $shift_id );
		return $wage;
	}

	/**
     * Filter CMB2's date and time picker options to use user's settings
     *
	 * @param $l10n
	 *
	 * @return mixed
	 */
	public function cmb2_datetimepicker_options( $l10n ) {

	    $options = $this->helper->get_datetimepicker_options();

	    if( isset( $options['date_format'] ) ) {
	        $l10n['defaults']['date_picker']['dateFormat'] = $options['date_format'];
        }

	    if( isset( $options['first_day_of_week'] ) ) {
		    $l10n['defaults']['date_picker']['firstDay'] = $options['first_day_of_week'];
	    }

        if( isset( $options['time_format'] ) ) {
            $l10n['defaults']['time_picker']['timeFormat'] = $options['time_format'];
        }

	    return $l10n;
    }

	/**
	 * Add custom fields to taxonomies.
	 *
	 * @see Tax_Meta_Class
	 * @link http://en.bainternet.info/wordpress-taxonomies-extra-fields-the-easy-way/
	 */
	public function define_taxonomy_meta() {

		$status_config = array(
			'id' => 'status_meta_box',
			'title' => 'Shift Status Details',
			'pages' => array( 'shift_status' ),
			'context' => 'normal',
			'fields' => array(),
			'local_images' => false,
			'use_with_theme' => false
		);

		$status_meta = new Tax_Meta_Class( $status_config );

		$status_meta->addColor( 'status_color',array( 'name'=> 'Shift Status Color ' ) );

		$status_meta->Finish();

		$loc_config = array(
			'id' => 'location_meta_box',
			'title' => 'Location Details',
			'pages' => array( 'location' ),
			'context' => 'normal',
			'fields' => array(),
			'local_images' => false,
			'use_with_theme' => false
		);

		$loc_meta = new Tax_Meta_Class( $loc_config );

		$loc_meta->addTextarea( 'location_address', array( 'name'=> 'Address' ) );

		$loc_meta->Finish();

	}

	/**
	 * Add columns to shift overview page.
	 *
	 * Change default columns on shift overview page to add columns for date and time.
	 *
	 * @since 1.0.0
	 *
	 * @param array $defaults  Default columns.
	 * @return array column list.
	 */
	public function shift_overview_columns_headers( $defaults ) {

		$defaults['scheduled'] = __( 'Scheduled For', 'employee-scheduler' );
		return $defaults;

	}

	public function shift_sortable_columns( $sortable_columns ) {
		$sortable_columns[ 'scheduled' ] = 'scheduled';
    }

	/**
	 * Populate shift overview columns.
	 *
	 * Add date and time to shift overview columns.
	 *
	 * @since 1.0.0
	 *
	 * @global object  $shift_metabox.
	 *
	 * @param string  Column name.
	 * @param int  Post ID.
	 */
	public function shift_overview_columns( $column_name, $post_ID ) {

	    if( 'scheduled' == $column_name ) {
		    echo $this->helper->show_shift_date_and_time( $post_ID );
        }
	}

	/**
	 * Send notification after shift is saved.
	 *
	 * Add the notification action now, with lowest priority so it runs after meta data has been saved.
	 *
	 * @since 1.0
	 *
	 * @see get_latest_priority()
	 */
	public function run_that_action_last() {
		add_action(
			'save_post',
			array( $this, 'notify_employee' ),
			$this->get_latest_priority( current_filter() ),
			2
		);

	}

	/**
	 * Find the last priority.
	 *
	 * The notify_employee function needs to run very last on save, so we need to find what priority will make it run last.
	 *
	 * @since 1.0
	 *
	 * @see notify_employee()
	 * @link http://wordpress.stackexchange.com/questions/116221/how-to-force-function-to-run-as-the-last-one-when-saving-the-post
	 *
	 * @param string @filter
	 * @return int Priority that will run last.
	 */
	private function get_latest_priority( $filter ) {

		if ( empty ( $GLOBALS['wp_filter'][ $filter ] ) )
			return PHP_INT_MAX;

		if( is_object( $GLOBALS['wp_filter'][ $filter ] ) ) {
			$last = key( array_slice( $GLOBALS['wp_filter'][ $filter ]->callbacks, -1, 1, TRUE ) );
		} else {
			$priorities = array_keys( $GLOBALS['wp_filter'][ $filter ] );
			$last       = end( $priorities );
		}

		if ( is_numeric( $last ) )
			return PHP_INT_MAX;

		return "$last-z";
	}

	/**
	 * Notify employee that shift has been created/updated.
	 *
	 * Admin can choose to have an email sent to the employee assigned to a shift when the shift is created or edited.
	 *
	 * @since 1.0.0
	 *
	 * @global shift_metabox WP Alchemy metabox containing shift metadata
	 *
	 * @param int  $post_id  The ID of the post the employee needs to be notified about.
	 */
	public function notify_employee( $post_id = null ) {

		if( null == $post_id ) {
			return;
		}

		if( is_admin() && 'trash' !== get_post_status( $post_id ) ) { // we only need to run this function if we're in the dashboard

			if( 'on' == get_post_meta( $post_id, '_shiftee_notify_employee', true ) ) {  // only send the email if the "notify employee" option is checked

				// get the employee id
				$employeeid = $this->helper->get_shift_connection( $post_id, 'employee', 'ID' );

				// send the email
				if( !isset( $employeeid ) ) {
					$error = __( 'We could not send a notification, because you did not select a staff member.  Click your back button and select a staff member for this shift, or uncheck the staff notification option.', 'employee-scheduler' );
					wp_die( $error );
				}
				if( isset( $employeeid ) ) {
					do_action( 'shiftee_notify_employee_about_shift', $post_id, $employeeid );
				}
			}
		}
	}

	/**
	 * Send employee notification.
	 *
	 * Send the email to the employee when the shift is saved.
	 *
	 * @since 1.0
	 *
	 * @param int $employeeid ID of the employee who will receive notification.
	 * @param int $postid The ID of the shift.
	 */
	public function send_notification_email( $postid, $employeeid ) {
		$options = $this->helper->shiftee_options();

		$employeeinfo = get_user_by( 'id', $employeeid );
		$employeeemail = $employeeinfo->user_email;

		$to = $employeeemail;

		$cc = apply_filters( 'shiftee_employee_shift_notification_cc', '' );

		$subject = esc_attr( $options['notification_subject'] );

		$message = '<p>' . __( 'You have been scheduled to work the following shift: ', 'employee-scheduler' ) . '</p>';
		if( '' !== get_post_meta( $postid, '_shiftee_shift_start', true ) ) {
			$message .= '<p><strong>' . __( 'Start: ', 'employee-scheduler' ) . '</strong>' . $this->helper->display_datetime( get_post_meta( $postid, '_shiftee_shift_start', true ) ) . '</p>';
		}
		if( '' !== get_post_meta( $postid, '_shiftee_shift_end', true ) ) {
			$message .= '<p><strong>' . __( 'End: ', 'employee-scheduler' ) . '</strong>' . $this->helper->display_datetime( get_post_meta( $postid, '_shiftee_shift_end', true ) ) . '</p>';
		}
		if( $this->helper->get_shift_connection( $postid, 'job', 'name' ) ) {
			$message .= '<p><strong>' . __( 'With the job: ', 'employee-scheduler' ) . '</strong>' . esc_attr( $this->helper->get_shift_connection( $postid, 'job', 'name' ) ) . '</p>';
		}

		$children = get_pages( array( 'child_of' => $postid, 'post_type' => 'shift' ) );
		if( count( $children ) !== 0 ) {
			$message .= '<p>' . __( 'This is a repeating shift.', 'employee-scheduler' ) . '</p>';
		}

		$content = get_post_field( 'post_content', $postid );
		if( isset( $content ) && !empty( $content ) ) {
			$message .= '<strong>' . __( 'Shift Details: ', 'employee-scheduler' ) . '</strong><br />' . $content;
		}

		$message .= '<p><strong>' . __( 'View this shift:', 'employee-scheduler' ) . '&nbsp;<a href="' . esc_url( get_the_permalink( $postid ) ) . '">' . esc_url( get_the_permalink( $postid ) ) . '</a>';

		$from = $options['notification_from_name'] . "<" . sanitize_email( $options['notification_from_email'] ) . ">";

		$email = new Shiftee_Email();
		$email->send_email( $from, $to, $cc, $subject, $message );
	}

	/**
	 * Email site admin when employee leaves a note on a shift.
	 *
	 * @since 2.0.0
	 *
	 * @param $shift
	 * @param $note
	 */
	public function employee_note_admin_notification( $shift, $note ) {

		if( isset( $this->options['admin_notify_note']) && 1 == $this->options['admin_notify_note'] ) {
			if( isset( $this->options['admin_notification_email'] ) ) {
				$to = sanitize_email( $this->options['admin_notification_email'] );
			} else {
				$to = sanitize_email( get_bloginfo( 'admin_email' ) );
			}

			$employee_id = $this->helper->get_shift_connection( $shift->ID, 'employee' );
			$cc = apply_filters( 'shiftee_admin_notification_cc', $employee_id );

			$start = get_post_meta( $shift->ID, '_shiftee_shift_start', true );
			$date = $this->helper->display_datetime( $start, 'date' );
			$employee_name = $this->helper->get_shift_connection( $shift->ID, 'employee', 'name' );
			$subject = $employee_name . " left a note on their shift on " . esc_html( $date );
			$message = '<p>' . $employee_name . " left the following note on their shift that is scheduled for " . $date . ':</p>';
			$message .= sanitize_text_field( $note );
			$from = $this->options['notification_from_name'] . "<" . sanitize_email( $this->options['notification_from_email'] ) . ">";

			$email = new Shiftee_Email();
			$email->send_email( $from, $to, $cc, $subject, $message );
		}
	}

	/**
	 * Email admin when employee clocks out
	 *
	 * @param $shift
	 */
	public function clock_out_notification( $shift ) {
		if( '1' == $this->options['admin_notify_clockout'] ) {

			$employee_id = $this->helper->get_shift_connection( $shift->ID, 'employee' );
			$cc = apply_filters( 'shiftee_admin_notification_cc', $employee_id );

			$from = $this->options['notification_from_name'] . "<" . sanitize_email( $this->options['notification_from_email'] ) . ">";

			if( isset( $this->options['admin_notification_email'] ) ) {
				$to = sanitize_email( $this->options['admin_notification_email'] );
			} else {
				$to = sanitize_email( get_bloginfo( 'admin_email' ) );
			}

			$employee_name = $this->helper->get_shift_connection( $shift->ID, 'employee', 'name' );

			$subject = sprintf( __( '%s has just clocked out', 'employee-scheduler' ), $employee_name );

			$message = '<p>' . sprintf( __( '%s has just clocked out', 'employee-scheduler' ), $employee_name ) . '</p>';
			$message .= '<p><strong>' . __( 'Scheduled hours', 'employee-scheduler' ) . ': </strong>' . esc_html( $this->helper->show_shift_date_and_time( $shift->ID, 'scheduled' ) ) . '</p>';
			$message .= '<p><strong>' . __( 'Worked hours', 'employee-scheduler' ) . ': </strong>' . esc_html( $this->helper->show_shift_date_and_time( $shift->ID, 'worked' ) ) . '</p>';
			$message .= '<p><a href="' . esc_url( get_the_permalink( $shift->ID ) ) . '">' . __( 'View Shift', 'employee-scheduler' ) . '</a></p>';
			$message .= '<p><a href="' . esc_url( get_edit_post_link( $shift->ID ) ) . '">' . __( 'Edit Shift', 'employee-scheduler' ) . '</a></p>';

			$email = new Shiftee_Email();
			$email->send_email( $from, $to, $cc, $subject, $message );
		}
	}

	/**
     * When an employee clocks out, calculate the wage for the shift
     *
     * @since 2.1.0
     *
	 * @param $shift
	 */
	public function calculate_wage_on_clock_out( $shift ) {

	    $wage = $this->helper->calculate_shift_wage( $shift );

	    update_post_meta( $shift, '_shiftee_wage', $wage );

    }

	/**
     * When an employee clocks out, calculate the worked duration
     *
     * @since 2.1.0
     *
	 * @param $shift
	 */
	public function calculate_duration_on_clock_out( $shift ) {

		$duration = $this->helper->get_shift_duration( $shift, 'worked', 'hours' );

		update_post_meta( $shift, '_shiftee_worked_duration', $duration );

    }


	/**
	 * Display a sidebar on Shiftee admin pages
	 */
	public function admin_sidebar() {
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/sidebar.php';
	}

	/**
	 * When employee saves extra shift, notify admin.
	 *
	 * @param $shift_id
	 * @param $employee
	 */
	public function notify_admin_extra_shift( $shift_id, $employee ) {
		if( isset( $this->options['extra_shift_approval'] ) && '1' == $this->options['extra_shift_approval'] ) {

			// email notification to admin
			$from = $this->options['notification_from_name'] . " <" . $this->options['notification_from_email'] . ">";

			$to = $this->options['admin_notification_email'];

			$cc = apply_filters( 'shiftee_admin_notification_cc', $employee->ID );

			$subject = sprintf( __( 'Extra shift by %s is pending your approval', 'employee-scheduler' ), esc_attr( $employee->display_name ) );
			$message = '
			<p>' . __( 'There is a new extra shift awaiting your approval', 'employee-scheduler' ) . '</p>
			<p><strong>' . __( 'Shift details' ) . '</strong>
				<ul>
					<li><strong>' . __( 'Staff:', 'employee-scheduler' ) . '</strong> ' . esc_attr( $employee->display_name ) . '</li>
					<li><strong>' . __( 'Date:', 'employee-scheduler' ) . '</strong> ' . esc_html( $this->helper->show_shift_date_and_time( $shift_id, 'worked' ) ) . '</li>
					<li><strong>' . __( 'Duration:', 'employee-scheduler' ) . '</strong> ' . esc_html( get_post_meta( $shift_id, '_shiftee_worked_duration', true )) . '</li>';
			if( isset( $_POST['description'] ) && '' !== $_POST['description'] ) {
				$message .= '
						<li><strong>' . __( 'Description:', 'employee-scheduler' ) . '</strong> ' . sanitize_text_field( $_POST['description'] ) . '</li>
						';
			}
			$message .=
				'</ul>
			</p>
			<p><a href="' . esc_url( get_the_permalink( $shift_id ) ) . '">' . __( 'View this shift', 'employee-scheduler' ) . '</a></p>
			<p><a href="' . esc_url( get_edit_post_link( $shift_id ) ) . '">' . __( 'Edit this shift', 'employee-scheduler' ) . '</a></p>
			<p>' . __( 'To approve this shift, edit it and change the shift status to "worked."  If you do not approve this shift, edit it and change the shift status to "not approved."') . '</p>
			<p><a href="' . esc_url( admin_url( 'edit.php?shift_status=pending-approval&post_type=shift' ) ) . '">' . __( 'View all extra shifts awaiting approval', 'employee-scheduler' ) . '</a></p>';

			$email = new Shiftee_Email();
			$email->send_email( $from, $to, $cc, $subject, $message );
		}
	}

	/**
	 * Show the sidebar on the Shiftee Basic admin pages
     *
     * @since 2.0.0
	 */
	public function show_sidebar() { ?>
		<aside id="shiftee-admin-sidebar">
			<img src="<?php echo plugin_dir_url( __FILE__ ); ?>partials/images/logo.png">
			<?php ob_start();
			include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/sidebar.php';

			echo apply_filters( 'shiftee_admin_sidebar', ob_get_clean() ); ?>

            <h3><?php _e( 'Need customization?', 'employee-scheduler' ); ?></h3>

            <p><?php _e( 'If Shiftee doesn\'t do exactly what you need, our developers are on hand to customize it just for you!  Just let us know what you need!', 'employee-scheduler' ); ?></p>

            <a class="button button-primary" href="https://shiftee.co/downloads/custom-development/"><?php _e( 'Purchase Custom Development', 'employee-scheduler' ); ?></a>

			<!-- Begin MailChimp Signup Form -->
			<div id="mc_embed_signup">
				<h3><?php _e( 'Stay in the loop!', 'employee-scheduler' ); ?></h3>
				<p><?php _e( 'Sign up for our mailing list to be the first to know about new features and add-ons. We’ll even send you a few discounts!', 'employee-scheduler' ); ?></p>
				<form action="//shiftee.us14.list-manage.com/subscribe/post?u=55314f9528b163798058954ef&amp;id=976bd42bdd" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
					<div id="mc_embed_signup_scroll">
						<label for="mce-EMAIL">Subscribe to our mailing list</label>
						<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="email address" required>
						<div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_55314f9528b163798058954ef_976bd42bdd" tabindex="-1" value=""></div>
						<div class="clear"><p><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></p></div>
					</div>
				</form>
			</div>

			<!--End mc_embed_signup-->
		</aside>
	<?php }

}
