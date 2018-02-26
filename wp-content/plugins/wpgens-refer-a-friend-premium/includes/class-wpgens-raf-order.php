<?php
/**
 * Add Meta box to Order Screen
 * @author WPGens
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPGENS_RAF_Order {

	/**
	 * Hook in order meta boxes and save order meta
	 *
	 * @since 2.0.0
	 */
	public function __construct() 
	{
		add_action( 'woocommerce_order_status_'.apply_filters('wpgens_raf_order_status','completed'),  array( $this,'gens_maybe_create_send_coupon') );
	}

	public function gens_maybe_create_send_coupon($order_id)
	{
		
		$raf_meta = get_post_meta( $order_id, '_raf_meta', true );

		// No referral ID? Exit.
		if(!get_post_meta( $order_id, '_raf_id', true)) {
			return false;
		}

		// Wrong referral ID? Exit
		$referral_id = $this->get_referral_id($order_id);
		if(!$referral_id) {
			return false;
		}

		// Referrer Coupon
		$coupon_object = new WPGens_RAF_Coupons('referrer', $order_id, $referral_id);
		$coupon_code   = $coupon_object->get_coupon(); // returns coupon code

		// Email Coupon Code
		if($coupon_code){
            $email = new WPGens_RAF_Email($coupon_object->coupon_mail, $coupon_code, $order_id);
            $email->send_email();
        }

		// Friend Coupon
		if(get_option( 'gens_raf_friend_enable' ) === "yes"){
			$friend_coupon_object = new WPGens_RAF_Coupons('friend', $order_id, $referral_id);
			$friend_coupon_code   = $friend_coupon_object->get_coupon(); // returns coupon code
		}

		// Email Friend Coupon Code
		if(get_option( 'gens_raf_friend_enable' ) === "yes" && $friend_coupon_code){
            $email = new WPGens_RAF_Email($friend_coupon_object->coupon_mail, $friend_coupon_code, $order_id, 'friend');
            $email->send_email();
        }

		// Increase referrals
		if($raf_meta['increase_referrals'] == "true") {
			WPGens_RAF_User::set_number_of_referrals($referral_id);			
		}

		// Mozda vrati coupon pa snimi neku meta.
	}

	public function get_referral_id($order_id){
		$rafID = esc_attr(get_post_meta( $order_id, '_raf_id', true));
        $gens_users = get_users( array(
            "meta_key" => "gens_referral_id",
            "meta_value" => $rafID,
            "number" => 1, 
            "fields" => "ID"
        ) );
        if(is_array($gens_users) && !empty($gens_users)) {
            return $gens_users[0];
        } else {
            return false;
        }
	}

}