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

<section class="progBoxes">
	<div class="row">
		<div class="col-sm-6 col-md-5 ml-md-6" id="examAids">
				<h1 style="color: #fff; text-align: center;"><strong>Exam Aid Sessions</strong></h1>
				<h2 style="color: #fff; text-align: center; margin-bottom: 30px;">CONQUER EXAMAPHOBIA. </h2>
				<p style="color: #fff; font-size: 1.2em;">Designed by students, for students and taught by A+ Instructors<br> to help you boost your marks.</br></br> Enter your next exam with confidence! Over 100,000 students have been supported by Exam Aid Sessions and 96% would recommend a session to a friend!</p>
					<a class="btn btn-primary btn-lg mx-auto my-3 d-block animated fadeIn" style="width: 200px;" href="<?php bloginfo('site_url'); ?>/shop" role="button">Find a Session</a>
		</div>
		<div class="col-sm-6 col-md-5" id="outreachTrips">
				<h1 style="color: #fff; text-align: center;"><strong>Outreach Trips</strong></h1>
				<h2 style="color: #fff; text-align: center; margin-bottom: 30px;">TRANSFORMATIONAL TRAVEL.</h2>
				<p style="color: #fff; font-size: 1.2em;">This is an immersive experience which will make you think twice about the world and your role in it.</p>
				<p style="color: #fff; font-size: 1.2em;">Outreach Trips last 2 weeks and enable reciprocal exchange between student participants and host communities, rooted in principles of social responsibility, local ownership, and participatory development.</p>
					<a class="btn btn-primary btn-lg mx-auto my-3 d-block animated fadeIn" style="width: 200px;" href="<?php bloginfo('site_url'); ?>/trips" role="button">Find a Trip</a>
		</div>
	</div>
</section>

<section id="voluSection">
	<div class="row">
		<div class="col-12 col-md-8 ml-md-10 align-middle py-5 px-4 py-lg-5 px-lg-8">
			<h2 class="animated fadeInDown mb-3 text-center" style="color: #fff;"><strong>Want to Join Your Campus Team?<br>Apply Today!</strong></h2>
			<div class="row d-flex justify-content-center">
				<h3 style="color: #fff;">Want to participate in the creation of a better world?</h3>
				<p class="lead" style="color: #fff;">Become an SOS Chapter Executive, Exam Aid Instructor, Outreach Trip Participant or Volunteer!</p>
				<a class="btn btn-primary btn-lg my-3" style="width: 200px;" href="<?php bloginfo('site_url'); ?>/apply" role="button">Volunteer Now</a>
			</div>
		</div>
	</div>
</section>

<section id="causeSection">
	<div class="row no-gutters">
		<div class="col-12 col-md-6 py-6 px-4 py-lg-5 px-lg-6 order-md-2">
			<h2 class="animated fadeInDown"><strong>Supporting a Great Cause.</strong></h2>
			<h3 style="color: #000;">We’re a registered charity. Since 2004 we’ve been working to support the universal right to education.</h3>
			<p class="lead">At Exam Aid sessions we collect donations, and thanks to our volunteers 100% of what's collected goes directly to our charitable mandate. By attending a session you’re not just helping your own marks, you’re helping to create a world where everyone has a fair chance to learn. </p>
			<a class="btn btn-primary btn-lg my-3" style="width: 200px;" href="http://sosvolunteertrips.org/our-impact/" role="button">Learn More</a>
		</div>
			<div class="col-12 col-md-6 causeImage py-8 order-md-1"></div>
	</div>
</section>

<section id="partners">
	<div class="container pt-3 pb-2">
		<div class="row d-flex justify-content-center">
			<div class="col-8 col-md-10">
				<h3 class="mb-3 text-center">Thank You to our Sponsors:</h3>
				<hr class="w-25" />
				<div class="row d-flex justify-content-center">
					<div class=" col-sm-6 col-md-5 ml-md-1 d-sm-flex align-middle justify-content-center my-2 my-sm-0">
						<a href="https://sosvolunteertrips.org/sponsors-and-partners/" target="_blank"><img src="https://sosvolunteertrips.org/wp-content/uploads/2018/07/coursehero-clear.png" alt="Coursehero logo"></a>
					</div>
					<div class=" col-sm-6 col-md-6 d-sm-flex align-middle my-2 my-sm-0">
						<a href="https://sosvolunteertrips.org/sponsors-and-partners/" target="_blank"><img src="https://sosvolunteertrips.org/wp-content/uploads/2018/07/Princeton-Rview_logo.png" alt="Princeton Review logo"></a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
