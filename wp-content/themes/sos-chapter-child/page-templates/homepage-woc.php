<?php
/**
 * Template Name: Home Page - Chapter Winds of Change
 *
 * This template can be used to override the default template and sidebar setup
 *
 * @package sos-chapter
 */

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>

<section id="bcg_banner" style="background-image: linear-gradient(0deg, rgba(128,128,128,0.3) 0%, rgba(128,128,128,0.2) 100%),url('https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/sites/34/2019/04/09154326/bck-banner-home.jpg');">
	<div class="row">
		<div class="col-12 col-md-8 ml-md-10 align-middle py-5 px-4 py-lg-7 px-lg-8">


		</div>
	</div>
</section>

<section id="causeSection">
	<div class="row no-gutters">
		<div class="col-12 py-6 px-4 py-lg-5 px-lg-6 order-md-2">
			<span class="lead" style="font-size: 1.6em; color: #808080;text-align:center;">The Winds of Change network engages corporate and community champions  to help <strong>‘elevate education and ignite leaders’</strong>. Since 2014, we’ve supported six school projects, a computer lab, several irrigation projects and lots of training impacting hundreds of lives. In close cooperation with the faculty of Industrial and Mechanical Engineering at the University of Toronto we also work with fourth year engineering students on exciting capstone projects including the design and installation of windmills, irrigation systems and water purification systems.</span>
		</div>
	</div>
</section>

<section style="padding-bottom:50px;">
<?php echo do_shortcode( '[sp_wpcarousel id="1004"]' ); ?>
</section>

<section class="progBoxes">
	<div class="row">
		<div class="col-sm-6 col-md-5 ml-md-6" id="gray1">
				<h1 style="text-align: center;"><strong>GET INVOLVED</strong></h1>
				<div style="font-size: 1.2em; color: #0f425c">
					<ul>
          	<li style="font-weight: 400;"><a href="<?php bloginfo('site_url'); ?>/get-involved#service-trip">Join a Service Trip</a></li>
 	          <li style="font-weight: 400;"><a href="<?php bloginfo('site_url'); ?>/get-involved#company-involved">Get Your Company Involved</a></li>
 	          <li style="font-weight: 400;"><a href="<?php bloginfo('site_url'); ?>/get-involved#donate">Donate</a></li>
         </ul>
        </div>
		</div>
		<div class="col-sm-6 col-md-5" id="gray1">
				<h1 style="text-align: center;"><strong>LEARN MORE</strong></h1>
				<div style="font-size: 1.2em; color: #0f425c">
					<ul>
 	          <li style="font-weight: 400;"><a href="<?php bloginfo('site_url'); ?>/learn-more#our-story">Our Story</a></li>
						<li style="font-weight: 400;"><a href="<?php bloginfo('site_url'); ?>/learn-more#our-team">Our 	Team</a></li>
 	          <li style="font-weight: 400;"><a href="<?php bloginfo('site_url'); ?>/learn-more#past-projects">Learn About Our Past Projects</a></li>
 	          <li style="font-weight: 400;"><a href="http://www.windsofchangecanada.com/blog" target="_blank">Blog</a></li>
           </ul>
				</div>
		</div>
	</div>
</section>

<section id="partners-woc">
	<div class="container pt-3 pb-2">
		<div class="row d-flex justify-content-center">
			<div class="col-8 col-md-10">
				<h3 class="mb-3 text-center" style="color: #0f425c; font-size: 1.8em;"><strong>Thank you to our supporters</strong></h3>
				<hr class="w-25" />
				<h3 class="mb-3 text-center" style="color: #808080; font-size: 1.3em;">While trip fees are the primary source of funding, in order to help these communities further we also do fundraising. A big THANK YOU to our many individual supporters and to our corporate supports: Lush Cosmetics, Compassionate Eye and CIBC.</h3>
				<div class="row d-flex justify-content-center">
					<div class=" col-sm-3 col-md-3 ml-md-1 d-sm-flex align-middle justify-content-center my-2 my-sm-0">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/lush_logo_black.svg" class="" title="lush-logo">
					</div>
					<div class=" col-sm-4 col-md-4 d-sm-flex align-middle my-2 my-sm-0">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/cef_black.svg" class="" title="CompassionateEye">
					</div>
					<div class=" col-sm-4 col-md-4 d-sm-flex align-middle my-2 my-sm-0">
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/cibc.svg" class="" title="cibc">
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<!--
<section id="partners">
	<div class="container pt-3 pb-2">
		<div class="row d-flex justify-content-center">
			<div class="col-8 col-md-10">
				<div class="row d-flex justify-content-center">
					<div class=" col-sm-6 col-md-5 ml-md-1 d-sm-flex align-middle justify-content-center my-2 my-sm-0">
							<img src="<?php echo get_stylesheet_directory_uri(); ?>/img/sos-logo-207x128.png" class="" title="sos-logo">
					</div>
					<div class=" col-sm-6 col-md-6 d-sm-flex align-middle my-2 my-sm-0">
							<h3 class="mb-3 text-center" style="color: #0f425c; font-size: 1.5em;">Winds of Change is a delivered in partnership with CANADA SOS, a registering charity that since 2004 has been elevating education and igniting leaders.</h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
-->

<?php get_footer(); ?>