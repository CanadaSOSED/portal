<?php
/**
 * The template for displaying vendor dashboard
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/dashboard/vendor-dashboard-sales-item-header.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.3.0
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
global $woocommerce, $WCMp;
?>
<tr>	
    <td align="center" >ID</td>
    <td align="center" ><?php _e('SKU', 'dc-woocommerce-multi-vendor'); ?></td>
    <td class="no_display"  align="center" ><?php _e('Sales', 'dc-woocommerce-multi-vendor'); ?></td>
    <td class="no_display" align="center" ><?php _e('Discount', 'dc-woocommerce-multi-vendor'); ?></td>
    <td align="center" ><?php _e('My Earnings', 'dc-woocommerce-multi-vendor'); ?></td>
</tr>