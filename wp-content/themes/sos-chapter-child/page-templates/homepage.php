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
				<!-- <p class="animated fadeIn"></p> -->
				<a class="btn btn-primary btn-lg mx-auto my-3 d-block animated fadeIn" style="width: 200px;" href="<?php bloginfo('site_url'); ?>/shop" role="button">Find a Session</a>
			</div>
		</div>
	</div>
</section>

<section id="features">
	<div class="row no-gutters">
		<div class="col-12 col-md-6 d-block align-middle py-6 px-4 py-lg-7 px-lg-6">
				<h3>SOS Volunteers are here to help.</h3>
				<p class="lead">They'll review course content in a way that is fun and easy to understand.</p>
				<a href="/shop" class="btn btn-outline-primary">Find a Session</a>
		</div>
			<div class="col-12 col-md-6 img-block-1 py-8 "></div>
	</div>
	<div class="row no-gutters">
		<div class="col-12 col-md-6 d-block align-middle py-6 px-4 py-lg-7 px-lg-6 order-1 order-md-2">
			<h3>Suited to Your Style.</h3>
			<p class="lead">We provide in-person review sessions, video tutorials, and take-home study packages, so you can learn in the way that suits your style. </p>
			<a href="/shop" class="btn btn-outline-primary">Find a Session</a>
		</div>
			<div class="col-12 col-md-6 img-block-2 py-8 order-2 order-md-1"></div>
	</div>
</section>
<section id="support-us">
	<div class="container py-4 py-md-7">
		<div class="row d-flex justify-content-center text-center">
			<div class="col-8 col-md-10">
				<h2 class="mb-3 text-center">Supporting a Great Cause</h2>
				<hr class="w-25" />
				<div class="row d-flex justify-content-center">
					<p class="lead">We’re a registered charity. Since 2004 we’ve been working to support the universal right to education.</p>
					<p>At Exam Aid sessions we collect donations, and thanks to our volunteers 100% of what's collected goes directly to our charitable mandate. By attending a session you’re not just helping your own marks, you’re helping to create a world where everyone has a fair chance to learn. </small></p>
					<a class="btn btn-primary btn-lg my-3" style="width: 200px;" href="http://sosvolunteertrips.org/our-impact/" role="button">See Our Impact</a>
				</div>
			</div>
		</div>
	</div>
</section>
<section id="volunteer">
	<div class="container py-4 py-md-7">
		<div class="row d-flex justify-content-center text-center">
			<div class="col-8 col-md-10">
				<h2 class="mb-3 text-center">Want to Join Your Campus Team? Apply Today!</h2>
				<hr class="w-25" />
				<div class="row d-flex justify-content-center">
					<p class="lead">Make an impact on your campus today and apply to be a part of something special now!</p>
					<p>Whether you want to be an Exam Aid Instructor, gain experience with our Finance team or contribute in other ways, reach out to us and learn how you can join SOS.</small></p>
					<a class="btn btn-primary btn-lg my-3" style="width: 200px;" href="<?php bloginfo('site_url'); ?>/apply" role="button">Volunteer Now</a>
				</div>
			</div>
		</div>
	</div>
</section>
<section id="partners">
	<div class="container pt-5 pb-4">
		<div class="row d-flex justify-content-center">
			<div class="col-8 col-md-10">
				<h3 class="mb-3 text-center">Thank You to Our Sponsors</h3>
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
