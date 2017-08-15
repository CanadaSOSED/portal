<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/** @var WC_Order_Export_Admin $WC_Order_Export */
$settings_export[ $WC_Order_Export->settings_name_now ]      = get_option( $WC_Order_Export->settings_name_now, array() );
$settings_export[ $WC_Order_Export->settings_name_profiles ] = get_option( $WC_Order_Export->settings_name_profiles, array() );
$settings_export[ $WC_Order_Export->settings_name_actions ]  = get_option( $WC_Order_Export->settings_name_actions , array() );
$settings_export[ $WC_Order_Export->settings_name_cron ]     = get_option( $WC_Order_Export->settings_name_cron, array() );

$type_labels = !$WC_Order_Export::is_full_version() ? array() : array(
	$WC_Order_Export->settings_name_profiles => __( 'Profiles', 'woocommerce-order-export' ),
	$WC_Order_Export->settings_name_actions  => __( 'Order Change', 'woocommerce-order-export' ),
	$WC_Order_Export->settings_name_cron     => __( 'Scheduled Exports', 'woocommerce-order-export' ),
);
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
                        <select id="tools-export-selector">
                            <option data-json='<?php echo json_encode( $settings_export, JSON_PRETTY_PRINT ) ?>'><?php _e( 'All', 'woocommerce-order-export' ) ?></option>
                            <option data-json='<?php echo json_encode( $settings_export[ $WC_Order_Export->settings_name_now ], JSON_PRETTY_PRINT ) ?>'>
		                        <?php _e( 'Export Now', 'woocommerce-order-export' ) ?></option>
		                    <?php foreach ( $type_labels as $group => $label ): ?>
                                <optgroup label="<?php echo $label ?>"></optgroup>
                                <?php foreach ( $settings_export[ $group ] as $item_id => $item ): ?>
                                    <option data-json='<?php echo json_encode( $item, JSON_PRETTY_PRINT ) ?>'>
                                        <?php echo $item_id . " - " . ( isset( $item['title'] ) ? $item['title'] : '' ) ?></option>
                                <?php endforeach; ?>
		                    <?php endforeach; ?>
                        </select>
                        <textarea rows="7" id="tools-export-text" class='tools-textarea'></textarea>
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
                        <textarea rows="7" id="tools-import-text" name="tools-import"></textarea>
                        <p class="help-block"><?php _e( 'This process will overwrite your "Advanced Order Export" settings!', 'woocommerce-order-export' ) ?></p>
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
        jQuery( '#wpbody .tools-textarea' ).click( function () {
            jQuery( this ).select();
        } );

        jQuery( '#tools-import-text' ).on( 'keyup', function () {
            var $textarea = jQuery( this ).val();
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

        jQuery( '#tools-export-selector' ).change( function() {
            jQuery( '#tools-export-text' ).val( jQuery( this ).find( ':selected' ).attr( 'data-json' ) );
        } ).change();

    } );
</script>