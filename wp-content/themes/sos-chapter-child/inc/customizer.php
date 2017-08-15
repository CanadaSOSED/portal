<?php

/**
 * Registers options with the Theme Customizer
 *
 * @param      object    $wp_customize    The WordPress Theme Customizer
 * @package    tcx
 * @since      0.2.0
 * @version    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Remove customizer defaults.
 *
 * @param object $wp_customize Customizer reference.
 */
if ( ! function_exists( 'understrap_customize_remove' ) ) {	

	function understrap_customize_remove( $wp_customize ) {

		$wp_customize->remove_section( 'themes' );
		$wp_customize->remove_section( 'understrap_theme_layout_options' );
		$wp_customize->remove_section( 'background_image' );
		$wp_customize->remove_section( 'header_image' );
		$wp_customize->remove_section( 'static_front_page' );
		$wp_customize->remove_section( 'custom_css' );
		$wp_customize->remove_control( 'background_color' );
		$wp_customize->remove_control( 'blogdescription' );
		$wp_customize->remove_control( 'site_icon' );
	}
}
add_action( 'customize_register', 'understrap_customize_remove', 20 );

/**
 * Register customizer support.
 *
 * @param object $wp_customize Customizer reference.
 */
if ( ! function_exists( 'understrap_customize_register' ) ) {	

	function understrap_customize_register( $wp_customize ) {

		/**
		 * Setup Color Scheme Overrides
		**/
		 
		// add the section to contain the settings
		$wp_customize->add_section( 'textcolors' , array(
		    'title' =>  'Color Scheme',
		) );

		// Primary color ( h1, h2, h4. h6, widget headings, nav, links, footer )
		$txtcolors[] = array(
		    'slug'=>'primary', 
		    'default' => '#000',
		    'label' => 'Primary Color'
		);
		 
		// secondary color ( site description, sidebar headings, h3, h5, nav links on hover )
		$txtcolors[] = array(
		    'slug'=>'secondary', 
		    'default' => '#000',
		    'label' => 'Secondary Color'
		);

		// accent color ( borders, highlights etc. )
		$txtcolors[] = array(
		    'slug'=>'accent', 
		    'default' => '#000',
		    'label' => 'Accent Color'
		);

		// Button color
		$txtcolors[] = array(
		    'slug'=>'btn_color', 
		    'default' => '#008AB7',
		    'label' => 'Button Color'
		);
		 
		// Button color ( hover, active )
		$txtcolors[] = array(
		    'slug'=>'hover_button_color', 
		    'default' => '#9e4059',
		    'label' => 'Button Color (on hover)'
		);
		 
		// link color
		$txtcolors[] = array(
		    'slug'=>'link_color', 
		    'default' => '#008AB7',
		    'label' => 'Link Color'
		);
		 
		// link color ( hover, active )
		$txtcolors[] = array(
		    'slug'=>'hover_link_color', 
		    'default' => '#9e4059',
		    'label' => 'Link Color (on hover)'
		);


		// add the settings and controls for each color
		foreach( $txtcolors as $txtcolor ) {
		 
		    // SETTINGS
		    $wp_customize->add_setting(
		        $txtcolor['slug'], array(
		            'default' => $txtcolor['default'],
		            'type' => 'option', 
		            'capability' => 'edit_theme_options'
		        )
		    );
		    // CONTROLS
		    $wp_customize->add_control(
		        new WP_Customize_Color_Control(
		            $wp_customize,
		            $txtcolor['slug'], 
		            array('label' => $txtcolor['label'], 
		            'section' => 'textcolors',
		            'settings' => $txtcolor['slug'])
		        )
		    );
		}
	}
}
add_action( 'customize_register', 'understrap_customize_register' );


/**
 * Writes styles out the `<head>` element of the page based on the configuration options
 * saved in the Theme Customizer.
 *
 * @since      0.2.0
 * @version    1.0.1
 */
function tcx_customizer_css() {

	// primary color
	$primary_color = get_option( 'primary' );
	 
	// secondary color
	$secondary_color = get_option( 'secondary' );

	// secondary color
	$accent_color = get_option( 'accent' );

	// button color
	$button_color = get_option( 'button_color' );
	 
	// hover or active button color
	$hover_button_color = get_option( 'hover_button_color' );
	 
	// link color
	$link_color = get_option( 'link_color' );
	 
	// hover or active link color
	$hover_link_color = get_option( 'hover_link_color' );


?>
	 <style type="text/css">

		.bg-primary {
			background-color: <?php echo $primary_color; ?> !important;
		}

		.btn-info {
		    background-color: <?php echo $button_color; ?> !important;
		}

		.btn-info:hover {
		    background-color: <?php echo $hover_button_color; ?> !important;
		}

	 </style>
<?php
} // end tcx_customizer_css
add_action( 'wp_head', 'tcx_customizer_css' );

/**
 * Registers the Theme Customizer Preview with WordPress.
 *
 * @package    tcx
 * @since      0.3.0
 * @version    1.0.0
 */
function tcx_customizer_live_preview() {

	wp_enqueue_script(
		'tcx-theme-customizer',
		get_template_directory_uri() . '/js/theme-customizer.js',
		array( 'customize-preview' ),
		'1.0.0',
		true
	);

} // end tcx_customizer_live_preview
add_action( 'customize_preview_init', 'tcx_customizer_live_preview' );
