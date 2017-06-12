<?php
/**
 * The template for displaying vendor report
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/vendor-policy.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
global $WCMp;
$wcmp_policy_settings = get_option("wcmp_general_policies_settings_name");
$wcmp_capabilities_settings_name = get_option("wcmp_general_policies_settings_name");
$can_vendor_edit_policy_tab_label_field = apply_filters('can_vendor_edit_policy_tab_label_field', true);
$can_vendor_edit_cancellation_policy_field = apply_filters('can_vendor_edit_cancellation_policy_field', true);
$can_vendor_edit_refund_policy_field = apply_filters('can_vendor_edit_refund_policy_field', true);
$can_vendor_edit_shipping_policy_field = apply_filters('can_vendor_edit_shipping_policy_field', true);
?>
<form method="post" name="shop_settings_form" class="wcmp_policy_form">
    <div class="wcmp_form1">
        <?php if (get_wcmp_vendor_settings('is_policy_on', 'general') == 'Enable' && isset($wcmp_capabilities_settings_name['can_vendor_edit_policy_tab_label']) && $can_vendor_edit_policy_tab_label_field && (isset($wcmp_capabilities_settings_name['can_vendor_edit_policy_tab_label']) || isset($wcmp_capabilities_settings_name['can_vendor_edit_cancellation_policy']) || isset($wcmp_capabilities_settings_name['can_vendor_edit_refund_policy']) || isset($wcmp_capabilities_settings_name['can_vendor_edit_shipping_policy']) )) { ?>
            <p> <?php _e('Policy Tab Label', $WCMp->text_domain); ?>	</p>
            <input class="no_input" type="text" readonly name="vendor_policy_tab_title" placeholder="<?php _e('Policies. ', $WCMp->text_domain); ?>" value="<?php echo isset($vendor_policy_tab_title['value']) ? $vendor_policy_tab_title['value'] : ''; ?>" >
        <?php } ?>

        <?php if (get_wcmp_vendor_settings('is_policy_on', 'general') == 'Enable' && isset($wcmp_policy_settings['is_cancellation_on']) && isset($wcmp_capabilities_settings_name['can_vendor_edit_cancellation_policy']) && $can_vendor_edit_cancellation_policy_field) { ?>
            <p> <?php _e('Cancellation/Return/Exchange Policy', $WCMp->text_domain); ?>	</p>
            <textarea class="no_input" readonly name="vendor_cancellation_policy" cols="" rows="" placeholder="<?php _e('It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. ', $WCMp->text_domain); ?>"><?php echo isset($vendor_cancellation_policy['value']) ? $vendor_cancellation_policy['value'] : $wcmp_policy_settings['cancellation_policy']; ?></textarea>
        <?php } ?>
        <?php if (get_wcmp_vendor_settings('is_policy_on', 'general') == 'Enable' && isset($wcmp_policy_settings['is_refund_on']) && isset($wcmp_capabilities_settings_name['can_vendor_edit_refund_policy']) && $can_vendor_edit_refund_policy_field) { ?>
            <p> <?php _e('Refund Policy', $WCMp->text_domain); ?>	</p>
            <textarea  class="no_input" readonly name="vendor_refund_policy" cols="" rows="" placeholder="<?php _e('It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. ', $WCMp->text_domain); ?>"><?php echo isset($vendor_refund_policy['value']) ? $vendor_refund_policy['value'] : $wcmp_policy_settings['refund_policy']; ?></textarea>
        <?php } ?>
        <?php if (get_wcmp_vendor_settings('is_policy_on', 'general') == 'Enable' && isset($wcmp_policy_settings['is_shipping_on']) && isset($wcmp_capabilities_settings_name['can_vendor_edit_shipping_policy']) && $can_vendor_edit_shipping_policy_field) { ?>
            <p> <?php _e('Shipping Policy', $WCMp->text_domain); ?></p>
            <textarea  class="no_input" readonly name="vendor_shipping_policy" cols="" rows="" placeholder="<?php _e('It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. ', $WCMp->text_domain); ?>"><?php echo isset($vendor_shipping_policy['value']) ? $vendor_shipping_policy['value'] : $wcmp_policy_settings['shipping_policy']; ?></textarea>
        <?php } ?>
        <?php if (get_wcmp_vendor_settings ('can_vendor_add_customer_support_details', 'general', 'customer_support_details') == 'Enable' && get_wcmp_vendor_settings ('is_customer_support_details', 'general') == 'Enable') { ?>
            <div class="wcmp_headding2 moregap"><?php _e('Customer Support Details', $WCMp->text_domain); ?></div>
            <div class="half_part">
                <p> <?php _e('Phone*', $WCMp->text_domain); ?></p>
                <input  class="no_input" readonly type="text" name="vendor_customer_phone" placeholder="" value="<?php echo isset($vendor_customer_phone['value']) ? $vendor_customer_phone['value'] : ''; ?>">
            </div>
            <div class="half_part">
                <p> <?php _e('Email*', $WCMp->text_domain); ?></p>
                <input  class="no_input" readonly type="text" placeholder="" name="vendor_customer_email" value="<?php echo isset($vendor_customer_email['value']) ? $vendor_customer_email['value'] : ''; ?>">
            </div>
            <div class="clear"></div>
            <p><?php _e('Address*', $WCMp->text_domain); ?></p>
            <input  class="no_input" readonly type="text" placeholder="Address line 1" name="vendor_csd_return_address1"  value="<?php echo isset($vendor_csd_return_address1['value']) ? $vendor_csd_return_address1['value'] : ''; ?>">
            <input  class="no_input" readonly type="text" placeholder="Address line 2" name="vendor_csd_return_address2"  value="<?php echo isset($vendor_csd_return_address2['value']) ? $vendor_csd_return_address2['value'] : ''; ?>">
            <div class="one_third_part">
                <input  class="no_input" readonly type="text" placeholder="Country" name="vendor_csd_return_country" value="<?php echo isset($vendor_csd_return_country['value']) ? $vendor_csd_return_country['value'] : ''; ?>">
            </div>
            <div class="one_third_part">
                <input  class="no_input" readonly type="text" placeholder="state"  name="vendor_csd_return_state" value="<?php echo isset($vendor_csd_return_state['value']) ? $vendor_csd_return_state['value'] : ''; ?>">
            </div>
            <div class="one_third_part">
                <input  class="no_input" readonly type="text" placeholder="city"  name="vendor_csd_return_city" value="<?php echo isset($vendor_csd_return_city['value']) ? $vendor_csd_return_city['value'] : ''; ?>">
            </div>
            <p></p>
            <input  class="no_input" readonly type="text" placeholder="Zipcode" style="width:50%;" name="vendor_csd_return_zip" value="<?php echo isset($vendor_csd_return_zip['value']) ? $vendor_csd_return_zip['value'] : ''; ?>">
            <div class="clear"></div>
        <?php } ?>
    </div>

    <?php do_action('other_exta_field_dcmv'); ?>
    <p class="error_wcmp"><?php _e('* This field is required, you must fill some information.', $WCMp->text_domain); ?></p>
    <div class="action_div">
        <button class="wcmp_orange_btn" name="store_save_policy"><?php _e('Save Options', $WCMp->text_domain); ?></button>
        <div class="clear"></div>
    </div>
</form>