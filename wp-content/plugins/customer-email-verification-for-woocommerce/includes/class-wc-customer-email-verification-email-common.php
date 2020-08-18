<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_customer_email_verification_email_Common {	

	public static $wuev_user_id = null;
	public static $wuev_myaccount_page_id = null;
	
	/**
	 * Initialize the main plugin function
	*/
    public function __construct() {
				
    }
	
	public static function init() {	
		add_filter( 'wc_cev_decode_html_content', array( $this, 'wc_cev_decode_html_content' ), 1 );
		add_filter( 'verification_email_email_body', array( $this, 'content_do_shortcode' ) );
	}

	public static function code_mail_sender( $email ) {
				
		$verification_pin = WC_customer_email_verification_email_Common::generate_verification_pin();
		$cev_initialise_customizer_settings = new cev_initialise_customizer_settings();		
		
		$user_id = self::$wuev_user_id;				
		
		update_user_meta( $user_id, 'cev_email_verification_pin', $verification_pin );		
		
		$result                      = false;		
		
		$email_subject               = get_option('cev_verification_email_subject',$cev_initialise_customizer_settings->defaults['cev_verification_email_subject']);
		$email_subject = WC_customer_email_verification_email_Common::maybe_parse_merge_tags( $email_subject );
		
		$email_heading               = get_option('cev_verification_email_heading',$cev_initialise_customizer_settings->defaults['cev_verification_email_heading']);					
		
		$mailer = WC()->mailer();
		ob_start();
		$mailer->email_header( $email_heading );		
		$email_body = get_option('cev_verification_email_body',$cev_initialise_customizer_settings->defaults['cev_verification_email_body']);
		echo $email_body;
		$mailer->email_footer();
		$email_body = ob_get_clean();
		$email_abstract_object = new WC_Email();
		$email_body = apply_filters( 'woocommerce_mail_content', $email_abstract_object->style_inline( wptexturize( $email_body ) ) );		
		
		$email_body = WC_customer_email_verification_email_Common::maybe_parse_merge_tags( $email_body );
		$email_body = apply_filters( 'wc_cev_decode_html_content', $email_body );
		
		$result = $mailer->send( $email, $email_subject, $email_body );

		return $result;
	}

	public static function content_do_shortcode( $content ) {
		return do_shortcode( $content );
	}	
	
	/*
	 * This function removes backslashes from the textfields and textareas of the plugin settings.
	 */
	public static function wc_cev_decode_html_content( $content ) {
		if ( empty( $content ) ) {
			return '';
		}
		$content = preg_replace( '#<script(.*?)>(.*?)</script>#is', '', $content );

		return html_entity_decode( stripslashes( $content ) );
	}
	/**
	 * Maybe try and parse content to found the xlwuev merge tags
	 * And converts them to the standard wp shortcode way
	 * So that it can be used as do_shortcode in future
	 *
	 * @param string $content
	 *
	 * @return mixed|string
	 */
	public static function maybe_parse_merge_tags( $content = '' ) {
		$get_all      = self::get_all_tags();
		$get_all_tags = wp_list_pluck( $get_all, 'tag' );

		//iterating over all the merge tags
		if ( $get_all_tags && is_array( $get_all_tags ) && count( $get_all_tags ) > 0 ) {
			foreach ( $get_all_tags as $tag ) {
				$matches = array();
				$re      = sprintf( '/\{{%s(.*?)\}}/', $tag );
				$str     = $content;

				//trying to find match w.r.t current tag
				preg_match_all( $re, $str, $matches );

				//if match found
				if ( $matches && is_array( $matches ) && count( $matches ) > 0 ) {

					//iterate over the found matches
					foreach ( $matches[0] as $exact_match ) {

						//preserve old match
						$old_match        = $exact_match;
						$single           = str_replace( '{{', '', $old_match );
						$single           = str_replace( '}}', '', $single );
						$get_parsed_value = call_user_func( array( __CLASS__, $single ) );
						$content          = str_replace( $old_match, $get_parsed_value, $content );
					}
				}
			}
		}

		return $content;
	}

	/*
	 * Mergetag callback for showing sitename.
	 */

	public static function get_all_tags() {
		$tags = array(
			array(
				'name' => __( 'User login', 'customer-email-verification-for-woocommerce' ),
				'tag'  => 'cev_user_login',
			),
			array(
				'name' => __( 'User display name', 'customer-email-verification-for-woocommerce' ),
				'tag'  => 'cev_display_name',
			),
			array(
				'name' => __( 'User email', 'customer-email-verification-for-woocommerce' ),
				'tag'  => 'cev_user_email',
			),
			array(
				'name' => __( 'Email Verification link', 'customer-email-verification-for-woocommerce' ),
				'tag'  => 'cev_user_verification_link',
			),			
			array(
				'name' => __( 'Verification link', 'customer-email-verification-for-woocommerce' ),
				'tag'  => 'customer_email_verification_code',
			),
			array(
				'name' => __( 'Resend Confirmation Email', 'customer-email-verification-for-woocommerce' ),
				'tag'  => 'cev_resend_email_link',
			),	
			array(
				'name' => __( 'Verification Pin', 'customer-email-verification-for-woocommerce' ),
				'tag'  => 'cev_user_verification_pin',
			),		
		);

		return $tags;
	}
	
	protected static function customer_email_verification_code() {
		$secret      = get_user_meta( self::$wuev_user_id, 'customer_email_verification_code', true );		
		return $secret;
	}
	
	protected static function cev_user_login() {
		$user = get_userdata( self::$wuev_user_id );		
		$user_login = $user->user_login ;	
		return $user_login;
	}
	
	protected static function cev_user_email() {
		$user = get_userdata( self::$wuev_user_id );		
		$user_email = $user->user_email ;	
		return $user_email;
	}
	
	protected static function cev_display_name() {		
		$user = get_userdata( self::$wuev_user_id );		
		$display_name = $user->display_name;

		return $display_name;
	}
	
	protected static function cev_user_verification_link(){
		$secret      = get_user_meta( self::$wuev_user_id, 'customer_email_verification_code', true );
		$create_link = $secret . '@' . self::$wuev_user_id;
		$hyperlink   = add_query_arg( array(
			'cusomer_email_verify' => base64_encode( $create_link ),
		), get_the_permalink( self::$wuev_myaccount_page_id ) );		
		$link        = '<a href="' . $hyperlink . '">'.__( 'Email Verification link', 'customer-email-verification-for-woocommerce' ).'</a>';

		return $link;
	}
	public static function cev_resend_email_link(){
		$link = add_query_arg( array(
			'cev_confirmation_resend' => base64_encode( self::$wuev_user_id ),
		), get_the_permalink( self::$wuev_myaccount_page_id ) );
		$resend_confirmation_text = __( 'Resend Confirmation Email', 'customer-email-verification-for-woocommerce' );
		$cev_resend_link          = '<a href="' . $link . '">' . $resend_confirmation_text . '</a>';

		return $cev_resend_link;
	}
	
	public static function cev_user_verification_pin(){	
		
		$user_id = self::$wuev_user_id;			
		
		$cev_email_verification_pin = get_user_meta( $user_id,  'cev_email_verification_pin', true );
		
		if($user_id == 1){
			$cev_email_verification_pin = 1234;	
		}
		
		return '<h1>'.$cev_email_verification_pin.'</h1>';
	}
	
	public static function generate_verification_pin(){
		$digits = 4;
		$i = 0; //counter
		$pin = ""; //our default pin is blank.
		while($i < $digits){
			//generate a random number between 0 and 9.
			$pin .= mt_rand(0, 9);
			$i++;
		}
		return $pin;
	}
}
/**
 * Returns an instance of zorem_woo_il_post.
 *
 * @since 1.0
 * @version 1.0
 *
 * @return zorem_woo_il_post
*/
function WC_customer_email_verification_email_Common() {
	static $instance;

	if ( ! isset( $instance ) ) {		
		$instance = new WC_customer_email_verification_email_Common();
	}

	return $instance;
}

/**
 * Register this class globally.
 *
 * Backward compatibility.
*/
$GLOBALS['WC_customer_email_verification_email_Common'] = WC_customer_email_verification_email_Common();