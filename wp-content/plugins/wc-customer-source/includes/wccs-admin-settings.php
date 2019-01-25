<?php
/**
 * WC_Customer_Source_Settings Class
 *
 * @version	1.0.0
 * @since 1.0.0
 * @package	WC_Customer_Source
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WC_Customer_Source_Settings Class
 */
final class WC_Customer_Source_Settings
{
    public static function save_settings() {

        if ( ! $_POST )
            return;

        if ( ! wp_verify_nonce( $_POST['_wccs_nonce'], 'wc_customer_source_nonce' ) )
            return;

        $source_options = array();

        if ( is_array( $_POST['checkout_field_options'] ) ) {

            foreach( $_POST['checkout_field_options'] as $source_option ) {
                if ( $source_option )
                    $source_options[ $source_option ] = sanitize_text_field( $source_option );
            }
        }

        foreach ( wc_get_order_statuses() as $status => $label ) {
            $order_statuses[ $status ] = isset( $_POST['report_orders_statuses'][ $status ] ) ? $status : false;
        }

        $updated = array(
            'plugin_enabled'            =>  isset( $_POST['plugin_enabled'] ) && $_POST['plugin_enabled'] ? true : false,
            'checkout_field_position'   =>  isset( $_POST['checkout_field_position'] ) ? sanitize_text_field( $_POST['checkout_field_position'] ) : 'woocommerce_after_order_notes',
            'checkout_field_label'		=>	isset( $_POST['checkout_field_label'] ) ? sanitize_text_field( $_POST['checkout_field_label'] ) : '',
			'checkout_field_options'	=>	$source_options,
            'checkout_field_required'   =>  isset( $_POST['checkout_field_required'] ) && $_POST['checkout_field_required'] ? true : false,
			'other_field_disable'		=>	isset( $_POST['other_field_disable'] ) ? true : false,
			'other_field_label'			=>	isset( $_POST['other_field_label'] ) ? sanitize_text_field( $_POST['other_field_label'] ) : '',
			'report_orders_displayed'	=>	isset( $_POST['report_orders_displayed'] ) ? true : false,
            'report_orders_statuses'    =>  $order_statuses,
        );

        $check = update_option( 'wc_customer_source_settings', $updated );

        if ( $check ) {
            $notice = array( 'status' => 'updated', 'message' => 'Settings have been updated' );
        } else {
            $notice = array( 'status' => 'error', 'message' => 'There was an error trying to update your settings. Please try again.' );
        }

        ?>
        <div id="message" class="<?php echo $notice['status']; ?> notice is-dismissible">
            <p><?php echo $notice['message']; ?></p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text">Dismiss this notice.</span>
            </button>
        </div>
        <?php
    }

