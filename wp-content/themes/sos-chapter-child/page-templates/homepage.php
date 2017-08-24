<?php
/**
 * Template Name: Home Page - Chapter
 *
 * This template can be used to override the default template and sidebar setup
 *
 * @package sos-chapter
 */

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>

<section>
	<div class="container my-5 ">
		<div class="jumbotron text-center">
			<h1 class="display-4">Ace Your Exams With an Exam Aid.</h1>
			<p class="lead">Exam aids help you prepare for your upcoming exams &amp; help build schools.</p>
			<hr class="my-4">
			<p>Everytime you attend an exam aid a portion of fee goes directly to projects that help poor and remote communities in south america.</p>
			<a class="btn btn-primary btn-lg mx-auto mt-5 d-block"  style="width: 200px;" href="<?php bloginfo('site_url'); ?>/shop" role="button">View Exam Aids</a>
		</div>
	</div>
</section>

<?php get_footer(); ?>
