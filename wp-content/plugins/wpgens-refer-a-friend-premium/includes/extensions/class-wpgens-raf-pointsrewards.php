<?php

/**
 * RAF & Points & Rewards Plugin Integration 
 *
 * @since    2.1.2
 */


// Add the action setting
add_filter( 'wc_points_rewards_action_settings', 'wpgens_raf_points_rewards_action_settings' );
function wpgens_raf_points_rewards_action_settings( $settings ) {
  
	$settings[] = array(
		'title'    => __( 'Points earned for referring users' ),
		'desc_tip' => __( 'Enter the amount of points earned when a customer refers someone and that person makes a purchase' ),
		'id'       => 'wc_points_rewards_wpgens_raf',
	);
	return $settings;
}

// add the event descriptions
add_filter( 'wc_points_rewards_event_description', 'wpgens_raf_points_rewards_event_description', 10, 3 );
function wpgens_raf_points_rewards_event_description( $event_description, $event_type, $event ) {
	$points_label = get_option( 'wc_points_rewards_points_label' );
	// set the description if we know the type
	switch ( $event_type ) {
		case 'wpgens-raf': $event_description = sprintf( __( ' %s earned for referral purchase' ), $points_label ); break;
	}
	return $event_description;
}

// perform the event
add_action( 'gens_before_generate_user_coupon', 'wpgens_raf_points_rewards_generate_points', 10, 3 );
function wpgens_raf_points_rewards_generate_points( $user_id, $type, $order ) {
	// get user or friend ID, depends on coupon type.
	if($type === 'friend') {
        $user_id = ( version_compare( WC_VERSION, '2.7', '<' ) ) ? get_post_meta($order->id, '_customer_user', true) : $order->get_user_id(); 
	}
	if ( ! $user_id ) {
		return;
	}
	// get the points configured for this custom action
	$points = apply_filters("gens_raf_points_amount",get_option( 'wc_points_rewards_wpgens_raf' ),$order);
	// Generate points
	if ( ! empty( $points ) ) {
		$order_id = ( version_compare( WC_VERSION, '2.7', '<' ) ) ? $order->id : $order->get_id();
		$data = array( 'order_id' => $order_id );
		if(class_exists('WC_Points_Rewards_Manager')) {
			WC_Points_Rewards_Manager::increase_points( $user_id, $points, 'wpgens-raf', $data );			
		}
	}
}