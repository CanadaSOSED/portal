<?php
/**
 * Template Name: Protected Form Page Template
 *
 * Template for displaying a page just with the header and footer area and a "naked" content area in between.
 * Good for landingpages and other types of pages where you want to add a lot of custom markup.
 *
 * @package sos-primary
 */

if(is_user_logged_in()){

    get_header();
    $container = get_theme_mod( 'understrap_container_type' );
    ?>

    <div class="wrapper" id="full-width-page-wrapper">

    	<div class="<?php echo esc_attr( $container ); ?>" id="content">

    		<div class="row">

    			<div class="col-md-12 content-area" id="primary">

    				<main class="site-main" id="main" role="main">

    					<?php while ( have_posts() ) : the_post(); ?>

    						<?php get_template_part( 'loop-templates/content', 'page' ); ?>

    						<?php
    						// If comments are open or we have at least one comment, load up the comment template.
    						if ( comments_open() || get_comments_number() ) :

    							comments_template();

    						endif;
    						?>

    					<?php endwhile; // end of the loop. ?>

    				</main><!-- #main -->

    			</div><!-- #primary -->

    		</div><!-- .row end -->

    	</div><!-- Container end -->

    </div><!-- Wrapper end -->

    <?php get_footer();

}else{
    wp_redirect(home_url());
    exit();
}
