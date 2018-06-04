<!-- ismara - 2018-03-26 - customazing opportunities page -->
<?php
 /**
  * Template Name: Single Opportunities - Chapters Template
  *
  * This template can be used to override the default template and sidebar setup
  *
  * @package sos-chapter
  */

get_header();
$container   = get_theme_mod( 'understrap_container_type' );
$sidebar_pos = get_theme_mod( 'understrap_sidebar_position' );
?>

<div class="wrapper" id="single-wrapper">

	<div class="<?php echo esc_html( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<!-- Do the left sidebar check -->
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
				<main class="site-main" id="main">
					<?php while ( have_posts() ) : the_post(); ?>
					<header class="entry-header">
						<?php the_title( '<h1 class="entry-title"; style="text-align:center" >', '</h1>' ); ?>
						<div class="entry-meta">
							<!-- <?php understrap_posted_on(); ?> -->
						</div><!-- .entry-meta -->
					</header><!-- .entry-header -->

					<div class="entry-content" style="margin: 50px 0px 0px 0px; text-align:justify">
						<?php the_content(); ?>
					</div><!-- .entry-content -->
					<br>
					<hr/>
					<div class="block_content" style="margin: 50px 0px 0px 20px; text-align:justify">

						<?php
							if(get_field('role_description')) {
								echo '<p><h5>Role Description:</h5>' . get_field('role_description') . '</p></br>';
							}?>


						<?php
							if(get_field('responsibility_text')) {
								echo '<p><h5>Responsibilities:</h5> ' . get_field('responsibility_text') . '</p></br>';
							}?>
						<?php
							if(get_field('skill_experience_text')) {
								echo '<p><h5>Desired Skills and Experience:</h5> ' . get_field('skill_experience_text') . '</p></br>';
							}?>
					</div><!-- .block_content -->
					<br>
					<!--ismara - 2018-05-30 - button didn't work with bloginfo('site_url') when we were at an publishe opportunity -->
					<a class="btn btn-primary btn-lg my-3" style="width: 200px;" href="<?php bloginfo('url'); ?>/apply" role="button">Volunteer Now</a>
					<br>
					<hr/>
					<br>
					<p>Last modified: <?php the_modified_date('F j, Y'); ?></p>
					<p>
					<?php endwhile; // end of the loop. ?>
				</main><!-- #main -->
			</div><!-- #primary -->

			<!-- Do the right sidebar check -->
			<?php if ( 'right' === $sidebar_pos || 'both' === $sidebar_pos ) : ?>
			<?php get_sidebar( 'right' ); ?>
			<?php endif; ?>
		</div><!-- .row -->
	</div><!-- Container end -->
</div><!-- Wrapper end -->

<?php get_footer(); ?>
