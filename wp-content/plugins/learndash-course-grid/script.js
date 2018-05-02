
jQuery(window).load(function() {
	function ld_course_grid_resize() {
		var last_position = {left: 0, top: 0}, last_item = null;
		var row_items = [];
		var row_idx = 0;
		row_items[row_idx] = [];
		
		// We first loop over the row items to setup a multidimenion array by rows. 
		jQuery(".ld_course_grid").each(function (i, v) {
			var item = jQuery(v);
			var item_position = item.position();

			// The outer <div> element we wrap on the grid items should NOT have a locally defined style attribute
			// If it is there we want to remove it to force the correct height calculations
			var attr_style = item.attr('style');
			if (attr_style != '') {
				item.removeAttr('style');
			}

			if ( (last_position.left != 0 ) && ( item_position.left <= last_position.left ) ) {
				row_idx += 1;
				row_items[row_idx] = [];
			} 
			row_items[row_idx].push(item);

			last_position = item_position;
			last_item = item;
		});

		// One we have the rows and related items we loop over each row
		for (var row_idx = 0; row_idx < row_items.length; row_idx++ ) {
		    var row = row_items[row_idx];
			
			// and determine the tallest row item...
			var row_max_height = 0;
		    for (var item_idx = 0; item_idx < row.length; item_idx++ ) {
		        var item = row[item_idx];
				
				item_height = item.height();
				if ( item_height > row_max_height )
					row_max_height = item_height;
		    }

			// Then loop over the rows again to set the height per the tallest item in the row.
		    for (var item_idx = 0; item_idx < row.length; item_idx++) {
		        var item = row[item_idx];
				item.height(row_max_height);
			}
		}



/*
		jQuery(".ld_course_grid").each(function (i, v) {
			var item = jQuery(this);
			var position = item.position();

			var attr_style = item.attr('style');
			if (attr_style != '') {
				console.log('item[%o] attr_style[%o]', item, attr_style);
				item.removeAttr('style');
			}

			var item_height = item.height();

			if(position.left <= last_position.left ) {
				if(item_height < talest )
					last_item.height(talest);
				talest = 0;
			}
			if(item_height >= talest)
				talest = item_height; // + 5; // 2016.06.01: Removed the addition of the 5 as not sure why this was included. 

			last_position = position;
			last_item = item;
		});
*/

	}
	ld_course_grid_resize();
	jQuery(window).resize(function() {
		ld_course_grid_resize();
	});

	function learndash_course_grid_course_edit_page_javascript() {
		jQuery("select[name=sfwd-courses_course_price_type]").change(function(){
			var price_type = 	jQuery("select[name=sfwd-courses_course_price_type]").val();
			if(price_type == "closed") 
				jQuery("#sfwd-courses_course_price").show();
		});
		jQuery("select[name=sfwd-courses_course_price_type]").change();
	}
	if(jQuery(".sfwd-courses_settings").length)
	setTimeout( function() {learndash_course_grid_course_edit_page_javascript();}, 1000);
});
