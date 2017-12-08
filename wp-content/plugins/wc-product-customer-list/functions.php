<?php
/**
 * @package WC_Product_Customer_List
 * @version 2.4.4
 */

// WooCommerce version check

if( ! function_exists('woocommerce_version_check') ) {
	function woocommerce_version_check( $version = '3.0' ) {
		if ( class_exists( 'WooCommerce' ) ) {
			global $woocommerce;
			if ( version_compare( $woocommerce->version, $version, ">=" ) ) {
				return true;
			}
		}
		return false;
	}
}

// Admin notice

if( ! function_exists('wpcl_admin_message') ) {
	function wpcl_admin_message() {
		echo '<div class="error"><p>' . __('Woocommerce Product Customer List is enabled but not effective. It requires WooCommerce 2.2+ in order to work.', 'wc-product-customer-list') . '</p></div>';
	}
}

// Localize plugin

if( ! function_exists('wpcl_load_textdomain') ) {
	function wpcl_load_textdomain() {
		load_plugin_textdomain( 'wc-product-customer-list', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
	}
	add_action('plugins_loaded', 'wpcl_load_textdomain');
}