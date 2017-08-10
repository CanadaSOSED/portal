<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       canadasos.com
 * @since      1.0.0
 *
 * @package    Sos_Reports
 * @subpackage Sos_Reports/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h2> OMG THIS IS AWESOME!!! </h2>


<?php

//require_once( 'lib/woocommerce-api.php' );

$options = array(
	'debug'           => true,
	'return_as_array' => false,
	'validate_url'    => false,
	'timeout'         => 30,
	'ssl_verify'      => false,
);

use Automattic\WooCommerce\Client;

try {

	//$client = new WC_API_Client( 'http://localhost/sos3', 'ck_f4d42835832ea123aca0f4684fd91ce678a13784', 'cs_59c1dd818515b1040503a791f4f71b6e2ec99130', $options );


	$woocommerce = new Client(
	    'http://localhost/sos3', 
	    'ck_f4d42835832ea123aca0f4684fd91ce678a13784', 
	    'cs_59c1dd818515b1040503a791f4f71b6e2ec99130',
	    [
	        'wp_api' => true,
	        'version' => 'wc/v2',
	    ]
	);

	//print_r($woocommerce->get(''));

	// coupons
	//print_r( $woocommerce->coupons->get() );
	//print_r( $woocommerce->coupons->get( $coupon_id ) );
	//print_r( $woocommerce->coupons->get_by_code( 'coupon-code' ) );
	//print_r( $woocommerce->coupons->create( array( 'code' => 'test-coupon', 'type' => 'fixed_cart', 'amount' => 10 ) ) );
	//print_r( $woocommerce->coupons->update( $coupon_id, array( 'description' => 'new description' ) ) );
	//print_r( $woocommerce->coupons->delete( $coupon_id ) );
	//print_r( $woocommerce->coupons->get_count() );

	// custom
	//$woocommerce->custom->setup( 'webhooks', 'webhook' );
	//print_r( $woocommerce->custom->get( $params ) );

	// customers
	//print_r( $woocommerce->customers->get() );
	//print_r( $woocommerce->customers->get( $customer_id ) );
	//print_r( $woocommerce->customers->get_by_email( 'help@woothemes.com' ) );
	//print_r( $woocommerce->customers->create( array( 'email' => 'woothemes@mailinator.com' ) ) );
	//print_r( $woocommerce->customers->update( $customer_id, array( 'first_name' => 'John', 'last_name' => 'Galt' ) ) );
	//print_r( $woocommerce->customers->delete( $customer_id ) );
	//print_r( $woocommerce->customers->get_count( array( 'filter[limit]' => '-1' ) ) );
	//print_r( $woocommerce->customers->get_orders( $customer_id ) );
	//print_r( $woocommerce->customers->get_downloads( $customer_id ) );
	//$customer = $woocommerce->customers->get( $customer_id );
	//$customer->customer->last_name = 'New Last Name';
	//print_r( $woocommerce->customers->update( $customer_id, (array) $customer ) );

	// index
	//print_r( $woocommerce->index->get() );

	// orders
	//print_r( $woocommerce->orders->get() );
	//print_r( $woocommerce->orders->get( $order_id ) );
	//print_r( $woocommerce->orders->update_status( $order_id, 'pending' ) );

	// order notes
	//print_r( $woocommerce->order_notes->get( $order_id ) );
	//print_r( $woocommerce->order_notes->create( $order_id, array( 'note' => 'Some order note' ) ) );
	//print_r( $woocommerce->order_notes->update( $order_id, $note_id, array( 'note' => 'An updated order note' ) ) );
	//print_r( $woocommerce->order_notes->delete( $order_id, $note_id ) );

	// order refunds
	//print_r( $woocommerce->order_refunds->get( $order_id ) );
	//print_r( $woocommerce->order_refunds->get( $order_id, $refund_id ) );
	//print_r( $woocommerce->order_refunds->create( $order_id, array( 'amount' => 1.00, 'reason' => 'cancellation' ) ) );
	//print_r( $woocommerce->order_refunds->update( $order_id, $refund_id, array( 'reason' => 'who knows' ) ) );
	//print_r( $woocommerce->order_refunds->delete( $order_id, $refund_id ) );

	// products
	$thing = $woocommerce->get('orders');
	echo '<pre>' . var_export($thing, true) . '</pre>';
	//print_r($woocommerce->get('products'));
	//print_r( $woocommerce->products->get( $product_id ) );
	//print_r( $woocommerce->products->get( $variation_id ) );
	//print_r( $woocommerce->products->get_by_sku( 'a-product-sku' ) );
	//print_r( $woocommerce->products->create( array( 'title' => 'Test Product', 'type' => 'simple', 'regular_price' => '9.99', 'description' => 'test' ) ) );
	//print_r( $woocommerce->products->update( $product_id, array( 'title' => 'Yet another test product' ) ) );
	//print_r( $woocommerce->products->delete( $product_id, true ) );
	//print_r( $woocommerce->products->get_count() );
	//print_r( $woocommerce->products->get_count( array( 'type' => 'simple' ) ) );
	//print_r( $woocommerce->products->get_categories() );
	//print_r( $woocommerce->products->get_categories( $category_id ) );

	// reports
	//print_r( $woocommerce->reports->get() );
	//print_r( $woocommerce->reports->get_sales( array( 'filter[date_min]' => '2014-07-01' ) ) );
	//print_r( $woocommerce->reports->get_top_sellers( array( 'filter[date_min]' => '2014-07-01' ) ) );

	// webhooks
	//print_r( $woocommerce->webhooks->get() );
	//print_r( $woocommerce->webhooks->create( array( 'topic' => 'coupon.created', 'delivery_url' => 'http://requestb.in/' ) ) );
	//print_r( $woocommerce->webhooks->update( $webhook_id, array( 'secret' => 'some_secret' ) ) );
	//print_r( $woocommerce->webhooks->delete( $webhook_id ) );
	//print_r( $woocommerce->webhooks->get_count() );
	//print_r( $woocommerce->webhooks->get_deliveries( $webhook_id ) );
	//print_r( $woocommerce->webhooks->get_delivery( $webhook_id, $delivery_id );

	// trigger an error
	//print_r( $woocommerce->orders->get( 0 ) );

	define( 'DIEONDBERROR', true ); 
	$wpdb->print_error();


/*
// SQL Script To Get All WooCommerce Orders Including Metadata
select
    p.ID as order_id,
    p.post_date,
    max( CASE WHEN pm.meta_key = '_billing_email' and p.ID = pm.post_id THEN pm.meta_value END ) as billing_email,
    max( CASE WHEN pm.meta_key = '_billing_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_first_name,
    max( CASE WHEN pm.meta_key = '_billing_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_last_name,
    max( CASE WHEN pm.meta_key = '_billing_address_1' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_address_1,
    max( CASE WHEN pm.meta_key = '_billing_address_2' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_address_2,
    max( CASE WHEN pm.meta_key = '_billing_city' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_city,
    max( CASE WHEN pm.meta_key = '_billing_state' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_state,
    max( CASE WHEN pm.meta_key = '_billing_postcode' and p.ID = pm.post_id THEN pm.meta_value END ) as _billing_postcode,
    max( CASE WHEN pm.meta_key = '_shipping_first_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_first_name,
    max( CASE WHEN pm.meta_key = '_shipping_last_name' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_last_name,
    max( CASE WHEN pm.meta_key = '_shipping_address_1' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_address_1,
    max( CASE WHEN pm.meta_key = '_shipping_address_2' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_address_2,
    max( CASE WHEN pm.meta_key = '_shipping_city' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_city,
    max( CASE WHEN pm.meta_key = '_shipping_state' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_state,
    max( CASE WHEN pm.meta_key = '_shipping_postcode' and p.ID = pm.post_id THEN pm.meta_value END ) as _shipping_postcode,
    max( CASE WHEN pm.meta_key = '_order_total' and p.ID = pm.post_id THEN pm.meta_value END ) as order_total,
    max( CASE WHEN pm.meta_key = '_order_tax' and p.ID = pm.post_id THEN pm.meta_value END ) as order_tax,
    max( CASE WHEN pm.meta_key = '_paid_date' and p.ID = pm.post_id THEN pm.meta_value END ) as paid_date,
    ( select group_concat( order_item_name separator '|' ) from wp_woocommerce_order_items where order_id = p.ID ) as order_items
from
    wp_posts p 
    join wp_postmeta pm on p.ID = pm.post_id
    join wp_woocommerce_order_items oi on p.ID = oi.order_id
where
    post_type = 'shop_order' and
    post_date BETWEEN '2015-01-01' AND '2015-07-08' and
    post_status = 'wc-completed' and
    oi.order_item_name = 'Product Name'
group by
    p.ID
  */



	if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {
	    $sites = get_sites();
	    foreach ( $sites as $site ) {
	        switch_to_blog( $site->blog_id );

	        // set the meta_key to the appropriate custom field meta key
	        $meta_key = 'miles';
	        $allmiles = $wpdb->get_var( $wpdb->prepare( 
	        	"
	        		SELECT sum(meta_value) 
	        		FROM $wpdb->postmeta 
	        		WHERE meta_key = %s
	        	", 
	        	$meta_key
	        ) );
	        echo "<p>Total miles is {$allmiles}</p>";


	        echo '<pre>' . var_export($thing, true) . '</pre> <h2>Site:'. $site  .'End</h2><br/><hr/><br/>';

	        restore_current_blog();
	    }
	    return;
	}


} catch ( WC_API_Client_Exception $e ) {

	echo $e->getMessage() . PHP_EOL;
	echo $e->getCode() . PHP_EOL;

	if ( $e instanceof WC_API_Client_HTTP_Exception ) {

		print_r( $e->get_request() );
		print_r( $e->get_response() );
	}
}
