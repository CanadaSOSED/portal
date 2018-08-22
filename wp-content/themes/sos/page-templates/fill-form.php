<?php
/**
 * Template Name: Fill For Template
 *
 * Template for displaying a page just with the header and footer area and a "naked" content area in between.
 * Good for landingpages and other types of pages where you want to add a lot of custom markup.
 *
 * @package sos-primary
 */

 get_header();
 $container = get_theme_mod( 'understrap_container_type' );

 ?>

 <div class="wrapper" id="wrapper-formfill">

 	<div class="<?php echo esc_attr( $container ); ?>" id="content">

 		<div class="row" id="row-formfill">

 			<?php get_sidebar( 'left' ); ?>

 			<div
 				class="<?php
 					if ( is_active_sidebar( 'left-sidebar' ) xor is_active_sidebar( 'right-sidebar' ) ) : ?>col-md-8<?php
 					elseif ( is_active_sidebar( 'left-sidebar' ) && is_active_sidebar( 'right-sidebar' ) ) : ?>col-md-4<?php
 					else : ?>col-md-12<?php
 					endif; ?> content-area"
 				id="primary">

 				<main class="site-main" id="main" role="main">

 					<?php while ( have_posts() ) : the_post(); ?>

 						<?php get_template_part( 'loop-templates/content', 'empty' ); ?>

 					<?php endwhile; // end of the loop. ?>

 				</main><!-- #main -->

 			</div><!-- #primary -->

 			<?php get_sidebar( 'right' ); ?>

 		</div><!-- .row -->

 	</div><!-- Container end -->

 </div><!-- Wrapper end -->

 <?php get_footer(); ?>
