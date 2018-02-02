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

    public $referral_id;

    public $coupon_mail;

    public $raf_meta = array();

    /**
     * Hook in profile tabs.
     */
    public function __construct($type, $order_id, $referral_id) 
    {
        $this->type = $type;
        $this->order_id = $order_id;
        $this->raf_meta = get_post_meta( $order_id, '_raf_meta', true );        
        $this->referral_id = $referral_id; 
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
            $user_info = get_userdata($this->referral_id);
            $this->coupon_mail = $user_info->user_email;
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
        $product_ids = get_option( $prefix.'_product_ids' );
        $exclude_product_ids = get_option( $prefix.'_exclude_product_ids' );
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

        $amount = apply_filters($prefix."_coupon_amount",$amount,$this->order_id);

        $coupon = array(
            'post_title' => $coupon_code,
            'post_excerpt' => 'Referral coupon for: '.$this->coupon_mail.' from order #'.$this->order_id,
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type'     => 'shop_coupon'
        );
                            
        $new_coupon_id = wp_insert_post( $coupon );

        // Add meta
        update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
        update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
        update_post_meta( $new_coupon_id, 'individual_use', $individual );
        update_post_meta( $new_coupon_id, 'limit_usage_to_x_items', $limit_usage );
        update_post_meta( $new_coupon_id, 'exclude_product_categories', $exclude_product_categories );
        update_post_meta( $new_coupon_id, 'product_categories', $product_categories );
        update_post_meta( $new_coupon_id, 'product_ids', $product_ids );
        update_post_meta( $new_coupon_id, 'usage_count', 0 );
        update_post_meta( $new_coupon_id, 'customer_email', strtolower($this->coupon_mail));
        update_post_meta( $new_coupon_id, 'exclude_product_ids', $exclude_product_ids );
        update_post_meta( $new_coupon_id, 'usage_limit', '1' ); // Only one coupon
        if($duration) {
            update_post_meta( $new_coupon_id, 'expiry_date', date('Y-m-d', strtotime('+'.$duration.' days')));          
        }
        update_post_meta( $new_coupon_id, 'minimum_amount', $minimum_amount );
        update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
        update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
        update_post_meta( $new_coupon_id, '_raf_order_id', $this->order_id );

        do_action( 'gens_generate_user_coupon', $new_coupon_id);

        if($new_coupon_id) {
            return $coupon_code;            
        } else {
            return "Error creating coupon";
        }

    }

}