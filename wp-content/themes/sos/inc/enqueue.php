<?php
/**
 * Understrap enqueue scripts
 *
 * @package sos-knowledge-base
 */

if ( ! function_exists( 'understrap_scripts' ) ) {
	/**
	 * Load theme's JavaScript sources.
	 */
	function understrap_scripts() {
		// Get the theme data.
		$the_theme = wp_get_theme();
		wp_enqueue_style( 'sos-styles', get_stylesheet_directory_uri() . '/assets/css/theme.min.css', array(), $the_theme->get( 'Version' ) );
		wp_enqueue_style( 'bootstrap-4', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css', array(), $the_theme->get( 'Version' ) );
		wp_enqueue_style( 'montserrat', 'https://fonts.googleapis.com/css?family=Montserrat:300,400,500,700', array(), $the_theme->get( 'Version' ) );

		//wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-3.2-slim', 'https://code.jquery.com/jquery-3.2.1.slim.min.js', array(), $the_theme->get( 'Version' ), true );
		wp_enqueue_script( 'popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js', array(), $the_theme->get( 'Version' ), true );
		wp_enqueue_script( 'bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js', array(), $the_theme->get( 'Version' ), true );
		wp_enqueue_script( 'sos-scripts', get_template_directory_uri() . '/assets/js/theme.min.js', array(), $the_theme->get( 'Version' ), true );


		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
} // endif function_exists( 'understrap_scripts' ).

add_action( 'wp_enqueue_scripts', 'understrap_scripts' );
