<?php
/**
 * @package WC_Product_Customer_List
 * @version 2.4.4
 */

if( ! function_exists('wpcl_row_action') ) {
	function wpcl_row_action($actions, $post){
		global $post;
		if ($post->post_type == 'product'){
			$actions['wpcl-customers'] = '<a href="' . admin_url( 'post.php' ) . '?post=' . $post->ID . '&action=edit#customer-bought">' . __('Customers','wc-product-customer-list') . '</a>';
		}
		return $actions;
	}
	add_filter('post_row_actions','wpcl_row_action', 10, 2);
}