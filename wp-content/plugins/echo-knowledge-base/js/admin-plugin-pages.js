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

            tab_toggle( '#welcome_tab_nav' , '#welcome_panel_container' );
            tab_toggle( '.add_on_container .epkb-main-nav > .epkb-admin-pages-nav-tabs', '#add_on_panels' );
            tab_toggle( '.epkb-main-nav > .epkb-admin-pages-nav-tabs', '#main_panels' );
            tab_toggle( '#help_tabs_nav', '#help_tab_panel' );

        })();

    })();


    /* Misc ----------------------------------------------------------------------*/
    (function(){

        // SEND FEEDBACK
        epkb.find( '#epkb_send_feedback' ).on('click', function (e) {

            epkb.find('.required').remove();

            // Remove old messages
            $('.eckb-top-notice-message').html('');

            e.preventDefault();  // do not submit the form
            var feedback = epkb.find( '#your_feedback' ).val().trim();
            var email = epkb.find( '#your_email' ).val().trim();
            var name = epkb.find( '#your_name' ).val().trim();
            var valid = true;

            if ( ! feedback  ) {
                //noinspection JSUnresolvedVariable
                epkb.find( '#your_feedback' ).after('<p class="required notification"><span class="error">* ' + epkb_vars.input_required + '</span> </p>');
                valid = false;
            }
            if ( ! email ) {
                //noinspection JSUnresolvedVariable
                epkb.find( '#your_email' ).after('<p class="required notification"><span class="error">* ' + epkb_vars.input_required + '</span> </p>');
                valid = false;
            }
            if ( ! name ) {
                //noinspection JSUnresolvedVariable
                epkb.find( '#your_name' ).after('<p class="required notification"><span class="error">* ' + epkb_vars.input_required + '</span> </p>');
                valid = false;
            }
            if( valid == false ){
                return;
            }

            var postData = {
                action: 'epkb_send_feedback',
                _wpnonce_epkb_send_feedback: $('#_wpnonce_epkb_send_feedback').val(),
                email: email,
                name: name,
                feedback: feedback
            };

            var msg;

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: postData,
                url: ajaxurl,
                beforeSend: function (xhr)
                {
                    $('#epkb-ajax-in-progress').text( epkb_vars.sending_feedback ).dialog('open');
                }
            }).done(function (response)
            {
                response = ( response ? response : '' );
                if ( response.error || typeof response.message === 'undefined' ) {
                    //noinspection JSUnresolvedVariable,JSUnusedAssignment
                    msg = response.message ? response.message : epkb_admin_notification('', epkb_vars.reload_try_again, 'error');
                    return;
                }

                msg = response.message;
                    
            }).fail( function ( response, textStatus, error )
            {
                //noinspection JSUnresolvedVariable
                msg = ( error ? ' [' + error + ']' : epkb_vars.unknown_error );
                //noinspection JSUnresolvedVariable
                msg = epkb_admin_notification(epkb_vars.error_occurred + '. ' + epkb_vars.msg_try_again, msg, 'error');
            }).always(function ()
            {
                $('#epkb-ajax-in-progress').dialog('close');

                if ( msg ) {
                    $('.eckb-top-notice-message').replaceWith(msg);
                    $( "html, body" ).animate( {scrollTop: 0}, "slow" );
                }
            });
        });

        //Feedback Toggle
        epkb.find( '.ekb-feedback-btn' ).on( 'click' , function(){
            epkb.find( '.ekb-feedback-btn' ).removeClass('ekb-feedback-btn-active');
            $( this ).addClass('ekb-feedback-btn-active');
            if ( $( this ).attr( 'id') === 'feature-request' ){
                epkb.find( '#epkb_feedback_form' ).show();
            }

        });

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
                    $('#epkb-ajax-in-progress').text( epkb_vars.changing_debug ).dialog('open');
                }
            }).done(function (response)
            {
                response = ( response ? response : '' );
                if ( response.error || typeof response.message === 'undefined' ) {
                    //noinspection JSUnresolvedVariable,JSUnusedAssignment
                    msg = response.message ? response.message : epkb_admin_notification('', epkb_vars.reload_try_again, 'error');
                    return;
                }

                window.location.href = window.location.href + '&info_tab=debug';

            }).fail( function ( response, textStatus, error )
            {
                //noinspection JSUnresolvedVariable
                msg = ( error ? ' [' + error + ']' : epkb_vars.unknown_error );
                //noinspection JSUnresolvedVariable
                msg = epkb_admin_notification(epkb_vars.error_occurred + '. ' + epkb_vars.msg_try_again, msg, 'error');
            }).always(function ()
            {
                $('#epkb-ajax-in-progress').dialog('close');

                if ( msg ) {
                    $('.eckb-top-notice-message').replaceWith(msg);
                    $( "html, body" ).animate( {scrollTop: 0}, "slow" );
                }
            });
        });

        // hide welcome section on settings page
        epkb.find( '#close_intro' ).on( 'click', function() {

            epkb.find( '.welcome_header' ).hide();

            var postData = {
                action: 'epkb_close_welcome_header'
            };

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: postData
            })
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
        $( '#kb_name' ).on( 'keyup', function(){
            var value   = $( this ).val().length;
            var limit   = 25;
            var result  = limit - value;
            $( '#character_value' ).remove();

            if( result < 0 ) {
                //noinspection JSUnresolvedVariable
                $( this ).after( '<div id="character_value" class="input_error"><p>' + epkb_vars.reduce_name_size + '</p></div>' );
            }
        });

        // toggle images on Welcome Page
        $( '#epkb-welcome-page-img1-thumb' ).on( 'click', function() {
            $('#epkb-welcome-page-img1').show();
            $('#epkb-welcome-page-img2').hide();
            $('#epkb-welcome-page-img3').hide();
        });
        $( '#epkb-welcome-page-img2-thumb' ).on( 'click', function() {
            $('#epkb-welcome-page-img2').show();
            $('#epkb-welcome-page-img1').hide();
            $('#epkb-welcome-page-img3').hide();
        });
        $( '#epkb-welcome-page-img3-thumb' ).on( 'click', function() {
            $('#epkb-welcome-page-img3').show();
            $('#epkb-welcome-page-img1').hide();
            $('#epkb-welcome-page-img2').hide();
        });

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
    (function(){

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

        // hide the dialog top bar
        $( ".ui-dialog-titlebar" ).hide();

        // ToolTip
        epkb.on( 'click', '.eckb-tooltip-button', function(){
            $( this ).parent().find( '.eckb-tooltip-contents' ).fadeToggle();
        });
    })();

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
});