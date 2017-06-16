<?php
/**
 * Footer Widgets Customizer Options
 *
 * @package OceanWP WordPress theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'OceanWP_Footer_Widgets_Customizer' ) ) :

	class OceanWP_Footer_Widgets_Customizer {

		/**
		 * Setup class.
		 *
		 * @since 1.0
		 */
		public function __construct() {

			add_action( 'customize_register', 	array( $this, 'customizer_options' ) );
			add_filter( 'ocean_head_css', 		array( $this, 'head_css' ) );

		}

		/**
		 * Customizer options
		 *
		 * @since 1.0.0
		 */
		public function customizer_options( $wp_customize ) {

			/**
			 * Section
			 */
			$section = 'ocean_footer_widgets_section';
			$wp_customize->add_section( $section , array(
				'title' 			=> esc_html__( 'Footer Widgets', 'oceanwp' ),
				'priority' 			=> 210,
			) );

			/**
			 * Enable Footer Widgets
			 */
			$wp_customize->add_setting( 'ocean_footer_widgets', array(
				'default'           	=> true,
				'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_footer_widgets', array(
				'label'	   				=> esc_html__( 'Enable Footer Widgets', 'oceanwp' ),
				'type' 					=> 'checkbox',
				'section'  				=> $section,
				'settings' 				=> 'ocean_footer_widgets',
				'priority' 				=> 10,
			) ) );

			/**
			 * Footer Widgets Visibility
			 */
			$wp_customize->add_setting( 'ocean_footer_widgets_visibility', array(
				'transport' 			=> 'postMessage',
				'default'           	=> 'all-devices',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_footer_widgets_visibility', array(
				'label'	   				=> esc_html__( 'Visibility', 'oceanwp' ),
				'type' 					=> 'select',
				'section'  				=> $section,
				'settings' 				=> 'ocean_footer_widgets_visibility',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_footer_widgets',
				'choices' 				=> array(
					'all-devices' 			=> esc_html__( 'Show On All Devices', 'oceanwp' ),
					'hide-tablet' 			=> esc_html__( 'Hide On Tablet', 'oceanwp' ),
					'hide-mobile' 			=> esc_html__( 'Hide On Mobile', 'oceanwp' ),
					'hide-tablet-mobile' 	=> esc_html__( 'Hide On Tablet & Mobile', 'oceanwp' ),
				),
			) ) );

			/**
			 * Fixed Footer
			 */
			$wp_customize->add_setting( 'ocean_fixed_footer', array(
				'default'           	=> 'off',
				'sanitize_callback' 	=> 'oceanwp_sanitize_select',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Buttonset_Control( $wp_customize, 'ocean_fixed_footer', array(
				'label'	   				=> esc_html__( 'Fixed Footer', 'oceanwp' ),
				'description'	   		=> esc_html__( 'This option add a height to your content to keep your footer at the bottom of your page.', 'oceanwp' ),
				'section'  				=> $section,
				'settings' 				=> 'ocean_fixed_footer',
				'priority' 				=> 10,
				'choices' 				=> array(
					'on' 	=> esc_html__( 'On', 'oceanwp' ),
					'off' 	=> esc_html__( 'Off', 'oceanwp' ),
				),
				'active_callback' 		=> 'oceanwp_cac_has_footer_widgets',
			) ) );

			/**
			 * Footer Widgets Page ID
			 */
			$wp_customize->add_setting( 'ocean_footer_widgets_page_id', array(
				'default' 				=> '',
				'sanitize_callback' 	=> 'oceanwp_sanitize_dropdown_pages',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Dropdown_Pages( $wp_customize, 'ocean_footer_widgets_page_id', array(
				'label'	   				=> esc_html__( 'Page ID', 'oceanwp' ),
				'description'	   		=> esc_html__( 'Choose a page to replace the widgets by this page.', 'oceanwp' ),
				'section'  				=> $section,
				'settings' 				=> 'ocean_footer_widgets_page_id',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_footer_widgets',
			) ) );

			/**
			 * Footer Widgets Columns
			 */
			$wp_customize->add_setting( 'ocean_footer_widgets_columns', array(
				'default'           	=> '4',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_setting( 'ocean_footer_widgets_tablet_columns', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_setting( 'ocean_footer_widgets_mobile_columns', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Slider_Control( $wp_customize, 'ocean_footer_widgets_columns', array(
				'label' 			=> esc_html__( 'Columns', 'oceanwp' ),
				'section'  			=> $section,
				'settings' => array(
		            'desktop' 	=> 'ocean_footer_widgets_columns',
		            'tablet' 	=> 'ocean_footer_widgets_tablet_columns',
		            'mobile' 	=> 'ocean_footer_widgets_mobile_columns',
			    ),
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_footer_widgets_and_no_page_id',
			    'input_attrs' 			=> array(
			        'min'   => 1,
			        'max'   => 4,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Footer Widgets Padding
			 */
			$wp_customize->add_setting( 'ocean_footer_top_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '30',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_footer_right_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_footer_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '30',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );
			$wp_customize->add_setting( 'ocean_footer_left_padding', array(
				'transport' 			=> 'postMessage',
				'default'           	=> '0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number',
			) );

			$wp_customize->add_setting( 'ocean_footer_tablet_top_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_footer_tablet_right_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_footer_tablet_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_footer_tablet_left_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_setting( 'ocean_footer_mobile_top_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_footer_mobile_right_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_footer_mobile_bottom_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );
			$wp_customize->add_setting( 'ocean_footer_mobile_left_padding', array(
				'transport' 			=> 'postMessage',
				'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Dimensions_Control( $wp_customize, 'ocean_footer_padding_dimensions', array(
				'label'	   				=> esc_html__( 'Padding (px)', 'oceanwp' ),
				'section'  				=> $section,				
				'settings'   => array(
		            'desktop_top' 		=> 'ocean_footer_top_padding',
		            'desktop_right' 	=> 'ocean_footer_right_padding',
		            'desktop_bottom' 	=> 'ocean_footer_bottom_padding',
		            'desktop_left' 		=> 'ocean_footer_left_padding',
		            'tablet_top' 		=> 'ocean_footer_tablet_top_padding',
		            'tablet_right' 		=> 'ocean_footer_tablet_right_padding',
		            'tablet_bottom' 	=> 'ocean_footer_tablet_bottom_padding',
		            'tablet_left' 		=> 'ocean_footer_tablet_left_padding',
		            'mobile_top' 		=> 'ocean_footer_mobile_top_padding',
		            'mobile_right' 		=> 'ocean_footer_mobile_right_padding',
		            'mobile_bottom' 	=> 'ocean_footer_mobile_bottom_padding',
		            'mobile_left' 		=> 'ocean_footer_mobile_left_padding',
				),
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_footer_widgets',
			    'input_attrs' 			=> array(
			        'min'   => 0,
			        'max'   => 500,
			        'step'  => 1,
			    ),
			) ) );

			/**
			 * Footer Widgets Background
			 */
			$wp_customize->add_setting( 'ocean_footer_background', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#222222',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_footer_background', array(
				'label'	   				=> esc_html__( 'Background Color', 'oceanwp' ),
				'section'  				=> $section,
				'settings' 				=> 'ocean_footer_background',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_footer_widgets',
			) ) );

			/**
			 * Footer Widgets Color
			 */
			$wp_customize->add_setting( 'ocean_footer_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#929292',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_footer_color', array(
				'label'	   				=> esc_html__( 'Text Color', 'oceanwp' ),
				'section'  				=> $section,
				'settings' 				=> 'ocean_footer_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_footer_widgets',
			) ) );

			/**
			 * Footer Widgets Borders Color
			 */
			$wp_customize->add_setting( 'ocean_footer_borders', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#555555',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_footer_borders', array(
				'label'	   				=> esc_html__( 'Borders Color', 'oceanwp' ),
				'section'  				=> $section,
				'settings' 				=> 'ocean_footer_borders',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_footer_widgets',
			) ) );

			/**
			 * Footer Widgets Links Color
			 */
			$wp_customize->add_setting( 'ocean_footer_link_color', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#ffffff',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_footer_link_color', array(
				'label'	   				=> esc_html__( 'Links Color', 'oceanwp' ),
				'section'  				=> $section,
				'settings' 				=> 'ocean_footer_link_color',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_footer_widgets',
			) ) );

			/**
			 * Footer Widgets Links Hover Color
			 */
			$wp_customize->add_setting( 'ocean_footer_link_color_hover', array(
				'transport' 			=> 'postMessage',
				'default' 				=> '#13aff0',
				'sanitize_callback' 	=> 'oceanwp_sanitize_color',
			) );

			$wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_footer_link_color_hover', array(
				'label'	   				=> esc_html__( 'Links Color: Hover', 'oceanwp' ),
				'section'  				=> $section,
				'settings' 				=> 'ocean_footer_link_color_hover',
				'priority' 				=> 10,
				'active_callback' 		=> 'oceanwp_cac_has_footer_widgets',
			) ) );

		}

		/**
		 * Get CSS
		 *
		 * @since 1.0.0
		 */
		public static function head_css( $output ) {
		
			// Global vars
			$footer_top_padding 				= get_theme_mod( 'ocean_footer_top_padding', '30' );
			$footer_right_padding 				= get_theme_mod( 'ocean_footer_right_padding', '0' );
			$footer_bottom_padding 				= get_theme_mod( 'ocean_footer_bottom_padding', '30' );
			$footer_left_padding 				= get_theme_mod( 'ocean_footer_left_padding', '0' );
			$tablet_footer_top_padding 			= get_theme_mod( 'ocean_footer_tablet_top_padding' );
			$tablet_footer_right_padding 		= get_theme_mod( 'ocean_footer_tablet_right_padding' );
			$tablet_footer_bottom_padding 		= get_theme_mod( 'ocean_footer_tablet_bottom_padding' );
			$tablet_footer_left_padding 		= get_theme_mod( 'ocean_footer_tablet_left_padding' );
			$mobile_footer_top_padding 			= get_theme_mod( 'ocean_footer_mobile_top_padding' );
			$mobile_footer_right_padding 		= get_theme_mod( 'ocean_footer_mobile_right_padding' );
			$mobile_footer_bottom_padding 		= get_theme_mod( 'ocean_footer_mobile_bottom_padding' );
			$mobile_lfooter_eft_padding 		= get_theme_mod( 'ocean_footer_mobile_left_padding' );
			$footer_background 					= get_theme_mod( 'ocean_footer_background', '#222222' );
			$footer_color 						= get_theme_mod( 'ocean_footer_color', '#929292' );
			$footer_borders 					= get_theme_mod( 'ocean_footer_borders', '#555555' );
			$footer_link_color 					= get_theme_mod( 'ocean_footer_link_color', '#ffffff' );
			$footer_link_color_hover 			= get_theme_mod( 'ocean_footer_link_color_hover', '#13aff0' );

			// Define css var
			$css = '';
			$padding_css = '';
			$tablet_padding_css = '';
			$mobile_padding_css = '';

			// DECREPITATED Footer padding
			$footer_padding = get_theme_mod( 'ocean_footer_padding' );
			if ( ! empty( $footer_padding ) ) {
				$css .= '#footer-widgets{padding:'. $footer_padding .';}';
			}

			// Footer top padding
			if ( ! empty( $footer_top_padding ) && '30' != $footer_top_padding ) {
				$padding_css .= 'padding-top:'. $footer_top_padding .'px;';
			}

			// Footer right padding
			if ( ! empty( $footer_right_padding ) && '0' != $footer_right_padding ) {
				$padding_css .= 'padding-right:'. $footer_right_padding .'px;';
			}

			// Footer bottom padding
			if ( ! empty( $footer_bottom_padding ) && '30' != $footer_bottom_padding ) {
				$padding_css .= 'padding-bottom:'. $footer_bottom_padding .'px;';
			}

			// Footer left padding
			if ( ! empty( $footer_left_padding ) && '0' != $footer_left_padding ) {
				$padding_css .= 'padding-left:'. $footer_left_padding .'px;';
			}

			// Footer padding css
			if ( ! empty( $footer_top_padding ) && '30' != $footer_top_padding
				|| ! empty( $footer_right_padding ) && '0' != $footer_right_padding
				|| ! empty( $footer_bottom_padding ) && '30' != $footer_bottom_padding
				|| ! empty( $footer_left_padding ) && '0' != $footer_left_padding ) {
				$css .= '#footer-widgets{'. $padding_css .'}';
			}

			// Tablet footer top padding
			if ( ! empty( $tablet_footer_top_padding ) ) {
				$tablet_padding_css .= 'padding-top:'. $tablet_footer_top_padding .'px;';
			}

			// Tablet footer right padding
			if ( ! empty( $tablet_footer_right_padding ) ) {
				$tablet_padding_css .= 'padding-right:'. $tablet_footer_right_padding .'px;';
			}

			// Tablet footer bottom padding
			if ( ! empty( $tablet_footer_bottom_padding ) ) {
				$tablet_padding_css .= 'padding-bottom:'. $tablet_footer_bottom_padding .'px;';
			}

			// Tablet footer left padding
			if ( ! empty( $tablet_footer_left_padding ) ) {
				$tablet_padding_css .= 'padding-left:'. $tablet_footer_left_padding .'px;';
			}

			// Tablet footer padding css
			if ( ! empty( $tablet_footer_top_padding )
				|| ! empty( $tablet_footer_right_padding )
				|| ! empty( $tablet_footer_bottom_padding )
				|| ! empty( $tablet_footer_left_padding ) ) {
				$css .= '@media (max-width: 768px){#footer-widgets{'. $tablet_padding_css .'}}';
			}

			// Mobile footer top padding
			if ( ! empty( $mobile_footer_top_padding ) ) {
				$mobile_padding_css .= 'padding-top:'. $mobile_footer_top_padding .'px;';
			}

			// Mobile footer right padding
			if ( ! empty( $mobile_footer_right_padding ) ) {
				$mobile_padding_css .= 'padding-right:'. $mobile_footer_right_padding .'px;';
			}

			// Mobile footer bottom padding
			if ( ! empty( $mobile_footer_bottom_padding ) ) {
				$mobile_padding_css .= 'padding-bottom:'. $mobile_footer_bottom_padding .'px;';
			}

			// Mobile footer left padding
			if ( ! empty( $mobile_footer_left_padding ) ) {
				$mobile_padding_css .= 'padding-left:'. $mobile_footer_left_padding .'px;';
			}

			// Mobile footer padding css
			if ( ! empty( $mobile_footer_top_padding )
				|| ! empty( $mobile_footer_right_padding )
				|| ! empty( $mobile_footer_bottom_padding )
				|| ! empty( $mobile_footer_left_padding ) ) {
				$css .= '@media (max-width: 480px){#footer-widgets{'. $mobile_padding_css .'}}';
			}

			// Footer background
			if ( ! empty( $footer_background ) && '#222222' != $footer_background ) {
				$css .= '#footer-widgets{background-color:'. $footer_background .';}';
			}

			// Footer color
			if ( ! empty( $footer_color ) && '#929292' != $footer_color ) {
				$css .= '#footer-widgets,#footer-widgets p,#footer-widgets li a:before,#footer-widgets .contact-info-widget span.oceanwp-contact-title,#footer-widgets .recent-posts-date,#footer-widgets .recent-posts-comments,#footer-widgets .widget-recent-posts-icons li .fa{color:'. $footer_color .';}';
			}

			// Footer borders color
			if ( ! empty( $footer_borders ) && '#555555' != $footer_borders ) {
				$css .= '#footer-widgets li,#footer-widgets #wp-calendar caption,#footer-widgets #wp-calendar th,#footer-widgets #wp-calendar tbody,#footer-widgets .contact-info-widget i,#footer-widgets .oceanwp-newsletter-form-wrap input[type="email"],#footer-widgets .posts-thumbnails-widget li,#footer-widgets .social-widget li a{border-color:'. $footer_borders .';}';
			}

			// Footer link color
			if ( ! empty( $footer_link_color ) && '#ffffff' != $footer_link_color ) {
				$css .= '#footer-widgets .footer-box a,#footer-widgets a{color:'. $footer_link_color .';}';
			}

			// Footer link hover color
			if ( ! empty( $footer_link_color_hover ) && '#13aff0' != $footer_link_color_hover ) {
				$css .= '#footer-widgets .footer-box a:hover,#footer-widgets a:hover{color:'. $footer_link_color_hover .';}';
			}
				
			// Return CSS
			if ( ! empty( $css ) ) {
				$output .= '/* Footer Widgets CSS */'. $css;
			}

			// Return output css
			return $output;

		}

	}

endif;

return new OceanWP_Footer_Widgets_Customizer();