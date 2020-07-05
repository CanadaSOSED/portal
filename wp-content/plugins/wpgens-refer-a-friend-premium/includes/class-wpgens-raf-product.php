<?php
/**
 * Handle RAF Product Tab
 *
 * @since     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPGens_RAF_Product {

    /**
     * Hook in methods.
     */
    public function __construct() {
        add_filter( 'woocommerce_product_tabs', array( $this, 'raf_product_tab') );
    }


    public function raf_product_tab($tabs) 
    {
        $tabs_hide = apply_filters('wpgens_raf_tab_disable',get_option( 'gens_raf_tabs_disable' ));
        if($tabs_hide !== TRUE && $tabs_hide !== "yes") {
            $tabs['refer_tab'] = array(
                'title'     => apply_filters("gens_raf_tab_title", __( 'Refer a Friend', 'gens-raf' )),
                'priority'  => 40,
                'callback'  => array($this,'raf_product_tab_content')
            );          
        }

        return $tabs;
    }

    public function raf_product_tab_content() 
    {
        $share_text  = __(get_option( 'gens_raf_share_text' ),'gens-raf');
        $guest_text  = __(get_option( 'gens_raf_guest_text' ),'gens-raf');
        $title       = __(get_option( 'gens_raf_twitter_title' ),'gens-raf');
        $twitter_via = __(get_option( 'gens_raf_twitter_via' ),'gens-raf');
        $email_hide  = get_option( 'gens_raf_email_hide' );
        $linkedin     = get_option( 'gens_raf_linkedin' );
        $pinterest     = get_option( 'gens_raf_pinterest' );
        $whatsapp     = get_option( 'gens_raf_whatsapp' );

        $template_path  = WPGens_RAF::get_template_path('product-tab.php','',TRUE);
        $rafLink        = $this->get_referral_link();
        
        $referral_code  = get_option( 'gens_raf_referral_codes' );
        $raf_id         = $this->get_referral_id();
        $allow_guests   = get_option( 'gens_raf_allow_guests' );
        $guest_cookie_class   = isset($_COOKIE['gens_raf_guest']) && $allow_guests === "yes" ? "guest_cookie_true" : "guest_cookie_false";

        if (!is_readable($template_path)) {
            return sprintf('<!-- Could not read "%s" file -->', $template_path);
        }

        ob_start();

        include $template_path;

        echo ob_get_clean();
    }

    public function get_referral_link() 
    {
        $raf_user = new WPGens_RAF_User(get_current_user_id());
        return $raf_user->generate_referral_url('product_tab');
    }

    public function get_referral_id() 
    {
        $referral = new WPGens_RAF_User(get_current_user_id());
        return $referral->get_referral_id();
    }

}