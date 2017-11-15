<?php
/**
 * WooCommerce Customer/Order CSV Export
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Customer/Order CSV Export to newer
 * versions in the future. If you wish to customize WooCommerce Customer/Order CSV Export for your
 * needs please refer to http://docs.woothemes.com/document/ordercustomer-csv-exporter/
 *
 * @package     WC-Customer-Order-CSV-Export/Handler
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2016, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Customer/Order CSV Export Handler
 *
 * Handles export actions/methods
 *
 * @since 3.0.0
 */
class WC_Customer_Order_CSV_Export_Handler {


	/** @var string $temp_filename temporary file path */
	protected $temp_filename;


	/**
	 * Initialize the Export Handler
	 *
	 * In 4.0.0 Removed constructor arguments, pass arguments to dedicated methods instead
	 *
	 * @since 3.0.0
	 * @return \WC_Customer_Order_CSV_Export_Handler
	 */
	public function __construct() {

		add_action( 'wc_customer_order_csv_export_unlink_temp_file', array( $this, 'unlink_temp_file' ) );
	}


	/**
	 * Exports test file and uploads to remote server
	 *
	 * @since 3.0.0
	 * @param string $export_type Optional, the export type. defaults to `orders`
	 */
	public function test_upload( $export_type = 'orders' ) {

		$this->test_export_via( 'ftp', $export_type );
	}


	/**
	 * Exports test and HTTP POSTs to remote server
	 *
	 * @since 3.0.0
	 * @param string $export_type Optional, the export type. defaults to `orders`
	 */
	public function test_http_post( $export_type = 'orders' ) {

		$this->test_export_via( 'http_post', $export_type );
	}


	/**
	 * Exports test file and emails admin with the file as attachment
	 *
	 * @since 3.0.0
	 * @param string $export_type Optional, the export type. defaults to `orders`
	 */
	public function test_email( $export_type = 'orders' ) {

		$this->test_export_via( 'email', $export_type );
	}


	/**
	 * Exports a test file via the given method
	 *
	 * @since 3.0.0
	 * @throws SV_WC_Plugin_Exception
	 * @param string $method the export method
	 * @param string $export_type Optional, the export type. defaults to `orders`
	 * @return array with 2 elements - success/error message, and message type
	 */
	public function test_export_via( $method, $export_type = 'orders' ) {

		// try to set unlimited script timeout
		@set_time_limit( 0 );

		try {

			// get method (download, FTP, etc)
			$export = wc_customer_order_csv_export()->get_methods_instance()->get_export_method( $method, $export_type );

			if ( ! is_object( $export ) ) {

				/** translators: %s - export method identifier */
				throw new SV_WC_Plugin_Exception( sprintf( __( 'Invalid Export Method: %s', 'woocommerce-customer-order-csv-export' ), $method ) );
			}

			// create a temp file with the test data
			$temp_file = $this->create_temp_file( $this->get_test_filename(), $this->get_test_data() );

			// simple test file
			if ( $export->perform_action( $temp_file ) ) {
				return array( __( 'Test was successful!', 'woocommerce-customer-order-csv-export' ), 'success' );
			} else {
				return array( __( 'Test failed!', 'woocommerce-customer-order-csv-export' ), 'error' );
			}

		} catch ( SV_WC_Plugin_Exception $e ) {

			// log errors
			wc_customer_order_csv_export()->log( $e->getMessage() );

			/** translators: %s - error message */
			return array( sprintf( __( 'Test failed: %s', 'woocommerce-customer-order-csv-export' ), $e->getMessage() ), 'error' );
		}
	}


