<?php
/**
 * Setup Menu Pages
 * @author    WPGens
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPGENS_RAF_Events {

	public function __construct() {
		// Add submenu items
		add_action( 'admin_menu', array( $this, 'register_events_menu') );
		add_filter( 'set-screen-option', array( $this, 'gens_raf_save_screen_options'), 10, 3);
	}


	/**
	 * Define submenu page under Woocommerce Page
	 *
	 * @since 2.0.0
	 */
	public function register_events_menu() {
		$raf_events_menu = add_submenu_page( 'woocommerce', __('Refers Stats', 'gens-raf'), __('Refer a Friend Events', 'gens-raf'), 'manage_woocommerce', 'gens-raf-events', array($this, 'display_raf_events_page'));
        add_action("load-$raf_events_menu", array($this,"add_events_screen_option"));
	}


	public function add_events_screen_option() {

		$screen = get_current_screen();
		// get out of here if we are not on our settings page
		if(!is_object($screen) || $screen->id !== "woocommerce_page_gens-raf-events")
			return;
	 
		$args = array(
			'label' => __('Actions per page', 'gens-raf'),
			'default' => 20,
			'option' => 'gens_raf_events_per_page'
		);
		add_screen_option( 'per_page', $args );
    }
    
	public function add_screen_option() {

		$screen = get_current_screen();
		// get out of here if we are not on our settings page
		if(!is_object($screen) || $screen->id !== "woocommerce_page_gens-raf")
			return;
	 
		$args = array(
			'label' => __('Referrals per page', 'gens-raf'),
			'default' => 20,
			'option' => 'gens_raf_posts_per_page'
		);
		add_screen_option( 'per_page', $args );
	}

	public function gens_raf_save_screen_options($status, $option, $value) {
        if ( 'gens_raf_events_per_page' == $option ) return $value;
	}


	/**
	 * Init the logs part.
	 *
	 * @since 2.0.0
	 */
	public function display_raf_events_page() {
        
        // Per page from screen option:
		$user = get_current_user_id();
		$screen = get_current_screen();
		$screen_option = $screen->get_option('per_page', 'option');
        if($screen_option) {
            $per_page = get_user_meta($user, $screen_option, true);            
        }
        $curpage = isset( $_GET['lpage'] ) ? abs( (int) $_GET['lpage'] ) : 1;
		if ( empty ( $per_page) || $per_page < 1 ) {
			$per_page = 20;
        }
        

        $db = new WPGens_RAF_DB();
        $logs = $db->get_by_page($curpage, $per_page);

        // Pagination
        $total_records = $db->total_records();
        $endpage = ceil($total_records/$per_page);
        $startpage = 1;
        $nextpage = $curpage + 1;
        $previouspage = $curpage - 1;
        
        $formattedLogs = [];
        foreach($logs as $key => $log) {
            $formattedLogs[$key]['type'] = $log['type'];
            $formattedLogs[$key]['type_name'] = $this->get_type_name($log['type']);
            $formattedLogs[$key]['date'] = date('d F Y, h:i:s A', strtotime($log['time'])); 
            $formattedLogs[$key]['info'] = $this->formatEvent($log['type'], $log['id'], $db);
        }
        
		include( WPGENS_RAF_ABSPATH . 'includes/admin/views/html-raf-logs.php' );
    }

    public function get_type_name($type) {
        switch($type) {
            case 'coupon_applied':
                return "Coupon Applied";
            case 'email_share':
                return "Email Share";
            case 'subscription_renewal':
                return "Subscription Renewal";
            case 'new_order':
                return 'New Order';
            case 'new_coupon':
                return "New Coupon Generated";
            case 'email_sent':
                return "Email Sent";
            case 'social_share':
                return "Social Share";
        }
    }


	/**
	 * Format Logs meta
	 *
	 * @since 2.0.0
	 */

    public function formatEvent($type, $id, $db){
        switch($type){
            case 'coupon_applied':
                $referral = $db->get_meta($id,'referral');
                if(filter_var($referral, FILTER_VALIDATE_EMAIL)) {
                    $referred_person = 'guest <a href="mailto:'.$referral.'">'.$referral.'</a>.';
                } else {
                    $user_info        = get_userdata($referral);
                    $user_name = $user_info->user_email;
                    $referred_person = '<a href="'.get_edit_user_link($referral).'">'.$user_name.'</a>.';
                }

                return 'Guest coupon successfully applied for a user that was referred by ' .$referred_person;
            case 'email_share':
                $user = $db->get_meta($id,'user');
                $email = $db->get_meta($id,'email');
                $name = $db->get_meta($id,'name') ? $db->get_meta($id,'name') : 'No name specified';
                $user_info = get_userdata($user);
                $user_name = $user_info ? $user_info->first_name.' '.$user_info->last_name : '';
                return 'Sharing via email by <a href="'.get_edit_user_link($user).'">'.$user_name.'</a> to: ('.$name.') '.$email.'.';
            case 'subscription_renewal':
                $user = $db->get_meta($id,'user');
                $order = $db->get_meta($id,'order');
                return 'Applied coupons to automatic subscription renewal order <a href="'.get_edit_post_link($order).'">#'.$order.'</a>.';
            case 'new_order':
                $user = $db->get_meta($id,'user');
                $referral = $db->get_meta($id,'referral');
                $order = $db->get_meta($id,'order');
                $info = $db->get_meta($id,'info');
                $user_info = get_userdata($referral);
                $user_name = $user_info ? $user_info->first_name.' '.$user_info->last_name : 'Guest';
                $person = $user_name === 'Guest' ? 'Guest' : '<a href="'.get_edit_user_link($referral).'">'.$user_name.'</a>';
                return 'New referral <a href="'.get_edit_post_link($order).'">order</a> referred by '.$person.' '.$info;
            case 'new_coupon':
                $user = $db->get_meta($id,'user');
                $order = $db->get_meta($id,'order');
                $coupon = $db->get_meta($id,'coupon_id');
                $user_info = get_userdata($user);
                $user_name = $user_info ? '<a href="'.get_edit_user_link($user).'">'.$user_info->first_name.' '.$user_info->last_name.'</a>' : 'guest';
                return 'New coupon generated and sent to user '.$user_name.'. Coupon was generated for referral order <a href="'.get_edit_post_link($order).'">#'.$order.'</a>.';
            case 'email_sent':
                $coupon_code = $db->get_meta($id,'coupon_code');
                $email = $db->get_meta($id,'email');
                $coupon_id = wc_get_coupon_id_by_code($coupon_code);
                return 'New email sent to '.$email.' containing the rewarded coupon code: <a href="'.get_edit_post_link($coupon_id).'">'.$coupon_code.'</a>.';
        }
    }

}

new WPGENS_RAF_Events();