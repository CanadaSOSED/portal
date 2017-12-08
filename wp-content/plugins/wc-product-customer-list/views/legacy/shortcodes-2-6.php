<?php
/**
 * @package WC_Product_Customer_List
 * @version 2.4.4
 */

// Add Shortcode
function wpcl_shortcode( $atts ) {
	$output = '';
	// Attributes
	$customer_atts = shortcode_atts( array(
        'product' => get_the_id(),
			'quantity' => false,
    ), $atts );

	// Code
	global $post, $wpdb;
	$post_id = $customer_atts['product'];
	$wpcl_orders = '';
	$columns = array();
	$customerquery = "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_itemmeta woim 
		LEFT JOIN {$wpdb->prefix}woocommerce_order_items oi 
		ON woim.order_item_id = oi.order_item_id 
		WHERE meta_key = '_product_id' AND meta_value = %d
		GROUP BY order_id;";
	$order_ids = $wpdb->get_col( $wpdb->prepare( $customerquery, $post_id ) );
	$order_status = get_option( 'wpcl_order_status_select', array('wc-completed') );
	if( $order_ids ) {
		$args = array(
			'post_type'       =>'shop_order',
			'post__in'   => $order_ids,
			'posts_per_page' =>  999,
			'order'          => 'ASC',             
			'post_status' => $order_status,
		);
		$wpcl_orders = new WP_Query( $args );
	}
	if($wpcl_orders) {
		$output .= '<table>';
		foreach($wpcl_orders->posts as $wpcl_order) {
			$order = new WC_Order($wpcl_order->ID);
			$output .= '<tr>';
			$output .= '<td>' . $order->billing_first_name . ' ' . $order->billing_last_name . '</td>';
			if($customer_atts['quantity'] == true) {
				if (sizeof($order->get_items())>0) { $singlecount = ''; foreach($order->get_items() as $item) { if( $item['product_id'] == $post_id ) { $productcount[] = $item['qty']; $singlecount+= $item['qty'];  }  } $items['order-qty'] = $singlecount;  $output .= '<td>' . $singlecount . '</td>'; } 
			}
			$output .= '</tr>';
		}
		$output .= '</table>';
	}
	return $output;
}
add_shortcode( 'customer_list', 'wpcl_shortcode' );