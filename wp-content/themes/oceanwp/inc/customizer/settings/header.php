<?php
/**
 * Header Customizer Options
 *
 * @package OceanWP WordPress theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'OceanWP_Header_Customizer' ) ) :

	class OceanWP_Header_Customizer {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {

			add_action( 'customize_register', 	array( $this, 'customizer_options' ) );
			add_filter( 'ocean_head_css', 		array( $this, 'head_css' ) );
			add_filter( 'ocean_head_css', 		array( $this, 'header_image_css' ) );

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
			$panel = 'ocean_header_panel';
			$wp_customize->add_panel( $panel , array(
				'title' 			=> esc_html__( 'Header', 'oceanwp' ),
				'priority' 			=> 210,
			) );

			/**
			 * Section
			 */
			$wp_customize->add_section( 'ocean_header_general' , array(
				'title' 			=> esc_html__( 'General', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**
			 * Header Style
			 */
			$wp_customize->add_setting( 'ocean_header_style', array(
				'default'           	=> 'minimal',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_header_style', array(
				'label'	   				=> esc_html__( 'Style', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_header_general',
				'settings' 				=> 'ocean_header_style',
				'priority' 				=> 10,
				'choices' 				=> array(
					'minimal' 		=> esc_html__( 'Minimal', 'oceanwp' ),
					'transparent' 	=> esc_html__( 'Transparent', 'oceanwp' ),
					'top'			=> esc_html__( 'Top Menu', 'oceanwp' ),
					'full_screen'	=> esc_html__( 'Full Screen', 'oceanwp' ),
					'center'		=> esc_html__( 'Center', 'oceanwp' ),
					'medium'		=> esc_html__( 'Medium', 'oceanwp' ),
					'custom'		=> esc_html__( 'Custom Header', 'oceanwp' ),
				),
			) ) );

			/**
			 * Custom Header Page ID
			 */
			$wp_customize->add_setting( 'ocean_header_page_id', array(
				'default' 				=> '',
				'sanitize_callback' 	=> 'oceanwp_sanitize_dropdown_pages',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Dropdown_Pages( $wp_customize, 'ocean_header_page_id', array(
				'label'	   				=> esc_html__( 'Page ID', 'oceanwp' ),
				'description'	   		=> esc_html__( 'Choose a page where the content will be displayed in the header.', 'oceanwp' ),
				'section'  				=> 'ocean_header_general',
				'settings' 				=> 'ocean_header_page_id',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_custom_header',
			) ) );

			/**
			 * Custom Header Add Container
			 */
			$wp_customize->add_setting( 'ocean_add_custom_header_container', array(
				'transport' 			=> 'postMessage',
				'default'           	=> true,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_add_custom_header_container', array(
				'label'	   				=> esc_html__( 'Add Container', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> 'ocean_header_general',
				'settings' 				=> 'ocean_add_custom_header_container',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_custom_header',
			) ) );

			/**
			 * Header Height
			 */
			$wp_customize->add_setting( 'ocean_header_height', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '74',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_header_height', array(
				'label'	   				=> esc_html__( 'Height (px)', 'oceanwp' ),
				'section'  				=> 'ocean_header_general',
				'settings' 				=> 'ocean_header_height',
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 1000,
			        'step'  => 1,
			    ),
				'active_callback' 		=> 'oceanwp_cac_hasnt_header_styles',
			) ) );

			/**
			 * Top Header Height
			 */
			$wp_customize->add_setting( 'ocean_top_header_height', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '40',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_top_header_height', array(
				'label'	   				=> esc_html__( 'Height (px)', 'oceanwp' ),
				'section'  				=> 'ocean_header_general',
				'settings' 				=> 'ocean_top_header_height',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_top_header_style',
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 100,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Header Full Width
			 */
			$wp_customize->add_setting( 'ocean_header_full_width', array(
				'transport' 			=> 'postMessage',
				'default'           	=> false,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_header_full_width', array(
				'label'	   				=> esc_html__( 'Header Full Width', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> 'ocean_header_general',
				'settings' 				=> 'ocean_header_full_width',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_hasnt_medium_custom_header_styles',
			) ) );

			/**
			 * Header Border Bottom
			 */
			$wp_customize->add_setting( 'ocean_has_header_border_bottom', array(
				'transport' 			=> 'postMessage',
				'default'           	=> true,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_has_header_border_bottom', array(
				'label'	   				=> esc_html__( 'Header Border Bottom', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> 'ocean_header_general',
				'settings' 				=> 'ocean_has_header_border_bottom',
				'priority' 				=> 10,
			) ) );

			/**
			 * Header Background Color
			 */
			$wp_customize->add_setting( 'ocean_header_background', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_header_background', array(
				'label'	   				=> esc_html__( 'Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_general',
				'settings' 				=> 'ocean_header_background',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_hasnt_medium_header_style',
			) ) );

			/**
			 * Header Border Bottom Color
			 */
			$wp_customize->add_setting( 'ocean_header_border_bottom', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#f1f1f1',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_header_border_bottom', array(
				'label'	   				=> esc_html__( 'Border Bottom Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_general',
				'settings' 				=> 'ocean_header_border_bottom',
				'priority' 				=> 10,
			) ) );

			/**
			 * Header Padding
			 */
			$wp_customize->add_setting( 'ocean_header_top_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_header_right_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_header_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_header_left_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_setting( 'ocean_header_tablet_top_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_header_tablet_right_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_header_tablet_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_header_tablet_left_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_setting( 'ocean_header_mobile_top_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_header_mobile_right_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_header_mobile_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_header_mobile_left_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Dimensions_Control( $wp_customize, 'ocean_header_padding', array(
				'label'	   				=> esc_html__( 'Padding (px)', 'oceanwp' ),
				'section'  				=> 'ocean_header_general',				
				'settings'   => array(					
		            'desktop_top' 		=> 'ocean_header_top_padding',
		            'desktop_right' 	=> 'ocean_header_right_padding',
		            'desktop_bottom' 	=> 'ocean_header_bottom_padding',
		            'desktop_left' 		=> 'ocean_header_left_padding',
		            'tablet_top' 		=> 'ocean_header_tablet_top_padding',
		            'tablet_right' 		=> 'ocean_header_tablet_right_padding',
		            'tablet_bottom' 	=> 'ocean_header_tablet_bottom_padding',
		            'tablet_left' 		=> 'ocean_header_tablet_left_padding',
		            'mobile_top' 		=> 'ocean_header_mobile_top_padding',
		            'mobile_right' 		=> 'ocean_header_mobile_right_padding',
		            'mobile_bottom' 	=> 'ocean_header_mobile_bottom_padding',
		            'mobile_left' 		=> 'ocean_header_mobile_left_padding',
				),
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_hasnt_medium_header_style',
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 100,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Section
			 */
			$wp_customize->add_section( 'ocean_header_transparent_style' , array(
				'title' 			=> esc_html__( 'Transparent Header', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**
			 * Transparent Header Image Logo
			 */
			$wp_customize->add_setting( 'ocean_transparent_header_logo', array(
				'default'           	=> '',
				'sanitize_callback' 	=> 'oceanwp_sanitize_image',
			) );

			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ocean_transparent_header_logo', array(
				'label'	   				=> esc_html__( 'Logo', 'oceanwp' ),
				'description'	   		=> esc_html__( 'If you want to display a different logo for this style (optional)', 'oceanwp' ),
				'section'  				=> 'ocean_header_transparent_style',
				'settings' 				=> 'ocean_transparent_header_logo',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_transparent_header_style',
			) ) );

			/**
			 * Transparent Header Retina Logo
			 */
			$wp_customize->add_setting( 'ocean_transparent_header_logo_retina', array(
				'default'           	=> '',
				'sanitize_callback' 	=> 'oceanwp_sanitize_image',
			) );

			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ocean_transparent_header_logo_retina', array(
				'label'	   				=> esc_html__( 'Retina Logo', 'oceanwp' ),
				'section'  				=> 'ocean_header_transparent_style',
				'settings' 				=> 'ocean_transparent_header_logo_retina',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_transparent_header_style',
			) ) );

			/**
			 * Transparent Header Background Color
			 */
			$wp_customize->add_setting( 'ocean_transparent_header_bg', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_transparent_header_bg', array(
				'label'	   				=> esc_html__( 'Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_transparent_style',
				'settings' 				=> 'ocean_transparent_header_bg',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_transparent_header_style',
			) ) );

			/**
			 * Section
			 */
			$wp_customize->add_section( 'ocean_header_top_style' , array(
				'title' 			=> esc_html__( 'Top Menu Header', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**
			 * Top Menu Header Menu Position
			 */
			$wp_customize->add_setting( 'ocean_top_header_menu_position', array(
				'default'           	=> 'before',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_top_header_menu_position', array(
				'label'	   				=> esc_html__( 'Menu Position', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_header_top_style',
				'settings' 				=> 'ocean_top_header_menu_position',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_top_header_style',
				'choices' 				=> array(
					'before' 	=> esc_html__( 'Before The Logo', 'oceanwp' ),
					'after' 	=> esc_html__( 'After The Logo', 'oceanwp' ),
				),
			) ) );

			/**
			 * Top Menu Header Menu Background Color
			 */
			$wp_customize->add_setting( 'ocean_top_header_menu_background', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_top_header_menu_background', array(
				'label'	   				=> esc_html__( 'Menu Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_top_style',
				'settings' 				=> 'ocean_top_header_menu_background',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_top_header_style',
			) ) );

			/**
			 * Top Menu Header Search Button Border Color
			 */
			$wp_customize->add_setting( 'ocean_top_header_search_button_border_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#f1f1f1',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_top_header_search_button_border_color', array(
				'label'	   				=> esc_html__( 'Search Button Border Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_top_style',
				'settings' 				=> 'ocean_top_header_search_button_border_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_top_header_style',
			) ) );

			/**
			 * Top Menu Header Search Button Color
			 */
			$wp_customize->add_setting( 'ocean_top_header_search_button_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#333333',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_top_header_search_button_color', array(
				'label'	   				=> esc_html__( 'Search Button Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_top_style',
				'settings' 				=> 'ocean_top_header_search_button_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_top_header_style',
			) ) );

			/**
			 * Top Menu Header Search Button Hover Color
			 */
			$wp_customize->add_setting( 'ocean_top_header_search_button_hover_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#13aff0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_top_header_search_button_hover_color', array(
				'label'	   				=> esc_html__( 'Search Button Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_header_top_style',
				'settings' 				=> 'ocean_top_header_search_button_hover_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_top_header_style',
			) ) );

			/**
			 * Section
			 */
			$wp_customize->add_section( 'ocean_header_full_screen_style' , array(
				'title' 			=> esc_html__( 'Full Screen Header', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**
			 * Full Screen Header Logo
			 */
			$wp_customize->add_setting( 'ocean_full_screen_header_logo', array(
				'default'           	=> '',
				'sanitize_callback' 	=> 'oceanwp_sanitize_image',
			) );

			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ocean_full_screen_header_logo', array(
				'label'	   				=> esc_html__( 'Logo (optional)', 'oceanwp' ),
				'description'	 		=> esc_html__( 'Select a custom logo when the menu is opened.', 'oceanwp' ),
				'section'  				=> 'ocean_header_full_screen_style',
				'settings' 				=> 'ocean_full_screen_header_logo',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_full_screen_header_style',
			) ) );

			/**
			 * Full Screen Styling Heading
			 */
			$wp_customize->add_setting( 'ocean_full_screen_styling_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_full_screen_styling_heading', array(
				'label'    				=> esc_html__( 'Styling', 'oceanwp' ),
				'section'  				=> 'ocean_header_full_screen_style',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_full_screen_header_style',
			) ) );

			/**
			 * Full Screen Header Menu Bar Color
			 */
			$wp_customize->add_setting( 'ocean_full_screen_header_menu_bar_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#333333',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_full_screen_header_menu_bar_color', array(
				'label'	   				=> esc_html__( 'Menu Bar Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_full_screen_style',
				'settings' 				=> 'ocean_full_screen_header_menu_bar_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_full_screen_header_style',
			) ) );

			/**
			 * Full Screen Header Menu Bar Close Color
			 */
			$wp_customize->add_setting( 'ocean_full_screen_header_menu_bar_close_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_full_screen_header_menu_bar_close_color', array(
				'label'	   				=> esc_html__( 'Menu Bar Close Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_full_screen_style',
				'settings' 				=> 'ocean_full_screen_header_menu_bar_close_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_full_screen_header_style',
			) ) );

			/**
			 * Full Screen Header Background Color
			 */
			$wp_customize->add_setting( 'ocean_full_screen_header_bg', array(
				'transport' 			=> 'postMessage',
				'default'           	=> 'rgba(0,0,0,0.9)',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_full_screen_header_bg', array(
				'label'	   				=> esc_html__( 'Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_full_screen_style',
				'settings' 				=> 'ocean_full_screen_header_bg',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_full_screen_header_style',
			) ) );

			/**
			 * Full Screen Header Links Background Color
			 */
			$wp_customize->add_setting( 'ocean_full_screen_header_links_bg_color', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_full_screen_header_links_bg_color', array(
				'label'	   				=> esc_html__( 'Links Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_full_screen_style',
				'settings' 				=> 'ocean_full_screen_header_links_bg_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_full_screen_header_style',
			) ) );

			/**
			 * Full Screen Header Links Hover Background Color
			 */
			$wp_customize->add_setting( 'ocean_full_screen_header_links_hover_bg_color', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_full_screen_header_links_hover_bg_color', array(
				'label'	   				=> esc_html__( 'Links Hover Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_full_screen_style',
				'settings' 				=> 'ocean_full_screen_header_links_hover_bg_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_full_screen_header_style',
			) ) );

			/**
			 * Full Screen Header Links Color
			 */
			$wp_customize->add_setting( 'ocean_full_screen_header_links_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_full_screen_header_links_color', array(
				'label'	   				=> esc_html__( 'Links Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_full_screen_style',
				'settings' 				=> 'ocean_full_screen_header_links_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_full_screen_header_style',
			) ) );

			/**
			 * Full Screen Header Links Hover Color
			 */
			$wp_customize->add_setting( 'ocean_full_screen_header_links_hover_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#929292',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_full_screen_header_links_hover_color', array(
				'label'	   				=> esc_html__( 'Links Hover Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_full_screen_style',
				'settings' 				=> 'ocean_full_screen_header_links_hover_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_full_screen_header_style',
			) ) );

			/**
			 * Full Screen Search Styling Heading
			 */
			$wp_customize->add_setting( 'ocean_full_screen_search_styling_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_full_screen_search_styling_heading', array(
				'label'    				=> esc_html__( 'Search Styling', 'oceanwp' ),
				'section'  				=> 'ocean_header_full_screen_style',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_full_screen_header_style',
			) ) );

			/**
			 * Full Screen Header Input Color
			 */
			$wp_customize->add_setting( 'ocean_full_screen_header_search_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_full_screen_header_search_color', array(
				'label'	   				=> esc_html__( 'Input Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_full_screen_style',
				'settings' 				=> 'ocean_full_screen_header_search_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_full_screen_header_style',
			) ) );

			/**
			 * Full Screen Header Input Dashed Text Color
			 */
			$wp_customize->add_setting( 'ocean_full_screen_header_search_dashed_bg', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_full_screen_header_search_dashed_bg', array(
				'label'	   				=> esc_html__( 'Input Dashed Text Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_full_screen_style',
				'settings' 				=> 'ocean_full_screen_header_search_dashed_bg',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_full_screen_header_style',
			) ) );

			/**
			 * Full Screen Header Input Border Bottom Color
			 */
			$wp_customize->add_setting( 'ocean_full_screen_header_search_border_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#666666',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_full_screen_header_search_border_color', array(
				'label'	   				=> esc_html__( 'Input Border Bottom Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_full_screen_style',
				'settings' 				=> 'ocean_full_screen_header_search_border_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_full_screen_header_style',
			) ) );

			/**
			 * Full Screen Header Input Hover Border Bottom Color
			 */
			$wp_customize->add_setting( 'ocean_full_screen_header_search_hover_border_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_full_screen_header_search_hover_border_color', array(
				'label'	   				=> esc_html__( 'Input Hover Border Bottom Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_full_screen_style',
				'settings' 				=> 'ocean_full_screen_header_search_hover_border_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_full_screen_header_style',
			) ) );

			/**
			 * Full Screen Header Input Focus Border Bottom Color
			 */
			$wp_customize->add_setting( 'ocean_full_screen_header_search_focus_border_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_full_screen_header_search_focus_border_color', array(
				'label'	   				=> esc_html__( 'Input Focus Border Bottom Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_full_screen_style',
				'settings' 				=> 'ocean_full_screen_header_search_focus_border_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_full_screen_header_style',
			) ) );

			/**
			 * Section
			 */
			$wp_customize->add_section( 'ocean_header_medium_style' , array(
				'title' 			=> esc_html__( 'Medium Header', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**
			 * Medium Header Hide Menu When Scrolling
			 */
			$wp_customize->add_setting( 'ocean_medium_header_hidden_menu', array(
				'transport' 			=> 'postMessage',
				'default'           	=> true,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_medium_header_hidden_menu', array(
				'label'	   				=> esc_html__( 'Hide Menu When Scrolling', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> 'ocean_header_medium_style',
				'settings' 				=> 'ocean_medium_header_hidden_menu',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			) ) );

			/**
			 * Medium Header Top Header Heading
			 */
			$wp_customize->add_setting( 'ocean_medium_header_top_header_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_medium_header_top_header_heading', array(
				'label'    				=> esc_html__( 'Top Header', 'oceanwp' ),
				'section'  				=> 'ocean_header_medium_style',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			) ) );

			/**
			 * Medium Header Top Header Background Color
			 */
			$wp_customize->add_setting( 'ocean_medium_header_top_header_bg', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_medium_header_top_header_bg', array(
				'label'	   				=> esc_html__( 'Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_medium_style',
				'settings' 				=> 'ocean_medium_header_top_header_bg',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			) ) );

			/**
		     * Medium Header Top Header Elements
		     */
	        $wp_customize->add_setting( 'ocean_medium_header_top_header_elements', array(
				'default'           	=> array( 'searchfrom', 'logo', 'social' ),
				'sanitize_callback' 	=> 'oceanwp_sanitize_multi_choices',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Sortable_Control( $wp_customize, 'ocean_medium_header_top_header_elements', array(
				'label'	   				=> esc_html__( 'Elements Positioning', 'oceanwp' ),
				'section'  				=> 'ocean_header_medium_style',
				'settings' 				=> 'ocean_medium_header_top_header_elements',
				'priority' 				=> 10,
				'choices' 				=> array(
					'searchfrom'  	=> esc_html__( 'Search Form', 'oceanwp' ),
					'logo' 			=> esc_html__( 'Logo', 'oceanwp' ),
					'social' 		=> esc_html__( 'Social Buttons', 'oceanwp' ),
				),
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			) ) );

			/**
			 * Medium Header Top Header Padding Top/Bottom
			 */
			$wp_customize->add_setting( 'ocean_medium_header_top_header_top_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '30',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_medium_header_top_header_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '30',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_setting( 'ocean_medium_header_top_header_tablet_top_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_medium_header_top_header_tablet_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_setting( 'ocean_medium_header_top_header_mobile_top_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_medium_header_top_header_mobile_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Dimensions_Control( $wp_customize, 'ocean_medium_header_top_header_padding', array(
				'label'	   				=> esc_html__( 'Padding (px)', 'oceanwp' ),
				'section'  				=> 'ocean_header_medium_style',
				'settings' => array(
		            'desktop_top' 		=> 'ocean_medium_header_top_header_top_padding',
		            'desktop_bottom' 	=> 'ocean_medium_header_top_header_bottom_padding',
		            'tablet_top' 		=> 'ocean_medium_header_top_header_tablet_top_padding',
		            'tablet_bottom' 	=> 'ocean_medium_header_top_header_tablet_bottom_padding',
		            'mobile_top' 		=> 'ocean_medium_header_top_header_mobile_top_padding',
		            'mobile_bottom' 	=> 'ocean_medium_header_top_header_mobile_bottom_padding',
			    ),
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 100,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Medium Header Sticky Top Header Padding Top/Bottom
			 */
			$wp_customize->add_setting( 'ocean_medium_header_sticky_top_header_top_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '20',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_medium_header_sticky_top_header_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '20',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_setting( 'ocean_medium_header_sticky_top_header_tablet_top_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_medium_header_sticky_top_header_tablet_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_setting( 'ocean_medium_header_sticky_top_header_mobile_top_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_medium_header_sticky_top_header_mobile_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Dimensions_Control( $wp_customize, 'ocean_medium_header_sticky_top_header_padding', array(
				'label'	   				=> esc_html__( 'Sticky Padding (px)', 'oceanwp' ),
				'description'	   		=> esc_html__( 'If you use the sticky header extension', 'oceanwp' ),
				'section'  				=> 'ocean_header_medium_style',
				'settings' => array(
		            'desktop_top' 		=> 'ocean_medium_header_sticky_top_header_top_padding',
		            'desktop_bottom' 	=> 'ocean_medium_header_sticky_top_header_bottom_padding',
		            'tablet_top' 		=> 'ocean_medium_header_sticky_top_header_tablet_top_padding',
		            'tablet_bottom' 	=> 'ocean_medium_header_sticky_top_header_tablet_bottom_padding',
		            'mobile_top' 		=> 'ocean_medium_header_sticky_top_header_mobile_top_padding',
		            'mobile_bottom' 	=> 'ocean_medium_header_sticky_top_header_mobile_bottom_padding',
			    ),
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 100,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Medium Header Menu Heading
			 */
			$wp_customize->add_setting( 'ocean_medium_header_menu_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_medium_header_menu_heading', array(
				'label'    				=> esc_html__( 'Menu', 'oceanwp' ),
				'section'  				=> 'ocean_header_medium_style',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			) ) );

			/**
			 * Medium Header Menu Position
			 */
			$wp_customize->add_setting( 'ocean_medium_header_menu_position', array(
				'transport' 			=> 'postMessage',
				'default'           	=> 'center-menu',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Buttonset_Control( $wp_customize, 'ocean_medium_header_menu_position', array(
				'label'	   				=> esc_html__( 'Position', 'oceanwp' ),
				'section'  				=> 'ocean_header_medium_style',
				'settings' 				=> 'ocean_medium_header_menu_position',
				'priority' 				=> 10,
				'choices' 				=> array(
					'left-menu' 	=> esc_html__( 'Left', 'oceanwp' ),
					'center-menu' 	=> esc_html__( 'Center', 'oceanwp' ),
					'right-menu' 	=> esc_html__( 'Right', 'oceanwp' ),
				),
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			) ) );

			/**
			 * Medium Header Menu Height
			 */
			$wp_customize->add_setting( 'ocean_medium_header_menu_height', array(
				'transport' 			=> 'postMessage',
				'default'     			=> '60',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_medium_header_menu_height', array(
				'label'	   				=> esc_html__( 'Height (px)', 'oceanwp' ),
				'section'  				=> 'ocean_header_medium_style',
				'settings' 				=> 'ocean_medium_header_menu_height',
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 150,
			        'step'  => 1,
			    ),
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			) ) );

			/**
			 * Medium Header Menu Items Padding
			 */
			$wp_customize->add_setting( 'ocean_medium_header_menu_items_right_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '22',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_medium_header_menu_items_left_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '22',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_setting( 'ocean_medium_header_menu_items_tablet_right_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_medium_header_menu_items_tablet_left_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_setting( 'ocean_medium_header_menu_items_mobile_right_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_medium_header_menu_items_mobile_left_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Dimensions_Control( $wp_customize, 'ocean_medium_header_menu_items_padding', array(
				'label'	   				=> esc_html__( 'Menu Items Padding (px)', 'oceanwp' ),
				'section'  				=> 'ocean_header_medium_style',
				'settings' => array(
		            'desktop_right' 	=> 'ocean_medium_header_menu_items_right_padding',
		            'desktop_left' 		=> 'ocean_medium_header_menu_items_left_padding',
		            'tablet_right' 		=> 'ocean_medium_header_menu_items_tablet_right_padding',
		            'tablet_left' 		=> 'ocean_medium_header_menu_items_tablet_left_padding',
		            'mobile_right' 		=> 'ocean_medium_header_menu_items_mobile_right_padding',
		            'mobile_left' 		=> 'ocean_medium_header_menu_items_mobile_left_padding',
			    ),
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 60,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Medium Header Menu Background Color
			 */
			$wp_customize->add_setting( 'ocean_medium_header_menu_bg', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#f9f9f9',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_medium_header_menu_bg', array(
				'label'	   				=> esc_html__( 'Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_medium_style',
				'settings' 				=> 'ocean_medium_header_menu_bg',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			) ) );

			/**
			 * Medium Header Search Form Heading
			 */
			$wp_customize->add_setting( 'ocean_medium_header_searchform_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_medium_header_searchform_heading', array(
				'label'    				=> esc_html__( 'Search Form', 'oceanwp' ),
				'section'  				=> 'ocean_header_medium_style',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			) ) );

			/**
			 * Medium Header Input Background Color
			 */
			$wp_customize->add_setting( 'ocean_medium_header_search_bg', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#f5f5f5',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_medium_header_search_bg', array(
				'label'	   				=> esc_html__( 'Input Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_medium_style',
				'settings' 				=> 'ocean_medium_header_search_bg',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			) ) );

			/**
			 * Medium Header Input Color
			 */
			$wp_customize->add_setting( 'ocean_medium_header_search_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#333333',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_medium_header_search_color', array(
				'label'	   				=> esc_html__( 'Input Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_medium_style',
				'settings' 				=> 'ocean_medium_header_search_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			) ) );

			/**
			 * Medium Header Placeholder Color
			 */
			$wp_customize->add_setting( 'ocean_medium_header_search_placeholder_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#333333',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_medium_header_search_placeholder_color', array(
				'label'	   				=> esc_html__( 'Placeholder Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_medium_style',
				'settings' 				=> 'ocean_medium_header_search_placeholder_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			) ) );

			/**
			 * Medium Header Button Color
			 */
			$wp_customize->add_setting( 'ocean_medium_header_search_button_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#333333',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_medium_header_search_button_color', array(
				'label'	   				=> esc_html__( 'Button Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_medium_style',
				'settings' 				=> 'ocean_medium_header_search_button_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			) ) );

			/**
			 * Medium Header Button Hover Color
			 */
			$wp_customize->add_setting( 'ocean_medium_header_search_button_hover_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#13aff0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_medium_header_search_button_hover_color', array(
				'label'	   				=> esc_html__( 'Button Hover Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_medium_style',
				'settings' 				=> 'ocean_medium_header_search_button_hover_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_medium_header_style',
			) ) );

			/**
			 * Section where header images options are automatically added
			 */
			$wp_customize->add_section( 'header_image' , array(
				'title' 			=> esc_html__( 'Header Media', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**
			 * Site Background Image Position
			 */
			$wp_customize->add_setting( 'ocean_header_image_position', array(
				'transport' 			=> 'postMessage',
				'default' 				=> 'initial',
				'sanitize_callback' 	=> 'sanitize_text_field',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_header_image_position', array(
				'label'	   				=> esc_html__( 'Position', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'header_image',
				'settings' 				=> 'ocean_header_image_position',
				'priority' 				=> 10,
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
			$wp_customize->add_setting( 'ocean_header_image_attachment', array(
				'transport' 			=> 'postMessage',
				'default' 				=> 'initial',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_header_image_attachment', array(
				'label'	   				=> esc_html__( 'Attachment', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'header_image',
				'settings' 				=> 'ocean_header_image_attachment',
				'priority' 				=> 10,
				'choices' 				=> array(
					'initial' 	=> esc_html__( 'Default', 'oceanwp' ),
					'scroll' 	=> esc_html__( 'Scroll', 'oceanwp' ),
					'fixed' 	=> esc_html__( 'Fixed', 'oceanwp' ),
				),
			) ) );

			/**
			 * Site Background Image Repeat
			 */
			$wp_customize->add_setting( 'ocean_header_image_repeat', array(
				'transport' 			=> 'postMessage',
				'default' 				=> 'initial',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_header_image_repeat', array(
				'label'	   				=> esc_html__( 'Repeat', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'header_image',
				'settings' 				=> 'ocean_header_image_repeat',
				'priority' 				=> 10,
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
			$wp_customize->add_setting( 'ocean_header_image_size', array(
				'transport' 			=> 'postMessage',
				'default' 				=> 'initial',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_header_image_size', array(
				'label'	   				=> esc_html__( 'Size', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'header_image',
				'settings' 				=> 'ocean_header_image_size',
				'priority' 				=> 10,
				'choices' 				=> array(
					'initial' 	=> esc_html__( 'Default', 'oceanwp' ),
					'auto' 		=> esc_html__( 'Auto', 'oceanwp' ),
					'cover' 	=> esc_html__( 'Cover', 'oceanwp' ),
					'contain' 	=> esc_html__( 'Contain', 'oceanwp' ),
				),
			) ) );

			/**
			 * Section
			 */
			$wp_customize->add_section( 'ocean_header_logo' , array(
				'title' 			=> esc_html__( 'Logo', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**
			 * Header Retina Logo
			 */
			$wp_customize->add_setting( 'ocean_retina_logo', array(
				'default'           	=> '',
				'sanitize_callback' 	=> 'oceanwp_sanitize_image',
			) );

			$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ocean_retina_logo', array(
				'label'	   				=> esc_html__( 'Retina Logo', 'oceanwp' ),
				'section'  				=> 'ocean_header_logo',
				'settings' 				=> 'ocean_retina_logo',
				'priority' 				=> 10,
			) ) );

			/**
			 * Header Logo Height
			 */
			$wp_customize->add_setting( 'ocean_logo_height', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_logo_height', array(
				'label'	   				=> esc_html__( 'Height (px)', 'oceanwp' ),
				'description' 			=> esc_html__( 'Used for retina.', 'oceanwp' ),
				'type' 					=> 'number',
				'section'  				=> 'ocean_header_logo',
				'settings' 				=> 'ocean_logo_height',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_custom_logo',
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 100,
			    ),
			) ) );

			/**
			 * Header Logo Max Width
			 */
			$wp_customize->add_setting( 'ocean_logo_max_width', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_logo_max_width_tablet', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_setting( 'ocean_logo_max_width_mobile', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Slider_Control( $wp_customize, 'ocean_logo_max_width', array(
				'label'	   				=> esc_html__( 'Max Width (px)', 'oceanwp' ),
				'section'  				=> 'ocean_header_logo',
				'settings' => array(
		            'desktop' 	=> 'ocean_logo_max_width',
		            'tablet' 	=> 'ocean_logo_max_width_tablet',
		            'mobile' 	=> 'ocean_logo_max_width_mobile',
			    ),
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_custom_logo',
			    'input_attrs' 			=> array(
			        'min'   => 10,
			        'max'   => 500,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Header Logo Color
			 */
			$wp_customize->add_setting( 'ocean_logo_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#333333',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_logo_color', array(
				'label'	   				=> esc_html__( 'Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_logo',
				'settings' 				=> 'ocean_logo_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_hasnt_custom_logo',
			) ) );

			/**
			 * Header Logo Hover Color
			 */
			$wp_customize->add_setting( 'ocean_logo_hover_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#13aff0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_logo_hover_color', array(
				'label'	   				=> esc_html__( 'Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_header_logo',
				'settings' 				=> 'ocean_logo_hover_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_hasnt_custom_logo',
			) ) );

			/**
			 * Section
			 */
			$wp_customize->add_section( 'ocean_header_menu' , array(
				'title' 			=> esc_html__( 'Menu', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**
			 * Menu Top Level Dropdown Icon
			 */
			$wp_customize->add_setting( 'ocean_menu_arrow_down', array(
				'default'           	=> true,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_menu_arrow_down', array(
				'label'	   				=> esc_html__( 'Top Level Dropdown Icon', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_menu_arrow_down',
				'priority' 				=> 10,
			) ) );

			/**
			 * Menu Second+ Level Dropdown Icon
			 */
			$wp_customize->add_setting( 'ocean_menu_arrow_side', array(
				'default'           	=> true,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_menu_arrow_side', array(
				'label'	   				=> esc_html__( 'Second+ Level Dropdown Icon', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_menu_arrow_side',
				'priority' 				=> 10,
			) ) );

			/**
			 * Menu Dropdown Top Border
			 */
			$wp_customize->add_setting( 'ocean_menu_dropdown_top_border', array(
				'transport' 			=> 'postMessage',
				'default'           	=> true,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_menu_dropdown_top_border', array(
				'label'	   				=> esc_html__( 'Dropdown Top Border', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_menu_dropdown_top_border',
				'priority' 				=> 10,
			) ) );

			/**
			 * Menu Position
			 */
			$wp_customize->add_setting( 'ocean_menu_position', array(
				'transport' 			=> 'postMessage',
				'default'           	=> 'right-menu',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Buttonset_Control( $wp_customize, 'ocean_menu_position', array(
				'label'	   				=> esc_html__( 'Position', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_menu_position',
				'priority' 				=> 10,
				'choices' 				=> array(
					'left-menu' 	=> esc_html__( 'Left', 'oceanwp' ),
					'center-menu' 	=> esc_html__( 'Center', 'oceanwp' ),
					'right-menu' 	=> esc_html__( 'Right', 'oceanwp' ),
				),
				'active_callback' 		=> 'oceanwp_cac_hasnt_header_styles',
			) ) );

			/**
			 * Main Styling Heading
			 */
			$wp_customize->add_setting( 'ocean_menu_main_styling_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_menu_main_styling_heading', array(
				'label'    				=> esc_html__( 'Main Styling', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'priority' 				=> 10,
			) ) );

			/**
			 * Menu Items Padding
			 */
			$wp_customize->add_setting( 'ocean_menu_items_padding', array(
				'transport' 			=> 'postMessage',
				'default'     			=> '15',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_menu_items_padding', array(
				'label'	   				=> esc_html__( 'Left/Right Padding (px)', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_menu_items_padding',
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 50,
			        'step'  => 1,
			    ),
				'active_callback' 		=> 'oceanwp_cac_hasnt_header_styles',
			) ) );

			/**
			 * Menu Link Color
			 */
			$wp_customize->add_setting( 'ocean_menu_link_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#555555',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_menu_link_color', array(
				'label'	   				=> esc_html__( 'Link Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_menu_link_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Menu Link Color Hover
			 */
			$wp_customize->add_setting( 'ocean_menu_link_color_hover', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#13aff0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_menu_link_color_hover', array(
				'label'	   				=> esc_html__( 'Link Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_menu_link_color_hover',
				'priority' 				=> 10,
			) ) );

			/**
			 * Menu Link Active Color
			 */
			$wp_customize->add_setting( 'ocean_menu_link_color_active', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#555555',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_menu_link_color_active', array(
				'label'	   				=> esc_html__( 'Link Color: Current Menu Item', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_menu_link_color_active',
				'priority' 				=> 10,
			) ) );

			/**
			 * Menu Link Background Color
			 */
			$wp_customize->add_setting( 'ocean_menu_link_background', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_menu_link_background', array(
				'label'	   				=> esc_html__( 'Link Background', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_menu_link_background',
				'priority' 				=> 10,
			) ) );

			/**
			 * Menu Link Hover Background Color
			 */
			$wp_customize->add_setting( 'ocean_menu_link_hover_background', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_menu_link_hover_background', array(
				'label'	   				=> esc_html__( 'Link Background: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_menu_link_hover_background',
				'priority' 				=> 10,
			) ) );

			/**
			 * Menu Link Background Current Menu Item
			 */
			$wp_customize->add_setting( 'ocean_menu_link_active_background', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_menu_link_active_background', array(
				'label'	   				=> esc_html__( 'Link Background: Current Menu Item', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_menu_link_active_background',
				'priority' 				=> 10,
			) ) );

			/**
			 * Dropdowns Styling Heading
			 */
			$wp_customize->add_setting( 'ocean_menu_dropdowns_styling_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_menu_dropdowns_styling_heading', array(
				'label'    				=> esc_html__( 'Dropdowns Styling', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'priority' 				=> 10,
			) ) );

			/**
			 * Dropdowns Width
			 */
			$wp_customize->add_setting( 'ocean_dropdown_width', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '180',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Range_Control( $wp_customize, 'ocean_dropdown_width', array(
				'label'	   				=> esc_html__( 'Width (px)', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_dropdown_width',
				'priority' 				=> 10,
			    'input_attrs' 			=> array(
			        'min'   => 30,
			        'max'   => 500,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Dropdown Background Color
			 */
			$wp_customize->add_setting( 'ocean_dropdown_menu_background', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_dropdown_menu_background', array(
				'label'	   				=> esc_html__( 'Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_dropdown_menu_background',
				'priority' 				=> 10,
			) ) );

			/**
			 * Dropdown Top Border Color
			 */
			$wp_customize->add_setting( 'ocean_dropdown_menu_top_border', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#13aff0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_dropdown_menu_top_border', array(
				'label'	   				=> esc_html__( 'Top Border Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_dropdown_menu_top_border',
				'priority' 				=> 10,
			) ) );

			/**
			 * Dropdown Borders Color
			 */
			$wp_customize->add_setting( 'ocean_dropdown_menu_borders', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#f1f1f1',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_dropdown_menu_borders', array(
				'label'	   				=> esc_html__( 'Borders Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_dropdown_menu_borders',
				'priority' 				=> 10,
			) ) );

			/**
			 * Dropdown Link Color
			 */
			$wp_customize->add_setting( 'ocean_dropdown_menu_link_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#333333',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_dropdown_menu_link_color', array(
				'label'	   				=> esc_html__( 'Link Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_dropdown_menu_link_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Dropdown Link Hover Color
			 */
			$wp_customize->add_setting( 'ocean_dropdown_menu_link_color_hover', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#555555',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_dropdown_menu_link_color_hover', array(
				'label'	   				=> esc_html__( 'Link Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_dropdown_menu_link_color_hover',
				'priority' 				=> 10,
			) ) );

			/**
			 * Dropdown Link Hover Background Color
			 */
			$wp_customize->add_setting( 'ocean_dropdown_menu_link_hover_bg', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#f8f8f8',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_dropdown_menu_link_hover_bg', array(
				'label'	   				=> esc_html__( 'Link Background: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_dropdown_menu_link_hover_bg',
				'priority' 				=> 10,
			) ) );

			/**
			 * Dropdown Link Active Color
			 */
			$wp_customize->add_setting( 'ocean_dropdown_menu_link_color_active', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_dropdown_menu_link_color_active', array(
				'label'	   				=> esc_html__( 'Link Color: Current Menu Item', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_dropdown_menu_link_color_active',
				'priority' 				=> 10,
			) ) );

			/**
			 * Dropdown Link Active Background Color
			 */
			$wp_customize->add_setting( 'ocean_dropdown_menu_link_bg_active', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_dropdown_menu_link_bg_active', array(
				'label'	   				=> esc_html__( 'Link Background: Current Menu Item', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_dropdown_menu_link_bg_active',
				'priority' 				=> 10,
			) ) );



			/**
			 * Search Heading
			 */
			$wp_customize->add_setting( 'ocean_menu_search_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_menu_search_heading', array(
				'label'    				=> esc_html__( 'Search Icon', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'priority' 				=> 10,
			) ) );

			/**
			 * Search Icon Style
			 */
			$wp_customize->add_setting( 'ocean_menu_search_style', array(
				'default'           	=> 'drop_down',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_menu_search_style', array(
				'label'	   				=> esc_html__( 'Search Icon Style', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_menu_search_style',
				'priority' 				=> 10,
				'choices' 				=> array(
					'disabled' 			=> esc_html__( 'Disabled','oceanwp' ),
					'drop_down' 		=> esc_html__( 'Drop Down','oceanwp' ),
					'header_replace' 	=> esc_html__( 'Header Replace','oceanwp' ),
					'overlay' 			=> esc_html__( 'Overlay','oceanwp' ),
				),
			) ) );

			/**
			 * Search Dropdown Input Background Color
			 */
			$wp_customize->add_setting( 'ocean_search_dropdown_input_background', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_search_dropdown_input_background', array(
				'label'	   				=> esc_html__( 'Input Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_search_dropdown_input_background',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_search_dropdown',
			) ) );

			/**
			 * Search Dropdown Input Color
			 */
			$wp_customize->add_setting( 'ocean_search_dropdown_input_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#333333',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_search_dropdown_input_color', array(
				'label'	   				=> esc_html__( 'Input Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_search_dropdown_input_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_search_dropdown',
			) ) );

			/**
			 * Search Dropdown Input Border Color
			 */
			$wp_customize->add_setting( 'ocean_search_dropdown_input_border', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#dddddd',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_search_dropdown_input_border', array(
				'label'	   				=> esc_html__( 'Input Border Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_search_dropdown_input_border',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_search_dropdown',
			) ) );

			/**
			 * Search Dropdown Input Focus Border Color
			 */
			$wp_customize->add_setting( 'ocean_search_dropdown_input_border_focus', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#bbbbbb',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_search_dropdown_input_border_focus', array(
				'label'	   				=> esc_html__( 'Input Border Color: Focus', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_search_dropdown_input_border_focus',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_search_dropdown',
			) ) );

			/**
			 * Search Overlay Background Color
			 */
			$wp_customize->add_setting( 'ocean_search_overlay_bg', array(
				'transport' 			=> 'postMessage',
				'default' 				=> 'rgba(0,0,0,0.9)',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_search_overlay_bg', array(
				'label'	   				=> esc_html__( 'Overlay Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_search_overlay_bg',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_search_overlay',
			) ) );

			/**
			 * Search Overlay Input Color
			 */
			$wp_customize->add_setting( 'ocean_search_overlay_input_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_search_overlay_input_color', array(
				'label'	   				=> esc_html__( 'Input Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_search_overlay_input_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_search_overlay',
			) ) );

			/**
			 * Search Overlay Input Placeholder Color
			 */
			$wp_customize->add_setting( 'ocean_search_overlay_placeholder_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_search_overlay_placeholder_color', array(
				'label'	   				=> esc_html__( 'Input Placeholder Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_search_overlay_placeholder_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_search_overlay',
			) ) );

			/**
			 * Search Overlay Input Dashed Text Color
			 */
			$wp_customize->add_setting( 'ocean_search_overlay_input_dashed_bg', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_search_overlay_input_dashed_bg', array(
				'label'	   				=> esc_html__( 'Input Dashed Text Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_search_overlay_input_dashed_bg',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_search_overlay',
			) ) );

			/**
			 * Search Overlay Input Border Color
			 */
			$wp_customize->add_setting( 'ocean_search_overlay_input_border_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#444444',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_search_overlay_input_border_color', array(
				'label'	   				=> esc_html__( 'Input Border Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_search_overlay_input_border_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_search_overlay',
			) ) );

			/**
			 * Search Overlay Input Hover Border Color
			 */
			$wp_customize->add_setting( 'ocean_search_overlay_input_hover_border_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#777777',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_search_overlay_input_hover_border_color', array(
				'label'	   				=> esc_html__( 'Input Border Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_search_overlay_input_hover_border_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_search_overlay',
			) ) );

			/**
			 * Search Overlay Input Focus Border Color
			 */
			$wp_customize->add_setting( 'ocean_search_overlay_input_focus_border_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_search_overlay_input_focus_border_color', array(
				'label'	   				=> esc_html__( 'Input Border Color: Focus', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_search_overlay_input_focus_border_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_search_overlay',
			) ) );

			/**
			 * Search Overlay Close Button Color
			 */
			$wp_customize->add_setting( 'ocean_search_overlay_close_button_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_search_overlay_close_button_color', array(
				'label'	   				=> esc_html__( 'Close Button Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_search_overlay_close_button_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_search_overlay',
			) ) );

			/**
			 * Dropdown Category Heading
			 */
			$wp_customize->add_setting( 'ocean_menu_dropdown_category_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_menu_dropdown_category_heading', array(
				'label'    				=> esc_html__( 'Dropdowns Categories Posts', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'priority' 				=> 10,
			) ) );

			/**
			 * Dropdown Category Title Background Color
			 */
			$wp_customize->add_setting( 'ocean_dropdown_category_title_bg', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#f8f8f8',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_dropdown_category_title_bg', array(
				'label'	   				=> esc_html__( 'Category Title: Background', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_dropdown_category_title_bg',
				'priority' 				=> 10,
			) ) );

			/**
			 * Dropdown Category Title Color
			 */
			$wp_customize->add_setting( 'ocean_dropdown_category_title_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#222222',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_dropdown_category_title_color', array(
				'label'	   				=> esc_html__( 'Category Title: Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_dropdown_category_title_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Dropdown Category Posts Links Color
			 */
			$wp_customize->add_setting( 'ocean_dropdown_category_links_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#555555',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_dropdown_category_links_color', array(
				'label'	   				=> esc_html__( 'Posts Links: Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_dropdown_category_links_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Dropdown Category Posts Links Hover Color
			 */
			$wp_customize->add_setting( 'ocean_dropdown_category_links_hover_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#333333',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_dropdown_category_links_hover_color', array(
				'label'	   				=> esc_html__( 'Posts Links Hover: Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_dropdown_category_links_hover_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Dropdown Category Posts Date Color
			 */
			$wp_customize->add_setting( 'ocean_dropdown_category_date_color', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '#bbbbbb',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_dropdown_category_date_color', array(
				'label'	   				=> esc_html__( 'Posts Date: Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu',
				'settings' 				=> 'ocean_dropdown_category_date_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Section
			 */
			$wp_customize->add_section( 'ocean_header_menu_social' , array(
				'title' 			=> esc_html__( 'Menu Social', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**
			 * Menu Social
			 */
			$wp_customize->add_setting( 'ocean_menu_social', array(
				'default'           	=> false,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_menu_social', array(
				'label'	   				=> esc_html__( 'Enable Menu Social', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> 'ocean_header_menu_social',
				'settings' 				=> 'ocean_menu_social',
				'priority' 				=> 10,
			) ) );

			/**
			 * Menu Social Style
			 */
			$wp_customize->add_setting( 'ocean_menu_social_style', array(
				'transport'           	=> 'postMessage',
				'default'           	=> 'simple',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_menu_social_style', array(
				'label'	   				=> esc_html__( 'Social Link Style', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_header_menu_social',
				'settings' 				=> 'ocean_menu_social_style',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_social',
				'choices' 				=> array(
					'simple' 	=> esc_html__( 'Simple', 'oceanwp' ),
					'colored' 	=> esc_html__( 'Colored', 'oceanwp' ),
					'minimal'	=> esc_html__( 'Minimal', 'oceanwp' ),
					'dark'		=> esc_html__( 'Dark', 'oceanwp' ),
				),
			) ) );

			/**
			 * Menu Social Target
			 */
			$wp_customize->add_setting( 'ocean_menu_social_target', array(
				'transport'           	=> 'postMessage',
				'default'           	=> 'blank',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_menu_social_target', array(
				'label'	   				=> esc_html__( 'Social Link Target', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> 'ocean_header_menu_social',
				'settings' 				=> 'ocean_menu_social_target',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_social',
				'choices' 				=> array(
					'blank' 	=> esc_html__( 'New Window', 'oceanwp' ),
					'self' 		=> esc_html__( 'Same Window', 'oceanwp' ),
				),
			) ) );

			/**
			 * Menu Social Font Size
			 */
			$wp_customize->add_setting( 'ocean_menu_social_font_size', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_setting( 'ocean_menu_social_tablet_font_size', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_setting( 'ocean_menu_social_mobile_font_size', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Slider_Control( $wp_customize, 'ocean_menu_social_font_size', array(
				'label' 			=> esc_html__( 'Font Size (px)', 'oceanwp' ),
				'section'  			=> 'ocean_header_menu_social',
				'settings' => array(
		            'desktop' 	=> 'ocean_menu_social_font_size',
		            'tablet' 	=> 'ocean_menu_social_tablet_font_size',
		            'mobile' 	=> 'ocean_menu_social_mobile_font_size',
			    ),
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_social',
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 100,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Menu Social Padding
			 */
			$wp_customize->add_setting( 'ocean_menu_social_right_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_menu_social_left_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_setting( 'ocean_menu_social_tablet_right_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_menu_social_tablet_left_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_setting( 'ocean_menu_social_mobile_right_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_menu_social_mobile_left_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Dimensions_Control( $wp_customize, 'ocean_menu_social_padding', array(
				'label'	   				=> esc_html__( 'Padding (px)', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu_social',
				'settings' => array(
		            'desktop_right' 	=> 'ocean_menu_social_right_padding',
		            'desktop_left' 		=> 'ocean_menu_social_left_padding',
		            'tablet_right' 		=> 'ocean_menu_social_tablet_right_padding',
		            'tablet_left' 		=> 'ocean_menu_social_tablet_left_padding',
		            'mobile_right' 		=> 'ocean_menu_social_mobile_right_padding',
		            'mobile_left' 		=> 'ocean_menu_social_mobile_left_padding',
			    ),
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_social',
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 60,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Menu Social Link Color
			 */
			$wp_customize->add_setting( 'ocean_menu_social_links_color', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_menu_social_links_color', array(
				'label'	   				=> esc_html__( 'Simple Social Links Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu_social',
				'settings' 				=> 'ocean_menu_social_links_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_social',
			) ) );

			/**
			 * Menu Social Link Hover Color
			 */
			$wp_customize->add_setting( 'ocean_menu_social_hover_links_color', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_menu_social_hover_links_color', array(
				'label'	   				=> esc_html__( 'Simple Social Links Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_header_menu_social',
				'settings' 				=> 'ocean_menu_social_hover_links_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_menu_social',
			) ) );

			/**
			 * Menu Social Settings
			 */
			$social_options = oceanwp_social_options();
			foreach ( $social_options as $key => $val ) {
				if ( 'skype' == $key ) {
					$sanitize = 'wp_filter_nohtml_kses';
				} else if ( 'email' == $key ) {
					$sanitize = 'sanitize_email';
				} else {
					$sanitize = 'esc_url_raw';
				}
				
				$wp_customize->add_setting( 'ocean_menu_social_profiles[' . $key .']', array(
					'sanitize_callback' 	=> $sanitize,
				) );

				$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_menu_social_profiles[' . $key .']', array(
					'label'	   				=> esc_html( $val['label'] ),
					'type' 					=> 'text',
					'section'  				=> 'ocean_header_menu_social',
					'settings' 				=> 'ocean_menu_social_profiles[' . $key .']',
					'priority' 				=> 10,
					'active_callback' 		=> 'oceanwp_cac_has_menu_social',
				) ) );
			}

			/**
			 * Section
			 */
			$wp_customize->add_section( 'ocean_header_mobile_menu' , array(
				'title' 			=> esc_html__( 'Mobile Menu', 'oceanwp' ),
				'priority' 			=> 10,
				'panel' 			=> $panel,
			) );

			/**
			 * Display Menu Text
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_display_opening_text', array(
				'default'           	=> true,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_mobile_menu_display_opening_text', array(
				'label'	   				=> esc_html__( 'Display Menu Text', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_display_opening_text',
				'priority' 				=> 10,
			) ) );

			/**
			 * Menu Text
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_text', array(
				'default'           	=> esc_html__( 'Menu', 'oceanwp' ),
				'transport'           	=> 'postMessage',
				'sanitize_callback' 	=> 'wp_kses_post',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_mobile_menu_text', array(
				'label'	   				=> esc_html__( 'Menu Text', 'oceanwp' ),
				'type' 					=> 'text',
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_text',
				'priority' 				=> 10,
			) ) );

			/**
			 * Hamburger Icon Class
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_open_icon', array(
				'default'           	=> 'fa fa-bars',
				'transport'           	=> 'postMessage',
				'sanitize_callback' 	=> 'wp_filter_nohtml_kses',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_mobile_menu_open_icon', array(
				'label'	   				=> esc_html__( 'Hamburger Icon Class', 'oceanwp' ),
				'description'	   		=> esc_html__( 'Enter the full icon class', 'oceanwp' ),
				'type' 					=> 'text',
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_open_icon',
				'priority' 				=> 10,
			) ) );

			/**
			 * Mobile Menu Panel
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_mobile_menu_heading', array(
				'label'    				=> esc_html__( 'Mobile Sidebar Menu', 'oceanwp' ),
				'section'  				=> 'ocean_header_mobile_menu',
				'priority' 				=> 10,
			) ) );

			/**
			 * Mobile Menu Direction
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_sidr_direction', array(
				'default'           	=> 'left',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Buttonset_Control( $wp_customize, 'ocean_mobile_menu_sidr_direction', array(
				'label'	   				=> esc_html__( 'Direction', 'oceanwp' ),
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_sidr_direction',
				'priority' 				=> 10,
				'choices' 				=> array(
					'left' 	=> esc_html__( 'Left', 'oceanwp' ),
					'right' => esc_html__( 'Right', 'oceanwp' ),
				),
			) ) );

			/**
			 * Mobile Menu Displace
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_sidr_displace', array(
				'default'           	=> true,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_mobile_menu_sidr_displace', array(
				'label'	   				=> esc_html__( 'Displace', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_sidr_displace',
				'priority' 				=> 10,
			) ) );

			/**
			 * Close Menu Button
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_close_btn', array(
				'default'           	=> true,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_mobile_menu_close_btn', array(
				'label'	   				=> esc_html__( 'Close Menu Button', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_close_btn',
				'priority' 				=> 10,
			) ) );

			/**
			 * Mobile Menu Search
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_search', array(
				'default'           	=> true,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_mobile_menu_search', array(
				'label'	   				=> esc_html__( 'Mobile Menu Search', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_search',
				'priority' 				=> 10,
			) ) );

			/**
			 * Close Menu Button Icon Class
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_close_btn_icon', array(
				'default'           	=> 'icon-close',
				'transport'           	=> 'postMessage',
				'sanitize_callback' 	=> 'wp_filter_nohtml_kses',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_mobile_menu_close_btn_icon', array(
				'label'	   				=> esc_html__( 'Close Menu Button Icon Class', 'oceanwp' ),
				'description'	   		=> esc_html__( 'Enter the full icon class', 'oceanwp' ),
				'type' 					=> 'text',
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_close_btn_icon',
				'priority' 				=> 10,
			) ) );

			/**
			 * Close Menu Button Text
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_close_btn_text', array(
				'default'           	=> esc_html__( 'Close Menu', 'oceanwp' ),
				'transport'           	=> 'postMessage',
				'sanitize_callback' 	=> 'wp_kses_post',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_mobile_menu_close_btn_text', array(
				'label'	   				=> esc_html__( 'Close Menu Button Text', 'oceanwp' ),
				'type' 					=> 'text',
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_close_btn_text',
				'priority' 				=> 10,
			) ) );

			/**
			 * Mobile Menu Dropdown Target
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_sidr_dropdown_target', array(
				'default'           	=> 'icon',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Buttonset_Control( $wp_customize, 'ocean_mobile_menu_sidr_dropdown_target', array(
				'label'	   				=> esc_html__( 'Dropdown Target', 'oceanwp' ),
				'description'	   		=> esc_html__( 'Choose your opening target for your submenus', 'oceanwp' ),
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_sidr_dropdown_target',
				'priority' 				=> 10,
				'choices' 				=> array(
					'icon' 	=> esc_html__( 'Icon', 'oceanwp' ),
					'link' 	=> esc_html__( 'Link', 'oceanwp' ),
				),
			) ) );

			/**
			 * Mobile Menu Styling
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_styling_heading', array(
				'sanitize_callback' 	=> 'wp_kses',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Heading_Control( $wp_customize, 'ocean_mobile_menu_styling_heading', array(
				'label'    				=> esc_html__( 'Styling: Mobile Sidebar Menu', 'oceanwp' ),
				'section'  				=> 'ocean_header_mobile_menu',
				'priority' 				=> 10,
			) ) );

			/**
			 * Mobile Menu Close Button Background
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_sidr_close_button_background', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#f8f8f8',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_mobile_menu_sidr_close_button_background', array(
				'label'	   				=> esc_html__( 'Close Button Background', 'oceanwp' ),
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_sidr_close_button_background',
				'priority' 				=> 10,
			) ) );

			/**
			 * Mobile Menu Background
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_sidr_background', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_mobile_menu_sidr_background', array(
				'label'	   				=> esc_html__( 'Background Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_sidr_background',
				'priority' 				=> 10,
			) ) );

			/**
			 * Mobile Menu Background
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_sidr_borders', array(
				'transport' 			=> 'postMessage',
				'default' 				=> 'rgba(0,0,0,0.035)',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_mobile_menu_sidr_borders', array(
				'label'	   				=> esc_html__( 'Borders Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_sidr_borders',
				'priority' 				=> 10,
			) ) );

			/**
			 * Mobile Menu Links Color
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_links', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#555555',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_mobile_menu_links', array(
				'label'	   				=> esc_html__( 'Links Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_links',
				'priority' 				=> 10,
			) ) );

			/**
			 * Mobile Menu Links Hover Color
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_links_hover', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#13aff0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_mobile_menu_links_hover', array(
				'label'	   				=> esc_html__( 'Links Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_links_hover',
				'priority' 				=> 10,
			) ) );

			/**
			 * Mobile Menu Background Color
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_sidr_dropdowns_background', array(
				'transport' 			=> 'postMessage',
				'default' 				=> 'rgba(0,0,0,0.02)',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_mobile_menu_sidr_dropdowns_background', array(
				'label'	   				=> esc_html__( 'Dropdowns Menus: Background', 'oceanwp' ),
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_sidr_dropdowns_background',
				'priority' 				=> 10,
			) ) );

			/**
			 * Mobile Menu Searchbar Background
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_sidr_search_bg', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_mobile_menu_sidr_search_bg', array(
				'label'	   				=> esc_html__( 'Searchbar Background', 'oceanwp' ),
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_sidr_search_bg',
				'priority' 				=> 10,
			) ) );

			/**
			 * Mobile Menu Searchbar Color
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_sidr_search_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#333333',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_mobile_menu_sidr_search_color', array(
				'label'	   				=> esc_html__( 'Searchbar Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_sidr_search_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Mobile Menu Searchbar Border Color
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_sidr_search_border_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#dddddd',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_mobile_menu_sidr_search_border_color', array(
				'label'	   				=> esc_html__( 'Searchbar Border Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_sidr_search_border_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Mobile Menu Searchbar Focus Border Color
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_sidr_search_border_color_focus', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#bbbbbb',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_mobile_menu_sidr_search_border_color_focus', array(
				'label'	   				=> esc_html__( 'Searchbar Border Color: Focus', 'oceanwp' ),
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_sidr_search_border_color_focus',
				'priority' 				=> 10,
			) ) );

			/**
			 * Mobile Menu Searchbar Button Color
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_sidr_search_button_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#555555',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_mobile_menu_sidr_search_button_color', array(
				'label'	   				=> esc_html__( 'Searchbar Button Color', 'oceanwp' ),
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_sidr_search_button_color',
				'priority' 				=> 10,
			) ) );

			/**
			 * Mobile Menu Searchbar Hover Button Color
			 */
			$wp_customize->add_setting( 'ocean_mobile_menu_sidr_search_button_hover_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#222222',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_mobile_menu_sidr_search_button_hover_color', array(
				'label'	   				=> esc_html__( 'Searchbar Button Color: Hover', 'oceanwp' ),
				'section'  				=> 'ocean_header_mobile_menu',
				'settings' 				=> 'ocean_mobile_menu_sidr_search_button_hover_color',
				'priority' 				=> 10,
			) ) );

		}

		/**
		 * Get CSS
		 *
		 * @since 1.0.0
		 */
		public static function head_css( $output ) {

			// Get header style
			$header_style 												= oceanwp_header_style();
		
			// Global vars
			$header_height 												= get_theme_mod( 'ocean_header_height', '74' );
			$top_height 												= get_theme_mod( 'ocean_top_header_height', '40' );
			$header_background 											= get_theme_mod( 'ocean_header_background', '#ffffff' );
			$header_border_bottom 										= get_theme_mod( 'ocean_header_border_bottom', '#f1f1f1' );
			$header_top_padding											= get_theme_mod( 'ocean_header_top_padding', '0' );
			$header_right_padding										= get_theme_mod( 'ocean_header_right_padding', '0' );
			$header_bottom_padding										= get_theme_mod( 'ocean_header_bottom_padding', '0' );
			$header_left_padding										= get_theme_mod( 'ocean_header_left_padding', '0' );
			$tablet_header_top_padding 									= get_theme_mod( 'ocean_header_tablet_top_padding' );
			$tablet_header_right_padding 								= get_theme_mod( 'ocean_header_tablet_right_padding' );
			$tablet_header_bottom_padding 								= get_theme_mod( 'ocean_header_tablet_bottom_padding' );
			$tablet_header_left_padding 								= get_theme_mod( 'ocean_header_tablet_left_padding' );
			$mobile_header_top_padding 									= get_theme_mod( 'ocean_header_mobile_top_padding' );
			$mobile_header_right_padding 								= get_theme_mod( 'ocean_header_mobile_right_padding' );
			$mobile_header_bottom_padding 								= get_theme_mod( 'ocean_header_mobile_bottom_padding' );
			$mobile_header_left_padding 								= get_theme_mod( 'ocean_header_mobile_left_padding' );
			$ransparent_header_bg										= get_theme_mod( 'ocean_transparent_header_bg' );
			$top_header_menu_background 								= get_theme_mod( 'ocean_top_header_menu_background', '#ffffff' );
			$top_header_search_button_border_color 						= get_theme_mod( 'ocean_top_header_search_button_border_color', '#f1f1f1' );
			$top_header_search_button_color 							= get_theme_mod( 'ocean_top_header_search_button_color', '#333333' );
			$top_header_search_button_hover_color 						= get_theme_mod( 'ocean_top_header_search_button_hover_color', '#13aff0' );
			$full_screen_header_menu_bar_color 							= get_theme_mod( 'ocean_full_screen_header_menu_bar_color', '#333333' );
			$full_screen_header_menu_bar_close_color 					= get_theme_mod( 'ocean_full_screen_header_menu_bar_close_color', '#ffffff' );
			$full_screen_header_bg 										= get_theme_mod( 'ocean_full_screen_header_bg', 'rgba(0,0,0,0.9)' );
			$full_screen_header_links_bg_color 							= get_theme_mod( 'ocean_full_screen_header_links_bg_color' );
			$full_screen_header_links_hover_bg_color 					= get_theme_mod( 'ocean_full_screen_header_links_hover_bg_color' );
			$full_screen_header_links_color 							= get_theme_mod( 'ocean_full_screen_header_links_color', '#ffffff' );
			$full_screen_header_links_hover_color 						= get_theme_mod( 'ocean_full_screen_header_links_hover_color', '#929292' );
			$full_screen_header_search_color 							= get_theme_mod( 'ocean_full_screen_header_search_color', '#ffffff' );
			$full_screen_header_search_dashed_bg 						= get_theme_mod( 'ocean_full_screen_header_search_dashed_bg', '#ffffff' );
			$full_screen_header_search_border_color 					= get_theme_mod( 'ocean_full_screen_header_search_border_color', '#666666' );
			$full_screen_header_search_hover_border_color 				= get_theme_mod( 'ocean_full_screen_header_search_hover_border_color', '#ffffff' );
			$full_screen_header_search_focus_border_color 				= get_theme_mod( 'ocean_full_screen_header_search_focus_border_color', '#ffffff' );
			$medium_header_top_header_top_padding 						= get_theme_mod( 'ocean_medium_header_top_header_top_padding', '30' );
			$medium_header_top_header_bottom_padding 					= get_theme_mod( 'ocean_medium_header_top_header_bottom_padding', '30' );
			$medium_header_top_header_tablet_top_padding 				= get_theme_mod( 'ocean_medium_header_top_header_tablet_top_padding' );
			$medium_header_top_header_tablet_bottom_padding 			= get_theme_mod( 'ocean_medium_header_top_header_tablet_bottom_padding' );
			$medium_header_top_header_mobile_top_padding 				= get_theme_mod( 'ocean_medium_header_top_header_mobile_top_padding' );
			$medium_header_top_header_mobile_bottom_padding 			= get_theme_mod( 'ocean_medium_header_top_header_mobile_bottom_padding' );
			$medium_header_sticky_top_header_top_padding 				= get_theme_mod( 'ocean_medium_header_sticky_top_header_top_padding', '20' );
			$medium_header_sticky_top_header_bottom_padding 			= get_theme_mod( 'ocean_medium_header_sticky_top_header_bottom_padding', '20' );
			$medium_header_sticky_top_header_tablet_top_padding 		= get_theme_mod( 'ocean_medium_header_sticky_top_header_tablet_top_padding' );
			$medium_header_sticky_top_header_tablet_bottom_padding 		= get_theme_mod( 'ocean_medium_header_sticky_top_header_tablet_bottom_padding' );
			$medium_header_sticky_top_header_mobile_top_padding 		= get_theme_mod( 'ocean_medium_header_sticky_top_header_mobile_top_padding' );
			$medium_header_sticky_top_header_mobile_bottom_padding 		= get_theme_mod( 'ocean_medium_header_sticky_top_header_mobile_bottom_padding' );
			$medium_header_menu_height 									= get_theme_mod( 'ocean_medium_header_menu_height', '60' );
			$medium_header_menu_position 								= get_theme_mod( 'ocean_medium_header_menu_position', 'center-menu' );
			$medium_header_menu_items_right_padding 					= get_theme_mod( 'ocean_medium_header_menu_items_right_padding', '22' );
			$medium_header_menu_items_left_padding 						= get_theme_mod( 'ocean_medium_header_menu_items_left_padding', '22' );
			$medium_header_menu_items_tablet_right_padding 				= get_theme_mod( 'ocean_medium_header_menu_items_tablet_right_padding' );
			$medium_header_menu_items_tablet_left_padding 				= get_theme_mod( 'ocean_medium_header_menu_items_tablet_left_padding' );
			$medium_header_menu_items_mobile_right_padding 				= get_theme_mod( 'ocean_medium_header_menu_items_mobile_right_padding' );
			$medium_header_menu_items_mobile_left_padding 				= get_theme_mod( 'ocean_medium_header_menu_items_mobile_left_padding' );
			$medium_header_top_header_bg 								= get_theme_mod( 'ocean_medium_header_top_header_bg', '#ffffff' );
			$medium_header_menu_bg 										= get_theme_mod( 'ocean_medium_header_menu_bg', '#f9f9f9' );
			$medium_header_search_bg 									= get_theme_mod( 'ocean_medium_header_search_bg', '#f5f5f5' );
			$medium_header_search_color 								= get_theme_mod( 'ocean_medium_header_search_color', '#333333' );
			$medium_header_search_placeholder_color 					= get_theme_mod( 'ocean_medium_header_search_placeholder_color', '#333333' );
			$medium_header_search_button_color 							= get_theme_mod( 'ocean_medium_header_search_button_color', '#333333' );
			$medium_header_search_button_hover_color 					= get_theme_mod( 'ocean_medium_header_search_button_hover_color', '#13aff0' );
			$logo_max_width												= get_theme_mod( 'ocean_logo_max_width' );
			$logo_max_width_tablet										= get_theme_mod( 'ocean_logo_max_width_tablet' );
			$logo_max_width_mobile										= get_theme_mod( 'ocean_logo_max_width_mobile' );
			$logo_color 												= get_theme_mod( 'ocean_logo_color', '#333333' );
			$logo_hover_color 											= get_theme_mod( 'ocean_logo_hover_color', '#13aff0' );
			$search_dropdown_input_bg 									= get_theme_mod( 'ocean_search_dropdown_input_background' );
			$search_dropdown_input_color 								= get_theme_mod( 'ocean_search_dropdown_input_color', '#333333' );
			$search_dropdown_input_border 								= get_theme_mod( 'ocean_search_dropdown_input_border', '#dddddd' );
			$search_dropdown_input_border_focus 						= get_theme_mod( 'ocean_search_dropdown_input_border_focus', '#bbbbbb' );
			$search_overlay_bg 											= get_theme_mod( 'ocean_search_overlay_bg', 'rgba(0,0,0,0.9)' );
			$search_overlay_input_color 								= get_theme_mod( 'ocean_search_overlay_input_color', '#ffffff' );
			$search_overlay_placeholder_color 							= get_theme_mod( 'ocean_search_overlay_placeholder_color', '#ffffff' );
			$search_overlay_input_dashed_bg 							= get_theme_mod( 'ocean_search_overlay_input_dashed_bg', '#ffffff' );
			$search_overlay_input_border 								= get_theme_mod( 'ocean_search_overlay_input_border_color', '#444444' );
			$search_overlay_input_border_hover 							= get_theme_mod( 'ocean_search_overlay_input_hover_border_color', '#777777' );
			$search_overlay_input_border_focus 							= get_theme_mod( 'ocean_search_overlay_input_focus_border_color', '#ffffff' );
			$search_overlay_close_button_color 							= get_theme_mod( 'ocean_search_overlay_close_button_color', '#ffffff' );
			$menu_items_padding 										= get_theme_mod( 'ocean_menu_items_padding', '15' );
			$menu_link_color 											= get_theme_mod( 'ocean_menu_link_color', '#555555' );
			$menu_link_color_hover 										= get_theme_mod( 'ocean_menu_link_color_hover', '#13aff0' );
			$menu_link_color_active 									= get_theme_mod( 'ocean_menu_link_color_active', '#555555' );
			$menu_link_background 										= get_theme_mod( 'ocean_menu_link_background' );
			$menu_link_hover_background 								= get_theme_mod( 'ocean_menu_link_hover_background' );
			$menu_link_active_background 								= get_theme_mod( 'ocean_menu_link_active_background' );
			$dropdown_width 											= get_theme_mod( 'ocean_dropdown_width', '180' );
			$dropdown_menu_background 									= get_theme_mod( 'ocean_dropdown_menu_background', '#ffffff' );
			$dropdown_menu_top_border 									= get_theme_mod( 'ocean_dropdown_menu_top_border', '#13aff0' );
			$dropdown_menu_borders 										= get_theme_mod( 'ocean_dropdown_menu_borders', '#f1f1f1' );
			$dropdown_menu_link_color 									= get_theme_mod( 'ocean_dropdown_menu_link_color', '#333333' );
			$dropdown_menu_link_color_hover 							= get_theme_mod( 'ocean_dropdown_menu_link_color_hover', '#555555' );
			$dropdown_menu_link_hover_bg 								= get_theme_mod( 'ocean_dropdown_menu_link_hover_bg', '#f8f8f8' );
			$dropdown_menu_link_color_active 							= get_theme_mod( 'ocean_dropdown_menu_link_color_active' );
			$dropdown_menu_link_bg_active 								= get_theme_mod( 'ocean_dropdown_menu_link_bg_active' );
			$dropdown_category_title_bg 								= get_theme_mod( 'ocean_dropdown_category_title_bg', '#f8f8f8' );
			$dropdown_category_title_color 								= get_theme_mod( 'ocean_dropdown_category_title_color', '#222222' );
			$dropdown_category_links_color 								= get_theme_mod( 'ocean_dropdown_category_links_color', '#555555' );
			$dropdown_category_links_hover_color 						= get_theme_mod( 'ocean_dropdown_category_links_hover_color', '#333333' );
			$dropdown_category_date_color 								= get_theme_mod( 'ocean_dropdown_category_date_color', '#bbbbbb' );
			$menu_social_font_size 										= get_theme_mod( 'ocean_menu_social_font_size' );
			$menu_social_tablet_font_size 								= get_theme_mod( 'ocean_menu_social_tablet_font_size' );
			$menu_social_mobile_font_size 								= get_theme_mod( 'ocean_menu_social_mobile_font_size' );
			$menu_social_right_padding 									= get_theme_mod( 'ocean_menu_social_right_padding' );
			$menu_social_left_padding 									= get_theme_mod( 'ocean_menu_social_left_padding' );
			$menu_social_tablet_right_padding 							= get_theme_mod( 'ocean_menu_social_tablet_right_padding' );
			$menu_social_tablet_left_padding 							= get_theme_mod( 'ocean_menu_social_tablet_left_padding' );
			$menu_social_mobile_right_padding 							= get_theme_mod( 'ocean_menu_social_mobile_right_padding' );
			$menu_social_mobile_left_padding 							= get_theme_mod( 'ocean_menu_social_mobile_left_padding' );
			$menu_social_links_color 									= get_theme_mod( 'ocean_menu_social_links_color' );
			$menu_social_hover_links_color 								= get_theme_mod( 'ocean_menu_social_hover_links_color' );
			$mobile_menu_sidr_close_button_bg 							= get_theme_mod( 'ocean_mobile_menu_sidr_close_button_background', '#f8f8f8' );
			$mobile_menu_sidr_background 								= get_theme_mod( 'ocean_mobile_menu_sidr_background', '#ffffff' );
			$mobile_menu_sidr_borders 									= get_theme_mod( 'ocean_mobile_menu_sidr_borders', 'rgba(0,0,0,0.035)' );
			$mobile_menu_links 											= get_theme_mod( 'ocean_mobile_menu_links', '#555555' );
			$mobile_menu_links_hover 									= get_theme_mod( 'ocean_mobile_menu_links_hover', '#13aff0' );
			$mobile_menu_sidr_dropdowns_bg 								= get_theme_mod( 'ocean_mobile_menu_sidr_dropdowns_background', 'rgba(0,0,0,0.02)' );
			$mobile_menu_sidr_search_bg 								= get_theme_mod( 'ocean_mobile_menu_sidr_search_bg' );
			$mobile_menu_sidr_search_color 								= get_theme_mod( 'ocean_mobile_menu_sidr_search_color', '#333333' );
			$mobile_menu_sidr_search_border_color 						= get_theme_mod( 'ocean_mobile_menu_sidr_search_border_color', '#dddddd' );
			$mobile_menu_sidr_search_border_color_focus 				= get_theme_mod( 'ocean_mobile_menu_sidr_search_border_color_focus', '#bbbbbb' );
			$mobile_menu_sidr_search_button_color 						= get_theme_mod( 'ocean_mobile_menu_sidr_search_button_color', '#555555' );
			$mobile_menu_sidr_search_button_hover_color 				= get_theme_mod( 'ocean_mobile_menu_sidr_search_button_hover_color', '#222222' );

			// Define css var
			$css = '';
			$padding_css = '';
			$tablet_padding_css = '';
			$mobile_padding_css = '';
			$mh_top_header_padding_css = '';
			$mh_top_header_tablet_padding_css = '';
			$mh_top_header_mobile_padding_css = '';
			$mh_sticky_top_header_padding_css = '';
			$mh_sticky_top_header_tablet_padding_css = '';
			$mh_sticky_top_header_mobile_padding_css = '';
			$mh_menu_items_padding_css = '';
			$mh_menu_items_tablet_padding_css = '';
			$mh_menu_items_mobile_padding_css = '';
			$mh_right_menu_padding_css = '';
			$mh_right_menu_tablet_padding_css = '';
			$mh_right_menu_mobile_padding_css = '';
			$mh_left_menu_padding_css = '';
			$mh_left_menu_tablet_padding_css = '';
			$mh_left_menu_mobile_padding_css = '';
			$menu_social_padding_css = '';
			$menu_social_tablet_padding_css = '';
			$menu_social_mobile_padding_css = '';

			// Add header height
			if ( ( 'top' != $header_style && 'medium' != $header_style ) && ! empty( $header_height ) && '74' != $header_height ) {
				if ( 'custom' != $header_style ) {
					$css .= '#site-logo #site-logo-inner,#oceanwp-social-menu .social-menu-inner,#site-header.full_screen-header .menu-bar-inner{height:'. $header_height .'px;}';
				}
				$css .= '#site-navigation-wrap .dropdown-menu > li > a,#oceanwp-mobile-menu-icon a{line-height:'. $header_height .'px;}';
			}

			// Add header height for top header style
			if ( 'top' == $header_style && ! empty( $top_height ) && '40' != $top_height ) {
				$css .= '#site-header.top-header #oceanwp-social-menu,#site-header.top-header #search-toggle{height:'. $top_height .'px;}';
				$css .= '#site-header.top-header #site-navigation-wrap .dropdown-menu > li > a,#site-header.top-header #oceanwp-mobile-menu-icon a{line-height:'. $top_height .'px;}';
			}

			// Header background color
			if ( 'medium' != $header_style && ! empty( $header_background ) && '#ffffff' != $header_background ) {
				$css .= '#site-header,.is-sticky #site-header.transparent-header,#searchform-header-replace{background-color:'. $header_background .';}';
			}

			// Header border color
			if ( ! empty( $header_border_bottom ) && '#f1f1f1' != $header_border_bottom ) {
				$css .= '#site-header{border-color:'. $header_border_bottom .';}';
			}

			if ( 'medium' != $header_style ) {

				// Header top padding
				if ( ! empty( $header_top_padding ) && '0' != $header_top_padding ) {
					$padding_css .= 'padding-top:'. $header_top_padding .'px;';
				}

				// Header right padding
				if ( ! empty( $header_right_padding ) && '0' != $header_right_padding ) {
					$padding_css .= 'padding-right:'. $header_right_padding .'px;';
				}

				// Header bottom padding
				if ( ! empty( $header_bottom_padding ) && '0' != $header_bottom_padding ) {
					$padding_css .= 'padding-bottom:'. $header_bottom_padding .'px;';
				}

				// Header left padding
				if ( ! empty( $header_left_padding ) && '0' != $header_left_padding ) {
					$padding_css .= 'padding-left:'. $header_left_padding .'px;';
				}

				// Header padding css
				if ( ! empty( $header_top_padding ) && '0' != $header_top_padding
					|| ! empty( $header_right_padding ) && '0' != $header_right_padding
					|| ! empty( $header_bottom_padding ) && '0' != $header_bottom_padding
					|| ! empty( $header_left_padding ) && '0' != $header_left_padding ) {
					$css .= '#site-header-inner{'. $padding_css .'}';
				}

				// Tablet header top padding
				if ( ! empty( $tablet_header_top_padding ) ) {
					$tablet_padding_css .= 'padding-top:'. $tablet_header_top_padding .'px;';
				}

				// Tablet header right padding
				if ( ! empty( $tablet_header_right_padding ) ) {
					$tablet_padding_css .= 'padding-right:'. $tablet_header_right_padding .'px;';
				}

				// Tablet header bottom padding
				if ( ! empty( $tablet_header_bottom_padding ) ) {
					$tablet_padding_css .= 'padding-bottom:'. $tablet_header_bottom_padding .'px;';
				}

				// Tablet header left padding
				if ( ! empty( $tablet_header_left_padding ) ) {
					$tablet_padding_css .= 'padding-left:'. $tablet_header_left_padding .'px;';
				}

				// Tablet header padding css
				if ( ! empty( $tablet_header_top_padding )
					|| ! empty( $tablet_header_right_padding )
					|| ! empty( $tablet_header_bottom_padding )
					|| ! empty( $tablet_header_left_padding ) ) {
					$css .= '@media (max-width: 768px){#site-header-inner{'. $tablet_padding_css .'}}';
				}

				// Mobile header top padding
				if ( ! empty( $mobile_header_top_padding ) ) {
					$mobile_padding_css .= 'padding-top:'. $mobile_header_top_padding .'px;';
				}

				// Mobile header right padding
				if ( ! empty( $mobile_header_right_padding ) ) {
					$mobile_padding_css .= 'padding-right:'. $mobile_header_right_padding .'px;';
				}

				// Mobile header bottom padding
				if ( ! empty( $mobile_header_bottom_padding ) ) {
					$mobile_padding_css .= 'padding-bottom:'. $mobile_header_bottom_padding .'px;';
				}

				// Mobile header left padding
				if ( ! empty( $mobile_header_left_padding ) ) {
					$mobile_padding_css .= 'padding-left:'. $mobile_header_left_padding .'px;';
				}

				// Mobile header padding css
				if ( ! empty( $mobile_header_top_padding )
					|| ! empty( $mobile_header_right_padding )
					|| ! empty( $mobile_header_bottom_padding )
					|| ! empty( $mobile_header_left_padding ) ) {
					$css .= '@media (max-width: 480px){#site-header-inner{'. $mobile_padding_css .'}}';
				}

			}

			// Transparent header background color
			if ( 'transparent' == $header_style && ! empty( $ransparent_header_bg ) ) {
				$css .= '#site-header.transparent-header{background-color:'. $ransparent_header_bg .';}';
			}

			// Top menu header menu background color
			if ( 'top' == $header_style && ! empty( $top_header_menu_background ) && '#ffffff' != $top_header_menu_background ) {
				$css .= '#site-header.top-header .header-top,#site-header.top-header #searchform-header-replace{background-color:'. $top_header_menu_background .';}';
			}

			// Top menu header menu background color
			if ( 'top' == $header_style && ! empty( $top_header_search_button_border_color ) && '#f1f1f1' != $top_header_search_button_border_color ) {
				$css .= '#site-header.top-header #search-toggle{border-color:'. $top_header_search_button_border_color .';}';
			}

			// Top menu header menu background color
			if ( 'top' == $header_style && ! empty( $top_header_search_button_color ) && '#333333' != $top_header_search_button_color ) {
				$css .= '#site-header.top-header #search-toggle a{color:'. $top_header_search_button_color .';}';
			}

			// Top menu header menu background color
			if ( 'top' == $header_style && ! empty( $top_header_search_button_hover_color ) && '#13aff0' != $top_header_search_button_hover_color ) {
				$css .= '#site-header.top-header #search-toggle a:hover{color:'. $top_header_search_button_hover_color .';}';
			}

			// Full Screen header menu bar color
			if ( 'full_screen' == $header_style && ! empty( $full_screen_header_menu_bar_color ) && '#333333' != $full_screen_header_menu_bar_color ) {
				$css .= '#site-header.full_screen-header .menu-bar .ham,#site-header.full_screen-header .menu-bar .ham:before,#site-header.full_screen-header .menu-bar .ham:after{background-color:'. $full_screen_header_menu_bar_color .';}';
			}

			// Full Screen header menu bar close color
			if ( 'full_screen' == $header_style && ! empty( $full_screen_header_menu_bar_close_color ) && '#ffffff' != $full_screen_header_menu_bar_close_color ) {
				$css .= '#site-header.full_screen-header .menu-bar.exit .ham:before,#site-header.full_screen-header .menu-bar.exit .ham:after{background-color:'. $full_screen_header_menu_bar_close_color .';}';
			}

			// Full Screen header background color
			if ( 'full_screen' == $header_style && ! empty( $full_screen_header_bg ) && 'rgba(0,0,0,0.9)' != $full_screen_header_bg ) {
				$css .= '#site-header.full_screen-header #full-screen-menu{background-color:'. $full_screen_header_bg .';}';
			}

			// Full Screen header links background color
			if ( 'full_screen' == $header_style && ! empty( $full_screen_header_links_bg_color ) ) {
				$css .= '#site-header.full_screen-header .fs-dropdown-menu li a{background-color:'. $full_screen_header_links_bg_color .';}';
			}

			// Full Screen header links hover background color
			if ( 'full_screen' == $header_style && ! empty( $full_screen_header_links_hover_bg_color ) ) {
				$css .= '#site-header.full_screen-header .fs-dropdown-menu li a:hover{background-color:'. $full_screen_header_links_hover_bg_color .';}';
			}

			// Full Screen header links color
			if ( 'full_screen' == $header_style && ! empty( $full_screen_header_links_color ) && '#ffffff' != $full_screen_header_links_color ) {
				$css .= '#site-header.full_screen-header .fs-dropdown-menu li a{color:'. $full_screen_header_links_color .';}';
			}

			// Full Screen header links hover color
			if ( 'full_screen' == $header_style && ! empty( $full_screen_header_links_hover_color ) && '#929292' != $full_screen_header_links_hover_color ) {
				$css .= '#site-header.full_screen-header .fs-dropdown-menu li a:hover{color:'. $full_screen_header_links_hover_color .';}';
			}

			// Full Screen header search color
			if ( 'full_screen' == $header_style && ! empty( $full_screen_header_search_color ) && '#ffffff' != $full_screen_header_search_color ) {
				$css .= '#site-header.full_screen-header .fs-dropdown-menu>li.search-toggle-li input,#site-header.full_screen-header .fs-dropdown-menu>li.search-toggle-li label{color:'. $full_screen_header_search_color .';}';
			}

			// Full Screen header search dashed background color
			if ( 'full_screen' == $header_style && ! empty( $full_screen_header_search_dashed_bg ) && '#ffffff' != $full_screen_header_search_dashed_bg ) {
				$css .= '#site-header.full_screen-header .fs-dropdown-menu>li.search-toggle-li label i{background-color:'. $full_screen_header_search_dashed_bg .';}';
			}

			// Full Screen header search border color
			if ( 'full_screen' == $header_style && ! empty( $full_screen_header_search_border_color ) && '#666666' != $full_screen_header_search_border_color ) {
				$css .= '#site-header.full_screen-header .fs-dropdown-menu>li.search-toggle-li input{border-color:'. $full_screen_header_search_border_color .';}';
			}

			// Full Screen header search hover border color
			if ( 'full_screen' == $header_style && ! empty( $full_screen_header_search_hover_border_color ) && '#ffffff' != $full_screen_header_search_hover_border_color ) {
				$css .= '#site-header.full_screen-header .fs-dropdown-menu>li.search-toggle-li input:hover{border-color:'. $full_screen_header_search_hover_border_color .';}';
			}

			// Full Screen header search focus border color
			if ( 'full_screen' == $header_style && ! empty( $full_screen_header_search_focus_border_color ) && '#ffffff' != $full_screen_header_search_focus_border_color ) {
				$css .= '#site-header.full_screen-header .fs-dropdown-menu>li.search-toggle-li input:focus{border-color:'. $full_screen_header_search_focus_border_color .';}';
			}

			// Add top header background for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_top_header_bg ) && '#ffffff' != $medium_header_top_header_bg ) {
				$css .= '#site-header.medium-header .top-header-wrap,#searchform-header-replace{background-color:'. $medium_header_top_header_bg .';}';
			}

			// Add top header top padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_top_header_top_padding ) && '30' != $medium_header_top_header_top_padding ) {
				$mh_top_header_padding_css .= 'padding-top:'. $medium_header_top_header_top_padding .'px;';
			}

			// Add top header bottom padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_top_header_bottom_padding ) && '30' != $medium_header_top_header_bottom_padding ) {
				$mh_top_header_padding_css .= 'padding-bottom:'. $medium_header_top_header_bottom_padding .'px;';
			}

			// Top header padding css
			if ( 'medium' == $header_style && ! empty( $medium_header_top_header_top_padding ) && '30' != $medium_header_top_header_top_padding
				|| 'medium' == $header_style && ! empty( $medium_header_top_header_bottom_padding ) && '30' != $medium_header_top_header_bottom_padding ) {
				$css .= '#site-header.medium-header .top-header-wrap{'. $mh_top_header_padding_css .'}';
			}

			// Tablet top header top padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_top_header_tablet_top_padding ) ) {
				$mh_top_header_tablet_padding_css .= 'padding-top:'. $medium_header_top_header_tablet_top_padding .'px;';
			}

			// Tablet top header bottom padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_top_header_tablet_bottom_padding ) ) {
				$mh_top_header_tablet_padding_css .= 'padding-bottom:'. $medium_header_top_header_tablet_bottom_padding .'px;';
			}

			// Tablet top header padding css
			if ( 'medium' == $header_style && ! empty( $medium_header_top_header_tablet_top_padding )
				|| 'medium' == $header_style && ! empty( $medium_header_top_header_tablet_bottom_padding ) ) {
				$css .= '@media (max-width: 768px){#site-header.medium-header .top-header-wrap{'. $mh_top_header_tablet_padding_css .'}}';
			}

			// Mobile top header top padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_top_header_mobile_top_padding ) ) {
				$mh_top_header_mobile_padding_css .= 'padding-top:'. $medium_header_top_header_mobile_top_padding .'px;';
			}

			// Mobile top header bottom padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_top_header_mobile_bottom_padding ) ) {
				$mh_top_header_mobile_padding_css .= 'padding-bottom:'. $medium_header_top_header_mobile_bottom_padding .'px;';
			}

			// Mobile top header padding css
			if ( 'medium' == $header_style && ! empty( $medium_header_top_header_mobile_top_padding )
				|| 'medium' == $header_style && ! empty( $medium_header_top_header_mobile_bottom_padding ) ) {
				$css .= '@media (max-width: 480px){#site-header.medium-header .top-header-wrap{'. $mh_top_header_mobile_padding_css .'}}';
			}

			// Add sticky top header top padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_sticky_top_header_top_padding ) && '20' != $medium_header_sticky_top_header_top_padding ) {
				$mh_sticky_top_header_padding_css .= 'padding-top:'. $medium_header_sticky_top_header_top_padding .'px;';
			}

			// Add sticky top header bottom padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_sticky_top_header_bottom_padding ) && '20' != $medium_header_sticky_top_header_bottom_padding ) {
				$mh_sticky_top_header_padding_css .= 'padding-bottom:'. $medium_header_sticky_top_header_bottom_padding .'px;';
			}

			// Sticky top header padding css
			if ( 'medium' == $header_style && ! empty( $medium_header_sticky_top_header_top_padding ) && '20' != $medium_header_sticky_top_header_top_padding
				|| 'medium' == $header_style && ! empty( $medium_header_sticky_top_header_bottom_padding ) && '20' != $medium_header_sticky_top_header_bottom_padding ) {
				$css .= '.is-sticky #site-header.medium-header .top-header-wrap{'. $mh_sticky_top_header_padding_css .'}';
			}

			// Tablet sticky top header top padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_sticky_top_header_tablet_top_padding ) ) {
				$mh_sticky_top_header_tablet_padding_css .= 'padding-top:'. $medium_header_sticky_top_header_tablet_top_padding .'px;';
			}

			// Tablet sticky top header bottom padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_sticky_top_header_tablet_bottom_padding ) ) {
				$mh_sticky_top_header_tablet_padding_css .= 'padding-bottom:'. $medium_header_sticky_top_header_tablet_bottom_padding .'px;';
			}

			// Tablet sticky top header padding css
			if ( 'medium' == $header_style && ! empty( $medium_header_sticky_top_header_tablet_top_padding )
				|| 'medium' == $header_style && ! empty( $medium_header_sticky_top_header_tablet_bottom_padding ) ) {
				$css .= '@media (max-width: 768px){.is-sticky #site-header.medium-header .top-header-wrap{'. $mh_sticky_top_header_tablet_padding_css .'}}';
			}

			// Mobile sticky top header top padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_sticky_top_header_mobile_top_padding ) ) {
				$mh_sticky_top_header_mobile_padding_css .= 'padding-top:'. $medium_header_sticky_top_header_mobile_top_padding .'px;';
			}

			// Mobile sticky top header bottom padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_sticky_top_header_mobile_bottom_padding ) ) {
				$mh_sticky_top_header_mobile_padding_css .= 'padding-bottom:'. $medium_header_sticky_top_header_mobile_bottom_padding .'px;';
			}

			// Mobile sticky top header padding css
			if ( 'medium' == $header_style && ! empty( $medium_header_sticky_top_header_mobile_top_padding )
				|| 'medium' == $header_style && ! empty( $medium_header_sticky_top_header_mobile_bottom_padding ) ) {
				$css .= '@media (max-width: 480px){.is-sticky #site-header.medium-header .top-header-wrap{'. $mh_sticky_top_header_mobile_padding_css .'}}';
			}

			// Add height menu for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_menu_height ) && '60' != $medium_header_menu_height ) {
				$css .= '#site-header.medium-header #site-navigation-wrap .dropdown-menu > li > a,#site-header.medium-header #oceanwp-mobile-menu-icon a{line-height:'. $medium_header_menu_height .'px;}';
			}

			// Add menu items right padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_menu_items_right_padding ) && '22' != $medium_header_menu_items_right_padding ) {
				$mh_menu_items_padding_css .= 'padding-right:'. $medium_header_menu_items_right_padding .'px;';
			}

			// Add menu items left padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_menu_items_left_padding ) && '22' != $medium_header_menu_items_left_padding ) {
				$mh_menu_items_padding_css .= 'padding-left:'. $medium_header_menu_items_left_padding .'px;';
			}

			// Menu items padding css
			if ( 'medium' == $header_style && ! empty( $medium_header_menu_items_right_padding ) && '22' != $medium_header_menu_items_right_padding
				|| 'medium' == $header_style && ! empty( $medium_header_menu_items_left_padding ) && '22' != $medium_header_menu_items_left_padding ) {
				$css .= '#site-header.medium-header #site-navigation-wrap .dropdown-menu > li > a{'. $mh_menu_items_padding_css .'}';
			}

			// Tablet menu items right padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_menu_items_tablet_right_padding ) ) {
				$mh_menu_items_tablet_padding_css .= 'padding-right:'. $medium_header_menu_items_tablet_right_padding .'px;';
			}

			// Tablet menu items left padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_menu_items_tablet_left_padding ) ) {
				$mh_menu_items_tablet_padding_css .= 'padding-left:'. $medium_header_menu_items_tablet_left_padding .'px;';
			}

			// Tablet menu items padding css
			if ( 'medium' == $header_style && ! empty( $medium_header_menu_items_tablet_right_padding )
				|| 'medium' == $header_style && ! empty( $medium_header_menu_items_tablet_left_padding ) ) {
				$css .= '@media (max-width: 768px){#site-header.medium-header #site-navigation-wrap .dropdown-menu > li > a,#site-header.medium-header #oceanwp-mobile-menu-icon a{'. $mh_menu_items_tablet_padding_css .'}}';
			}

			// Mobile menu items right padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_menu_items_mobile_right_padding ) ) {
				$mh_menu_items_mobile_padding_css .= 'padding-right:'. $medium_header_menu_items_mobile_right_padding .'px;';
			}

			// Mobile menu items left padding for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_menu_items_mobile_left_padding ) ) {
				$mh_menu_items_mobile_padding_css .= 'padding-left:'. $medium_header_menu_items_mobile_left_padding .'px;';
			}

			// Mobile menu items padding css
			if ( 'medium' == $header_style && ! empty( $medium_header_menu_items_mobile_left_padding )
				|| 'medium' == $header_style && ! empty( $medium_header_menu_items_mobile_left_padding ) ) {
				$css .= '@media (max-width: 480px){#site-header.medium-header #site-navigation-wrap .dropdown-menu > li > a,#site-header.medium-header #oceanwp-mobile-menu-icon a{'. $mh_menu_items_mobile_padding_css .'}}';
			}

			// Add left attr if left position for medium header style
			if ( 'medium' == $header_style && 'left-menu' == $medium_header_menu_position ) {

				if ( ! empty( $medium_header_menu_items_left_padding ) && '22' != $medium_header_menu_items_left_padding ) {
					$css .= '#site-header.medium-header #site-navigation-wrap.left-menu #site-navigation{left:-'. $medium_header_menu_items_left_padding .'px}';
				}

				if ( ! empty( $medium_header_menu_items_tablet_left_padding ) ) {
					$css .= '@media (max-width: 768px){#site-header.medium-header #site-navigation-wrap.left-menu #site-navigation{left:-'. $medium_header_menu_items_tablet_left_padding .'px}}';
				}

				if ( ! empty( $medium_header_menu_items_mobile_left_padding ) ) {
					$css .= '@media (max-width: 480px){#site-header.medium-header #site-navigation-wrap.left-menu #site-navigation{left:-'. $medium_header_menu_items_mobile_left_padding .'px}}';
				}

			}

			// Add right attr if right position for medium header style
			if ( 'medium' == $header_style && 'right-menu' == $medium_header_menu_position ) {

				if ( ! empty( $medium_header_menu_items_right_padding ) && '22' != $medium_header_menu_items_right_padding ) {
					$css .= '#site-header.medium-header #site-navigation-wrap.left-menu #site-navigation{right:-'. $medium_header_menu_items_right_padding .'px}';
				}

				if ( ! empty( $medium_header_menu_items_tablet_right_padding ) ) {
					$css .= '@media (max-width: 768px){#site-header.medium-header #site-navigation-wrap.left-menu #site-navigation{right:-'. $medium_header_menu_items_tablet_right_padding .'px}}';
				}

				if ( ! empty( $medium_header_menu_items_mobile_right_padding ) ) {
					$css .= '@media (max-width: 480px){#site-header.medium-header #site-navigation-wrap.left-menu #site-navigation{right:-'. $medium_header_menu_items_mobile_right_padding .'px}}';
				}

			}

			// Add menu background for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_menu_bg ) && '#f9f9f9' != $medium_header_menu_bg ) {
				$css .= '#site-header.medium-header #site-navigation-wrap,#site-header.medium-header #oceanwp-mobile-menu-icon{background-color:'. $medium_header_menu_bg .';}';
			}

			// Add search background for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_search_bg ) && '#f5f5f5' != $medium_header_search_bg ) {
				$css .= '#site-header.medium-header #medium-searchform .search-bg{background-color:'. $medium_header_search_bg .';}';
			}

			// Add search color for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_search_color ) && '#333333' != $medium_header_search_color ) {
				$css .= '#site-header.medium-header #medium-searchform input{color:'. $medium_header_search_color .';}';
			}

			// Add search label color for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_search_placeholder_color ) && '#333333' != $medium_header_search_placeholder_color ) {
				$css .= '#site-header.medium-header #medium-searchform label{color:'. $medium_header_search_placeholder_color .';}';
			}

			// Add search button color for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_search_button_color ) && '#333333' != $medium_header_search_button_color ) {
				$css .= '#site-header.medium-header #medium-searchform button{color:'. $medium_header_search_button_color .';}';
			}

			// Add search button hover color for medium header style
			if ( 'medium' == $header_style && ! empty( $medium_header_search_button_hover_color ) && '#13aff0' != $medium_header_search_button_hover_color ) {
				$css .= '#site-header.medium-header #medium-searchform button:hover{color:'. $medium_header_search_button_hover_color .';}';
			}

			// Header logo max width
			if ( ! empty( $logo_max_width ) ) {
				$css .= '#site-logo #site-logo-inner a img, #site-header.center-header #site-navigation .middle-site-logo a img{max-width:'. $logo_max_width .'px;}';
			}

			// Header logo max width tablet
			if ( ! empty( $logo_max_width_tablet ) ) {
				$css .= '@media (max-width: 768px){#site-logo #site-logo-inner a img, #site-header.center-header #site-navigation .middle-site-logo a img{max-width:'. $logo_max_width_tablet .'px;}}';
			}

			// Header logo max width mobile
			if ( ! empty( $logo_max_width_mobile ) ) {
				$css .= '@media (max-width: 480px){#site-logo #site-logo-inner a img, #site-header.center-header #site-navigation .middle-site-logo a img{max-width:'. $logo_max_width_mobile .'px;}}';
			}

			// Header logo color
			if ( ! empty( $logo_color ) && '#333333' != $logo_color ) {
				$css .= '#site-logo a.site-logo-text{color:'. $logo_color .';}';
			}

			// Header logo hover color
			if ( ! empty( $logo_hover_color ) && '#13aff0' != $logo_hover_color ) {
				$css .= '#site-logo a.site-logo-text:hover{color:'. $logo_hover_color .';}';
			}

			// Search dropdown input background
			if ( ! empty( $search_dropdown_input_bg ) ) {
				$css .= '#searchform-dropdown input{background-color:'. $search_dropdown_input_bg .';}';
			}

			// Search dropdown input color
			if ( ! empty( $search_dropdown_input_color ) && '#333333' != $search_dropdown_input_color ) {
				$css .= '#searchform-dropdown input{color:'. $search_dropdown_input_color .';}';
			}

			// Search dropdown input border color
			if ( ! empty( $search_dropdown_input_border ) && '#dddddd' != $search_dropdown_input_border ) {
				$css .= '#searchform-dropdown input{border-color:'. $search_dropdown_input_border .';}';
			}

			// Search dropdown input border color focus
			if ( ! empty( $search_dropdown_input_border_focus ) && '#bbbbbb' != $search_dropdown_input_border_focus ) {
				$css .= '#searchform-dropdown input:focus{border-color:'. $search_dropdown_input_border_focus .';}';
			}

			// Search overlay background color
			if ( ! empty( $search_overlay_bg ) && 'rgba(0,0,0,0.9)' != $search_overlay_bg ) {
				$css .= '#searchform-overlay{background-color:'. $search_overlay_bg .';}';
			}

			// Search overlay input color
			if ( ! empty( $search_overlay_input_color ) && '#ffffff' != $search_overlay_input_color ) {
				$css .= '#searchform-overlay form input, #searchform-overlay form label{color:'. $search_overlay_input_color .';}';
			}

			// Search overlay input placeholder color
			if ( ! empty( $search_overlay_placeholder_color ) && '#ffffff' != $search_overlay_placeholder_color ) {
				$css .= '#searchform-overlay form label{color:'. $search_overlay_placeholder_color .';}';
			}

			// Search overlay input dashed background
			if ( ! empty( $search_overlay_input_dashed_bg ) && '#ffffff' != $search_overlay_input_dashed_bg ) {
				$css .= '#searchform-overlay form label i{background-color:'. $search_overlay_input_dashed_bg .';}';
			}

			// Search overlay input border color
			if ( ! empty( $search_overlay_input_border ) && '#444444' != $search_overlay_input_border ) {
				$css .= '#searchform-overlay form input{border-color:'. $search_overlay_input_border .';}';
			}

			// Search overlay input border color hover
			if ( ! empty( $search_overlay_input_border_hover ) && '#777777' != $search_overlay_input_border_hover ) {
				$css .= '#searchform-overlay form input:hover{border-color:'. $search_overlay_input_border_hover .';}';
			}

			// Search overlay input border color focus
			if ( ! empty( $search_overlay_input_border_focus ) && '#ffffff' != $search_overlay_input_border_focus ) {
				$css .= '#searchform-overlay form input:focus{border-color:'. $search_overlay_input_border_focus .';}';
			}

			// Search overlay close button color
			if ( ! empty( $search_overlay_close_button_color ) && '#ffffff' != $search_overlay_close_button_color ) {
				$css .= '.search-overlay .search-toggle-li .search-overlay-toggle.exit > span:before{color:'. $search_overlay_close_button_color .';}';
			}

			// Menu items padding
			if ( ! empty( $menu_items_padding ) && '15' != $menu_items_padding ) {
				$css .= '#site-navigation-wrap .dropdown-menu > li > a{padding: 0 '. $menu_items_padding .'px;}';
			}

			// Menu link color
			if ( ! empty( $menu_link_color ) && '#555555' != $menu_link_color ) {
				$css .= '#site-navigation-wrap .dropdown-menu > li > a,#oceanwp-mobile-menu-icon a,#searchform-header-replace-close{color:'. $menu_link_color .';}';
			}

			// Menu link color hover
			if ( ! empty( $menu_link_color_hover ) && '#13aff0' != $menu_link_color_hover ) {
				$css .= '#site-navigation-wrap .dropdown-menu > li > a:hover,#oceanwp-mobile-menu-icon a:hover,#searchform-header-replace-close:hover{color:'. $menu_link_color_hover .';}';
			}

			// Menu link active color
			if ( ! empty( $menu_link_color_active ) && '#555555' != $menu_link_color_active ) {
				$css .= '#site-navigation-wrap .dropdown-menu > .current-menu-item > a,#site-navigation-wrap .dropdown-menu > .current-menu-parent > a,#site-navigation-wrap .dropdown-menu > .current-menu-item > a:hover,#site-navigation-wrap .dropdown-menu > .current-menu-parent > a:hover{color:'. $menu_link_color_active .';}';
			}

			// Menu link background color
			if ( ! empty( $menu_link_background ) ) {
				$css .= '#site-navigation-wrap .dropdown-menu > li > a{background-color:'. $menu_link_background .';}';
			}

			// Menu link hover background color
			if ( ! empty( $menu_link_hover_background ) ) {
				$css .= '#site-navigation-wrap .dropdown-menu > li > a:hover,#site-navigation-wrap .dropdown-menu > li.sfHover > a{background-color:'. $menu_link_hover_background .';}';
			}

			// Menu link active background color
			if ( ! empty( $menu_link_active_background ) ) {
				$css .= '#site-navigation-wrap .dropdown-menu > .current-menu-item > a,#site-navigation-wrap .dropdown-menu > .current-menu-parent > a,#site-navigation-wrap .dropdown-menu > .current-menu-item > a:hover,#site-navigation-wrap .dropdown-menu > .current-menu-parent > a:hover{background-color:'. $menu_link_active_background .';}';
			}

			// Dropdown menu width
			if ( ! empty( $dropdown_width ) && '180' != $dropdown_width ) {
				$css .= '.dropdown-menu .sub-menu{min-width:'. $dropdown_width .'px;}';
			}

			// Dropdown menu background color
			if ( ! empty( $dropdown_menu_background ) && '#ffffff' != $dropdown_menu_background ) {
				$css .= '.dropdown-menu .sub-menu,#searchform-dropdown,#current-shop-items-dropdown{background-color:'. $dropdown_menu_background .';}';
			}

			// Dropdown menu top border color
			if ( ! empty( $dropdown_menu_top_border ) && '#13aff0' != $dropdown_menu_top_border ) {
				$css .= '.dropdown-menu .sub-menu,#searchform-dropdown,#current-shop-items-dropdown{border-color:'. $dropdown_menu_top_border .';}';
			}

			// Dropdown menu borders color
			if ( ! empty( $dropdown_menu_borders ) && '#f1f1f1' != $dropdown_menu_borders ) {
				$css .= '.dropdown-menu ul li.menu-item,.navigation > ul > li > ul.megamenu.sub-menu > li,.navigation .megamenu li ul.sub-menu{border-color:'. $dropdown_menu_borders .';}';
			}

			// Dropdown menu link color
			if ( ! empty( $dropdown_menu_link_color ) && '#333333' != $dropdown_menu_link_color ) {
				$css .= '.dropdown-menu ul li a.menu-link{color:'. $dropdown_menu_link_color .';}';
			}

			// Dropdown menu link hover color
			if ( ! empty( $dropdown_menu_link_color_hover ) && '#555555' != $dropdown_menu_link_color_hover ) {
				$css .= '.dropdown-menu ul li a.menu-link:hover{color:'. $dropdown_menu_link_color_hover .';}';
			}

			// Dropdown menu link hover background color
			if ( ! empty( $dropdown_menu_link_hover_bg ) && '#f8f8f8' != $dropdown_menu_link_hover_bg ) {
				$css .= '.dropdown-menu ul li a.menu-link:hover{background-color:'. $dropdown_menu_link_hover_bg .';}';
			}

			// Dropdown menu link active color
			if ( ! empty( $dropdown_menu_link_color_active ) ) {
				$css .= '.dropdown-menu ul > .current-menu-item > a.menu-link{color:'. $dropdown_menu_link_color_active .';}';
			}

			// Dropdown menu link active background color
			if ( ! empty( $dropdown_menu_link_bg_active ) ) {
				$css .= '.dropdown-menu ul > .current-menu-item > a.menu-link{background-color:'. $dropdown_menu_link_bg_active .';}';
			}

			// Dropdown menu link active background color
			if ( ! empty( $dropdown_category_title_bg ) && '#f8f8f8' != $dropdown_category_title_bg ) {
				$css .= '.navigation li.mega-cat .mega-cat-title{background-color:'. $dropdown_category_title_bg .';}';
			}

			// Dropdown menu link active background color
			if ( ! empty( $dropdown_category_title_color ) && '#222222' != $dropdown_category_title_color ) {
				$css .= '.navigation li.mega-cat .mega-cat-title{color:'. $dropdown_category_title_color .';}';
			}

			// Dropdown menu link active background color
			if ( ! empty( $dropdown_category_links_color ) && '#555555' != $dropdown_category_links_color ) {
				$css .= '.navigation li.mega-cat ul li .mega-post-title a{color:'. $dropdown_category_links_color .';}';
			}

			// Dropdown menu link active background color
			if ( ! empty( $dropdown_category_links_hover_color ) && '#333333' != $dropdown_category_links_hover_color ) {
				$css .= '.navigation li.mega-cat ul li .mega-post-title a:hover{color:'. $dropdown_category_links_hover_color .';}';
			}

			// Dropdown menu link active background color
			if ( ! empty( $dropdown_category_date_color ) && '#bbbbbb' != $dropdown_category_date_color ) {
				$css .= '.navigation li.mega-cat ul li .mega-post-date{color:'. $dropdown_category_date_color .';}';
			}

			// Add menu social font size
			if ( ! empty( $menu_social_font_size ) ) {
				$css .= '#oceanwp-social-menu ul li a,#oceanwp-social-menu .colored ul li a,#oceanwp-social-menu .minimal ul li a,#oceanwp-social-menu .dark ul li a{font-size:'. $menu_social_font_size .'px;}';
			}

			// Add menu social tablet font size
			if ( ! empty( $menu_social_tablet_font_size ) ) {
				$css .= '@media (max-width: 768px){#oceanwp-social-menu ul li a,#oceanwp-social-menu .colored ul li a,#oceanwp-social-menu .minimal ul li a,#oceanwp-social-menu .dark ul li a,.sidr-class-social-menu-inner ul li a{font-size:'. $menu_social_tablet_font_size .'px;}}';
			}

			// Add menu social mobile font size
			if ( ! empty( $menu_social_mobile_font_size ) ) {
				$css .= '@media (max-width: 480px){#oceanwp-social-menu ul li a,#oceanwp-social-menu .colored ul li a,#oceanwp-social-menu .minimal ul li a,#oceanwp-social-menu .dark ul li a,.sidr-class-social-menu-inner ul li a{font-size:'. $menu_social_mobile_font_size .'px;}}';
			}

			// Add menu social right padding
			if ( ! empty( $menu_social_right_padding ) ) {
				$menu_social_padding_css .= 'padding-right:'. $menu_social_right_padding .'px;';
			}

			// Add menu social left padding
			if ( ! empty( $menu_social_left_padding ) ) {
				$menu_social_padding_css .= 'padding-left:'. $menu_social_left_padding .'px;';
			}

			// Menu social padding css
			if ( ! empty( $menu_social_right_padding )
				|| ! empty( $menu_social_left_padding ) ) {
				$css .= '#oceanwp-social-menu ul li a,#oceanwp-social-menu .colored ul li,#oceanwp-social-menu .minimal ul li,#oceanwp-social-menu .dark ul li{'. $menu_social_padding_css .'}';
			}

			// Tablet menu social right padding
			if ( ! empty( $menu_social_tablet_right_padding ) ) {
				$menu_social_tablet_padding_css .= 'padding-right:'. $menu_social_tablet_right_padding .'px;';
			}

			// Tablet menu social left padding
			if ( ! empty( $menu_social_tablet_left_padding ) ) {
				$menu_social_tablet_padding_css .= 'padding-left:'. $menu_social_tablet_left_padding .'px;';
			}

			// Tablet menu social padding css
			if ( ! empty( $menu_social_tablet_right_padding )
				|| ! empty( $menu_social_tablet_left_padding ) ) {
				$css .= '@media (max-width: 768px){#oceanwp-social-menu ul li a,#oceanwp-social-menu .colored ul li,#oceanwp-social-menu .minimal ul li,#oceanwp-social-menu .dark ul li{'. $menu_social_tablet_padding_css .'}}';
			}

			// Mobile menu social right padding
			if ( ! empty( $menu_social_mobile_right_padding ) ) {
				$menu_social_mobile_padding_css .= 'padding-right:'. $menu_social_mobile_right_padding .'px;';
			}

			// Mobile menu social left padding
			if ( ! empty( $menu_social_mobile_left_padding ) ) {
				$menu_social_mobile_padding_css .= 'padding-left:'. $menu_social_mobile_left_padding .'px;';
			}

			// Mobile menu social padding css
			if ( ! empty( $menu_social_mobile_right_padding )
				|| ! empty( $menu_social_mobile_left_padding ) ) {
				$css .= '@media (max-width: 480px){#oceanwp-social-menu ul li a,#oceanwp-social-menu .colored ul li,#oceanwp-social-menu .minimal ul li,#oceanwp-social-menu .dark ul li{'. $menu_social_mobile_padding_css .'}}';
			}

			// Menu social links color
			if ( ! empty( $menu_social_links_color ) ) {
				$css .= '#oceanwp-social-menu ul li a,#site-header.full_screen-header #oceanwp-social-menu.simple-social ul li a{color:'. $menu_social_links_color .';}';
			}

			// Menu social links hover color
			if ( ! empty( $menu_social_hover_links_color ) ) {
				$css .= '#oceanwp-social-menu ul li a:hover,#site-header.full_screen-header #oceanwp-social-menu.simple-social ul li a:hover{color:'. $menu_social_hover_links_color .'!important;}';
			}

			// Mobile menu sidr close button background
			if ( ! empty( $mobile_menu_sidr_close_button_bg ) && '#f8f8f8' != $mobile_menu_sidr_close_button_bg ) {
				$css .= 'a.sidr-class-toggle-sidr-close{background-color:'. $mobile_menu_sidr_close_button_bg .';}';
			}

			// Mobile menu background
			if ( ! empty( $mobile_menu_sidr_background ) && '#ffffff' != $mobile_menu_sidr_background ) {
				$css .= '#sidr{background-color:'. $mobile_menu_sidr_background .';}';
			}

			// Mobile menu borders color
			if ( ! empty( $mobile_menu_sidr_borders ) && 'rgba(0,0,0,0.035)' != $mobile_menu_sidr_borders ) {
				$css .= '#sidr li, #sidr ul{border-color:'. $mobile_menu_sidr_borders .';}';
			}

			// Mobile menu links color
			if ( ! empty( $mobile_menu_links ) && '#555555' != $mobile_menu_links ) {
				$css .= 'body .sidr a, body .sidr-class-dropdown-toggle{color:'. $mobile_menu_links .';}';
			}

			// Mobile menu links hover color
			if ( ! empty( $mobile_menu_links_hover ) && '#13aff0' != $mobile_menu_links_hover ) {
				$css .= 'body .sidr a:hover, body .sidr-class-dropdown-toggle:hover, body .sidr-class-dropdown-toggle .fa, body .sidr-class-menu-item-has-children.active > a, body .sidr-class-menu-item-has-children.active > a > .sidr-class-dropdown-toggle{color:'. $mobile_menu_links_hover .';}';
			}

			// Mobile menu dropdowns background color
			if ( ! empty( $mobile_menu_sidr_dropdowns_bg ) && 'rgba(0,0,0,0.02)' != $mobile_menu_sidr_dropdowns_bg ) {
				$css .= '.sidr-class-dropdown-menu ul{background-color:'. $mobile_menu_sidr_dropdowns_bg .';}';
			}

			// Mobile menu search background color
			if ( ! empty( $mobile_menu_sidr_search_bg ) ) {
				$css .= 'body .sidr-class-mobile-searchform input{background-color:'. $mobile_menu_sidr_search_bg .';}';
			}

			// Mobile menu search background color
			if ( ! empty( $mobile_menu_sidr_search_color ) && '#333333' != $mobile_menu_sidr_search_color ) {
				$css .= 'body .sidr-class-mobile-searchform input,body .sidr-class-mobile-searchform input:focus{color:'. $mobile_menu_sidr_search_color .';}';
			}

			// Mobile menu search border color
			if ( ! empty( $mobile_menu_sidr_search_border_color ) && '#dddddd' != $mobile_menu_sidr_search_border_color ) {
				$css .= 'body .sidr-class-mobile-searchform input{border-color:'. $mobile_menu_sidr_search_border_color .';}';
			}

			// Mobile menu search focus border color
			if ( ! empty( $mobile_menu_sidr_search_border_color_focus ) && '#bbbbbb' != $mobile_menu_sidr_search_border_color_focus ) {
				$css .= 'body .sidr-class-mobile-searchform input:focus{border-color:'. $mobile_menu_sidr_search_border_color_focus .';}';
			}

			// Mobile menu search border color
			if ( ! empty( $mobile_menu_sidr_search_button_color ) && '#555555' != $mobile_menu_sidr_search_button_color ) {
				$css .= '.sidr-class-mobile-searchform button{color:'. $mobile_menu_sidr_search_button_color .';}';
			}

			// Mobile menu search border color
			if ( ! empty( $mobile_menu_sidr_search_button_hover_color ) && '#222222' != $mobile_menu_sidr_search_button_hover_color ) {
				$css .= '.sidr-class-mobile-searchform button:hover{color:'. $mobile_menu_sidr_search_button_hover_color .';}';
			}
				
			// Return CSS
			if ( ! empty( $css ) ) {
				$output .= '/* Header CSS */'. $css;
			}

			// Return output css
			return $output;

		}

		/**
		 * Get header image CSS
		 *
		 * @since 1.0.0
		 */
		public static function header_image_css( $output ) {

			// Global vars
			$header_image 				= get_header_image();
			$header_image_position 		= get_theme_mod( 'ocean_header_image_position' );
			$header_image_attachment 	= get_theme_mod( 'ocean_header_image_attachment' );
			$header_image_repeat 		= get_theme_mod( 'ocean_header_image_repeat' );
			$header_image_size 			= get_theme_mod( 'ocean_header_image_size' );

			// Define css var
			$css = '';

			// Get site background image
			if ( ! empty( $header_image ) ) {
				$css .= 'background-image:url('. $header_image .');';
			}

			// Get site background position
			if ( ! empty( $header_image_position ) && 'initial' != $header_image_position ) {
				$css .= 'background-position:'. $header_image_position .';';
			}

			// Get site background attachment
			if ( ! empty( $header_image_attachment ) && 'initial' != $header_image_attachment ) {
				$css .= 'background-attachment:'. $header_image_attachment .';';
			}

			// Get site background repeat
			if ( ! empty( $header_image_repeat ) && 'initial' != $header_image_repeat ) {
				$css .= 'background-repeat:'. $header_image_repeat .';';
			}

			// Get site background size
			if ( ! empty( $header_image_size ) && 'initial' != $header_image_size ) {
				$css .= 'background-size:'. $header_image_size .';';
			}

			// Return CSS
			if ( has_header_image() && ! empty( $css ) ) {
				$output .= '/* Header Image CSS */#site-header{'. $css .'}';
			}

			// Return output css
			return $output;

		}

	}

endif;

return new OceanWP_Header_Customizer();