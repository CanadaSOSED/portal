<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'woocommerce/woocommerce.php' ) ){
	return;
}

class ACUI_WooCommerce{
	private $all_virtual;

	function __construct(){
		add_filter( 'acui_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
		add_action( 'acui_documentation_after_plugins_activated', array( $this, 'documentation' ) );
	}

	function fields(){
		return array(
			'billing_first_name', // Billing Address Info
			'billing_last_name',
			'billing_company',
			'billing_address_1',
			'billing_address_2',
			'billing_city',
			'billing_state',
			'billing_postcode',
			'billing_country',
			'billing_email',
			'billing_phone',

			'shipping_first_name', // Shipping Address Info
			'shipping_last_name',
			'shipping_company',
			'shipping_address_1',
			'shipping_address_2',
			'shipping_city',
			'shipping_state',
			'shipping_postcode',
			'shipping_country',
		);
	}

	function restricted_fields( $acui_restricted_fields ){
		return array_merge( $acui_restricted_fields, $this->fields() );
	}

	function documentation(){
	?>
		<tr valign="top">
			<th scope="row"><?php _e( "WooCommerce is activated", 'import-users-from-csv-with-meta' ); ?></th>
			<td><?php _e( "You can use those labels if you want to set data adapted to the WooCommerce default user columns", 'import-users-from-csv-with-meta' ); ?>
				<ol>
					<li>billing_first_name</li>
					<li>billing_last_name</li>
					<li>billing_company</li>
					<li>billing_address_1</li>
					<li>billing_address_2</li>
					<li>billing_city</li>
					<li>billing_postcode</li>
					<li>billing_country</li>
					<li>billing_state</li>
					<li>billing_phone</li>
					<li>billing_email</li>
					<li>shipping_first_name</li>
					<li>shipping_last_name</li>
					<li>shipping_company</li>
					<li>shipping_address_1</li>
					<li>shipping_address_2</li>
					<li>shipping_city</li>
					<li>shipping_postcode</li>
					<li>shipping_country</li>
					<li>shipping_state</li>
				</ol>
			</td>
		</tr>
		<?php
	}

}

new ACUI_WooCommerce();