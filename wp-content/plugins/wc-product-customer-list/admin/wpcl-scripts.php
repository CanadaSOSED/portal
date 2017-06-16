<?php
/**
 * @package WC_Product_Customer_List
 * @version 2.4.4
 */

if( ! function_exists('wpcl_enqueue_scripts') ) {
	function wpcl_enqueue_scripts($hook) {
		global $post;
  		if ( 'post.php' != $hook || 'product' != get_post_type( $post ) ) {
			return;
		}
		wp_register_style( 'wpcl-admin-css', plugin_dir_url( __FILE__ ) . 'assets/admin.css', false, '2.3.1' );
		wp_register_style( 'datatables-css', 'https://cdn.datatables.net/t/dt/dt-1.10.11,r-2.0.2/datatables.min.css', false, '1.10.11' );
		wp_register_style( 'datatables-buttons-css', 'https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css', false, '1.2.2' );

		wp_register_script( 'datatables-js', 'https://cdn.datatables.net/t/dt/dt-1.10.11,r-2.0.2/datatables.min.js', true, '2.0.2' );
		wp_register_script( 'datatables-buttons-js', 'https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js', true, '1.2.2' );
		wp_register_script( 'datatables-buttons-flash', 'https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js', true, '1.2.2' );
		wp_register_script( 'datatables-print', 'https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js', true, '1.2.2' );
		wp_register_script( 'datatables-jszip', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js', true, '2.5.0' );
		wp_register_script( 'datatables-pdfmake', plugin_dir_url( __FILE__ ) . 'assets/pdfmake/pdfmake.min.js', true, '0.1.20' );
		wp_register_script( 'datatables-vfs-fonts', 'https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.20/vfs_fonts.js', true, '0.1.20' );
		wp_register_script( 'datatables-buttons-html', 'https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js', true, '1.2.2' );
		wp_register_script( 'datatables-buttons-print', 'https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js', true, '1.2.2' );
		wp_register_script( 'datatables-colreorder', 'https://cdn.datatables.net/colreorder/1.3.2/js/dataTables.colReorder.min.js', true, '1.3.2' );
		wp_register_script( 'wpcl-script', plugin_dir_url( __FILE__ ) . 'assets/admin.js', true, '2.3.6' );

		wp_enqueue_style( 'wpcl-admin-css' );
		wp_enqueue_style( 'datatables-css' );
		wp_enqueue_style( 'datatables-buttons-css' );

		wp_enqueue_script( 'datatables-js');
		wp_enqueue_script( 'datatables-buttons-js');
		wp_enqueue_script( 'datatables-buttons-flash');
		wp_enqueue_script( 'datatables-print');
		wp_enqueue_script( 'datatables-jszip');
		wp_enqueue_script( 'datatables-pdfmake');
		wp_enqueue_script( 'datatables-vfs-fonts');
		wp_enqueue_script( 'datatables-buttons-html');
		wp_enqueue_script( 'datatables-buttons-print');
		wp_enqueue_script( 'datatables-colreorder');
		wp_enqueue_script( 'wpcl-script');

		wp_localize_script('wpcl-script', 'wpcl_script_vars', array(
			'copybtn'				=> __('Copy', 'wc-product-customer-list'),
			'printbtn'				=> __('Print', 'wc-product-customer-list'),
			'search'				=> __('Search', 'wc-product-customer-list'),
			'emptyTable'			=> __('This product currently has no customers', 'wc-product-customer-list'),
			'zeroRecords'			=> __('No orders match your search', 'wc-product-customer-list'),
			'tableinfo'				=> __('Showing _START_ to _END_ out of _TOTAL_ orders', 'wc-product-customer-list'),
			'lengthMenu'			=> __('Show _MENU_ orders', 'wc-product-customer-list'),
			'copyTitle'				=> __('Copy to clipboard', 'wc-product-customer-list'),
			'copySuccessMultiple'	=> __('Copied %d rows', 'wc-product-customer-list'),
			'copySuccessSingle'		=> __('Copied 1 row', 'wc-product-customer-list'),
			'paginateFirst'			=> __('First', 'wc-product-customer-list'),
			'paginatePrevious'		=> __('Previous', 'wc-product-customer-list'),
			'paginateNext'			=> __('Next', 'wc-product-customer-list'),
			'paginateLast'			=> __('Last', 'wc-product-customer-list'),
			'productTitle'			=> get_the_title(),
			'pdfPagesize'			=> get_option( 'wpcl_export_pdf_pagesize', 'LETTER' ),
			'pdfOrientation'		=> get_option( 'wpcl_export_pdf_orientation', 'portrait' )
		));
	}
	add_action( 'admin_enqueue_scripts', 'wpcl_enqueue_scripts' );
}