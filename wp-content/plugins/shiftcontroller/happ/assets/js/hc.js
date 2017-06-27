jQuery(document).on( 'click', '.hcj-target ul.hcj-dropdown-menu', function(e)
{
	e.stopPropagation();
//	e.preventDefault();
});

jQuery(document).on( 'click', '.hcj-confirm', function(event)
{
	if( window.confirm("Are you sure?") ){
		return true;
	}
	else {
		event.preventDefault();
		event.stopPropagation();
		return false;
	}
});

/* load ajax content into flatmodal */
function hc_click_flatmodal_closer( obj )
{
	var myParent = obj.closest( '.hcj-flatmodal-parent' );
	var targetDiv = myParent.find('.hcj-flatmodal-container');

	myParent.children().show();
	obj.hide();
	targetDiv.hide();
}

/* load ajax content */
function hc_click_ajax_loader( obj )
{
	var targetUrl = obj.attr('href');
	if(
		( targetUrl.length > 0 ) &&
		( targetUrl.charAt(targetUrl.length-1) == '#' )
		){
		return false;
	}
	targetUrl = hc_convert_ajax_url( targetUrl )

/* search in children */
	var myParent = obj.closest( '.hcj-ajax-parent' );
	var targetDiv = myParent.find('.hcj-ajax-container').filter(":first");
	var scrollInto = obj.hasClass('hcj-ajax-scroll') ? true : false;

	var external_target = true;
	if( targetUrl.charAt(0) == '#' ){
		external_target = false;
	}

	if( targetDiv.length ){
		var currentUrl = targetDiv.data( 'targetUrl' );
		/* already loaded? then close */
		if( currentUrl == targetUrl ){
			targetDiv.data( 'targetUrl', '' );
			targetDiv.html('');
			targetDiv.hide();
		}
		else {
			var highlightTarget = ( targetDiv.is(':visible') && (targetDiv.html().length > 0) );
			if( highlightTarget ){
				targetDiv.addClass( 'hc-loading' );
			}
			else {
				targetDiv.show();
				myParent.addClass( 'hc-loading' );
			}

			targetDiv.data( 'targetUrl', targetUrl );
			if( external_target ){
				targetDiv.load( targetUrl, function()
				{
					if( highlightTarget ){
						targetDiv.removeClass( 'hc-loading' );
					}
					else {
						myParent.removeClass( 'hc-loading' );
					}

					if( scrollInto ){
						jQuery('html, body').animate({
							scrollTop: targetDiv.offset().top - 40,
							}
						);
					}

					/* get some values from elements on the page: */
					var reloadTargetDiv = obj.closest('.hcj-target');
					if( reloadTargetDiv.length > 0 ){
						targetDiv.data( 'return-target', reloadTargetDiv );
					}
					hc_init_page(targetDiv);
				});
			}
			else {
				targetDiv.html( jQuery(targetUrl).html() );
				hc_init_page(targetDiv);

				if( highlightTarget ){
					targetDiv.removeClass( 'hc-loading' );
				}
				else {
					myParent.removeClass( 'hc-loading' );
				}
				targetDiv.data( 'return-target', obj );
			}
		}
	}
	// append after parent
	else {
		myParent.addClass( 'hc-loading' );
		jQuery.get( targetUrl, function(data){
			var wrap_with = myParent.data('wrap-ajax-child');
			if( wrap_with ){
				data = '<' + wrap_with + '>' + '<span>' + data + '</span>' + '</' + wrap_with + '>';
			}
			myParent.after( data );
			myParent.removeClass( 'hc-loading' );

			myParent[0].scrollIntoView();
			});
	}

	return false;
}

function hc_close_flatmodal( obj )
{
	var myParent = obj.closest( '.hcj-flatmodal-parent' );
	if( myParent.length > 0 ){
		var targetDiv = myParent.find('.hcj-flatmodal-container');

		myParent.children(':not(.hcj-flatmodal-closer)').show();
		targetDiv.hide();
		myParent.children('.hcj-flatmodal-closer').hide();

		var scrollInto = true;
		if( scrollInto ){
			var returnDiv = targetDiv.data('return-target');
			if( returnDiv ){
				jQuery('html, body').animate(
					{
					scrollTop: returnDiv.offset().top - 40,
					}
				);
			}
		}
	}
}

function hc_close_ajax( obj )
{
	var myParent = obj.closest( '.hcj-ajax-parent' );
	if( myParent.length > 0 ){
		var targetDiv = myParent.find('.hcj-ajax-container');
		targetDiv.hide();

		var scrollInto = true;
		if( scrollInto ){
			var returnDiv = targetDiv.data('return-target');
			if( returnDiv ){
				jQuery('html, body').animate(
					{
					scrollTop: returnDiv.offset().top - 40,
					}
				);
			}
		}
	}
}

