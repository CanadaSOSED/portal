<?php

class WPGens_RAF_Email
{

    private $email;

    private $coupon_code;

    private $name;

    private $order_id;

    private $type;

    /**
     * Constructor.
     *
     * @param array $options
     */
    public function __construct($email, $coupon_code, $order_id, $type = "referrer")
    {
        $this->email        = $email;
        $this->coupon_code  = $coupon_code;
        $this->order_id     = $order_id;
        $this->type         = $type;
        $this->name         = $this->get_user_name();
    }

    /**
     * Send Email to user
     *
     * @since    1.0.0
     */
    public function send_email() 
    {
        global $woocommerce;
        $mailer = $woocommerce->mailer();

        $subject          = str_replace( '{{name}}', $this->name, get_option( 'gens_raf_email_subject' ));
        $heading          = str_replace( '{{name}}', $this->name, get_option( 'gens_raf_email_heading' ));
        $use_woo_template = get_option( 'gens_raf_use_woo_mail' );
        $color            = get_option( 'woocommerce_email_base_color' );
        $footer_text      = get_option( 'woocommerce_email_footer_text' );
        $header_image     = get_option( 'woocommerce_email_header_image' );
        $expiry           = get_option( 'gens_raf_coupon_duration' );
        // Referrer or friend?
        if($this->type === "friend") {
            $user_message  = str_replace( '{{name}}', $this->name, get_option( 'gens_raf_buyer_email_message' ));
        } else {
            $user_message  = str_replace( '{{name}}', $this->name, get_option( 'gens_raf_email_message' ));
        }
        // Fallback for {{code}} which is depricated
        $user_message  = str_replace( '{{code}}', $this->coupon_code, $user_message);
        // Expiry date
        if($expiry) {
            $expiry = date_i18n(wc_date_format(), strtotime('+'.$expiry.' days'));
        }
        $coupon_code      = $this->coupon_code;

        ob_start();

        if($use_woo_template === "yes") {
            wc_get_template( 'emails/email-header.php', array( 'email_heading' => $subject ) );
        } else {
            include WPGens_RAF::get_template_path('email-header.php','/emails');            
        }

        include WPGens_RAF::get_template_path('email-body-coupon.php','/emails');

        if($use_woo_template === "yes") {
            wc_get_template( 'emails/email-footer.php' );
        } else {
            include WPGens_RAF::get_template_path('email-footer.php','/emails');            
        }

        $message = ob_get_clean();
        // Debug wp_die($user_email);
        $mailer->send( $this->email, $subject, $message);
    }

    public function get_user_name()
    {
        $user = get_user_by( 'email', $this->email );
        if(!empty($user)) {
            return ucwords($user->first_name);            
        } else {
            $order = new WC_Order( $this->order_id );
            $billing_first_name = ( version_compare( WC_VERSION, '2.7', '<' ) ) ? $order->billing_first_name : $order->get_billing_first_name(); 
            return ucwords($billing_first_name);
        }
    }

}