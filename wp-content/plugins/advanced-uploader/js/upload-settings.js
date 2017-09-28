/**
 * upload-settings.js
 *
 * handles settings for Advanced uploader.
 */

function removeButton (button) {
	num = button.id.match(/del_dest_(.*)/)[1];
	
	jQuery('#adv_file_upload_destination_'+num).remove();
}

function addButton( label, dest, library ) {
	index_field = jQuery('#index');
	var index = index_field.val();
	createBoxes(index, label, dest, library ).appendTo('#adv_file_upload_destinations');
	index++;
	index_field.val(index);
}

function createBoxes( index, label, dest, library, current ) {
	label = (typeof label  === 'undefined') ? '' : label;
	dest = (typeof dest === 'undefined') ? adv_upload_base_dir  : dest;
	library = (typeof library === 'undefined') ? true: library;
	library = (library == 1) ? true: library;
	
	var boxes = jQuery('<div/>',{
		id:'adv_file_upload_destination_'+index,
		style:'overflow:hidden;'
		});
	if( !current )
		boxes.append(jQuery('<input/>',{
			class: 'adv_file_upload_destination_index',
			type:'hidden',
			value:index,
			}));
	boxes.append(jQuery('<input/>',{
		id:'adv_file_upload_destination_label_'+index,
		name:'adv_file_upload_destination['+index+'][label]',
		type:'text',
		value:label,
		disabled:current,
		style:'float:left;width:135px;'
		}));
	boxes.append(jQuery('<input/>',{
		id:'adv_file_upload_destination_destination_'+index,
		name:'adv_file_upload_destination['+index+'][dest]',
		type:'text',
		value:dest,
		disabled:current,
		style:'float:left;width:285px;'
		}));
	boxes.append(jQuery('<input/>',{
		id:'adv_file_upload_destination_library_'+index,
		name:'adv_file_upload_destination['+index+'][library]',
		type:'checkbox',
		checked:library,
		value:1,
		disabled:current,
		style:'float:left;margin:5px;'
		}));
	boxes.append(jQuery('<input/>',{
		id:'del_dest_'+index,
		name:'del_dest',
		type:'button',
		style:'width:2.5em;float:right;',
		class:'button button-primary',
		value:'-',
		disabled:current,
		onClick:'removeButton(this)'
		}));
	return boxes;
}

function scanButton( button ) {
	var dlg = jQuery('<div>Scanning uploads directory...</div>');
	dlg.dialog({
		title    : 'Possible Destinations',
		dialogClass : 'wp-dialog',
		width    : 'auto',
		modal    : true,
		autoOpen : false,
		closeOnEscape : true,
		buttons : [{
			'text' : 'Cancel',
			'class' : 'button-primary',
			'click' : function() {
				jQuery(this).dialog('close');
				}
		},{
			'text' : 'Add Destinations',
			'class' : 'button-primary',
			'click' : function() {
				jQuery('.adv_file_upload_destination_index', this).each(function( index ) {
					var index = jQuery( this ).val();
					addButton( 
						jQuery( '#adv_file_upload_destination_label_' + index ).val(),
						jQuery( '#adv_file_upload_destination_destination_' + index ).val(),
						document.getElementById( 'adv_file_upload_destination_library_' + index ).checked
					);
				});
				jQuery(this).dialog('close');
				}
		}],
		close : function () {
			jQuery(this).dialog('destroy').remove();
		}});
	dlg.dialog('open');
	jQuery.post( ajaxurl, {'action':'adv_file_upload_scan'/*, 'security':security */ }, function( data ) {
		dlg.html('');
		for (i = 0; i < data .length; i++) { 
			createBoxes('scan_'+i, data[i].label, data[i].dest, data[i].library, data[i].current).appendTo(dlg);
		}
	}, "json");
}