<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Product Vendors Orders Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.1.0
 */

class WCFM_Orders_WCPVendors_Controller {
	
	private $vendor_id;
	private $vendor_data;
	
	public function __construct() {
		global $WCFM;
		
		$this->vendor_id   = WC_Product_Vendors_Utils::get_logged_in_vendor();
		$this->vendor_data = WC_Product_Vendors_Utils::get_vendor_data_from_user();
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST, $start_date, $end_date;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$user_id = $this->vendor_id;
		
		$can_view_orders = apply_filters( 'wcfm_is_allow_order_details', true );
		
		$the_orderby = ! empty( $_POST['orderby'] ) ? sanitize_text_field( $_POST['orderby'] ) : 'order_id';
		$the_order   = ( ! empty( $_POST['order'] ) && 'asc' === $_POST['order'] ) ? 'ASC' : 'DESC';

		$items_per_page = $length;

		$sql = 'SELECT COUNT(commission.id) FROM ' . WC_PRODUCT_VENDORS_COMMISSION_TABLE . ' AS commission';

		$sql .= ' WHERE 1=1';

		$sql .= " AND `vendor_id` = {$this->vendor_id}";

		// check if it is a search
		if ( ! empty( $_POST['search']['value'] ) ) {
			$order_id = absint( $_POST['search']['value'] );

			$sql .= " AND `order_id` = {$order_id}";

		} else {

			if ( ! empty( $_POST['m'] ) ) {

				$year  = absint( substr( $_POST['m'], 0, 4 ) );
				$month = absint( substr( $_POST['m'], 4, 2 ) );

				$time_filter = " AND MONTH( commission.order_date ) = {$month} AND YEAR( commission.order_date ) = {$year}";

				$sql .= $time_filter;
			}

			if ( ! empty( $_POST['commission_status'] ) ) {
				$commission_status = esc_sql( $_POST['commission_status'] );

				$status_filter = " AND `commission_status` = '{$commission_status}'";

				$sql .= $status_filter;
			}
		}

		$total_items = $wpdb->get_var( $sql );

		$sql = 'SELECT * FROM ' . WC_PRODUCT_VENDORS_COMMISSION_TABLE . ' AS commission';

		$sql .= ' WHERE 1=1';

		$sql .= " AND `vendor_id` = {$this->vendor_id}";

		// check if it is a search
		if ( ! empty( $_POST['search']['value'] ) ) {
			$order_id = absint( $_POST['search']['value'] );

			$sql .= " AND `order_id` = {$order_id}";

		} else {

			if ( ! empty( $_POST['m'] ) ) {
				$sql .= $time_filter;
			}

			if ( ! empty( $_POST['commission_status'] ) ) {
				$sql .= $status_filter;
			}
		}

		$sql .= " ORDER BY `{$the_orderby}` {$the_order}";

		$sql .= " LIMIT {$items_per_page}";

		$sql .= " OFFSET {$offset}";

		$data = $wpdb->get_results( $sql );

		$the_order_summary = $data;
		
		// Generate Products JSON
		$wcfm_orders_json = '';
		$wcfm_orders_json = '{
														"draw": ' . $_POST['draw'] . ',
														"recordsTotal": ' . (double) $total_items . ',
														"recordsFiltered": ' . (double) $total_items . ',
														"data": ';
		
		if ( !empty( $the_order_summary ) ) {
			$index = 0;
			$totals = 0;
			$wcfm_orders_json_arr = array();
			
			foreach ( $the_order_summary as $order ) {
	
				$the_order = new WC_Order( $order->order_id );
				//$the_order = wc_get_order( $the_order );
				$needs_shipping = false; 
	
				$the_order_date = ( version_compare( WC_VERSION, '2.7', '<' ) ) ? $the_order->order_date : $the_order->get_date_created(); 
	
				// Status
				$wcfm_orders_json_arr[$index][] =  '<span class="order-status tips wcicon-status-' . sanitize_title( $the_order->get_status() ) . ' text_tip" data-tip="' . wc_get_order_status_name( $the_order->get_status() ) . '"></span>';
				
				// Order
				if ( $the_order->user_id ) {
					$user_info = get_userdata( $the_order->user_id );
				}
	
				if ( ! empty( $user_info ) ) {
	
					$username = '';
	
					if ( $user_info->first_name || $user_info->last_name ) {
						$username .= esc_html( sprintf( _x( '%1$s %2$s', 'full name', 'wc-frontend-manager' ), ucfirst( $user_info->first_name ), ucfirst( $user_info->last_name ) ) );
					} else {
						$username .= esc_html( ucfirst( $user_info->display_name ) );
					}
	
				} else {
					if ( $the_order->billing_first_name || $the_order->billing_last_name ) {
						$username = trim( sprintf( _x( '%1$s %2$s', 'full name', 'wc-frontend-manager' ), $the_order->billing_first_name, $the_order->billing_last_name ) );
					} else if ( $the_order->billing_company ) {
						$username = trim( $the_order->billing_company );
					} else {
						$username = __( 'Guest', 'wc-frontend-manager' );
					}
				}
	
				if( $can_view_orders )
					$wcfm_orders_json_arr[$index][] =  '<a href="' . get_wcfm_view_order_url($the_order->id, $the_order) . '" class="wcfm_order_title">#' . esc_attr( $the_order->get_order_number() ) . '</a> by ' . $username;
				else
					$wcfm_orders_json_arr[$index][] =  '#' . esc_attr( $the_order->get_order_number() ) . ' by ' . $username;
				
				// Purchased
				$the_order_item_details = '<div class="order_items" cellspacing="0">';
				$quantity       = absint( $order->product_quantity );
				$var_attributes = '';
				$sku            = '';

				// check if product is a variable product
				if ( ! empty( $order->variation_id ) ) {
					$product = wc_get_product( absint( $order->variation_id ) );

					$the_order_item = WC_Order_Factory::get_order_item( $order->order_item_id );
					
					if ( $metadata = $the_order_item->get_formatted_meta_data() ) {
						foreach ( $metadata as $meta_id => $meta ) {
							// Skip hidden core fields
							if ( in_array( $meta->key, apply_filters( 'wcpv_hidden_order_itemmeta', array(
								'_qty',
								'_tax_class',
								'_product_id',
								'_variation_id',
								'_line_subtotal',
								'_line_subtotal_tax',
								'_line_total',
								'_line_tax',
								'_fulfillment_status',
								'_commission_status',
								'method_id',
								'cost',
							) ) ) ) {
								continue;
							}

							$var_attributes .= sprintf( __( '<br /><small>( %1$s: %2$s )</small>', 'woocommerce-product-vendors' ), wp_kses_post( rawurldecode( $meta->display_key ) ), wp_kses_post( $meta->value ) );
						}
					}
				} else {
					$product = wc_get_product( absint( $order->product_id ) );
				}

				if ( is_object( $product ) && $product->get_sku() ) {
					$sku = sprintf( __( '%1$s %2$s: %3$s', 'woocommerce-product-vendors' ), '<br />', 'SKU', $product->get_sku() );
				}
				
				if ( is_object( $product ) ) {
					$the_order_item_details .= '<div>' . $quantity . 'x ' . sanitize_text_field( $order->product_name ) . $var_attributes . $sku . '</div>';

				} elseif ( ! empty( $order->product_name ) ) {
					$the_order_item_details .= '<div>' . $quantity . 'x ' . sanitize_text_field( $order->product_name ) . '</div>';

				} else {
					$the_order_item_details .= '<div>' . sprintf( '%s ' . __( 'Product Not Found', 'woocommerce-product-vendors' ), '#' . absint( $order->product_id ) ) . '</div>';
				}
				
				$the_order_item_details .= '</div>';
				$wcfm_orders_json_arr[$index][] = '<a href="#" class="show_order_items">' . sprintf( _n( '%d item', '%d items', $quantity, 'wc-frontend-manager' ), $quantity ) . '</a>' . $the_order_item_details;
				
				// Total
				$status = __( 'N/A', 'woocommerce-product-vendors' );

				if ( 'unpaid' === $order->commission_status ) {
					$status = '<span class="wcpv-unpaid-status">' . esc_html__( 'UNPAID', 'woocommerce-product-vendors' ) . '</span>';
				}

				if ( 'paid' === $order->commission_status ) {
					$status = '<span class="wcpv-paid-status">' . esc_html__( 'PAID', 'woocommerce-product-vendors' ) . '</span>';
				}

				if ( 'void' === $order->commission_status ) {
					$status = '<span class="wcpv-void-status">' . esc_html__( 'VOID', 'woocommerce-product-vendors' ) . '</span>';
				}
				$wcfm_orders_json_arr[$index][] =  wc_price( sanitize_text_field( $order->total_commission_amount ) ) . '<br />' . $status;
				
				// Date
				$wcfm_orders_json_arr[$index][] = date_i18n( wc_date_format(), strtotime( $the_order_date ) );
				
				// Action
				if( $can_view_orders )
					$actions = '<a class="wcfm-action-icon" href="' . get_wcfm_view_order_url($the_order->id, $the_order) . '"><span class="fa fa-eye text_tip" data-tip="' . esc_attr__( 'View Details', 'wc-frontend-manager' ) . '"></span></a>';
				else
				  $actions = '';
				
				if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true) ) {
						$actions .= '<a class="wcfm_wcvendors_order_mark_shipped_dummy wcfm-action-icon" href="#" data-orderid="' . $wcfm_orders_single->ID . '"><span class="fa fa-truck text_tip" data-tip="' . esc_attr__( 'Mark Shipped', $WCFMu->text_domain ) . '"></span></a>';
					}
				}
				
