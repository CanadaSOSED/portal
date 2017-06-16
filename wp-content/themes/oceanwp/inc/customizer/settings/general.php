<?php
/**
 * General Customizer Options
 *
 * @package OceanWP WordPress theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'OceanWP_General_Customizer' ) ) :

	class OceanWP_General_Customizer {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {

			add_action( 'customize_register', 	array( $this, 'customizer_options' ) );
			add_filter( 'ocean_head_css', 		array( $this, 'page_header_overlay' ) );
			add_filter( 'ocean_head_css', 		array( $this, 'head_css' ) );

		}

		/**
		 * Customizer options
		 *
		 * @since 1.0.0
		 */
		public function customizer_options( $wp_customize ) {

			/**
			 * Panel
			 */
			$panel = 'ocean_general_panel';
			$wp_customize->add_panel( $panel , array(
				'title' 			=> esc_html__( 'General Options', 'oceanwp' ),
				'priority' 			=> 210,
			) );

			/**
			 * Section
			 */
			$wp_customize->add_section( 'ocean_general_styling' , array(
				'title' 			=> esc_html__( 'General Styling', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**
			 * Primary Color
			 */
			$wp_customize->add_setting( 'ocean_primary_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#13aff0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_primary_color', array(
				'label'	   				=> esc_html__( 'Primary Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_styling',
				'settings' 				=> 'ocean_primary_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Hover Primary Color
			 */
			$wp_customize->add_setting( 'ocean_hover_primary_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#0b7cac',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_hover_primary_color', array(
				'label'	   				=> esc_html__( 'Hover Primary Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_styling',
				'settings' 				=> 'ocean_hover_primary_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Main Border Color
			 */
			$wp_customize->add_setting( 'ocean_main_border_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#e9e9e9',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_main_border_color', array(
				'label'	   				=> esc_html__( 'Main Border Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_styling',
				'settings' 				=> 'ocean_main_border_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Heading Site Background
			 */
			$wp_customize->add_setting( 'ocean_site_background_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_site_background_heading', array(
				'label'    				=> esc_html__( 'Site Background', 'oceanwp' ),
				'section'  				=> 'ocean_general_styling',
				'priority' 				=> 10,
			) ) );

			/**
			 * Site Background
			 */
			$wp_customize->add_setting( 'ocean_background_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_background_color', array(
				'label'	   				=> esc_html__( 'Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_styling',
				'settings' 				=> 'ocean_background_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_hasnt_boxed_layout',
			) ) );

			/**
			 * Site Background Image
			 */
			$wp_customize->add_setting( 'ocean_background_image', array(
				'sanitize_callback' 	=> 'oceanwp_sanitize_image',
			) );

			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ocean_background_image', array(
				'label'	   				=> esc_html__( 'Background Image', 'oceanwp' ),
				'section'  				=> 'ocean_general_styling',
				'settings' 				=> 'ocean_background_image',
				'priority' 				=> 10,
			) ) );

			/**
			 * Site Background Image Position
			 */
			$wp_customize->add_setting( 'ocean_background_image_position', array(
				'transport' 			=> 'postMessage',
				'default' 				=> 'initial',
				'sanitize_callback' 	=> 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_background_image_position', array(
				'label'	   				=> esc_html__( 'Position', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_general_styling',
				'settings' 				=> 'ocean_background_image_position',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_background_image',
				'choices' 				=> array(
					'initial' 			=> esc_html__( 'Default', 'oceanwp' ),
					'top left' 			=> esc_html__( 'Top Left', 'oceanwp' ),
					'top center' 		=> esc_html__( 'Top Center', 'oceanwp' ),
					'top right'  		=> esc_html__( 'Top Right', 'oceanwp' ),
					'center left' 		=> esc_html__( 'Center Left', 'oceanwp' ),
					'center center' 	=> esc_html__( 'Center Center', 'oceanwp' ),
					'center right' 		=> esc_html__( 'Center Right', 'oceanwp' ),
					'bottom left' 		=> esc_html__( 'Bottom Left', 'oceanwp' ),
					'bottom center' 	=> esc_html__( 'Bottom Center', 'oceanwp' ),
					'bottom right' 		=> esc_html__( 'Bottom Right', 'oceanwp' ),
				),
			) ) );

			/**
			 * Site Background Image Attachment
			 */
			$wp_customize->add_setting( 'ocean_background_image_attachment', array(
				'transport' 			=> 'postMessage',
				'default' 				=> 'initial',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_background_image_attachment', array(
				'label'	   				=> esc_html__( 'Attachment', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_general_styling',
				'settings' 				=> 'ocean_background_image_attachment',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_background_image',
				'choices' 				=> array(
					'initial' 	=> esc_html__( 'Default', 'oceanwp' ),
					'scroll' 	=> esc_html__( 'Scroll', 'oceanwp' ),
					'fixed' 	=> esc_html__( 'Fixed', 'oceanwp' ),
				),
			) ) );

			/**
			 * Site Background Image Repeat
			 */
			$wp_customize->add_setting( 'ocean_background_image_repeat', array(
				'transport' 			=> 'postMessage',
				'default' 				=> 'initial',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_background_image_repeat', array(
				'label'	   				=> esc_html__( 'Repeat', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_general_styling',
				'settings' 				=> 'ocean_background_image_repeat',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_background_image',
				'choices' 				=> array(
					'initial' 	=> esc_html__( 'Default', 'oceanwp' ),
					'no-repeat' => esc_html__( 'No-repeat', 'oceanwp' ),
					'repeat' 	=> esc_html__( 'Repeat', 'oceanwp' ),
					'repeat-x' 	=> esc_html__( 'Repeat-x', 'oceanwp' ),
					'repeat-y' 	=> esc_html__( 'Repeat-y', 'oceanwp' ),
				),
			) ) );

			/**
			 * Site Background Image Size
			 */
			$wp_customize->add_setting( 'ocean_background_image_size', array(
				'transport' 			=> 'postMessage',
				'default' 				=> 'initial',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_background_image_size', array(
				'label'	   				=> esc_html__( 'Size', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_general_styling',
				'settings' 				=> 'ocean_background_image_size',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_background_image',
				'choices' 				=> array(
					'initial' 	=> esc_html__( 'Default', 'oceanwp' ),
					'auto' 		=> esc_html__( 'Auto', 'oceanwp' ),
					'cover' 	=> esc_html__( 'Cover', 'oceanwp' ),
					'contain' 	=> esc_html__( 'Contain', 'oceanwp' ),
				),
			) ) );

			/**
			 * Heading Links Color
			 */
			$wp_customize->add_setting( 'ocean_links_color_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_links_color_heading', array(
				'label'    				=> esc_html__( 'Links Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_styling',
				'priority' 				=> 10,
			) ) );

			/**
			 * Links Color
			 */
			$wp_customize->add_setting( 'ocean_links_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#333333',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_links_color', array(
				'label'	   				=> esc_html__( 'Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_styling',
				'settings' 				=> 'ocean_links_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Links Color Hover
			 */
			$wp_customize->add_setting( 'ocean_links_color_hover', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#13aff0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_links_color_hover', array(
				'label'	   				=> esc_html__( 'Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_general_styling',
				'settings' 				=> 'ocean_links_color_hover',
				'priority' 				=> 10,
			) ) );

			/**
			 * Section
			 */
			$wp_customize->add_section( 'ocean_general_settings' , array(
				'title' 			=> esc_html__( 'General Settings', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**
			 * Main Layout Style
			 */
			$wp_customize->add_setting( 'ocean_main_layout_style', array(
				'default'           	=> 'wide',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Buttonset_Control( $wp_customize, 'ocean_main_layout_style', array(
				'label'	   				=> esc_html__( 'Layout Style', 'oceanwp' ),
				'section'  				=> 'ocean_general_settings',
				'settings' 				=> 'ocean_main_layout_style',
				'priority' 				=> 10,
				'choices' 				=> array(
					'wide'  			=> esc_html__( 'Wide', 'oceanwp' ),
					'boxed' 			=> esc_html__( 'Boxed', 'oceanwp' ),
				),
			) ) );

			/**
			 * Boxed Layout Drop-Shadow
			 */
			$wp_customize->add_setting( 'ocean_boxed_dropdshadow', array(
				'transport' 			=> 'postMessage',
				'default'           	=> true,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_boxed_dropdshadow', array(
				'label'	   				=> esc_html__( 'Boxed Layout Drop-Shadow', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> 'ocean_general_settings',
				'settings' 				=> 'ocean_boxed_dropdshadow',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_boxed_layout',
			) ) );

			/**
			 * Boxed Width
			 */
			$wp_customize->add_setting( 'ocean_boxed_width', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '1280',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_boxed_width', array(
				'label'	   				=> esc_html__( 'Boxed Width (px)', 'oceanwp' ),
				'section'  				=> 'ocean_general_settings',
				'settings' 				=> 'ocean_boxed_width',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_boxed_layout',
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 4000,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Boxed Outside Background
			 */
			$wp_customize->add_setting( 'ocean_boxed_outside_bg', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#e9e9e9',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_boxed_outside_bg', array(
				'label'	   				=> esc_html__( 'Outside Background', 'oceanwp' ),
				'section'  				=> 'ocean_general_settings',
				'settings' 				=> 'ocean_boxed_outside_bg',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_boxed_layout',
			) ) );

			/**
			 * Boxed Inner Background
			 */
			$wp_customize->add_setting( 'ocean_boxed_inner_bg', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_boxed_inner_bg', array(
				'label'	   				=> esc_html__( 'Inner Background', 'oceanwp' ),
				'section'  				=> 'ocean_general_settings',
				'settings' 				=> 'ocean_boxed_inner_bg',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_boxed_layout',
			) ) );

			/**
			 * Main Container Width
			 */
			$wp_customize->add_setting( 'ocean_main_container_width', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '1200',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_main_container_width', array(
				'label'	   				=> esc_html__( 'Main Container Width (px)', 'oceanwp' ),
				'section'  				=> 'ocean_general_settings',
				'settings' 				=> 'ocean_main_container_width',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_hasnt_boxed_layout',
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 4096,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Content Width
			 */
			$wp_customize->add_setting( 'ocean_left_container_width', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '72',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_left_container_width', array(
				'label'	   				=> esc_html__( 'Content Width (%)', 'oceanwp' ),
				'section'  				=> 'ocean_general_settings',
				'settings' 				=> 'ocean_left_container_width',
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 100,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Sidebar Width
			 */
			$wp_customize->add_setting( 'ocean_sidebar_width', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '28',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_sidebar_width', array(
				'label'	   				=> esc_html__( 'Sidebar Width (%)', 'oceanwp' ),
				'section'  				=> 'ocean_general_settings',
				'settings' 				=> 'ocean_sidebar_width',
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 100,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Heading Pages
			 */
			$wp_customize->add_setting( 'ocean_pages_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_pages_heading', array(
				'label'    	=> esc_html__( 'Pages', 'oceanwp' ),
				'section'  	=> 'ocean_general_settings',
				'priority' 	=> 10,
			) ) );

			/**
			 * Pages
			 */
			$wp_customize->add_setting( 'ocean_page_single_layout', array(
				'default'           	=> 'right-sidebar',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Radio_Image_Control( $wp_customize, 'ocean_page_single_layout', array(
				'label'	   				=> esc_html__( 'Layout', 'oceanwp' ),
				'section'  				=> 'ocean_general_settings',
				'settings' 				=> 'ocean_page_single_layout',
				'priority' 				=> 10,
				'choices' 				=> array(
					'right-sidebar'  	=> OCEANWP_INC_DIR_URI . 'customizer/assets/img/rs.png',
					'left-sidebar' 		=> OCEANWP_INC_DIR_URI . 'customizer/assets/img/ls.png',
					'full-width'  		=> OCEANWP_INC_DIR_URI . 'customizer/assets/img/fw.png',
					'full-screen'  		=> OCEANWP_INC_DIR_URI . 'customizer/assets/img/fs.png',
				),
			) ) );

			/**
			 * Content Padding
			 */
			$wp_customize->add_setting( 'ocean_page_content_top_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '50',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_page_content_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '50',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_setting( 'ocean_page_content_tablet_top_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_page_content_tablet_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_setting( 'ocean_page_content_mobile_top_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_page_content_mobile_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Dimensions_Control( $wp_customize, 'ocean_page_content_padding', array(
				'label'	   				=> esc_html__( 'Content Padding (px)', 'oceanwp' ),
				'section'  				=> 'ocean_general_settings',				
				'settings'   => array(
		            'desktop_top' 		=> 'ocean_page_content_top_padding',
		            'desktop_bottom' 	=> 'ocean_page_content_bottom_padding',
		            'tablet_top' 		=> 'ocean_page_content_tablet_top_padding',
		            'tablet_bottom' 	=> 'ocean_page_content_tablet_bottom_padding',
		            'mobile_top' 		=> 'ocean_page_content_mobile_top_padding',
		            'mobile_bottom' 	=> 'ocean_page_content_mobile_bottom_padding',
				),
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 300,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Heading Search Result Page
			 */
			$wp_customize->add_setting( 'ocean_search_result_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_search_result_heading', array(
				'label'    	=> esc_html__( 'Search Result Page', 'oceanwp' ),
				'section'  	=> 'ocean_general_settings',
				'priority' 	=> 10,
			) ) );

			/**
			 * Search Page
			 */
			$wp_customize->add_setting( 'ocean_search_custom_sidebar', array(
				'default'           	=> true,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_search_custom_sidebar', array(
				'label'	   				=> esc_html__( 'Custom Sidebar', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> 'ocean_general_settings',
				'settings' 				=> 'ocean_search_custom_sidebar',
				'priority' 				=> 10,
			) ) );

			/**
			 * Search Page Layout
			 */
			$wp_customize->add_setting( 'ocean_search_layout', array(
				'default'           	=> 'right-sidebar',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Radio_Image_Control( $wp_customize, 'ocean_search_layout', array(
				'label'	   				=> esc_html__( 'Layout', 'oceanwp' ),
				'section'  				=> 'ocean_general_settings',
				'settings' 				=> 'ocean_search_layout',
				'priority' 				=> 10,
				'choices' 				=> array(
					'right-sidebar'  	=> OCEANWP_INC_DIR_URI . 'customizer/assets/img/rs.png',
					'left-sidebar' 		=> OCEANWP_INC_DIR_URI . 'customizer/assets/img/ls.png',
					'full-width'  		=> OCEANWP_INC_DIR_URI . 'customizer/assets/img/fw.png',
					'full-screen'  		=> OCEANWP_INC_DIR_URI . 'customizer/assets/img/fs.png',
				),
			) ) );

			/**
			 * Heading 404 Error Page
			 */
			$wp_customize->add_setting( 'ocean_error_page_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_error_page_heading', array(
				'label'    	=> esc_html__( '404 Error Page', 'oceanwp' ),
				'section'  	=> 'ocean_general_settings',
				'priority' 	=> 10,
			) ) );

			/**
			 * Elementor Templates
			 */
		    if ( class_exists( 'Elementor\Plugin' ) ) {

				$wp_customize->add_setting( 'ocean_error_page_elementor_templates', array(
					'default'           	=> '0',
					'sanitize_callback' 	=> 'oceanwp_sanitize_dropdown_pages',
				) );

				$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_error_page_elementor_templates', array(
					'label'	   				=> esc_html__( 'Elementor Templates', 'oceanwp' ),
				    'description' 			=> esc_html__( 'Select your template created in Elementor > My Library.', 'oceanwp' ),
					'type' 					=> 'select',
					'section'  				=> 'ocean_general_settings',
					'settings' 				=> 'ocean_error_page_elementor_templates',
					'priority' 				=> 10,
					'choices' 				=> $this->helpers( 'elementor' ),
				) ) );

			}

			/**
			 * Page ID
			 */
			else {

				$wp_customize->add_setting( 'ocean_error_page_id', array(
					'default' 				=> '',
					'sanitize_callback' 	=> 'oceanwp_sanitize_dropdown_pages',
				) );

				$wp_customize->add_control( new OceanWP_Customizer_Dropdown_Pages( $wp_customize, 'ocean_error_page_id', array(
					'label'	   				=> esc_html__( 'Page ID', 'oceanwp' ),
					'description'	   		=> esc_html__( 'Choose a page where the content will be displayed in the popup.', 'oceanwp' ),
					'section'  				=> 'ocean_general_settings',
					'settings' 				=> 'ocean_error_page_id',
					'priority' 				=> 10,
				) ) );

			}

			/**
			 * Section
			 */
			$wp_customize->add_section( 'ocean_general_page_header' , array(
				'title' 			=> esc_html__( 'Page Title', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**Page Title Bottom Visibility
			 */
			$wp_customize->add_setting( 'ocean_page_header_visibility', array(
				'transport' 			=> 'postMessage',
				'default'           	=> 'all-devices',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_page_header_visibility', array(
				'label'	   				=> esc_html__( 'Visibility', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_page_header_visibility',
				'priority' 				=> 10,
				'choices' 				=> array(
					'all-devices' 			=> esc_html__( 'Show On All Devices', 'oceanwp' ),
					'hide-tablet' 			=> esc_html__( 'Hide On Tablet', 'oceanwp' ),
					'hide-mobile' 			=> esc_html__( 'Hide On Mobile', 'oceanwp' ),
					'hide-tablet-mobile' 	=> esc_html__( 'Hide On Tablet & Mobile', 'oceanwp' ),
				),
			) ) );

			/**
			 * Page Title Style
			 */
			$wp_customize->add_setting( 'ocean_page_header_style', array(
				'default'           	=> '',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_page_header_style', array(
				'label'	   				=> esc_html__( 'Style', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_page_header_style',
				'priority' 				=> 10,
				'choices' 				=> array(
					'' 					=> esc_html__( 'Default','oceanwp' ),
					'centered' 			=> esc_html__( 'Centered', 'oceanwp' ),
					'centered-minimal' 	=> esc_html__( 'Centered Minimal', 'oceanwp' ),
					'background-image' 	=> esc_html__( 'Background Image', 'oceanwp' ),
					'hidden' 			=> esc_html__( 'Hidden', 'oceanwp' ),
				),
			) ) );

			/**
			 * Page Title Background Image
			 */
			$wp_customize->add_setting( 'ocean_page_header_bg_image', array(
				'sanitize_callback' 	=> 'oceanwp_sanitize_image',
			) );

			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ocean_page_header_bg_image', array(
				'label'	   				=> esc_html__( 'Image', 'oceanwp' ),
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_page_header_bg_image',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_bg_image_page_header',
			) ) );

			/**
			 * Page Title Background Image Position
			 */
			$wp_customize->add_setting( 'ocean_page_header_bg_image_position', array(
				'transport' 			=> 'postMessage',
				'default' 				=> 'top center',
				'sanitize_callback' 	=> 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_page_header_bg_image_position', array(
				'label'	   				=> esc_html__( 'Position', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_page_header_bg_image_position',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_bg_image_page_header',
				'choices' 				=> array(
					'initial' 			=> esc_html__( 'Default', 'oceanwp' ),
					'top left' 			=> esc_html__( 'Top Left', 'oceanwp' ),
					'top center' 		=> esc_html__( 'Top Center', 'oceanwp' ),
					'top right'  		=> esc_html__( 'Top Right', 'oceanwp' ),
					'center left' 		=> esc_html__( 'Center Left', 'oceanwp' ),
					'center center' 	=> esc_html__( 'Center Center', 'oceanwp' ),
					'center right' 		=> esc_html__( 'Center Right', 'oceanwp' ),
					'bottom left' 		=> esc_html__( 'Bottom Left', 'oceanwp' ),
					'bottom center' 	=> esc_html__( 'Bottom Center', 'oceanwp' ),
					'bottom right' 		=> esc_html__( 'Bottom Right', 'oceanwp' ),
				),
			) ) );

			/**
			 * Page Title Background Image Attachment
			 */
			$wp_customize->add_setting( 'ocean_page_header_bg_image_attachment', array(
				'transport' 			=> 'postMessage',
				'default' 				=> 'initial',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_page_header_bg_image_attachment', array(
				'label'	   				=> esc_html__( 'Attachment', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_page_header_bg_image_attachment',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_bg_image_page_header',
				'choices' 				=> array(
					'initial' 	=> esc_html__( 'Default', 'oceanwp' ),
					'scroll' 	=> esc_html__( 'Scroll', 'oceanwp' ),
					'fixed' 	=> esc_html__( 'Fixed', 'oceanwp' ),
				),
			) ) );

			/**
			 * Page Title Background Image Repeat
			 */
			$wp_customize->add_setting( 'ocean_page_header_bg_image_repeat', array(
				'transport' 			=> 'postMessage',
				'default' 				=> 'no-repeat',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_page_header_bg_image_repeat', array(
				'label'	   				=> esc_html__( 'Repeat', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_page_header_bg_image_repeat',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_bg_image_page_header',
				'choices' 				=> array(
					'initial' => esc_html__( 'Default', 'oceanwp' ),
					'no-repeat' => esc_html__( 'No-repeat', 'oceanwp' ),
					'repeat' 	=> esc_html__( 'Repeat', 'oceanwp' ),
					'repeat-x' 	=> esc_html__( 'Repeat-x', 'oceanwp' ),
					'repeat-y' 	=> esc_html__( 'Repeat-y', 'oceanwp' ),
				),
			) ) );

			/**
			 * Page Title Background Image Size
			 */
			$wp_customize->add_setting( 'ocean_page_header_bg_image_size', array(
				'transport' 			=> 'postMessage',
				'default' 				=> 'cover',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_page_header_bg_image_size', array(
				'label'	   				=> esc_html__( 'Size', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_page_header_bg_image_size',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_bg_image_page_header',
				'choices' 				=> array(
					'initial' 	=> esc_html__( 'Default', 'oceanwp' ),
					'auto' 		=> esc_html__( 'Auto', 'oceanwp' ),
					'cover' 	=> esc_html__( 'Cover', 'oceanwp' ),
					'contain' 	=> esc_html__( 'Contain', 'oceanwp' ),
				),
			) ) );

			/**
			 * Page Title Background Image Height
			 */
			$wp_customize->add_setting( 'ocean_page_header_bg_image_height', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '400',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_page_header_bg_image_height', array(
				'label'	   				=> esc_html__( 'Height (px)', 'oceanwp' ),
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_page_header_bg_image_height',
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 800,
			        'step'  => 1,
			    ),
				'active_callback' 		=> 'oceanwp_cac_has_bg_image_page_header',
			) ) );

			/**
			 * Page Title Background Image Overlay Opacity
			 */
			$wp_customize->add_setting( 'ocean_page_header_bg_image_overlay_opacity', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '0.5',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_page_header_bg_image_overlay_opacity', array(
				'label'	   				=> esc_html__( 'Overlay Opacity', 'oceanwp' ),
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_page_header_bg_image_overlay_opacity',
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 1,
			        'step'  => 0.1,
			    ),
				'active_callback' 		=> 'oceanwp_cac_has_bg_image_page_header',
			) ) );

			/**
			 * Page Title Background Image Overlay Color
			 */
			$wp_customize->add_setting( 'ocean_page_header_bg_image_overlay_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#000000',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_page_header_bg_image_overlay_color', array(
				'label'	   				=> esc_html__( 'Overlay Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_page_header_bg_image_overlay_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_bg_image_page_header',
			) ) );

			/**
			 * Page Title Padding
			 */
			$wp_customize->add_setting( 'ocean_page_header_top_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '34',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_page_header_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '34',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_setting( 'ocean_page_header_tablet_top_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_page_header_tablet_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_setting( 'ocean_page_header_mobile_top_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_page_header_mobile_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Dimensions_Control( $wp_customize, 'ocean_page_header_padding', array(
				'label'	   				=> esc_html__( 'Padding (px)', 'oceanwp' ),
				'section'  				=> 'ocean_general_page_header',				
				'settings'   => array(
		            'desktop_top' 		=> 'ocean_page_header_top_padding',
		            'desktop_bottom' 	=> 'ocean_page_header_bottom_padding',
		            'tablet_top' 		=> 'ocean_page_header_tablet_top_padding',
		            'tablet_bottom' 	=> 'ocean_page_header_tablet_bottom_padding',
		            'mobile_top' 		=> 'ocean_page_header_mobile_top_padding',
		            'mobile_bottom' 	=> 'ocean_page_header_mobile_bottom_padding',
				),
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 200,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Page Title Background Color
			 */
			$wp_customize->add_setting( 'ocean_page_header_background', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#f5f5f5',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_page_header_background', array(
				'label'	   				=> esc_html__( 'Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_page_header_background',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_hasnt_bg_image_page_header',
			) ) );

			/**
			 * Page Title Color
			 */
			$wp_customize->add_setting( 'ocean_page_header_title_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#333333',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_page_header_title_color', array(
				'label'	   				=> esc_html__( 'Text Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_page_header_title_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_hasnt_bg_image_page_header',
			) ) );

			/**
			 * Breadcrumbs Heading
			 */
			$wp_customize->add_setting( 'ocean_breadcrumbs_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_breadcrumbs_heading', array(
				'label'    				=> esc_html__( 'Breadcrumbs', 'oceanwp' ),
				'section'  				=> 'ocean_general_page_header',
				'priority' 				=> 10,
			) ) );

			/**
			 * Breadcrumbs
			 */
			$wp_customize->add_setting( 'ocean_breadcrumbs', array(
				'default'           	=> true,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_breadcrumbs', array(
				'label'	   				=> esc_html__( 'Breadcrumbs', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_breadcrumbs',
				'priority' 				=> 10,
			) ) );

			/**
			 * Breadcrumbs Position
			 */
			$wp_customize->add_setting( 'ocean_breadcrumbs_position', array(
				'default'           	=> '',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_breadcrumbs_position', array(
				'label'	   				=> esc_html__( 'Position', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_breadcrumbs_position',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_breadcrumbs',
				'choices' 				=> array(
					'' 					=> esc_html__( 'Absolute Right','oceanwp' ),
					'under-title' 		=> esc_html__( 'Under Title', 'oceanwp' ),
				),
			) ) );

			/**
			 * Breadcrumbs Text Color
			 */
			$wp_customize->add_setting( 'ocean_breadcrumbs_text_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#c6c6c6',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_breadcrumbs_text_color', array(
				'label'	   				=> esc_html__( 'Text Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_breadcrumbs_text_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_breadcrumbs',
			) ) );

			/**
			 * Breadcrumbs Separator Color
			 */
			$wp_customize->add_setting( 'ocean_breadcrumbs_seperator_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#c6c6c6',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_breadcrumbs_seperator_color', array(
				'label'	   				=> esc_html__( 'Separator Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_breadcrumbs_seperator_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_breadcrumbs',
			) ) );

			/**
			 * Breadcrumbs Link Color
			 */
			$wp_customize->add_setting( 'ocean_breadcrumbs_link_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#333333',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_breadcrumbs_link_color', array(
				'label'	   				=> esc_html__( 'Link Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_breadcrumbs_link_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_breadcrumbs',
			) ) );

			/**
			 * Breadcrumbs Link Color
			 */
			$wp_customize->add_setting( 'ocean_breadcrumbs_link_color_hover', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#13aff0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_breadcrumbs_link_color_hover', array(
				'label'	   				=> esc_html__( 'Link Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_general_page_header',
				'settings' 				=> 'ocean_breadcrumbs_link_color_hover',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_breadcrumbs',
			) ) );

			/**
			 * Section
			 */
			$wp_customize->add_section( 'ocean_general_scroll_top' , array(
				'title' 			=> esc_html__( 'Scroll To Top', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**
			 * Scroll To Top
			 */
			$wp_customize->add_setting( 'ocean_scroll_top', array(
				'default'           	=> true,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_scroll_top', array(
				'label'	   				=> esc_html__( 'Scroll Up Button', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> 'ocean_general_scroll_top',
				'settings' 				=> 'ocean_scroll_top',
				'priority' 				=> 10,
			) ) );

			/**
			 * Scroll Top Arrow
			 */
			$wp_customize->add_setting( 'ocean_scroll_top_arrow', array(
				'transport' 			=> 'postMessage',
				'default'           	=> 'fa fa-angle-up',
				'sanitize_callback' 	=> 'wp_filter_nohtml_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Icon_Select_Control( $wp_customize, 'ocean_scroll_top_arrow', array(
				'label'	   				=> esc_html__( 'Arrow Icon', 'oceanwp' ),
				'section'  				=> 'ocean_general_scroll_top',
				'settings' 				=> 'ocean_scroll_top_arrow',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_scrolltop',
			    'choices' 				=> oceanwp_get_awesome_icons( 'up_arrows' ),
			) ) );

			/**
			 * Scroll Top Size
			 */
			$wp_customize->add_setting( 'ocean_scroll_top_size', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '40',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_scroll_top_size', array(
				'label'	   				=> esc_html__( 'Button Size (px)', 'oceanwp' ),
				'section'  				=> 'ocean_general_scroll_top',
				'settings' 				=> 'ocean_scroll_top_size',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_scrolltop',
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 60,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Scroll Top Icon Size
			 */
			$wp_customize->add_setting( 'ocean_scroll_top_icon_size', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '18',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_scroll_top_icon_size', array(
				'label'	   				=> esc_html__( 'Icon Size (px)', 'oceanwp' ),
				'section'  				=> 'ocean_general_scroll_top',
				'settings' 				=> 'ocean_scroll_top_icon_size',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_scrolltop',
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 60,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Scroll Top Border Radius
			 */
			$wp_customize->add_setting( 'ocean_scroll_top_border_radius', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '2',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_scroll_top_border_radius', array(
				'label'	   				=> esc_html__( 'Border Radius (px)', 'oceanwp' ),
				'section'  				=> 'ocean_general_scroll_top',
				'settings' 				=> 'ocean_scroll_top_border_radius',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_scrolltop',
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 100,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Scroll Top Background Color
			 */
			$wp_customize->add_setting( 'ocean_scroll_top_bg', array(
				'transport' 			=> 'postMessage',
				'default'           	=> 'rgba(0,0,0,0.4)',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_scroll_top_bg', array(
				'label'	   				=> esc_html__( 'Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_scroll_top',
				'settings' 				=> 'ocean_scroll_top_bg',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_scrolltop',
			) ) );

			/**
			 * Scroll Top Background Hover Color
			 */
			$wp_customize->add_setting( 'ocean_scroll_top_bg_hover', array(
				'transport' 			=> 'postMessage',
				'default'           	=> 'rgba(0,0,0,0.8)',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_scroll_top_bg_hover', array(
				'label'	   				=> esc_html__( 'Background Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_general_scroll_top',
				'settings' 				=> 'ocean_scroll_top_bg_hover',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_scrolltop',
			) ) );

			/**
			 * Scroll Top Color
			 */
			$wp_customize->add_setting( 'ocean_scroll_top_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_scroll_top_color', array(
				'label'	   				=> esc_html__( 'Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_scroll_top',
				'settings' 				=> 'ocean_scroll_top_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_scrolltop',
			) ) );

			/**
			 * Scroll Top Hover Color
			 */
			$wp_customize->add_setting( 'ocean_scroll_top_color_hover', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_scroll_top_color_hover', array(
				'label'	   				=> esc_html__( 'Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_general_scroll_top',
				'settings' 				=> 'ocean_scroll_top_color_hover',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_scrolltop',
			) ) );

			/**
			 * Section
			 */
			$wp_customize->add_section( 'ocean_general_pagination' , array(
				'title' 			=> esc_html__( 'Pagination', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**
			 * Pagination Align
			 */
			$wp_customize->add_setting( 'ocean_pagination_align', array(
				'transport' 			=> 'postMessage',
				'default'           	=> 'right',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_pagination_align', array(
				'label'	   				=> esc_html__( 'Align', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_general_pagination',
				'settings' 				=> 'ocean_pagination_align',
				'priority' 				=> 10,
				'choices' 				=> array(
					'right' 	=> esc_html__( 'Right', 'oceanwp' ),
					'center' 	=> esc_html__( 'Center', 'oceanwp' ),
					'left' 		=> esc_html__( 'Left', 'oceanwp' ),
				),
			) ) );

			/**
			 * Pagination Font Size
			 */
			$wp_customize->add_setting( 'ocean_pagination_font_size', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '18',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_pagination_font_size', array(
				'label'	   				=> esc_html__( 'Font Size (px)', 'oceanwp' ),
				'section'  				=> 'ocean_general_pagination',
				'settings' 				=> 'ocean_pagination_font_size',
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 100,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Pagination Border Width
			 */
			$wp_customize->add_setting( 'ocean_pagination_border_width', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '1',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_pagination_border_width', array(
				'label'	   				=> esc_html__( 'Border Width (px)', 'oceanwp' ),
				'section'  				=> 'ocean_general_pagination',
				'settings' 				=> 'ocean_pagination_border_width',
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 20,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Pagination Background Color
			 */
			$wp_customize->add_setting( 'ocean_pagination_bg', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_pagination_bg', array(
				'label'	   				=> esc_html__( 'Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_pagination',
				'settings' 				=> 'ocean_pagination_bg',
				'priority' 				=> 10,
			) ) );

			/**
			 * Pagination Background Color Hover
			 */
			$wp_customize->add_setting( 'ocean_pagination_hover_bg', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#f8f8f8',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_pagination_hover_bg', array(
				'label'	   				=> esc_html__( 'Background Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_general_pagination',
				'settings' 				=> 'ocean_pagination_hover_bg',
				'priority' 				=> 10,
			) ) );

			/**
			 * Pagination Color
			 */
			$wp_customize->add_setting( 'ocean_pagination_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#555555',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_pagination_color', array(
				'label'	   				=> esc_html__( 'Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_pagination',
				'settings' 				=> 'ocean_pagination_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Pagination Color Hover
			 */
			$wp_customize->add_setting( 'ocean_pagination_hover_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#333333',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_pagination_hover_color', array(
				'label'	   				=> esc_html__( 'Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_general_pagination',
				'settings' 				=> 'ocean_pagination_hover_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Pagination Border Color
			 */
			$wp_customize->add_setting( 'ocean_pagination_border_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#e9e9e9',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_pagination_border_color', array(
				'label'	   				=> esc_html__( 'Border Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_pagination',
				'settings' 				=> 'ocean_pagination_border_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Pagination Border Color Hover
			 */
			$wp_customize->add_setting( 'ocean_pagination_border_hover_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#e9e9e9',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_pagination_border_hover_color', array(
				'label'	   				=> esc_html__( 'Border Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_general_pagination',
				'settings' 				=> 'ocean_pagination_border_hover_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Section
			 */
			$wp_customize->add_section( 'ocean_general_forms' , array(
				'title' 			=> esc_html__( 'Forms (Input - Textarea)', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**
			 * Forms Label Color
			 */
			$wp_customize->add_setting( 'ocean_label_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#929292',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_label_color', array(
				'label'	   				=> esc_html__( 'Label Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_forms',
				'settings' 				=> 'ocean_label_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Forms Padding
			 */
			$wp_customize->add_setting( 'ocean_input_top_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '6',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_input_right_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '12',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_input_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '6',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_input_left_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '12',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_setting( 'ocean_input_tablet_top_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_input_tablet_right_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_input_tablet_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_input_tablet_left_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_setting( 'ocean_input_mobile_top_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_input_mobile_right_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_input_mobile_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_input_mobile_left_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Dimensions_Control( $wp_customize, 'ocean_input_padding_dimensions', array(
				'label'	   				=> esc_html__( 'Padding (px)', 'oceanwp' ),
				'section'  				=> 'ocean_general_forms',				
				'settings'   => array(
		            'desktop_top' 		=> 'ocean_input_top_padding',
		            'desktop_right' 	=> 'ocean_input_right_padding',
		            'desktop_bottom' 	=> 'ocean_input_bottom_padding',
		            'desktop_left' 		=> 'ocean_input_left_padding',
		            'tablet_top' 		=> 'ocean_input_tablet_top_padding',
		            'tablet_right' 		=> 'ocean_input_tablet_right_padding',
		            'tablet_bottom' 	=> 'ocean_input_tablet_bottom_padding',
		            'tablet_left' 		=> 'ocean_input_tablet_left_padding',
		            'mobile_top' 		=> 'ocean_input_mobile_top_padding',
		            'mobile_right' 		=> 'ocean_input_mobile_right_padding',
		            'mobile_bottom' 	=> 'ocean_input_mobile_bottom_padding',
		            'mobile_left' 		=> 'ocean_input_mobile_left_padding',
				),
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 100,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Forms Font Size
			 */
			$wp_customize->add_setting( 'ocean_input_font_size', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '14',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_input_font_size', array(
				'label'	   				=> esc_html__( 'Font Size (px)', 'oceanwp' ),
				'section'  				=> 'ocean_general_forms',
				'settings' 				=> 'ocean_input_font_size',
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 100,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Forms Border Width
			 */
			$wp_customize->add_setting( 'ocean_input_top_border_width', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '1',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_input_right_border_width', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '1',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_input_bottom_border_width', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '1',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_input_left_border_width', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '1',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_setting( 'ocean_input_tablet_top_border_width', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_input_tablet_right_border_width', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_input_tablet_bottom_border_width', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_input_tablet_left_border_width', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_setting( 'ocean_input_mobile_top_border_width', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_input_mobile_right_border_width', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_input_mobile_bottom_border_width', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_input_mobile_left_border_width', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Dimensions_Control( $wp_customize, 'ocean_input_border_width_dimensions', array(
				'label'	   				=> esc_html__( 'Border Width (px)', 'oceanwp' ),
				'section'  				=> 'ocean_general_forms',				
				'settings'   => array(
		            'desktop_top' 		=> 'ocean_input_top_border_width',
		            'desktop_right' 	=> 'ocean_input_right_border_width',
		            'desktop_bottom' 	=> 'ocean_input_bottom_border_width',
		            'desktop_left' 		=> 'ocean_input_left_border_width',
		            'tablet_top' 		=> 'ocean_input_tablet_top_border_width',
		            'tablet_right' 		=> 'ocean_input_tablet_right_border_width',
		            'tablet_bottom' 	=> 'ocean_input_tablet_bottom_border_width',
		            'tablet_left' 		=> 'ocean_input_tablet_left_border_width',
		            'mobile_top' 		=> 'ocean_input_mobile_top_border_width',
		            'mobile_right' 		=> 'ocean_input_mobile_right_border_width',
		            'mobile_bottom' 	=> 'ocean_input_mobile_bottom_border_width',
		            'mobile_left' 		=> 'ocean_input_mobile_left_border_width',
				),
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 100,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Forms Border Radius
			 */
			$wp_customize->add_setting( 'ocean_input_border_radius', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '3',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_input_border_radius', array(
				'label'	   				=> esc_html__( 'Border Radius (px)', 'oceanwp' ),
				'section'  				=> 'ocean_general_forms',
				'settings' 				=> 'ocean_input_border_radius',
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 100,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Forms Border Color
			 */
			$wp_customize->add_setting( 'ocean_input_border_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#dddddd',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_input_border_color', array(
				'label'	   				=> esc_html__( 'Border Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_forms',
				'settings' 				=> 'ocean_input_border_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Forms Border Color Focus
			 */
			$wp_customize->add_setting( 'ocean_input_border_color_focus', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#bbbbbb',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_input_border_color_focus', array(
				'label'	   				=> esc_html__( 'Border Color: Focus', 'oceanwp' ),
				'section'  				=> 'ocean_general_forms',
				'settings' 				=> 'ocean_input_border_color_focus',
				'priority' 				=> 10,
			) ) );

			/**
			 * Forms Background Color
			 */
			$wp_customize->add_setting( 'ocean_input_background', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_input_background', array(
				'label'	   				=> esc_html__( 'Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_forms',
				'settings' 				=> 'ocean_input_background',
				'priority' 				=> 10,
			) ) );

			/**
			 * Forms Color
			 */
			$wp_customize->add_setting( 'ocean_input_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#333333',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_input_color', array(
				'label'	   				=> esc_html__( 'Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_forms',
				'settings' 				=> 'ocean_input_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Section
			 */
			$wp_customize->add_section( 'ocean_general_theme_button' , array(
				'title' 			=> esc_html__( 'Theme Button', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**
			 * Theme Button Padding
			 */
			$wp_customize->add_setting( 'ocean_theme_button_top_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '14',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_theme_button_right_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '20',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_theme_button_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '14',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_theme_button_left_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '20',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_setting( 'ocean_theme_button_tablet_top_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_theme_button_tablet_right_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_theme_button_tablet_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_theme_button_tablet_left_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_setting( 'ocean_theme_button_mobile_top_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_theme_button_mobile_right_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_theme_button_mobile_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_theme_button_mobile_left_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Dimensions_Control( $wp_customize, 'ocean_theme_button_padding_dimensions', array(
				'label'	   				=> esc_html__( 'Padding (px)', 'oceanwp' ),
				'section'  				=> 'ocean_general_theme_button',				
				'settings'   => array(
		            'desktop_top' 		=> 'ocean_theme_button_top_padding',
		            'desktop_right' 	=> 'ocean_theme_button_right_padding',
		            'desktop_bottom' 	=> 'ocean_theme_button_bottom_padding',
		            'desktop_left' 		=> 'ocean_theme_button_left_padding',
		            'tablet_top' 		=> 'ocean_theme_button_tablet_top_padding',
		            'tablet_right' 		=> 'ocean_theme_button_tablet_right_padding',
		            'tablet_bottom' 	=> 'ocean_theme_button_tablet_bottom_padding',
		            'tablet_left' 		=> 'ocean_theme_button_tablet_left_padding',
		            'mobile_top' 		=> 'ocean_theme_button_mobile_top_padding',
		            'mobile_right' 		=> 'ocean_theme_button_mobile_right_padding',
		            'mobile_bottom' 	=> 'ocean_theme_button_mobile_bottom_padding',
		            'mobile_left' 		=> 'ocean_theme_button_mobile_left_padding',
				),
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 100,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Theme Button Border Radius
			 */
			$wp_customize->add_setting( 'ocean_theme_button_border_radius', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_theme_button_border_radius', array(
				'label'	   				=> esc_html__( 'Border Radius (px)', 'oceanwp' ),
				'section'  				=> 'ocean_general_theme_button',
				'settings' 				=> 'ocean_theme_button_border_radius',
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 100,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Theme Button Background Color
			 */
			$wp_customize->add_setting( 'ocean_theme_button_bg', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#13aff0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_theme_button_bg', array(
				'label'	   				=> esc_html__( 'Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_theme_button',
				'settings' 				=> 'ocean_theme_button_bg',
				'priority' 				=> 10,
			) ) );

			/**
			 * Theme Button Background Color Hover
			 */
			$wp_customize->add_setting( 'ocean_theme_button_hover_bg', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#0b7cac',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_theme_button_hover_bg', array(
				'label'	   				=> esc_html__( 'Background Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_general_theme_button',
				'settings' 				=> 'ocean_theme_button_hover_bg',
				'priority' 				=> 10,
			) ) );

			/**
			 * Theme Button Color
			 */
			$wp_customize->add_setting( 'ocean_theme_button_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_theme_button_color', array(
				'label'	   				=> esc_html__( 'Color', 'oceanwp' ),
				'section'  				=> 'ocean_general_theme_button',
				'settings' 				=> 'ocean_theme_button_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Theme Button Color Hover
			 */
			$wp_customize->add_setting( 'ocean_theme_button_hover_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_theme_button_hover_color', array(
				'label'	   				=> esc_html__( 'Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_general_theme_button',
				'settings' 				=> 'ocean_theme_button_hover_color',
				'priority' 				=> 10,
			) ) );

		}

		/**
		 * Helpers
		 *
		 * @since 1.0.0
		 */
		public static function helpers( $return = NULL ) {

			// Return elementor templates array
			if ( 'elementor' == $return ) {
				$templates 		= array( esc_html__( 'Default', 'oceanwp' ) ); 
				$get_templates 	= get_posts( array( 'post_type' => 'elementor_library', 'numberposts' => -1, 'post_status' => 'publish' ) );

			    if ( ! empty ( $get_templates ) ) {
			    	foreach ( $get_templates as $template ) {
						$templates[ $template->ID ] = $template->post_title;
				    }
				}

				return $templates;
			}

		}

		/**
		 * Generates arrays of elements to target
		 *
		 * @since 1.0.0
		 */
		private static function primary_color_arrays( $return ) {

			// Texts
			$texts = apply_filters( 'ocean_primary_texts', array(
				'a:hover',
				'a.light:hover',
				'.theme-heading .text::before',
				'#top-bar-content > a:hover',
				'#top-bar-social li.oceanwp-email a:hover',
				'#site-navigation-wrap .dropdown-menu > li > a:hover',
				'#site-header.medium-header #medium-searchform button:hover',
				'#oceanwp-mobile-menu-icon a:hover',
				'.blog-entry.post .blog-entry-header h2 a:hover',
				'.blog-entry.post .blog-entry-readmore a:hover',
				'ul.meta li a:hover',
				'.dropcap',
				'.single-post nav.post-navigation .nav-links .title',
				'.related-post-title a:hover',
				'#wp-calendar caption',
				'.contact-info-widget i',
				'.custom-links-widget .oceanwp-custom-links li a:hover',
				'.custom-links-widget .oceanwp-custom-links li a:hover:before',
				'.posts-thumbnails-widget li a:hover',
				'.social-widget li.oceanwp-email a:hover',
				'.comment-author .comment-meta .comment-reply-link',
				'#respond #cancel-comment-reply-link:hover',
				'#footer-widgets .footer-box a:hover',
				'#footer-bottom a:hover',
				'#footer-bottom #footer-bottom-menu a:hover',
				'.sidr a:hover',
				'.sidr-class-dropdown-toggle:hover',
				'.sidr-class-menu-item-has-children.active > a',
				'.sidr-class-menu-item-has-children.active > a > .sidr-class-dropdown-toggle',
				'input[type=checkbox]:checked:before'
			) );

			// Backgrounds
			$backgrounds = apply_filters( 'ocean_primary_backgrounds', array(
				'input[type="button"]',
				'input[type="reset"]',
				'input[type="submit"]',
				'.button',
				'#site-navigation-wrap .dropdown-menu > li.btn > a > span',
				'.thumbnail:hover i',
				'.post-quote-content',
				'.omw-modal .omw-close-modal'
			) );

			// Borders
			$borders = apply_filters( 'ocean_primary_borders', array(
				'.widget-title',
				'blockquote',
				'#searchform-dropdown',
				'.dropdown-menu .sub-menu',
				'.blog-entry.large-entry .blog-entry-readmore a:hover',
				'.oceanwp-newsletter-form-wrap input[type="email"]:focus',
				'.social-widget li.oceanwp-email a:hover',
				'#respond #cancel-comment-reply-link:hover',
				'#footer-widgets .oceanwp-newsletter-form-wrap input[type="email"]:focus'
			) );

			// Return array
			if ( 'texts' == $return ) {
				return $texts;
			} elseif ( 'backgrounds' == $return ) {
				return $backgrounds;
			} elseif ( 'borders' == $return ) {
				return $borders;
			}

		}

		/**
		 * Generates array of elements to target
		 *
		 * @since 1.0.0
		 */
		private static function hover_primary_color_array( $return ) {

			// Hover backgrounds
			$hover = apply_filters( 'ocean_hover_primary_backgrounds', array(
				'input[type="button"]:hover',
				'input[type="reset"]:hover',
				'input[type="submit"]:hover',
				'input[type="button"]:focus',
				'input[type="reset"]:focus',
				'input[type="submit"]:focus',
				'.button:hover',
				'#site-navigation-wrap .dropdown-menu > li.btn > a:hover > span',
				'.post-quote-author',
				'.omw-modal .omw-close-modal:hover',
			) );

			// Return array
			if ( 'hover' == $return ) {
				return $hover;
			}

		}

		/**
		 * Returns array of elements and border style to apply
		 *
		 * @since 1.0.0
		 */
		private static function main_border_array() {

			return apply_filters( 'ocean_border_color_elements', array(

				// General
				'table th',
				'table td',
				'hr',
				'.content-area',
				'body.content-left-sidebar #content-wrap .content-area,
				.content-left-sidebar .content-area',

				// Top bar
				'#top-bar-wrap',

				// Header
				'#site-header',

				// Search top header
				'#site-header.top-header #search-toggle',

				// Dropdown
				'.dropdown-menu ul li',

				// Page header
				'.centered-minimal-page-header',

				// Blog
				'.blog-entry.post',

				'.blog-entry.grid-entry .blog-entry-inner',

				'.single-post h2.entry-title',

				'.single-post .entry-share',
				'.single-post .entry-share ul li a',

				'.single-post nav.post-navigation',
				'.single-post nav.post-navigation .nav-links .nav-previous',

				'#author-bio',
				'#author-bio .author-bio-avatar',
				'#author-bio .author-bio-social li a',

				'#related-posts',

				'#comments',
				'.comment-body',
				'#respond #cancel-comment-reply-link',

				'#blog-entries .type-page',
				
				// Pagination
				'.page-numbers a,
				.page-numbers span:not(.elementor-screen-only),
				.page-links span',

				// Widgets
				'#wp-calendar caption,
				#wp-calendar th,
				#wp-calendar tbody',

				'.contact-info-widget i',

				'.posts-thumbnails-widget li',

				'.tagcloud a'

			) );

		}

		/**
		 * Get Page Header Overlay CSS
		 *
		 * @since 1.0.0
		 */
		public function page_header_overlay( $output ) {

			// Only needed for the background-image style so return otherwise
			if ( 'background-image' != oceanwp_page_header_style() ) {
				return;
			}

			// Global vars
			$opacity 			= get_theme_mod( 'ocean_page_header_bg_image_overlay_opacity', '0.5' );
			$overlay_color 		= get_theme_mod( 'ocean_page_header_bg_image_overlay_color', '#000000' );

			if ( true == get_theme_mod( 'ocean_blog_single_featured_image_title', false )
				&& is_singular( 'post' ) ) {
				$opacity 		= get_theme_mod( 'ocean_blog_single_title_bg_image_overlay_opacity', '0.5' );
				$overlay_color 	= get_theme_mod( 'ocean_blog_single_title_bg_image_overlay_color', '#000000' );
			}

			if ( 'background-image' == get_post_meta( oceanwp_post_id(), 'ocean_post_title_style', true ) ) {

				if ( $meta_opacity = get_post_meta( oceanwp_post_id(), 'ocean_post_title_bg_overlay', true ) ) {
					$opacity 		= $meta_opacity;
				}
				if ( $meta_overlay_color = get_post_meta( oceanwp_post_id(), 'ocean_post_title_bg_overlay_color', true ) ) {
					$overlay_color 	= $meta_overlay_color;
				}

			}

			// Define css var
			$css = '';

			// Get page header overlayopacity
			if ( ! empty( $opacity ) && '0.5' != $opacity ) {
				$css .= 'opacity:'. $opacity .';';
			}

			// Get page header overlay color
			if ( ! empty( $overlay_color ) && '#000000' != $overlay_color ) {
				$css .= 'background-color:'. $overlay_color .';';
			}

			// Return CSS
			if ( ! empty( $css ) ) {
				$output .= '/* Page Header Overlay CSS */.background-image-page-header-overlay{'. $css .'}';
			}

			// Return output css
			return $output;

		}

		/**
		 * Get CSS
		 *
		 * @since 1.0.0
		 */
		public function head_css( $output ) {

			// Global vars
			$primary_color 					= get_theme_mod( 'ocean_primary_color', '#13aff0' );
			$hover_primary_color 			= get_theme_mod( 'ocean_hover_primary_color', '#0b7cac' );
			$main_border_color 				= get_theme_mod( 'ocean_main_border_color', '#e9e9e9' );
			$background_color 				= get_theme_mod( 'ocean_background_color', '#ffffff' );
			$background_image 				= get_theme_mod( 'ocean_background_image' );
			$background_image_position 		= get_theme_mod( 'ocean_background_image_position' );
			$background_image_attachment 	= get_theme_mod( 'ocean_background_image_attachment' );
			$background_image_repeat 		= get_theme_mod( 'ocean_background_image_repeat' );
			$background_image_size 			= get_theme_mod( 'ocean_background_image_size' );
			$links_color 					= get_theme_mod( 'ocean_links_color', '#333333' );
			$links_color_hover 				= get_theme_mod( 'ocean_links_color_hover', '#13aff0' );
			$boxed_width 					= get_theme_mod( 'ocean_boxed_width', '1280' );
			$boxed_outside_bg 				= get_theme_mod( 'ocean_boxed_outside_bg', '#e9e9e9' );
			$boxed_inner_bg 				= get_theme_mod( 'ocean_boxed_inner_bg', '#ffffff' );
			$main_container_width 			= get_theme_mod( 'ocean_main_container_width', '1200' );
			$left_container_width 			= get_theme_mod( 'ocean_left_container_width', '72' );
			$sidebar_width 					= get_theme_mod( 'ocean_sidebar_width', '28' );
			$content_top_padding 			= get_theme_mod( 'ocean_page_content_top_padding', '50' );
			$content_bottom_padding 		= get_theme_mod( 'ocean_page_content_bottom_padding', '50' );
			$tablet_content_top_padding 	= get_theme_mod( 'ocean_page_content_tablet_top_padding' );
			$tablet_content_bottom_padding 	= get_theme_mod( 'ocean_page_content_tablet_bottom_padding' );
			$mobile_content_top_padding 	= get_theme_mod( 'ocean_page_content_mobile_top_padding' );
			$mobile_content_bottom_padding 	= get_theme_mod( 'ocean_page_content_mobile_bottom_padding' );
			$page_header_top_padding 		= get_theme_mod( 'ocean_page_header_top_padding', '34' );
			$page_header_bottom_padding 	= get_theme_mod( 'ocean_page_header_bottom_padding', '34' );
			$tablet_ph_top_padding 			= get_theme_mod( 'ocean_page_header_tablet_top_padding' );
			$tablet_ph_bottom_padding 		= get_theme_mod( 'ocean_page_header_tablet_bottom_padding' );
			$mobile_ph_top_padding 			= get_theme_mod( 'ocean_page_header_mobile_top_padding' );
			$mobile_ph_bottom_padding 		= get_theme_mod( 'ocean_page_header_mobile_bottom_padding' );
			$page_header_bg 				= get_theme_mod( 'ocean_page_header_background', '#f5f5f5' );
			$page_header_title_color 		= get_theme_mod( 'ocean_page_header_title_color', '#333333' );
			$breadcrumbs_text_color 		= get_theme_mod( 'ocean_breadcrumbs_text_color', '#c6c6c6' );
			$breadcrumbs_seperator_color 	= get_theme_mod( 'ocean_breadcrumbs_seperator_color', '#c6c6c6' );
			$breadcrumbs_link_color 		= get_theme_mod( 'ocean_breadcrumbs_link_color', '#333333' );
			$breadcrumbs_link_color_hover 	= get_theme_mod( 'ocean_breadcrumbs_link_color_hover', '#13aff0' );
			$scroll_top_size 				= get_theme_mod( 'ocean_scroll_top_size', '40' );
			$scroll_top_icon_size 			= get_theme_mod( 'ocean_scroll_top_icon_size', '18' );
			$scroll_top_border_radius 		= get_theme_mod( 'ocean_scroll_top_border_radius', '2' );
			$scroll_top_bg 					= get_theme_mod( 'ocean_scroll_top_bg', 'rgba(0,0,0,0.4)' );
			$scroll_top_bg_hover 			= get_theme_mod( 'ocean_scroll_top_bg_hover', 'rgba(0,0,0,0.8)' );
			$scroll_top_color 				= get_theme_mod( 'ocean_scroll_top_color', '#ffffff' );
			$scroll_top_color_hover 		= get_theme_mod( 'ocean_scroll_top_color_hover', '#ffffff' );
			$pagination_font_size 			= get_theme_mod( 'ocean_pagination_font_size', '18' );
			$pagination_border_width 		= get_theme_mod( 'ocean_pagination_border_width', '1' );
			$pagination_bg 					= get_theme_mod( 'ocean_pagination_bg' );
			$pagination_hover_bg 			= get_theme_mod( 'ocean_pagination_hover_bg', '#f8f8f8' );
			$pagination_color 				= get_theme_mod( 'ocean_pagination_color', '#555555' );
			$pagination_hover_color 		= get_theme_mod( 'ocean_pagination_hover_color', '#333333' );
			$pagination_border_color 		= get_theme_mod( 'ocean_pagination_border_color', '#e9e9e9' );
			$pagination_border_hover_color 	= get_theme_mod( 'ocean_pagination_border_hover_color', '#e9e9e9' );
			$label_color 					= get_theme_mod( 'ocean_label_color', '#929292' );
			$input_top_padding 				= get_theme_mod( 'ocean_input_top_padding', '6' );
			$input_right_padding 			= get_theme_mod( 'ocean_input_right_padding', '12' );
			$input_bottom_padding 			= get_theme_mod( 'ocean_input_bottom_padding', '6' );
			$input_left_padding 			= get_theme_mod( 'ocean_input_left_padding', '12' );
			$tablet_input_top_padding 		= get_theme_mod( 'ocean_input_tablet_top_padding' );
			$tablet_input_right_padding 	= get_theme_mod( 'ocean_input_tablet_right_padding' );
			$tablet_input_bottom_padding 	= get_theme_mod( 'ocean_input_tablet_bottom_padding' );
			$tablet_input_left_padding 		= get_theme_mod( 'ocean_input_tablet_left_padding' );
			$mobile_input_top_padding 		= get_theme_mod( 'ocean_input_mobile_top_padding' );
			$mobile_input_right_padding 	= get_theme_mod( 'ocean_input_mobile_right_padding' );
			$mobile_input_bottom_padding 	= get_theme_mod( 'ocean_input_mobile_bottom_padding' );
			$mobile_input_left_padding 		= get_theme_mod( 'ocean_input_mobile_left_padding' );
			$input_font_size 				= get_theme_mod( 'ocean_input_font_size', '14' );
			$input_top_border_width 		= get_theme_mod( 'ocean_input_top_border_width', '1' );
			$input_right_border_width 		= get_theme_mod( 'ocean_input_right_border_width', '1' );
			$input_bottom_border_width 		= get_theme_mod( 'ocean_input_bottom_border_width', '1' );
			$input_left_border_width 		= get_theme_mod( 'ocean_input_left_border_width', '1' );
			$tablet_input_top_bw 			= get_theme_mod( 'ocean_input_tablet_top_border_width' );
			$tablet_input_right_bw 			= get_theme_mod( 'ocean_input_tablet_right_border_width' );
			$tablet_input_bottom_bw 		= get_theme_mod( 'ocean_input_tablet_bottom_border_width' );
			$tablet_input_left_bw 			= get_theme_mod( 'ocean_input_tablet_left_border_width' );
			$mobile_input_top_bw 			= get_theme_mod( 'ocean_input_mobile_top_border_width' );
			$mobile_input_right_bw 			= get_theme_mod( 'ocean_input_mobile_right_border_width' );
			$mobile_input_bottom_bw 		= get_theme_mod( 'ocean_input_mobile_bottom_border_width' );
			$mobile_input_left_bw 			= get_theme_mod( 'ocean_input_mobile_left_border_width' );
			$input_border_radius 			= get_theme_mod( 'ocean_input_border_radius', '3' );
			$input_border_color 			= get_theme_mod( 'ocean_input_border_color', '#dddddd' );
			$input_border_color_focus 		= get_theme_mod( 'ocean_input_border_color_focus', '#bbbbbb' );
			$input_background 				= get_theme_mod( 'ocean_input_background' );
			$input_color 					= get_theme_mod( 'ocean_input_color', '#333333' );
			$theme_button_top_padding 		= get_theme_mod( 'ocean_theme_button_top_padding', '14' );
			$theme_button_right_padding 	= get_theme_mod( 'ocean_theme_button_right_padding', '20' );
			$theme_button_bottom_padding 	= get_theme_mod( 'ocean_theme_button_bottom_padding', '14' );
			$theme_button_left_padding 		= get_theme_mod( 'ocean_theme_button_left_padding', '20' );
			$tablet_tb_top_padding 			= get_theme_mod( 'ocean_theme_button_tablet_top_padding' );
			$tablet_tb_right_padding 		= get_theme_mod( 'ocean_theme_button_tablet_right_padding' );
			$tablet_tb_bottom_padding 		= get_theme_mod( 'ocean_theme_button_tablet_bottom_padding' );
			$tablet_tb_left_padding 		= get_theme_mod( 'ocean_theme_button_tablet_left_padding' );
			$mobile_tb_top_padding 			= get_theme_mod( 'ocean_theme_button_mobile_top_padding' );
			$mobile_tb_right_padding 		= get_theme_mod( 'ocean_theme_button_mobile_right_padding' );
			$mobile_tb_bottom_padding 		= get_theme_mod( 'ocean_theme_button_mobile_bottom_padding' );
			$mobile_tb_left_padding 		= get_theme_mod( 'ocean_theme_button_mobile_left_padding' );
			$theme_button_border_radius 	= get_theme_mod( 'ocean_theme_button_border_radius', '0' );
			$theme_button_bg 				= get_theme_mod( 'ocean_theme_button_bg', '#13aff0' );
			$theme_button_hover_bg 			= get_theme_mod( 'ocean_theme_button_hover_bg', '#0b7cac' );
			$theme_button_color 			= get_theme_mod( 'ocean_theme_button_color', '#ffffff' );
			$theme_button_hover_color 		= get_theme_mod( 'ocean_theme_button_hover_color', '#ffffff' );

			// Meta
			$meta_breadcrumbs_text_color 		= get_post_meta( oceanwp_post_id(), 'ocean_breadcrumbs_color', true );
			$meta_breadcrumbs_seperator_color 	= get_post_meta( oceanwp_post_id(), 'ocean_breadcrumbs_separator_color', true );
			$meta_breadcrumbs_link_color 		= get_post_meta( oceanwp_post_id(), 'ocean_breadcrumbs_links_color', true );
			$meta_breadcrumbs_link_color_hover 	= get_post_meta( oceanwp_post_id(), 'ocean_breadcrumbs_links_hover_color', true );

			// Define css var
			$css = '';
			$content_padding_css = '';
			$tablet_content_padding_css = '';
			$mobile_content_padding_css = '';
			$page_header_padding_css = '';
			$tablet_page_header_padding_css = '';
			$mobile_page_header_padding_css = '';
			$input_padding_css = '';
			$tablet_input_padding_css = '';
			$mobile_input_padding_css = '';
			$input_border_width_css = '';
			$tablet_input_border_width_css = '';
			$mobile_input_border_width_css = '';
			$theme_button_padding_css = '';
			$tablet_theme_button_padding_css = '';
			$mobile_theme_button_padding_css = '';

			// Get primary color arrays
			$texts       	= self::primary_color_arrays( 'texts' );
			$backgrounds 	= self::primary_color_arrays( 'backgrounds' );
			$borders     	= self::primary_color_arrays( 'borders' );

			// Get hover primary color arrays
			$hover_primary 	= self::hover_primary_color_array( 'hover' );

			// Get hover primary color arrays
			$main_border 	= self::main_border_array();

			// Texts
			if ( ! empty( $texts ) && '#13aff0' != $primary_color ) {
				$css .= implode( ',', $texts ) .'{color:'. $primary_color .';}';
			}

			// Backgrounds
			if ( ! empty( $backgrounds ) && '#13aff0' != $primary_color ) {
				$css .= implode( ',', $backgrounds ) .'{background-color:'. $primary_color .';}';
			}

			// Borders
			if ( ! empty( $borders ) && '#13aff0' != $primary_color ) {
				foreach ( $borders as $key => $val ) {
					if ( is_array( $val ) ) {
						$css .= $key .'{';
						foreach ( $val as $key => $val ) {
							$css .= 'border-'. $val .'-color:'. $primary_color .';';
						}
						$css .= '}'; 
					} else {
						$css .= $val .'{border-color:'. $primary_color .';}';
					}
				}
			}

			// Hover primary color
			if ( ! empty( $hover_primary ) && '#0b7cac' != $hover_primary_color ) {
				$css .= implode( ',', $hover_primary ) .'{background-color:'. $hover_primary_color .';}';
			}

			// Main border color
			if ( ! empty( $main_border ) && '#e9e9e9' != $main_border_color ) {
				$css .= implode( ',', $main_border ) .'{border-color:'. $main_border_color .';}';
			}

			// Get site background color
			if ( ! empty( $background_color ) && '#ffffff' != $background_color ) {
				$css .= 'body{background-color:'. $background_color .';}';
			}

			// Get site background image
			if ( ! empty( $background_image ) ) {
				$css .= 'body{background-image:url('. $background_image .');}';
			}

			// Get site background position
			if ( ! empty( $background_image_position ) && 'initial' != $background_image_position ) {
				$css .= 'body{background-position:'. $background_image_position .';}';
			}

			// Get site background attachment
			if ( ! empty( $background_image_attachment ) && 'initial' != $background_image_attachment ) {
				$css .= 'body{background-attachment:'. $background_image_attachment .';}';
			}

			// Get site background repeat
			if ( ! empty( $background_image_repeat ) && 'initial' != $background_image_repeat ) {
				$css .= 'body{background-repeat:'. $background_image_repeat .';}';
			}

			// Get site background size
			if ( ! empty( $background_image_size ) && 'initial' != $background_image_size ) {
				$css .= 'body{background-size:'. $background_image_size .';}';
			}

			// Links color
			if ( ! empty( $links_color ) && '#333333' != $links_color ) {
				$css .= 'a{color:'. $links_color .';}';
			}

			// Links color hover
			if ( ! empty( $links_color_hover ) && '#13aff0' != $links_color_hover ) {
				$css .= 'a:hover{color:'. $links_color_hover .';}';
			}

			// Boxed width
			if ( ! empty( $boxed_width ) && '1280' != $boxed_width ) {
				$css .= '.boxed-main-layout #wrap{width:'. $boxed_width .'px;}';
			}

			// Boxed outside background
			if ( ! empty( $boxed_outside_bg ) && '#e9e9e9' != $boxed_outside_bg ) {
				$css .= '.boxed-main-layout{background-color:'. $boxed_outside_bg .';}';
			}

			// Boxed inner background
			if ( ! empty( $boxed_inner_bg ) && '#ffffff' != $boxed_inner_bg ) {
				$css .= '.boxed-main-layout #wrap{background-color:'. $boxed_inner_bg .';}';
			}

			// Content top padding
			if ( ! empty( $main_container_width ) && '1200' != $main_container_width ) {
				$css .= '.container{width:'. $main_container_width .'px;}';
			}

			// Content top padding
			if ( ! empty( $left_container_width ) && '72' != $left_container_width ) {
				$css .= '@media only screen and (min-width: 960px){ .content-area,.content-left-sidebar .content-area{width:'. $left_container_width .'%;} }';
			}

			// Content top padding
			if ( ! empty( $sidebar_width ) && '28' != $sidebar_width ) {
				$css .= '@media only screen and (min-width: 960px){ .widget-area,.content-left-sidebar .widget-area{width:'. $sidebar_width .'%;} }';
			}

			// Content top padding
			if ( ! empty( $content_top_padding ) && '50' != $content_top_padding ) {
				$content_padding_css .= 'padding-top:'. $content_top_padding .'px;';
			}

			// Content bottom padding
			if ( ! empty( $content_bottom_padding ) && '50' != $content_bottom_padding ) {
				$content_padding_css .= 'padding-bottom:'. $content_bottom_padding .'px;';
			}

			// Content padding css
			if ( ! empty( $content_top_padding ) && '50' != $content_top_padding
				|| ! empty( $content_bottom_padding ) && '50' != $content_bottom_padding ) {
				$css .= '#main #content-wrap{'. $content_padding_css .'}';
			}

			// Tablet content top padding
			if ( ! empty( $tablet_content_top_padding ) ) {
				$tablet_content_padding_css .= 'padding-top:'. $tablet_content_top_padding .'px;';
			}

			// Tablet content bottom padding
			if ( ! empty( $tablet_content_bottom_padding ) ) {
				$tablet_content_padding_css .= 'padding-bottom:'. $tablet_content_bottom_padding .'px;';
			}

			// Tablet content padding css
			if ( ! empty( $tablet_content_top_padding )
				|| ! empty( $tablet_content_bottom_padding ) ) {
				$css .= '@media (max-width: 768px){#main #content-wrap{'. $tablet_content_padding_css .'}}';
			}

			// Mobile content top padding
			if ( ! empty( $mobile_content_top_padding ) ) {
				$mobile_content_padding_css .= 'padding-top:'. $mobile_content_top_padding .'px;';
			}

			// Mobile content bottom padding
			if ( ! empty( $mobile_content_bottom_padding ) ) {
				$mobile_content_padding_css .= 'padding-bottom:'. $mobile_content_bottom_padding .'px;';
			}

			// Mobile content padding css
			if ( ! empty( $mobile_content_top_padding )
				|| ! empty( $mobile_content_bottom_padding ) ) {
				$css .= '@media (max-width: 480px){#main #content-wrap{'. $mobile_content_padding_css .'}}';
			}

			// Page header top padding
			if ( ! empty( $page_header_top_padding ) && '34' != $page_header_top_padding ) {
				$css .= '.page-header, .has-transparent-header .page-header{padding-top:'. $page_header_top_padding .'px;}';
			}

			// Page header bottom padding
			if ( ! empty( $page_header_bottom_padding ) && '34' != $page_header_bottom_padding ) {
				$css .= '.page-header, .has-transparent-header .page-header{padding-bottom:'. $page_header_bottom_padding .'px;}';
			}

			// Page header top padding
			if ( ! empty( $page_header_top_padding ) && '34' != $page_header_top_padding ) {
				$page_header_padding_css .= 'padding-top:'. $page_header_top_padding .'px;';
			}

			// Page header bottom padding
			if ( ! empty( $page_header_bottom_padding ) && '34' != $page_header_bottom_padding ) {
				$page_header_padding_css .= 'padding-bottom:'. $page_header_bottom_padding .'px;';
			}

			// Page header padding css
			if ( ! empty( $page_header_top_padding ) && '34' != $page_header_top_padding
				|| ! empty( $page_header_bottom_padding ) && '34' != $page_header_bottom_padding ) {
				$css .= '.page-header, .has-transparent-header .page-header{'. $page_header_padding_css .'}';
			}

			// Tablet page header top padding
			if ( ! empty( $tablet_ph_top_padding ) ) {
				$tablet_page_header_padding_css .= 'padding-top:'. $tablet_ph_top_padding .'px;';
			}

			// Tablet page header bottom padding
			if ( ! empty( $tablet_ph_bottom_padding ) ) {
				$tablet_page_header_padding_css .= 'padding-bottom:'. $tablet_ph_bottom_padding .'px;';
			}

			// Tablet page header padding css
			if ( ! empty( $tablet_ph_top_padding )
				|| ! empty( $tablet_ph_bottom_padding ) ) {
				$css .= '@media (max-width: 768px){.page-header, .has-transparent-header .page-header{'. $tablet_page_header_padding_css .'}}';
			}

			// Mobile page header top padding
			if ( ! empty( $mobile_ph_top_padding ) ) {
				$mobile_page_header_padding_css .= 'padding-top:'. $mobile_ph_top_padding .'px;';
			}

			// Mobile page header bottom padding
			if ( ! empty( $mobile_ph_bottom_padding ) ) {
				$mobile_page_header_padding_css .= 'padding-bottom:'. $mobile_ph_bottom_padding .'px;';
			}

			// Mobile page header padding css
			if ( ! empty( $mobile_ph_top_padding )
				|| ! empty( $mobile_ph_bottom_padding ) ) {
				$css .= '@media (max-width: 480px){.page-header, .has-transparent-header .page-header{'. $mobile_page_header_padding_css .'}}';
			}

			// Page header background
			if ( ! empty( $page_header_bg ) && '#f5f5f5' != $page_header_bg ) {
				$css .= '.page-header{background-color:'. $page_header_bg .';}';
			}

			// Page header color
			if ( ! empty( $page_header_title_color ) && '#333333' != $page_header_title_color ) {
				$css .= '.page-header .page-header-title{color:'. $page_header_title_color .';}';
			}

			// Breadcrumbs text color
			if ( ! empty( $breadcrumbs_text_color ) && '#c6c6c6' != $breadcrumbs_text_color ) {
				$css .= '.site-breadcrumbs, .background-image-page-header .site-breadcrumbs{color:'. $breadcrumbs_text_color .';}';
			}

			// Breadcrumbs seperator color
			if ( ! empty( $breadcrumbs_seperator_color ) && '#c6c6c6' != $breadcrumbs_seperator_color ) {
				$css .= '.site-breadcrumbs ul li:after{color:'. $breadcrumbs_seperator_color .';}';
			}

			// Breadcrumbs link color
			if ( ! empty( $breadcrumbs_link_color ) && '#333333' != $breadcrumbs_link_color ) {
				$css .= '.site-breadcrumbs a, .background-image-page-header .site-breadcrumbs a{color:'. $breadcrumbs_link_color .';}';
			}

			// Breadcrumbs link hover color
			if ( ! empty( $breadcrumbs_link_color_hover ) && '#13aff0' != $breadcrumbs_link_color_hover ) {
				$css .= '.site-breadcrumbs a:hover, .background-image-page-header .site-breadcrumbs a:hover{color:'. $breadcrumbs_link_color_hover .';}';
			}

			// Meta breadcrumbs text color
			if ( ! empty( $meta_breadcrumbs_text_color ) ) {
				$css .= '.site-breadcrumbs, .background-image-page-header .site-breadcrumbs{color:'. $meta_breadcrumbs_text_color .';}';
			}

			// Meta breadcrumbs seperator color
			if ( ! empty( $meta_breadcrumbs_seperator_color ) ) {
				$css .= '.site-breadcrumbs ul li:after{color:'. $meta_breadcrumbs_seperator_color .';}';
			}

			// Meta breadcrumbs link color
			if ( ! empty( $meta_breadcrumbs_link_color ) ) {
				$css .= '.site-breadcrumbs a, .background-image-page-header .site-breadcrumbs a{color:'. $meta_breadcrumbs_link_color .';}';
			}

			// Meta breadcrumbs link hover color
			if ( ! empty( $meta_breadcrumbs_link_color_hover ) ) {
				$css .= '.site-breadcrumbs a:hover, .background-image-page-header .site-breadcrumbs a:hover{color:'. $meta_breadcrumbs_link_color_hover .';}';
			}

			// Scroll top button size
			if ( ! empty( $scroll_top_size ) && '40' != $scroll_top_size ) {
				$css .= '#scroll-top{width:'. $scroll_top_size .'px;height:'. $scroll_top_size .'px;line-height:'. $scroll_top_size .'px;}';
			}

			// Scroll top button icon size
			if ( ! empty( $scroll_top_icon_size ) && '18' != $scroll_top_icon_size ) {
				$css .= '#scroll-top{font-size:'. $scroll_top_icon_size .'px;}';
			}

			// Scroll top button border radius
			if ( ! empty( $scroll_top_border_radius ) && '2' != $scroll_top_border_radius ) {
				$css .= '#scroll-top{border-radius:'. $scroll_top_border_radius .'px;}';
			}

			// Scroll top button background color
			if ( ! empty( $scroll_top_bg ) && 'rgba(0,0,0,0.4)' != $scroll_top_bg ) {
				$css .= '#scroll-top{background-color:'. $scroll_top_bg .';}';
			}

			// Scroll top button background hover color
			if ( ! empty( $scroll_top_bg_hover ) && 'rgba(0,0,0,0.8)' != $scroll_top_bg_hover ) {
				$css .= '#scroll-top:hover{background-color:'. $scroll_top_bg_hover .';}';
			}

			// Scroll top button background color
			if ( ! empty( $scroll_top_color ) && '#ffffff' != $scroll_top_color ) {
				$css .= '#scroll-top{color:'. $scroll_top_color .';}';
			}

			// Scroll top button background hover color
			if ( ! empty( $scroll_top_color_hover ) && '#ffffff' != $scroll_top_color_hover ) {
				$css .= '#scroll-top:hover{color:'. $scroll_top_color_hover .';}';
			}

			// Pagination font size
			if ( ! empty( $pagination_font_size ) && '18' != $pagination_font_size ) {
				$css .= '.page-numbers a, .page-numbers span:not(.elementor-screen-only), .page-links span{font-size:'. $pagination_font_size .'px;}';
			}

			// Pagination border width
			if ( ! empty( $pagination_border_width ) && '1' != $pagination_border_width ) {
				$css .= '.page-numbers a, .page-numbers span:not(.elementor-screen-only), .page-links span{border-width:'. $pagination_border_width .'px;}';
			}

			// Pagination background color
			if ( ! empty( $pagination_bg ) ) {
				$css .= '.page-numbers a, .page-numbers span:not(.elementor-screen-only), .page-links span{background-color:'. $pagination_bg .';}';
			}

			// Pagination background color hover
			if ( ! empty( $pagination_hover_bg ) && '#f8f8f8' != $pagination_hover_bg ) {
				$css .= '.page-numbers a:hover, .page-links a:hover span, .page-numbers.current, .page-numbers.current:hover{background-color:'. $pagination_hover_bg .';}';
			}

			// Pagination color
			if ( ! empty( $pagination_color ) && '#555555' != $pagination_color ) {
				$css .= '.page-numbers a, .page-numbers span:not(.elementor-screen-only), .page-links span{color:'. $pagination_color .';}';
			}

			// Pagination color hover
			if ( ! empty( $pagination_hover_color ) && '#333333' != $pagination_hover_color ) {
				$css .= '.page-numbers a:hover, .page-links a:hover span, .page-numbers.current, .page-numbers.current:hover{color:'. $pagination_hover_color .';}';
			}

			// Pagination border color
			if ( ! empty( $pagination_border_color ) && '#e9e9e9' != $pagination_border_color ) {
				$css .= '.page-numbers a, .page-numbers span:not(.elementor-screen-only), .page-links span{border-color:'. $pagination_border_color .';}';
			}

			// Pagination border color hover
			if ( ! empty( $pagination_border_hover_color ) && '#e9e9e9' != $pagination_border_hover_color ) {
				$css .= '.page-numbers a:hover, .page-links a:hover span, .page-numbers.current, .page-numbers.current:hover{border-color:'. $pagination_border_hover_color .';}';
			}

			// Label color
			if ( ! empty( $label_color ) && '#929292' != $label_color ) {
				$css .= 'label{color:'. $label_color .';}';
			}

			// Input top padding
			if ( ! empty( $input_top_padding ) && '6' != $input_top_padding ) {
				$input_padding_css .= 'padding-top:'. $input_top_padding .'px;';
			}

			// Input right padding
			if ( ! empty( $input_right_padding ) && '12' != $input_right_padding ) {
				$input_padding_css .= 'padding-right:'. $input_right_padding .'px;';
			}

			// Input bottom padding
			if ( ! empty( $input_bottom_padding ) && '6' != $input_bottom_padding ) {
				$input_padding_css .= 'padding-bottom:'. $input_bottom_padding .'px;';
			}

			// Input left padding
			if ( ! empty( $input_left_padding ) && '12' != $input_left_padding ) {
				$input_padding_css .= 'padding-left:'. $input_left_padding .'px;';
			}

			// Input padding css
			if ( ! empty( $input_top_padding ) && '6' != $input_top_padding
				|| ! empty( $input_right_padding ) && '12' != $input_right_padding
				|| ! empty( $input_bottom_padding ) && '6' != $input_bottom_padding
				|| ! empty( $input_left_padding ) && '12' != $input_left_padding ) {
				$css .= 'form input[type="text"],form input[type="password"],form input[type="email"],form input[type="tel"],form input[type="url"],form input[type="search"],form textarea{'. $input_padding_css .'}';
			}

			// Tablet input top padding
			if ( ! empty( $tablet_input_top_padding ) ) {
				$tablet_input_padding_css .= 'padding-top:'. $tablet_input_top_padding .'px;';
			}

			// Tablet input right padding
			if ( ! empty( $tablet_input_right_padding ) ) {
				$tablet_input_padding_css .= 'padding-right:'. $tablet_input_right_padding .'px;';
			}

			// Tablet input bottom padding
			if ( ! empty( $tablet_input_bottom_padding ) ) {
				$tablet_input_padding_css .= 'padding-bottom:'. $tablet_input_bottom_padding .'px;';
			}

			// Tablet input left padding
			if ( ! empty( $tablet_input_left_padding ) ) {
				$tablet_input_padding_css .= 'padding-left:'. $tablet_input_left_padding .'px;';
			}

			// Tablet input padding css
			if ( ! empty( $tablet_input_top_padding )
				|| ! empty( $tablet_input_right_padding )
				|| ! empty( $tablet_input_bottom_padding )
				|| ! empty( $tablet_input_left_padding ) ) {
				$css .= '@media (max-width: 768px){form input[type="text"],form input[type="password"],form input[type="email"],form input[type="tel"],form input[type="url"],form input[type="search"],form textarea{'. $tablet_input_padding_css .'}}';
			}

			// Mobile input top padding
			if ( ! empty( $mobile_input_top_padding ) ) {
				$mobile_input_padding_css .= 'padding-top:'. $mobile_input_top_padding .'px;';
			}

			// Mobile input right padding
			if ( ! empty( $mobile_input_right_padding ) ) {
				$mobile_input_padding_css .= 'padding-right:'. $mobile_input_right_padding .'px;';
			}

			// Mobile input bottom padding
			if ( ! empty( $mobile_input_bottom_padding ) ) {
				$mobile_input_padding_css .= 'padding-bottom:'. $mobile_input_bottom_padding .'px;';
			}

			// Mobile input left padding
			if ( ! empty( $mobile_input_left_padding ) ) {
				$mobile_input_padding_css .= 'padding-left:'. $mobile_input_left_padding .'px;';
			}

			// Mobile input padding css
			if ( ! empty( $mobile_input_top_padding )
				|| ! empty( $mobile_input_right_padding )
				|| ! empty( $mobile_input_bottom_padding )
				|| ! empty( $mobile_input_left_padding ) ) {
				$css .= '@media (max-width: 480px){form input[type="text"],form input[type="password"],form input[type="email"],form input[type="tel"],form input[type="url"],form input[type="search"],form textarea{'. $mobile_input_padding_css .'}}';
			}

			// Input font size
			if ( ! empty( $input_font_size ) && '14' != $input_font_size ) {
				$css .= 'form input[type="text"],form input[type="password"],form input[type="email"],form input[type="tel"],form input[type="url"],form input[type="search"],form textarea{font-size:'. $input_font_size .'px;}';
			}

			// Input top border width
			if ( ! empty( $input_top_border_width ) && '1' != $input_top_border_width ) {
				$input_border_width_css .= 'border-top-width:'. $input_top_border_width .'px;';
			}

			// Input right border width
			if ( ! empty( $input_right_border_width ) && '1' != $input_right_border_width ) {
				$input_border_width_css .= 'border-right-width:'. $input_right_border_width .'px;';
			}

			// Input bottom border width
			if ( ! empty( $input_bottom_border_width ) && '1' != $input_bottom_border_width ) {
				$input_border_width_css .= 'border-bottom-width:'. $input_bottom_border_width .'px;';
			}

			// Input left border width
			if ( ! empty( $input_left_border_width ) && '1' != $input_left_border_width ) {
				$input_border_width_css .= 'border-left-width:'. $input_left_border_width .'px;';
			}

			// Input border width css
			if ( ! empty( $input_top_border_width ) && '1' != $input_top_border_width
				|| ! empty( $input_right_border_width ) && '1' != $input_right_border_width
				|| ! empty( $input_bottom_border_width ) && '1' != $input_bottom_border_width
				|| ! empty( $input_left_border_width ) && '1' != $input_left_border_width ) {
				$css .= 'form input[type="text"],form input[type="password"],form input[type="email"],form input[type="tel"],form input[type="url"],form input[type="search"],form textarea{'. $input_border_width_css .'}';
			}

			// Tablet input top border width
			if ( ! empty( $tablet_input_top_bw ) ) {
				$tablet_input_border_width_css .= 'border-top-width:'. $tablet_input_top_bw .'px;';
			}

			// Tablet input right border width
			if ( ! empty( $tablet_input_right_bw ) ) {
				$tablet_input_border_width_css .= 'border-right-width:'. $tablet_input_right_bw .'px;';
			}

			// Tablet input bottom border width
			if ( ! empty( $tablet_input_bottom_bw ) ) {
				$tablet_input_border_width_css .= 'border-bottom-width:'. $tablet_input_bottom_bw .'px;';
			}

			// Tablet input left border width
			if ( ! empty( $tablet_input_left_bw ) ) {
				$tablet_input_border_width_css .= 'border-left-width:'. $tablet_input_left_bw .'px;';
			}

			// Tablet input border width css
			if ( ! empty( $tablet_input_top_bw )
				|| ! empty( $tablet_input_right_bw )
				|| ! empty( $tablet_input_bottom_bw )
				|| ! empty( $tablet_input_left_bw ) ) {
				$css .= '@media (max-width: 768px){form input[type="text"],form input[type="password"],form input[type="email"],form input[type="tel"],form input[type="url"],form input[type="search"],form textarea{'. $tablet_input_border_width_css .'}}';
			}

			// Mobile input top border width
			if ( ! empty( $mobile_input_top_bw ) ) {
				$mobile_input_border_width_css .= 'border-top-width:'. $mobile_input_top_bw .'px;';
			}

			// Mobile input right border width
			if ( ! empty( $mobile_input_right_bw ) ) {
				$mobile_input_border_width_css .= 'border-right-width:'. $mobile_input_right_bw .'px;';
			}

			// Mobile input bottom border width
			if ( ! empty( $mobile_input_bottom_bw ) ) {
				$mobile_input_border_width_css .= 'border-bottom-width:'. $mobile_input_bottom_bw .'px;';
			}

			// Mobile input left border width
			if ( ! empty( $mobile_input_left_bw ) ) {
				$mobile_input_border_width_css .= 'border-left-width:'. $mobile_input_left_bw .'px;';
			}

			// Mobile input border width css
			if ( ! empty( $mobile_input_top_bw )
				|| ! empty( $mobile_input_right_bw )
				|| ! empty( $mobile_input_bottom_bw )
				|| ! empty( $mobile_input_left_bw ) ) {
				$css .= '@media (max-width: 480px){form input[type="text"],form input[type="password"],form input[type="email"],form input[type="tel"],form input[type="url"],form input[type="search"],form textarea{'. $mobile_input_border_width_css .'}}';
			}

			// Input border radius
			if ( ! empty( $input_border_radius ) && '3' != $input_border_radius ) {
				$css .= 'form input[type="text"],form input[type="password"],form input[type="email"],form input[type="tel"],form input[type="url"],form input[type="search"],form textarea{border-radius:'. $input_border_radius .'px;}';
			}

			// Input border radius
			if ( ! empty( $input_border_color ) && '#dddddd' != $input_border_color ) {
				$css .= 'form input[type="text"],form input[type="password"],form input[type="email"],form input[type="tel"],form input[type="url"],form input[type="search"],form textarea,.select2-container .select2-choice{border-color:'. $input_border_color .';}';
			}

			// Input border radius
			if ( ! empty( $input_border_color_focus ) && '#bbbbbb' != $input_border_color_focus ) {
				$css .= 'form input[type="text"]:focus,form input[type="password"]:focus,form input[type="email"]:focus,form input[type="tel"]:focus,form input[type="url"]:focus,form input[type="search"]:focus,form textarea:focus,.select2-drop-active,.select2-dropdown-open.select2-drop-above .select2-choice,.select2-dropdown-open.select2-drop-above .select2-choices,.select2-drop.select2-drop-above.select2-drop-active,.select2-container-active .select2-choice,.select2-container-active .select2-choices{border-color:'. $input_border_color_focus .';}';
			}

			// Input border radius
			if ( ! empty( $input_background ) ) {
				$css .= 'form input[type="text"],form input[type="password"],form input[type="email"],form input[type="tel"],form input[type="url"],form input[type="search"],form textarea{background-color:'. $input_background .';}';
			}

			// Input border radius
			if ( ! empty( $input_color ) && '#333333' != $input_color ) {
				$css .= 'form input[type="text"],form input[type="password"],form input[type="email"],form input[type="tel"],form input[type="url"],form input[type="search"],form textarea{color:'. $input_color .';}';
			}

			// Theme button top padding
			if ( ! empty( $theme_button_top_padding ) && '14' != $theme_button_top_padding ) {
				$theme_button_padding_css .= 'padding-top:'. $theme_button_top_padding .'px;';
			}

			// Theme button right padding
			if ( ! empty( $theme_button_right_padding ) && '20' != $theme_button_right_padding ) {
				$theme_button_padding_css .= 'padding-right:'. $theme_button_right_padding .'px;';
			}

			// Theme button bottom padding
			if ( ! empty( $theme_button_bottom_padding ) && '14' != $theme_button_bottom_padding ) {
				$theme_button_padding_css .= 'padding-bottom:'. $theme_button_bottom_padding .'px;';
			}

			// Theme button left padding
			if ( ! empty( $theme_button_left_padding ) && '20' != $theme_button_left_padding ) {
				$theme_button_padding_css .= 'padding-left:'. $theme_button_left_padding .'px;';
			}

			// Theme button padding css
			if ( ! empty( $theme_button_top_padding ) && '14' != $theme_button_top_padding
				|| ! empty( $theme_button_right_padding ) && '20' != $theme_button_right_padding
				|| ! empty( $theme_button_bottom_padding ) && '14' != $theme_button_bottom_padding
				|| ! empty( $theme_button_left_padding ) && '20' != $theme_button_left_padding ) {
				$css .= '.theme-button,input[type="submit"],button{'. $theme_button_padding_css .'}';
			}

			// Tablet theme button top padding
			if ( ! empty( $tablet_tb_top_padding ) ) {
				$tablet_theme_button_padding_css .= 'padding-top:'. $tablet_tb_top_padding .'px;';
			}

			// Tablet theme button right padding
			if ( ! empty( $tablet_tb_right_padding ) ) {
				$tablet_theme_button_padding_css .= 'padding-right:'. $tablet_tb_right_padding .'px;';
			}

			// Tablet theme button bottom padding
			if ( ! empty( $tablet_tb_bottom_padding ) ) {
				$tablet_theme_button_padding_css .= 'padding-bottom:'. $tablet_tb_bottom_padding .'px;';
			}

			// Tablet theme button left padding
			if ( ! empty( $tablet_tb_left_padding ) ) {
				$tablet_theme_button_padding_css .= 'padding-left:'. $tablet_tb_left_padding .'px;';
			}

			// Tablet theme button padding css
			if ( ! empty( $tablet_tb_top_padding )
				|| ! empty( $tablet_tb_right_padding )
				|| ! empty( $tablet_tb_bottom_padding )
				|| ! empty( $tablet_tb_left_padding ) ) {
				$css .= '@media (max-width: 768px){.theme-button,input[type="submit"],button{'. $tablet_theme_button_padding_css .'}}';
			}

			// Mobile theme button top padding
			if ( ! empty( $mobile_tb_top_padding ) ) {
				$mobile_theme_button_padding_css .= 'padding-top:'. $mobile_tb_top_padding .'px;';
			}

			// Mobile theme button right padding
			if ( ! empty( $mobile_tb_right_padding ) ) {
				$mobile_theme_button_padding_css .= 'padding-right:'. $mobile_tb_right_padding .'px;';
			}

			// Mobile theme button bottom padding
			if ( ! empty( $mobile_tb_bottom_padding ) ) {
				$mobile_theme_button_padding_css .= 'padding-bottom:'. $mobile_tb_bottom_padding .'px;';
			}

			// Mobile theme button left padding
			if ( ! empty( $mobile_tb_left_padding ) ) {
				$mobile_theme_button_padding_css .= 'padding-left:'. $mobile_tb_left_padding .'px;';
			}

			// Mobile theme button padding css
			if ( ! empty( $mobile_tb_top_padding )
				|| ! empty( $mobile_tb_right_padding )
				|| ! empty( $mobile_tb_bottom_padding )
				|| ! empty( $mobile_tb_left_padding ) ) {
				$css .= '@media (max-width: 480px){.theme-button,input[type="submit"],button{'. $mobile_theme_button_padding_css .'}}';
			}

			// Theme button border radius
			if ( ! empty( $theme_button_border_radius ) && '0' != $theme_button_border_radius ) {
				$css .= '.theme-button,input[type="submit"],button{border-radius:'. $theme_button_border_radius .'px;}';
			}

			// Theme button background color
			if ( ! empty( $theme_button_bg ) && '#13aff0' != $theme_button_bg ) {
				$css .= '.theme-button,input[type="submit"],button{background-color:'. $theme_button_bg .';}';
			}

			// Theme button background color
			if ( ! empty( $theme_button_hover_bg ) && '#0b7cac' != $theme_button_hover_bg ) {
				$css .= '.theme-button:hover,input[type="submit"]:hover,button:hover{background-color:'. $theme_button_hover_bg .';}';
			}

			// Theme button background color
			if ( ! empty( $theme_button_color ) && '#ffffff' != $theme_button_color ) {
				$css .= '.theme-button,input[type="submit"],button,.button{color:'. $theme_button_color .';}';
			}

			// Theme button background color
			if ( ! empty( $theme_button_hover_color ) && '#ffffff' != $theme_button_hover_color ) {
				$css .= '.theme-button:hover,input[type="submit"]:hover,button:hover,.button:hover{color:'. $theme_button_hover_color .';}';
			}

			// Return CSS
			if ( ! empty( $css ) ) {
				$output .= '/* General CSS */'. $css;
			}

			// Return output css
			return $output;

		}

	}

endif;

return new OceanWP_General_Customizer();