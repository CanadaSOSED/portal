<?php

/**
 * RAF & Popup Maker
 *
 * @since    2.1.0
 */
add_filter( 'pum_get_conditions', 'gens_raf_popupmaker' );
function gens_raf_popupmaker( $conditions ) {
	return array_merge( $conditions, array(
		'password_page_unlocked' => array(
			'group'    => __( 'General' ), // Can match any existing group. 
			'name'     => __( 'Referral link' ), // Label to identify it in the list
			'callback' => 'wpgens_came_from_referral',
		),
	) );
}

function wpgens_came_from_referral(){
	if(isset($_GET['raf'])) {
		return true;		
	}
	return false;
}