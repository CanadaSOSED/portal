<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/dashboard/vendor-dasboard-pending-shipping-items-header.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce, $WCMp;
?>
<tr>
	<td align="center" ><?php echo __('Product Name',$WCMp->text_domain); ?></td>
	<td  align="center" class="no_display" ><?php echo __('Order Date',$WCMp->text_domain); ?><br>
		<span style="font-size:12px;"><?php echo __('dd/mm',$WCMp->text_domain); ?></span></td>
	<td  align="center" class="no_display" ><?php echo __('L/B/H/W',$WCMp->text_domain); ?></td>
	<td align="left" ><?php echo __('Address',$WCMp->text_domain); ?></td>
	<td align="center" class="no_display" ><?php echo __('Charges',$WCMp->text_domain); ?></td>
</tr>