function hc_submit_ajax( method, targetUrl, resultDiv, thisFormData )
{
	resultDiv.addClass( 'hc-loading' );

	if( targetUrl == '-referrer-' )	{
		targetUrl = resultDiv.data('targetUrl');
		if( ! targetUrl ){
			resultDiv.removeClass( 'hc-loading' );
			return false;
		}
	}

	targetUrl = hc_convert_ajax_url( targetUrl );
	jQuery.ajax({
		type: method,
		url: targetUrl,
//		dataType: "json",
		dataType: "text",
		data: thisFormData,
		success: function(data, textStatus){
			resultDiv.removeClass( 'hc-loading' );

			var is_json = true;
			try {
				data = jQuery.parseJSON( data );
			}
			catch( err ){
				is_json = false;
			}

			var is_flatmodal = resultDiv.closest(".hcj-flatmodal-container").length;
			var result_in_me = false;
			if( is_flatmodal ){
				result_in_me = true;
			}

			if( is_json ){
				if( data && data.redirect ){
					var parent_refresh = ( (data.parent_refresh !== undefined) && data.parent_refresh ) ? data.parent_refresh : [];

				/* refresh selected divs in parent */
					if( parent_refresh && (parent_refresh.length > 0) ){
						if( is_flatmodal ){
							hc_close_flatmodal( resultDiv );
						}
						else {
							hc_close_ajax( resultDiv );
						}

						parent_refresh.push('');
						for (ii = 0; ii < parent_refresh.length; ii++ ){
							var parent_refresh_class = parent_refresh[ii];
							if( parent_refresh_class.length ){
								parent_refresh_class = 'hcj-rfr-' + parent_refresh_class;
							}
							else {
								parent_refresh_class = 'hcj-rfr';
							}

							jQuery('.' + parent_refresh_class).each(
								function(index)
								{
									var thisDiv = jQuery(this);
									var src = thisDiv.data('src');

									src = hc_convert_ajax_url( src )

									thisDiv.addClass( 'hc-loading' );
									thisDiv.load( src, function(){
										thisDiv.removeClass( 'hc-loading' );
										hc_init_page( thisDiv );
									});
								});
						} 
					}
					else {
						var parent_redirect = ( (data.parent !== undefined) && data.parent ) ? 1 : 0;
						var full_parent_redirect = ( (data.parent !== undefined) && (data.parent == 2) ) ? 1 : 0;
						var force_parent_redirect = ( (data.parent !== undefined) && (data.parent == 3) ) ? 1 : 0;

						if( force_parent_redirect ){
							resultDiv.addClass( 'hc-loading' );
							src = data.redirect;
							location.href = src;
						}
						else if( full_parent_redirect ){
							resultDiv.addClass( 'hc-loading' );
							location.reload();
						}
					/* reload me with another url */
						else if( result_in_me && (! parent_redirect) ){
							var src = resultDiv.data('targetUrl');
							if( ! src )	{
								src = data.redirect;
							}
							src = data.redirect;

							if( data.redirect != '-referrer-'){
								resultDiv.data('targetUrl', data.redirect);	
							}
							hc_submit_ajax( "GET", src, resultDiv, null )
						}
					/* reload target div in main screen */
						else {
							if( is_flatmodal ){
								hc_close_flatmodal( resultDiv );
							}

							var returnDiv = resultDiv.data('return-target');
							if( returnDiv ){
								var src = returnDiv.data('src');
								returnDiv.addClass( 'hc-loading' );

								src = hc_convert_ajax_url( src )

								returnDiv.load( src, function(){
									returnDiv.removeClass( 'hc-loading' );
								});

								/* also if we have hcj-page-status divs */
								jQuery('.hcj-page-status').each(
									function(index)
									{
										var thisDiv = jQuery(this);
										var src = thisDiv.data('src');
										thisDiv.addClass( 'hc-loading' );
										thisDiv.load( src, function(){
											thisDiv.removeClass( 'hc-loading' );
										});
									});
							}
							else {
								// reload window
								location.reload();
							}
						}
					}
				}
				else if( data && data.html ){
					resultDiv.html( data.html );
					hc_init_page();
				}
				else {
					alert( 'Unrecognized JSON from ' + targetUrl );
				}
			}
			else {
				resultDiv.html( data );

			/* run inline JavaScript */
				resultDiv.find('script').each( function()
				{
					eval( jQuery(this).text() );
				});
				hc_init_page();
			}
		}
	})
	.fail( function(jqXHR, textStatus, errorThrown){
		alert( 'Error parsing JSON from ' + targetUrl );
		alert( jqXHR.responseText );
		resultDiv.removeClass( 'hc-loading' );
		})
	.always( function(){
//		resultDiv.removeClass( 'hc-loading' );
		});
}

