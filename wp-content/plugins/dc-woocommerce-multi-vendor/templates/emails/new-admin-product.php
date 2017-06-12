<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/emails/new-admin-product.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version   0.0.1
 */


if ( !defined( 'ABSPATH' ) ) exit; 
global $WCMp;
?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

	<p><?php printf( __( "Hi there! This is to notify that a new product has been submitted in %s.",  $WCMp->text_domain ), get_option( 'blogname' ) ); ?></p>

	<p>
		<?php printf( __( "Product title: %s",  $WCMp->text_domain ), $product_name ); ?><br/>
		<?php printf( __( "Submitted by: %s",  $WCMp->text_domain ), 'Site Administrator' ); ?><br/>
		<?php 
			if($submit_product) {
				printf( __( "Edit product: %s",  $WCMp->text_domain ), admin_url( 'post.php?post=' . $post_id . '&action=edit' ) ); 
			} else {
				printf( __( "View product: %s",  $WCMp->text_domain ), get_permalink($post_id)); 
			}
		?>
		<br/>
	</p>

<?php do_action( 'woocommerce_email_footer' ); ?>