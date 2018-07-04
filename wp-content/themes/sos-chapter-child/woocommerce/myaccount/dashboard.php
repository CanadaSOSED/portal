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
	/* translators: 1: user display name 2: logout url */
	printf(
		__( 'Hello %1$s (not %1$s?    <strong><a href="%2$s">Log out</a></strong>).', 'woocommerce' ),
		'<strong>' . esc_html( $current_user->display_name ) . '</strong>',
		esc_url( wc_logout_url( wc_get_page_permalink( 'myaccount' ) ) )
	);
?></h5>

&nbsp;

<p><?php
	$link = home_url();
	echo "Click <strong><a href='$link'>here</a></strong> to go back to the homepage or click on the logo above.";
?></p>

<p><?php
	printf(
		__( 'Your sessions will be listed under <a href="%1$s"><strong>Order History</strong></a>.<br>',  'woocommerce' ),
		esc_url( wc_get_endpoint_url( 'orders', 'downloads' ) )
	);
?></p>
<p><?php
	printf(
		__( 'Your downloadable content is available under <a href="%1$s"><strong>Exam Aid Materials</strong></a>.',  'woocommerce' ),
		esc_url( wc_get_endpoint_url( 'downloads' ) )
	);
?></p>

<p><?php
	printf(
		__( 'From your account dashboard you can review your <strong><a href="%1$s">Trip application</a></strong> and edit your <strong><a href="%3$s">Account details</a></strong>.', 'woocommerce' ),
		esc_url( wc_get_endpoint_url( 'orders' ) ),
		esc_url( wc_get_endpoint_url( 'edit-address' ) ),
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
