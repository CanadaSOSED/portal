
function bind_events() {

	// for filter by ORDER custom fields
    jQuery( '#custom_fields' ).change( function() {

        jQuery( '#select_custom_fields' ).attr( 'disabled', 'disabled' );
        var data = {
            'cf_name': jQuery( this ).val(),
            method: "get_order_custom_fields_values",
            action: "order_exporter"
        };

        jQuery.post( ajaxurl, data, function( response ) {
            jQuery( '#select_custom_fields' ).remove();
            if ( response ) {
                var options = '';
                jQuery.each( response, function( index, value ) {
                    options += '<option>' + value + '</option>';
                } );
                jQuery( '<select id="select_custom_fields" style="margin-top: 0px;margin-right: 6px;">' + options + '</select>' ).insertBefore( jQuery( '#add_custom_fields' ) );
            }
            else {
                jQuery( '<input type="text" id="select_custom_fields" style="margin-right: 8px;">' ).insertBefore( jQuery( '#add_custom_fields' ) );
            }
        }, 'json' );
    } );
    jQuery( '#add_custom_fields' ).click( function() {

        var val = !jQuery( "#select_custom_fields" ).is(':disabled') ? jQuery( "#select_custom_fields" ).val() : jQuery( "#text_custom_fields" ).val();
        var val2 = jQuery( '#custom_fields' ).val();
        var val_op = jQuery( '#custom_fields_compare' ).val();
        if ( val != null && val2 != null && val.length && val2.length ) {
            val = val2 + ' ' + val_op + ' ' + val;

            var f = true;
            jQuery( '#custom_fields_check' ).next().find( 'ul li' ).each( function() {
                if ( jQuery( this ).attr( 'title' ) == val ) {
                    f = false;
                }
            } );

            if ( f ) {

                jQuery( '#custom_fields_check' ).append( '<option selected="selected" value="' + val + '">' + val + '</option>' );
                jQuery( '#custom_fields_check' ).select2();

                jQuery( '#custom_fields_check option' ).each( function() {
                    jQuery( '#custom_fields_check option[value=\"' + jQuery( this ).val() + '\"]:not(:last)' ).remove();
                } );

                jQuery( "input#select_custom_fields" ).val( '' );
            }
        }

        return false;
    } );

    jQuery( '#custom_fields_compare').change(function() {
        var val_op = jQuery( '#custom_fields_compare' ).val();
        if ( 'LIKE' === val_op ) {
            jQuery( "#select_custom_fields" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
            jQuery( "#text_custom_fields" ).css('display', 'inline' ).attr( 'disabled', false );
        }
        else {
            jQuery( "#select_custom_fields" ).css( 'display', 'inline-block' ).attr( 'disabled', false );
            jQuery( "#text_custom_fields" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
        }
    });
	//end of change 
	
    jQuery( '#attributes' ).change( function() {

        jQuery( '#select_attributes' ).attr( 'disabled', 'disabled' );
        var data = {
            'attr': jQuery( this ).val(),
            method: "get_products_attributes_values",
            action: "order_exporter"
        };

        jQuery.post( ajaxurl, data, function( response ) {
            jQuery( '#select_attributes' ).remove();
            if ( response ) {
                var options = '';
                jQuery.each( response, function( index, value ) {
                    options += '<option>' + value + '</option>';
                } );
                jQuery( '<select id="select_attributes" style="margin-top: 0px;margin-right: 6px;">' + options + '</select>' ).insertBefore( jQuery( '#add_attributes' ) );
            }
            else {
                jQuery( '<input type="text" id="select_attributes" style="margin-right: 8px;">' ).insertBefore( jQuery( '#add_attributes' ) );
            }
        }, 'json' );
    } );

    jQuery( '#add_attributes' ).click( function() {

        var val = !jQuery( "#select_attributes" ).is(':disabled') ? jQuery( "#select_attributes" ).val() : jQuery( "#text_attributes" ).val();
        var val2 = jQuery( '#attributes' ).val();
        var val_op = jQuery( '#attributes_compare' ).val();
        if ( val != null && val2 != null && val.length && val2.length ) {
            val = val2 + ' ' + val_op + ' ' + val;

            var f = true;
            jQuery( '#attributes_check' ).next().find( 'ul li' ).each( function() {
                if ( jQuery( this ).attr( 'title' ) == val ) {
                    f = false;
                }
            } );

            if ( f ) {

                jQuery( '#attributes_check' ).append( '<option selected="selected" value="' + val + '">' + val + '</option>' );
                jQuery( '#attributes_check' ).select2();

                jQuery( '#attributes_check option' ).each( function() {
                    jQuery( '#attributes_check option[value=\"' + jQuery( this ).val() + '\"]:not(:last)' ).remove();
                } );

                jQuery( "input#select_attributes" ).val( '' );
            }
        }

        return false;
    } );

    jQuery( '#attributes_compare').change(function() {
        var val_op = jQuery( '#attributes_compare' ).val();
        if ( 'LIKE' === val_op ) {
            jQuery( "#select_attributes" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
            jQuery( "#text_attributes" ).css('display', 'inline' ).attr( 'disabled', false );
        }
        else {
            jQuery( "#select_attributes" ).css( 'display', 'inline-block' ).attr( 'disabled', false );
            jQuery( "#text_attributes" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
        }
    });

    jQuery( '#itemmeta' ).change( function() {

        jQuery( '#select_itemmeta' ).attr( 'disabled', 'disabled' );
        var data = {
            'item': jQuery( this ).val(),
            method: "get_products_itemmeta_values",
            action: "order_exporter"
        };

        jQuery.post( ajaxurl, data, function( response ) {
            jQuery( '#select_itemmeta' ).remove();
            if ( response ) {
                var options = '';
                jQuery.each( response, function( index, value ) {
                    options += '<option>' + value + '</option>';
                } );
                jQuery( '<select id="select_itemmeta" style="margin-top: 0px;margin-right: 6px;">' + options + '</select>' ).insertBefore( jQuery( '#add_itemmeta' ) );
            }
            else {
                jQuery( '<input type="text" id="select_itemmeta" style="margin-right: 8px;">' ).insertBefore( jQuery( '#add_itemmeta' ) );
            }
        }, 'json' );
    } );
    
    jQuery( '#add_itemmeta' ).click( function() {

        var val = !jQuery( "#select_itemmeta" ).is(':disabled') ? jQuery( "#select_itemmeta" ).val() : jQuery( "#text_itemmeta" ).val();
        var val2 = jQuery( '#itemmeta' ).val();
        var val_op = jQuery( '#itemmeta_compare' ).val();
        if ( val != null && val2 != null && val.length && val2.length ) {
            val = val2 + ' ' + val_op + ' ' + val;

            var f = true;
            jQuery( '#itemmeta_check' ).next().find( 'ul li' ).each( function() {
                if ( jQuery( this ).attr( 'title' ) == val ) {
                    f = false;
                }
            } );

            if ( f ) {

                jQuery( '#itemmeta_check' ).append( '<option selected="selected" value="' + val + '">' + val + '</option>' );
                jQuery( '#itemmeta_check' ).select2();

                jQuery( '#itemmeta_check option' ).each( function() {
                    jQuery( '#itemmeta_check option[value=\"' + jQuery( this ).val() + '\"]:not(:last)' ).remove();
                } );

                jQuery( "input#select_itemmeta" ).val( '' );
            }
        }

        return false;
    } );

    jQuery( '#itemmeta_compare').change(function() {
        var val_op = jQuery( '#itemmeta_compare' ).val();
        if ( 'LIKE' === val_op ) {
            jQuery( "#select_itemmeta" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
            jQuery( "#text_itemmeta" ).css('display', 'inline' ).attr( 'disabled', false );
        }
        else {
            jQuery( "#select_itemmeta" ).css( 'display', 'inline-block' ).attr( 'disabled', false );
            jQuery( "#text_itemmeta" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
        }
    });
    
    jQuery( '#add_taxonomies' ).click( function() {

        var val = jQuery( "#text_taxonomies" ).val();
        var val2 = jQuery( '#taxonomies' ).val();
        if ( val != null && val2 != null && val.length && val2.length ) {
            val = val2 + '=' + val;

            var f = true;
            jQuery( '#taxonomies_check' ).next().find( 'ul li' ).each( function() {
                if ( jQuery( this ).attr( 'title' ) == val ) {
                    f = false;
                }
            } );

            if ( f ) {

                jQuery( '#taxonomies_check' ).append( '<option selected="selected" value="' + val + '">' + val + '</option>' );
                jQuery( '#taxonomies_check' ).select2();

                jQuery( '#taxonomies_check option' ).each( function() {
                    jQuery( '#taxonomies_check option[value=\"' + jQuery( this ).val() + '\"]:not(:last)' ).remove();
                } );

                jQuery( "#text_taxonomies" ).val( '' );
            }
        }

        return false;
    } );
	
	
	
	// for filter by PRODUCT custom fields
    jQuery( '#product_custom_fields' ).change( function() {

        jQuery( '#select_product_custom_fields' ).attr( 'disabled', 'disabled' );
        var data = {
            'cf_name': jQuery( this ).val(),
            method: "get_product_custom_fields_values",
            action: "order_exporter"
        };

        jQuery.post( ajaxurl, data, function( response ) {
            jQuery( '#select_product_custom_fields' ).remove();
            if ( response ) {
                var options = '';
                jQuery.each( response, function( index, value ) {
                    options += '<option>' + value + '</option>';
                } );
                jQuery( '<select id="select_product_custom_fields" style="margin-top: 0px;margin-right: 6px;">' + options + '</select>' ).insertBefore( jQuery( '#add_product_custom_fields' ) );
            }
            else {
                jQuery( '<input type="text" id="select_product_custom_fields" style="margin-right: 8px;">' ).insertBefore( jQuery( '#add_product_custom_fields' ) );
            }
        }, 'json' );
    } );
    jQuery( '#add_product_custom_fields' ).click( function() {

        var val = !jQuery( "#select_product_custom_fields" ).is(':disabled') ? jQuery( "#select_product_custom_fields" ).val() : jQuery( "#text_product_custom_fields" ).val();
        var val2 = jQuery( '#product_custom_fields' ).val();
        var val_op = jQuery( '#product_custom_fields_compare' ).val();
        if ( val != null && val2 != null && val.length && val2.length ) {
            val = val2 + ' ' + val_op + ' ' + val;

            var f = true;
            jQuery( '#product_custom_fields_check' ).next().find( 'ul li' ).each( function() {
                if ( jQuery( this ).attr( 'title' ) == val ) {
                    f = false;
                }
            } );

            if ( f ) {

                jQuery( '#product_custom_fields_check' ).append( '<option selected="selected" value="' + val + '">' + val + '</option>' );
                jQuery( '#product_custom_fields_check' ).select2();

                jQuery( '#product_custom_fields_check option' ).each( function() {
                    jQuery( '#product_custom_fields_check option[value=\"' + jQuery( this ).val() + '\"]:not(:last)' ).remove();
                } );

                jQuery( "input#select_product_custom_fields" ).val( '' );
            }
        }

        return false;
    } );

    jQuery( '#product_custom_fields_compare').change(function() {
        var val_op = jQuery( '#product_custom_fields_compare' ).val();
        if ( 'LIKE' === val_op ) {
            jQuery( "#select_product_custom_fields" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
            jQuery( "#text_product_custom_fields" ).css('display', 'inline' ).attr( 'disabled', false );
        }
        else {
            jQuery( "#select_product_custom_fields" ).css( 'display', 'inline-block' ).attr( 'disabled', false );
            jQuery( "#text_product_custom_fields" ).css( 'display', 'none' ).attr( 'disabled', 'disabled' );
        }
    });
	//end of change 
	

    jQuery( '#orders_add_custom_field' ).click( function() {
        jQuery( "#fields_control > div" ).hide();
        jQuery( "#fields_control .div_custom" ).show();

        //add_custom_field(jQuery("#order_fields"),'products','CSV');
        return false;
    } );
    jQuery( '#orders_add_custom_meta' ).click( function() {
		jQuery('#custom_meta_order_mode_used').attr('checked', false);
		jQuery('#custom_meta_order_mode_used').change();		
        jQuery( "#fields_control > div" ).hide();
        jQuery( "#fields_control .div_meta" ).show();

        //add_custom_field(jQuery("#order_fields"),'products','CSV');
        return false;
    } );

    jQuery( '.button_cancel' ).click( function() {
        reset_field_contorls();
        return false;
    } );

///*CUSTOM FIELDS BINDS
    jQuery( '#button_custom_field' ).click( function() {
        var colname = jQuery( '#colname_custom_field' ).val();
        var value = jQuery( '#value_custom_field' ).val();
        if ( !colname )
        {
            alert( 'empty Column name' );
			jQuery( '#colname_custom_field' ).focus();
            return false
        }
        /*if ( !value )
        {
            alert( 'empty Value' );
			jQuery( '#value_custom_field' ).focus();
            return false
        }*/
        add_custom_field( jQuery( "#order_fields" ), 'orders', output_format, colname, value );
        reset_field_contorls();
        return false;
    } );

    jQuery('input[name=custom_meta_order_mode]').change(function() {
        if ( !jQuery(this).prop('checked') ) {
            var options = '<option></option>';
            jQuery.each( window.order_custom_meta_fields, function( index, value ) {
                options += '<option value="' + escapeStr(value) + '">' + value + '</option>';
            } );
            jQuery( '#select_custom_meta_order' ).html( options );
        }
        else {
            var data = jQuery( '#export_job_settings' ).serialize()
            data = data + "&action=order_exporter&method=get_used_custom_order_meta";

            jQuery.post( ajaxurl, data, function( response ) {
                if ( response ) {
                    var options = '<option></option>';
                    jQuery.each( response, function( index, value ) {
                        options += '<option value="' + escapeStr(value)  + '">' + value + '</option>';
                    } );
                    jQuery( '#select_custom_meta_order' ).html( options );
                }
            }, 'json' );
        }
    });
	
    jQuery('input[name=custom_meta_products_mode]').change(function() {
        if (jQuery(this).val() == 'all') {
            var options = '<option></option>';
            jQuery.each( window.order_products_custom_meta_fields, function( index, value ) {
                options += '<option value="' + escapeStr(value)  + '">' + value + '</option>';
            } );
            jQuery( '#select_custom_meta_products' ).html( options );
        }
        else {
            jQuery('#modal-manage-products').html(jQuery('#TB_ajaxContent').html());
            var data = jQuery( '#export_job_settings' ).serialize();
            data = data + "&action=order_exporter&method=get_used_custom_products_meta&mode=" + mode + "&id=" + job_id;

            jQuery.post( ajaxurl, data, function( response ) {
                if ( response ) {
                    var options = '<option></option>';
                    jQuery.each( response, function( index, value ) {
                        options += '<option value="' + escapeStr(value)  + '">' + value + '</option>';
                    } );
                    jQuery( '#select_custom_meta_products' ).html( options );
                }
            }, 'json' );
            jQuery('#modal-manage-products').html('');
        }
    });

    jQuery('input[name=custom_meta_coupons_mode]').change(function() {
        if (jQuery(this).val() == 'all') {
            var options = '<option></option>';
            jQuery.each( window.order_coupons_custom_meta_fields, function( index, value ) {
                options += '<option value="' + escapeStr(value)  + '">' + value + '</option>';
            } );
            jQuery( '#select_custom_meta_coupons' ).html( options );
        }
        else {
            var data = jQuery( '#export_job_settings' ).serialize()
            data = data + "&action=order_exporter&method=get_used_custom_coupons_meta";

            jQuery.post( ajaxurl, data, function( response ) {
                if ( response ) {
                    var options = '<option></option>';
                    jQuery.each( response, function( index, value ) {
                        options += '<option value="' + escapeStr(value)  + '">' + value + '</option>';
                    } );
                    jQuery( '#select_custom_meta_coupons' ).html( options );
                }
            }, 'json' );
        }
    });

    jQuery( '#button_custom_meta' ).click( function() {
        var label = jQuery( '#select_custom_meta_order' ).val();
        var colname = jQuery( '#colname_custom_meta' ).val();        
		if (! label) //try custom text 
			label = jQuery( '#text_custom_meta_order' ).val();;
        if ( !label )
        {
            alert( 'empty meta key' );
			jQuery( '#select_custom_meta_order' ).focus();
            return false
        }
        if ( !colname )
        {
            alert( 'empty Column name' );
			jQuery( '#colname_custom_meta' ).focus();
            return false
        }
        add_custom_meta( jQuery( "#order_fields" ), 'orders', output_format, label, colname );
        reset_field_contorls();
        return false;
    } );

/////////////END CUSTOM FIELDS BINDS

    jQuery( '#shipping_locations' ).change( function() {

        jQuery( '#text_locations' ).attr( 'disabled', 'disabled' );
        var data = {
            'item': jQuery( this ).val(),
            method: "get_products_shipping_values",
            action: "order_exporter"
        };

        jQuery.post( ajaxurl, data, function( response ) {
            jQuery( '#text_locations' ).remove();
            if ( response ) {
                var options = '';
                jQuery.each( response, function( index, value ) {
                    options += '<option>' + value + '</option>';
                } );
                jQuery( '<select id="text_locations" style="margin-top: 0px;margin-right: 6px;">' + options + '</select>' ).insertBefore( jQuery( '#add_locations' ) );
            }
            else {
                jQuery( '<input type="text" id="text_locations" style="margin-right: 8px;">' ).insertBefore( jQuery( '#add_locations' ) );
            }
        }, 'json' );
    } );

    jQuery( '#add_locations' ).click( function() {

        var val = jQuery( "#text_locations" ).val();
        var val2 = jQuery( '#shipping_locations' ).val();
        var val_op = jQuery( '#shipping_compare' ).val();
        if ( val != null && val2 != null && val.length && val2.length ) {
            val = val2 + val_op + val;

            var f = true;
            jQuery( '#locations_check' ).next().find( 'ul li' ).each( function() {
                if ( jQuery( this ).attr( 'title' ) == val ) {
                    f = false;
                }
            } );

            if ( f ) {

                jQuery( '#locations_check' ).append( '<option selected="selected" value="' + val + '">' + val + '</option>' );
                jQuery( '#locations_check' ).select2();

                jQuery( '#locations_check option' ).each( function() {
                    jQuery( '#locations_check option[value=\"' + jQuery( this ).val() + '\"]:not(:last)' ).remove();
                } );

                jQuery( "input#text_locations" ).val( '' );
            }
        }
        return false;
    } );
}

function add_bind_for_custom_fields( prefix, output_format, $to ) {
    jQuery( '#button_custom_meta_' + prefix + '' ).off();
    jQuery( '#button_custom_field_' + prefix + '' ).off();
    jQuery( '#button_custom_field_' + prefix + '' ).click( function() {
        var colname = jQuery( '#colname_custom_field_' + prefix + '' ).val();
        var value = jQuery( '#value_custom_field_' + prefix + '' ).val();
        if ( !colname )
        {
            alert( 'empty Column name' );
			jQuery( '#colname_custom_field_' + prefix + '' ).focus();
            return false
        }
        if ( !value )
        {
            alert( 'empty Value' );
			jQuery( '#value_custom_field_' + prefix + '' ).focus();
            return false
        }
        jQuery( '#colname_custom_field_' + prefix + '' ).val( "" );
        jQuery( '#value_custom_field_' + prefix + '' ).val( "" );
        add_custom_field( $to, prefix, output_format, colname, value );
        return false;
    } );

    jQuery( '#button_custom_meta_' + prefix + '' ).click( function() {
        var type = jQuery( '#select_custom_meta_' + prefix + '' ).val() != '' ? 'meta' : 'taxonomies';
        type = type + '_' + prefix;
        var label = jQuery( '#select_custom_' + type + '' ).val();
        var colname = jQuery( '#colname_custom_meta_' + prefix + '' ).val();
        if ( colname == undefined || colname == '' ) {
            colname = label;
        }
        if ( !colname )
        {
            alert( 'empty Column name' );
            return false
        }
        add_custom_meta( $to, prefix, output_format, label, colname );
        jQuery( '#select_custom_' + type + '' ).val( "" );
        jQuery( '#colname_custom_meta_' + prefix + '' ).val( "" );
        return false;
    } );
}

function reset_field_contorls() {
    jQuery( '#fields_control' ).find( 'input' ).val( '' );
    jQuery( "#fields_control > div" ).hide();
    jQuery( "#fields_control .div1" ).show();
    jQuery( "#fields_control .div2" ).show();
}

function formatItem( item ) {
    var markup = '<div class="clearfix">' +
        '<div>';
    if ( typeof item.photo_url !== "undefined" )
        markup += '<img src="' + item.photo_url + '" style="width: 20%;float:left;" />';
    markup += '<div style="width:75%;float:left;  padding: 5px;">' + item.text + '</div>' +
        '</div>' +
        '</div><div style="clear:both"></div>';

    return markup;
}

function add_custom_field( to, index_p, format, colname, value ) {
    
    value   = escapeStr(value);
    colname = escapeStr(colname);
    var arr = jQuery( 'input[name*=' + index_p + '\\[label\\]\\[custom_field]' );
    var count = arr.length;
    
    var max = 0;
    for(var i=0; i<count; i++) {
        var n = parseInt(arr[i].name.replace(index_p+'[label][custom_field_', '').replace(']','')); // fixed for popups
        if(n > max) {
            max = n;
        }
    }
    count = max+1;

//    console.log( to, index_p, format, colname, value );
    var row = '<li class="mapping_row segment_modal_' + index_p + '">\
                                                        <div class="mapping_col_1">\
                                                                <input type=hidden name="' + index_p + '[exported][custom_field_' + count + ']"  value="0">\
                                                                <input type=checkbox name="' + index_p + '[exported][custom_field_' + count + ']"  value="1" checked>\
                                                                <input class="mapping_fieldname" type=hidden name="' + index_p + '[segment][custom_field_' + count + ']" value="misc">\
                                                                <input class="mapping_fieldname" type=hidden name="' + index_p + '[label][custom_field_' + count + ']" value="' + colname + '">\
                                                        </div>\
                                                        <div class="mapping_col_2">' + colname + '<a href="#" onclick="return remove_custom_field(this);" style="float: right;"><span class="ui-icon ui-icon-trash"></span></a></div>\
                                                        <div class="mapping_col_3"><input class="mapping_fieldname" type=input name="' + index_p + '[colname][custom_field_' + count + ']" value="' + colname + '"></div>\
                                                        <div class="mapping_col_3"><input class="mapping_fieldname" type=input name="' + index_p + '[value][custom_field_' + count + ']" value="' + value + '"></div>\
                                                </li>\
                        ';
    to.append( row );
}

function add_custom_meta( to, index_p, format, label, colname ) {
 
    label   = escapeStr(label);
    colname = escapeStr(colname);
 
//    console.log();
    var row = '<li class="mapping_row segment_modal_' + index_p + '">\
                                                        <div class="mapping_col_1">\
                                                                <input type=hidden name="' + index_p + '[exported][' + label + ']"   value="0">\
                                                                <input type=checkbox name="' + index_p + '[exported][' + label + ']"   value="1" checked>\
                                                                <input class="mapping_fieldname" type=hidden name="' + index_p + '[label][' + label + ']" value="' + label + '">\
                                                        </div>\
                                                        <div class="mapping_col_2">' + label + '<a href="#" onclick="return remove_custom_field(this);" style="float: right;"><span class="ui-icon ui-icon-trash"></span></a></div>\
                                                        <div class="mapping_col_3"><input class="mapping_fieldname" type=input name="' + index_p + '[colname][' + label + ']" value="' + colname + '"></div>\
                                                </li>\
                        ';
    to.append( row );
}

function formatItemSelection( item ) {
    return item.text;
}

function select2_inits()
{
    jQuery( "#from_status, #to_status" ).select2({
        multiple: true
    });
    jQuery( "#statuses" ).select2();
    jQuery( "#shipping_methods" ).select2();
    jQuery( "#user_roles" ).select2();
    jQuery( "#payment_methods" ).select2();
    jQuery( "#attributes" ).select2( {
        width: 150
    } );
    jQuery( "#attributes_check" ).select2();
    jQuery( "#itemmeta" ).select2( {
        width: 220
    } );
    jQuery( "#itemmeta_check" ).select2();

    jQuery( "#custom_fields" ).select2( {
        width: 150
    } );
    jQuery( "#custom_fields_check" ).select2();
	
    jQuery( "#product_custom_fields" ).select2( {
        width: 150
    } );
    jQuery( "#product_custom_fields_check" ).select2();
	

	
    jQuery( "#taxonomies" ).select2( {
        width: 150
    } );
    jQuery( "#taxonomies_check" ).select2();

    jQuery( "#shipping_locations" ).select2( {
        width: 150
    } );
    jQuery( "#locations_check" ).select2();



    jQuery( "#product_categories" ).select2( {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function( params ) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    method: "get_categories",
                    action: "order_exporter"
                };
            },
            processResults: function( data, page ) {
                // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function( markup ) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 3,
        templateResult: formatItem, // omitted for brevity, see the source of this page
        templateSelection: formatItemSelection // omitted for brevity, see the source of this page
    } );

    jQuery( "#product_vendors" ).select2( {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function( params ) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    method: "get_vendors",
                    action: "order_exporter"
                };
            },
            processResults: function( data, page ) {
                // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function( markup ) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 3,
        templateResult: formatItem, // omitted for brevity, see the source of this page
        templateSelection: formatItemSelection // omitted for brevity, see the source of this page
    } );

    jQuery( "#products" ).select2( {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function( params ) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    method: "get_products",
                    action: "order_exporter"
                };
            },
            processResults: function( data, page ) {
                // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function( markup ) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 3,
        templateResult: formatItem, // omitted for brevity, see the source of this page
        templateSelection: formatItemSelection // omitted for brevity, see the source of this page
    } );

    jQuery( "#user_names" ).select2( {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function( params ) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    method: "get_users",
                    action: "order_exporter"
                };
            },
            processResults: function( data, page ) {
                // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function( markup ) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 3,
        templateResult: formatItem, // omitted for brevity, see the source of this page
        templateSelection: formatItemSelection // omitted for brevity, see the source of this page
    } );

    jQuery( "#coupons" ).select2( {
        ajax: {
            url: ajaxurl,
            dataType: 'json',
            delay: 250,
            data: function( params ) {
                return {
                    q: params.term, // search term
                    page: params.page,
                    method: "get_coupons",
                    action: "order_exporter"
                };
            },
            processResults: function( data, page ) {
                // parse the results into the format expected by Select2.
                // since we are using custom formatting functions we do not need to
                // alter the remote JSON data
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function( markup ) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 3,
        templateResult: formatItem, // omitted for brevity, see the source of this page
        templateSelection: formatItemSelection // omitted for brevity, see the source of this page
    } );
}

function escapeStr(str) 
{
    var entityMap = {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': '&quot;',
        "'": '&#39;',
        "/": '&#x2F;'
      };

    return String(str).replace(/[&<>"'\/]/g, function (s) {
      return entityMap[s];
    });
    
}