jQuery(document).on( 'click', 'a.hcj-ajax-loader', function(e)
{
	return hc_click_ajax_loader( jQuery(this) );
});

function hc_convert_ajax_url( url )
{
	if( typeof hc_vars == 'undefined'){
		return url;
	}
	if( typeof hc_vars.link_prefix_ajax == 'undefined' ){
		return url;
	}

	/* if already there */
	if( url.substring(0, hc_vars.link_prefix_ajax.length) == hc_vars.link_prefix_ajax ){
		url = url.replace(/^https?:/,'');
		return url;
	}

	/* if not starts with regular prefix */
	if( url.substring(0, hc_vars.link_prefix_regular.length) != hc_vars.link_prefix_regular ){
		url = url.replace(/^https?:/,'');
		return url;
	}

	/* replace prefix to ajax's */
	var remain_url = url.substring(hc_vars.link_prefix_regular.length);
	var new_url = hc_vars.link_prefix_ajax + remain_url;

	/* remove protocol */
	new_url = new_url.replace(/^https?:/,'');
	return new_url;
}

jQuery(document).on( 'click', 'a.hcj-flatmodal-loader', function(e)
{
	var obj = jQuery(this);
	var targetUrl = obj.attr('href');

	if(
		( targetUrl.length > 0 ) &&
		( targetUrl.charAt(targetUrl.length-1) == '#' )
		){
		return false;
	}

	targetUrl = hc_convert_ajax_url( targetUrl );

/* search in children */
	var myParent = obj.closest( '.hcj-flatmodal-parent' );
	if( myParent.length > 0 ){
		var scrollInto = true;
		var targetDiv = myParent.find('.hcj-flatmodal-container');
		var currentUrl = targetDiv.data( 'targetUrl' );

		var markParent = obj.closest('.hcj-target');
		if( markParent.length <= 0 ){
			var markParent = obj.closest('div,li');
		}

		markParent.addClass( 'hc-loading' );
		targetDiv.data( 'targetUrl', targetUrl );
		targetDiv.data( 'mark-parent', markParent );

		var external_target = true;
		if( targetUrl.charAt(0) == '#' ){
			external_target = false;
		}

		if( external_target ){
			targetDiv.load( targetUrl, function(){
			/* run inline JavaScript */
				jQuery(this).find('script').each( function(){
					eval( jQuery(this).text() );
				});

				hc_init_page(targetDiv);

			/* hide other */
				myParent.children(':not(.hcj-flatmodal-closer)').hide();
				myParent.children('.hcj-flatmodal-closer').show();
				targetDiv.show();
				markParent.removeClass( 'hc-loading' );

				/* get some values from elements on the page: */
				var reloadTargetDiv = obj.closest('.hcj-target');
				if( reloadTargetDiv.length > 0 ){
					targetDiv.data( 'return-target', reloadTargetDiv );
				}

				if( scrollInto ){
					var closerLink = myParent.find('.hcj-flatmodal-closer');
					var animateTo = (closerLink.length > 0) ? closerLink : targetDiv;
					var animateTo = targetDiv;
					jQuery('html, body').animate({
						scrollTop: animateTo.offset().top - 40,
					});
				}
			});
		}
		else {
			targetDiv.html( jQuery(targetUrl).html() );
			hc_init_page(targetDiv);

		/* hide other */
			myParent.children(':not(.hcj-flatmodal-closer)').hide();
			myParent.children('.hcj-flatmodal-closer').show();
			targetDiv.show();
			markParent.removeClass( 'hc-loading' );

			targetDiv.data( 'return-target', jQuery(this) );

			if( scrollInto ){
				var closerLink = myParent.find('.hcj-flatmodal-closer');
				var animateTo = (closerLink.length > 0) ? closerLink : targetDiv;
				var animateTo = targetDiv;
				jQuery('html, body').animate({
					scrollTop: animateTo.offset().top - 40,
				});
			}

		}

		return false;
	}
});

