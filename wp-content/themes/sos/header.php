<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till </nav>
 *
 * @package sos-primary-theme
 */

$container = get_theme_mod( 'understrap_container_type' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="<?php bloginfo( 'name' ); ?> - <?php bloginfo( 'description' ); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class( $class = 'landing-page' ); ?> >

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
	<div class="container">
	        <a href="<?php bloginfo('url'); ?>" class="navbar-brand">
<img class="w-25" src="<?php bloginfo( 'stylesheet_directory' ); ?>/img/sos-logo-white.svg" />
			SOS Portal
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mainNavbarToggler" aria-controls="mainNavbarToggler" aria-expanded="false" aria-label="Toggle navigation">
			  <span class="navbar-toggler-icon"></span>
			</button>

	<?php wp_nav_menu(
		array(
			'theme_location'  => 'primary',
			'container_class' => 'collapse navbar-collapse d-flex justify-content-end',
			'container_id'    => 'mainNavbarToggler',
			'menu_class'      => 'navbar-nav',
			'fallback_cb'     => '',
			'menu_id'         => '',
			'walker'          => new WP_Bootstrap_Navwalker(),
		)
		); ?>
    </div>
</nav>
