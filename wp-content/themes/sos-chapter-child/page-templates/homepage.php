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

<section id="hero-banner" class="bg bg-dark">
	<div class="grad-overlay"></div>
	<div class="container">
		<div class="row d-flex justify-content-center text-center p-3 p-md-3">
			<div class="col-12 my-6">
				<h1 class="h1 animated fadeInDown" >Walk into your next exam with confidence!</h1>
				<div class="lead animated fadeIn">
					<p>Over <b>100,000</b> students have used Exam Aids to ace their exams!</p>
				</div>
				<p class="animated fadeIn">The nearer it gets to exams the faster Exam Aid sessions fill up. Don't miss out on the easiest way to boost your marks.</p>
				<a class="btn btn-primary btn-lg mx-auto my-3 d-block animated fadeIn" style="width: 200px;" href="<?php bloginfo('site_url'); ?>/shop" role="button">Find a Session</a>
			</div>
		</div>
	</div>
</section>

<section id="features">
	<div class="row no-gutters">
		<div class="col-12 col-md-6 d-block align-middle py-6 px-4 py-lg-7 px-lg-6">
				<h3>They know what you need to know...<br/>and what you don't.</h3>
				<p class="lead">Learn from an A+ student who' has already aced your course.</p>
				<a href="/shop" class="btn btn-outline-primary">Find a Session</a>
		</div>
			<div class="col-12 col-md-6 img-block-1 py-8 "></div>
	</div>
	<div class="row no-gutters">
		<div class="col-12 col-md-6 d-block align-middle py-6 px-4 py-lg-7 px-lg-6 order-1 order-md-2">
			<h3>Watch, learn, and relax.</h3>
			<p class="lead">You'll learn your course's exam content inside &amp; out in a simple and easy way.</p>
			<a href="/shop" class="btn btn-outline-primary">Find a Session</a>
		</div>
			<div class="col-12 col-md-6 img-block-2 py-8 order-2 order-md-1"></div>
	</div>
</section>
<section id="partners">
	<div class="container pt-5 pb-4">
		<div class="row d-flex justify-content-center">
			<div class="col-8 col-md-10">
				<h3 class="mb-3 text-center">In Partnership With</h3>
				<hr class="w-25" />
				<div class="row d-flex justify-content-center">
					<div class=" col-sm-3 d-sm-flex align-middle justify-content-center my-4 my-sm-0">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/scotiabank-logo.svg" class="" title="scotiabank-logo">
					</div>
					<div class=" col-sm-3 d-sm-flex align-middle justify-content-center my-4 my-sm-0">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/princeton-logo.svg" class="w-50" title="princeton-review-logo">
					</div>
					<div class=" col-sm-3 d-sm-flex align-middle justify-content-center my-4 my-sm-0">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/minute-logo.svg" class="" title="minute-school-logo">
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