jQuery(document).on( 'click', 'a.hcj-flatmodal-return-loader', function(e)
{
	var meThis = jQuery(this);
	// hc_close_flatmodal( meThis );

	var myParent = jQuery(this).closest( '.hcj-flatmodal-parent' );
	if( myParent.length > 0 ){
		var targetDiv = myParent.find('.hcj-flatmodal-container');
		targetDiv.addClass( 'hc-loading' );

		var returnDiv = targetDiv.data('return-target');
		if( returnDiv ){
			var targetUrl = jQuery(this).attr("href");
			if( ! targetUrl ){
				returnDiv.removeClass( 'hc-loading' );
				targetDiv.removeClass( 'hc-loading' );
				hc_close_flatmodal( meThis );
				return false;
			}

			if(
				( targetUrl.length > 0 ) &&
				( targetUrl.charAt(targetUrl.length-1) == '#' )
				){
				return false;
			}

			targetUrl = hc_convert_ajax_url( targetUrl )

			returnDiv.addClass( 'hc-loading' );
			returnDiv.load( targetUrl, function(){
				returnDiv.removeClass( 'hc-loading' );
				targetDiv.removeClass( 'hc-loading' );
				hc_close_flatmodal( meThis );
			});
		}
		return false;
	}
	else {
		hc_close_flatmodal( meThis );
	}
});

jQuery(document).on( 'click', 'a.hcj-flatmodal-closer', function(e)
{
	var meThis = jQuery(this);
	hc_close_flatmodal( meThis );
	return false;
});

jQuery(document).on( 'click', '.hcj-alert-dismisser', function(e)
{
	jQuery(this).closest('.hcj-alert').hide();
	return false;
});

/* submit forms by links */
jQuery(document).on( 'click', 'a.hcj-form-submit', function(event)
{
	var thisLink = jQuery( this );
	var thisForm = thisLink.closest('form');
	var myAction = thisLink.prop('hash').substr(1);

	var moreCollect = thisLink.data('collect');
	if( moreCollect ){
		var moreAppend = [];
		jQuery("input[name^='" + moreCollect + "']").each( function()
		{
			var appendValue = jQuery(this).val();
			if( 
				( jQuery(this).attr('type') != 'checkbox' )
				|| 
				( jQuery(this).is(':checked') )
				){
				moreAppend.push( appendValue );
			}
		});

		var addInput2 = jQuery("<input>").attr("type", "hidden").attr("name", moreCollect).val( moreAppend.join('-') );
		thisForm.append( addInput2 );
	}

	var addInput = jQuery("<input>").attr("type", "hidden").attr("name", "nts-action").val( myAction );
	thisForm.append( addInput );

	thisForm.submit();
	return false;
});

jQuery(document).on( 'click', 'a.hcj-target-reloader', function(event)
{
	var resultDiv = jQuery(this).closest('.hcj-target');
	if( resultDiv.length > 0 ){
		var targetUrl = resultDiv.data('src');
		targetUrl = hc_convert_ajax_url( targetUrl );

		resultDiv.addClass( 'hc-loading' );
		resultDiv.load( targetUrl, function(){
			resultDiv.removeClass( 'hc-loading' );
		});
	}
});


/*
click ajaxified links within hcj-target
the hcj-target is being reloaded with its data-src url after success
*/
jQuery(document).on( 'click', '.hcj-target a:not(.hcj-tab-toggler,.hcj-toggler,.hcj-toggle,.hcj-collapse-next,.hcj-ajax-loader,.hcj-flatmodal-loader,.hcj-parent-loader)', function(event)
{
	if( ! jQuery(this).hasClass('hcj-target-reloader2') ){
		if( jQuery(this).closest('.hcj-ajax-container').length ){
			return false;
		}
	}

	if( event.isPropagationStopped() )
		return false;

	var targetUrl = jQuery(this).attr('href');
	if(
		( targetUrl.length > 0 ) &&
			( 
			(targetUrl.charAt(targetUrl.length-1) == '#') ||
			(targetUrl.charAt(0) == '#')
			)
		){
		return false;
	}

	/* stop form from submitting normally */
	event.preventDefault(); 

	/* get some values from elements on the page: */
	var resultDiv = jQuery(this).closest('.hcj-target');
	resultDiv.data( 'return-target', resultDiv );

	hc_submit_ajax( 
		"GET", 
		targetUrl,
		resultDiv,
		null
		);

	return false;
});

/*
click ajaxified links within hcj-ajax-container
the hcj-ajax-container is being reloaded with the URL of the clicked link
*/
jQuery(document).on( 'click', '.hcj-ajax-container a:not(.hcj-tab-toggler,.hcj-ajax-loader,.hcj-flatmodal-loader,.hcj-parent-loader)', function(event)
{
	var thisLink = jQuery( this );
	var targetUrl = thisLink.attr('href');
	if(
		( targetUrl.length > 0 ) &&
			( 
			(targetUrl.charAt(targetUrl.length-1) == '#') ||
			(targetUrl.charAt(0) == '#')
			)
		){
		return false;
	}

	if( event.isPropagationStopped() )
		return false;

	var resultDiv = thisLink.closest('.hcj-ajax-container');
	if( thisLink.hasClass('hcj-ajax-parent-loader') ){
		var resultDiv = resultDiv.parents('.hcj-ajax-container');
		if( ! resultDiv.length ){
			return true;
		}
	}

	/* stop form from submitting normally */
	event.preventDefault();

	if(
		( ! thisLink.hasClass('hcj-confirm') ) && 
		( ! thisLink.hasClass('hcj-confirm') )
		){
		resultDiv.data( 'targetUrl', targetUrl );
	}

	hc_submit_ajax(
		"GET",
		targetUrl,
		resultDiv,
		null
		);

	return false;
});

