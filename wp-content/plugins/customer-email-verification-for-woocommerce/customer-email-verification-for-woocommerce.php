<?php
/**
 * @wordpress-plugin
 * Plugin Name: Customer Verification for WooCommerce 
 * Plugin URI: https://www.zorem.com/products/customer-email-verification-for-woocommerce/ 
 * Description: This plugin Customer verification for WooCommerce will reduce registration spam in your store and verifies the email address of the customers by sending a verification link to the email address that they registered to their accounts. You can allow the customer to be able to log in to their account when they registered and to require them to verify the email for next logins or restrict access the my-account area to users that verified their accounts.
 * Version: 1.1
 * Author: zorem
 * Author URI:  
 * License: GPL-2.0+
 * License URI: 
 * Text Domain: customer-email-verification-for-woocommerce
 * Domain Path: /lang/
 * WC tested up to: 4.2
*/


class zorem_woo_customer_email_verification {
	/**
	 * Customer verification for WooCommerce version.
	 *
	 * @var string
	 */
	public $version = '1.1';
	
	/**
	 * Initialize the main plugin function
	*/
    public function __construct() {
		
		$this->plugin_file = __FILE__;
		// Add your templates to this array.
		
		if(!defined('CUSTOMER_EMAIL_VERIFICATION_PATH')) define( 'CUSTOMER_EMAIL_VERIFICATION_PATH', $this->get_plugin_path());
			
		$this->my_account = get_option( 'woocommerce_myaccount_page_id' );

		if ( '' === $this->my_account ) {
			$this->my_account = get_option( 'page_on_front' );
		}
		
		if ( $this->is_wc_active() ) {
			
			// Include required files.
			$this->includes();			
			
			//start adding hooks
			$this->init();

			$this->admin->init();	
			
			$this->email->init();
			
			//$this->csv_permalink->init();
			
			add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
		}	
	}
	
