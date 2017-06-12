<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Email_Admin_New_Vendor_Account' ) ) :

/**
 * New Order Email
 *
 * An email sent to the admin when a new order is received/paid for.
 *
 * @class 		WC_Email_New_Order
 * @version		2.0.0
 * @package		WooCommerce/Classes/Emails
 * @author 		WooThemes
 * @extends 	WC_Email
 */
class WC_Email_Admin_New_Vendor_Account extends WC_Email {

	/**
	 * Constructor
	 */
	function __construct() {
		global $WCMp;
		$this->id 				= 'admin_new_vendor';
		$this->title 			= __( 'Admin New Vendor Account', $WCMp->text_domain );
		$this->description		= __( 'New emails are sent when a user applies to be a vendor.', $WCMp->text_domain );

		$this->heading 			= __( 'New Vendor Account', $WCMp->text_domain );
		$this->subject      	= __( '[{site_title}] New Vendor Account', $WCMp->text_domain );

		$this->template_html 	= 'emails/admin-new-vendor-account.php';
		$this->template_plain 	= 'emails/plain/admin-new-vendor-account.php';
		$this->template_base = $WCMp->plugin_path . 'templates/';
		
		// Call parent constructor
		parent::__construct();

		// Other settings
		$this->recipient = $this->get_option( 'recipient' );

		if ( ! $this->recipient )
			$this->recipient = get_option( 'admin_email' );
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 */
	function trigger( $user_id, $user_pass = '', $password_generated = false ) {

		if ( $user_id ) {
			$this->object 		= new WP_User( $user_id );
			$this->user_email         = stripslashes( $this->object->user_email );
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() )
			return;

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * get_subject function.
	 *
	 * @access public
	 * @return string
	 */
	function get_subject() {
			return apply_filters( 'woocommerce_email_subject_admin_new_vendor', $this->format_string( $this->subject ), $this->object );
	}

	/**
	 * get_heading function.
	 *
	 * @access public
	 * @return string
	 */
	function get_heading() {
			return apply_filters( 'woocommerce_email_heading_admin_new_vendor', $this->format_string( $this->heading ), $this->object );
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {
		ob_start();
		wc_get_template( $this->template_html, array(
			'email_heading'      => $this->get_heading(),
			'user_email'         => $this->user_email,
			'blogname'           => $this->get_blogname(),
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
			'email_heading'      => $this->get_heading(),
			'user_email'         => $this->user_email,
			'blogname'           => $this->get_blogname(),
			'sent_to_admin' => false,
			'plain_text'    => true
		) ,'dc-product-vendor/', $this->template_base );
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
				'label' 		=> __( 'Enable this email notification.', $WCMp->text_domain ),
				'default' 		=> 'yes'
			),
			'recipient' => array(
				'title' 		=> __( 'Recipient(s)', $WCMp->text_domain ),
				'type' 			=> 'text',
				'description' 	=> sprintf( __( 'Enter recipient(s) (comma separated) for this email. Defaults to <code>%s</code>.', $WCMp->text_domain ), esc_attr( get_option('admin_email') ) ),
				'placeholder' 	=> '',
				'default' 		=> ''
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
				'description' 	=> sprintf( __( 'This controls the main heading contained within the email notification. Leave it blank to use the default heading: <code>%s</code>.', $WCMp->text_domain ), $this->heading ),
				'placeholder' 	=> '',
				'default' 		=> ''
			),
			'email_type' => array(
				'title' 		=> __( 'Email Type', $WCMp->text_domain ),
				'type' 			=> 'select',
				'description' 	=> __( 'Choose which format of email to be sent.', $WCMp->text_domain ),
				'default' 		=> 'html',
				'class'			=> 'email_type',
				'options'		=> array(
					'plain'		 	=> __( 'Plain Text', $WCMp->text_domain ),
					'html' 			=> __( 'HTML', $WCMp->text_domain ),
					'multipart' 	=> __( 'Multipart', $WCMp->text_domain ),
				)
			)
		);
	}
}

endif;

return new WC_Email_Admin_New_Vendor_Account();