<?php
/**
 * Refer a friend Data Screen
 * @author WPGens
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(!class_exists('WP_List_Table')){
   require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class RAF_List_Table extends WP_List_Table {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {
		parent::__construct( array(
			'singular'=> 'referral',
			'plural' => 'referrals',
			'ajax'   => false
		) );
    }

    function extra_tablenav( $which ) {
	   if ( $which == "top" ){
	      //The code that goes before the table is here
//	      echo"Hello, I'm before the table";
	   }
	   if ( $which == "bottom" ){
	      //The code that goes after the table is there
//	      echo"Hi, I'm after the table";
	   }
	}

	function get_columns() {
	   return $columns= array(
	   		'status' => __('Status','gens-raf'),
			'ID'=>__('RAF Order'),
			'referrer'=>__('Referred by:','gens-raf'),
			'date'=>__('Created','gens-raf'),
			'total' => __('Order amount','gens-raf')
	   );
	}

	/**
	 * Prepare the table with different parameters, pagination, columns and table elements
	 */
	function get_customers() {
		$args = array(
		    'meta_query'  => array(
		    	array(
		    		'key' => '_raf_id',
		    		'compare' => 'EXISTS',
		    	)
		    ),
		    'post_type'   => wc_get_order_types(),
		    'post_status' => array_keys( wc_get_order_statuses() ),
		    'posts_per_page' => 99999
		);

		$orders = new WP_Query($args);
	    return $orders->get_posts();
	}

	public function no_items() {
	    _e( 'No Referrals found yet.', 'gens-raf' );
	}

	function column_default($item, $column_name){
		$order = new WC_Order($item->ID);

		switch ( $column_name ) {
			case 'status':
				return $order->get_status();
				break;
			case 'ID':
				$return = "<a href='".get_edit_post_link( $item->ID )."'>#".$item->ID."</a> by ";
				$user = $order->get_user();
				if ( $order->get_user_id() ) {
					$return .= "<a href='".get_edit_user_link( $user->ID )."'>".$user->display_name."</a><br/>";
					// $return .= "<a href='mailto:".$user->display_name."'>".$user->display_name."</a>";
				} else {
					$return .= "Guest";
				}
				return $return;
				break; 
			case 'referrer':
				if(method_exists($order, "get_id")) {
					$referralID = get_post_meta( $order->get_id(), '_raf_id', true );					
				} else {
					$referralID = get_post_meta( $order->id, '_raf_id', true );
				}
				if (!empty($referralID)) {
					$args = array('meta_key' => "gens_referral_id", 'meta_value' => $referralID );
					$user = get_users($args);
					return "<a href='".get_edit_user_link( $user[0]->ID )."'>".$user[0]->display_name."</a>";					
				}
				break; 
			case 'date':
				$order_date = ( version_compare( WC_VERSION, '2.7', '<' ) ) ? $order->order_date : date_i18n(wc_date_format(), strtotime($order->get_date_created()));  
				return $order_date;
			case 'total':
				return $order->get_formatted_order_total();
			default:
				print_r($column_name);
		}

	}

	function prepare_items() {

		$posts = $this->get_customers();
		// Lets filter here instead with meta query which is super slow.
		$filtered_posts = array();
		foreach ($posts as $single_post) {
			$meta = get_post_meta($single_post->ID,'_raf_meta',true);
			if($meta) {
				if((isset($meta["publish"]) && $meta["publish"] == "true") || (isset($meta["generate"]) && $meta["generate"] == "true")) {
					array_push($filtered_posts, $single_post);
				}
			} else {
				array_push($filtered_posts, $single_post);
			}
		}

		$per_page = 10;
		$current_page = $this->get_pagenum();
		$total_items = count($filtered_posts);

	    $columns = $this->get_columns();
	    $hidden = array();
	    $sortable = $this->get_sortable_columns();
	    $this->_column_headers = array($columns, $hidden, $sortable);
	    $this->process_bulk_action();
		
		$filtered_posts = array_slice($filtered_posts,(($current_page-1)*$per_page),$per_page);

	    $this->set_pagination_args( array (
	        'total_items' => $total_items,
	        'per_page'    => $per_page,
	        'total_pages' => ceil( $total_items / $per_page )
	    ) );

	    $this->items = $filtered_posts;
	}

}