	/**
	 * Create a temp file that is automatically removed on shutdown or after a given delay
	 *
	 * @since 4.0.0
	 * @param string $filename the filename
	 * @param string $source path to source file or the data to write to the file
	 * @param string $temp_path path to dir to place the temp file in. Defaults to the WP temp dir.
	 * @param bool|int $delay_remove Delay temp file removal by amount of seconds.
	 *                               If boolean true, this will default to 60 seconds.
	 *                               Normally, temp files are removed once the script
	 *                               exists, but with this param set to true, it's possible
	 *                               to keep it for a short while. This helps
	 *                               in cases when the file has to be accessible briefly
	 *                               after the script exists (such as for redirect downloads ).
	 * @return string $filename path to the temp file
	 */
	public function create_temp_file( $filename, $source, $temp_path = '', $delay_remove = false ) {

		$temp_path = $temp_path && is_readable( $temp_path ) ? $temp_path : get_temp_dir();

		// prepend the temp directory to filename
		$filename = untrailingslashit( $temp_path ) . '/' . $filename;

		// source is a path to existing file
		if ( is_readable( $source ) ) {

			copy( $source, $filename  );
		}

		// if not a readable source file, it's most likely just raw data
		else {

			// create the file
			touch( $filename );

			// open the file, write file, and close it
			$fp = @fopen( $filename, 'w+');

			@fwrite( $fp, $source );
			@fclose( $fp );
		}

		// make sure the temp file is removed afterwards

		// delay the remove for the given period
		if ( $delay_remove ) {

			if ( ! is_int( $delay_remove ) ) {
				$delay_remove = 60; // default to 60 seconds
			}

			wp_schedule_single_event( time() + $delay_remove, 'wc_customer_order_csv_export_unlink_temp_file', array( $filename ) );
		}

		// ...or simply remove on shutdown
		else {
			$this->temp_filename = $filename;
			register_shutdown_function( array( $this, 'unlink_temp_file' ) );
		}

		return $filename;
	}


	/**
	 * Unlink temp file
	 *
	 * @since 4.0.0
	 * @param string $file_path Optional. If not provided, will look for file path
	 *                          on $this->temp_filename;
	 */
	public function unlink_temp_file( $file_path = '' ) {

		if ( ! $file_path ) {
			$file_path = $this->temp_filename;
		}

		if ( $file_path ) {
			@unlink( $file_path );
		}
	}


	/**
	 * Marks orders as exported by setting the `_wc_customer_order_csv_export_is_exported` order meta flag
	 *
	 * In 4.0.0 added $ids param as the first param
	 *
	 * @since 3.0.0
	 * @param array $ids
	 * @param string $method the export method, `download`, `ftp`, `http_post`, or `email`
	 */
	public function mark_orders_as_exported( $ids, $method = 'download' ) {

		if ( empty( $ids ) ) {
			return;
		}

		foreach ( $ids as $order_id ) {

			$order      = wc_get_order( $order_id );
			$order_note = null;

			// only add order notes if the option is turned on and order has not already been exported
			$add_order_note = ( 'yes' === get_option( 'wc_customer_order_csv_export_orders_add_note' ) ) && ! get_post_meta( $order_id, '_wc_customer_order_csv_export_is_exported', true );

			/**
			 * Filter if an order note should be added when an order is successfully exported
			 *
			 * @since 3.9.1
			 * @param bool $add_order_note true if the order note should be added, false otherwise
			 */
			if ( apply_filters( 'wc_customer_order_csv_export_add_order_note', $add_order_note ) ) {

				switch ( $method ) {

					// note that order downloads using the AJAX order action are not marked or noted, only bulk order downloads
					case 'download':
						$order_note = esc_html__( 'Order exported to CSV and successfully downloaded.', 'woocommerce-customer-order-csv-export' );
					break;

					case 'ftp':
						$order_note = esc_html__( 'Order exported to CSV and successfully uploaded to server.', 'woocommerce-customer-order-csv-export' );
					break;

					case 'http_post':
						$order_note = esc_html__( 'Order exported to CSV and successfully POSTed to remote server.', 'woocommerce-customer-order-csv-export' );
					break;

					case 'email':
						$order_note = esc_html__( 'Order exported to CSV and successfully emailed.', 'woocommerce-customer-order-csv-export' );
					break;

					default:
						$order_note = esc_html__( 'Order exported to CSV.', 'woocommerce-customer-order-csv-export' );
					break;
				}

				$order->add_order_note( $order_note );
			}

			// add exported flag
			update_post_meta( $order_id, '_wc_customer_order_csv_export_is_exported', 1 );

			/**
			 * Order Exported Action.
			 *
			 * Fired when an order is exported.
			 *
			 * @since 3.1.0
			 * @param WC_Order $order order being exported
			 * @param string $method how the order is exported (ftp, download, etc)
			 * @param string|null $order_note order note message, null if no note was added
			 * @param \WC_Customer_Order_CSV_Export_Handler $this, handler instance
			 */
			do_action( 'wc_customer_order_csv_export_order_exported', $order, $method, $order_note, $this );
		}
	}