	/**
	 * Check if WooCommerce is active
	 *
	 * @access private
	 * @since  1.0.0
	 * @return bool
	*/
	private function is_wc_active() {
		
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
		}
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$is_active = true;
		} else {
			$is_active = false;
		}
		

		// Do the WC active check
		if ( false === $is_active ) {
			add_action( 'admin_notices', array( $this, 'notice_activate_wc' ) );
		}		
		return $is_active;
	}
	
	/**
	 * Display WC active notice
	 *
	 * @access public
	 * @since  1.0.0
	*/
	public function notice_activate_wc() {
		?>
		<div class="error">
			<p><?php printf( __( 'Please install and activate WooCommerce!', '' ), '<a href="' . admin_url( 'plugin-install.php?tab=search&s=WooCommerce&plugin-search-input=Search+Plugins' ) . '">', '</a>' ); ?></p>
		</div>
		<?php
	}
	
	/**
	 * Gets the absolute plugin path without a trailing slash, e.g.
	 * /path/to/wp-content/plugins/plugin-directory.
	 *
	 * @return string plugin path
	 */
	public function get_plugin_path() {
		if ( isset( $this->plugin_path ) ) {
			return $this->plugin_path;
		}

		$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );

		return $this->plugin_path;
	}
	
	/**
	 * Gets the absolute plugin url.
	 */	
	public function plugin_dir_url(){
		return plugin_dir_url( __FILE__ );
	}
	
	/*
	* init when class loaded
	*/
	public function init(){
		add_action( 'plugins_loaded', array( $this, 'customer_email_verification_load_textdomain'));

		//Custom Woocomerce menu
		add_action('admin_menu', array( $this->admin, 'register_woocommerce_menu' ), 99 );
		
		//load css js 
		add_action( 'admin_enqueue_scripts', array( $this->admin, 'admin_styles' ), 4);	
		add_filter( 'woocommerce_account_menu_items', array( $this, 'cev_account_menu_items' ), 10, 1 );	
		add_filter( 'woocommerce_account_menu_items', array( $this, 'hide_cev_menu_my_account' ), 999 );
		add_action( 'init', array( $this, 'cev_add_my_account_endpoint' ) );
		add_action( 'woocommerce_account_email-verification_endpoint', array( $this, 'cev_email_verification_endpoint_content' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'front_styles' ));

	}
	
	
	
	/*** Method load Language file ***/
	function customer_email_verification_load_textdomain() {
		load_plugin_textdomain( 'customer-email-verification-for-woocommerce', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
	}
	
	/*
	* include files
	*/
	private function includes(){
		require_once $this->get_plugin_path() . '/includes/class-wc-customer-email-verification-admin.php';
		$this->admin = WC_customer_email_verification_admin::get_instance();

		require_once $this->get_plugin_path() . '/includes/class-wc-customer-email-verification-email.php';
		$this->email = WC_customer_email_verification_email::get_instance();		
		
		//require_once $this->get_plugin_path() . '/includes/class-cev-permalink-manager.php';
		//$this->csv_permalink = CEV_Permalink_Manager::get_instance();	
		
		require_once $this->get_plugin_path() . '/includes/class-wc-customer-email-verification-email-common.php';
	}

	/*
	* include file on plugin load
	*/
	public function on_plugins_loaded() {		
		require_once $this->get_plugin_path() . '/includes/customizer/class-cev-customizer.php';								
	}
	
	/**
	 * Include front js and css
	*/
	public function front_styles(){				
		wp_register_script( 'cev-front-js', woo_customer_email_verification()->plugin_dir_url().'assets/js/front.js', array( 'jquery' ), woo_customer_email_verification()->version );
		wp_localize_script( 'cev-front-js', 'cev_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		
		wp_register_style( 'cev_front_style',  woo_customer_email_verification()->plugin_dir_url() . 'assets/css/front.css', array(), woo_customer_email_verification()->version );		
		
		global $wp;	
		$current_slug = add_query_arg( array(), $wp->request );
		
		if($current_slug == 'my-account/email-verification'){	
			wp_enqueue_style( 'cev_front_style' );			
			wp_enqueue_script( 'cev-front-js' );			
		}		
	}

	/**
	* Account menu items
	*
	* @param arr $items
	* @return arr
	*/
	public function cev_account_menu_items( $items ) {
		$items['email-verification'] = __( 'Sign Up Email Verification', 'customer-email-verification-for-woocommerce' );
		return $items;
	}
	
	/**
	* @snippet       Hide Edit Address Tab @ My Account
	* @how-to        Get CustomizeWoo.com FREE
	* @sourcecode    https://businessbloomer.com/?p=21253
	* @author        Rodolfo Melogli
	* @testedwith    WooCommerce 3.5.1
	* @donate $9     https://businessbloomer.com/bloomer-armada/
	*/			
	public function hide_cev_menu_my_account( $items ) {
		unset($items['email-verification']);
		return $items;
	}
	
	/**
	* Add endpoint
	*/
	public function cev_add_my_account_endpoint() {
		add_rewrite_endpoint( 'email-verification', EP_PAGES );
		if(version_compare(get_option( 'cev_version' ),'1.5', '<') ){
			global $wp_rewrite;
			$wp_rewrite->set_permalink_structure('/%postname%/');
			$wp_rewrite->flush_rules();
			update_option( 'cev_version', '1.5');				
		}
	}
	
	/**
	* Information content
	*/
	public function cev_email_verification_endpoint_content() {
		$current_user = wp_get_current_user();
		$email = $current_user->user_email;	
		
		$resend_email_link = add_query_arg( array('cev_confirmation_resend' => base64_encode( get_current_user_id() ),), get_the_permalink( $this->my_account ) );
		
		$verified  = get_user_meta( get_current_user_id(), 'customer_email_verified', true );
		
		$cev_skip_verification_for_selected_roles = get_option('cev_skip_verification_for_selected_roles');
		
		$cev_verification_form_theme_color = get_option('cev_verification_form_theme_color','#74c2e1');
		
		if ( is_super_admin() || 'administrator' == $current_user->roles[0] || $cev_skip_verification_for_selected_roles[$current_user->roles[0]] != 0) {			
			return;
		}
		if ( 'true' === $verified ) {
			return;
		}	
		?>
		<style>
		.cev-authorization-grid__visual{
			background: <?php echo $this->hex2rgba($cev_verification_form_theme_color,'0.7'); ?>;	
		}		
		</style>
		<div class="cev-authorization-grid__visual">
			<div class="cev-authorization-grid__holder">
				<div class="cev-authorization-grid__inner">
					<div class="cev-authorization">				
						<form class="cev_pin_verification_form" method="post">                    					
							<section class="cev-authorization__holder">								
								<span class="cev-authorization__envelope dashicons"></span>
								<div class="cev-authorization__heading">
									<span class="cev-authorization__title"><?php _e( 'Sign Up Email Verification', 'customer-email-verification-for-woocommerce' ); ?></span>
									<span class="cev-authorization__description"><?php echo sprintf(__("To verify your email a PIN was sent to <strong>%s</strong>. Please check your inbox and enter the PIN below.", 'customer-email-verification-for-woocommerce'), $email) ?></span>
								</div>
					
								<div class="cev-pin-verification">								
									<div class="cev-pin-verification__row">
										<div class="cev-field cev-field_size_extra-large cev-field_icon_left cev-field_event_right cev-field_text_center">
											<!--input type="number" placeholder="Enter your PIN" class="js-pincode" name="cev_pin" id="cev_pin"-->
											<input class="cev_pin_box" id="cev_pin1" name="cev_pin1" type="text" maxlength="1" oninput="this.value=this.value.replace(/[^0-9]/g,'');"  onkeyup="onKeyUpEvent(1, event)" onfocus="onFocusEvent(1)">
											<input class="cev_pin_box" id="cev_pin2" name="cev_pin2" type="text" maxlength="1" oninput="this.value=this.value.replace(/[^0-9]/g,'');"  onkeyup="onKeyUpEvent(2, event)" onfocus="onFocusEvent(2)">
											<input class="cev_pin_box" id="cev_pin3" name="cev_pin3" type="text" maxlength="1" oninput="this.value=this.value.replace(/[^0-9]/g,'');"  onkeyup="onKeyUpEvent(3, event)" onfocus="onFocusEvent(3)">
											<input class="cev_pin_box" id="cev_pin4" name="cev_pin4" type="text" maxlength="1" oninput="this.value=this.value.replace(/[^0-9]/g,'');"  onkeyup="onKeyUpEvent(4, event)" onfocus="onFocusEvent(4)">	
										</div>
									</div>
									<div class="cev-pin-verification__failure js-pincode-invalid" style="display: none;">
										<div class="cev-alert cev-alert_theme_red">										
											<span class="js-pincode-error-message"><?php _e( 'Invalid PIN Code', 'customer-email-verification-for-woocommerce' ); ?></span>
										</div>
									</div>
									<div class="cev-pin-verification__events">
										<input type="hidden" name="cev_user_id" value="<?php echo get_current_user_id(); ?>">
										<input type="hidden" name="action" value="cev_verify_user_email_with_pin">
										<button class="cev-button cev-button_color_success cev-button_size_promo cev-button_type_block cev-pin-verification__button is-disabled" id="SubmitPinButton" type="submit">
											<?php _e( 'Verify', 'customer-email-verification-for-woocommerce' ); ?>
											<i class="cev-icon cev-icon_size_medium dmi-continue_arrow_24 cev-button__visual cev-button__visual_type_fixed"></i>
										</button>									
									</div>
								</div>
							</section>
							<footer class="cev-authorization__footer">
								<?php _e( 'Didn’t receive an email?', 'customer-email-verification-for-woocommerce' ); ?> <a href="<?php echo $resend_email_link; ?>"class="cev-link"><?php _e( 'Resend Email', 'customer-email-verification-for-woocommerce' ); ?></a>
							</footer>
						</form>            
					</div>
				</div>
			</div>
		</div>
	<?php }	
	
	/* Convert hexdec color string to rgb(a) string */
 
	public function hex2rgba($color, $opacity = false) {
	
		$default = 'rgba(116,194,225,0.7)';
	
		//Return default if no color provided
		if(empty($color))
			return $default; 
	
		//Sanitize $color if "#" is provided 
			if ($color[0] == '#' ) {
				$color = substr( $color, 1 );
			}
	
			//Check if color has 6 or 3 characters and get values
			if (strlen($color) == 6) {
					$hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
			} elseif ( strlen( $color ) == 3 ) {
					$hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
			} else {
					return $default;
			}
	
			//Convert hexadec to rgb
			$rgb =  array_map('hexdec', $hex);
	
			//Check if opacity is set(rgba or rgb)
			if($opacity){
				if(abs($opacity) > 1)
					$opacity = 1.0;
				$output = 'rgba('.implode(",",$rgb).','.$opacity.')';
			} else {
				$output = 'rgb('.implode(",",$rgb).')';
			}
	
			//Return rgb(a) color string
			return $output;
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
function woo_customer_email_verification() {
	static $instance;

	if ( ! isset( $instance ) ) {		
		$instance = new zorem_woo_customer_email_verification();
	}

	return $instance;
}

/**
 * Register this class globally.
 *
 * Backward compatibility.
*/
woo_customer_email_verification();