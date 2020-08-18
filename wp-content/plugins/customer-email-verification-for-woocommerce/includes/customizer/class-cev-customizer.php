<?php
/**
 * Customizer Setup and Custom Controls
 *
 */

/**
 * Adds the individual sections, settings, and controls to the theme customizer
 */
class cev_initialise_customizer_settings {
	// Get our default values	
	private static $order_ids  = null;
	
	public function __construct() {
		// Get our Customizer defaults
		$this->defaults = $this->cev_generate_defaults();		
		
		// Register our sample default controls
		add_action( 'customize_register', array( $this, 'wcast_register_sample_default_controls' ) );
		
		// Only proceed if this is own request.				
		if ( ! cev_initialise_customizer_settings::is_own_customizer_request() && ! cev_initialise_customizer_settings::is_own_preview_request()) {
			return;
		}				
		// Register our Panels
		add_action( 'customize_register', array( $this, 'cev_add_customizer_panels' ) );

		// Register our sections
		add_action( 'customize_register', array( $this, 'cev_add_customizer_sections' ) );	
		
		// Remove unrelated components.
		add_filter( 'customize_loaded_components', array( $this, 'remove_unrelated_components' ), 99, 2 );

		// Remove unrelated sections.
		add_filter( 'customize_section_active', array( $this, 'remove_unrelated_sections' ), 10, 2 );	
		
		// Unhook divi front end.
		add_action( 'woomail_footer', array( $this, 'unhook_divi' ), 10 );

		// Unhook Flatsome js
		add_action( 'customize_preview_init', array( $this, 'unhook_flatsome' ), 50  );	

		add_filter( 'customize_controls_enqueue_scripts', array( $this, 'enqueue_customizer_scripts' ) );		
		
		add_action( 'parse_request', array( $this, 'set_up_preview' ) );				
	}		
	
	/**
	 * add css and js for customizer
	*/
	public function enqueue_customizer_scripts(){	
		if(isset( $_REQUEST['cev-customizer'] ) && '1' === $_REQUEST['cev-customizer']){
			wp_enqueue_style('cev-customizer-styles', woo_customer_email_verification()->plugin_dir_url() . 'assets/css/customizer-styles.css', array(), woo_customer_email_verification()->version  );
			wp_enqueue_script('cev-customizer-scripts', woo_customer_email_verification()->plugin_dir_url() . 'assets/js/customizer-scripts.js', array('jquery', 'customize-controls'), woo_customer_email_verification()->version, true);
	
			// Send variables to Javascript
			wp_localize_script('cev-customizer-scripts', 'cev_customizer', array(
				'ajax_url'              => admin_url('admin-ajax.php'),				
				'trigger_click'        => '#accordion-section-'.$_REQUEST['section'].' h3',
			));		
		}
	}
	
	/**
	 * Checks to see if we are opening our custom customizer preview
	 *
	 * @access public
	 * @return bool
	 */
	public static function is_own_preview_request() {
		return isset( $_REQUEST['cev-email-preview'] ) && '1' === $_REQUEST['cev-email-preview'];
	}
	
	/**
	 * Checks to see if we are opening our custom customizer controls
	 *
	 * @access public
	 * @return bool
	 */
	public static function is_own_customizer_request() {
		return isset( $_REQUEST['section'] ) && $_REQUEST['section'] === 'cev_controls_section';
	}
	
	/**
	 * Get Customizer URL
	 *
	 */
	public static function get_customizer_url($section) {	
			//echo $return_tab;exit;
			$customizer_url = add_query_arg( array(
				'cev-customizer' => '1',
				'section' => $section,
				'url'     => urlencode( add_query_arg( array( 'cev-email-preview' => '1' ), home_url( '/' ) ) ),
			), admin_url( 'customize.php' ) );		

		return $customizer_url;
	}
	
