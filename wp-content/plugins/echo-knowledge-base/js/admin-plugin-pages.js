jQuery(document).ready(function($) {

	var epkb = $( '#ekb-admin-page-wrap' );

	// KBs DROPDOWN - reload on change
	$( '#epkb-list-of-kbs' ).on( 'change', function(e) {
		// var what = e.target.value;
		var kb_admin_url = $(this).find(":selected").data('kb-admin-url');
		if ( kb_admin_url ) {
			window.location.href = kb_admin_url;
		}
	});

	/* Tabs ----------------------------------------------------------------------*/
	(function(){

		/**
		 * Toggles Tabs
		 *
		 * The HTML Structure for this is as follows:
		 * 1. tab_nav_container must be the main ID or class element for the navigation tabs containing the tabs.
		 *    Those nav items must have a class of nav_tab.
		 *
		 * 2. tab_panel_container must be the main ID or class element for the panels. Those panel items must have
		 *    a class of ekb-admin-page-tab-panel
		 *
		 * @param tab_nav_container  ( ID/class containing the Navs )
		 * @param tab_panel_container ( ID/class containing the Panels
		 */
	   (function(){
			function tab_toggle( tab_nav_container, tab_panel_container ){

				epkb.find( tab_nav_container+ ' > .nav_tab' ).on( 'click', function(){

					//Remove all Active class from Nav tabs
					epkb.find(tab_nav_container + ' > .nav_tab').removeClass('active');

					//Add Active class to clicked Nav
					$(this).addClass('active');

					//Remove Class from the tab panels
					epkb.find(tab_panel_container + ' > .ekb-admin-page-tab-panel').removeClass('active');

					//Set Panel active
					var number = $(this).index() + 1;
					epkb.find(tab_panel_container + ' > .ekb-admin-page-tab-panel:nth-child( ' + number + ' ) ').addClass('active');
				});
			}

			tab_toggle( '.add_on_container .epkb-main-nav > .epkb-admin-pages-nav-tabs', '#add_on_panels' );
			tab_toggle( '.epkb-main-nav > .epkb-admin-pages-nav-tabs', '#main_panels' );
			tab_toggle( '#help_tabs_nav', '#help_tab_panel' );
			tab_toggle( '#new_features_tabs_nav', '#new_features_tab_panel' );
		})();

	})();


	/* Misc ----------------------------------------------------------------------*/
	(function(){

		// TOGGLE DEBUG
		epkb.find( '#epkb_toggle_debug' ).on( 'click', function() {

			// Remove old messages
			$('.eckb-top-notice-message').html('');

			var postData = {
				action: 'epkb_toggle_debug',
				_wpnonce_epkb_toggle_debug: $('#_wpnonce_epkb_toggle_debug').val()
			};

			var msg;

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: ajaxurl,
				data: postData,
				beforeSend: function (xhr)
				{
					epkb_loading_Dialog( 'show', epkb_vars.changing_debug );
				}
			}).done(function (response)
			{
				response = ( response ? response : '' );
				if ( response.error || typeof response.message === 'undefined' ) {
					//noinspection JSUnresolvedVariable,JSUnusedAssignment
					msg = response.message ? response.message : epkb_admin_notification('', epkb_vars.reload_try_again, 'error');
					return;
				}

				window.location.href = window.location.href + '&epkb-tab=debug';

			}).fail( function ( response, textStatus, error )
			{
				//noinspection JSUnresolvedVariable
				msg = ( error ? ' [' + error + ']' : epkb_vars.unknown_error );
				//noinspection JSUnresolvedVariable
				msg = epkb_admin_notification(epkb_vars.error_occurred + '. ' + epkb_vars.msg_try_again, msg, 'error');
			}).always(function ()
			{

				epkb_loading_Dialog( 'remove', '' );

				if ( msg ) {
					$('.eckb-top-notice-message').replaceWith(msg);
					$( "html, body" ).animate( {scrollTop: 0}, "slow" );
				}
			});
		});

		// ADD-ON PLUGINS + OUR OTHER PLUGINS - PREVIEW POPUP
		 (function(){
			//Open Popup larger Image
			epkb.find( '.featured_img' ).on( 'click', function( e ){

				e.preventDefault();
				e.stopPropagation();

				epkb.find( '.image_zoom' ).remove();

				var img_src;
				var img_tag = $( this ).find( 'img' );
				if ( img_tag.length > 1 ) {
					img_src = $(img_tag[0]).is(':visible') ? $(img_tag[0]).attr('src') :
							( $(img_tag[1]).is(':visible') ? $(img_tag[1]).attr('src') : $(img_tag[2]).attr('src') );

				} else {
					img_src = $( this ).find( 'img' ).attr( 'src' );
				}

				$( this ).after('' +
					'<div id="epkb_image_zoom" class="image_zoom">' +
					'<img src="' + img_src + '" class="image_zoom">' +
					'<span class="close icon_close"></span>'+
					'</div>' + '');

				//Close Plugin Preview Popup
				$('html, body').bind('click.epkb', function(){
					$( '#epkb_image_zoom' ).remove();
					$('html, body').unbind('click.epkb');
				});
			});
		})();

		// Show Character count on Tab Name input field and warning message
	/*	$( '#kb_name' ).on( 'keyup', function(){
			var value   = $( this ).val().length;
			var limit   = 25;
			var result  = limit - value;
			$( '#character_value' ).remove();

			if( result < 0 ) {
				//noinspection JSUnresolvedVariable
				$( this ).after( '<div id="character_value" class="input_error"><p>' + epkb_vars.reduce_name_size + '</p></div>' );
			}
		}); */

		//Info Icon for Licenses
		$( '#add_on_panels' ).on( 'click', '.ep_font_icon_info', function(){

			$( this ).parent().find( '.ep_font_icon_info_content').toggle();

		});
	})();

	// Clear Messages after set time
	(function(){

		var epkb_timeout;
		if( $('.eckb-bottom-notice-message' ).length > 0 ) {
			clearTimeout(epkb_timeout);

			//Add fadeout class to notice after set amount of time has passed.
			epkb_timeout = setTimeout(function () {
				var bottom_message = $('body').find('.eckb-bottom-notice-message');
				if ( bottom_message.length ) {
					bottom_message.addClass('fadeOutDown');
				}
			} , 10000);
		}
	})();

	// Close Button Message if Close Icon clicked
	$( 'body' ).find( '.epkb-close-notice' ).on( 'click', function(){
		$( this ).parent().addClass( 'fadeOutDown' );
	});


	/* Dialogs --------------------------------------------------------------------*/

	/**
	  * Displays a Center Dialog box with a loading icon and text.
	  *
	  * This should only be used for indicating users that loading or saving or processing is in progress, nothing else.
	  * This code is used in these files, any changes here must be done to the following files.
	  *   - admin-plugin-pages.js
	  *   - admin-kb-config-scripts.js
	  *   - admin-kb-wizard-script.js
	  *
	  * @param  {string}    displayType     Show or hide Dialog initially. ( show, remove )
	  * @param  {string}    message         Optional    Message output from database or settings.
	  *
	  * @return {html}                      Removes old dialogs and adds the HTML to the end body tag with optional message.
	  *
	  */
	function epkb_loading_Dialog( displayType, message ){

		if( displayType === 'show' ){

			let output =
				'<div class="epkb-admin-dialog-box-loading">' +

				//<-- Header -->
				'<div class="epkb-admin-dbl__header">' +
				'<div class="epkb-admin-dbl-icon epkbfa epkbfa-hourglass-half"></div>'+
				(message ? '<div class="<div class="epkb-admin-dbl-text">' + message + '</div>' : '' ) +
				'</div>'+

				'</div>' +
				'<div class="epkb-admin-dialog-box-overlay"></div>';

			//Add message output at the end of Body Tag
			$( 'body' ).append( output );
		}else if( displayType === 'remove' ){

			// Remove loading dialogs.
			$( '.epkb-admin-dialog-box-loading' ).remove();
			$( '.epkb-admin-dialog-box-overlay' ).remove();
		}

	}


	// HELP ICON DIALOG
	// open dialog but re-center when loading finished so that it stays in the center of the screen
	var epkb_help_dialog = $("#epkb-dialog-info-icon").dialog(
		{
			resizable: false,
			autoOpen: false,
			modal: true,
			buttons: {
				Ok: function ()
				{
					$( this ).dialog( "close" );
				}
			},
			close: function ()
			{
				$('#epkb-dialog-info-icon-msg').html();
			}
		}
	);
	epkb.find( '.ekb-admin-page-tab-panel, .epkb-config-sidebar-options' ).on('click', '.info-icon',  function () {
		var has_image = false;
		var img = '';
		var title = $( this ).parent().find( '.label' ).text();
		title = ( title ? title : '' );

		var msg = $( this ).find( 'p' ).html();
		if( msg )
		{
			var arrayOfStrings = msg.split('@');
			msg = arrayOfStrings[0] ? arrayOfStrings[0] : epkb_vars.help_text_coming;
			if ( arrayOfStrings[1] ) {
				has_image = true;
				img = '<img class="epkb-help-image" src="' + arrayOfStrings[1] + '">';
			}
		} else {
			msg = epkb_vars.help_text_coming;
		}

		$('#epkb-dialog-info-icon-msg').html('<p>' + msg + '</p><br/>' + img);

		epkb_help_dialog.dialog( {
			title: title,
			width: (has_image ? 1000 : 400),
			maxHeight: (has_image ? 750 : 300),
			open: function ()
			{
				// reposition dialog after image loads
				$("#epkb-dialog-info-icon").find('.epkb-help-image').one("load", function ()
				{
					epkb_help_dialog.dialog('option', { position: { my: "center", at: "center", of: window } } );
					//  $(this).dialog({position: {my: "center", at: "center", of: window}});
				});

				// close dialog if user clicks outside of it
				$( '.ui-widget-overlay' ).bind( 'click', function ()
				{
					$("#epkb-dialog-info-icon").dialog('close')
				});
			}
		}).dialog('open');
	});

	// AJAX DIALOG USED BY KB CONFIGURATION AND SETTINGS PAGES
	$('#epkb-ajax-in-progress').dialog({
		resizable: false,
		height: 70,
		width: 200,
		modal: false,
		autoOpen: false
	}).hide();

	// ToolTip
	epkb.on( 'click', '.eckb-tooltip-button', function(){
		$( this ).parent().find( '.eckb-tooltip-contents' ).fadeToggle();
	});


	/* KB Analytics Page -------------------------------------------------------------*/

	var analytics_container = $( '.epkb-analytics-container' );

	//When Top Nav is clicked on show it's content.
	analytics_container.find( '.page-icon' ).on( 'click', function(){

		//Reset ( Hide all content, remove all active classes )
		analytics_container.find( '.eckb-config-content' ).removeClass( 'epkb-active-content' );
		analytics_container.find( '.eckb-nav-section' ).removeClass( 'epkb-active-nav' );

		//Get ID of Icon
		var id = $( this ).attr( 'id' );

		//Target Content from icon ID
		analytics_container.find( '#' + id + '-content').addClass( 'epkb-active-content' );

		//Set this Nav to be active
		analytics_container.find( this ).parents( '.eckb-nav-section' ).addClass( 'epkb-active-nav' )

	});

	// SHOW INFO MESSAGES
	function epkb_admin_notification( $title, $message , $type ) {
		return '<div class="eckb-top-notice-message">' +
			'<div class="contents">' +
			'<span class="' + $type + '">' +
			($title ? '<h4>' + $title + '</h4>' : '' ) +
			($message ? $message : '') +
			'</span>' +
			'</div>' +
			'</div>';
	}
	
	/**
	 * Accordion for the options 
	 */
	$('body').on('click', '.eckb-wizard-accordion .eckb-wizard-option-heading', function(){
		var wrap = $(this).closest('.eckb-wizard-accordion');
		var currentItem = $(this).closest('.eckb-wizard-accordion__body-content');
		var isCurrentActive = currentItem.hasClass('eckb-wizard-accordion__body-content--active');

		wrap.find('.eckb-wizard-accordion__body-content').removeClass('eckb-wizard-accordion__body-content--active');
		
		if (!isCurrentActive) {
			currentItem.addClass('eckb-wizard-accordion__body-content--active');
		}
		
	});

	$('body').on('click', '#eckb-wizard-main-page-preview a, .epkb-wizard-theme-panel-container a, #eckb-wizard-article-page-preview a', false);
	
	/** 
	 * Categories icons JS
	 */
	 
	if ($('.epkb-categories-icons').length) {
		// Tabs 
		$('.epkb-categories-icons__button').click(function(){
			
			if ($(this).hasClass('epkb-categories-icons__button--active')) {
				return;
			}
			
			$('.epkb-categories-icons__button').removeClass('epkb-categories-icons__button--active');
			$(this).addClass('epkb-categories-icons__button--active');
			
			
			$('.epkb-categories-icons__tab-body').slideUp('fast');
			
			var val = $(this).data('type');
			
			if ( $('.epkb-categories-icons__tab-body--' + val).length ) {
				$('.epkb-categories-icons__tab-body--' + val).slideDown('fast');
			}
			
			$('#epkb_head_category_icon_type').val(val); 
		});
		
		// Icon Save 
		$('.epkb-icon-pack__icon').click(function(){
			$('.epkb-icon-pack__icon').removeClass('epkb-icon-pack__icon--checked');
			$(this).addClass('epkb-icon-pack__icon--checked');
			$('#epkb_head_category_icon_name').val($(this).data('key'));
		});
		
		// Image save 
		$('.epkb-category-image__button').click(function(e){
			e.preventDefault();
 
    		var button = $(this),
    		custom_uploader = wp.media({
				title: button.data('title'),
					library : {
						type : 'image'
					},
					multiple: false 
				}).on('select', function() { 
					var attachment = custom_uploader.state().get('selection').first().toJSON();
					
					$('#epkb_head_category_icon_image').val(attachment.id);
					$('.epkb-category-image__button').removeClass('epkb-category-image__button--no-image');
					$('.epkb-category-image__button').addClass('epkb-category-image__button--have-image');
					$('.epkb-category-image__button').css({'background-image' : 'url('+attachment.url+')'});
				})
				.open();
		});
		
		// Show/Hide Categories block depends on category parent 
		$('#parent').change(function(){
			
			var category_level;
			var option;
			var select = $(this);
			var template = $('#epkb_head_category_template').val();
			var hide_block = false;
			
			select.find('option').each(function(){
				if ( $(this).val() == select.val() ) {
					option = $(this);
				}
			});
			
			if ( option.val() == '-1' ) {
				category_level = 1;
			} else if ( option.hasClass('level-0') ) {
				category_level = 2;
			} else {
				category_level = 3;
			}
			
			if ( template == 'Tabs' ) {
				if ( category_level !== 2 ) {
					hide_block = true;
				}
			} else if ( template == 'Sidebar' ) {
				hide_block = true;
			} else { 
				// all else layouts 
				if ( category_level > 1 ) {
					hide_block = true;
				}
			}
			
			if ( hide_block ) {
				$('.epkb-categories-icons').hide();
				$('.epkb-categories-icons+.epkb-term-options-message').show();
			} else {
				$('.epkb-categories-icons').show();
				$('.epkb-categories-icons+.epkb-term-options-message').hide();
			}
			
		});
		
		function epkb_reset_categories_icon_box() {
			$('#epkb_font_icon').click();
			$('#epkb_head_category_thumbnail_size').val( $('#epkb_head_category_thumbnail_size').find('option').eq(0).val() );
			$('.epkb-category-image__button').addClass('epkb-category-image__button--no-image');
			$('.epkb-category-image__button').removeClass('epkb-category-image__button--have-image');
			$('.epkb-category-image__button').css({'background-image' : ''});
			$('#epkb_head_category_icon_image').val(0);
			$('div[data-key=ep_font_icon_document]').click();
		}
		
		// look when new category was added 
		$( document ).ajaxComplete(function( event, xhr, settings ) {
			
			if ( ! settings ) {
				return;
			}
			
			let data = settings.data.split('&');
			let i;
			
			for (i = 0; i < data.length; i++) {
				sParameterName = data[i].split('=');

				if (sParameterName[0] === 'action' && sParameterName[1] === 'add-tag' ) {
					epkb_reset_categories_icon_box();
					
					$("html, body").animate({ scrollTop: $('.wp-heading-inline').offset().top }, 300);
				}
			}
		});
	}
	
	/** KB Manage Page */ 
	if ( $('.epkb-manage-kb-container').length ) {
		
		// Main tabs (left panel)
		$('.epkb-manage-tabs__button__title').click(function(){
			let kb_id = $(this).data('kb_id');
			
			if ( kb_id == 'undefined' || !kb_id ) {
				return;
			}
			
			$('.epkb-manage-tabs__button').removeClass('active');
			$(this).parent().addClass('active');
			
			$('.epkb-manage-content').removeClass('active');
			$( $(this).data('target') ).addClass('active');
			
			return false;
		});
		
		// Top panel tabs 
		$('.epkb-manage-content__tab-button').click(function(){
			$(this).parent().find('.epkb-manage-content__tab-button').removeClass('active');
			$(this).addClass('active');
			
			$(this).closest('.epkb-manage-content').find('.epkb-manage-content__tab').removeClass('active');
			$( $(this).data('target') ).addClass('active');
			
			return false;
		});
		
		// Delete KB dialog
		$('.epkb-delete-kbs .error-btn').click(function(){
			$(this).closest('.epkb-delete-kbs').find( '.epkb-dialog-box-form' ).toggleClass( 'epkb-dialog-box-form--active' );
			return false;
		});
		
		$('.epkb-dbf__close, .epkb-dbf__footer__cancel__btn').click(function(){
			$(this).closest( '.epkb-dialog-box-form' ).toggleClass( 'epkb-dialog-box-form--active' );
		});
		
		$('.epkb-delete-kbs .epkb-dbf__footer__accept__btn').click(function(){
			if ( $(this).closest('form').length ) {
				$(this).closest('form').submit();
			}
		});


		//MKB Import Enable/Disable

		var importBtn = $('.epkb-import-kbs input[type=submit]');
		var importFile = $('input[name=import_file]');

		importFile.change(function(){
				if($(this).val()) {
					importBtn.attr('disabled', false);
					importBtn.removeClass('error-btn');
					importBtn.addClass('success-btn');
				}
			}
		);
	}
});