<?php
/**
 * Post rendering content according to caller of get_template_part.
 *
 * @package sos-chapter
 */
 wp_head();
 get_header();
 $container = get_theme_mod( 'understrap_container_type' );
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<?php
		switch_to_blog(1);
		$main_blog_url = get_site_url();
		// restore_current_blog();
		if(is_user_logged_in()){
			$application_url = $main_blog_url . "/trip-application/?Trip=" . get_the_ID() . '&Applicant=' . get_current_user_id();
		}else{
			restore_current_blog();
			$application_url = home_url() . "/my-account";
			switch_to_blog(1);
		}
	?>

	<header class="archive-entry-header" style="text-align: center;">
		<?php the_title( sprintf( '<h2 class="archive-entry-title"><a href="%s" rel="bookmark">', esc_url( $application_url ) ),
		'</a></h2>' ); ?>
		<p><a class="btn btn-primary" href="<?php echo $application_url ?>" >Apply</a></p>
	</header><!-- .archive-entry-header -->

<div class="container">
	<div class="row">
		<div class="col-12" style="border: 1px solid grey; padding: 1em; margin-bottom: 1em;">
			<h3 style="text-align: center;"><strong>Our Partners</strong></h3>
			<?php the_field('trip_partners'); ?>
		</div>
	</div>
	<div class="row">
		<div class="col-12 col-md-3">
				<div class="tripInfo">
				<?php
				echo "<strong>Cost:</strong> $" . get_field('trip_total_cost', get_the_ID());
				echo '<br>';
				echo "<strong>Departure City:</strong> " . get_field('trip_departure_city', get_the_ID());
				echo '<br>';
				echo "<strong>Departure Date:</strong> " . get_field('trip_departure_date', get_the_ID());
				echo '<br>';
				echo "<strong>Return Date:</strong> " . get_field('trip_return_date', get_the_ID());
				?>
				</div>
        <div class="tripMap ml-5 ml-md-0">
    				<?php
    					$image = get_field('trip_map');
    					if( $image ) {
    					echo wp_get_attachment_image( $image ); }
    					restore_current_blog();
    				?>
    		</div>
		</div>

	<div class="col-12 col-md-9">
		<div class="archive-entry-content">
			<?php
			echo apply_filters('the_content', get_the_content(get_the_ID()));
				// the_content();
			?>
		</div><!-- .archive-entry-content -->
	</div>
</div>
</div>

	<div class="icons">
    <h4 style="text-align: center;"><strong>What's Included?</strong></h4>
  		<div class="row">
  			<div class="col-6 col-md-6"><img class="ml-3 ml-md-10" src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/2018/07/09132716/home.png"><p>SOS volunteers sleep in available community structures (classrooms, community centers, churches), and live as close to the conditions of the community as possible. </p></div>
  			<div class="col-6 col-md-6"><img class="ml-3 ml-md-10" src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/2018/07/09132717/transportation.png"><p>SOS works directly with Flight Centre to process all volunteers' Outreach Trip logistics from your travel insurance to your in country needs like clean drinking water and accommodations!</p></div>
  			<div class="col-6 col-md-6"><img class="ml-3 ml-md-10" src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/2018/07/09132715/food.png"><p>3 meals a day plus clean drinking water and snacks! </p></div>
  			<div class="col-6 col-md-6"><img class="ml-3 ml-md-10" src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/2018/07/09132716/support.png"><p>SOS works exclusively with registered non governmental organizations in every community to ensure our volunteers have the best experience possible.</p></div>
	     </div>
  <div style="text-align: center;">
    <p><a class="btn btn-primary" href="<?php echo $application_url ?>">Apply</a></p>
  </div>
</div>
</div>


	<footer class="archive-entry-footer">

	</footer><!-- .archive-entry-footer -->

</article><!-- #post-## -->