/*
post ajaxified forms within hcj-container
the hcj-target is being reloaded with its data-src url after success
*/
jQuery(document).on( 'submit', '.hcj-target form:not(.hcj-form-external)', function(event)
{
	if( jQuery(this).closest('.hcj-ajax-container').length ){
		return false;
	}

	/* stop form from submitting normally */
	event.preventDefault(); 
	/* get some values from elements on the page: */
	var thisForm = jQuery( this );
	var thisFormData = thisForm.serialize();

	var targetUrl = thisForm.attr( 'action' );
	var resultDiv = thisForm.closest('.hcj-target');
	resultDiv.data( 'return-target', resultDiv );

	/* Send the data using post and put the results in a div */
	hc_submit_ajax(
		"POST",
		targetUrl,
		resultDiv,
		thisFormData
		);
	return false;
});

jQuery(document).on( 'click', '.hcj-action-setter', function(event)
{
	var thisForm = jQuery(this).closest('form');
	var actionFieldName = 'action';
	var actionValue = jQuery(this).attr('name');

	thisForm.find("input[name='" + actionFieldName + "']").each( function(){
		jQuery(this).val( actionValue );
	});
});

jQuery(document).on( 'submit', '.hcj-ajax-container form:not(.hcj-form-external)', function(event)
{
	/* stop form from submitting normally */
	event.preventDefault(); 
	/* get some values from elements on the page: */
	var thisForm = jQuery( this );
	var thisFormData = thisForm.serialize();
	
	var targetUrl = thisForm.attr('action');
	var resultDiv = thisForm.closest('.hcj-ajax-container');

	/* Send the data using post and put the results in a div */
	hc_submit_ajax(
		"POST",
		targetUrl,
		resultDiv,
		thisFormData
		);
	return false;
});

/*
this displays more info divs for radio choices
*/
jQuery(document).on( 'change', '.hcj-radio-more-info', function(event)
{
	// jQuery('.hcj-radio-info').hide();
	var total_container = jQuery( this ).closest('.hcj-radio-info-container');
	total_container.find('.hcj-radio-info').hide();

	var my_container = jQuery( this ).closest('label');
	var my_info = my_container.find('.hcj-radio-info');
	my_info.show();
});

/* toggle */
jQuery(document).on('click', '.hcj-toggle', function(e)
{
	var this_target_id = jQuery(this).data('target');
	if( this_target_id.length > 0 ){
		this_target = jQuery(this_target_id);
		if( this_target.is(':visible') ){
			this_target.hide();
		}
		else {
			this_target.show();
		}
	}
	return false;
});

/* tab toggle */
jQuery(document).on('click', '.hcj-tab-toggler', function(e)
{
	var total_parent = jQuery(this).closest('.hcj-tabs');
	var menu_parent = total_parent.find('.hcj-tab-links');;
	var panes_parent = total_parent.find('.hcj-tab-content');

	var new_tab_id = jQuery(this).data('toggle-tab');
	panes_parent.find('.hcj-tab-pane').hide();
	// menu_parent.find('li').removeClass('hc-active');
	menu_parent.find('a').removeClass('hc-active');

	panes_parent.find('[data-tab-id=' + new_tab_id + ']').show();
	// jQuery(this).parent('li').addClass('hc-active');
	jQuery(this).addClass('hc-active');

	jQuery(this).trigger({
		type: 'shown.hc.tab'
	});

	return false;
});

/* collapse next */
jQuery(document).on('click', '.hcj-collapse-next,[data-toggle=collapse-next]', function(e)
{
	var this_target = jQuery(this).closest('.hcj-collapse-panel').children('.hcj-collapse');

	if( this_target.is(':visible') ){
		this_target.hide();
		this_target.removeClass('hcj-open');
		jQuery(this).trigger({
			type: 'hidden.hc.collapse'
		});
	}
	else {
		this_target.show();
		this_target.addClass('hcj-open');
		jQuery(this).trigger({
			type: 'shown.hc.collapse'
		});

		if( jQuery(this).hasClass('hcj-collapser-hide')){
			jQuery(this).closest('li').hide();
		}
	}
//	this_target.collapse('toggle');

	if( jQuery(this).attr('type') != 'checkbox' ){
		/* scroll into view */
//		var this_parent = jQuery(this).parents('.collapse-panel');
//		this_parent[0].scrollIntoView();
		return false;
	}
	else {
		return true;
	}
});

