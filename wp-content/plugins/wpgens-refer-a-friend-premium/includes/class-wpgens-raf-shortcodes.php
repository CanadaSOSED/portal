<?php

/**
 * RAF ShortCodes
 *
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WPGens_RAF_Shortcodes
{

    /**
     * Init shortcodes.
     * @since 2.0.0
     */
    public static function init() 
    {   
        // Init Main Shortcodes
        $shortcodes = array(
            'WOO_GENS_RAF' => __CLASS__.'::simple_shortcode',
            'WOO_GENS_RAF_ADVANCE' => __CLASS__.'::advance_shortcode',
            'WOO_GENS_RAF_FULL' => __CLASS__.'::full_shortcode',
        );

        foreach ($shortcodes as $shortcode => $function) {
            add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
        }

        // Init CF7 Shortcode - maybe move to extensions.
        if(function_exists('wpcf7_add_form_tag')){
            wpcf7_add_form_tag( 'gens_raf', __CLASS__.'::cf7_shortcode' );
        }
    }

    public static function simple_shortcode($atts)
    {
        $atts = shortcode_atts( array(
            'id' => get_current_user_id(),
            'url' => get_home_url()
        ), $atts, 'WOO_GENS_RAF' );

        if($atts['id'] === 0) {
            return false;
        }

        $raf_user = new WPGens_RAF_User($atts['id']);
        $rafLink = $raf_user->generate_referral_url('shortcode',$atts['url']);

        return "<a class='wpgens-raf-simple-shortcode' href='".$rafLink."'>".$rafLink."</a>";

    }

    public static function advance_shortcode($atts)
    {
        $atts = shortcode_atts( array(
            'guest_text' => 'Please register to get your referral link.',
            'url'        => get_home_url(),
            'id'         => get_current_user_id()
        ), $atts, 'WOO_GENS_RAF_ADVANCE' );

        $guest_text  = $atts['guest_text'];
        $title       = __(get_option( 'gens_raf_twitter_title' ),'gens-raf');
        $twitter_via = __(get_option( 'gens_raf_twitter_via' ),'gens-raf');

        $raf_user = new WPGens_RAF_User($atts['id']);
        $rafLink = $raf_user->generate_referral_url('shortcode',$atts['url']);
        
        $referral_code  = get_option( 'gens_raf_referral_codes' );
        $raf_id = $raf_user->get_referral_id();

        $template_path  = WPGens_RAF::get_template_path('advance-shortcode.php','',TRUE);

        if (!is_readable($template_path)) {
            return sprintf('<!-- Could not read "%s" file -->', $template_path);
        }

        ob_start();

        include $template_path;

        return ob_get_clean();
    }

    public static function full_shortcode($atts)
    {
        $atts = shortcode_atts( array(
            'guest_text' => 'Please register to get your referral link.',
        ), $atts, 'WOO_GENS_RAF_FULL' );

        if(!is_user_logged_in()) {
            $guest_text  = $atts['guest_text'];
            ob_start();
            include WPGENS_RAF_ABSPATH . 'templates/guest-advance-shortcode.php';
            return ob_get_clean();
        }

        $full = new WPGens_RAF_MyAccount(false);

        ob_start();

        $full->gens_account_referral_content();            

        return ob_get_clean();
    }

    /**
     * Register RAF link as ContactForm7 shortcode.
     *
     * @since    2.0.0
     */
    public function cf7_shortcode() 
    {
        $raf_user = new WPGens_RAF_User($atts['id']);
        $rafLink = $raf_user->generate_referral_url('shortcode');
        return '<input type="hidden" name="gens_raf" value="'.$rafLink.'" />';
    }

}