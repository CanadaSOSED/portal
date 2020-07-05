<?php

/**
 * RAF & Woocommerce Subscription 
 *
 * @since    2.0.0
 */
$gens_subs = get_option("gens_raf_subscription");
if(isset($gens_subs) && $gens_subs === "yes") {
	add_filter('wcs_new_order_created','gens_renewal_order_created', 10, 2 );
}

function gens_renewal_order_created($order, $subscription)
{
	$gens_subs_coupons = get_option("gens_raf_subscription_all_coupons");
	$gens_exclude_shipping = get_option("gens_raf_subscription_exclude_shipping");
	$user_id = $order->get_user_id();
	$user_info = get_userdata($user_id);
	$user_email = $user_info->user_email;
	// Prevent empty emails.
	if(empty($user_email)) {
    	return $order;
	}

	$order_total = $order->get_total() - $order->get_total_tax();
	if(isset($gens_exclude_shipping) && $gens_exclude_shipping === "yes") {
		$order_total = $order_total - $order->get_total_shipping() - $order->get_shipping_tax();
	}
	$args = array(
		'posts_per_page'   => 999,
	    'post_type'        => 'shop_coupon',
	    'post_status'      => 'publish',
		'meta_query' => array(
			'relation' => 'AND',
		    array(
			  'key' => 'customer_email',
			  'value' => $user_email,
              'compare' => 'LIKE'
            )
            // Provjeriti da ne povuce kupone koji su istekli
		)
	);
    $coupons = get_posts( $args );
	
	if(empty($coupons)) {
		return $order;
    }
    
    $availableCoupons = [];
    // Get only coupons that havent been used yet:
    foreach($coupons as $coupon) {
        $couponUsage = get_post_meta($coupon->ID,'usage_count', true);
        $usageLimit = get_post_meta($coupon->ID,'usage_limit', true);
        if($couponUsage < $usageLimit || $usageLimit === '') {
            array_push($availableCoupons, $coupon);
        }
	}
	
	if(empty($availableCoupons)) {
		return $order;
	}

	// If you want to apply all coupons, up to renewal price.
	if(isset($gens_subs_coupons) && $gens_subs_coupons === "yes") {
		$total_value = 0;
		$amount = 0;
		foreach ($availableCoupons as $coupon) {
			$coupons_obj = new WC_Coupon($coupon->ID);
			$total_value = $total_value + $coupons_obj->get_amount();
			$amount += gens_renewal_order_get_discount($order, $coupons_obj);
			if($total_value >= $order_total) {
				break;
			}
		}
		// Check to make sure discount is not bigger than order
		if ( $amount > $order_total ) {
			$amount = $order_total;
		}
		if($amount > 0) {
			gens_renewal_order_apply_discount($order, $amount);
		}
	} else {
		$coupons_obj = new WC_Coupon($availableCoupons[0]->ID);
		gens_renewal_order_apply_coupon($order, $coupons_obj);
	}

    $order_id = ( version_compare( WC_VERSION, '2.7', '<' ) ) ? $order->id : $order->get_id();

    do_action('new_raf_data', 'subscription_renewal', array('user' => $user_id, 'order' => $order_id) );

	return $order;
}

function gens_renewal_order_apply_discount($order,$amount){
	$item = new WC_Order_Item_Fee();
	$item->set_props( array(
	    'name'      => __("Referral applied","gens-raf"),
	    'tax_class' => NULL,
	    'total'     => -$amount,
	    'total_tax' => 0,
	    'tax_status'=> 'none',
	    'order_id'  => $order->get_id(),
	) );
	$item->save();
	$order->add_item( $item );
	$order->update_taxes();
	$order->calculate_totals( true );
}

function gens_renewal_order_get_discount($order, $coupons_obj)
{
	$order_total = $order->get_total() - $order->get_total_tax();
	if(isset($gens_exclude_shipping) && $gens_exclude_shipping === "yes") {
		$order_total = $order_total - $order->get_total_shipping() - $order->get_shipping_tax();
	}
	$amount = $coupons_obj->get_amount();
	$type = $coupons_obj->get_discount_type(); // fixed_cart or percent
	$coupons_obj->set_usage_count($coupons_obj->get_usage_count() + 1);
	$coupons_obj->save();

	if( $type == "percent") {
		$discount = $order_total * ( $amount / 100);
	} else {
		$discount = $amount;
	}
	return $discount;
}

function gens_renewal_order_apply_coupon($order, $coupons_obj)
{
	$order_total = $order->get_total() - $order->get_total_tax();
	if(isset($gens_exclude_shipping) && $gens_exclude_shipping === "yes") {
		$order_total = $order_total - $order->get_total_shipping() - $order->get_shipping_tax();
	}
	$amount = $coupons_obj->get_amount();
	
	$type = $coupons_obj->get_discount_type(); // fixed_cart or percent
	$coupons_obj->set_usage_count($coupons_obj->get_usage_count() + 1);
	$coupons_obj->save();

	if( $type == "percent") {
		$discount = $order_total * ( $amount / 100);
	} else {
		$discount = $amount;
	}

	// Check to make sure discount is not bigger than order
	if ( $discount > $order_total ) {
		$discount = $order_total;
	}

	$item = new WC_Order_Item_Fee();
	$item->set_props( array(
	    'name'      => __("Referral applied","gens-raf"),
	    'tax_class' => NULL,
	    'total'     => -$discount,
	    'total_tax' => 0,
	    'tax_status'=> 'none',
	    'order_id'  => $order->get_id(),
	) );
	$item->save();
	$order->add_item( $item );
	$order->update_taxes();
	$order->calculate_totals( true );
}


/**
 * Prevent gens referral code copying during renewals. 
 * https://docs.woocommerce.com/document/subscriptions/develop/filter-reference/
 *
 * @since    2.0.9
 */
function gens_prevent_referral_copying( $order_meta_query, $to_order, $from_order ) {

    $order_meta_query .= " AND `meta_key` NOT IN ('_raf_id', '_wpgens_raf_id', '_raf_meta', '_wpgens_raf_meta')";

    return $order_meta_query;
}
add_filter( 'wcs_renewal_order_meta_query', 'gens_prevent_referral_copying', 10, 3 );