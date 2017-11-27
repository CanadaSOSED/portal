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

<div class="wrapper " id="wrapper-footer" style="background-color: #0f425c;">
	<div class="<?php echo esc_html( $container ); ?>">

		<div class="row d-flex justify-content-between">
			<div class="col-12 col-sm-6">
			<nav class="navbar navbar-dark d-flex justify-content-start">
				<div class="nav-item"><a class="nav-link" href="<?php bloginfo( 'site_url'); ?>">Home</a></div>
				<!-- <div class="nav-item"><?php //sos_wp_loginout(); ?></div> -->
				<div class="nav-item"><a class="nav-link" href="<?php echo network_site_url(); ?>/privacy-policy">Privacy Policy</a></div>
				<div class="nav-item"><a href="http://faq.soscampus.com/">Help</a></div>
			</nav>
			</div><!--col end -->
			<div class="col-12 col-sm-6">

			</div><!--col end -->

		</div><!-- row end -->

	</div><!-- container end -->

</div><!-- wrapper end -->

</div><!-- #page -->

<?php wp_footer(); ?>

</body>

</html>
