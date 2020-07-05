<?php
/**
 * Handle RAF Tab on My Account Screen
 *
 * @since     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPGens_RAF_MyAccount {

    /**
     * Hook in methods.
     */
    public function __construct($account_page = TRUE) {

        if($account_page === TRUE) {
            add_action( 'init', array( $this, 'gens_myreferral_tab'));
            add_filter( 'woocommerce_account_menu_items', array( $this, 'gens_account_menu_item'), 10, 1 );            
        }
        add_action( 'woocommerce_account_myreferrals_endpoint', array( $this, 'gens_account_referral_content') );
    }

    public function gens_myreferral_tab(){
        add_rewrite_endpoint( 'myreferrals', EP_PAGES );
    }

    public function gens_account_menu_item( $items ) {
        // Remove the logout menu tab.
        $logout_exists = false;
        if(isset($items['customer-logout'])) {
            $logout_exists = true;
            $logout = $items['customer-logout'];
            unset( $items['customer-logout'] );            
        }

        // Insert RAF Tab.
        $items['myreferrals'] = apply_filters("gens_raf_tab_title", __( 'Refer a Friend', 'gens-raf' ));
        // Insert back the logout tab.
        if($logout_exists) {
            $items['customer-logout'] = $logout;
        }
        return $items;
    }

    public function gens_account_referral_content() {
        $share_text     = __(get_option( 'gens_raf_myaccount_text' ),'gens-raf');
        $title          = __(get_option( 'gens_raf_twitter_title' ),'gens-raf');
        $twitter_via    = __(get_option( 'gens_raf_twitter_via' ),'gens-raf');
        $email_hide     = get_option( 'gens_raf_email_hide' );
        $linkedin     = get_option( 'gens_raf_linkedin' );
        $pinterest     = get_option( 'gens_raf_pinterest' );
        $whatsapp     = get_option( 'gens_raf_whatsapp' );

        $referral_code  = get_option( 'gens_raf_referral_codes' );
        $template_path  = WPGens_RAF::get_template_path('myaccount-tab.php');
        $rafLink        = $this->get_referral_link();
        $raf_id         = $this->get_referral_id();
        $coupons        = $this->prepare_coupons();
        $referrer_data  = $this->prepare_friends();
        
        if (!is_readable($template_path)) {
            return sprintf('<!-- Could not read "%s" file -->', $template_path);
        }

        ob_start();

        include $template_path;

        echo ob_get_clean();
    }


    /**
     * Account page - get unused referral coupons
     *
     * @since    1.0.0
     */
    public function prepare_coupons() {
        $user_info = get_userdata(get_current_user_id());
        $user_email = $user_info->user_email;
        $date_format = get_option( 'date_format' );
        $args = array(
            'posts_per_page'   => -1,
            'post_type'        => 'shop_coupon',
            'post_status'      => 'publish',
            'meta_query' => array (
                array (
                  'key' => 'customer_email',
                  'value' => $user_email,
                  'compare' => 'LIKE'
                )
            ),
        );
        $raf_coupons = array();
        $coupons = get_posts( $args );

        if($coupons) {
            $i = 0;
            foreach ( $coupons as $coupon ) {
                if(substr( $coupon->post_title, 0, 3 ) != "RAF") {
                    continue;
                }
                $discount = esc_attr(get_post_meta($coupon->ID, "coupon_amount" ,true));
                $separator = get_option( 'woocommerce_price_decimal_sep', '.' );
                $discount_type = esc_attr(get_post_meta($coupon->ID, "discount_type" ,true));
                $usageCount = esc_attr(get_post_meta($coupon->ID, "usage_count" ,true));
                $usageLimit = esc_attr(get_post_meta($coupon->ID,'usage_limit', true));
                $expiry_date = esc_attr(get_post_meta($coupon->ID,"expiry_date",true));
                if($expiry_date == "") {
                    $expiry_date = __('No expiry date','gens-raf');
                } else {
                    $date = new DateTime();
                    $expiry_date = date_i18n(wc_date_format(), strtotime($expiry_date));
                }
                if($discount_type === "percent_product" || $discount_type === "percent" || $discount_type === "sign_up_fee_percent" || $discount_type === "recurring_percent") {
                    $discount = $discount."%";
                } else {
                    $discount = wc_price($discount);
                }

                $usageLimitText = $usageLimit ? $usageLimit : __('Unlimited','gens-raf');
                
                // If coupon isnt used yet.
                if($usageCount < $usageLimit || $usageLimit === '') {
                    $raf_coupons[$i]['title']    = $coupon->post_title;
                    $raf_coupons[$i]['discount'] = str_replace(".", $separator,$discount);
                    $raf_coupons[$i]['usageCount'] = $usageCount.'/'.$usageLimitText;
                    $raf_coupons[$i]['expiry']   = $expiry_date;
                } 
                $i++;
            }
        }
        return $raf_coupons;
    }


    /**
     * Account page - Get all referrals made by user referral code.
     *
     * @since    1.2.0
     */
    public function prepare_friends() {
        $raf_user = new WPGens_RAF_User(get_current_user_id());
        $referral_id = $raf_user->get_referral_id();
        $data = array();
        $potential_orders = 0;
        $friends = array();
        $args = array(
            'meta_query'  => array(
                array(
                    'key' => '_raf_id',
                    'value' => $referral_id,
                    'compare' => '='
                )
            ),
            'post_type'   => wc_get_order_types(),
            'post_status' => array_keys( wc_get_order_statuses() ),
            'posts_per_page' => 999 // faster query
        );
        $orders = get_posts( $args );

        foreach ( $orders as $order ) {
            $raf_meta = get_post_meta( $order->ID, '_raf_meta', true );
            $status = get_post_status($order->ID);
            if(isset($raf_meta['publish']) && $raf_meta['publish'] == "false") {
                continue;
            }
            if($status == 'wc-pending' || $status == 'wc-on-hold' || $status == 'wc-processing') {
                $potential_orders++;                
            }
        }
        ?>
        <?php
        if($orders) {
            $i = 0;
            foreach ( $orders as $order ) {
                $raf_meta = get_post_meta( $order->ID, '_raf_meta', true );
                $order = new WC_Order($order->ID);
                if(isset($raf_meta['publish']) && $raf_meta['publish'] == "false") {
                    continue;
                }
                // Order ID, support 2.6
                $order_id = ( version_compare( WC_VERSION, '2.7', '<' ) ) ? $order->id : $order->get_id();  

                // User
                if ( $order->get_user_id() ) {
                    $user = $order->get_user();
                    $user = $user->display_name;
                } else {
                    $user = __( 'Guest', 'gens-raf' );
                }
                // Date, support 2.6
                if(method_exists($order, "get_date_created")) {
                    $date = date_i18n(wc_date_format(), strtotime($order->get_date_created()));
                } else {
                    $date = date_i18n(wc_date_format(), strtotime($order->order_date));
                }
                $friends[$i]['name']   = $user;
                $friends[$i]['date']   = $date;
                $friends[$i]['status'] = wc_get_order_status_name($order->get_status());
                $i++;
            }
        }
        if($raf_user->get_number_of_referrals() > 0) {
            $data['num_friends_refered'] = $raf_user->get_number_of_referrals();
        } else {
            $data['num_friends_refered'] = 0;
        }
        $data['potential_orders']    = $potential_orders;
        $data['friends']             = $friends;
        return $data;
    }


    public function get_referral_link() 
    {
        $raf_user = new WPGens_RAF_User(get_current_user_id());

        return $raf_user->generate_referral_url('account');
    }

    public function get_referral_id() 
    {
        $referral = new WPGens_RAF_User(get_current_user_id());
        return $referral->get_referral_id();
    }

}