<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till </nav>
 *
 * @package sos-chapter
 */

$container = get_theme_mod( 'understrap_container_type' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
			<!-- Facebook Pixel Code -->
		<script>
		!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
		n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
		t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
		document,'script','https://connect.facebook.net/en_US/fbevents.js');
		fbq('init', '1818543425084819');
		fbq('track', "PageView");</script>
		<noscript><img height="1" width="1" style="display:none"
		src="https://www.facebook.com/tr?id=1781207815497213&ev=PageView&noscript=1"
		/></noscript>
		<!-- End Facebook Pixel Code -->
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="<?php bloginfo( 'name' ); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class( $class = '' ); ?> >
<div class="navbar-expand-lg navbar-dark navbar-top" style="background-color: #0f425c;"> <!--Top part of navigation-->
		<div class="container"><!--container-->
			<div class="navbar-brand" id="topNav">
				<div>
					<a href="<?php echo network_site_url(); ?>" class="navbar-logo">
					<img class="mr" src="https://sosvolunteertrips.org/wp-content/uploads/2018/08/new-logo.png" />
					</a>
				</div>
				<div>
					<a class="navbar-caption" href="<?php bloginfo('url'); ?>" ><?php bloginfo( 'name' ); ?></a> <!--This gets the blog name-->
				</div>
				<div>
					<?php
						get_template_part( 'header', 'top' );
					?>
				</div>
			</div>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#primaryNav" aria-controls="primaryNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>

    </div> <!--container-->
</div> <!--Top part of navigation ends-->
<div class="navbar-expand-lg"> <!-- Navigation menu//You can find this content inside header-nav.php -->
			<?php
				get_template_part( 'header', 'nav' );
			?>
</div> <!-- Navigation ends -->
