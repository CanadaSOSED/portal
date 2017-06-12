<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Email_Admin_Widthdrawal_Request' ) ) :

/**
 * New Commission Email
 *
 * An email sent to the admin when a new order is received/paid for.
 *
 * @class 		WC_Email_Vendor_Direct_Bank
 * @version		2.0.0
 * @package		WooCommerce/Classes/Emails
 * @extends 	WC_Email
 *
 * @property DC_Commission $object
 */
class WC_Email_Admin_Widthdrawal_Request extends WC_Email {

	/**
	 * Constructor
	 */
	function __construct() {
		global $WCMp;
		$this->id 				= 'admin_widthdrawal_request';
		$this->title 			= __( 'Withdrawal request to Admin from Vendor by BAC', $WCMp->text_domain );
		$this->description		= __( 'New commissions withdrawal request have been submitted.', $WCMp->text_domain);

		$this->heading 			= __( 'New Commission Withdrawal Request', $WCMp->text_domain);
		$this->subject      	= __( '[{site_title}] Commission Widthdrawal Request', $WCMp->text_domain);

		$this->template_base = $WCMp->plugin_path . 'templates/';
		$this->template_html 	= 'emails/admin-widthdrawal-request.php';
		$this->template_plain 	= 'emails/plain/admin-widthdrawal-request.php';


		// Call parent constructor
		parent::__construct();
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 *
	 * @param Commission $commission Commission paid
	 */
	function trigger( $trans_id, $vendor_id ) {
		
		if(!isset($trans_id) && !isset($vendor_id)) return;
		
		$this->vendor = get_wcmp_vendor_by_term($vendor_id);
		
		$commissions = get_post_meta($trans_id, 'commission_detail', true);
		
		$this->commissions = $commissions;	
		
		$this->transaction_id = $trans_id;
		
		$this->transaction_mode = get_post_meta($trans_id, 'transaction_mode', true);
		
    $this->recipient = get_option( 'admin_email' );
    
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {
		global $WCMp;
		ob_start();
		wc_get_template( $this->template_html, array(
			'commissions'    => $this->commissions,
			'email_heading' => $this->get_heading(),
			'transaction_mode' => $this->transaction_mode,
			'vendor' =>  $this->vendor,
			'transaction_id' => $this->transaction_id,
			'sent_to_admin' => false,
			'plain_text'    => false
			), 'dc-product-vendor/', $this->template_base);
		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain() {
		ob_start();
		wc_get_template( $this->template_plain, array(
			'commissions'    => $this->commissions,
			'email_heading' => $this->get_heading(),
			'transaction_mode' => $this->transaction_mode,
			'vendor' =>  $this->vendor,
			'transaction_id' => $this->transaction_id,
			'sent_to_admin' => false,
			'plain_text'    => false
			), 'dc-product-vendor/', $this->template_base);
		return ob_get_clean();
	}

	/**
	 * Initialise Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	function init_form_fields() {
		global $WCMp;
		$this->form_fields = array(
			'enabled' => array(
				'title' 		=> __( 'Enable/Disable', $WCMp->text_domain ),
				'type' 			=> 'checkbox',
				'label' 		=> __( 'Enable notification for this email', $WCMp->text_domain ),
				'default' 		=> 'yes'
			),
			'subject' => array(
				'title' 		=> __( 'Subject', $WCMp->text_domain ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'This controls the email subject line. Leave it blank to use the default subject: <code>%s</code>.', $WCMp->text_domain ), $this->subject ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'heading' => array(
				'title' 		=> __( 'Email Heading', $WCMp->text_domain ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'This controls the main heading contained in the email notification. Leave it blank to use the default heading: <code>%s</code>.', $WCMp->text_domain ), $this->heading ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'email_type' => array(
				'title' 		=> __( 'Email Type', $WCMp->text_domain ),
				'type' 			=> 'select',
				'description' 	=> __( 'Choose format for the email that will be sent.', $WCMp->text_domain ),
				'default' 		=> 'html',
				'class'			=> 'email_type wc-enhanced-select',
				'options'		=> $this->get_email_type_options()
			)
		);
	}
}

endif;

return new WC_Email_Admin_Widthdrawal_Request();