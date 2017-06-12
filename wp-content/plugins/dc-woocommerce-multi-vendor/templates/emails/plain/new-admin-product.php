<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/emails/plain/new-admin-product.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version   0.0.1
 */

if ( !defined( 'ABSPATH' ) ) exit; 
global $WCMp;


echo "= " . $email_heading . " =\n\n";

echo sprintf( __( "Hi there! This is to notify that a new product has been submitted in %s.",  $WCMp->text_domain ), get_option( 'blogname' ) );
echo '\n'; 
echo sprintf( __( "Product title: %s",  $WCMp->text_domain ), $product_name ); 
echo '\n';
echo sprintf( __( "Submitted by: %s",  $WCMp->text_domain ), 'Site Administrator' ); 
echo '\n';
if($submit_product) {
	printf( __( "Edit product: %s",  $WCMp->text_domain ), admin_url( 'post.php?post=' . $post_id . '&action=edit' ) ); 
} else {
	printf( __( "View product: %s",  $WCMp->text_domain ), get_permalink($post_id)); 
}

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
do_action( 'woocommerce_email_footer' ); 