	/**
	 * Marks customers as exported by setting the `_wc_customer_order_csv_export_is_exported`
	 * user meta flag on users, and `_wc_customer_order_csv_export_customer_is_exported` on
	 * orders for guest customers.
	 *
	 * @since 4.0.0
	 * @param array $ids customer IDs to to mark as exported. also accepts an array of arrays with billing email and
	 *                   order Ids, for guest customers: array( $user_id, array( $billing_email, $order_id ) )
	 * @param string $method the export method, `download`, `ftp`, `http_post`, or `email`
	 */
	public function mark_customers_as_exported( $ids, $method = 'download' ) {

		foreach ( $ids as $customer_id ) {

			$order_id = $email = $user = null;

			if ( is_array( $customer_id ) ) {

				list( $email, $order_id ) = $customer_id;

				update_post_meta( $order_id, '_wc_customer_order_csv_export_customer_is_exported', 1 );

			} else {

				$user = is_numeric( $customer_id ) ? get_user_by( 'id', $customer_id ) : get_user_by( 'email', $customer_id );

				if ( $user ) {

					$email = $user->user_email;

					update_user_meta( $user->ID, '_wc_customer_order_csv_export_is_exported', 1 );
				}
			}

			/**
			 * Customer Exported Action.
			 *
			 * Fired when a customer is exported.
			 *
			 * @since 4.0.0
			 * @param string $email customer email being exported
			 * @param int|null $user_id customer user ID being exported, null if guest customer
			 * @param int|null $order_id related order ID, used for guest customers, may be null if no related order
			 * @param string $method how the order is exported (ftp, download, etc)
			 * @param \WC_Customer_Order_CSV_Export_Handler $this, handler instance
			 */
			do_action( 'wc_customer_order_csv_export_customer_exported', $email, ( $user ? $user->ID : null ), $order_id, $method, $this );
		}
	}


	/**
	 * Replaces variables in file name setting (e.g. %%timestamp%% becomes 2013_03_20_16_22_14 )
	 *
	 * In 4.0.0 added $ids and $export_type params
	 *
	 * @since 3.0.0
	 * @param array $ids
	 * @param string $export_type
	 * @return string filename with variables replaced
	 */
	protected function replace_filename_variables( $ids, $export_type ) {

		$pre_replace_filename = get_option( 'wc_customer_order_csv_export_' . $export_type . '_filename' );

		$variables = array(
			'%%timestamp%%' => date( 'Y_m_d_H_i_s', current_time( 'timestamp' ) ),
		);

		if ( 'orders' === $export_type ) {
			$variables['%%order_ids%%'] = implode( '-', $ids );
		}

		/**
		 * Allow actors to adjust filename merge vars and their replacements
		 *
		 * @since 4.0.0
		 * @param array $variables associative array of variables and their replacement values
		 * @param array $ids
		 * @param string $export_type
		 */
		$variables = apply_filters( 'wc_customer_order_csv_export_filename_variables', $variables, $ids, $export_type );

		$post_replace_filename = ! empty( $variables ) ? str_replace( array_keys( $variables ), array_values( $variables ), $pre_replace_filename ) : $pre_replace_filename;

		/**
		 * Filter exported file name
		 *
		 * @since 3.0.0
		 * @param string $filename Filename after replacing variables
		 * @param string $pre_replace_filename Filename before replacing variables
		 * @param array $ids Array of entity (customer or order) IDs being exported
		 */
		return apply_filters( 'wc_customer_order_csv_export_filename', $post_replace_filename, $pre_replace_filename, $ids );
	}


