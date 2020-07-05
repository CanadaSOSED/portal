<?php

/**
 * Manages WordPress Meme Shortcode options.
 *
 */
class WPGens_RAF_User
{
    /**
     * @var string
     */
    private $user_id;

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct( $user_id ){
        $this->user_id = $user_id;
        $this->hide_no_orders = get_option( 'gens_raf_hide_no_orders' );
        $this->hide_no_orders_text = get_option( 'gens_raf_hide_no_orders_text' );
    }

    /**
     * Load the plugin options from WordPress.
     *
     * @return WPGens_RAF_User
     */
    public function get_referral_id() {

        if ( !$this->user_id ) {
            return false;
        }
        
        if($this->hide_no_orders === "yes") {
            
            $customer_orders = get_posts( array(
                'numberposts' => 1,
                'meta_key'    => '_customer_user',
                'meta_value'  => $this->user_id,
                'post_type'   => wc_get_order_types(),
                'post_status' => array( 'wc-processing', 'wc-completed' )
            ) );
            if(count($customer_orders) < 1) {
                return $this->hide_no_orders_text;
            }
        }

        $referral_id = get_user_meta($this->user_id, "gens_referral_id", true);
        if($referral_id && $referral_id != "") {
            return apply_filters('wpgens_raf_code', $referral_id);
        } else {
            do{
                $referral_id = $this->generate_referral_id();
            } while ($this->exists_ref_id($referral_id));
            update_user_meta( $this->user_id, 'gens_referral_id', $referral_id );
            return apply_filters('wpgens_raf_code', $referral_id);
        }

    }

    /**
     * Check if ID already exists
     *
     * @since    2.0.0
     * @return string
     */
    public function exists_ref_id($referral_id) {
        $args = array('meta_key' => "gens_referral_id", 'meta_value' => $referral_id );
        if (get_users($args)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Generate a new Referral ID
     *
     * @since    2.0.0
     * @return string
     */
    function generate_referral_id($randomString="ref")
    {
        $characters = "0123456789";
        for ($i = 0; $i < 7; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    /**
     * Get number of referrals for a user
     *
     * @since    2.0.0
     * @return   string
     */
    public function get_number_of_referrals() {
        $number = get_user_meta($this->user_id, "gens_num_friends", true);
        if(!empty($number)) {
            return $number;
        } else {
            return 0;
        }
    }

    /**
     * Generate referral URL for front end(product tab,shortcode & my account page)
     *
     * @since    2.0.0
     * @return   string
     */
    public function generate_referral_url($type,$url = NULL) 
    {

        if($this->hide_no_orders === "yes") {

            $customer_orders = get_posts( array(
                'numberposts' => 1,
                'meta_key'    => '_customer_user',
                'meta_value'  => $this->user_id,
                'post_type'   => wc_get_order_types(),
                'post_status' => array( 'wc-processing', 'wc-completed' )
            ) );
            if(count($customer_orders) < 1) {
                return $this->hide_no_orders_text;
            }
        }

        global $wp;
        $referral_id = $this->get_referral_id();
        $link = get_home_url();
        
        switch ($type) {
            case 'product_tab':
                $refLink = esc_url(home_url(add_query_arg(array('raf' => $referral_id),trailingslashit($wp->request))));
            break;
            case 'shortcode':
                if($url) {
                    $link = $url;
                }
                $refLink = esc_url(add_query_arg( 'raf', $referral_id, trailingslashit($link) ));
            break;
            default:
                $my_account_url = get_option( 'gens_raf_my_account_url' );
                if($my_account_url != "") {
                    $link = $my_account_url;
                }
                $refLink = esc_url(add_query_arg( 'raf', $referral_id, trailingslashit($link) ));
            break;
        }

        // If its a guest and cookie is set?
        if(!is_user_logged_in() && isset($_COOKIE['gens_raf_guest'])) {
            $refLink = $refLink .'?raf='.$_COOKIE['gens_raf_guest'];
        }

        if(isset($_GET['order']) && !is_user_logged_in()) {
            $order = new WC_Order( $_GET['order'] );
            $user_email = ( version_compare( WC_VERSION, '2.7', '<' ) ) ? $order->billing_email : $order->get_billing_email();  
            $refLink = $refLink .'?raf='.$user_email;
        }

        return apply_filters('wpgens_raf_link', $refLink, $referral_id, $type);
    }

    /**
     * Create referral code on new user registration, in case someone needs meta fields for mailchimp and such, 
     * otherwise its created when customer checks page with referral link.
     *
     * @since 2.0.0
     */
    public static function new_user_add_referral_id( $user_id ) {
        $referral = new WPGens_RAF_User($user_id);
        $referral->get_referral_id();
    }

    /**
     * Increase referrals number on every success RAF order. 
     *
     * @since 2.0.0
     */
    public static function set_number_of_referrals( $user_id ) {
        if(!filter_var($user_id, FILTER_VALIDATE_EMAIL)) {
            $referral = new WPGens_RAF_User($user_id);
            $num_friends_refered = $referral->get_number_of_referrals();
            update_user_meta( $user_id, 'gens_num_friends', (int)$num_friends_refered + 1 );
        }

    }
}