<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * WC_Customer_Source_Report.
 *
 * @category    Admin
 * @since       1.1.0
 * @package     WC_Customer_Source
 */
class WC_Customer_Source_Report extends WP_List_Table {

	/**
	 * Constructor.
	 */
	public function __construct() {

		parent::__construct( array(
			'singular'  => __( 'Report', 'wc-customer-source' ),
			'plural'    => __( 'Report', 'wc-customer-source' ),
			'ajax'      => false,
		) );
	}

	/**
	 * No items found text.
	 */
	public function no_items() {
		_e( 'No orders found.', 'wc-customer-source' );
	}

	/**
	 * Don't need this.
	 *
	 * @param string $position
	 */
	public function display_tablenav( $position ) {

		if ( 'top' !== $position ) {
			parent::display_tablenav( $position );
		}
	}

	/**
	 * Output the report.
	 */
	public function output_report() {

		$this->prepare_items();
		echo '<div id="poststuff" class="woocommerce-reports-wide">';
		$this->display();
		echo '</div>';
	}

	/**
	 * Get column value.
	 *
	 * @param mixed $item
	 * @param string $column_name
	 */
	public function column_default( $item, $column_name ) {

		switch ( $column_name ) {

			case 'order_id' :
                echo '<strong>' . $item['order_id'] . '</strong>';
			break;

            case 'wccs_status' :
                    echo $item['wccs_status'];
    		break;

            case 'customer_name' :
                    echo $item['customer_name'];
    		break;

            case 'customer_source' :
                    echo $item['customer_source'];
    		break;

            case 'actions' :
                    echo '<a class="button" href="' . get_edit_post_link( $item['order_id'] ) . '">View Order</a>';
    		break;
		}
	}

	/**
	 * Get columns.
	 *
	 * @return array
	 */
	public function get_columns() {

		$columns = array(
			'order_id'      => __( 'Order ID', 'wc-customer-source' ),
            'wccs_status'  => __( 'Order Status', 'wc-customer-source' ),
			'customer_name'       => __( 'Customer Name', 'wc-customer-source' ),
			'customer_source'  => __( 'Customer Source', 'wc-customer-source' ),
			'actions' => __( 'Actions', 'wc-customer-source' ),
		);

		return $columns;
	}

	/**
	 * Prepare customer list items.
	 */
	public function prepare_items() {

		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
		$current_page          = absint( $this->get_pagenum() );
		$per_page              = apply_filters( 'wc_customer_source_report_per_page', 10 );

		$this->get_items( $current_page, $per_page );
	}

    /**
	 * Get orders matching stock criteria.
	 *
	 * @param int $current_page
	 * @param int $per_page
	 */
	public function get_items( $current_page, $per_page ) {

        $settings = get_option( 'wc_customer_source_settings' );
        $items = array();

        $args = array(
            'post_type' => 'shop_order',
            'post_status'   => $settings['report_orders_statuses'],
            'posts_per_page' => $per_page,
            'paged' => $current_page
        );

        if ( $settings['report_orders_displayed'] ) {
            $args['meta_key'] = 'wc_customer_source_checkout_field';
        }

        $query = new WP_Query( $args );

        while( $query->have_posts() ) : $query->the_post();

            $order = new WC_Order( get_the_ID() );

            $customer_source = get_post_meta( get_the_ID(), 'wc_customer_source_checkout_field', true );
            $other  = get_post_meta( $order->id, 'wc_customer_source_checkout_other_field', true );

            $customer_source .= ( $customer_source == 'other' && $other ) ? ' - ' . $other : '';

            $items[] = array(
                'order_id'  =>  get_the_ID(),
                'wccs_status'  =>  $order->get_status(),
                'customer_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
                'customer_source' => $customer_source,
            );
        endwhile;

        $this->items = $items;

        /**
		 * Pagination.
		 */
		$this->set_pagination_args( array(
			'total_items' => $query->found_posts,
			'per_page'    => $per_page,
			'total_pages' => $query->max_num_pages,
		) );
    }
}