	/**
	 * Get background export handler instance
	 *
	 * Shortcut method for convenience
	 *
	 * @since 4.0.0
	 * @return \WC_Customer_Order_CSV_Export_Background_Export instance
	 */
	private function get_background_export_handler() {
		return wc_customer_order_csv_export()->get_background_export_instance();
	}


	/**
	 * Get an export by its ID
	 *
	 * A simple wrapper around SV_WP_Background_Job_Handler::get_job(), for convenience
	 *
	 * @since 4.0.0
	 * @see SV_WP_Background_Job_Handler::get_job()
	 * @param string $id
	 * @return object|null
	 */
	public function get_export( $id ) {

		return $this->get_background_export_handler()->get_job( $id );
	}


	/**
	 * Get an array of exports
	 *
	 * A simple wrapper around SV_WP_Background_Job_Handler::get_jobs(), for convenience
	 *
	 * @since 4.0.0
	 * @see SV_WP_Background_Job_Handler::get_jobs()
	 * @param array $args Optional. An array of arguments passed to SV_WP_Background_Job_Handler::get_jobs()
	 * @return array Found export objects
	 */
	public function get_exports( $args = array() ) {

		return wc_customer_order_csv_export()->get_background_export_instance()->get_jobs( $args );
	}


	/**
	 * Transfer an export via the given method (FTP, etc)
	 *
	 * @since 4.0.0
	 * @throws SV_WC_Plugin_Exception
	 * @param string|object $export_id Export (job) instance or ID
	 * @param string $export_method Export method, will default to export's own if not provided
	 * @return mixed
	 */
	public function transfer_export( $export_id, $export_method = null ) {

		$export        = is_object( $export_id ) ? $export_id : $this->get_export( $export_id );
		$export_method = $export_method ? $export_method : $export->method;

		if ( ! $export ) {
			/* translators: Placeholders: %s - export ID */
			throw new SV_WC_Plugin_Exception( sprintf( esc_html__( 'Could not find export: %s', 'woocommerce-customer-order-csv-export' ), $export_id ) );
		}

		$_export_method = wc_customer_order_csv_export()->get_methods_instance()->get_export_method( $export_method, $export->type, (array) $export );

		if ( ! is_object( $_export_method ) ) {
			/* translators: Placeholders: %s - export method */
			throw new SV_WC_Plugin_Exception( sprintf( esc_html__( 'Invalid Export Method: %s', 'woocommerce-customer-order-csv-export' ), $export_method ) );
		}

		$filename = basename( $export->file_path );

		// strip random part from filename
		$filename = substr( $filename, strpos( $filename, '-' ) + 1 );

		// create a temp file with the random part stripped from the filename, so
		// that the filename appears without the random part in the destination (email, ftp, etc)
		$temp_file_path = $this->create_temp_file( $filename, $export->file_path );

		// indicate that the transfer has started
		$export->transfer_status = 'processing';

		$this->update_export( $export );

		// perform the transfer action
		try {

			$_export_method->perform_action( $temp_file_path );

			$export->transfer_status = 'completed';

			$this->update_export( $export );

			// Mark orders/customers as exported
			if ( 'orders' === $export->type ) {
				$this->mark_orders_as_exported( $export->object_ids, $export->method );
			} elseif ( 'customers' === $export->type ) {
				$this->mark_customers_as_exported( $export->object_ids, $export->method );
			}

		} catch ( SV_WC_Plugin_Exception $e ) {

			$export->transfer_status = 'failed';

			$this->update_export( $export );

			throw $e;
		}
	}


	/**
	 * Return the export generator class instance
	 *
	 * In 4.0.3 added $ids param
	 *
	 * @since 4.0.0
	 * @param string $export_type export type
	 * @param array $ids optional object ids to pass to generator for reference
	 * @return \WC_Customer_Order_CSV_Export_Generator
	 */
	protected function get_generator( $export_type, $ids = null ) {

		require_once( wc_customer_order_csv_export()->get_plugin_path() . '/includes/class-wc-customer-order-csv-export-generator.php' );

		return new WC_Customer_Order_CSV_Export_Generator( $export_type, $ids );
	}


