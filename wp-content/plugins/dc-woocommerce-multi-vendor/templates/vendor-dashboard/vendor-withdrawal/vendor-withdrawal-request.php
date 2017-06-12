<?php
/**
 * The template for displaying vendor withdrawal content
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-withdrawal/vendor-withdrawal-request.php
 *
 * @author 	WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $WCMp;
$transaction = get_post($transaction_id);
if (isset($transaction->post_type) && $transaction->post_type == 'wcmp_transaction') {
    $vendor = get_wcmp_vendor_by_term($transaction->post_author);
    ?>
    <p class="wcmp_headding4"><?php echo apply_filters('wcmp_thankyou_transaction_received_text', sprintf(__('Hello,<br>We have received a new withdrawal request for $%s from you and Your request is being processed.The order details are as follow:', $WCMp->text_domain), get_post_meta($transaction_id, 'amount', true)), $transaction_id); ?></p>
    <div class="wcmp_table_holder">
    <table cellspacing="0" cellpadding="6"  border="1" >
        <tbody>
            <?php $commission_details = $WCMp->transaction->get_transaction_item_details($transaction_id); ?>
            
                <?php if (!empty($commission_details['header'])) { ?>
                <tr>
                    <?php foreach ($commission_details['header'] as $header_val) { ?>
                        <td class="td" scope="col"><?php echo $header_val; ?></td><?php
                    }
                    ?>
                </tr>	<?php
            }
            ?>
            <?php
            if (!empty($commission_details['body'])) {
                foreach ($commission_details['body'] as $commission_detail) {
                    ?>
                    <tr>
                        <?php
                        foreach ($commission_detail as $details) {
                            foreach ($details as $detail_key => $detail) {
                                ?>
                                <td class="td" scope="col"><?php echo $detail; ?></td><?php
                            }
                        }
                        ?>
                    </tr><?php
                }
            }
            if ($totals = $WCMp->transaction->get_transaction_item_totals($transaction_id, $vendor)) {
                foreach ($totals as $total) {
                    ?><tr>
                        <td class="td" scope="col" colspan="2" ><?php echo $total['label']; ?></td>
                        <td class="td" scope="col" ><?php echo $total['value']; ?></td>
                    </tr><?php
                }
            }
            ?>
        </tbody>
    </table>
    </div>
<?php } else { ?>
    <p class="wcmp_headding3"><?php printf(__('Hello,<br>Unfortunately your request for withdrawal amount could not be completed. You may try again later, or check you PayPal settings in your account page, or contact the admin at <b>%s</b>', $WCMp->text_domain), get_option('admin_email')); ?></p>
    <?php
}
?>