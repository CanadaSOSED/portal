<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>


<h5><?php

// Retrieve The Post's Author ID
$user_id = get_the_author_meta('ID');
// Set the image size. Accepts all registered images sizes and array(int, int)
$size = 'thumbnail';
// Get the image URL using the author ID and image size params
$imgURL = get_cupp_meta($user_id, $size);
// Print the image on the page
echo '<img style="padding-right:20px" src="'. $imgURL .'" alt="" width="150" height="150">';


	/* translators: 1: user display name 2: logout url */
	printf(
		__('Hello %1$s (not %1$s?    <strong><a href="%2$s">Log out</a></strong>).', 'woocommerce' ),
		'<strong>' . esc_html( $current_user->display_name ) . '</strong>',
		esc_url( wc_logout_url( wc_get_page_permalink( 'myaccount' ) ) )
	);
?></h5>

&nbsp;

<p><?php
	$link = home_url();
	echo "Click <strong><a href='$link'>here</a></strong> to go back to the homepage.";
?></p>

<p><?php
	printf(
		__( 'Your Training Course will be listed under <a href="%1$s"><strong>My Courses</strong></a>.<br>',  'woocommerce' ),
		esc_url( wc_get_endpoint_url( 'my-courses' ) )
	);
?></p>
<p><?php
	printf(
		__( 'From your account dashboard you can edit your <strong><a href="%1$s">Account details</a></strong>.', 'woocommerce' ),
		esc_url( wc_get_endpoint_url( 'edit-account' ) )
	);
?></p>


&nbsp;

<p><?php
printf(
	__( 'Visit our <strong><a href="http://faq.soscampus.com/">FAQ</a></strong> for any questions you may have.', 'woocommerce' )
);
 ?></p>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
