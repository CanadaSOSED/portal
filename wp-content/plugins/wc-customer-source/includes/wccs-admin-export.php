<?php
/**
 * WC_Customer_Source_Settings Class
 *
 * @version	1.0.0
 * @since 1.0.0
 * @package	WC_Customer_Source
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WC_Customer_Source_Settings Class
 */
final class WC_Customer_Source_Export
{
    public static function export_data() {

        if ( ! $_POST )
            return;

        if ( ! wp_verify_nonce( $_POST['_wccs_nonce'], 'wc_customer_source_nonce' ) )
            return;

        if( empty( $_POST['woa_export'] ) || 'export_source' != $_POST['woa_export'] )
		return;
	if( ( $_POST['olimit'] ) )
		$lim = $_POST['olimit'];
	if( !( $_POST['olimit'] ) )
		$lim = -1;
	if( ( $_POST['date[ssd]'] ) )
		$ssd = $_POST['date[ssd]'];
		$StartDate = strtotime($ssd);
	if( ( $_POST['date[eed]'] ) )
		$eed = $_POST['date[eed]'];
		$EndDate = strtotime($eed);
	if( ! wp_verify_nonce( $_POST['_wccs_nonce'], 'wc_customer_source_nonce' ) )
		return;
	if( ! current_user_can( 'manage_options' ) )
		return;
	ob_end_clean();
// output headers so that the file is downloaded rather than displayed
header('Content-type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=customer-source-' . date( 'm-d-Y' ) . '.csv' );
 
// do not cache the file
header('Pragma: no-cache');
header('Expires: 0');
 
// create a file pointer connected to the output stream
$file = fopen("php://output", 'w');
 
// send the column headers
fputcsv($file, array('Order ID', 'Order Status', 'Customer Name', 'Customer Source', 'Order Created'));

$query = new WC_Order_Query( array(
	'limit' => $lim,
) );

$orders = $query->get_orders();

$items = array();

        foreach ($orders as $order) {
			
			$order_id = $order->id;
			$order_status = $order->status;
			$cust_name = $order->billing_first_name . ' ' . $order->billing_last_name;
			
            $customer_source = get_post_meta( $order->id, 'wc_customer_source_checkout_field', true );
            $other  = get_post_meta( $order->id, 'wc_customer_source_checkout_other_field', true );

            $customer_source .= ( $customer_source == 'other' && $other ) ? ' - ' . $other : '';
			$ordercreated = $order->date_created;

            $items[] = array($order_id,$order_status,$cust_name,$customer_source,$ordercreated );
        }
		foreach ($items as $rows) {
			fputcsv($file, $rows);
		}
		fclose($file);
		exit();
    }

    public static function display_export_page() {

        $settings = get_option( 'wc_customer_source_export' );

        $plugin_enabled = isset( $settings['plugin_enabled'] ) ? $settings['plugin_enabled'] : true;
        ?>
			 <h2><?php echo __( 'Export Data to CSV', 'wc-customer-source' ); ?></h2>
						<form method="post">
						<p><input type="hidden" name="woa_export" value="export_source" /></p>
							<h1>
								Export latest orders
							</h1>
							<p>
								Enter number of recent orders to export (leave blank to export all)
							</p>
							<input type="text" id="olimit" name="olimit" value="" class="olimit" />
							WARNING: Exporting large amounts of data at a time may cause your server to timeout.
						<p>
							<?php wp_nonce_field( 'wc_customer_source_nonce', '_wccs_nonce' ); ?>
							<?php submit_button( __( 'Export Data to CSV' ), 'secondary', 'submit', false ); ?>
						</p>
					</form>
<?php
    }
}