	/**
	 * code for initialize default value for customizer
	*/	
	public function cev_generate_defaults() {
		$customizer_defaults = array(
			'cev_verification_email_heading' => 'Please verify your Email Address',			
			'cev_verification_email_subject' => 'Email Verification for user - {{cev_display_name}}',			
			'cev_verification_email_body' => 'We have recevied your Email Verification request.Your PIN number is shown below:{{cev_user_verification_pin}}<p>You can also verify your Email Account by clicking on the following {{cev_user_verification_link}}</p>',			
		);

		return $customizer_defaults;
	}	
	
	/**
     * Remove unrelated components
     *
     * @access public
     * @param array $components
     * @param object $wp_customize
     * @return array
     */
    public function remove_unrelated_components($components, $wp_customize)	{
        // Iterate over components
        foreach ($components as $component_key => $component) {

            // Check if current component is own component
            if ( ! $this->is_own_component( $component ) ) {
                unset($components[$component_key]);
            }
        }

        // Return remaining components
        return $components;
    }

    /**
     * Remove unrelated sections
     *
     * @access public
     * @param bool $active
     * @param object $section
     * @return bool
     */
    public function remove_unrelated_sections( $active, $section ) {
        // Check if current section is own section
        if ( ! $this->is_own_section( $section->id ) ) {
            return false;
        }

        // We can override $active completely since this runs only on own Customizer requests
        return true;
    }
	
	/**
	* Check if current component is own component
	*
	* @access public
	* @param string $component
	* @return bool
	*/
	public static function is_own_component( $component ) {
		return false;
	}

	/**
	* Check if current section is own section
	*
	* @access public
	* @param string $key
	* @return bool
	*/
	public static function is_own_section( $key ) {		
		if ($key === 'cev_controls_section') {
			return true;
		}

		// Section not found
		return false;
	}

	/*
	 * Unhook flatsome front end.
	 */
	public function unhook_flatsome() {
		// Unhook flatsome issue.
		wp_dequeue_style( 'flatsome-customizer-preview' );
		wp_dequeue_script( 'flatsome-customizer-frontend-js' );
	}
	
	/*
	 * Unhook Divi front end.
	 */
	public function unhook_divi() {
		// Divi Theme issue.
		remove_action( 'wp_footer', 'et_builder_get_modules_js_data' );
		remove_action( 'et_customizer_footer_preview', 'et_load_social_icons' );
	}
	
	/**
	 * Register the Customizer panels
	 */
	public function cev_add_customizer_panels( $wp_customize ) {
		/**
		* Add our Header & Navigation Panel
		*/
		$wp_customize->add_panel( 'cev_naviation_panel',
			array(
				'title' => __( 'Customer Email Verification for WooCommerce', 'customer-email-verification-for-woocommerce' ),
				'description' => esc_html__( '', 'customer-email-verification-for-woocommerce' )
			)
		);		
	}	
	
	/**
	 * Register the Customizer sections
	 */
	public function cev_add_customizer_sections( $wp_customize ) {	
		$wp_customize->add_section( 'cev_controls_section',
			array(
				'title' => __( 'Email for Verification', 'customer-email-verification-for-woocommerce' ),
				'description' => '',
				'panel' => 'cev_naviation_panel'
			)
		);			
	}
	
