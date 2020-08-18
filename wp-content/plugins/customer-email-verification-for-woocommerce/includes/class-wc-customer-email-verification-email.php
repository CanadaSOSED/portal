<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_customer_email_verification_email {				
	
	public $is_user_already_verified = false;
	public $is_new_user_email_sent = false;
	private $user_id;
	/**
	 * Initialize the main plugin function
	*/
    public function __construct() {		

		$this->my_account = get_option( 'woocommerce_myaccount_page_id' );

		if ( '' === $this->my_account ) {
			$this->my_account = get_option( 'page_on_front' );
		}
	}
	
	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	 */
	private static $instance;
	
	/**
	 * Get the class instance
	 *
	 * @return WC_Advanced_Shipment_Tracking_Admin
	*/
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	/*
	* init from parent mail class
	*/
	public function init(){
		add_shortcode( 'customer_email_verification_code', array( $this, 'customer_email_verification_code' ) );
		add_action( 'woocommerce_created_customer_notification', array( $this, 'new_user_registration_from_registration_form' ), 10, 3 );
		add_action( 'woocommerce_email_footer', array( $this, 'append_content_before_woocommerce_footer' ), 9, 1 );
		add_action( 'wp', array( $this, 'authenticate_user_by_email' ) );
		//add_action( 'wp_login', array( $this, 'custom_form_login_check' ), 10, 1 );
		add_filter( 'woocommerce_registration_redirect', array( $this, 'redirect_user_after_registration' ) );	
		add_filter( 'wcalr_register_user_successful', array( $this, 'wcalr_register_user_successful_fun' ) );	
		add_action( 'wp', array( $this, 'show_cev_notification_message_after_register' ) );
		add_action( 'wp', array( $this, 'cev_resend_verification_email' ) );		
		add_action( 'wp', array( $this, 'check_user_and_redirect_to_endpoint' ) );						
		add_action( 'wp_ajax_nopriv_cev_verify_user_email_with_pin', array( $this, 'cev_verify_user_email_with_pin_fun') );
		add_action( 'wp_ajax_cev_verify_user_email_with_pin', array( $this, 'cev_verify_user_email_with_pin_fun') );
		add_action( 'user_register', array( $this, 'cev_verify_user_email_on_registration_checkout'), 10, 1 );
	}
	

	/**
	 * This function is executed when a new user is made from the woocommerce registration form in the myaccount page.
	 * Its hooked into 'woocommerce_registration_auth_new_customer' filter.
	 *
	 * @param $customer
	 * @param $user_id
	 *
	 * @return mixed
	 */
	public function new_user_registration_from_registration_form( $user_id, $new_customer_data = array(), $password_generated = false ) {
		$this->new_user_registration( $user_id );
	}
	
	function cev_verify_user_email_on_registration_checkout( $user_id ) {
		if ( isset($_POST['createaccount']) && $_POST['createaccount'] == '1'  ){
			update_user_meta( $user_id, 'customer_email_verified', 'true' );
		}

	}

	/*
	 * This function gets executed from different places when ever a new user is registered or resend verifcation email is sent.
	 */
	public function new_user_registration( $user_id ) {
		$cev_skip_verification_for_selected_roles = get_option('cev_skip_verification_for_selected_roles');
		
		$user_role = get_userdata( $user_id );
		
		$verified = get_user_meta( $user_id, 'customer_email_verified', true );
		
		$cev_enable_email_verification = get_option('cev_enable_email_verification',0);		
		if ( 'administrator' !== $user_role->roles[0] && $cev_skip_verification_for_selected_roles[$user_role->roles[0]] == 0 && $cev_enable_email_verification == 1 && $verified != 'true') {
			$current_user = get_user_by( 'id', $user_id );
			$this->user_id                         = $current_user->ID;
			$this->email_id                        = $current_user->user_email;
			$this->user_login                      = $current_user->user_login;
			$this->user_email                      = $current_user->user_email;
			WC_customer_email_verification_email_Common::$wuev_user_id  = $current_user->ID;
			WC_customer_email_verification_email_Common::$wuev_myaccount_page_id = $this->my_account;
			$this->is_user_created                 = true;		
			$is_secret_code_present                = get_user_meta( $this->user_id, 'customer_email_verification_code', true );
	
			if ( '' === $is_secret_code_present ) {
				$secret_code = md5( $this->user_id . time() );
				update_user_meta( $user_id, 'customer_email_verification_code', $secret_code );
			}
			$cev_email_for_verification = get_option('cev_email_for_verification',1);
			//echo $secret_code;exit;
			if($cev_email_for_verification == 1){
				WC_customer_email_verification_email_Common::code_mail_sender( $current_user->user_email );
			}
			$this->is_new_user_email_sent = true;
		}
	}

	/**
	 * This function appends the verification link to the bottom of the welcome email of woocommerce.
	 *
	 * @param $emailclass_object
	 */
	public function append_content_before_woocommerce_footer( $emailclass_object ) {
		if ( isset( $emailclass_object->id ) && ( 'customer_new_account' === $emailclass_object->id ) ) {
			$cev_skip_verification_for_selected_roles = get_option('cev_skip_verification_for_selected_roles');	
			$cev_initialise_customizer_settings = new cev_initialise_customizer_settings();			
			$user_id = $emailclass_object->object->data->ID;
			
			$verification_pin = WC_customer_email_verification_email_Common::generate_verification_pin();			
						
			$user_role = get_userdata( $user_id );
			
			$cev_enable_email_verification = get_option('cev_enable_email_verification',0);		
			if ( 'administrator' !== $user_role->roles[0] && $cev_skip_verification_for_selected_roles[$user_role->roles[0]] == 0 && $cev_enable_email_verification == 1) {
				$cev_email_for_verification = get_option('cev_email_for_verification',1);
				if($cev_email_for_verification != 2)return;
					
					update_user_meta( $user_id, 'cev_email_verification_pin', $verification_pin );
					$cev_email_verification_pin = get_user_meta( $user_id,  'cev_email_verification_pin', true );							
					
					$this->user_id = $user_id;					
					$is_secret_code_present = get_user_meta( $user_id, 'customer_email_verification_code', true );
		
					if ( '' === $is_secret_code_present ) {
						$secret_code = md5( $user_id . time() );
						update_user_meta( $user_id, 'customer_email_verification_code', $secret_code );
					}
										
					$email_body = get_option('cev_verification_email_body',$cev_initialise_customizer_settings->defaults['cev_verification_email_body']);;
					$email_body = WC_customer_email_verification_email_Common::maybe_parse_merge_tags( $email_body );
					$email_body = apply_filters( 'the_content', $email_body );	
					echo $email_body;				
			}
		}
	}	
	
	/**
	 * This function generates the verification link from the shortocde [customer_email_verification_code] and returns the link.
	 * @return string
	 */
	public function customer_email_verification_code() {
		$secret      = get_user_meta( $this->user_id, 'customer_email_verification_code', true );
		$create_link = $secret . '@' . $this->user_id;
		$hyperlink   = add_query_arg( array(
			'cusomer_email_verify' => base64_encode( $create_link ),
		), get_the_permalink( $this->my_account ) );		
		$link        = '<a href="' . $hyperlink . '">"Email Verification Link"</a>';

		return $link;
	}
	
	/*
	 * This function verifies the user when the user clicks on the verification link in its email.
	 * If automatic login setting is enabled in plugin setting screen, then the user is forced loggedin.
	 */
	public function authenticate_user_by_email() {
		
		if ( isset( $_GET['cusomer_email_verify'] ) && '' !== $_GET['cusomer_email_verify'] ) { // WPCS: input var ok, CSRF ok.
			$user_meta = explode( '@', base64_decode( $_GET['cusomer_email_verify'] ) ); // WPCS: input var ok, CSRF ok.
			if ( 'true' === get_user_meta( (int) $user_meta[1], 'customer_email_verified', true ) ) {
				$this->is_user_already_verified = true;
			}

			$verified_code = get_user_meta( (int) $user_meta[1], 'customer_email_verification_code', true );
			
			if ( ! empty( $verified_code ) && $verified_code === $user_meta[0] ) {
				WC_customer_email_verification_email_Common::$wuev_user_id = (int) $user_meta[1];
				$allow_automatic_login       = 1;

				update_user_meta( (int) $user_meta[1], 'customer_email_verified', 'true' );				
				$verification_success_message = get_option('cev_verification_success_message','Your Email is verified!');
				wc_add_notice( $verification_success_message, 'notice' );		
			}
		}
	}
	
	/*
	 * This function executes just after the user logged in. If restrict user setting is enabled in the plugin settings screen, the the user is force
	 * logged out.
	 */
	public function custom_form_login_check( $user_login ) {
		$user = get_user_by( 'login', $user_login );
		$user_role = $user->roles[0];
		$cev_redirect_after_successfull_verification = get_option('cev_redirect_after_successfull_verification',$this->my_account);
		$cev_skip_verification_for_selected_roles = get_option('cev_skip_verification_for_selected_roles');
		
		$cev_enable_email_verification = get_option('cev_enable_email_verification',0);		
		if ( 'administrator' !== $user_role && $cev_skip_verification_for_selected_roles[$user_role] == 0 && $cev_enable_email_verification == 1) {
			if (!class_exists('Zorem_AJAX_Login_Register')) {
				if ( 'true' !== get_user_meta( $user->ID, 'customer_email_verified', true ) ) {												
					wp_logout();									
					$redirect_url = add_query_arg( array(
						'cevsm' => base64_encode( $user->ID ),
					), apply_filters( 'cev_custom_form_login_check_redirect_url', get_the_permalink( $this->my_account ) ) );
					wp_safe_redirect( $redirect_url );
					exit;				
				} else{
					$redirect_url = add_query_arg( array(
						'cevsm' => base64_encode( $user->ID ),
					), apply_filters( 'cev_custom_form_login_check_redirect_url', get_the_permalink( $cev_redirect_after_successfull_verification ) ) );
					wp_safe_redirect( $redirect_url );
					exit;
				}
			}
		}
	}
	
	/*
	 * This function is executed just after a new user is made from woocommerce registration form in myaccount page.
	 * Its hooked into 'woocommerce_registration_redirect' filter.
	 * If restrict user setting is enabled from the plugin settings screen, then this function will logs out the user.
	 */
	public function redirect_user_after_registration( $redirect ){
		if ( true === $this->is_new_user_email_sent  ) {
			$cev_enter_account_after_registration = get_option('cev_enter_account_after_registration',0);
			if($cev_enter_account_after_registration == 1){								
				WC()->session->set( 'first_login', 1 );
			}
		}
		return $redirect;
	}
	
	public function wcalr_register_user_successful_fun(){
		if ( true === $this->is_new_user_email_sent  ) {
			$cev_enter_account_after_registration = get_option('cev_enter_account_after_registration',0);
			if($cev_enter_account_after_registration == 1){								
				WC()->session->set( 'first_login', 1 );
			}
		}		
	}
	
	public function show_cev_notification_message_after_register(){
		if ( isset( $_GET['cev'] ) && '' !== $_GET['cev'] ) { // WPCS: input var ok, CSRF ok.
			$registration_message = get_option('cev_verification_message','We sent you a verification email. Check and verify your account.');
			wc_add_notice( $registration_message, 'notice' );
		}
		if ( isset( $_GET['cevsm'] ) && '' !== $_GET['cevsm'] ) { // WPCS: input var ok, CSRF ok.
			WC_customer_email_verification_email_Common::$wuev_user_id = base64_decode( $_GET['cevsm'] ); // WPCS: input var ok, CSRF ok.
			if ( false === WC()->session->has_session() ) {
				WC()->session->set_customer_session_cookie( true );
			}
			$message = get_option('cev_resend_verification_email_message', 'You need to verify your account before login. {{cev_resend_email_link}}');
			$message = WC_customer_email_verification_email_Common::maybe_parse_merge_tags( $message );
			if ( false === wc_has_notice( $message, 'notice' ) ) {
				wc_add_notice( $message, 'notice' );
			}
		}
	}
	
	/**
	 * This function sends a new verification email to user if the user clicks on 'resend verification email' link.
	 * If the email is already verified then it redirects to my-account page
	 */
	public function cev_resend_verification_email() {
		if ( isset( $_GET['cev_confirmation_resend'] ) && '' !== $_GET['cev_confirmation_resend'] ) { // WPCS: input var ok, CSRF ok.
			$user_id = base64_decode( $_GET['cev_confirmation_resend'] ); // WPCS: input var ok, CSRF ok.

			if ( false === WC()->session->has_session() ) {
				WC()->session->set_customer_session_cookie( true );
			}

			$verified = get_user_meta( $user_id, 'customer_email_verified', true );

			if ( 'true' === $verified ) {
				$already_verified_message = XlWUEV_Common::maybe_parse_merge_tags( XlWUEV_Common::get_setting_value( 'wuev-messages', 'xlwuev_email_verification_already_done' ) );
				$verified_message = get_option('cev_verified_user_message', 'Your Email is already verified');
				wc_add_notice( $verified_message, 'notice' );
			} else {
				WC_customer_email_verification_email_Common::$wuev_user_id                  = $user_id;
				WC_customer_email_verification_email_Common::$wuev_myaccount_page_id        = $this->my_account;
				//WC_customer_email_verification_email_Common::$is_xlwuev_resend_link_clicked = true;
				$current_user = get_user_by( 'id', $user_id );
				WC_customer_email_verification_email_Common::code_mail_sender( $current_user->user_email );
				//$this->new_user_registration( $user_id );
				$message = get_option('cev_resend_verification_email_message', 'A new verification link is sent. Check email. {{cev_resend_email_link}}');
				$message = WC_customer_email_verification_email_Common::maybe_parse_merge_tags( $message );
				wc_add_notice( $message, 'notice' );
			}
		}
	}

	public function check_user_and_redirect_to_endpoint(){
				
		if(!is_account_page() ){
			return;	
		}	
		if ( is_user_logged_in() ) {
			$user = get_user_by( 'id', get_current_user_id() );
			
			$first_login = WC()->session->get( 'first_login', 0 );
			
			if($first_login == 1){
				return;	
			}
			
			if(!$user){
				return;
			}
			
			$user_role = $user->roles[0];
			
			$cev_redirect_after_successfull_verification = get_option('cev_redirect_after_successfull_verification',$this->my_account);
			$cev_skip_verification_for_selected_roles = get_option('cev_skip_verification_for_selected_roles');
			$redirect_url = wc_get_account_endpoint_url( 'email-verification' );						
			
			global $wp;			 
			$current_slug = add_query_arg( array(), $wp->request );	
			
			if($current_slug == 'my-account/customer-logout'){
				return;
			}	
											
			$cev_enable_email_verification = get_option('cev_enable_email_verification',0);		
			if ( 'administrator' !== $user_role && $cev_skip_verification_for_selected_roles[$user_role] == 0 && $cev_enable_email_verification == 1) {
				$verified = get_user_meta( get_current_user_id(), 'customer_email_verified', true );					
				$cev_email_verification_pin = get_user_meta( get_current_user_id(), 'cev_email_verification_pin',true );
				if(!empty($cev_email_verification_pin)){
					if ( 'true' !== $verified ) {					
						if($current_slug != 'my-account/email-verification'){
							wp_safe_redirect( $redirect_url );exit;	
						}
					}
				}
			}
		} 
	}
		
	public function cev_verify_user_email_with_pin_fun(){				
		$cev_email_verification_pin = get_user_meta( get_current_user_id(),  'cev_email_verification_pin', true );	
		$cev_pin1 = $_POST['cev_pin1'];
		$cev_pin2 = $_POST['cev_pin2'];
		$cev_pin3 = $_POST['cev_pin3'];
		$cev_pin4 = $_POST['cev_pin4'];
		$cev_pin = $cev_pin1.$cev_pin2.$cev_pin3.$cev_pin4;
		
		if($cev_email_verification_pin == $cev_pin){
			$my_account = woo_customer_email_verification()->my_account;	
			update_user_meta( get_current_user_id(), 'customer_email_verified', 'true' );				
			$verification_success_message = get_option('cev_verification_success_message','Your Email is verified!');
			wc_add_notice( $verification_success_message, 'notice' );
			echo json_encode( array('success' => 'true','url' => get_permalink($my_account)) );die();			
		} else{
			echo json_encode( array('success' => 'false') );die();			
		}
		exit;
	}			
}	