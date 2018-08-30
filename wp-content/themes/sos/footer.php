<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package sos-chapter
 */
$the_theme = wp_get_theme();
$container = get_theme_mod( 'understrap_container_type' );
?>

<?php get_sidebar( 'footerfull' ); ?>

<div class="wrapper" id="wrapper-footer" style="background-color: #262626;">
	<div class="<?php echo esc_html( $container ); ?>">

		<div class="row d-flex justify-content-md-center">
			<div class="col-sm-6 col-md-4 col-lg-3">
				<h5>Contact Information</h5>
				<ul>
					<li>(289)-210-1855</li>
					<li><a style="color:inherit;" href="mailto:info@studentsofferingsupport.ca">info@studentsofferingsupport.ca</a></li>
					<li>720 Bathurst Street - Suite 410,<br>Toronto, Ontario M5S 2R4</li>
					<li>Charity #: 81495 0416 RR 0001</li>
				</ul>
				<div class="socialFooter">
					<ul>
						<li><a href="https://www.facebook.com/StudentsOfferingSupport" target="_blank"><img alt="facebook" src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/2018/08/09154317/facebook.png" width="20" height="20"></a></li>
						<li><a href="https://www.instagram.com/studentsofferingsupport/" target="_blank"><img alt="instagram" src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/2018/08/09154317/instagram.png" width="20" height="20"></a></li>
						<li><a href="https://www.linkedin.com/groups/1845827" target="_blank"><img alt="linkedin" src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/2018/08/09154318/linkedin.png" width="20" height="20"></a></li>
						<li><a href="https://twitter.com/SOSheadoffice" target="_blank"><img alt="twitter" src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/2018/08/09154318/twitter.png" width="20" height="20"></a></li>
					</ul>
				</div>
			</div><!--col end -->
			<div class="col-sm-6 col-md-4 col-lg-3">
				<h5>About Us</h5>
				<p style="color:#919191;">Students Offering Support is a community of student changemakers delivering transformational learning programs, across Canada and around the world. We’ve been a registered charity since 2008, driven by a one-of-a-kind social enterprise model that creates win-win-win solutions for our volunteers, participants, and communities at large.</p>
			</div><!--col end -->
		</div><!-- row end -->
	</div><!-- container end -->
</div><!-- wrapper end -->

<div id="copyright-wrapper"  style="background-color: #111111;">
	<div class="<?php echo esc_html( $container ); ?>">
		<div class="row">
			<div class="col-lg-8 col-sm-6">
				<p> Students Offering Support &#169; 2018.</p>
			</div><!--col end -->
			<div class="col-lg-4 col-sm-6">
				<nav class="navbar">
					<div class="nav-item" style="margin-left: 12rem;"><a class="nav-link" href="<?php echo network_site_url(); ?>/privacy-policy">Privacy Policy</a></div>
				</nav>
			</div><!--col end -->
		</div><!-- row end -->
	</div>
</div><!-- container end -->

<?php wp_footer(); ?>

</body>
</html>
