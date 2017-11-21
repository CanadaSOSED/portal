<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package sos-chapter
 */



get_header();
?>

<?php
$container   = get_theme_mod( 'understrap_container_type' );
$sidebar_pos = get_theme_mod( 'understrap_sidebar_position' );
?>

<div class="wrapper" id="archive-wrapper">

	<div class="<?php echo esc_html( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<!-- Do the left sidebar check -->
			<?php get_template_part( 'global-templates/left-sidebar-check', 'none' ); ?>

			<main class="site-main" id="main">

				<?php
					//logic for grabbic Trip posts from main SOS site
					$currentblog = get_current_blog_id();

					switch_to_blog(1);

					$args = (array(
						'post_type'       => 'trips',
						'meta_key'        => 'trip_schools',
						'meta_value'      => '"'.$currentblog.'"',
						'meta_compare'	  => 'LIKE'
					));
					// var_dump($args); die();

					$trips = new WP_Query($args);


					restore_current_blog();

					// echo "<pre>";
					// print_r($trips);
					// echo "</pre>";
					// die();
				?>

				<?php if ( $trips ) : ?>

					<div class="page-title">
						<?php

						'<h1 class="page-title">Trips </h1>'
						?>
						<div>
							
						</div>
						<hr/>
						<br>
					</div><!-- .page-header -->

					<?php /* Start the Loop */ ?>
					<?php while ( $trips->have_posts() ) : $trips->the_post(); ?>

						<?php

						/*
						 * Include the Post-Format-specific template for the content.
						 * If you want to override this in a child theme, then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						get_template_part( 'loop-templates/content-trips', get_post_format() );
						?>

					<?php endwhile; ?>

				<?php else : ?>

					<?php get_template_part( 'loop-templates/content-trips', 'none' ); ?>

				<?php endif; ?>

				<?php wp_reset_postdata(); ?>

			</main><!-- #main -->

			<!-- The pagination component -->
			<?php understrap_pagination(); ?>

		</div><!-- #primary -->

		<!-- Do the right sidebar check -->
		<?php if ( 'right' === $sidebar_pos || 'both' === $sidebar_pos ) : ?>

			<?php get_sidebar( 'right' ); ?>

		<?php endif; ?>

	</div> <!-- .row -->

</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
