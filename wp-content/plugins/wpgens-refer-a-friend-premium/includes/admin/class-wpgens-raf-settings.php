<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Gens_RAF
 * @subpackage Gens_RAF/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Gens_RAF
 * @subpackage Gens_RAF/admin
 * @author     Your Name <email@example.com>
 */
class WPGens_Settings_RAF extends WC_Settings_Page {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $gens_raf    The ID of this plugin.
	 */
	private $gens_raf;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $gens_raf       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct() {

		$this->id    = 'gens_raf';
		$this->label = __( 'Refer A Friend', 'gens-raf');

		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
		add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );

	}

	/**
	 * Get sections
	 *
	 * @return array
	 */
	public function get_sections() {

		$sections = array(
			''         => __( 'General', 'gens-raf' ),
			'coupon_settings'  => __( 'Coupon Settings', 'gens-raf' ),
			'friend_coupon_settings'  => __( 'Referred Person Coupon Settings', 'gens-raf' ),
			'tabs'         => __( 'Woo Product Tab', 'gens-raf' ),
			'my_account'   => __( 'My Account Page', 'gens-raf' ),
			'share' => __( 'Share Options', 'gens-raf' ),
			'emails' => __( 'Email', 'gens-raf' ),
			'howto' => __( 'How to use plugin', 'gens-raf' ),
		);

		return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
	}

	/**
	 * Get settings array
	 *
	 * @since 1.0.0
	 * @param string $current_section Optional. Defaults to empty string.
	 * @return array Array of settings
	 */
	public function get_settings( $current_section = '' ) {
		$prefix = 'gens_raf_';
		switch ($current_section) {
			case 'coupon_settings':
				$settings = array(
					array(
						'name' => __( 'Referrer Coupon Settings', 'gens-raf' ),
						'type' => 'title',
						'desc' => __( 'Coupon for a user that has referred a person.' ),
						'id'   => 'coupon_settings',
					),
					array(
						'id'			=> $prefix.'coupon_type',
						'name' 			=> __( 'Coupon Type', 'gens-raf' ), // Type: fixed_cart, percent, fixed_product, percent_product
						'type' 			=> 'select',
						'class'    => 'wc-enhanced-select',
						'options'		=> array(
							'fixed_cart'	=> __( 'Cart Discount', 'gens-raf' ),
							'percent'	=> __( 'Cart % Discount', 'gens-raf' ),
							'fixed_product'	=> __( 'Product Discount', 'gens-raf' ),
							'order_percent'	=> __( 'Percent of Order', 'gens-raf' )
						)
					),
					array(
						'id'		=> $prefix.'coupon_amount',
						'name' 		=> __( 'Coupon Amount', 'gens-raf' ), 
						'type' 		=> 'number',
						'custom_attributes' => array('step'=>'0.01'),
						'desc_tip'	=> __( ' Entered without the currency unit or a percent sign as these will be added automatically, e.g., ’10’ for 10£ or 10%.', 'gens-raf'),
						'desc' 		=> __( 'Fixed value or percentage off depending on the discount type you choose.', 'gens-raf' )
					),
					array(
						'id'		=> $prefix.'product_ids',
						'name' 		=> __( 'Products', 'gens-raf' ), 
						'type' 		=> 'text',
						'desc_tip'	=> __( ' Products which need to be in the cart to use this coupon or, for "Product Discounts", which products are discounted. ', 'gens-raf'),
						'desc' 		=> __( 'Write product ID-s, separated by comma, e.g. 152,321,25', 'gens-raf' )
					),
					array(
						'id'		=> $prefix.'product_exclude_ids',
						'name' 		=> __( 'Exclude Products', 'gens-raf' ), 
						'type' 		=> 'text',
						'desc_tip'	=> __( ' Products that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'gens-raf'),
						'desc' 		=> __( 'Write product ID-s, separated by comma, e.g. 152,321,25', 'gens-raf' )
					),
					array(
						'id'		=> $prefix.'product_categories',
						'name' 		=> __( 'Product Categories', 'gens-raf' ),
						'type' 		=> 'text',
						'desc_tip'	=> __( 'A product must be in this category for the coupon to remain valid or, for "Product Discounts", products in these categories will be discounted.', 'gens-raf'),
						'desc' 		=> __( 'Write category ID-s, separated by comma, e.g. 3,5,7', 'gens-raf' )
					),
					array(
						'id'		=> $prefix.'exclude_product_categories',
						'name' 		=> __( 'Exclude Categories', 'gens-raf' ),
						'type' 		=> 'text',
						'desc_tip'	=> __( 'Product must not be in this category for the coupon to remain valid or, for "Product Discounts", products in these categories will not be discounted. ', 'gens-raf'),
						'desc' 		=> __( 'Write category ID-s, separated by comma, e.g. 3,5,7', 'gens-raf' )
					),
					array(
						'id'		=> $prefix.'coupon_duration',
						'name' 		=> __( 'Coupon Duration', 'gens-raf' ),
						'type' 		=> 'text',
						'desc' 		=> __( 'How many days coupon should last, just type number, like: 30', 'gens-raf' )
					),
					array(
						'id'		=> $prefix.'min_order',
						'name' 		=> __( 'Minimum Order', 'gens-raf' ), // Type: fixed_cart, percent, fixed_product, percent_product
						'type' 		=> 'number',
						'desc' 		=> __( 'Define minimum order subtotal in order for coupon to work.', 'gens-raf' )
					),
					array(
						'id'		=> $prefix.'individual_use',
						'name' 		=> __( 'Individual Use', 'gens-raf' ),
						'type' 		=> 'checkbox',
						'desc' 	=> __( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'gens-raf' ), // checkbox only
						'default' 	=> 'no'
					),
					array(
						'id'		=> $prefix.'limit_usage',
						'name' 		=> __( 'Limit usage to X Items', 'gens-raf' ),
						'type' 		=> 'number',
						'desc' 	=> __( 'The maximum number of individual items this coupon can apply to when using product discounts. Leave blank to apply to all qualifying items in cart.', 'gens-raf' ), // checkbox only
						'default' 	=> 'no'
					),
					array(
						'id'		=> $prefix.'nth_coupon',
						'name' 		=> __( 'Generate coupons on every nth referral', 'gens-raf' ),
						'type' 		=> 'number',
						'desc' 	=> __( 'By default, coupons are generated on every referral order. If you want to generate on every third. Place number 3. Leave empty for default every order.', 'gens-raf' ), // checkbox only
						'default' 	=> '1'
					),
					array(
						'id'		=> '',
						'name' 		=> __( 'General', 'gens-raf' ),
						'type' 		=> 'sectionend',
						'desc' 		=> '',
						'id'   		=> 'coupon_settings',
					),
				);
				break;
			case 'friend_coupon_settings':
				$settings = array(
					array(
						'name' => __( 'Referred Person Coupon Settings', 'gens-raf' ),
						'type' => 'title',
						'desc' => __( 'Give coupon to a user being referred as well. AFTER their first purchase. (Scroll down for first purchase.)', 'gens-raf' ),
						'id'   => 'friend_coupon_options',
					),
					array(
						'id'			=> $prefix.'friend_enable',
						'name' 			=> __( 'Enable', 'gens-raf' ),
						'type' 			=> 'checkbox',
						'label' 		=> __( 'Enable Coupons for friends', 'gens-raf' ), // checkbox only
						'desc'			=> __( 'Check to enable. Coupon code will be sent to their email.', 'gens-raf'),
						'default' 		=> 'no'
					),
					array(
						'id'			=> $prefix.'friend_coupon_type',
						'name' 			=> __( 'Coupon Type', 'gens-raf' ), // Type: fixed_cart, percent, fixed_product, percent_product
						'type' 			=> 'select',
						'class'    => 'wc-enhanced-select',
						'options'		=> array(
							'fixed_cart'	=> __( 'Cart Discount', 'gens-raf' ),
							'percent'	=> __( 'Cart % Discount', 'gens-raf' ),
							'fixed_product'	=> __( 'Product Discount', 'gens-raf' ),
							'order_percent'	=> __( 'Percent of Order', 'gens-raf' )
						)
					),
					array(
						'id'		=> $prefix.'friend_coupon_amount',
						'name' 		=> __( 'Coupon Amount', 'gens-raf' ), 
						'type' 		=> 'number',
						'desc_tip'	=> __( ' Entered without the currency unit or a percent sign as these will be added automatically, e.g., ’10’ for 10£ or 10%.', 'gens-raf'),
						'desc' 		=> __( 'Fixed value or percentage off depending on the discount type you choose.', 'gens-raf' )
					),
					array(
						'id'		=> $prefix.'friend_product_ids',
						'name' 		=> __( 'Products', 'gens-raf' ), 
						'type' 		=> 'text',
						'desc_tip'	=> __( ' Products which need to be in the cart to use this coupon or, for "Product Discounts", which products are discounted. ', 'gens-raf'),
						'desc' 		=> __( 'Write product ID-s, separated by comma, e.g. 152,321,25', 'gens-raf' )
					),
					array(
						'id'		=> $prefix.'friend_product_exclude_ids',
						'name' 		=> __( 'Exclude Products', 'gens-raf' ), 
						'type' 		=> 'text',
						'desc_tip'	=> __( ' Products that the coupon will not be applied to, or that cannot be in the cart in order for the "Fixed cart discount" to be applied.', 'gens-raf'),
						'desc' 		=> __( 'Write product ID-s, separated by comma, e.g. 152,321,25', 'gens-raf' )
					),
					array(
						'id'		=> $prefix.'friend_product_categories',
						'name' 		=> __( 'Product Categories', 'gens-raf' ),
						'type' 		=> 'text',
						'desc_tip'	=> __( 'A product must be in this category for the coupon to remain valid or, for "Product Discounts", products in these categories will be discounted.', 'gens-raf'),
						'desc' 		=> __( 'Write category ID-s, separated by comma, e.g. 3,5,7', 'gens-raf' )
					),
					array(
						'id'		=> $prefix.'friend_exclude_product_categories',
						'name' 		=> __( 'Exclude Categories', 'gens-raf' ),
						'type' 		=> 'text',
						'desc_tip'	=> __( 'Product must not be in this category for the coupon to remain valid or, for "Product Discounts", products in these categories will not be discounted. ', 'gens-raf'),
						'desc' 		=> __( 'Write category ID-s, separated by comma, e.g. 3,5,7', 'gens-raf' )
					),
					array(
						'id'		=> $prefix.'friend_coupon_duration',
						'name' 		=> __( 'Coupon Duration', 'gens-raf' ),
						'type' 		=> 'text',
						'desc' 		=> __( 'How many days coupon should last, just type number, like: 30', 'gens-raf' )
					),
					array(
						'id'		=> $prefix.'friend_min_order',
						'name' 		=> __( 'Minimum Order', 'gens-raf' ), // Type: fixed_cart, percent, fixed_product, percent_product
						'type' 		=> 'number',
						'desc' 		=> __( 'Define minimum order subtotal in order for coupon to work.', 'gens-raf' )
					),
					array(
						'id'		=> $prefix.'friend_individual_use',
						'name' 		=> __( 'Individual Use', 'gens-raf' ),
						'type' 		=> 'checkbox',
						'desc' 	=> __( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'gens-raf' ), // checkbox only
						'default' 	=> 'no'
					),
					array(
						'id'		=> $prefix.'friend_limit_usage',
						'name' 		=> __( 'Limit usage to X Items', 'gens-raf' ),
						'type' 		=> 'number',
						'desc' 	=> __( 'The maximum number of individual items this coupon can apply to when using product discounts. Leave blank to apply to all qualifying items in cart.', 'gens-raf' ), // checkbox only
						'default' 	=> 'no'
					),
					array(
						'id'		=> '',
						'name' 		=> __( 'General', 'gens-raf' ),
						'type' 		=> 'sectionend',
						'desc' 		=> '',
						'id'   		=> 'friend_coupon_options',
					),
					array(
						'name' => __( 'Referral Coupons applied on first purchase', 'gens-raf' ),
						'type' => 'title',
						'desc' => __( 'This option will allow you to automatically apply coupon to a person being referred to your site via someones referral link, this is also coupon that is applied if you enable referral codes option.<br/>
			First go to WooCommerce coupons page and manually create a coupon, choose options you want and make sure it does not have limit or email restriction. Then put coupon code in the textbox below.<br/>
			Once you activate this option, deactivate the above one, or you will be sending coupons to referrals on both first and second purchase.', 'gens-raf' ),
						'id'   => 'friend_coupon_options',
					),
					array(
						'id'			=> $prefix.'guest_enable',
						'name' 			=> __( 'Enable', 'gens-raf' ),
						'type' 			=> 'checkbox',
						'label' 		=> __( 'Enable Coupons for referrals as well.', 'gens-raf' ), // checkbox only
						'desc'			=> __( 'Check to enable. Coupon code will be automatically applied in cart.', 'gens-raf'),
						'default' 		=> 'no'
					),
					array(
						'id'		=> $prefix.'guest_coupon_code',
						'name' 		=> __( 'Coupon CODE', 'gens-raf' ),
						'type' 		=> 'text',
						'desc' 		=> __( 'Read the description above. Then paste coupon code that will be applied to referrals on first purchase.', 'gens-raf' ),
					),
					array(
						'id'		=> $prefix.'guest_coupon_msg',
						'name' 		=> __( 'Cart Message', 'gens-raf' ),
						'type' 		=> 'textarea',
						'class'     => 'input-text wide-input',
						'desc' 		=> __( 'This is the message that will be shown at the cart page when coupon is automatically applied.', 'gens-raf' ),
						'default' 	=> __( 'Your purchase through referral link earned you a coupon!', 'gens-raf' )
					),
					array(
						'id'		=> '',
						'name' 		=> __( 'General', 'gens-raf' ),
						'type' 		=> 'sectionend',
						'desc' 		=> '',
						'id'   		=> 'friend_coupon_options',
					),
				);
				break;
			case 'emails':
				$settings = array(
					array(
						'name' => __( 'Email Settings', 'gens-raf' ),
						'type' => 'title',
						'desc' => __( 'Setup the look of email that will be sent to the referral together with coupon.', 'gens-raf'),
						'id'   => 'email_options',
					),
					array(
						'id'			=> $prefix.'use_woo_mail',
						'name' 			=> __( 'WooCommerce Template', 'gens-raf' ),
						'type' 			=> 'checkbox',
						'label' 		=> __( 'Use WooCommerce default template', 'gens-raf' ),
						'desc'			=> __( 'Check this to use woo default email template.', 'gens-raf'),
						'default' 		=> 'no'
					),
					array(
						'id'			=> $prefix.'email_subject',
						'name' 			=> __( 'Email Subject', 'gens-raf' ),
						'type' 			=> 'text',
						'desc_tip'		=> __( 'Enter the subject of email that will be sent when notifiying the user of their coupon code. Use {{name}} to show users name.', 'gens-raf'),
						'default' 		=> __( 'Hey there!', 'gens-raf')
					),
					array(
						'id'			=> $prefix.'email_heading',
						'name' 			=> __( 'Email Headings', 'gens-raf' ),
						'type' 			=> 'text',
						'desc_tip'		=> __( 'Enter the email headings. It is shown above body text in bigger fonts. Use {{name}} to show users name.', 'gens-raf'),
						'default' 		=> __( 'Hey there!', 'gens-raf')
					),
					array(
						'id'			=> $prefix.'email_message',
						'name' 			=> __( 'Email Message', 'gens-raf' ),
						'type' 			=> 'textarea',
						'class'         => 'input-text wide-input',
						'desc'			=> __( 'Text that will appear in email that is sent to user once they get the code. Use {{name}} to show users name. HTML allowed.', 'gens-raf'),
						'default' 		=> __( 'You invited a friend or family member to check out our shop. We are pleased to tell you they made a purchase which means you now get discount for your next order with us. <br> Use the coupon below.', 'gens-raf')
					),
					array(
						'id'			=> $prefix.'buyer_email_subject',
						'name' 			=> __( 'Buyer Email Subject', 'gens-raf' ),
						'type' 			=> 'text',
						'desc_tip'		=> __( 'Enter the subject of email that will be sent when notifiying the buyer of their coupon code.', 'gens-raf'),
						'default' 		=> __( 'Hey there!', 'gens-raf')
					),
					array(
						'id'			=> $prefix.'buyer_email_message',
						'name' 			=> __( 'Buyer Email Message', 'gens-raf' ),
						'type' 			=> 'textarea',
						'class'         => 'input-text wide-input',
						'desc'			=> __( 'Text that will appear in email that is sent to buyer once they get the code. Use {{name}} to show users name. HTML allowed.', 'gens-raf'),
						'default' 		=> __( 'Thank you! You just made a purchase at our shop after clicking your friends referral link. And as a way of saying thank you for trusting us, we would like to give you 10% off of your next order with us. Use the coupon below.', 'gens-raf')
					),
					array(
						'id'		=> '',
						'name' 		=> __( 'General', 'gens-raf' ),
						'type' 		=> 'sectionend',
						'desc' 		=> '',
						'id'   		=> 'email_options',
					),
				);
				break;
			case 'tabs':
				$settings = array(
					array(
						'name' => __( 'Product Tab', 'gens-raf' ),
						'type' => 'title',
						'desc' => __( 'Each product can have RAF Tab enabled that includes share buttons with some text. Update text here. If you want to add some content before or after raf tab content, use "gens_tab_filter_before" and "gens_tab_filter_after" filters.', 'gens-raf'),
						'id'   => 'email_options',
					),
					array(
						'id'			=> $prefix.'tabs_disable',
						'name' 			=> __( 'Disable', 'gens-raf' ),
						'type' 			=> 'checkbox',
						'label' 		=> __( 'Disable tabs', 'gens-raf' ), // checkbox only
						'desc'			=> __( 'Check to disable. RAF Tab wont show.', 'gens-raf'),
						'default' 		=> 'no'
					),
					array(
						'id'			=> $prefix.'share_text',
						'name' 			=> __( 'Text Before', 'gens-raf' ),
						'type' 			=> 'textarea',
						'class'         => 'input-text wide-input',
						'desc_tip'		=> __( 'Text that is shown above share icons. HTML allowed.', 'gens-raf')
					),
					array(
						'id'			=> $prefix.'guest_text',
						'name' 			=> __( 'Guest text', 'gens-raf' ),
						'type' 			=> 'textarea',
						'class'         => 'input-text wide-input',
						'desc_tip'		=> __( 'Text to show when user is not logged in.', 'gens-raf'),
						'default' 		=> __( 'Please register to get your referral link.', 'gens-raf')
					),
					array(
						'id'		=> '',
						'name' 		=> __( 'General', 'gens-raf' ),
						'type' 		=> 'sectionend',
						'desc' 		=> '',
						'id'   		=> 'email_options',
					),
				);
				break;
			case 'my_account':
				$settings = array(
					array(
						'name' => __( 'My Account Page', 'gens-raf' ),
						'type' => 'title',
						'desc' => __( 'Add some text above share icons. HTML tags allowed.', 'gens-raf'),
						'id'   => 'my_account',
					),
					array(
						'id'			=> $prefix.'my_account_url',
						'name' 			=> __( 'My Account Page Share Link', 'gens-raf' ),
						'type' 			=> 'text',
						'class'         => 'regular-input',
						'desc'          => __( 'Page URL that is used for refer a friend link in my account page. Leave empty for home page.', 'gens-raf'),
						'desc_tip'		=> __( 'Default share url that is shown in my account page is home url. Change it here to some other. Use full link like: http://mysite.com/some-page/', 'gens-raf'),
						'default' 		=> ''
					),
					array(
						'id'			=> $prefix.'myaccount_text',
						'name' 			=> __( 'Text Before', 'gens-raf' ),
						'type' 			=> 'textarea',
						'class'         => 'input-text wide-input',
						'desc_tip'		=> __( 'Text that is shown above share icons.', 'gens-raf')
					),
					array(
						'id'		=> '',
						'name' 		=> __( 'My Account Page', 'gens-raf' ),
						'type' 		=> 'sectionend',
						'desc' 		=> '',
						'id'   		=> 'my_account',
					),
				);
				break;
			case 'share':
				$settings = array(
					array(
						'name' => __( 'Share options (Twitter, Facebook, Email, WhatsUp)', 'gens-raf' ),
						'type' => 'title',
						'id'   => 'plugin_options',
					),
					array(
						'id'			=> $prefix.'twitter_via',
						'name' 			=> __( 'Twitter via (without @)', 'gens-raf' ),
						'type' 			=> 'text',
						'default' 		=> ''
					),
					array(
						'id'			=> $prefix.'twitter_title',
						'name' 			=> __( 'Twitter & WhatsUp Title', 'gens-raf' ),
						'type' 			=> 'text',
						'desc'			=> __( 'Default Text that will appear as a title in Twitter and WhatsUp. User can change this manualy.', 'gens-raf'),
						'default' 		=> ''
					),
					array(
						'id'			=> $prefix.'email_subject_share',
						'name' 			=> __( 'Email Subject', 'gens-raf' ),
						'type' 			=> 'text',
						'class'         => 'input-text wide-input',
						'desc'			=> __( 'Subject of an email that is sent when user shares his URL via email. Use {{name}} to show senders name. Use {{friend_name}} to show name of a friend to whom referrer is sending email', 'gens-raf'),
					),
					array(
						'id'			=> $prefix.'email_heading_share',
						'name' 			=> __( 'Email Headings', 'gens-raf' ),
						'type' 			=> 'text',
						'desc_tip'		=> __( 'Enter the email headings. It is shown above body text in bigger fonts. Use {{name}} to show senders name. Use {{friend_name}} to show name of a friend to whom referrer is sending email.', 'gens-raf'),
						'default' 		=> __( 'Hey there!', 'gens-raf')
					),
					array(
						'id'			=> $prefix.'email_body',
						'name' 			=> __( 'Email Body', 'gens-raf' ),
						'type' 			=> 'textarea',
						'class'         => 'input-text wide-input',
						'desc'			=> __( 'Body text of the email that is sent when user shares his referral url via email. Use {{name}} to show senders name. Use {{friend_name}} to show name of a friend to whom referrer is sending email. Check the look of the email by sending an email to yourself.', 'gens-raf'),
						'default'		=> __( 'I thought you might like this site. I gave their products a try and I like them. Click on the link below and you will get discount during your purchase.', 'gens-raf')
					),
					array(
						'id'			=> $prefix.'email_from',
						'name' 			=> __( 'Send via Referrer email.', 'gens-raf' ),
						'type' 			=> 'checkbox',
						'label' 		=> __( 'Referrer email as from.', 'gens-raf' ), // checkbox only
						'desc'			=> __( 'Checking this will use referrer from email instead of sites, but gmail might mark it as a spam so test it out.', 'gens-raf'),
						'default' 		=> 'no'
					),
					array(
						'id'		=> '',
						'name' 		=> __( 'Plugins', 'gens-raf' ),
						'type' 		=> 'sectionend',
						'desc' 		=> '',
						'id'   		=> 'plugin_options',
					),
				);
				break;
			case 'howto':
				$settings = array(
					array(
						'name' => __( 'Small tutorial to help you get started', 'gens-raf' ),
						'type' => 'title',
						'desc' => sprintf( __( 'Thanks for supporting Refer a Friend plugin by purchasing premium version. <br/>
							There are additional options that comes with premium version as well as fully styled shortcodes and user statistics. <br/><br/>
							You can find new options under this "Refer a Friend" tab, while shortcodes to be used are as follow:<br/><br/>
							1. Simple Shortcode that shows just link and can be used anywhere: <strong>[WOO_GENS_RAF]</strong><br/>
							2. Advance Shortcode that comes with share links, like example on <a href="%s" target="_blank">this page</a>. To get that exact look, just center text via editor. Shortcode is:<br/> <strong>[WOO_GENS_RAF_ADVANCE guest_text="Text to show when user is not logged in" share_text="Text showing above share icons"]</strong><br/>
							3. Shortcode for Contact Form 7 plugin is <strong>[gens_raf]</strong>. User who uses contact form will have his referral URL shown in that shortcode. Place it in two places, first one before submit button inside form. Second one place in mail tab that will show in email sent to user.<br/>
							<h3>FULL GUIDE</h3>
							Check <a href="http://wpgens.helpscoutdocs.com">this link.</a> for full guide.
							<h3>QUICK SETUP GUIDE</h3>
							<ul>
								<li>1. After installing plugin, go to Refer a friend settings (this page), and click on General tab inside Refer a friend tab. Setup main plugin options.</li>
								<li>2. Click on Coupon Settings to setup a coupon for a user that referrs someone.</li>
								<li>3. Click on Referred Person Coupon Settings to setup a coupon for a user that has been referred.</li>
								<li>4. Click on Product Tab option and populate tab text, or turn it off. You can use "gens_tab_filter_before" and "gens_tab_filter_after" filters to easily add text before and after RAF tab text/share icons.</li>
								<li>5. Click on email tab and populate text that will be sent to user after he gets coupon.</li>
								<li>Thats it! Now every user will have referral link in their account page, or in page where you placed shortcode. After someone makes a purchase through their referral link, and after order is marked as complete. They will recieve coupon in their inbox.</li>
							</ul>
							', 'gens-raf' ), 'http://itsgoran.com/wp/testing-gens-raf/', 'https://profiles.wordpress.org/goran87/#content-plugins'),
						'id'   => 'plugin_options',
					),
					array(
						'id'		=> '',
						'name' 		=> __( 'Plugins', 'gens-raf' ),
						'type' 		=> 'sectionend',
						'desc' 		=> '',
						'id'   		=> 'plugin_options',
					),
				);
				break;
			default:
				$settings = array(
					array(
						'name' => __( 'General', 'gens-raf' ),
						'type' => 'title',
						'desc' => 'General Options, setup plugin here first.',
						'id'   => 'general_options',
					),
					array(
						'id'			=> $prefix.'disable',
						'name' 			=> __( 'Disable', 'gens-raf' ),
						'type' 			=> 'checkbox',
						'label' 		=> __( 'Disable Coupons', 'gens-raf' ), // checkbox only
						'desc'			=> __( 'Check to disable. Referral links wont work anymore.', 'gens-raf'),
						'default' 		=> 'no'
					),
					array(
						'id'		=> $prefix.'allow_existing',
						'name' 		=> __( 'Existing users', 'gens-raf' ),
						'type' 		=> 'checkbox',
						'desc_tip'	=> __( 'By default, if user sends referral link to other user that is registered on your site and has at least one order, referral link will not work for them. If you check this, then user can get reward for sharing referral link with both new and existing users.', 'gens-raf'),
						'desc' 		=> __( 'Check this to enable users to refer existing customers. Uncheck to enable referral link for new customers only.' ),
					),
					array(
						'id'		=> $prefix.'cookie_time',
						'name' 		=> __( 'Cookie Time', 'gens-raf' ),
						'type' 		=> 'number',
						'desc_tip'	=> __( 'As long as cookie is saved, user will recieve coupon after referral purchase product.', 'gens-raf'),
						'desc' 		=> __( 'How long to keep cookies before it expires.(In days)','gens-raf' )
					),
					array(
						'id'		=> $prefix.'min_ref_order',
						'name' 		=> __( 'Minimum referral order', 'gens-raf' ),
						'type' 		=> 'number',
						'desc' 		=> __( 'Set how much someone needs to purchase in order to generate coupon for referral','gens-raf' )
					),
					array(
						'id'		=> $prefix.'cookie_remove',
						'name' 		=> __( 'Single Purchase', 'gens-raf' ),
						'label' 	=> __( 'Single Purchase', 'gens-raf' ),
						'type' 		=> 'checkbox',
						'desc_tip'	=> __( 'This means that coupon is sent only the first time referral makes a purchase, as referral cookie is deleted after it.', 'gens-raf'),
						'desc' 		=> __( 'If checked, cookie will be deleted after customer makes a purchase.' ),
					),
					array(
						'id'		=> $prefix.'referral_codes',
						'name' 		=> __( 'Referral Codes', 'gens-raf' ),
						'label' 	=> __( 'Referral Codes', 'gens-raf' ),
						'type' 		=> 'checkbox',
						'desc_tip'	=> __( 'Referral codes are users personal coupon code that he share with his friend. Their friend will insert them as coupons and get discount while user will get new referral and once order is completed, a coupon. Applied coupon is the one that you define at the bottom of the "Referred Person Coupon Settings" tab.', 'gens-raf'),
						'desc' 		=> __( 'Checking this will enable the use of both referral links and referral codes.', 'gens-raf' ),
					),
					array(
						'id'		=> $prefix.'subscription',
						'name' 		=> __( 'WooCommerce Subscription', 'gens-raf' ),
						'label' 	=> __( 'WooCommerce Subscription', 'gens-raf' ), // checkbox only
						'type' 		=> 'checkbox',
						'desc_tip'	=> __( 'Checking this means that earned coupons will be applied on next subscription renewal.', 'gens-raf'),
						'desc' 		=> __( 'Check this to enable auto applying of coupons to subscription renewal. Works with payment gateways that support <a href="https://docs.woocommerce.com/document/subscriptions/payment-gateways/#advanced-features" target="_blank">recurring total modifications.</a>' ),
					),
					array(
						'id'		=> '',
						'name' 		=> __( 'General', 'gens-raf' ),
						'type' 		=> 'sectionend',
						'desc' 		=> '',
						'id'   		=> 'general_options',
					),
				);
				break;
		}

		/**
		 * Filter Memberships Settings
		 *
		 * @since 1.0.0
		 * @param array $settings Array of the plugin settings
		 */
		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );

	}

	/**
	 * Output the settings
	 *
	 * @since 1.0
	 */
	public function output() {
		global $current_section;

		$settings = $this->get_settings( $current_section );
		WC_Admin_Settings::output_fields( $settings );
	}


	/**
	 * Save settings
	 */
	public function save() {
		global $current_section;

		$settings = $this->get_settings( $current_section );
		WC_Admin_Settings::save_fields( $settings );
	}

}

return new WPGens_Settings_RAF();