				if( $wcfm_is_allow_pdf_invoice = apply_filters( 'wcfm_is_allow_pdf_invoice', true ) ) {
					if( WCFM_Dependencies::wcfmu_plugin_active_check() && WCFM_Dependencies::wcfm_wc_pdf_invoices_packing_slips_plugin_active_check() ) {
						$actions .= '<a class="wcfm_pdf_invoice wcfm-action-icon" href="#" data-orderid="' . $the_order->ID . '"><span class="fa fa-file-pdf-o text_tip" data-tip="' . esc_attr__( 'PDF Invoice', 'wc-frontend-manager' ) . '"></span></a>';
					} else {
						if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true) ) {
							$actions .= '<a class="wcfm_pdf_invoice_vendor_dummy wcfm-action-icon" href="#" data-orderid="' . $wcfm_orders_single->ID . '"><span class="fa fa-file-pdf-o text_tip" data-tip="' . esc_attr__( 'PDF Invoice', 'wc-frontend-manager' ) . '"></span></a>';
						}
					}
				}
				
				$wcfm_orders_json_arr[$index][] =  apply_filters ( 'wcpvendors_orders_actions', $actions, $user_id, $the_order, $order );
				
				$index++;
			}
		}
		if( !empty($wcfm_orders_json_arr) ) $wcfm_orders_json .= json_encode($wcfm_orders_json_arr);
		else $wcfm_orders_json .= '[]';
		$wcfm_orders_json .= '
													}';
													
		echo $wcfm_orders_json;
	}
}