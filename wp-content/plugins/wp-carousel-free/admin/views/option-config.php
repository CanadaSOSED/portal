<?php if ( ! defined( 'ABSPATH' ) ) {
	die; } // Cannot access directly.

//
// Set a unique slug-like ID
//
$prefix = 'sp_wpcp_settings';

//
// Create options
//
SP_WPCF::createOptions(
	$prefix,
	array(
		'menu_title'         => __( 'Settings', 'wp-carousel-free' ),
		'menu_slug'          => 'wpcp_settings',
		'menu_parent'        => 'edit.php?post_type=sp_wp_carousel',
		'menu_type'          => 'submenu',
		'ajax_save'          => true,
		'save_defaults'      => true,
		'show_reset_all'     => true,
		'framework_title'    => __( 'WordPress Carousel', 'wp-carousel-free' ),
		'framework_class'    => 'sp-wpcp-options',
		'theme'              => 'light',
		// menu extras.
		'show_bar_menu'      => false,
		'show_sub_menu'      => false,
		'show_network_menu'  => false,
		'show_in_customizer' => false,
		'show_search'        => false,
		// 'show_reset_all'     => true,
		'show_reset_section' => true,
		'show_all_options'   => false,
	)
);

//
// Create a section
//
SP_WPCF::createSection(
	$prefix,
	array(
		'title'  => 'Advanced Settings',
		'icon'   => 'fa fa-cogs',
		'fields' => array(
			array(
				'id'       => 'wpcf_delete_all_data',
				'type'     => 'checkbox',
				'title'    => __( 'Remove Data when Delete', 'wp-carousel-free' ),
				'subtitle' => __( 'Check to remove plugin\'s data when plugin is uninstalled or deleted.', 'wp-carousel-free' ),
				'default'  => false,
			),
			array(
				'type'    => 'subheading',
				'content' => __( 'Enqueue or Dequeue CSS', 'wp-carousel-free' ),
			),
			array(
				'id'         => 'wpcp_enqueue_slick_css',
				'type'       => 'switcher',
				'title'      => __( 'Slick CSS', 'wp-carousel-free' ),
				'subtitle'   => __( 'Enqueue/Dequeue slick CSS.', 'wp-carousel-free' ),
				'text_on'    => __( 'Enqueue', 'wp-carousel-free' ),
				'text_off'   => __( 'Dequeue', 'wp-carousel-free' ),
				'text_width' => 95,
				'default'    => true,
			),
			array(
				'id'         => 'wpcp_enqueue_fa_css',
				'type'       => 'switcher',
				'title'      => __( 'Font Awesome CSS', 'wp-carousel-free' ),
				'subtitle'   => __( 'Enqueue/Dequeue font awesome CSS.', 'wp-carousel-free' ),
				'text_on'    => __( 'Enqueue', 'wp-carousel-free' ),
				'text_off'   => __( 'Dequeue', 'wp-carousel-free' ),
				'text_width' => 95,
				'default'    => true,
			),
			array(
				'type'    => 'subheading',
				'content' => __( 'Enqueue or Dequeue JS', 'wp-carousel-free' ),
			),
			array(
				'id'         => 'wpcp_slick_js',
				'type'       => 'switcher',
				'title'      => __( 'Slick JS', 'wp-carousel-free' ),
				'subtitle'   => __( 'Enqueue/Dequeue slick JS.', 'wp-carousel-free' ),
				'text_on'    => __( 'Enqueue', 'wp-carousel-free' ),
				'text_off'   => __( 'Dequeue', 'wp-carousel-free' ),
				'text_width' => 95,
				'default'    => true,
			),
		),
	)
);

//
// Custom CSS Fields
//
SP_WPCF::createSection(
	$prefix,
	array(
		'id'     => 'custom_css_section',
		'title'  => __( 'Custom CSS', 'wp-carousel-free' ),
		'icon'   => 'fa fa-css3',
		'fields' => array(
			array(
				'id'       => 'wpcp_custom_css',
				'type'     => 'code_editor',
				'title'    => __( 'Custom CSS', 'wp-carousel-free' ),
				'subtitle' => __( 'Write your custom css.', 'wp-carousel-free' ),
				'settings' => array(
					'mode'  => 'css',
					'theme' => 'monokai',
				),
			),
		),
	)
);