/* collapse other */
jQuery(document).on('click', '.hcj-collapser', function(e)
{
	// var targetUrl = jQuery(this).attr('href');
	var targetUrl = jQuery(this).data('target');
	if(
		( targetUrl.length > 0 ) &&
		( targetUrl.charAt(targetUrl.length-1) == '#' )
		){
		return false;
	}

	if( targetUrl.charAt(0) != '#' ){
		targetUrl = '#' + targetUrl
	}

	var this_target = jQuery(targetUrl);

	if( this_target.is(':visible') ){
		this_target.hide();
		this_target.removeClass('hcj-open');
		jQuery(this).trigger({
			type: 'hidden.hc.collapse'
		});
	}
	else {
		this_target.show();
		this_target.addClass('hcj-open');
		jQuery(this).trigger({
			type: 'shown.hc.collapse'
		});
	}
//	this_target.collapse('toggle');
	if( jQuery(this).attr('type') != 'checkbox' ){
		return false;
	}
	else {
		return true;
	}
});

/* collapse other */
jQuery(document).on('click', '.hcj-collapse-closer', function(e)
{
	var this_target = jQuery(this).closest('.hcj-collapse');

	if( this_target.is(':visible') ){
		this_target.hide();
		this_target.removeClass('in');
		jQuery(this).trigger({
			type: 'hidden.hc.collapse'
		});
	}
	else {
		this_target.show();
		this_target.addClass('in');
		jQuery(this).trigger({
			type: 'shown.hc.collapse'
		});
	}

	if( jQuery(this).attr('type') != 'checkbox' ){
		return false;
	}
	else {
		return true;
	}
});

jQuery(document).on('click', '.hcj-dropdown-menu select', function()
{
	return false;
});

jQuery(document).on( 'click', 'a.hcj-toggler', function(event)
{
	jQuery('.hcj-toggled').toggle();
	return false;
});

jQuery(document).on('change', '.hcj-collector-wrap input.hcj-collect-me', function(event){
	var my_val = jQuery(this).val();
	var me_remove = ( jQuery(this).is(":checked") ) ? 0 : 1;
	var input_name = jQuery(this).attr('name');

	/* find an input of the same name in the collector form */
	var collector_form = jQuery(this).closest('.hcj-collector-wrap').find('form.hcj-collector-form');
	var collector_input = collector_form.find("input[name^='" + input_name + "']");

	if( collector_input.length ){
		var current_value = collector_input.val();
		if( current_value.length ){
			current_value = current_value.split('|');
		}
		else {
			current_value = [];
		}

		var my_pos = jQuery.inArray(my_val, current_value);

	/* remove */
		if( me_remove ){
			if( my_pos != -1 ){
				current_value.splice(my_pos, 1);
			}
		}
	/* add */
		else {
			if( my_pos == -1 ){
				current_value.push(my_val);
			}
		}

		current_value = current_value.join('|');
		collector_input.val( current_value );
	}
});

jQuery(document).on( 'click', '.hcj-all-checker', function(event)
{
	var thisLink = jQuery( this );
	var firstFound = false;
	var whatSet = true;

	var moreCollect = thisLink.data('collect');
	if( moreCollect ){
		var myParent = thisLink.closest('.hcj-collector-wrap');
		if( myParent.length > 0 )
			myParent.first();
		else
			myParent = jQuery('#nts');

		myParent.find("input[name^='" + moreCollect + "']").each( function()
		{
			if( 
				( jQuery(this).attr('type') == 'checkbox' )
				){
				if( ! firstFound ){
					whatSet = ! this.checked;
					firstFound = true;
				}
				// this.checked = whatSet;
				jQuery(this)
					.prop("checked", whatSet)
					.change()
					;
			}
		});
	}

	if(
		( thisLink.prop('tagName').toLowerCase() == 'input' ) &&
		( thisLink.attr('type').toLowerCase() == 'checkbox' )
		){
		return true;
	}
	else {
		return false;
	}
});

/* color picker */
jQuery(document).on('click', 'a.hcj-color-picker-selector', function(event)
{
	var my_value = jQuery(this).data('color');

	var my_form = jQuery(this).closest('.hcj-color-picker');
	my_form.find('.hcj-color-picker-value').val( my_value );
	my_form.find('.hcj-color-picker-display').css('background-color', my_value);

	/* close collapse */

	
	return false;
});




