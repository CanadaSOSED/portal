<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till </nav>
 *
 * @package sos-knowledge-base
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
	<meta name="apple-mobile-web-app-title" content="<?php bloginfo( 'name' ); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class( $class = 'landing-page' ); ?> >

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
	<div class="container">
	        <div class="navbar-brand">
				<a href="<?php bloginfo('url'); ?>" class="navbar-logo">
					<img class="mr-3" src="<?php echo get_template_directory_uri(); ?>/img/sos-logo-white.svg" />
				</a>
				<a class="navbar-caption" href="<?php bloginfo('url'); ?>" ><?php bloginfo( 'name' ); ?></a>
			</div>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#primaryNav" aria-controls="primaryNav" aria-expanded="false" aria-label="Toggle navigation">
			  <span class="navbar-toggler-icon"></span>
			</button>

	<?php wp_nav_menu(
		array(
			'theme_location'  => 'primary',
			'container_class' => 'collapse navbar-collapse justify-content-lg-end',
			'container_id'    => 'primaryNav',
			'menu_class'      => 'navbar-nav ml-auto',
			'fallback_cb'     => '',
			'menu_id'         => '',
			'walker'          => new WP_Bootstrap_Navwalker(),
		)
		); ?>
    </div>
</nav>


	<!-- ******************* The Breadcrumb Area ******************* -->
	<?php 	// Only Display breadcrumb on single posts 
			if ( is_single() ) { ?>
	<div class="wrapper-fluid wrapper-navbar" id="wrapper-breadcrumb">

		<nav class="navbar navbar-toggleable-lg navbar-inverse nav-breadcrumb ">
		
			<div class="container ">
				
				<div class="col-sm-12 hidden-md-down col-xl-9 ">
					<p><?php the_breadcrumb(); ?></p>
				</div>
				
				<div class="col-sm-12 col-xl-3 search">
					<!-- Display Custom Search box -->
					<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search">
						<label class="assistive-text" for="s">Search</label>
						<div class="form-group">
							<input class="field form-control" id="s" name="s" type="text" placeholder="Search …">
						</div>
					</form>
				</div>
		
		</div><!-- .container -->
	
	</div><!-- .wrapper-navbar end -->
	
	<?php } ?>
