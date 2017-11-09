<?php
/**
 * Post rendering content according to caller of get_template_part.
 *
 * @package sos-chapter
 */

?>


<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<?php
		switch_to_blog(1);
		$main_blog_url = get_site_url();
		// restore_current_blog();
		$application_url = $main_blog_url . "/trip-application/?Trip=" . get_the_ID() . '&Applicant=' . get_current_user_id();
	?>

	<header class="archive-entry-header">

		<?php the_title( sprintf( '<h2 class="archive-entry-title"><a href="%s" rel="bookmark">', esc_url( $application_url ) ),
		'</a></h2>' ); ?>


	</header><!-- .archive-entry-header -->

		<?php
		echo "<strong>Cost:</strong> $" . get_field('trip_total_cost', get_the_ID());
		echo '<br>';
		echo "<strong>Departure City:</strong> " . get_field('trip_departure_city', get_the_ID());
		echo '<br>';
		echo "<strong>Departure Date:</strong> " . get_field('trip_departure_date', get_the_ID());
		echo '<br>';
		echo "<strong>Return Date:</strong> " . get_field('trip_return_date', get_the_ID());
		restore_current_blog();
		?>

	<div class="archive-entry-content">

		<?php
			the_content();
		?>
		<p><a class="btn btn-primary" href="<?php echo $application_url ?>" >Apply</a></p>


	</div><!-- .archive-entry-content -->

	<footer class="archive-entry-footer">

	</footer><!-- .archive-entry-footer -->

</article><!-- #post-## -->
