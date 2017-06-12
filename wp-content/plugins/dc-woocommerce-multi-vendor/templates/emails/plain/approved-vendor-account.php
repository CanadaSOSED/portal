<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/emails/plain/approved-vendor-account.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version   0.0.1
 */
 
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $WCMp;

echo "= " . $email_heading . " =\n\n";
echo sprintf( __("Congratulations! There is a new vendor application on %s.", $WCMp->text_domain ), get_option( 'blogname' ) );
echo '\n';
echo sprintf( __( "Application Status: %s", $WCMp->text_domain ), 'Approved' );
echo '\n';
echo sprintf( __( "Applicant Username: %s", $WCMp->text_domain ), $user_login ); 
echo '\n';
echo _e('You have been cleared for landing! Congratulations and welcome aboard!', $WCMp->text_domain);

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
do_action( 'woocommerce_email_footer' ); 

?>