<?php
/**
 * The template for displaying the header.
 *
 * @package GeneratePress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

   <!-- Facebook Pixel Code -->
   <script>
	    !function(f,b,e,v,n,t,s)
	    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
	    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
	    if(!f._fbq)f._fbq=n;
	    n.push=n;n.loaded=!0;n.version='2.0';
	    n.queue=[];t=b.createElement(e);t.async=!0;
	    t.src=v;s=b.getElementsByTagName(e)[0];
	    s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
	    fbq('init', '1818543425084819');
	    fbq('track', 'PageView');
   </script>
	 <noscript>
	    <img height="1" width="1" src="https://www.facebook.com/tr?id=1818543425084819&ev=PageView&noscript=1" />
   </noscript>
	 <!-- End Facebook Pixel Code -->


	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php generate_body_schema();?> <?php body_class(); ?>>
	<?php
	/**
	 * generate_before_header hook.
	 *
	 * @since 0.1
	 *
	 * @hooked generate_do_skip_to_content_link - 2
	 * @hooked generate_top_bar - 5
	 * @hooked generate_add_navigation_before_header - 5
	 */
	do_action( 'generate_before_header' );

	/**
	 * generate_header hook.
	 *
	 * @since 1.3.42
	 *
	 * @hooked generate_construct_header - 10
	 */
	do_action( 'generate_header' );

	/**
	 * generate_after_header hook.
	 *
	 * @since 0.1
	 *
	 * @hooked generate_featured_page_header - 10
	 */
	do_action( 'generate_after_header' );
	?>

	<div id="page" class="hfeed site grid-container container grid-parent">
		<div id="content" class="site-content">
			<?php
			/**
			 * generate_inside_container hook.
			 *
			 * @since 0.1
			 */
			do_action( 'generate_inside_container' );
