<?php
/**
 * Post rendering content according to caller of get_template_part.
 *
 * @package sos-knowledge-base
 */

?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<header class="archive-entry-header">

		<?php the_title( sprintf( '<h2 class="archive-entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ),
		'</a></h2>' ); ?>

		<?php if ( 'post' == get_post_type() ) : ?>

			<div class="archive-entry-meta">
				<?php// understrap_posted_on(); ?>
			</div><!-- .archive-entry-meta -->

		<?php endif; ?>

	</header><!-- .archive-entry-header -->

	<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>

	<div class="archive-entry-content">

		<?php
			the_excerpt();
		?>

		<?php
		wp_link_pages( array(
			'before' => '<div class="page-links">' . __( 'Pages:', 'understrap' ),
			'after'  => '</div>',
		) );
		?>

	</div><!-- .archive-entry-content -->

	<footer class="archive-entry-footer">

	</footer><!-- .archive-entry-footer -->

</article><!-- #post-## -->
