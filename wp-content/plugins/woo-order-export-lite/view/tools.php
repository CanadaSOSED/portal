<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$settings_export[ $WC_Order_Export->settings_name_now ]      = get_option( $WC_Order_Export->settings_name_now, array() );
$settings_export[ $WC_Order_Export->settings_name_profiles ] = get_option( $WC_Order_Export->settings_name_profiles, array() );
$settings_export[ $WC_Order_Export->settings_name_actions ] = get_option( $WC_Order_Export->settings_name_actions , array() );
$settings_export[ $WC_Order_Export->settings_name_cron ]     = get_option( $WC_Order_Export->settings_name_cron, array() );
$settings_json = json_encode( $settings_export, JSON_PRETTY_PRINT );
?>
<div class="clearfix"></div>
<div id="woe-admin" class="container-fluid wpcontent">
    <form>
        <div class="woe-tab" id="woe-tab-general">
            <div class="woe-box woe-box-main">
                <h2 class="woe-box-title"><?php _e( 'Export settings', 'woocommerce-order-export' ) ?></h2>
                <div class="row">
                    <div class="col-sm-12 form-group">
                        <h6 class="woe-fake-label"><?php _e( 'Copy these settings and use it to migrate plugin to another wordpress install', 'woocommerce-order-export' ) ?></h6>
                    </div>
                    <div class="col-sm-8 form-group woe-input-simple">
                        <textarea rows="7" id="tools-textarea" class='tools-textarea'><?php echo $settings_json; ?></textarea>
                        <p class="help-block"><?php _e( 'Just click inside the textarea and copy (Ctrl+C)', 'woocommerce-order-export' ) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <form method="post">
        <div class="woe-tab" id="woe-tab-general">
            <div class="woe-box woe-box-main">
                <h2 class="woe-box-title"><?php _e( 'Import settings', 'woocommerce-order-export' ) ?></h2>
                <div class="row">
                    <div class="col-sm-12 form-group">
                        <h6 class="woe-fake-label"><?php _e( 'Paste text into this field to import settings into the current wordpress install.', 'woocommerce-order-export' ) ?></h6>
                    </div>
                    <div class="col-sm-8 form-group woe-input-simple">
                        <textarea rows="7" id="tools-import" name="tools-import"></textarea>
                        <p class="help-block"><?php _e( 'This process will overwrite all your "Advanced Order Export" settings', 'woocommerce-order-export' ) ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2 form-group col-md-offset-7">
                        <input  disabled type="submit" class="woe-btn-tools" value="Import" name="woe-tools-import" id="submit-import">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    jQuery( function ( $ ) {
        jQuery( '#wpbody #tools-textarea' ).click( function () {
            jQuery( '#tools-textarea' ).select();
        } );

        jQuery( '#tools-import' ).on( 'keyup', function () {
            var $textarea = jQuery( '#tools-import' ).val();
            var disable = ( $textarea.length == '' );
            $( "#submit-import" ).prop( "disabled", disable );
        } );

        jQuery( '#submit-import' ).on( 'click', function ( e ) {
            if ( !confirm( 'Are you sure to continue?' ) ) {
                e.preventDefault();
                $( document.activeElement ).blur();
            } else {
                var data = $( '#woe-admin form' ).serialize();
                data = data + "&action=order_exporter&method=save_tools";
                $.post( ajaxurl, data, function ( response ) {
                    document.location = '<?php echo admin_url( 'admin.php?page=wc-order-export&tab=tools&save=y' ) ?>';
                }, "json" );
                return false;
            }
        } );

    } );
</script>