	/**
	 * Register our sample default controls
	 */
	public function wcast_register_sample_default_controls( $wp_customize ) {		
		/**
		* Load all our Customizer Custom Controls
		*/
		require_once trailingslashit( dirname(__FILE__) ) . 'custom-controls.php';		
				

		// Email Subject	
		$wp_customize->add_setting( 'cev_verification_email_subject',
			array(
				'default' => $this->defaults['cev_verification_email_subject'],
				'transport' => 'refresh',
				'type' => 'option',
				'sanitize_callback' => ''
			)
		);
		$wp_customize->add_control( 'cev_verification_email_subject',
			array(
				'label' => __( 'Subject', 'woocommerce' ),
				'description' => esc_html__( 'Only for a separate verification email', 'customer-email-verification-for-woocommerce' ),
				'section' => 'cev_controls_section',
				'type' => 'text',
				'input_attrs' => array(
					'class' => '',
					'style' => '',
					'placeholder' => __( $this->defaults['cev_verification_email_subject'], 'customer-email-verification-for-woocommerce' ),
				),
			)
		);

		// Email Heading	
		$wp_customize->add_setting( 'cev_verification_email_heading',
			array(
				'default' => $this->defaults['cev_verification_email_heading'],
				'transport' => 'refresh',
				'type' => 'option',
				'sanitize_callback' => ''
			)
		);
		$wp_customize->add_control( 'cev_verification_email_heading',
			array(
				'label' => __( 'Email Heading', 'customer-email-verification-for-woocommerce' ),
				'description' => esc_html__( 'Only for a separate verification email', 'customer-email-verification-for-woocommerce' ),
				'section' => 'cev_controls_section',
				'type' => 'text',
				'input_attrs' => array(
					'class' => '',
					'style' => '',
					'placeholder' => __( $this->defaults['cev_verification_email_heading'], 'customer-email-verification-for-woocommerce' ),
				),
			)
		);

		// Email Body	
		$wp_customize->add_setting( 'cev_verification_email_body',
			array(
				'default' => $this->defaults['cev_verification_email_body'],
				'transport' => 'refresh',
				'type' => 'option',
				'sanitize_callback' => ''
			)
		);
		$wp_customize->add_control( 'cev_verification_email_body',
			array(
				'label' => __( 'Email Body', 'customer-email-verification-for-woocommerce' ),
				'description' => esc_html__( '', 'customer-email-verification-for-woocommerce' ),
				'section' => 'cev_controls_section',
				'type' => 'textarea',
				'input_attrs' => array(
					'class' => '',
					'style' => '',
					'placeholder' => __( $this->defaults['cev_verification_email_body'], 'customer-email-verification-for-woocommerce' ),
				),
			)
		);

		$wp_customize->add_setting( 'cev_email_code_block',
			array(
				'default' => '',
				'transport' => 'postMessage',
				'sanitize_callback' => ''
			)
		);
		$wp_customize->add_control( new WP_Customize_cev_codeinfoblock_Control( $wp_customize, 'cev_email_code_block',
			array(
				'label' => __( 'Available variables', 'customer-email-verification-for-woocommerce' ),
				'description' => '<code>{{customer_email_verification_code}}<br>{{cev_user_verification_pin}}<br>{{cev_user_verification_link}}<br>{{cev_resend_email_link}}<br>{{cev_display_name}}<br>{{cev_user_login}}<br>{{cev_user_email}}</code>',
				'section' => 'cev_controls_section',				
			)
		) );			
	}	
	
	/**
	 * Set up preview
	 *
	 * @access public
	 * @return void
	 */
	public function set_up_preview() {
		
		// Make sure this is own preview request.
		if ( ! cev_initialise_customizer_settings::is_own_preview_request() ) {
			return;
		}
		include woo_customer_email_verification()->get_plugin_path() . '/includes/customizer/preview/preview.php';		
		exit;			
	}	

	/**
	 * code for preview of tracking info in email
	*/	
	public function preview_account_email(){				
		
		// Load WooCommerce emails.
		$wc_emails      = WC_Emails::instance();
		$emails         = $wc_emails->get_emails();				
		WC_customer_email_verification_email_Common::$wuev_user_id  = 1;				
		$email_heading     = get_option('cev_verification_email_heading',$this->defaults['cev_verification_email_heading']);
		$email_heading 	   = WC_customer_email_verification_email_Common::maybe_parse_merge_tags( $email_heading );		
		$email_content = get_option('cev_verification_email_body',$this->defaults['cev_verification_email_body']);
					
		$sent_to_admin = false;
		$plain_text = false;
		$email = '';
				
		$mailer = WC()->mailer();								

		// create a new email
		$email = new WC_Email();
		$email->id = 'Customer_New_Account';
		$email_content = WC_customer_email_verification_email_Common::maybe_parse_merge_tags( $email_content );
		// wrap the content with the email template and then add styles
		$message = apply_filters( 'woocommerce_mail_content', $email->style_inline( $mailer->wrap_message( $email_heading, $email_content ) ) );
		echo $message;
	}	
}
/**
 * Initialise our Customizer settings
 */

$cev_customizer_settings = new cev_initialise_customizer_settings();