<?php
/**
 * The template for displaying vendor orders item band called from vendor_orders.php template
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-transaction/vendor-transaction-items.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce, $WCMp;	

if(!empty($transactions)) { 
	foreach($transactions as $transaction_id) {
		$order_ids = $commssion_ids = '';
		$commission_details = get_post_meta($transaction_id, 'commission_detail', true);
		if(!empty($commission_details)) {
			$is_first = false;
			foreach($commission_details as $commission_id => $order_id) {
				if($is_first) $order_ids .= ', ';
				$order_ids .= '<a href="'.esc_url( wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_orders_endpoint', 'vendor', 'general', 'vendor-orders'), $order_id)).'" target="_blank"><span class="orange">#'.$order_id.'</span></a>';
				$is_first = true;
			}
		}
		$transfer_charge = get_post_meta($transaction_id, 'transfer_charge', true);
		$transaction_amt = get_post_meta($transaction_id, 'amount', true);	
		?>
		<tr>
			<td align="center"  width="20" >
				<span class="input-group-addon beautiful">
					<input name="transaction_ids[]" value="<?php echo $transaction_id; ?>"  class="select_transaction" type="checkbox" >
				</span>
			</td>
			<td align="center" ><?php echo get_the_date('d/m', $transaction_id); ?></td>
                        <td align="center" ><a href="<?php echo  esc_url(wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_orders_endpoint', 'vendor', 'general', 'vendor-withdrawal'), $transaction_id));?>">#<?php echo $transaction_id; ?></a></td>
			<td align="center" ><?php echo $order_ids; ?> </td>
			<td align="center" ><?php echo isset($transfer_charge) ? get_woocommerce_currency_symbol().__($transfer_charge, $WCMp->text_domain) : get_woocommerce_currency_symbol().'0.00'; ?></td>
			<td align="center" valign="middle" ><?php echo get_woocommerce_currency_symbol().__( $transaction_amt, $WCMp->text_domain); ?></td>
    </tr>
		<?php 
	} 
}	
?>