	/**
	 * Return the filename for test export
	 *
	 * @since 4.0.0
	 * @return string
	 */
	protected function get_test_filename() {
		return 'test.csv';
	}


	/**
	 * Return the data (file contents) for test export
	 *
	 * @since 4.0.0
	 * @return string
	 */
	protected function get_test_data() {
		return "column_1,column_2,column_3\ntest_1,test_2,test_3";
	}


	/**
	 * Get exports direcotry path in local filesystem
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public function get_exports_dir() {

		$upload_dir = wp_upload_dir( null, false );
		return $upload_dir['basedir'] . '/csv_exports';
	}


	/**
	 * Get exports directory URL for downloads
	 *
	 * @since 4.0.0
	 * @return string
	 */
	public function get_exports_url() {

		$uploads_dir = wp_upload_dir( null, false );
		return $uploads_dir['baseurl'] . '/csv_exports';
	}


	/**
	 * Kick off an export
	 *
	 * @since 4.0.0
	 * @throws SV_WC_Plugin_Exception
	 * @param int|string|array $ids
	 * @param array $args {
	 *                 An array of arguments
	 *                 @type string $type Export type either `orders` or `customers`. Defaults to `orders`
	 *                 @type string $method Export transfer method, such as `email`, `ftp`, etc. Defaults to `download`
	 *                 @type string $invocation Export invocation type, used for informational purposes. One of `manual` or `auto`, defaults to `manual`
	 * }
	 * @return object|false Background export job or false on failure
	 */
	public function start_export( $ids, $args = array() ) {

		// make sure default args are set
		$args = wp_parse_args( $args, array(
			'type'       => 'orders',
			'method'     => 'download',
			'invocation' => 'manual',
		) );

		// handle single order/customer exports
		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		/**
		 * Allow actors to change the order/customer IDs to be exported
		 *
		 * In 4.0.0 removed $this param, added $args param, moved here from WC_Customer_Order_CSV_Export_Generator class
		 *
		 * @since 3.9.1
		 * @param array $ids Order/customer IDs to be exported.
		 * @param array $args Export args, see WC_Customer_Order_CSV_Export_Handler::start_export()
		 */
		$ids = apply_filters( 'wc_customer_order_csv_export_ids', $ids, $args );

		$exports_dir = $this->get_exports_dir();
		$filename    = $this->replace_filename_variables( $ids, $args['type'] );

		if ( ! $filename ) {
			throw new SV_WC_Plugin_Exception( esc_html__( "No filename given for export file, can't export", 'woocommerce-customer-order-csv-export' ) );
		}

		$file_path = $exports_dir . '/' . uniqid( null, true ) . '-' . $filename;
		$stream    = fopen( $file_path, 'w' );

		if ( false === $stream ) {
			/* translators: Placeholders: %s - file name */
			throw new SV_WC_Plugin_Exception( sprintf( esc_html__( 'Could not open the export file %s for writing', 'woocommerce-customer-order-csv-export' ), $file_path ) );
		}

		$background_export = $this->get_background_export_handler();
		$header            = $this->get_generator( $args['type'], $ids )->get_header();

		// write header if provided
		if ( null !== $header  ) {
			fputs( $stream, $header );
		}

		fclose( $stream );

		$job_attrs = array(
			'object_ids'      => $ids,
			'file_path'       => $file_path,
			'invocation'      => $args['invocation'],
			'type'            => $args['type'],
			'method'          => $args['method'],
			'transfer_status' => null,
		);

		$job = $background_export->create_job( $job_attrs );

		$background_export->dispatch();

		return $job;
	}


