<?php if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access pages directly.
// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// FRAMEWORK SETTINGS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$settings = array(
	'menu_title'      => __( 'Settings', 'wp-carousel-free' ),
	'menu_parent'     => 'edit.php?post_type=sp_wp_carousel',
	'menu_type'       => 'submenu', // menu, submenu, options, theme, etc.
	'menu_slug'       => 'wpcp_settings',
	'ajax_save'       => true,
	'show_reset_all'  => false,
	'framework_title' => __( 'WordPress Carousel', 'wp-carousel-free' ),
);

// ===============================================================================================
// -----------------------------------------------------------------------------------------------
// FRAMEWORK OPTIONS
// -----------------------------------------------------------------------------------------------
// ===============================================================================================
$options = array();

// ----------------------------------------
// a option section for options overview  -
// ----------------------------------------
$options[] = array(
	'name'   => 'general_settings',
	'title'  => __( 'Advanced Settings', 'wp-carousel-free' ),
	'icon'   => 'fa fa-cogs',

	// Begin fields.
	'fields' => array(
		array(
			'type'    => 'subheading',
			'content' => __( 'Enqueue or Dequeue CSS', 'wp-carousel-free' ),
		),
		array(
			'id'      => 'wpcp_dequeue_slick_css',
			'type'    => 'switcher',
			'title'   => __( 'Slick CSS', 'wp-carousel-free' ),
			'desc'    => __( 'On/off the switch to enqueue/dequeue slick CSS.', 'wp-carousel-free' ),
			'default' => true,
		),
		array(
			'id'      => 'wpcp_dequeue_fa_css',
			'type'    => 'switcher',
			'title'   => __( 'Font Awesome CSS', 'wp-carousel-free' ),
			'desc'    => __( 'On/off the switch to enqueue/dequeue font awesome CSS.', 'wp-carousel-free' ),
			'default' => true,
		),
	), // End fields.
);

// ------------------------------
// Custom CSS                   -
// ------------------------------
$options[] = array(
	'name'   => 'custom_css_section',
	'title'  => __( 'Custom CSS', 'wp-carousel-free' ),
	'icon'   => 'fa fa-css3',
	'fields' => array(

		array(
			'id'    => 'wpcp_custom_css',
			'type'  => 'textarea',
			'title' => __( 'Custom CSS', 'wp-carousel-free' ),
			'desc'  => __( 'Type your css.', 'wp-carousel-free' ),
		),

	),
);

SP_WPCP_Framework::instance( $settings, $options );
