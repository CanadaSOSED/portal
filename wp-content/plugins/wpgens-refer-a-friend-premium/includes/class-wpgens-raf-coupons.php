<?php

/**
 * Main Coupon Class
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPGens_RAF_Coupons {

    public $type;

    public $order_id;

    public $referral_user_id;

    public $coupon_mail;

    public $raf_meta = array();

    /**
     * Hook in profile tabs.
     */
    public function __construct($type, $order_id, $referral_user_id) 
    {
        $this->type = $type;
        $this->order_id = $order_id;
        $this->raf_meta = get_post_meta( $order_id, '_raf_meta', true );        
        $this->referral_user_id = $referral_user_id;
    }


    /**
     * Generate coupon and email it after order status has been changed to complete
     * woocommerce_order_status_completed hook
     *
     * @since    2.0.0
     */
    public function get_coupon() {

        $coupon_code = $this->maybe_generate_coupon();

        if($coupon_code){
            return $coupon_code;
        }

        //error_log("Coupon wasnt sent and created.");

        return false;

    }

    public function maybe_generate_coupon()
    {
        if ((isset($this->raf_meta["publish"]) && $this->raf_meta["publish"] == "false") || $this->raf_meta["generate"] == "false") {
            return false;
        }

        if($this->type === "referrer") {
            if(filter_var($this->referral_user_id, FILTER_VALIDATE_EMAIL)) {
                $this->coupon_mail = $this->referral_user_id;
            } else {
                $user_info = get_userdata($this->referral_user_id);
                $this->coupon_mail = $user_info->user_email;
            }

        } else {
            $order = new WC_Order( $this->order_id );
            $this->coupon_mail = ( version_compare( WC_VERSION, '2.7', '<' ) ) ? $order->billing_email : $order->get_billing_email();        
        }
        if(!$this->coupon_mail || empty($this->coupon_mail)) {
            return false;
        }
        return $this->generate_coupon();
    }

    /**
     * Generate a coupon for userID
     *
     * @since    1.0.0
     * @return string
     */
    public function generate_coupon() {

        $prefix = "gens_raf";
        if($this->type === "friend") {
            $prefix = "gens_raf_friend";
        }

        $coupon_code = "RAF-".substr( str_shuffle(md5( time() )), 22); // Code
        $amount = get_option( $prefix.'_coupon_amount' );
        $duration = get_option( $prefix.'_coupon_duration' );
        $individual = get_option( $prefix.'_individual_use' );
        $discount_type = get_option( $prefix.'_coupon_type' );
        $limit_usage = get_option( $prefix.'_limit_usage' );
        $minimum_amount = get_option( $prefix.'_min_order' );
        $wcs_number_payments = get_option( $prefix.'_wcs_number_payments' );
        $product_ids = get_option( $prefix.'_product_ids' );
        $free_shipping = get_option( $prefix.'_free_shipping' );
        $exclude_product_ids = get_option( $prefix.'_product_exclude_ids' );
        $exclude_product_categories = get_option( $prefix.'_exclude_product_categories' );
        $exclude_product_categories = array_map('intval', explode(',', $exclude_product_categories));
        $product_categories = get_option( $prefix.'_product_categories' );
        $product_categories = array_map('intval', explode(',', $product_categories));

        // % of order total
        $order = new WC_Order( $this->order_id );
        $order_total = $order->get_total();
        if($discount_type == "order_percent") {
            $discount_type = "fixed_cart";
            $amount = number_format($order_total * ($amount / 100),2,'.','');
        }

        $generate = apply_filters("gens_raf_generate_coupon", true, $this->order_id);

        $amount = apply_filters($prefix."_coupon_amount",$amount,$this->order_id);

        do_action( 'gens_before_generate_user_coupon', $this->referral_user_id, $this->type, $order);

        $coupon = array(
            'post_title' => $coupon_code,
            'post_excerpt' => __('Referral coupon for: ','gens-raf'). $this->coupon_mail. __(' from order #','gens-raf') .$this->order_id,
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type'     => 'shop_coupon'
        );
        
        if($generate) {
            $newCouponID = wp_insert_post( $coupon );

            // Enable the filtering of discount type for other plugins.
            $discount_type = apply_filters($prefix."_discount_type", $discount_type);
            // Add meta
            update_post_meta( $newCouponID, 'discount_type', $discount_type );
            update_post_meta( $newCouponID, 'coupon_amount', $amount );
            update_post_meta( $newCouponID, 'individual_use', $individual );
            update_post_meta( $newCouponID, 'limit_usage_to_x_items', $limit_usage );
            update_post_meta( $newCouponID, 'exclude_product_categories', $exclude_product_categories );
            update_post_meta( $newCouponID, 'product_categories', $product_categories );
            update_post_meta( $newCouponID, 'product_ids', $product_ids );
            update_post_meta( $newCouponID, 'usage_count', 0 );
            update_post_meta( $newCouponID, '_wcs_number_payments', $wcs_number_payments);
            update_post_meta( $newCouponID, 'customer_email', strtolower($this->coupon_mail));
            update_post_meta( $newCouponID, 'exclude_product_ids', $exclude_product_ids );
            if($discount_type === 'recurring_percent' || $discount_type === 'recurring_fee' && $wcs_number_payments === '') {
                update_post_meta( $newCouponID, 'usage_limit', '');
            } else {
                update_post_meta( $newCouponID, 'usage_limit', $wcs_number_payments > 1 ? $wcs_number_payments : 1 );
            }
            if($duration) {
                update_post_meta( $newCouponID, 'expiry_date', date('Y-m-d', strtotime('+'.$duration.' days')));          
            }
            update_post_meta( $newCouponID, 'minimum_amount', $minimum_amount );
            update_post_meta( $newCouponID, 'apply_before_tax', 'yes' );
            update_post_meta( $newCouponID, 'free_shipping', $free_shipping );
            update_post_meta( $newCouponID, '_raf_order_id', $this->order_id );

            do_action('new_raf_data', 'new_coupon', array('user' => $this->referral_user_id, 'order' => $this->order_id, 'coupon_id' => $newCouponID) );

        }

        do_action( 'gens_after_generate_user_coupon', $this->referral_user_id, $this->type, $order, $newCouponID);

        do_action( 'gens_generate_user_coupon', $newCouponID);

        if($newCouponID) {
            return $coupon_code;            
        } else {
            return false;
        }

    }

}