	/**
	 * Export a single item to the export's file path
	 *
	 * This method will normally only ever be called from the background export to
	 * export items one-by-one to a file.
	 *
	 * In 4.0.3 renamed from export_item_to_file to export_item, removed $file_path and
	 * $export_type params, added $export param
	 *
	 * @since 4.0.0
	 * @param mixed $item Item to export
	 * @param object $export Export (job) associated with the item
	 */
	public function export_item( $item, $export ) {

		if ( empty( $export->file_path ) ) {
			wc_customer_order_csv_export()->log( esc_html__( 'No export file path provided, cannot export item.', 'woocommerce-customer-order-csv-export' ) );
			return;
		}

		$stream = fopen( $export->file_path, 'a' );

		if ( false === $stream ) {

			/* translators: Placeholders: %s - file path */
			wc_customer_order_csv_export()->log( sprintf( esc_html__( 'Could not open the export file %s for appending.', 'woocommerce-customer-order-csv-export' ), $export->file_path ) );
			return;
		}

		$generator = $this->get_generator( $export->type, $export->object_ids );

		fputs( $stream, $generator->get_output( array( $item ) ) );

		# clear the WP cache, as per https://github.com/woothemes/woocommerce/issues/7310#issuecomment-100980589
		wp_cache_flush();

		fclose( $stream );
	}


	/**
	 * Update an export instance
	 *
	 * @since 4.0.0
	 * @param object $export
	 */
	private function update_export( $export ) {

		$this->get_background_export_handler()->update_job( $export );
	}


	/**
	 * Finish off an export
	 *
	 * @since 4.0.0
	 * @param object|string $export Export object or ID
	 */
	public function finish_export( $export ) {

		if ( is_string( $export ) ) {
			$export = $this->get_export( $export );
		}

		if ( ! $export ) {
			return;
		}

		if ( 'auto' === $export->invocation ) {

			/**
			 * Auto-Export Action.
			 *
			 * Fired when orders or customers are auto-exported.
			 * Moved from WC_Customer_Order_CSV_Export_Cron class in 4.0.0.
			 *
			 * @since 3.0.0
			 * @param array $order_ids order IDs that were exported
			 */
			do_action( 'wc_customer_order_csv_export_' . $export->type . '_exported', $export->object_ids );
		}

		// TODO: remove the following deprecated filter in next major release {IT 2016-06-21}

		// filter the final generated CSV
		$generated_csv = file_get_contents( $export->file_path );

		/**
		 * Allow actors to change the generated CSV, such as removing headers.
		 *
		 * @since 4.0.0 moved here from WC_Customer_Order_CSV_Export_Generator class
		 *
		 * @since 3.0.6
		 * @param string $csv - generated CSV file
		 * @param \WC_Customer_Order_CSV_Export_Handler $this - generator class instance
		 */
		$csv = apply_filters( 'wc_customer_order_csv_export_generated_csv', $generated_csv, $this );

		file_put_contents( $export->file_path, $csv );

		if ( 'download' === $export->method || 'local' === $export->method ) {

			// Mark orders/customers as exported
			if ( 'orders' === $export->type ) {
				$this->mark_orders_as_exported( $export->object_ids, $export->method );
			} elseif ( 'customers' === $export->type ) {
				$this->mark_customers_as_exported( $export->object_ids, $export->method );
			}

		} else {

			try {

				// transfer file via the provided export method
				$this->transfer_export( $export );

			} catch ( SV_WC_Plugin_Exception $e ) {

				// log errors
				/* translators: Placeholders: %s - error message */
				wc_customer_order_csv_export()->log( sprintf( __( 'Failed to transfer exported file: %s', 'woocommerce-customer-order-csv-export' ), $e->getMessage() ) );
			}
		}

		// Notify the user that the export is complete
		$this->add_export_finished_notice( $export );
	}


	/**
	 * Handle a failed export
	 *
	 * @since 4.0.0
	 * @param object|string $export Export object or ID
	 */
	public function failed_export( $export ) {

		if ( is_string( $export ) ) {
			$export = $this->get_export( $export );
		}

		if ( ! $export ) {
			return;
		}

		$this->add_export_finished_notice( $export );
	}