    public static function display_settings_page() {

        $settings = get_option( 'wc_customer_source_settings' );

        $plugin_enabled = isset( $settings['plugin_enabled'] ) ? $settings['plugin_enabled'] : true;

        $field_positions = apply_filters( 'wc_customer_source_checkout_field_actions', array(
            'woocommerce_before_order_notes'            =>  __( 'Before order notes', 'wc-customer-source' ),
            'woocommerce_after_order_notes'             =>  __( 'After order notes', 'wc-customer-source' ),
            'woocommerce_before_checkout_billing_form'  =>  __( 'Before checkout billing form', 'wc-customer-source' ),
            'woocommerce_after_checkout_billing_form'   =>  __( 'After checkout billing form', 'wc-customer-source' ),
            'woocommerce_before_checkout_shipping_form' =>  __( 'Before checkout shipping form', 'wc-customer-source' ),
            'woocommerce_after_checkout_shipping_form'  =>  __( 'After checkout shipping form', 'wc-customer-source' ),
        ) );

        $current_field_position = isset( $settings['checkout_field_position'] ) ? $settings['checkout_field_position'] : 'woocommerce_after_order_notes';

        ?>
        <form method="post">

            <h2><?php echo __( 'Checkout Field Settings', 'wc-customer-source' ); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th>
                        <label for="checkout_field_position"><?php echo __( 'Enable / Disable', 'wc-customer-source' ); ?></label>
                    </th>
                    <td class="forminp forminp-text">
                        <label>
                            <input type="checkbox" name="plugin_enabled" value="true" <?php checked( $plugin_enabled, true ); ?>>
                            Enable custom field on Checkout page
                        </label>

                    </td>
                </tr>
                <tr valign="top">
                    <th>
                        <label for="checkout_field_position"><?php echo __( 'Checkout Field Position', 'wc-customer-source' ); ?></label>
                    </th>
                    <td class="forminp forminp-text">
                        <select name="checkout_field_position" id="checkout_field_position" style="min-width: 300px" required>
                            <option value=""><?php echo __( 'select', 'wc-customer-source' ); ?></option>
                            <?php foreach( $field_positions as $value => $label ) : ?>
                                <option value="<?php echo $value; ?>" <?php selected( $current_field_position, $value ); ?>><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th>
                        <label for="checkout_field_label"><?php echo __( 'Checkout Field Label', 'wc-customer-source' ); ?></label>
                    </th>
                    <td class="forminp forminp-text">
                        <input name="checkout_field_label" id="checkout_field_label" type="text" value="<?php echo $settings['checkout_field_label']; ?>" style="min-width: 300px" required>
                    </td>
                </tr>
                <tr valign="top">
                    <th>
                        <label for="checkout_field_options"><?php echo __( 'Checkout Field Options', 'wc-customer-source' ); ?></label>
                    </th>
                    <td class="forminp forminp-text">
                        <div class="checkout-field-options-list">
                            <?php if ( is_array( $settings['checkout_field_options'] ) ) : ?>
                                <?php foreach( $settings['checkout_field_options'] as $option ) : ?>
                                    <div class="option">
                                        <input name="checkout_field_options[]" id="checkout_field_options" type="text" value="<?php echo $option; ?>" style="min-width: 300px">
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="option">
                                    <input name="checkout_field_options[]" id="checkout_field_options" type="text" value="" style="min-width: 300px">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="new-option">
                            <button type="button" class="button wccs-new-option"><?php echo __( 'New option', 'wc-customer-source' ); ?></button>
                        </div>
                    </td>
                </tr>
                <tr valign="top">
                    <th>
                        <label for="checkout_field_label"><?php echo __( 'Required', 'wc-customer-source' ); ?></label>
                    </th>
                    <td class="forminp forminp-text">
                        <label>
                            <input name="checkout_field_required" id="checkout_field_required" type="checkbox" value="true" <?php checked( $settings['checkout_field_required'], true ) ?>>
                            <?php echo __( 'Make field required', 'wc-customer-source' ); ?>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th>
                        <label for="checkout_field_label"><?php echo __( 'Other field', 'wc-customer-source' ); ?></label>
                    </th>
                    <td class="forminp forminp-text">
                        <label>
                            <input name="other_field_disable" id="other_field_disable" type="checkbox" value="true" <?php checked( $settings['other_field_disable'], true ) ?>>
                            <?php echo __( 'Disable other field', 'wc-customer-source' ); ?>
                        </label>
                    </td>
                </tr>
                <tr valign="top" class="other-field-option">
                    <th>
                        <label for="checkout_field_label"><?php echo __( 'Other field label', 'wc-customer-source' ); ?></label>
                    </th>
                    <td class="forminp forminp-text">
                        <input name="other_field_label" id="other_field_label" type="text" value="<?php echo $settings['other_field_label']; ?>" style="min-width: 300px">
                    </td>
                </tr>
            </table>

            <hr>

            <h2><?php echo __( 'Report Settings', 'wc-customer-source' ); ?></h2>
            <table class="form-table">
                <tr valign="top">
                    <th><?php echo __( 'Orders displayed', 'wc-customer-source' ); ?></th>
                    <td>
                        <label>
                            <input name="report_orders_displayed" id="report_orders_displayed" type="checkbox" value="true" <?php checked( $settings['report_orders_displayed'], true ) ?>>
                            <?php echo __( 'Show only orders that have customer source.', 'wc-customer-source' ); ?>
                        </label>
                    </td>
                </tr>
                <tr valign="top">
                    <th><?php echo __( 'Orders Statuses', 'wc-customer-source' ); ?></th>
                    <td>
                        <?php foreach ( wc_get_order_statuses() as $status => $label ) : ?>
                            <label style="display: block; margin-bottom: 5px;">
                                <input name="report_orders_statuses[<?php echo $status; ?>]" type="checkbox" value="<?php echo $status; ?>" <?php checked( $settings['report_orders_statuses'][ $status ], $status ) ?>>
                                <?php echo $label; ?>
                            </label>
                        <?php endforeach; ?>
                    </td>
                </tr>
            </table>

            <input name="save" class="button-primary woocommerce-save-button" type="submit" value="Save changes">
            <?php wp_nonce_field( 'wc_customer_source_nonce', '_wccs_nonce' );  ?>
        </form>

        <script type="text/javascript">
        (jQuery( document ).ready( function($) {

            // add new option
            $('.wccs-new-option').on( 'click', function() {

                optionHtml = '<div class="option"><input name="checkout_field_options[]" id="checkout_field_options" type="text" value="" style="min-width: 300px"></div>';
                $('.checkout-field-options-list').append( optionHtml );
            });

            // toggle other label options
            $('#other_field_disable').on( 'change', function() {

                if ( $(this).prop('checked') ) {
                    $('.other-field-option').find('input,select,textarea').prop( 'readonly', true );
                    $('.other-field-option').hide();
                } else {
                    $('.other-field-option').find('input,select,textarea').prop( 'readonly', false );
                    $('.other-field-option').show();
                }
            });

            $('#other_field_disable').trigger('change');
        }));
        </script>
        <?php
    }
}
