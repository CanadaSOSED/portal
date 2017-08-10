jQuery(document).ready(function($) {
	var productTitle = wpcl_script_vars.productTitle;
	var pdfOrientation = wpcl_script_vars.pdfOrientation;
	var pdfPageSize = wpcl_script_vars.pdfPagesize;
	var fileName = productTitle.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');

	var table = $('.wpcl #list-table').DataTable( {

		columnDefs: [
	        { targets: [0], visible: false},
	        { targets: '_all', visible: true }
    	],
		colReorder: true,
		stateSave:  true,
  		stateLoadParams: function (settings, data) {  data.columns['0'].visible = false; },

		select: true,
		lengthMenu: [[10, 25, 50, -1], [10, 25, 50, wpcl_script_vars.lengthMenuAll]],
		dom: 'Blfrtip',
		buttons: [
			{
				extend: 'copy',
				text: wpcl_script_vars.copybtn,
			},
			{
				extend: 'print',
				title: productTitle,
				text: wpcl_script_vars.printbtn,
				customize: function ( win ) {
					$(win.document.body)
						.css( 'background-color', '#fff' )
						.css( 'padding', '1px' );

					$(win.document.body).find( 'table' )
						.addClass( 'compact' )
						.css( 'font-size', 'inherit' )
						.css( 'border', '0px' )
						.css( 'border-collapse', 'collapse' );

					$(win.document.body).find( 'table th' )
						.css( 'padding', '5px 8px 8px' )
						.css( 'background-color', '#f1f1f1' )
						.css( 'border-bottom', '0px' );

					$(win.document.body).find( 'table td' )
						.css( 'border', '1px solid #dfdfdf' )
						.css( 'padding', '8px' );
												
					$(win.document.body).find( 'table tr:nth-child(even)' )
						.css( 'background-color', '#f9f9f9' );
				}
			},
			{
				extend: 'excelHtml5',
				title: fileName
			},
			{
				extend: 'csvHtml5',
				title: fileName
			},
			{
				extend: 'pdfHtml5',
				title: productTitle,
				orientation: pdfOrientation,
				pageSize: pdfPageSize,
				filename: fileName,
				customize: function(doc)
				{
					doc.styles.tableHeader.fillColor = '#f1f1f1';
					doc.styles.tableHeader.color = '#000';
					doc.styles.tableBodyEven.fillColor = '#f9f9f9';
					doc.styles.tableBodyOdd.fillColor = '#fff';
				}
			},
			{
				text: wpcl_script_vars.resetColumn,
				action: function ( e, dt, node, config ) {
					table.colReorder.reset();
					table.state.clear();
					window.location.reload();
				}
			}
		],
		pagingType: 'full',
		scrollX: true,
		language: {
			'search': wpcl_script_vars.search,
			'emptyTable': wpcl_script_vars.emptyTable,
			'zeroRecords': wpcl_script_vars.zeroRecords,
			'tableinfo': wpcl_script_vars.tableinfo,
			'lengthMenu': wpcl_script_vars.lengthMenu,
			'info': wpcl_script_vars.info,
			paginate: {
				first:    '«',
				previous: '‹',
				next:     '›',
				last:     '»'
			},
			buttons: {
			copyTitle: wpcl_script_vars.copyTitle,
			copySuccess: {
				_: wpcl_script_vars.copySuccessMultiple,
				1: wpcl_script_vars.copySuccessSingle,
			}
		},
		aria: {
			paginate: {
				first:    wpcl_script_vars.paginateFirst,
				previous: wpcl_script_vars.paginatePrevious,
				next:     wpcl_script_vars.paginateNext,
				last:     wpcl_script_vars.paginateLast,
			}
		}
	}
	} );

	// Update email list on row selection
	$('#email-selected').click(function( event ) {
		//event.preventDefault();
	});
	table.on( 'select', function ( e, dt, type, indexes ) {
		var emails = $.map(table.rows('.selected').data(), function (item) {
			return item[0];
		});
		var emailBcc = emails.join(",");
		$('#email-selected').attr('href', 'mailto:?bcc=' + emailBcc);
		if(emailBcc) {
			$('#email-selected').removeAttr('disabled');
		}
	});

	// Update email list on row deselection
	table.on( 'deselect', function ( e, dt, type, indexes ) {
		var emails = $.map(table.rows('.selected').data(), function (item) {
			return item[0];
		});
		var emailBcc = emails.join(",");
		$('#email-selected').attr('href', 'mailto:?bcc=' + emailBcc);
		if(emailBcc) {
			$('#email-selected').removeAttr('disabled');
		} else {
			$('#email-selected').attr('disabled', 'true');
		}
	});
} );