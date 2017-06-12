<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/emails/approved-vendor-account.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version   0.0.1
 */
 
global $WCMp;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
?>
<?php do_action( 'woocommerce_email_header', $email_heading ); ?>
<p><?php printf( __( "Congratulations! There is a new vendor application on %s.", $WCMp->text_domain ), get_option( 'blogname' ) ); ?></p>
<p>
	<?php _e( "Application status: Approved",  $WCMp->text_domain ); ?><br/>
	<?php printf( __( "Applicant Username: %s",  $WCMp->text_domain ), $user_login ); ?>
</p>
<p><?php _e('You have been cleared for landing! Congratulations and welcome aboard!', $WCMp->text_domain) ?> <p>
<?php do_action( 'woocommerce_email_footer' );?>