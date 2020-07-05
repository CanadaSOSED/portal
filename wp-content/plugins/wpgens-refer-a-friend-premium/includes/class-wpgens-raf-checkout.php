<?php

/**
 * Hook into checkout
 *
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPGens_RAF_Checkout
{

    /**
     * Constructor.
     *
     */
    public function __construct()
    {   
        // Save RAF ID in Order Meta after Order is Complete
        add_action('woocommerce_checkout_update_order_meta',  array( $this,'maybe_save_raf_id') );
        //Remove Cookie after checkout if Setting is set
        add_action('woocommerce_thankyou',  array( $this,'remove_cookie_after') );
        // Hide auto applied coupon codes from showing.
        add_filter( 'woocommerce_cart_totals_coupon_label',  array( $this,'hide_coupon_code'), 10, 2 );
        // Auto apply RAF Coupons on cart for referrals. Also apply on checkout if cart is skipped.
        add_action( 'woocommerce_before_cart', array( $this,'apply_matched_coupons') ); // woocommerce_before_checkout_form
        add_action( 'woocommerce_before_checkout_form', array( $this,'apply_matched_coupons') ); // woocommerce_before_checkout_form
        add_action( 'woocommerce_checkout_update_order_review', array( $this,'checkout_form_check') );
        add_filter( 'woocommerce_get_shop_coupon_data', array( $this,'add_referral_via_coupon_field'), 10, 2 );
        add_action( 'woocommerce_applied_coupon', array( $this,'add_referral_apply_coupon_referral_code'), 10, 2 );
        add_action( 'woocommerce_removed_coupon', array( $this,'wc_removed_coupon'), 10, 1 ); 
    }
 
    public function wc_removed_coupon($coupon)
    {
        $guest_coupon_code  = get_option('gens_raf_guest_coupon_code');
        if($coupon === $guest_coupon_code || substr( $coupon, 0, 3 ) === "ref") {
            // unset($_COOKIE['gens_raf']);
            // setcookie('gens_raf', '', time() - 3600, '/');
        }
    }

    public function add_referral_apply_coupon_referral_code($coupon_code)
    {
        $guest_coupon_code  = get_option('gens_raf_guest_coupon_code');
        if($coupon_code !== $guest_coupon_code) {
        $user_id = $this->get_id_from_referral_code($coupon_code);
            if($user_id) {
                $time = 1;
                if(get_current_user_id() != $user_id) {
                    if( get_option( 'gens_raf_cookie_time' ) != '') {
                        $time = intval(get_option( 'gens_raf_cookie_time' ));
                    }
                    do_action('new_raf_data', 'coupon_applied', array('user' => get_current_user_id(), 'referral' => $user_id, 'type' => 'code') );
                    setcookie('gens_raf', $coupon_code, time()+60*60*24*$time, '/');
                }
            }            
        }
    }

    public function add_referral_via_coupon_field( $data, $coupon_code) 
    {
        
        $guest_coupon_code  = get_option('gens_raf_guest_coupon_code');
        if( get_option('gens_raf_referral_codes') !== "yes" || is_admin() || (isset(WC()->cart) &&  WC()->cart->has_discount($guest_coupon_code)) ) {
            return $data;
        }

        $user_id = $this->get_id_from_referral_code($coupon_code);
        if($user_id) {
            if(get_current_user_id() != $user_id) {
                $coupon_post_obj = get_page_by_title($guest_coupon_code, OBJECT, 'shop_coupon');
                $coupon_id = $coupon_post_obj->ID;

                $discount_type              = get_post_meta($coupon_id,"discount_type",true);
                $amount                     = get_post_meta($coupon_id,"coupon_amount",true);
                $product_ids                = get_post_meta($coupon_id,"product_ids",true);
                $exclude_product_ids        = get_post_meta($coupon_id,"exclude_product_ids",true);
                $exclude_product_categories = get_post_meta($coupon_id,"exclude_product_categories",true);
                $product_categories         = get_post_meta($coupon_id,"product_categories",true);
                $minimum_amount             = get_post_meta($coupon_id,"minimum_amount",true);
                $individual_use             = get_post_meta($coupon_id,"individual_use",true);
                $exclude_sale_items         = get_post_meta($coupon_id,"exclude_sale_items",true);
                
                $data = array(
                    'discount_type'               => $discount_type,
                    'amount'                      => $amount,
                    'minimum_amount'              => $minimum_amount,
                    'individual_use'              => $individual_use === "yes" ? true : false,
                    'exclude_sale_items'          => $exclude_sale_items === "yes" ? true : false,
                    'product_ids'                 => array_map( 'intval', (array) explode(',', $product_ids) ),
                    'excluded_product_ids'        => array_map( 'intval', (array) explode(',', $exclude_product_ids) ),
                    'product_categories'          => array_map( 'intval', (array) $product_categories ),
                    'excluded_product_categories' => array_map( 'intval', (array) $exclude_product_categories ),
                    'id'                          => true
                );
            }
        }
        return $data;
    }

    /**
     * Save RAF(User) ID in Order Meta after Order is Complete
     * woocommerce_checkout_update_order_meta hook
     *
     * @since    2.0.0
     * @return   string
     */
    public function maybe_save_raf_id( $order_id ) 
    {
        //1. Check cookie & get referrer or exit
        $referrer_id = $this->check_referrer_cookie();
        if(!$referrer_id)
        {
            return $order_id;
        }
        
        //2. Check filter & is plugin active
        $disable = apply_filters('gens_raf_disable', get_option( 'gens_raf_disable' ), $order_id, $referrer_id);
        if($disable === TRUE || $disable === "yes") 
        {
            return $order_id;
        }
        
        // 3. Save referral id and then work on security.
        if(filter_var($referrer_id, FILTER_VALIDATE_EMAIL)) {
            $rafID = $referrer_id;
        } else {
            $rafID = get_user_meta($referrer_id, "gens_referral_id", true);
        }

        update_post_meta( $order_id, '_raf_id', esc_attr($rafID)); // will be depricated
        update_post_meta( $order_id, '_wpgens_raf_id', esc_attr($rafID));

        $raf_info = $this->security_check($order_id, $referrer_id);

        do_action('new_raf_data', 'new_order', array_merge(array('user' => get_current_user_id(), 'referral' => $referrer_id, 'order' => $order_id), $raf_info) );

        update_post_meta( $order_id, '_raf_meta',$raf_info); // will be depricated
        update_post_meta( $order_id, '_wpgens_raf_meta',$raf_info);        
        return $order_id;
    }

    public function security_check($order_id, $referrer_id)
    {
        $allow_existing   = get_option( 'gens_raf_allow_existing' );
        $minimum_amount   = get_option( 'gens_raf_min_ref_order' );
        $nth_coupon       = intval(get_option( 'gens_raf_nth_coupon' ));

        // Prevent user from checkout as a guest using his email
        $order             = new WC_Order( $order_id );
        $aelia_order_total = get_post_meta( $order_id, '_order_total_base_currency', true );
        $order_total       = $order->get_total();
        $user_email        = ( version_compare( WC_VERSION, '2.7', '<' ) ) ? $order->billing_email : $order->get_billing_email();
        $user_address      = ( version_compare( WC_VERSION, '2.7', '<' ) ) ? $order->billing_address_1 : $order->get_billing_address_1(); 

        $num_referrals = intval(get_user_meta($referrer_id, "gens_num_friends", true)) + 1;
        
        // Check if user exists with this email already in the system?
        $order_statuses =  apply_filters('gens_raf_fraud_order_status', array_keys( wc_get_order_statuses() ));
        $email_exists = get_posts( array(
            'numberposts' => 1,
            'meta_key'    => '_billing_email',
            'meta_value'  => $user_email,
            'post_type'   => wc_get_order_types(),
            'post_status' => $order_statuses,
            'post__not_in' => array($order_id)
        ) );

        $custom_msg = "";
        $raf_info['custom_msg'] = apply_filters('gens_raf_custom_message',$custom_msg);
        if(!empty($email_exists) && $allow_existing != "yes") {
            $raf_info = array("info" => __("Potential Fraud Detected. Referred customer has previous orders with ".$user_email." address. Check plugin general settings to change this.","gens-raf"), "generate" => "false", "increase_referrals" => "false");
        } else if(get_current_user_id() === $referrer_id || $user_email === $referrer_id) {
            $raf_info = array("info" => __("Potential Fraud Detected. User is trying to refer himself.","gens-raf"), "generate" => "false", "increase_referrals" => "false");
        } else if($this->user_has_orders() && $allow_existing != "yes") {
            $raf_info = array("info" => __("This is not a new customer, and settings are set to disable coupons for existing customers.","gens-raf"), "generate" => "false", "increase_referrals" => "false");
        } else if(($user_address === get_user_meta($referrer_id,"billing_address_1",true)) && $user_address != '') {
            $raf_info = array("info" => __("Potential Fraud Detected. Referer and referre have the same billing address. Investigate.","gens-raf"), "generate" => "false", "increase_referrals" => "false");
        } else if($minimum_amount && (($aelia_order_total === '' && $minimum_amount > $order_total) || ($aelia_order_total !== '' && $minimum_amount > $aelia_order_total))) {
            $raf_info = array("info" => __("Order minimum amount of ".$minimum_amount." has not been met.", "gens-raf"), "generate" => "false", "increase_referrals" => "false");
        } else if(!empty($nth_coupon) && ($nth_coupon !== 1) && ($num_referrals % $nth_coupon != 0)) {
            $raf_info = array("info" => __("Coupons wont be generated due to nth coupon option.", "gens-raf"), "generate" => "false", "increase_referrals" => "true");
        } else {
            $raf_info = array("info" => "Referral is fine. Coupon will be generated on the order complete.", "generate" => "true", "increase_referrals" => "true");
        }
        
        return apply_filters("gens_raf_order_info",$raf_info,$order,$referrer_id);
    }

    /**
     * Returning number of orders customer has.
     *
     * @since    2.0.0
     */
    public function user_has_orders($user_email = false) 
    {
        $user_id = get_current_user_id();
        if($user_id == 0) {
            if($user_email !== false && $user_email != '') {
                $email_exists = get_posts( array(
                    'numberposts' => 1,
                    'meta_key'    => '_billing_email',
                    'meta_value'  => $user_email,
                    'post_type'   => wc_get_order_types(),
                    'post_status' => array( 'wc-processing', 'wc-completed' )
                ) );
                return count($email_exists);
            }
            return 0;
        }
        $customer_orders = get_posts( array(
            'numberposts' => 99999,
            'meta_query' => array(
                array(
                    'key'    => '_customer_user',
                    'value'  => $user_id,
                ),
            ),
            'post_type'   => wc_get_order_types(),
            'post_status' => 'wc-completed',
        ) );

        return count($customer_orders);
    }


    public function check_referrer_cookie() 
    {
        // First check cookie
        if(isset($_COOKIE["gens_raf"])) {
            $user_id = $this->get_id_from_referral_code($_COOKIE["gens_raf"]);
            if($user_id) {
                return $user_id;
            }
        }
        // Then check if referral was applied through coupon.
        if(!empty(WC()->cart->applied_coupons)) {
            $coupons = WC()->cart->applied_coupons;
            foreach ($coupons as $coupon) {
                if(substr( $coupon, 0, 3 ) === "ref") {
                    $user_id = $this->get_id_from_referral_code($coupon);
                    if($user_id) {
                        return $user_id;
                    }
                }
            }
        }

        if(isset($_COOKIE["gens_raf"]) && filter_var($_COOKIE["gens_raf"], FILTER_VALIDATE_EMAIL)) {
            return $_COOKIE["gens_raf"];
        }
        // Nothing? Return false.
        return false;
    }


    /**
     * Remove Cookie after checkout if Setting is set
     * woocommerce_thankyou hook
     *
     * @since    1.0.0
     */
    public function remove_cookie_after( $order_id ) 
    {
        $remove = get_option( 'gens_raf_cookie_remove' );
        if (isset($_COOKIE['gens_raf']) && $remove === "yes") {
            unset($_COOKIE['gens_raf']);
            setcookie('gens_raf', '', time() - 3600, '/');
        }
    }


    public function hide_coupon_code($text,$coupon) 
    {
        $guest_coupon_code  = get_option( 'gens_raf_guest_coupon_code' );
        if(method_exists($coupon, "get_code")) { // support for older version of woo
            $coupon_code = $coupon->get_code();
        } else {
            $coupon_code = $coupon->code;
        }
        if($coupon_code == strtolower($guest_coupon_code)) {
            _e("Coupon Applied!","gens-raf");
        } else {
            return $text;
        }
    }

    /**
     * Auto apply coupons at the cart page for referred person, if chosen.
     *
     * @since    1.1.0
     */
    public function apply_matched_coupons() 
    {
        $disabled           = get_option( 'gens_raf_disable' );
        $guest_coupon_stats = get_option( 'gens_raf_guest_enable' );
        $guest_coupon_code  = get_option( 'gens_raf_guest_coupon_code' );
        $guest_coupon_msg   = get_option( 'gens_raf_guest_coupon_msg' );
        $allow_existing     = get_option( 'gens_raf_allow_existing' );
        $referrer_id        = $this->check_referrer_cookie();

        if(!$referrer_id || get_current_user_id() === $referrer_id) {
            return false;
        }

        do_action('gens_raf_auto_apply_coupon',$referrer_id);
        if(filter_var($referrer_id, FILTER_VALIDATE_EMAIL)) {
            $user = get_user_by( 'email', $referrer_id );
            if(!$user) {
                return false;
            }
            $customer_orders = get_posts( array(
                'numberposts' => 1,
                'meta_key'    => '_customer_user',
                'meta_value'  => $user->ID,
                'post_type'   => wc_get_order_types(),
                'post_status' => array_keys( wc_get_order_statuses() ),
            ) );
            if(count( $customer_orders ) < 1) {
                return false;
            }
        } else {
            $user_info = get_userdata($referrer_id);
            if($user_info->first_name != '') {
                $user_name = $user_info->first_name.' '.$user_info->last_name;
            } else {
                $user_name = __("Your friend","gens-raf");
            }
            $guest_coupon_msg = str_replace( '{{name}}', $user_name, $guest_coupon_msg);
        }
        if(!empty(WC()->cart->applied_coupons) || empty($guest_coupon_code) || $disabled === "yes" || WC()->cart->cart_contents_count < 1 || $guest_coupon_stats !== "yes" || ($this->user_has_orders() >= 1 && $allow_existing !== "yes") )
        {
            return false;
        }
        do_action('new_raf_data', 'coupon_applied', array('user' => get_current_user_id(), 'referral' => $referrer_id, 'type' => 'link') );
        WC()->cart->add_discount( $guest_coupon_code );
        wc_add_notice($guest_coupon_msg);
        wc_print_notices();
    }

    /**
     * Remove coupon if user wants to abuse it by adding it as a guest then logging in at the checkout.
     *
     * @since    1.1.0
     */
    public function checkout_form_check($post_data) 
    {
        $user_id = 0;
        $guest_coupon_code  = get_option( 'gens_raf_guest_coupon_code' );
        $allow_existing   = get_option( 'gens_raf_allow_existing' );
        $email_exists = false;
        parse_str($post_data,$data);

        $referrer_id = $this->check_referrer_cookie();
        if(!$referrer_id)
        {
            return false;
        }

        if(isset($data['billing_email']) && $data['billing_email'] != "") {
            // This email already exists? Remove coupon.
            $email_exists = get_posts( array(
                'numberposts' => 1,
                'meta_key'    => '_billing_email',
                'meta_value'  => $data['billing_email'],
                'post_type'   => wc_get_order_types(),
                'post_status' => array( 'wc-processing', 'wc-completed' ),
            ) );

            // In case referral is using his own coupon code.
            $user = get_user_by( 'email', $data['billing_email'] );
            if($user) {
                $raf_code = get_user_meta($user->ID,'gens_referral_id', true);
                if(WC()->cart->has_discount($raf_code)) {
                    WC()->cart->remove_coupon( $raf_code );                                    
                }
            }
        }

        do_action('gens_raf_checkout_check', $guest_coupon_code, $referrer_id);

        if (
            (isset($_COOKIE["gens_raf_guest"]) && $data['billing_email'] === $_COOKIE["gens_raf_guest"]) || 
                get_current_user_id() === $referrer_id || 
                    (($this->user_has_orders($data['billing_email']) > 0 || !empty($email_exists)) && $allow_existing != "yes") 
                        ) {
                            if(WC()->cart->has_discount($guest_coupon_code)){
                                WC()->cart->remove_coupon( $guest_coupon_code );
                                wc_add_notice( __( 'The coupon has been removed from your cart.', 'gens-raf' ), 'error' );
                            }
                            if(isset($_COOKIE["gens_raf"]) && !empty($_COOKIE["gens_raf"]) && WC()->cart->has_discount($_COOKIE["gens_raf"])) {
                                WC()->cart->remove_coupon( $_COOKIE["gens_raf"] );
                                wc_add_notice( __( 'The coupon has been removed from your cart.', 'gens-raf' ), 'error' );
                            }
        }

        // Because we cant remove multiple coupons during applying of them....
        if(WC()->cart->has_discount() && count(WC()->cart->get_applied_coupons()) > 1) {
            $coupons = WC()->cart->get_applied_coupons();
            $i = 0;
            foreach ($coupons as $coupon) {
                if(substr( $coupon, 0, 3 ) === "ref" || $coupon == $guest_coupon_code) {
                    $i++;           
                }
                if($i > 1) {
                    WC()->cart->remove_coupon( $coupon );
                }
            }
        }

    }


    public function get_id_from_referral_code($referral_code) {
        global $wpdb;
        $results = $wpdb->get_results($wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key='gens_referral_id' AND meta_value = %s", $referral_code));
        if(!empty($results)) {
            return (int)$results[0]->user_id;
        }
        return false;
    }

}

$wpgens_raf_checkout = new WPGens_RAF_Checkout();