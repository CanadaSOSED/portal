jQuery(document).ready(function($) {
		var productTitle = wpcl_script_vars.productTitle;
		var pdfOrientation = wpcl_script_vars.pdfOrientation;
		var pdfPageSize = wpcl_script_vars.pdfPagesize;
		var fileName = productTitle.replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, '-');

	$('.wpcl #list-table').DataTable( {
		colReorder: true,
		stateSave:  true,
		dom: 'Bfrtip',
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
		],
		pagingType: 'full',
		scrollX: true,
		language: {
			'search': wpcl_script_vars.search,
			'emptyTable': wpcl_script_vars.emptyTable,
			'zeroRecords': wpcl_script_vars.zeroRecords,
			'tableinfo': wpcl_script_vars.tableinfo,
			'lengthMenu': wpcl_script_vars.lengthMenu,
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
} );