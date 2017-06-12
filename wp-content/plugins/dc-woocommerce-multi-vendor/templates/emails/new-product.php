<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/emails/new-product.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version   0.0.1
 */


if ( !defined( 'ABSPATH' ) ) exit; 
global  $WCMp;

if($post_type == 'shop_coupon') $title = __( 'Coupon', $WCMp->text_domain );
else  $title = __( 'Product', $WCMp->text_domain );
	
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

	<p><?php printf( __( "Hi there! This is a notification about a new %s on %s.",  $WCMp->text_domain ), $title, get_option( 'blogname' ) ); ?></p>

	<p>
		<?php printf( __( "%s title: %s",  $WCMp->text_domain ), $title, $product_name ); ?><br/>
		<?php printf( __( "Submitted by: %s",  $WCMp->text_domain ), $vendor_name ); ?><br/>
		<?php printf( __( "Edit %s: %s",  $WCMp->text_domain ), $title, admin_url( 'post.php?post=' . $post_id . '&action=edit' ) ); ?>
		<br/>
	</p>

<?php do_action( 'woocommerce_email_footer' ); ?>