/* todo: move it to shiftcontroller only file */
jQuery(document).on('click', 'a.hcj-shift-templates', function(event)
{
	jQuery(this).closest('form').find('[name=time_start]').val( jQuery(this).data('start') );
	jQuery(this).closest('form').find('[name=time_end]').val( jQuery(this).data('end') );
	jQuery(this).closest('form').find('[name=lunch_break]').val( jQuery(this).data('lunch-break') );
	jQuery(this).closest('form').find('[name=time_start_display]').val( jQuery(this).data('start-display') );
	jQuery(this).closest('form').find('[name=time_end_display]').val( jQuery(this).data('end-display') );

	jQuery(this).closest('.hcj-dropdown').find('.hcj-dropdown-toggle').dropdown('toggle');
	return false;
});

function hc_has_class(element, cls)
{
	return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1;
}

function hc_print_page()
{
	var content = document.getElementById("nts").innerHTML;
	var i;

	var new_nts = jQuery('#nts').clone();
	new_nts.find('.hc-no-print,.hc-hidden-print').remove();
	new_nts.find('script').remove();
	var content = new_nts.html();

	var head = document.getElementsByTagName("head")[0].innerHTML;

	var new_head = jQuery('head').clone();
	new_head.children('script').remove();
	var head = new_head.html();

	var myWindow = window.open('','','');
	myWindow.document.write("<html><head>"+head+"<style></style></head><body><div id='nts'>"+content+"</div><script language='Javascript'>window.print();</script></body></html>");
}

function hc_init_page( where )
{
	if( typeof where !== 'undefined' ){
	}
	else {
		where = jQuery("#nts");
	}

	where.find('.hcj-radio-more-info:checked').each( function(){
		var my_container = jQuery( this ).closest('label');
		var my_info = my_container.find('.hcj-radio-info');
		my_info.show();
	});


	if( where.find('.hc-datepicker').length ){
		where.find('.hc-datepicker').hc_datepicker({
			})
			.on('changeDate', function(ev)
				{
				var dbDate = 
					ev.date.getFullYear() 
					+ "" + 
					("00" + (ev.date.getMonth()+1) ).substr(-2)
					+ "" + 
					("00" + ev.date.getDate()).substr(-2);

			// remove '_display' from end
				var display_id = jQuery(this).attr('id');
				var display_suffix = '_display';
				var value_id = display_id.substr(0, (display_id.length - display_suffix.length) );

				jQuery(this).closest('form').find('#' + value_id)
					.val(dbDate)
					.trigger('change')
					;
				});
	}

	if (typeof hc_init_page2 !== 'undefined' && typeof hc_init_page2 === 'function'){
		hc_init_page2();
	}

	jQuery('[name=date_recurring]').val( "single" );
	jQuery('a.hcj-tab-toggler').on('shown.hc.tab', function(e)
	{
		var active_tab = jQuery(this).data('toggle-tab');
		jQuery(this).closest('form').find('[name=date_recurring]').val( active_tab );
	});
}

jQuery(document).ready( function()
{
	hc_init_page();

	/* add icon for external links */
	// jQuery('#nts a[target="_blank"]').append( '<i class="fa fa-fw fa-external-link"></i>' );

	jQuery('#nts a[target="_blank"]').each(function(index){
		var my_icon = '<i class="fa fa-fw fa-external-link"></i>';
		var common_link_parent = jQuery(this).closest('.hcj-common-link-parent');
		if( common_link_parent.length > 0 ){
			// common_link_parent.prepend(my_icon);
		}
		else {
			jQuery(this).append(my_icon);
		}
	});

/*
	jQuery('#nts a[target="_blank"]')
		.attr('style', 'position: relative; overflow: hidden;')
		.append( '<i class="fa fa-fw fa-external-link" style="position: absolute; top: 0; right: 0; border: red 1px solid;"></i>' )
		;
*/
	/* scroll into view */
	if ( typeof nts_no_scroll !== 'undefined' ){
		// no scroll
	}
	else {
		document.getElementById("nts").scrollIntoView();	
	}

/*
	jQuery('html, body').animate(
	{
		scrollTop: jQuery("#nts").offset().top - 20,
	}, 0 );
*/

	/* auto dismiss alerts */
	jQuery('#nts .hcj-auto-dismiss').delay(4000).slideUp(200, function(){
		// jQuery('#nts .hcj-auto-dismiss .alert').alert('close');
	});
});

var hc = {};

/* ========================================================================
* Bootstrap: dropdown.js v3.3.5
* http://getbootstrap.com/javascript/#dropdowns
* ========================================================================
* Copyright 2011-2015 Twitter, Inc.
* Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)

* Modified for hcj prefixes
* ======================================================================== */

