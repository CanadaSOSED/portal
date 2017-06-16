<?php

/**
 * WCMp MassPay Cron Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
class WCMp_MassPay_Cron {

    public function __construct() {
        add_action('masspay_cron_start', array(&$this, 'do_mass_payment'));
    }

    /**
     * Calculate the amount and selete payment method.
     *
     *
     */
    function do_mass_payment() {
        global $WCMp;
        $payment_admin_settings = get_option('wcmp_payment_settings_name');
        if (!isset($payment_admin_settings['wcmp_disbursal_mode_admin'])) {
            return;
        }






        doProductVendorLOG("Cron Run Start for array creatation @ " . date('d/m/Y g:i:s A', time()));
        $commissions = $this->get_query_commission();
        $commission_data = $commission_totals = $commissions_data = array();
        if ($commissions) {
            $transaction_data = array();
            foreach ($commissions as $commission) {
                $WCMp_Commission = new WCMp_Commission();
                $commission_data = $WCMp_Commission->get_commission($commission->ID);
                $commission_order_id = get_post_meta($commission->ID, '_commission_order_id', true);
                $vendor_shipping = get_post_meta($commission->ID, '_shipping', true);
                $vendor_tax = get_post_meta($commission->ID, '_tax', true);
                $order = new WC_Order($commission_order_id);
                $vendor = get_wcmp_vendor_by_term($commission_data->vendor->term_id);
                $payment_type = get_user_meta($vendor->id, '_vendor_payment_mode', true);
                if (empty($payment_type)) {
                    continue;
                }
                $due_vendor = get_wcmp_vendor_order_amount(array('vendor_id' => $vendor->id, 'order_id' => $order->get_id()));
                $vendor_due = (float) $due_vendor['total'];
                //check unpaid commission threshold
                $total_vendor_due = $vendor->wcmp_vendor_get_total_amount_due();
                $get_vendor_thresold = 0;
                if (isset($WCMp->vendor_caps->payment_cap['commission_threshold'])) {
                    $get_vendor_thresold = (float) $WCMp->vendor_caps->payment_cap['commission_threshold'];
                }
                if ($get_vendor_thresold > $total_vendor_due) {
                    continue;
                }

                $commission_threshold_time = isset($WCMp->vendor_caps->payment_cap['commission_threshold_time']) && !empty($WCMp->vendor_caps->payment_cap['commission_threshold_time']) ? $WCMp->vendor_caps->payment_cap['commission_threshold_time'] : 0;
                $commission_create_date = get_the_date('U', $commission->ID);
                $current_date = date('U');
                $diff = intval(($current_date - $commission_create_date) / (3600 * 24));
                if ($diff < $commission_threshold_time) {
                    continue;
                }

                if (array_key_exists($commission_data->vendor->term_id, $transaction_data)) {
                    $commission_totals[$commission_data->vendor->term_id]['amount'] += apply_filters('paypal_masspay_amount', $vendor_due, $commission_order_id, $commission_data->vendor->term_id);
                } else {
                    $commission_totals[$commission_data->vendor->term_id]['amount'] = apply_filters('paypal_masspay_amount', $vendor_due, $commission_order_id, $commission_data->vendor->term_id);
                }
                $transaction_data[$commission_data->vendor->term_id]['commission_detail'][$commission->ID] = $commission_order_id;
                $transaction_data[$commission_data->vendor->term_id]['amount'] = $commission_totals[$commission_data->vendor->term_id]['amount'];
                $transaction_data[$commission_data->vendor->term_id]['payment_mode'] = $payment_type;
            }
            // Set info for all payouts
            $currency = get_woocommerce_currency();
            $payout_note = sprintf(__('Total commissions earned from %1$s as at %2$s on %3$s', 'dc-woocommerce-multi-vendor'), get_bloginfo('name'), date('H:i:s'), date('d-m-Y'));
            $commissions_data = array();
            $transactions_data = array();
            foreach ($commission_totals as $vendor_id => $total) {
                if (!isset($total['amount']))
                    continue; //$total['amount'] = 0;
                if (isset($total['transaction_fee']))
                    $total_payable = $total['amount'] - $total['transaction_fee'];
                else
                    $total_payable = $total['amount'];
                // Get vendor data
                $vendor_payment_mode = $transaction_data[$vendor_id]['payment_mode'];
                if (empty($vendor_payment_mode)) {
                    continue;
                }
                $commissions_data[$vendor_payment_mode][] = array(
                    'total' => round($total_payable, 2),
                    'currency' => $currency,
                    'vendor_id' => $vendor_id,
                    'payout_note' => $payout_note
                );
                $transactions_data[$vendor_payment_mode][$vendor_id] = $transaction_data[$vendor_id];
            }
            //print_r($transactions_data);die;
            if (!empty($commissions_data)) {
                foreach ($commissions_data as $payment_mode => $payment_data) {
                    // Call masspay api as vendor payment mode.
                    if ($payment_mode == 'direct_bank') {
                        if (!empty($payment_data)) {
                            // create a new transaction by vendor
                            $transaction_data = $transactions_data[$payment_mode];
                            if (!empty($transaction_data)) {
                                $transaction_id = $WCMp->transaction->insert_new_transaction($transaction_data, 'wcmp_processing', 'direct_bank');
                                $email_vendor = WC()->mailer()->emails['WC_Email_Vendor_Direct_Bank'];
                                $email_vendor->trigger($transaction_id, $payment_data['vendor_id']);
                                $email_admin = WC()->mailer()->emails['WC_Email_Admin_Widthdrawal_Request'];
                                $email_admin->trigger($transaction_id, $payment_data['vendor_id']);
                            }
                        }
                    } else {
                        do_action('wcmp_payment_cron_' . $payment_mode, array('payment_data' => $payment_data, 'transaction_data' => $transactions_data[$payment_mode]));
                    }
                }
            }
        }
    }

    /**
     * Get Commissions
     *
     * @return object $commissions
     */
    public function get_query_commission() {
        $args = array(
            'post_type' => 'dc_commission',
            'post_status' => array('publish', 'private'),
            'meta_key' => '_paid_status',
            'meta_value' => 'unpaid',
            'posts_per_page' => -1
        );
        $commissions = get_posts($args);
        return $commissions;
    }

}
