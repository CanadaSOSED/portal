<?php
/**
 * Share Ajax Call
 * @author WPGens
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPGENS_RAF_Share {

	public function __construct() 
	{
 		add_action( 'wp_ajax_gens_share_via_email', array( $this, 'gens_share_via_email') );
        add_action( 'wp_ajax_nopriv_gens_share_via_email', array( $this, 'gens_share_via_email') );
	}

    /**
     * When share via email is clicked.
     *
     * @since    1.0.0
     */
    public static function gens_share_via_email() 
    {

        global $woocommerce;
        $mailer           = $woocommerce->mailer();
        $user_info        = get_userdata(get_current_user_id());
        if($user_info->first_name != '') {
            $user_name = $user_info->first_name.' '.$user_info->last_name;
        } else {
            $user_name = __("Your friend","gens-raf");
        }

        $subject          = str_replace( '{{name}}', $user_name, __(get_option( 'gens_raf_email_subject_share' ),'gens-raf'));
        $heading          = str_replace( '{{name}}', $user_name, __(get_option( 'gens_raf_email_heading_share' ),'gens-raf'));
        $user_message     = str_replace( '{{name}}', $user_name, __(get_option( 'gens_raf_email_body' ),'gens-raf'));
        $use_woo_template = get_option( 'gens_raf_use_woo_mail' );
        $color            = get_option( 'woocommerce_email_base_color' );
        $footer_text      = get_option( 'woocommerce_email_footer_text' );
        $header_image     = get_option( 'woocommerce_email_header_image' );
        $from             = get_option( 'gens_raf_email_from' );
        $my_account_url   = get_option( 'gens_raf_my_account_url' );

        $friends_data     = $_POST['data'];
        $refLink          = esc_url($_POST['link']);
        $raf_user         = new WPGens_RAF_User(get_current_user_id());
        $refCode          = $raf_user->get_referral_id();
        
        // Fallback for {{code}} which is depricated
        $user_message  = str_replace( '{{code}}', $refLink, $user_message);

        ob_start();

        if($use_woo_template === "yes") {
            wc_get_template( 'emails/email-header.php', array( 'email_heading' => $subject ) );
        } else {
            include WPGens_RAF::get_template_path('email-header.php','/emails');
        }
        
        include WPGens_RAF::get_template_path('email-body-share.php','/emails');
        
        if($use_woo_template === "yes") {
            wc_get_template( 'emails/email-footer.php' );
        } else {
            include WPGens_RAF::get_template_path('email-footer.php','/emails');
        }

        $message = ob_get_clean();

        if($from == "yes") {
            add_filter( 'woocommerce_email_from_name', array( 'WPGENS_RAF_Share', 'gens_get_from_name' ), 10 );
            add_filter( 'woocommerce_email_from_address', array( 'WPGENS_RAF_Share', 'gens_get_from_address' ), 10 );            
        }
    
        foreach ($friends_data as $data) {
            $new_message = str_replace( '{{friend_name}}', sanitize_text_field($data['name']), $message );
            $new_subject = str_replace( '{{friend_name}}', sanitize_text_field($data['name']), $subject );
            $mailer->send( sanitize_text_field($data['email']), $new_subject, $new_message);
            do_action('new_raf_data', 'email_share', array('user' => get_current_user_id(), 'email' => sanitize_text_field($data['email']), 'name' => sanitize_text_field($data['name'])) );
        }

        if($from == "yes") {
            remove_filter( 'woocommerce_email_from_name', array( 'WPGENS_RAF_Share', 'gens_get_from_name' ), 10 );
            remove_filter( 'woocommerce_email_from_address', array( 'WPGENS_RAF_Share', 'gens_get_from_address' ), 10 );         
        }

        wp_send_json_success();

    }

    public static function gens_get_from_name() {
        $current_user = wp_get_current_user();
        return $current_user->user_firstname." ".$current_user->user_lastname;
    }

    public static function gens_get_from_address() {
        $current_user = wp_get_current_user();
        return $current_user->user_email;
    }

}

new WPGENS_RAF_Share();