+function ($) {
	'use strict';

	// DROPDOWN CLASS DEFINITION
	// =========================

  var backdrop = '.hcj-dropdown-backdrop'
  // var toggle   = '[data-toggle="dropdown"]'
  var toggle   = '.hcj-dropdown-toggle'
  var Dropdown = function (element) {
    $(element).on('click.bs.dropdown', this.toggle)
  }

  Dropdown.VERSION = '3.3.5'

  function getParent($this) {
    var selector = $this.attr('data-target')

    if (!selector) {
      selector = $this.attr('href')
      selector = selector && /#[A-Za-z]/.test(selector) && selector.replace(/.*(?=#[^\s]*$)/, '') // strip for ie7
    }

    var $parent = selector && $(selector)

    return $parent && $parent.length ? $parent : $this.parent()
  }

  function clearMenus(e) {
    if (e && e.which === 3) return
    $(backdrop).remove()
    $(toggle).each(function () {
      var $this         = $(this)
      var $parent       = getParent($this)
      var relatedTarget = { relatedTarget: this }

      if (!$parent.hasClass('hcj-open')) return

      if (e && e.type == 'click' && /input|textarea/i.test(e.target.tagName) && $.contains($parent[0], e.target)) return

      $parent.trigger(e = $.Event('hide.bs.dropdown', relatedTarget))

      if (e.isDefaultPrevented()) return

      $this.attr('aria-expanded', 'false')
      $parent.removeClass('hcj-open').trigger('hidden.bs.dropdown', relatedTarget)
    })
  }

  Dropdown.prototype.toggle = function (e) {
    var $this = $(this)

    if ($this.is('.disabled, :disabled')) return

    var $parent  = getParent($this)
    var isActive = $parent.hasClass('hcj-open')

    clearMenus()
    if (!isActive) {
		if ('ontouchstart' in document.documentElement && !$parent.closest('.navbar-nav').length) {
        // if mobile we use a backdrop because click events don't delegate
        $(document.createElement('div'))
          .addClass('hcj-dropdown-backdrop')
          .insertAfter($(this))
          .on('click', clearMenus)
      }

      var relatedTarget = { relatedTarget: this }
      $parent.trigger(e = $.Event('show.bs.dropdown', relatedTarget))

      if (e.isDefaultPrevented()) return

      $this
        .trigger('focus')
        .attr('aria-expanded', 'true')

		$parent
        .toggleClass('hcj-open')
        .trigger('shown.bs.dropdown', relatedTarget)
    }

    return false
  }

  Dropdown.prototype.keydown = function (e) {
    if (!/(38|40|27|32)/.test(e.which) || /input|textarea/i.test(e.target.tagName)) return

    var $this = $(this)

    e.preventDefault()
    e.stopPropagation()

    if ($this.is('.disabled, :disabled')) return

    var $parent  = getParent($this)
    var isActive = $parent.hasClass('hcj-open')

    if (!isActive && e.which != 27 || isActive && e.which == 27) {
      if (e.which == 27) $parent.find(toggle).trigger('focus')
      return $this.trigger('click')
    }

    var desc = ' li:not(.disabled):visible a'
    var $items = $parent.find('.hcj-dropdown-menu' + desc)

    if (!$items.length) return

    var index = $items.index(e.target)

    if (e.which == 38 && index > 0)                 index--         // up
    if (e.which == 40 && index < $items.length - 1) index++         // down
    if (!~index)                                    index = 0

    $items.eq(index).trigger('focus')
  }


  // DROPDOWN PLUGIN DEFINITION
  // ==========================

  function Plugin(option) {
    return this.each(function () {
      var $this = $(this)
      var data  = $this.data('bs.dropdown')

      if (!data) $this.data('bs.dropdown', (data = new Dropdown(this)))
      if (typeof option == 'string') data[option].call($this)
    })
  }

  var old = $.fn.dropdown

  $.fn.dropdown             = Plugin
  $.fn.dropdown.Constructor = Dropdown


  // DROPDOWN NO CONFLICT
  // ====================

  $.fn.dropdown.noConflict = function () {
    $.fn.dropdown = old
    return this
  }


  // APPLY TO STANDARD DROPDOWN ELEMENTS
  // ===================================

  $(document)
    .on('click.bs.dropdown.data-api', clearMenus)
    .on('click.bs.dropdown.data-api', '.hcj-dropdown form', function (e) { e.stopPropagation() })
    .on('click.bs.dropdown.data-api', toggle, Dropdown.prototype.toggle)
    .on('keydown.bs.dropdown.data-api', toggle, Dropdown.prototype.keydown)
    .on('keydown.bs.dropdown.data-api', '.hcj-dropdown-menu', Dropdown.prototype.keydown)

}(jQuery);
