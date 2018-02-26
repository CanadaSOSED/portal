<?php

$gens_subs = get_option("gens_raf_subscription");

if(isset($gens_subs) && $gens_subs === "yes") {

	add_filter('wcs_new_order_created','gens_renewal_order_created', 10, 2 );

	function gens_renewal_order_created($order, $subscription){  
		$user_id = $order->get_user_id();
		$user_info = get_userdata($user_id);
		$user_email = $user_info->user_email;
		$order_total = $order->get_total();
		$args = array(
			'posts_per_page'   => -1,
		    'post_type'        => 'shop_coupon',
		    'post_status'      => 'publish',
			'meta_query' => array(
				'relation' => 'AND',
			    array(
				  'key' => 'customer_email',
				  'value' => $user_email,
	              'compare' => 'LIKE'
			    ),
			    array(
			    	'relation' => 'OR',
			    	array(
				    	'key' => 'usage_count',
				    	'value' => '0',
				    	'compare' => 'LIKE'
				    ),
			    	array(
				    	'key' => 'usage_count',
				    	'compare' => 'NOT EXISTS'
			    	)
			    )
			),
		);
		$coupons = get_posts( $args );
		
		if(empty($coupons)) {
			return $order;
		}
		
		$coupons_obj = new WC_Coupon($coupons[0]->ID);
		$amount = $coupons_obj->get_amount();
		$type = $coupons_obj->get_discount_type(); // fixed_cart or percent
		$coupons_obj->set_usage_count(1);
		$coupons_obj->save();

		if( $type == "percent") {
			$discount = $order_total * ( $amount / 100);
		} else {
			$discount = $amount;
		}

		// Check to make sure discount is not bigger than order
		if ( $amount > $order_total ) {
			$discount = $order_total;
		}

		$item = new WC_Order_Item_Fee();
		$item->set_props( array(
		    'name'      => "Referral applied",
		    'tax_class' => NULL,
		    'total'     => -$discount,
		    'total_tax' => 0,
		    'order_id'  => $order->get_id(),
		) );
		$item->save();
		$order->add_item( $item );
		$order->update_taxes();
		$order->calculate_totals( true );
	    return $order;
	}

}


?>