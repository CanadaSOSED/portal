<?php

if (!defined('ABSPATH'))
    exit;

/**
 * @class 		WCMp Transaction Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
class WCMp_Transaction {

    private $post_type;
    public $dir;
    public $file;

    public function __construct() {
        $this->post_type = 'wcmp_transaction';
        $this->register_post_type();
        $this->register_post_status();
    }

    /**
     * Register commission post type
     *
     * @access public
     * @return void
     */
    function register_post_type() {
        global $WCMp;
        if (post_type_exists($this->post_type))
            return;
        $labels = array(
            'name' => _x('Transactions', 'post type general name', $WCMp->text_domain),
            'singular_name' => _x('Transaction', 'post type singular name', $WCMp->text_domain),
            'add_new' => _x('Add New', $this->post_type, $WCMp->text_domain),
            'add_new_item' => sprintf(__('Add New %s', $WCMp->text_domain), __('Transaction', $WCMp->text_domain)),
            'edit_item' => sprintf(__('Edit %s', $WCMp->text_domain), __('Transaction', $WCMp->text_domain)),
            'new_item' => sprintf(__('New %s', $WCMp->text_domain), __('Transaction', $WCMp->text_domain)),
            'all_items' => sprintf(__('All %s', $WCMp->text_domain), __('Transaction', $WCMp->text_domain)),
            'view_item' => sprintf(__('View %s', $WCMp->text_domain), __('Transaction', $WCMp->text_domain)),
            'search_items' => sprintf(__('Search %a', $WCMp->text_domain), __('Transactions', $WCMp->text_domain)),
            'not_found' => sprintf(__('No %s found', $WCMp->text_domain), __('Transactions', $WCMp->text_domain)),
            'not_found_in_trash' => sprintf(__('No %s found In trash', $WCMp->text_domain), __('Transactions', $WCMp->text_domain)),
            'parent_item_colon' => '',
            'all_items' => __('Transactions', $WCMp->text_domain),
            'menu_name' => __('Transactions', $WCMp->text_domain)
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'publicly_queryable' => true,
            'exclude_from_search' => true,
            'show_ui' => false,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'query_var' => false,
            'rewrite' => true,
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => true,
            'supports' => array('title', 'editor', 'comments', 'custom-fields', 'excerpt'),
            'menu_position' => 57,
            'menu_icon' => $WCMp->plugin_url . '/assets/images/dualcube.png'
        );

        register_post_type($this->post_type, $args);
    }

    function register_post_status() {
        register_post_status('wcmp_processing', array(
            'label' => _x('Processing', $this->post_type),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Processing <span class="count">(%s)</span>', 'Processing <span class="count">(%s)</span>'),
        ));

        register_post_status('wcmp_completed', array(
            'label' => _x('Completed', $this->post_type),
            'public' => true,
            'exclude_from_search' => false,
            'show_in_admin_all_list' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>'),
        ));
    }

    /**
     * Create new transaction
     *
     * @param object $transaction_data
     * @param $transaction_status
     * @param $mode
     * @param bool $paypal_response
     * 
     * @return int $transaction_id
     */
    function insert_new_transaction($transaction_data, $transaction_status, $mode, $paypal_response = false) {
        global $WCMp;
        $trans_id = false;
        if (!empty($transaction_data)) {
            foreach ($transaction_data as $vendor_id => $transaction_detail) {
                $trans_details = array(
                    'post_type' => $this->post_type,
                    'post_title' => sprintf(__('Transaction - %s', $WCMp->text_domain), strftime(_x('%B %e, %Y @ %I:%M %p', 'Transaction date parsed by strftime', $WCMp->text_domain))),
                    'post_status' => $transaction_status,
                    'ping_status' => 'closed',
                    'post_author' => $vendor_id
                );
                $trans_id = wp_insert_post($trans_details);
                if ($trans_id) {
                    update_post_meta($trans_id, 'transaction_mode', $mode);
                    if (!isset($transaction_detail['transfer_charge'])) {
                        $transaction_detail['transfer_charge'] = 0;
                    }
                    update_post_meta($trans_id, 'amount', $transaction_detail['amount'] - $transaction_detail['transfer_charge']);
                    update_post_meta($trans_id, 'transfer_charge', $transaction_detail['transfer_charge']);
                    if ($paypal_response) {
                        update_post_meta($trans_id, 'paypal_response', $paypal_response);
                    }
                    update_post_meta($trans_id, 'commission_detail', $transaction_detail['commission_detail']);
                    if ($transaction_status != 'wcmp_processing') {
                        $email_admin = WC()->mailer()->emails['WC_Email_Vendor_Commission_Transactions'];
                        $email_admin->trigger($trans_id, $vendor_id);
                        $commission_id = false;
                        foreach ($transaction_detail['commission_detail'] as $commission_id => $order_id) {
                            update_post_meta($commission_id, '_paid_request', $mode);
                            wcmp_paid_commission_status($commission_id);
                        }
                    } else {
                        $commission_id = false;
                        foreach ($transaction_detail['commission_detail'] as $commission_id => $order_id) {
                            wcmp_paid_commission_status($commission_id);
                            update_post_meta($commission_id, '_paid_request', $mode);
                        }
                    }
                }
            }
        }
        return $trans_id;
    }

    /**
     * Get transaction item total for vendor
     * 
     * @param int $transaction_id
     * @param $vendor
     * @return $item_total
     */
    function get_transaction_item_totals($transaction_id, $vendor) {
        global $WCMp;
        $item_totals = array();
        $transaction_amount = get_post_meta($transaction_id, 'amount', true);
        $transfer_charge = get_post_meta($transaction_id, 'transfer_charge', true);
        $transaction_mode = get_post_meta($transaction_id, 'transaction_mode', true);
        $item_totals['date'] = array('label' => __('Date of request', $WCMp->text_domain), 'value' => get_the_date('Y-m-d', $transaction_id));
        $item_totals['amount'] = array('label' => __('Amount', $WCMp->text_domain), 'value' => get_woocommerce_currency_symbol() . $transaction_amount);
        if ($transfer_charge) {
            $item_totals['transfer_fee'] = array('label' => __('Transfer Fee', $WCMp->text_domain), 'value' => get_woocommerce_currency_symbol() . $transfer_charge);
        }

        if ($transaction_mode == 'direct_bank') {
            $item_totals['via'] = array('label' => __('Transaction Mode', $WCMp->text_domain), 'value' => __('Direct Bank', $WCMp->text_domain));
            $item_totals['bank_account_type'] = array('label' => __('Bank Account Type', $WCMp->text_domain), 'value' => get_user_meta($vendor->id, '_vendor_bank_account_type', true));
            $item_totals['bank_account_name'] = array('label' => __('Bank Account Number', $WCMp->text_domain), 'value' => get_user_meta($vendor->id, '_vendor_bank_account_number', true));
            $item_totals['bank_name'] = array('label' => __('Bank Name', $WCMp->text_domain), 'value' => get_user_meta($vendor->id, '_vendor_bank_name', true));
            $item_totals['aba_routing_number'] = array('label' => __('ABA Routing Number', $WCMp->text_domain), 'value' => get_user_meta($vendor->id, '_vendor_aba_routing_number', true));
            $item_totals['bank_address'] = array('label' => __('Bank Address', $WCMp->text_domain), 'value' => get_user_meta($vendor->id, '_vendor_bank_address', true));
            $item_totals['destination_currency'] = array('label' => __('Destination Currency', $WCMp->text_domain), 'value' => get_user_meta($vendor->id, '_vendor_destination_currency', true));
            $item_totals['iban'] = array('label' => __('IBAN', $WCMp->text_domain), 'value' => get_user_meta($vendor->id, '_vendor_iban', true));
            $item_totals['account_holder_name'] = array('label' => __('Account Holder Name', $WCMp->text_domain), 'value' => get_user_meta($vendor->id, '_vendor_account_holder_name', true));
        } else if ($transaction_mode == 'paypal_masspay') {
            $item_totals['via'] = array('label' => __('Transaction Mode', $WCMp->text_domain), 'value' => __('PayPal', $WCMp->text_domain));
            $item_totals['paypal_email'] = array('label' => __('PayPal Email', $WCMp->text_domain), 'value' => get_user_meta($vendor->id, '_vendor_paypal_email', true));
        } else if ($transaction_mode == 'manual') {
            $item_totals['via'] = array('label' => __('Transaction Mode', $WCMp->text_domain), 'value' => __('Manual', $WCMp->text_domain));
        }
        return apply_filters('wcmp_transaction_item_totals', $item_totals);
    }

    /**
     * Get transaction item details
     *
     * @param int $transaction_id
     */
    function get_transaction_item_details($transaction_id) {
        global $WCMp;
        $commission_details = array();
        $commissions = get_post_meta($transaction_id, 'commission_detail', true);
        if (is_array($commissions)) {
            foreach ($commissions as $commission_id => $order_id) {
                $commission_products = get_post_meta($commission_id, '_commission_product', true);
                if (!is_array($commission_products))
                    $commission_products = array($commission_products);
                if (!empty($commission_products)) {
                    $title = '';
                    foreach ($commission_products as $commission_product) {
                        if (function_exists('wc_get_product')) {
                            $product = wc_get_product($commission_product);
                        } else {
                            $product = new WC_Product($commission_product);
                        }
                        if (is_object($product)) {
                            if ($product->get_formatted_name()) {
                                $title .= $product->get_formatted_name() . ',';
                            } else {
                                $title .= $product->get_title() . ',';
                            }
                        }
                    }
                }
                $amount = (float) get_post_meta($commission_id, '_commission_amount', true) + (float) get_post_meta($commission_id, '_shipping', true) + (float) get_post_meta($commission_id, '_tax', true);

                $commission_details['body'][$commission_id][]['Order'] = '<a href="' . esc_url( wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_orders_endpoint', 'vendor', 'general', 'vendor-orders'), $order_id)) . '" target="_blank">#' . $order_id . '</a>';
                $commission_details['body'][$commission_id][]['Products'] = $title;
                $commission_details['body'][$commission_id][]['Amount'] = get_woocommerce_currency_symbol() . $amount;
            }
        }
        $commission_details['header'] = array(__('Order', $WCMp->text_domain), __('Products', $WCMp->text_domain), __('Amount', $WCMp->text_domain));
        return apply_filters('wcmp_transaction_item_details', $commission_details);
    }

    /**
     * Get transactions for a period
     */
    function get_transactions($vendor_term_id = false, $start_date = false, $end_date = false, $transaction_status = false, $offset = false, $no_of = false) {
        global $WCMp;

        if (!$no_of)
            $no_of = -1;
        if (!$transaction_status)
            $transaction_status = 'any';

        $args = array(
            'post_type' => 'wcmp_transaction',
            'post_status' => $transaction_status,
            'posts_per_page' => $no_of
        );
        if ($offset)
            $args['offset'] = $offset;

        if (isset($vendor_term_id))
            $args['author'] = $vendor_term_id;
        if ($start_date) {
            $start_year = date('Y', strtotime($start_date));
            $start_month = date('n', strtotime($start_date));
            $start_day = date('j', strtotime($start_date));
        }

        if ($end_date) {
            $end_year = date('Y', strtotime($end_date));
            $end_month = date('n', strtotime($end_date));
            $end_day = date('j', strtotime($end_date));
        }


        if ($start_date && !$end_date) {
            $args['date_query'] = array(
                array(
                    'year' => $start_year,
                    'month' => $start_month,
                    'day' => $start_day,
                ),
            );
        } else if ($start_date && $end_date) {
            $args['date_query'] = array(
                array(
                    'after' => array(
                        'year' => $start_year,
                        'month' => $start_month,
                        'day' => $start_day,
                    ),
                    'before' => array(
                        'year' => $end_year,
                        'month' => $end_month,
                        'day' => $end_day,
                    ),
                    'inclusive' => true,
                ),
            );
        }
        $transactions = new WP_Query($args);
        $transactions = $transactions->get_posts();
        $transaction_details = array();

        if ($transactions) {
            foreach ($transactions as $transaction_key => $transaction) {

                $transaction_details[$transaction->ID]['post_date'] = $transaction->post_date;
                if ($transaction->post_status == 'wcmp_completed')
                    $transaction_details[$transaction->ID]['status'] = __('Completed', $WCMp->text_domain);
                else if ($transaction->post_status == 'wcmp_processing')
                    $transaction_details[$transaction->ID]['status'] = __('Processing', $WCMp->text_domain);
                $transaction_details[$transaction->ID]['vendor_id'] = $transaction->post_author;
                $transaction_details[$transaction->ID]['commission'] = get_post_meta($transaction->ID, 'amount', true) + get_post_meta($transaction->ID, 'transfer_charge', true);
                $transaction_details[$transaction->ID]['amount'] = get_post_meta($transaction->ID, 'amount', true);
                $transaction_details[$transaction->ID]['transfer_charge'] = get_post_meta($transaction->ID, 'transfer_charge', true);
                $transaction_details[$transaction->ID]['commission_details'] = get_post_meta($transaction->ID, 'commission_detail', true);
                $mode = get_post_meta($transaction->ID, 'transaction_mode', true);
                if ($mode == 'paypal_masspay')
                    $transaction_details[$transaction->ID]['mode'] = __('PayPal', $WCMp->text_domain);
                else if ($mode == 'direct_bank')
                    $transaction_details[$transaction->ID]['mode'] = __('Direct Bank Transfer', $WCMp->text_domain);
            }
        }
        return $transaction_details;
    }

    /**
     * Create transaction from commissions
     *
     * @param array $commission_ids
     */
    function create_transactions($commission_ids) {
        global $WCMp;
        $transaction_datas = array();
        if (!empty($commission_ids)) {
            foreach ($commission_ids as $commission_id) {
                $vendor_id = get_post_meta($commission_id, '_commission_vendor', true);
                $vendor = get_wcmp_vendor_by_term($vendor_id);
                $paid_status = get_post_meta($commission_id, '_paid_status', true);
                $order_id = get_post_meta($commission_id, '_commission_order_id', true);
                $order = new WC_Order($order_id);
                $vendor_shipping = get_post_meta($commission_id, '_shipping', true);
                $vendor_tax = get_post_meta($commission_id, '_tax', true);
                $due_vendor = $vendor->wcmp_get_vendor_part_from_order($order, $vendor_id);

                if (!$vendor_shipping)
                    $vendor_shipping = $due_vendor['shipping'];
                if (!$vendor_tax)
                    $vendor_tax = $due_vendor['tax'];

                $amount = get_post_meta($commission_id, '_commission_amount', true);
                $vendor_due = 0;
                $vendor_due = (float) $amount + (float) $vendor_shipping + (float) $vendor_tax;
                $transaction_datas[$vendor_id]['commission_detail'][$commission_id] = $order_id;

                if (!isset($transaction_datas[$vendor_id]['amount']))
                    $transaction_datas[$vendor_id]['amount'] = $vendor_due;
                else
                    $transaction_datas[$vendor_id]['amount'] += $vendor_due;
            }
            $this->insert_new_transaction($transaction_datas, 'wcmp_completed', 'manual');
        }
    }

}
