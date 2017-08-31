<?php
/**
 * Template Name: Find your campus - SOS Main
 *
 * Template for displaying a page just with the header and footer area and a "naked" content area in between.
 * Good for landingpages and other types of pages where you want to add a lot of custom markup.
 *
 * @package sos-primary
 */

get_header(); ?>

<div class="container my-5 py-5">
	<div class="row d-flex justify-content-center">
		<div class="col-12 col-md-8 ">
		<div class="h2 text-center pb-4">Select Your Campus</div>
			<div class="row d-flex justify-content-start text-center">
				<?php sos_chapters_list(); ?>
			</div>
		</div>
	</div>
</div>


<?php 
// while ( have_posts() ) : the_post();
// 	get_template_part( 'loop-templates/content', 'empty' );
// endwhile;

get_footer(); ?>