	/**
	 * Add export finished notice for a user
	 *
	 * @since 4.0.0
	 * @param object|string $export Export object or ID
	 */
	public function add_export_finished_notice( $export ) {

		if ( is_string( $export ) ) {
			$export = $this->get_export( $export );
		}

		// don't notify if no export found
		if ( ! $export ) {
			return;
		}

		$filename = basename( $export->file_path );

		// Notify the user that the manual export failed
		if ( 'manual' === $export->invocation ) {

			$message_id = 'wc_customer_order_csv_export_finished_' . $export->id;

			// add notice for manually created exports
			if ( $export->created_by && ! wc_customer_order_csv_export()->get_admin_notice_handler()->is_notice_dismissed( $message_id, $export->created_by ) ) {

				$export_notices = get_user_meta( $export->created_by, '_wc_customer_order_csv_export_notices', true );

				if ( ! $export_notices ) {
					$export_notices = array();
				}

				$export_notices[] = $export->id;

				update_user_meta( $export->created_by, '_wc_customer_order_csv_export_notices', $export_notices );
			}

		}

		$successful = 'completed' === $export->status && ( ! $export->transfer_status || 'completed' === $export->transfer_status );

		// Notify admins that automatic exports are failing
		if ( 'auto' === $export->invocation && ! $successful ) {

			$failure_type      = 'failed' === $export->status ? 'export' : 'transfer';
			$failure_notices   = get_option( 'wc_customer_order_csv_export_failure_notices', array() );
			$multiple_failures = ! empty( $failure_notices ) && ! empty( $failure_notices[ $failure_type ] );

			$failure_notices[ $failure_type ] = array(
				'export_id'         => $export->id,
				'multiple_failures' => $multiple_failures,
			);

			update_option( 'wc_customer_order_csv_export_failure_notices', $failure_notices );
		}

	}


	/**
	 * Remove export fnished notice from user meta
	 *
	 * @since 4.0.0
	 * @param object|string $export Export object or ID
	 * @param int $user_id
	 */
	public function remove_export_finished_notice( $export, $user_id ) {

		$export_id = is_string( $export ) ? $export : ( is_object( $export ) ? $export->id : null );

		if ( ! $export_id || ! $user_id ) {
			return;
		}

		$export_notices = get_user_meta( $user_id, '_wc_customer_order_csv_export_notices', true );

		if ( ! empty( $export_notices ) && in_array( $export_id, $export_notices, true ) ) {

			unset( $export_notices[ array_search( $export_id, $export_notices ) ] );

			update_user_meta( $user_id, '_wc_customer_order_csv_export_notices', $export_notices );
		}

		// also remove the message from user dismissed notices
		$dismissed_notices = wc_customer_order_csv_export()->get_admin_notice_handler()->get_dismissed_notices( $user_id );
		$message_id        = 'wc_customer_order_csv_export_finished_' . $export_id;

		if ( ! empty( $dismissed_notices ) && isset( $dismissed_notices[ $message_id ] ) ) {
			unset( $dismissed_notices[ $message_id ] );

			update_user_meta( $user_id, '_wc_plugin_framework_customer_order_csv_export_dismissed_messages', $dismissed_notices );
		}
	}


	/**
	 * Remove expired exports
	 *
	 * Deletes exports completed/failed more than 14 days ago
	 *
	 * @since 4.0.0
	 */
	public function remove_expired_exports() {

		$args = array(
			'status' => array( 'completed', 'failed' ),
		);

		// get all completed or failed jobs
		$all_jobs = $this->get_exports( $args );

		if ( empty( $all_jobs ) ) {
			return;
		}

		// loop over the jobs and find those that should be removed
		foreach ( $all_jobs as $job ) {

			$date = 'completed' === $job->status ? $job->completed_at : $job->failed_at;

			// job completed/failed at least 14 days ago, remove it (along with the file)
			if ( strtotime( $date ) <= strtotime( '14 days ago' ) ) {
				$this->get_background_export_handler()->delete_job( $job );
			}
		}
	}

}
