<?php
/**
 * @package WC_Product_Customer_List
 * @version 2.4.9
 */

function wpcl_add_section( $sections ) {
	$sections['wpcl'] = __( 'Product Customer List', 'wc-product-customer-list' );
	return $sections;
}
add_filter( 'woocommerce_get_sections_products', 'wpcl_add_section' );

function wpcl_all_settings( $settings, $current_section ) {

	// Get all available statuses
	$statuses = array();
	foreach ( get_post_stati( array( 'show_in_admin_status_list' => true ), 'objects' ) as $status ) { 
		if ( ! in_array( $status->name, array( 'publish', 'draft', 'pending', 'trash', 'future', 'private', 'auto-draft' ) ) ) { 
			$statuses[$status->name] = $status->label;
		}
	}
	if ( $current_section == 'wpcl' ) {
		$settings_wpcl = array();
		$settings_wpcl[] = array( 'name' => __( 'Product Customer List for WooCommerce', 'wc-product-customer-list' ), 'type' => 'title', 'desc' => __( 'The following options are used to configure Product Customer List for WooCommerce', 'wc-product-customer-list' ), 'id' => 'wcslider' );
		$settings_wpcl[] = array(
			'name'    => __( 'Order status', 'woocommerce' ),
			'desc'    => __( 'Select one or multiple order statuses for which you will display the customers.', 'wc-product-customer-list' ),
			'id'      => 'wpcl_order_status_select',
			'css'     => 'min-width:300px;',
			'default' => array('wc-completed','wc-processing'),
			'type'    => 'multiselect',
			'options' => $statuses,
			'desc_tip' =>  true,
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Columns', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_order_number',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable order number column', 'wc-product-customer-list' ),
			'checkboxgroup' => 'start'
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Order date column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_order_date',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'css' 		=> 'min-width:300px;',
			'desc'		=> __( 'Enable order date column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Order status column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_order_status',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable order status column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Order quantity column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_order_qty',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable order quantity column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Order total column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_order_total',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css' 		=> 'min-width:300px;',
			'desc'		=> __( 'Enable order total column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Payment method column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_order_payment',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css' 		=> 'min-width:300px;',
			'desc'		=> __( 'Enable payment method column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Customer message column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_customer_message',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable customer message column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Customer ID', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_customer_ID',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable customer ID column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing first name column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_first_name',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing first name column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing last name column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_last_name',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing last name column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing e-mail column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_email',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing e-mail column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing phone column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_phone',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing phone column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing address 1 column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_address_1',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing address 1 column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing address 2 column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_address_2',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing address 2 column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing city column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_city',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing city column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing state column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_state',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'css' 		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing state column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing Postal Code / Zip column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_postalcode',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing postal code / Zip column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing country column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_billing_country',
			'default'	=> 'no',
				'type'		=> 'checkbox',
				'css'		=> 'min-width:300px;',
				'desc' 		=> __( 'Enable billing country column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping first name column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_first_name',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping first name column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping last name column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_last_name',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping last name column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping address 1 column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_address_1',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping address 1 column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping address 2 column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_address_2',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping address 2 column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping city column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_city',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping city column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping state column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_state',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping state column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping Postal Code / Zip column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_postalcode',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping postal code / Zip column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping country column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shipping_country',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping country column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'PDF orientation', 'woocommerce' ),
			'id'		=> 'wpcl_export_pdf_orientation',
			'css'		=> 'min-width:300px;',
			'default'	=> array('portrait'),
			'type'		=> 'select',
			'options'	=> array(
				'portrait' 		=> __( 'Portrait', 'wc-product-customer-list' ),
				'landscape'		=> __( 'Landscape', 'wc-product-customer-list' ),
			),
			'desc_tip'	=>  false,
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'PDF page size', 'woocommerce' ),
			'id'		=> 'wpcl_export_pdf_pagesize',
			'css'		=> 'min-width:300px;',
			'default'	=> array('letter'),
			'type'		=> 'select',
			'options'	=> array(
				'LETTER'	=> __( 'US Letter', 'wc-product-customer-list' ),
				'LEGAL'		=> __( 'US Legal', 'wc-product-customer-list' ),
				'A3'		=> __( 'A3', 'wc-product-customer-list' ),
				'A4'		=> __( 'A4', 'wc-product-customer-list' ),
				'A5'		=> __( 'A5', 'wc-product-customer-list' ),
			),
			'desc_tip' =>  false,
		);

		/*

		$settings_wpcl[] = array( 'type' => 'sectionend', 'id' => 'wpcl' );

		$settings_wpcl[] = array( 'name' => __( 'Shortcode', 'wc-product-customer-list' ), 'type' => 'title', 'id' => 'wcslider' );

		$settings_wpcl[] = array(
			'name'		=> __( 'Order number column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_order_number',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable order number column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Order date column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_order_date',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'css' 		=> 'min-width:300px;',
			'desc'		=> __( 'Enable order date column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Order status column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_order_status',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable order status column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Order quantity column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_order_qty',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable order quantity column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Payment method column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_order_payment',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css' 		=> 'min-width:300px;',
			'desc'		=> __( 'Enable payment method column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Customer message column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_customer_message',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable customer message column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Customer ID', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_customer_ID',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable customer ID column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing first name column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_billing_first_name',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing first name column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing last name column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_billing_last_name',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing last name column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing e-mail column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_billing_email',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing e-mail column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing phone column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_billing_phone',
			'default'	=> 'yes',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing phone column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing address 1 column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_billing_address_1',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing address 1 column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing address 2 column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_billing_address_2',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing address 2 column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing city column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_billing_city',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing city column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing state column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_billing_state',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'css' 		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing state column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing Postal Code / Zip column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_billing_postalcode',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable billing postal code / Zip column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Billing country column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_billing_country',
			'default'	=> 'no',
				'type'		=> 'checkbox',
				'css'		=> 'min-width:300px;',
				'desc' 		=> __( 'Enable billing country column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping first name column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_shipping_first_name',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping first name column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping last name column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_shipping_last_name',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping last name column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping address 1 column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_shipping_address_1',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping address 1 column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping address 2 column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_shipping_address_2',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping address 2 column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping city column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_shipping_city',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping city column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping state column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_shipping_state',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping state column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping Postal Code / Zip column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_shipping_postalcode',
			'default'	=> 'no',
			'type' 		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping postal code / Zip column', 'wc-product-customer-list' ),
		);
		$settings_wpcl[] = array(
			'name'		=> __( 'Shipping country column', 'wc-product-customer-list' ),
			'id'		=> 'wpcl_shortcode_shipping_country',
			'default'	=> 'no',
			'type'		=> 'checkbox',
			'css'		=> 'min-width:300px;',
			'desc'		=> __( 'Enable shipping country column', 'wc-product-customer-list' ),
		);
		*/

			$settings_wpcl[] = array( 'type' => 'sectionend', 'id' => 'wpcl' );
			
			return $settings_wpcl;

	} else {
		return $settings;
	}
}

add_filter( 'woocommerce_get_settings_products', 'wpcl_all_settings', 10, 2 );