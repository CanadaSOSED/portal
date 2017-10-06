<?php
/**
 * Plugin Name: SOS Custom Woocommerce Columns
 * Plugin URI: 
 * Description: Adds some additional column to woocommerce. 
 * Version: 1.0 
 * Author: SOS Development Team <briancaicco@gmail.com>
 * Author URI: 
 * License: GPL2
 *
 * Text Domain: customize-customizer
 * Domain Path: /languages/
 *
 */


add_filter( 'manage_edit-shop_order_columns', 'sos_woocommerce_custom_columns' );

function sos_woocommerce_custom_columns( $columns ) {
	$new_columns = ( is_array( $columns ) ) ? $columns : array();
  	unset( $new_columns[ 'order_actions' ] );
	
  	$new_columns['MY_COLUMN_ID_1'] = 'MY_COLUMN_1_TITLE';
  	$new_columns['MY_COLUMN_ID_2'] = 'MY_COLUMN_2_TITLE';
	

  	$new_columns[ 'order_actions' ] = $columns[ 'order_actions' ];
  	return $new_columns;
}


add_filter( "manage_edit-shop_order_sortable_columns", 'sos_woocommerce_custom_columns_sort' );
function sos_woocommerce_custom_columns_sort( $columns ) 
{
	$custom = array(
			'MY_COLUMN_ID_1'    => 'MY_COLUMN_1_POST_META_ID', 
			'MY_COLUMN_ID_2'    => 'MY_COLUMN_2_POST_META_ID' 
			);
	return wp_parse_args( $custom, $columns );
}

add_action( 'manage_shop_order_posts_custom_column', 'sos_woocommerce_custom_columns_order', 2 );
function sos_woocommerce_custom_columns_order( $column ) {
	global $post;
	$data = get_post_meta( $post->ID );
	
	
	if ( $column == 'MY_COLUMN_ID_1' ) {
		echo ( isset( $data[ 'MY_COLUMN_1_POST_META_ID' ] ) ? $data[ 'MY_COLUMN_1_POST_META_ID' ] : '' );
	}
	
	if ( $column == 'MY_COLUMN_ID_2' ) {
		echo ( isset( $data[ 'MY_COLUMN_2_POST_META_ID' ] ) ? $data[ 'MY_COLUMN_2_POST_META_ID' ] : '' );
	}
}


if ( get_option( 'orddd_show_column_on_orders_page_check' ) == 'on' ) {
    add_filter( 'manage_edit-shop_order_columns', array( 'orddd_filter', 'orddd_woocommerce_order_delivery_date_column' ), 20, 1 );
    add_action( 'manage_shop_order_posts_custom_column', array( 'orddd_filter', 'orddd_woocommerce_custom_column_value' ), 20, 1 );
    add_filter( 'manage_edit-shop_order_sortable_columns', array( 'orddd_filter', 'orddd_woocommerce_custom_column_value_sort' ) );
    add_filter( 'request', array( 'orddd_filter', 'orddd_woocommerce_delivery_date_orderby' ) );			     
}
