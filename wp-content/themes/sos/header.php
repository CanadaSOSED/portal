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
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="<?php bloginfo( 'name' ); ?> - <?php bloginfo( 'description' ); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class( $class = 'landing-page' ); ?> >

<nav class="navbar navbar-toggleable-md bg-primary fixed-top" <?php if ( is_user_logged_in() ) { echo 'style=" margin-top: 32px;"';  }?>>
	<div class="container">
	<div class="navbar-translate">
	    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
	        <span class="navbar-toggler-bar bar1"></span>
	        <span class="navbar-toggler-bar bar2"></span>
	        <span class="navbar-toggler-bar bar3"></span>
	    </button>
	    <div class="navbar-brand">
	        <a href="<?php bloginfo('url'); ?>" class="navbar-logo">
				<img src="<?php bloginfo('template_url'); ?>/assets/img/sos-logo-207x128.png" alt="SOS Portal">
			</a>
	        <a class="navbar-caption" href="<?php bloginfo('url'); ?>" >SOS Portal</a>
	    </div>
	</div>
	<?php wp_nav_menu(
		array(
			'theme_location'  => 'primary',
			'container_class' => 'collapse navbar-collapse justify-content-end',
			'container_id'    => 'navigation',
			'menu_class'      => 'navbar-nav ml-auto nav-dropdown collapse navbar-inverse nav navbar-toggleable-sm',
			'fallback_cb'     => '',
			'menu_id'         => 'navbar-nav',
			'walker'          => new WP_Bootstrap_Navwalker(),
		)
		); ?>
    </div>
</nav>
