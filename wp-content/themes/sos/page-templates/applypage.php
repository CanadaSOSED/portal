<?php
/**
 * Template Name: Apply Page - Chapters Template
 *
 * This template can be used to override the default template and sidebar setup
 *
 * @package sos-chapter
 */

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper" id="page-wrapper">

	<div class="<?php echo esc_html( $container ); ?>" id="content">

		<div class="row">

			<div class="col-md-4 mt-4">
				<div id="left-sidebar">
					<h5 class="widget-title">Opportunities</h5>
					<?php 
					$args = array( 'post_type' => 'opportunities', 'posts_per_page' => 30 );
					$loop = new WP_Query( $args );
					while ( $loop->have_posts() ) : $loop->the_post(); ?>
					<a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
					<div class="card mb-2">
					  <div class="card-body">
					  	<p class="card-title h6"><?php the_title(); ?></p>
					    <h6 class="card-subtitle mb-2 text-muted small">Posted: <?php the_date(); ?></h6>
					    <a href="<?php the_permalink(); ?>" class="card-link">More Info</a>
					  </div>
					</div>
					</a>
					   
					<?php endwhile; ?>
				</div>
			</div>

			<div class="col-md-8 content-area" id="primary">

				<main class="site-main" id="main" role="main">

					<?php while ( have_posts() ) : the_post(); ?>

						<?php get_template_part( 'loop-templates/content', 'page' ); ?>

					<?php endwhile; // end of the loop. ?>

				</main><!-- #main -->

			</div><!-- #primary -->

		</div><!-- .row -->

	</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
