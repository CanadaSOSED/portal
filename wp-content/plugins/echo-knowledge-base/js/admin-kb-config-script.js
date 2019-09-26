jQuery(document).ready(function($) {

    var ekb_admin_page_wrap = $( '#ekb-admin-page-wrap' );
    var epkb_order_sequence_updated = false;
    var epkb_switch_from_article_sidebar = false;

    // when Main Page is refreshed do setup
    $('#epkb-main-page-content').bind('main_content_changed', function() {

        epkb_set_tabs_height();

        // enable re-ordering if necessary
        epkb_custom_ordering();

        epkb_disable_clicks();
    });

    // when Article Page is refreshed do setup
    $('#epkb-article-page-content').bind('article_content_changed', function() {

        epkb_disable_clicks();

        // enable re-ordering if necessary
        epkb_custom_ordering();
    });

    function epkb_disable_clicks() {
        // diable search
        ekb_admin_page_wrap.find('[id*=_search_terms]').prop('readonly', true);
        $('[id*=-search-kb]').prop('disabled', true);

        // disable Links
        $('#epkb-main-page-container').find( 'a' ).on( 'click', function(e){
            // FUTURE $('#epkb-article-page').trigger('click');
            e.preventDefault();
        });
        $('#eckb-article-page-container').find( 'a' ).on( 'click', function(e){
            e.preventDefault();
        });

        // disable back navigation
        $('.epkb-navigation-button').attr('onclick','').unbind('click');
    }

    //Manage Archive Page Config Mega Menu Settings
    function epkb_archive_page_mega_menu_settings(){
        
	    $( '.eckb-no-kb-template-message' ).remove();
	    var archiveWrapper = $( '.epkb-archive-page-template-config' );

	    function disable_archive_settings() {

		    //Show Message
		    ekb_admin_page_wrap.find('#eckb-mm-arch-links-setup-list').append('<p class="eckb-no-kb-template-message">' + epkb_vars.archive_page + '</p>');

		    //Hide Settings
		    ekb_admin_page_wrap.find('#eckb-mm-arch-links-setup-list section').hide();
		    ekb_admin_page_wrap.find('#eckb-mm-arch-links-alltext-list section').hide();
	    }


        // if KB Templates option is on then show KB Templates config fields
        var templates_for_kb = ekb_admin_page_wrap.find('#templates_for_kb input[name=templates_for_kb]:checked').val();

        if ( templates_for_kb === 'kb_templates' ) {
            ekb_admin_page_wrap.find(archiveWrapper).show();
            ekb_admin_page_wrap.find( '#eckb-mm-arch-links-setup-list section' ).show();
            ekb_admin_page_wrap.find( '#eckb-mm-arch-links-alltext-list section' ).show();
            $( '.eckb-no-kb-template-message' ).remove();
        } else {
            disable_archive_settings();

        }
    }


	epkb_archive_page_mega_menu_settings();


    /*********************************************************************************************
     *********************************************************************************************
     *
     *                TOP PANEL
     *
     * ********************************************************************************************
     ********************************************************************************************/

    {
        // 1. KBs DROPDOWN - reload on change
        $( '#epkb-list-of-kbs' ).on( 'change', function(e) {
            // var what = e.target.value;
            var kb_admin_url = $(this).find(":selected").data('kb-admin-url');
            if ( kb_admin_url ) {
                window.location.href = kb_admin_url;
            }
        });

        // 2. User switches between Overview / MAIN PAGE / ARTICLE PAGE
        $( '#epkb-config-main-info').find('.page-icon' ).on( 'click', function() {

	        epkb_archive_page_mega_menu_settings();

            //Remove old info/error notices
            $('.eckb-bottom-notice-message').html('');

            // remove info boxes that are still open
            $('.option-info-icon').removeClass('active-info');
            $('.option-info-content').addClass('hidden');

            // Show TOP panel buttons
            $( '#epkb-main-page').show();
            $( '#epkb-article-page').show();

            // select Button clicked on
            $( '.epkb-info-section').removeClass( 'epkb-active-page' );
            $( this ).parent().parent().toggleClass( 'epkb-active-page' );

            // hide CONTENT PREVIEW panel
            $( '.epkb-config-content' ).hide();

            // hide SIDEBAR configuration fields panel
            $( '.epkb-config-sidebar' ).removeClass( 'open-menu' );
            $('#epkb-article-page-settings').hide();
            $('#epkb-main-page-settings').hide();

            // hide configuration fields
            $( '.config-option-group').removeClass( 'epkb-mm-active');

            // Show Settings Icon
            $('.epkb-mega-menu-toggle').show();

            // Toggle Page Content
            var id = $( this ).attr( 'id' );
            var article_page_layout = $('#kb_article_page_layout input[name=kb_article_page_layout]:checked').val();

            epkb_mega_menu.page_switching( id );

           $( '#' + id + '-content' ).fadeToggle();


            if ( id == 'epkb-main-page' ) {

                // prepare to show configuration settings form
                $('#epkb-main-page-settings').show();

                // show Save and Cancel and Setting buttons when on Main page
                $('.epkb-info-save').show();
                $('.epkb-demo-data-button' ).show();

                // renable ordering
                epkb_custom_ordering();

                // show Layout Preview panel
                $('#epkb-main-page-content').show();

                // if KB Templates option is on then show KB Templates config fields
                var templates_for_kb = $('#templates_for_kb input[name=templates_for_kb]:checked').val();

                if ( templates_for_kb == 'kb_templates' ) {
                    $('.eckb-mm-mp-links-setup-main-template-content').addClass('epkb-mm-active');

                }

                // if user changed article or category sequence on Article Page then update Main Page sequence too
                if ( epkb_order_sequence_updated && article_page_layout == 'Sidebar' ) {
                    epkb_order_sequence_updated = false;
                    //noinspection JSUnusedAssignment
                    epkb_switch_from_article_sidebar = true;
                    epkb_process_user_sequence_change('main_page', epkb_get_new_article_page_sequence());
                    epkb_switch_from_article_sidebar = false;
                }

            } else if ( id == 'epkb-article-page' ) {

                // if KB Templates option is on then show KB Templates config fields
                var templates_for_kb = $('#templates_for_kb input[name=templates_for_kb]:checked').val();
                if ( templates_for_kb == 'kb_templates' ) {
                    $('.eckb-mm-mp-links-setup-article-template-content').addClass('epkb-mm-active');
                }

                // prepare to show configuration settings form
                $('#epkb-article-page-settings').show();

                // show Save and Cancel and Setting buttons when on Article page
                $('.epkb-info-save').show();
                $('.epkb-demo-data-button' ).show();

                // renable ordering
                epkb_custom_ordering();

                // update Article Page Layout preview based on current selection of Article layout that could change due to Main Page Layout choice
                if ( $('#epkb-article-page-content').html().trim().length === 0) {
                    epkb_ajax_article_page_config_change_request( 'layout', article_page_layout );
                }

                // show Layout Preview panel
                $('#epkb-article-page-content').show();

                // if user changed article or category sequence on Main Page then update Article Page sequence too
                if ( epkb_order_sequence_updated && article_page_layout == 'Sidebar' ) {
                    epkb_process_user_sequence_change('article_page', epkb_get_new_main_page_sequence());
                }

                epkb_order_sequence_updated = ( article_page_layout == 'Article' ? epkb_order_sequence_updated : false );

            } else if ( id == 'epkb-archive-page' ) {
	            // show Save and Cancel and Setting buttons when on Article page
	            $('.epkb-info-save').show();
				
            } else {   // OVERVIEW PAGE
                // hide Save and Cancel and setting buttons when on Overview
                $('.epkb-info-save').hide();
                $('.epkb-mega-menu-toggle').hide();
                $('.epkb-demo-data-button').hide();

            }
        });

        // Toggle Demo information popup
        $( '.epkb-demo-data-button' ).on( 'click', '.ep_font_icon_info', function(){
            $( this ).toggleClass( 'active-info' );
            $( '.epkb-demo-info-content' ).toggleClass( 'hidden' );
        });

        //Settings Mega Menu
        var epkb_mega_menu = {

            general:    function() {

                // 1. USER clicks on Settings Icon - show/hide Mega Menu
                $( '#epkb-config-main-info .epkb-setting-icon' ).on( 'click', function(){

                    // Show highlight for Button clicked on
                    $( this ).parent().parent().toggleClass( 'epkb-active-page' );
                    //$( '#epkb-admin-mega-menu' ).toggle();
                });

                // 2. USER clicks on link on the left - show menu content on the right
                $( '#epkb-admin-mega-menu' ).on( 'click', '.epkb-mm-sidebar li', function(){

                    // remove all active links from sidebar
                    $( '.epkb-mm-sidebar li' ).removeClass( 'epkb-mm-active' );
                    $( '.epkb-mm-links li' ).removeClass( 'active-link');

                    // highlight clicked on link in the sidebar
                    $( this ).addClass( 'epkb-mm-active' );

                    // remove active menu item content (right side)
                    $( '.epkb-mm-content .epkb-mm-links').removeClass( 'epkb-mm-active');

                    // hide configuration fields
                    $( '.config-option-group').removeClass( 'epkb-mm-active');



                    // display content on the right based on clicked on menu item
                    var linkID = $(this).attr( 'id');
                    $( '.epkb-mm-content').find( '#' + linkID + '-list' ).addClass( 'epkb-mm-active' );

                    // for ORGANIZE show the configuration fields
                    if ( linkID == 'eckb-mm-mp-links-organize' ) {
                        $('.eckb-mm-mp-links-organize--organize-content').addClass('epkb-mm-active');
                        $('#epkb-config-ordering-sidebar').show();
                    } else if ( linkID == 'eckb-mm-ap-links-organize' ) {
                        $('.eckb-mm-ap-links-organize--organize-content').addClass('epkb-mm-active');
                        $('#epkb-config-article-ordering-sidebar').show();
                    } else if ( linkID == 'eckb-mm-mp-links-setup' ){
	                    var templates_for_kb = $('#templates_for_kb input[name=templates_for_kb]:checked').val();

	                    if ( templates_for_kb == 'kb_templates' ) {
		                    $('.eckb-mm-mp-links-setup-main-template-content').addClass('epkb-mm-active');

	                    }
                    }


                    epkb_custom_ordering();
                });

                // 3. USER clicks on sub-links on the right - display config fields in sidebar
                $( '#epkb-admin-mega-menu' ).on( 'click', '.epkb-mm-links li', function() {

                    var id = $( this ).parents( '.epkb-mm-links' ).attr( 'id' );

                    //If it's the main setup these are not links but actual form functions so skip the active class.
                    if ( id === 'eckb-mm-mp-links-setup-list' || id === 'eckb-mm-ap-links-setup-list' ) {
                        return;
                    }

                    //Remove all active links
                    $( '.epkb-mm-links li' ).removeClass( 'active-link');

                    $( this ).addClass( 'active-link' );

                    //Remove all active Sidebar config options
                    $('.config-option-group').removeClass( 'epkb-mm-active' );

                    //Display Content from link
                    var linkID = $(this).attr( 'id' );
                    $( '.epkb-config-sidebar' ).find( '.' + linkID + '-content' ).addClass( 'epkb-mm-active' );
                });

                // 4. USER Clicks on Middle Arrow close icon
                $( '#epkb-admin-mega-menu' ).on( 'click', '.epkb-close-mm', function(){

                    $( '.epkb-open-mm' ).show();
                    $( '#epkb-admin-mega-menu' ).slideUp( 600 );
                });

                // 5. USER Clicks on Middle Arrow open icon
                $( '.epkb-open-mm' ).on( 'click', function(){

                    $( this ).hide();
                    $( '#epkb-admin-mega-menu' ).slideDown( 600 );
                });

                // 6. USER Clicks on Templates option
                $( '#epkb-admin-mega-menu' ).on( 'click', '#templates_for_kb_group', function() {

	                epkb_archive_page_mega_menu_settings();

                    //Remove all active Sidebar config options
                    $('.config-option-group').removeClass( 'epkb-mm-active' );

                    var templates_for_kb = $('#templates_for_kb input[name=templates_for_kb]:checked').val();

                    if ( templates_for_kb == 'kb_templates' ) {
                        $('.eckb-mm-mp-links-setup-main-template-content').addClass('epkb-mm-active');

                    }
                });
            },
            page_switching:     function( id ) {

                //Always hide Down MM arrow on page switching since the MM will be open.
                $( '.epkb-open-mm' ).hide();

                //Show the appropriate links for each page
                if ( id === 'epkb-main-page' ) {

                    //Show Mega Menu
	                display_mega_menu_with_flexbox( 'show' );

                    //Remove all active Sidebar links and Content.
                    $( '.epkb-mm-links' ).removeClass( 'epkb-mm-active' );

                    //Show MM: Main Page Sidebar
                    $( '#eckb-mm-mp-links' ).show();
                    $( '#eckb-mm-ap-links' ).hide();
	                $( '#eckb-mm-arch-links' ).hide();

                    //Clear all MM: Sidebar active links
                    $( '#eckb-mm-mp-links li' ).removeClass( 'epkb-mm-active');

                    //Show MM: Main page First Link Content
                    $( '#eckb-mm-mp-links-setup' ).addClass( 'epkb-mm-active' );
                    $( '#eckb-mm-mp-links-setup-list' ).addClass( 'epkb-mm-active' );
                    $( '#eckb-mm-ap-links-setup-list' ).removeClass( 'epkb-mm-active' );


                } else if ( id === 'epkb-article-page' ) {

                    //Show Mega Menu
	                display_mega_menu_with_flexbox( 'show' );

                    //Remove all active Sidebar links and Content.
                    $( '.epkb-mm-links' ).removeClass( 'epkb-mm-active' );

                    //Show MM: Article Page Sidebar
                    $( '#eckb-mm-ap-links' ).show();
                    $( '#eckb-mm-mp-links' ).hide();
	                $( '#eckb-mm-arch-links' ).hide();
					
                    //Clear all MM: Sidebar active links
                    $( '#eckb-mm-ap-links li' ).removeClass( 'epkb-mm-active');

                    //Show MM: Article First Link Content
                    $( '#eckb-mm-ap-links-setup' ).addClass( 'epkb-mm-active' );
                    $( '#eckb-mm-ap-links-setup-list' ).addClass( 'epkb-mm-active' );
                    $( '#eckb-mm-mp-links-setup-list' ).removeClass( 'epkb-mm-active' );

                } else if ( id === 'epkb-config-overview' ) {
                    //If Overview Clicked Hide Mega Menu.
                    $( '#epkb-admin-mega-menu' ).hide();

                } else if ( id === 'epkb-archive-page' ){

	                //Show Mega Menu
	                display_mega_menu_with_flexbox( 'show' );

	                //Remove all active Sidebar links and Content.
	                $( '.epkb-mm-links' ).removeClass( 'epkb-mm-active' );
	                $( '#eckb-mm-arch-links li' ).removeClass( 'epkb-mm-active' );

	                //Show MM: Article First Link Content
	                $( '#eckb-mm-arch-links-setup-list' ).addClass( 'epkb-mm-active' );
	                $( '#eckb-mm-arch-links-setup' ).addClass( 'epkb-mm-active' );

	                //Show MM: Article Page Sidebar
	                $( '#eckb-mm-arch-links' ).show();
	                $( '#eckb-mm-ap-links' ).hide();
	                $( '#eckb-mm-mp-links' ).hide();
                }

                return this;
            },
            drag_drop:   function(){
                // when user clicks on Organize link in MM or AP, show quick highlight of preview boxes
                $( '#epkb-admin-mega-menu' ).on( 'click', '#eckb-mm-mp-links-organize, #eckb-mm-ap-links-organize', function() {

                    //Basic
                    $('.epkb-section-container').animate( { backgroundColor: "#00ff00" }, 1 ).animate( { backgroundColor: "#ffffff" }, 1500 );

                    //Tabs Layout
                    $('.epkb-nav-tabs').animate( { backgroundColor: "#00ff00" }, 1 ).animate( { backgroundColor: "#ffffff" }, 1500 );
                    $('.epkb-panel-container ').animate( { backgroundColor: "#00ff00" }, 1 ).animate( { backgroundColor: "#ffffff" }, 1500 );

                    //Grid Layout
                    $('.eckb-categories-list' ).animate( { backgroundColor: "#00ff00" }, 1 ).animate( { backgroundColor: "#ffffff" }, 1500 );

                    //Sidebar Layout
                    $('.sidebar-sections' ).animate( { backgroundColor: "#00ff00" }, 1 ).animate( { backgroundColor: "#ffffff" }, 1500 );
                });
            }
        };

        epkb_mega_menu.general();
        epkb_mega_menu.drag_drop();

        // cancel configuration changes
        $( '#epkb_cancel_config, #epkb_cancel_dashboard' ).on( 'click', function(){
            location.reload();
        });

        // when first loaded show or hide Save/Cancel buttons accordingly
        if ( $('#epkb-main-page-button').hasClass('epkb-active-page') || $('#epkb-article-page-button').hasClass('epkb-active-page') ) {
            $('.epkb-info-save').show();
        } else {
            
            $('.epkb-demo-data-button' ).hide();
        }

        // highlight text for clicked on radio button
        $( '#templates_for_kb_group' ).find( ":radio" ).on('click', function(){
            epkb_highlight_text_for_checked_radio_button( $( this ) );
        });
    }


    /*********************************************************************************************
     *********************************************************************************************
     *
     *                LAYOUT PREVIEW BOX
     *
     * ********************************************************************************************
     ********************************************************************************************/

    {
        // SEARCH

        // Sidebar Layout Search Toggle
        $('[id*=-search-toggle]').on('click', function () {
            $('#epkb-article-page-content').find('[id*=-search-toggle]').slideToggle();
        });

        // Prevent Search from being used on config
        $('#ekb-admin-page-wrap').find('[id*=_search_terms]').prop('readonly', true);
        $('[id*=-search-kb]').prop('disabled', true);

        // Show Character count on Tab Name input field and warning message
        $('#kb_name').on('keyup', function () {
            var value = $(this).val().length;
            var limit = 25;
            var result = limit - value;
            $('#character_value').remove();
            if (result < 0) { //noinspection JSUnresolvedVariable
                $(this).after('<div id="character_value" class="input_error"><p>' + epkb_vars.reduce_name_size + '</p></div>');
            }
        });

        // INFO BOX
        ekb_admin_page_wrap.on( 'click', '.epkb-info', function (){

            $( this ).parent().toggleClass( 'epkb-preview-active-info' );
            $( this ).parent().find( '.epkb-preview-information' ).slideToggle();
            $( this ).parent().next().css( "opacity", ".2" );

            if( !$( this ).parent().hasClass( 'epkb-preview-active-info')){
                $( this ).parent().next().css( "opacity", "1" );
            }
        });

        //Set the Height of the preview box and the config sidebar to match the users screen size.
        function epkb_set_layout_preview_box_height() {
            var screenHeight = $(window).outerHeight();
            var wpAdminAdminbar = 32;
            var configMainInfo = 103;
            var previewInfo = 75;
            var sidebarMenuheader = 64;
            var sidebarBack = 48;
            var bodyPadding = 65;
            var wpAdminfooter = 40;

            var newPreviewHeight = screenHeight - wpAdminfooter - wpAdminAdminbar - configMainInfo - previewInfo - bodyPadding;
            var newSidebarHeight = screenHeight - wpAdminAdminbar - configMainInfo - sidebarMenuheader - sidebarBack - bodyPadding - wpAdminfooter;
            $('#epkb-main-page-container').css('max-height', newPreviewHeight);
            $('#eckb-article-page-container').css('max-height', newPreviewHeight);
            $('[id*=lay-grid-layout-page-container]').css('max-height', newPreviewHeight);
            $('[id*=lay-sidebar-layout-page-container]').css('max-height', newPreviewHeight);
            $('.epkb-sidebar-container').css('max-height', newSidebarHeight);
        }
       // epkb_set_layout_preview_box_height();
    }


    /*********************************************************************************************
     *********************************************************************************************
     *
     *                SIDEBAR: ALL
     *
     * ********************************************************************************************
     ********************************************************************************************/

    {
        // CHANGE any field
        var slowdown_rapid_change = 0;

        $( '#epkb-config-config' ).on( 'change' ,function (event) {

            // exclude style/color/layout changes
            if ( typeof event.target.id !== 'undefined' &&
                ( event.target.id.includes("reset_style") || event.target.id.match("^categories_display_sequence") || event.target.id.match("^articles_display_sequence") ||
                event.target.id.match("^epkb-layout-preview-data") || event.target.id.match("^kb_main_page_layout") || event.target.id.match("^kb_article_page_layout") ||
                event.target.id.match("grid_category_icon*") || event.target.id.match("article_common_path*") || event.target.id.match("kb_articles_common_path") ||
                    ( event.target.id.indexOf('section_head_category_icon') === 0 && event.target.id !== 'section_head_category_icon_location' && event.target.id !== 'section_head_category_icon_size') ) ) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            clearTimeout(slowdown_rapid_change);
            //noinspection JSUnusedAssignment
            slowdown_rapid_change = setTimeout(function () {
                epkb_update_preview_on_change();
            }, 3000);
        });

        function epkb_update_preview_on_change() {
            var epkb_is_article_icon_active = $('#epkb-article-page-button').hasClass('epkb-active-page');

            var postData = {
                action: 'epkb_change_one_config_param_ajax',
                epkb_kb_id: $('#epkb_kb_id').val(),
                form: $('#epkb-config-config').serialize(),
                epkb_demo_kb: $('#epkb-layout-preview-data').is(':checked'),
                epkb_is_article_icon_active: epkb_is_article_icon_active
            };

            var msg;

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: postData,
                url: ajaxurl,
                beforeSend: function (xhr)
                {
                    $('#epkb-ajax-in-progress').text( epkb_vars.updating_preview ).dialog('open');
                }
            }).done(function (response)
            {
                response = ( response ? response : '' );
                if ( response.error || typeof response.message === 'undefined' || typeof response.kb_info_panel_output === 'undefined' ) {
                    //noinspection JSUnresolvedVariable,JSUnusedAssignment
                    msg = response.message ? response.message : epkb_admin_notification('', epkb_vars.reload_try_again, 'error');
                    return;
                }

                //noinspection JSUnresolvedVariable
                if ( epkb_is_article_icon_active ) {
                    $('#epkb-article-page-content').html(response.kb_info_panel_output);
                    $('#epkb-article-page-content').trigger('article_content_changed');
                } else {
                    $('#epkb-main-page-content').html(response.kb_info_panel_output);
                    $('#epkb-main-page-content').trigger('main_content_changed');
                }

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
                    $('.eckb-bottom-notice-message').replaceWith(msg);
                    $( "html, body" ).animate( {scrollTop: 0}, "slow" );
                }
            });
        }

        // CHANGE color picker
        var myOptions = {
            // a callback to fire whenever the color changes to a valid color
            done: function(event, ui){
            },
            // a callback to fire when the input is emptied or an invalid color
            clear: function() {}
        };
        $( '.ekb-color-picker input' ).wpColorPicker(myOptions);

        // because handlers tied to upper elements are blocked we need to reload them after changes are
        // made to colors menu
        function epkb_setup_color_preview_handlers() {
            $('.wp-picker-container').on('click', function () {

                //Add Preview button beside color square
                $('.epkb-picker-preview').remove();
                $(this).find('.iris-picker-inner').before('<div class="epkb-picker-preview">' + epkb_vars.preview + '</div>');

            });
            $('.wp-picker-container').on('click', '.epkb-picker-preview', function (e) {

                // exclude style/color/layout changes
                e.preventDefault();
                e.stopPropagation();

                var epkb_is_article_icon_active = $('#epkb-article-page-button').hasClass('epkb-active-page');

                var postData = {
                    action: 'epkb_change_one_config_param_ajax',
                    epkb_kb_id: $('#epkb_kb_id').val(),
                    form: $('#epkb-config-config').serialize(),
                    epkb_demo_kb: $('#epkb-layout-preview-data').is(':checked'),
                    epkb_is_article_icon_active: epkb_is_article_icon_active
                };

                var msg;

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: postData,
                    url: ajaxurl,
                    beforeSend: function (xhr) {
                        $('#epkb-ajax-in-progress').text( epkb_vars.updating_preview ).dialog('open');
                    }
                }).done(function (response) {
                    response = ( response ? response : '' );
                    if (response.error || typeof response.message === 'undefined' || typeof response.kb_info_panel_output === 'undefined') {
                        //noinspection JSUnresolvedVariable,JSUnusedAssignment
                        msg = response.message ? response.message : epkb_admin_notification('', epkb_vars.reload_try_again, 'error');
                        return;
                    }

                    //noinspection JSUnresolvedVariable
                    if (epkb_is_article_icon_active) {
                        $('#epkb-article-page-content').html(response.kb_info_panel_output);
                        $('#epkb-article-page-content').trigger('article_content_changed');
                    } else {
                        $('#epkb-main-page-content').html(response.kb_info_panel_output);
                        $('#epkb-main-page-content').trigger('main_content_changed');
                    }

                }).fail(function (response, textStatus, error) {
                    //noinspection JSUnresolvedVariable
                    msg = ( error ? ' [' + error + ']' : epkb_vars.unknown_error );
                    //noinspection JSUnresolvedVariable
                    msg = epkb_admin_notification(epkb_vars.error_occurred + '. ' + epkb_vars.msg_try_again, msg, 'error');
                }).always(function () {
                    $('#epkb-ajax-in-progress').dialog('close');

                    if (msg) {
                        $('.eckb-bottom-notice-message').replaceWith(msg);
                        $("html, body").animate({scrollTop: 0}, "slow");
                    }
                });
            });
        }
        epkb_setup_color_preview_handlers();

        // highlight text for clicked on radio button
        $( '.epkb-config-sidebar' ).find( ":radio" ).on('click', function(){
            epkb_highlight_text_for_checked_radio_button( $( this ) );
        });

        epkb_disable_clicks();

        // Display Info Icon Content
        $( '.epkb-sidebar-container' ).on( 'click', '.option-info-icon', function(e){
            e.preventDefault();

            var is_help_hidden = $( this ).parents( '.config-option-group').find( '.option-info-content' ).hasClass('hidden');

            // first remove all info boxes
            $('.option-info-icon').removeClass('active-info');
            $('.option-info-content').addClass('hidden');

            //Get Sidebar Position
            var sidbarPOS = $(this).parents( '.epkb-sidebar-container' ).position();

            //Toggle Active class for icon
            if ( is_help_hidden ) {
                //Show Content
                $( this ).addClass('active-info');
                $( this ).parents('.config-option-group').find( '.option-info-content' ).removeClass( 'hidden' );
                $( this ).parents('.config-option-group').find( '.option-info-content' ).css({
                    top: sidbarPOS.top + 30,
                    right: $('.epkb-sidebar-container').outerWidth( true ) + 20
                });
            }
        });
    }


    /*********************************************************************************************
     *********************************************************************************************
     *
     *                SIDEBAR: MAIN PAGE
     *
     * ********************************************************************************************
     ********************************************************************************************/

    {
        // DEMO / KB DATA Switching
        $('#epkb-config-main-info').on('change', '#epkb-layout-preview-data', function (e) {
            e.preventDefault();  // do not submit the form
            epkb_ajax_main_page_config_change_request( 'demo', 'demo' );
            epkb_ajax_article_page_config_change_request( 'demo', 'demo' );
        });

        // LAYOUT: if user wants to change layout, confirm it first
        $( '#epkb-admin-mega-menu').on( 'change',  '#kb_main_page_layout :input', function (e) {
           e.preventDefault();

            epkb_order_sequence_updated = false;

            // initialize hidden layout field
            $('#kb_main_page_layout').val(e.target.value);

            var target_name = e.target.value;

            // remove focus so that arrow keys up/down page can be used
            $('#kb_main_page_layout input[name=kb_main_page_layout]').blur();

            // show or hide Categories link on KB Main Page
            if ( target_name === 'Grid') {
	            $('#kb_main_page_category_link_group').show();
            } else {
	            $('#kb_main_page_category_link_group').hide();
            }

            epkb_ajax_main_page_config_change_request( 'layout', target_name );
         });

        // STYLE: if user wants to change style, confirm it first
        $( '#epkb-admin-mega-menu' ).on( 'change', '#main_page_reset_style :input', function (e) {
            e.preventDefault();

            var target_name = e.target.value;

            // allow user to select the same option if they want to reset their changes to the same style
            $('#main_page_reset_style input[name=main_page_reset_style]').prop('checked', false);

            epkb_ajax_main_page_config_change_request('style', target_name);
        });

	    // ADV SEARCH STYLE: if user wants to change style, confirm it first
	    $( '#epkb-admin-mega-menu' ).on( 'change', '#main_page_reset_search_box_style :input', function (e) {
		    e.preventDefault();

		    var target_name = e.target.value;

		    // allow user to select the same option if they want to reset their changes to the same style
		    $('#main_page_reset_search_box_style input[name=main_page_reset_search_box_style]').prop('checked', false);

		    epkb_ajax_main_page_config_change_request('advanced_search_box_style', target_name);
	    });

        // COLORS: if user wants to change colors
        $( '#epkb-admin-mega-menu').on( 'click', '#main_page_reset_colors :button', function (e) {
            e.preventDefault();

            var target_name = e.target.value;

            // allow user to select the same option if they want to reset their changes to the same colors
            $('#main_page_reset_colors input[name=reset_colors]').prop('checked', false);

            epkb_ajax_main_page_config_change_request( 'colors', target_name );
        });

        function epkb_ajax_main_page_config_change_request( target_type, target_name ) {

            var postData = {
                action: 'epkb_change_main_page_config_ajax',
                epkb_kb_id: $('#epkb_kb_id').val(),
                target_type: target_type,
                target_name: target_name,
                epkb_chosen_main_page_layout: $('#kb_main_page_layout input[name=kb_main_page_layout]:checked').val(),
                epkb_demo_kb: $('#epkb-layout-preview-data').is(':checked'),
                form: $('#epkb-config-config').serialize()
            };

            var msg;

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: postData,
                url: ajaxurl,
                beforeSend: function (xhr)
                {
                    $('#epkb-ajax-in-progress').text( epkb_vars.changing_config ).dialog('open');
                }
            }).done(function (response)
            {
                response = ( response ? response : '' );
                if ( response.error || typeof response.message === 'undefined' || typeof response.style_tab_output === 'undefined' ||
                    typeof response.colors_tab_output === 'undefined' || typeof response.kb_main_page_output === 'undefined' ) {
                    //noinspection JSUnresolvedVariable,JSUnusedAssignment
                    msg = response.message ? response.message : epkb_admin_notification('', epkb_vars.reload_try_again, 'error');
                    return;
                }

                msg = response.message;

                //   ===========    LAYOUT CHANGE   ==============

                // always refresh Main Page layout
                $('#epkb-main-page-content').html(response.kb_main_page_output);
                $('#epkb-main-page-content').trigger('main_content_changed');

                if ( target_type == 'layout' ) {

                    // update Mega menu
                    $('#epkb-admin-mega-menu').html(response.kb_mega_menu);
                    //Show Mega Menu
                    epkb_mega_menu.page_switching('epkb-main-page');

                    $('#epkb-article-page-content').html(response.article_page_output);
                    $('#epkb-article-page-content').trigger('article_content_changed');

                    if ( response.ordering_output ) {
                        $('#epkb-config-ordering-sidebar').replaceWith(response.ordering_output);
                    }

                    if ( response.overview_page_output ) {
                        $('#epkb-config-overview-content').html(response.overview_page_output);
                    }

                    if ( response.main_page_text_output ) {
                        $('#epkb-config-text-sidebar').replaceWith(response.main_page_text_output);
                    }

                    // also reset article page
                    epkb_handle_article_page_config_response( false, target_type, response.article_page_layout, response );
                }

                if ( response.style_tab_output != 'NONE' ) {
                     $('#epkb-config-styles-sidebar').replaceWith(response.style_tab_output);
                }

                if ( response.colors_tab_output != 'NONE' ) {
                     $('#epkb-config-colors-sidebar').replaceWith(response.colors_tab_output);
                }

	            if ( target_type == 'advanced_search_box_style' ) {
		            $('#epkb-config-text-sidebar').replaceWith(response.main_page_text_output);
	            }

            }).fail( function ( response, textStatus, error )
            {
                //noinspection JSUnresolvedVariable
                msg = ( error ? ' [' + error + ']' : epkb_vars.unknown_error );
                //noinspection JSUnresolvedVariable
                msg = epkb_admin_notification(epkb_vars.error_occurred + '. ' + epkb_vars.msg_try_again, msg, 'error');
            }).always(function ()
            {
                $('#epkb-ajax-in-progress').dialog('close');

                //If color pickers are not detected then add them.
                var call_color_handlers = false;
                if(  $( '#epkb-config-colors-sidebar .wp-picker-container' ).length == 0 ){
                    $( '#epkb-config-colors-sidebar .ekb-color-picker input' ).wpColorPicker();
	                call_color_handlers = true;
                }
	            if(  $( '#epkb-config-article-colors-sidebar .wp-picker-container' ).length == 0 ){
		            $( '#epkb-config-article-colors-sidebar .ekb-color-picker input' ).wpColorPicker();
		            call_color_handlers = true;
	            }
	            if ( call_color_handlers ) {
		            epkb_setup_color_preview_handlers();
	            }


                if ( msg ) {
                    $('.eckb-bottom-notice-message').replaceWith(msg);
                    $( "html, body" ).animate( {scrollTop: 0}, "slow" );
                }
            });
        }

    }


    /*********************************************************************************************
     *********************************************************************************************
     *
     *                SIDEBAR: ARTICLE PAGE
     *
     *********************************************************************************************
     ********************************************************************************************/

    {
        // LAYOUT: if user wants to change layout
        $('body').on('change', '#kb_article_page_layout_group', function (e) {
            e.preventDefault();

            var target_name = e.target.value;

            // initialize hidden layout field
            $('#kb_article_page_layout').val(e.target.value);
            
            //Remove all active Sidebar config options
            $('.config-option-group').removeClass( 'epkb-mm-active' );

            // remove focus so that arrow keys up/down page can be used
            $('#kb_article_page_layout input[name=kb_article_page_layout]').blur();

            epkb_ajax_article_page_config_change_request('layout', target_name);
        });

        // STYLE: if user wants to change style
        $('body').on('change', '#article_page_reset_style :input', function (e) {
            e.preventDefault();

            var target_name = e.target.value;

            // allow user to select the same option if they want to reset their changes to the same style
            $('#article_page_reset_style input[name=article_page_reset_style]').prop('checked', false);

            epkb_ajax_article_page_config_change_request('style', target_name);
        });

	    // ADV SEARCH STYLE: if user wants to change style, confirm it first
	    $('body').on( 'change', '#article_page_reset_search_box_style :input', function (e) {
		    e.preventDefault();

		    var target_name = e.target.value;

		    // allow user to select the same option if they want to reset their changes to the same style
		    $('#article_page_reset_search_box_style input[name=article_page_reset_search_box_style]').prop('checked', false);

		    epkb_ajax_article_page_config_change_request('advanced_search_box_style', target_name);
	    });

        // COLORS: if user wants to change colors
        $('body').on('click', '#article_page_reset_colors :button', function (e) {
            e.preventDefault();

            var target_name = e.target.value;

            // allow user to select the same option if they want to reset their changes to the same colors
            $('#reset_colors input[name=reset_colors]').prop('checked', false);

            epkb_ajax_article_page_config_change_request('colors', target_name);
        });

        // change pre-set styles, colors
        function epkb_ajax_article_page_config_change_request(target_type, target_name) {

            var postData = {
                action: 'epkb_change_article_page_config_ajax',
                epkb_kb_id: $('#epkb_kb_id').val(),
                target_type: target_type,
                target_name: target_name,
                epkb_chosen_main_page_layout: $('#kb_main_page_layout input[name=kb_main_page_layout]:checked').val(),
                epkb_chosen_article_page_layout: $('#kb_article_page_layout input[name=kb_article_page_layout]:checked').val(),
                epkb_demo_kb: $('#epkb-layout-preview-data').is(':checked'),
                form: $('#epkb-config-config').serialize()
            };

            var msg;

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: postData,
                url: ajaxurl,
                beforeSend: function (xhr)
                {
                    $('#epkb-ajax-in-progress').text( epkb_vars.changing_config ).dialog('open');
                }
            }).done(function (response)
            {
                var error = epkb_handle_article_page_config_response(true, target_type, target_name, response);
                msg = error.length > 0 ? error : response.message;

            }).fail(function (response, textStatus, error)
            {
                //noinspection JSUnresolvedVariable
                msg = ( error ? ' [' + error + ']' : epkb_vars.unknown_error );
                //noinspection JSUnresolvedVariable
                msg = epkb_admin_notification(epkb_vars.error_occurred + '. ' + epkb_vars.msg_try_again, msg, 'error');
            }).always(function ()
            {
                $('#epkb-ajax-in-progress').dialog('close');

                //If color pickers are not detected then add them.
                if ($('#epkb-config-article-colors-sidebar .wp-picker-container').length == 0)
                {
                    $('#epkb-config-article-colors-sidebar .ekb-color-picker input').wpColorPicker();
                    epkb_setup_color_preview_handlers();
                }

                if (msg)
                {
                    $('.eckb-bottom-notice-message').replaceWith(msg);
                    $("html, body").animate({scrollTop: 0}, "slow");
                }
            });
        }

        function epkb_handle_article_page_config_response(showing_article_page, target_type, target_name, response) {

            response = ( response ? response : '' );
            if (response.error || typeof response.message === 'undefined' ||
                typeof response.article_style_tab_output === 'undefined' || typeof response.article_colors_tab_output === 'undefined')
            {
                //noinspection JSUnresolvedVariable,JSUnusedAssignment
                return response.message ? response.message : epkb_admin_notification('', epkb_vars.reload_try_again, 'error');
            }

            // update Mega menu
            if ( response.kb_mega_menu != 'NONE' && showing_article_page ) {
                $('#epkb-admin-mega-menu').html(response.kb_mega_menu);
                //Show Mega Menu
                epkb_mega_menu.page_switching('epkb-article-page');
            }

            // always refresh colors
            $('#epkb-article-page-content').html(response.article_page_output);
            $('#epkb-article-page-content').trigger('article_content_changed');

            if (response.article_style_tab_output != 'NONE')
            {
                $('#epkb-config-article-styles-sidebar').replaceWith(response.article_style_tab_output);
            }

            if (response.article_ordering_output != 'NONE')
            {
                $('#epkb-config-article-ordering-sidebar').replaceWith(response.article_ordering_output);
            }

            if (response.article_colors_tab_output != 'NONE')
            {
                $('#epkb-config-article-colors-sidebar').replaceWith(response.article_colors_tab_output);
            }

            if (response.article_text_tab_output != 'NONE')
            {
                $('#epkb-config-article-text-sidebar').replaceWith(response.article_text_tab_output);
            }

            if (response.article_general_tab_output)
            {
                $('#epkb-config-article-general-sidebar').replaceWith(response.article_general_tab_output);
            }

	        if ( target_type == 'advanced_search_box_style' ) {
		        $('#epkb-config-text-sidebar').replaceWith(response.main_page_text_output);
	        }

            // if user changed article or category sequence on Main Page then update Article Page sequence too
            var article_page_layout = $('#kb_article_page_layout input[name=kb_article_page_layout]:checked').val();
            if ( epkb_order_sequence_updated && article_page_layout == 'Sidebar' ) {
                epkb_process_user_sequence_change('article_page', epkb_get_new_main_page_sequence());
            }
            epkb_order_sequence_updated = false;

            return '';
        }

      // only if user changes common path do save it
     $('body').on('click', '.epkb_art_common_path_button', function (e) {
         $('#epkb_common_path_changed').val('yes');
     });
    }

    /********************************************************************************************
     *
     *                SAVE CONFIGURATION
     *
     ********************************************************************************************/

    // SAVE KB configuration
    $( '#epkb_save_kb_config, #epkb_save_dashboard' ).on( 'click', function (e) {
        e.preventDefault();  // do not submit the form
        var msg = '';

        // retrieve all categories and article ids
        var article_page_layout = $('#kb_article_page_layout input[name=kb_article_page_layout]:checked').val();
        var new_ordering_sequence = $('#epkb-article-page-button').hasClass('epkb-active-page') && article_page_layout === 'Sidebar'
                                        ? epkb_get_new_article_page_sequence() : epkb_get_new_main_page_sequence();
        var postData = {
            action: 'epkb_save_kb_config_changes',
            _wpnonce_epkb_save_kb_config: $('#_wpnonce_epkb_save_kb_config').val(),
            epkb_kb_id: $('#epkb_kb_id').val(),
            form: $('#epkb-config-config, #epkb-config-config2, #epkb-config-config3').serialize(),
            epkb_new_sequence: new_ordering_sequence,
            top_cat_sequence: epkb_get_top_category_seq()  // get top level category sequence for Tabs Layout handling
        };

        $.ajax({
            type: 'POST',
            dataType: 'json',
            data: postData,
            url: ajaxurl,
            beforeSend: function (xhr)
            {
                //noinspection JSUnresolvedVariable
                $('#epkb-ajax-in-progress').text(epkb_vars.save_config).dialog('open');
            }

        }).done(function (response)
        {
            response = ( response ? response : '' );
            if ( ! response.error && typeof response.message !== 'undefined' )
            {
                msg = response.message;

                $('#epkb-content-container').find('.epkb-sortable-item-highlight').each(function(i, obj) {
                    $(this).removeClass('epkb-sortable-item-highlight');
                });

                if (msg.indexOf('RELOAD') >= 0) {
                    $('.eckb-bottom-notice-message').replaceWith(msg);
                    msg = '';
                    $("html, body").animate({scrollTop: 0}, "slow");

                    window.setTimeout(show_reload_dialog, 2000);
                    function show_reload_dialog() {
                        location.reload();
                    }
                }
            } else {
                //noinspection JSUnresolvedVariable
                msg = response.message ? response.message : epkb_admin_notification('', epkb_vars.reload_try_again, 'error');
            }

        }).fail(function (response, textStatus, error)
        {
            //noinspection JSUnresolvedVariable
            msg = ( error ? ' [' + error + ']' : epkb_vars.unknown_error );
            //noinspection JSUnresolvedVariable
            msg = epkb_admin_notification(epkb_vars.not_saved + ' ' + epkb_vars.msg_try_again, msg, 'error');
        }).always(function ()
        {
            $('#epkb-ajax-in-progress').dialog('close');

            if ( msg ) {
                $('.eckb-bottom-notice-message').replaceWith(msg);
                $("html, body").animate({scrollTop: 0}, "slow");
            }
        });
    });

	// SAVE WPML Enabled configuration
	$( '#epkb_save_wpml_settings' ).on( 'click', function (e) {
		e.preventDefault();  // do not submit the form
		var msg = '';

		var postData = {
			action: 'epkb_save_wpml_settings',
			_wpnonce_epkb_save_wpml_settings: $('#_wpnonce_epkb_save_wpml_settings').val(),
			epkb_kb_id: $('#epkb_kb_id').val(),
            epkb_wpml_is_enabled: $('#epkb_wpml_is_enabled').is(':checked')
		};

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: postData,
			url: ajaxurl,
			beforeSend: function (xhr)
			{
				//noinspection JSUnresolvedVariable
				$('#epkb-ajax-in-progress').text(epkb_vars.save_config).dialog('open');
			}

		}).done(function (response)
		{
			response = ( response ? response : '' );
			if ( ! response.error && typeof response.message !== 'undefined' )
			{
				msg = response.message;

			} else {
				//noinspection JSUnresolvedVariable
				msg = response.message ? response.message : epkb_admin_notification('', epkb_vars.reload_try_again, 'error');
			}

		}).fail(function (response, textStatus, error)
		{
			//noinspection JSUnresolvedVariable
			msg = ( error ? ' [' + error + ']' : epkb_vars.unknown_error );
			//noinspection JSUnresolvedVariable
			msg = epkb_admin_notification(epkb_vars.not_saved + ' ' + epkb_vars.msg_try_again, msg, 'error');

		}).always(function ()
		{
			$('#epkb-ajax-in-progress').dialog('close');

			if ( msg ) {
				$('.eckb-bottom-notice-message').replaceWith(msg);
				$("html, body").animate({scrollTop: 0}, "slow");
			}
		});
	});

    /********************************************************************************************
     *
     *                ARTICLES / CATEGORIES SEQUENCE ORDERING
     *
     ********************************************************************************************/

    {
        // enable custom ordering
        function epkb_custom_ordering() {
            var isDisabled;

            isDisabled = ( ! $('#epkb-main-page-button').hasClass('epkb-active-page') || ! $( '#eckb-mm-mp-links-organize' ).hasClass('epkb-mm-active') ) &&
                         ( ! $('#epkb-article-page-button').hasClass('epkb-active-page') || ! $( '#eckb-mm-ap-links-organize' ).hasClass('epkb-mm-active') );

            epkb_add_custom_ordering();

            $('.epkb-config-content .epkb-top-categories-list').sortable("option", "disabled", isDisabled);
            $('.epkb-config-content .eckb-categories-list').sortable("option", "disabled", isDisabled);
            $('.epkb-config-content .eckb-sub-category-ordering').sortable("option", "disabled", isDisabled);
            $('.epkb-config-content .eckb-sub-sub-category-ordering').sortable("option", "disabled", isDisabled);
            $('.epkb-config-content .eckb-articles-ordering').sortable("option", "disabled", isDisabled);

            var style = isDisabled ? 'auto' : 'move';
            $('#epkb-content-container').css('cursor', style, 'important');
            $('.epkb-config-content').find( 'a' ).css('cursor', style, 'important');
        }

        function epkb_select_categories_custom_ordering() {
            epkb_order_sequence_updated = true;
            if ( $('#epkb-main-page').closest('.epkb-info-pages').hasClass('epkb-active-page') ) {
                $('#epkb-config-ordering-sidebar').find('input[name=categories_display_sequence][value=user-sequenced]').click();
            } else if ( $('#epkb-article-page').closest('.epkb-info-pages').hasClass('epkb-active-page') ) {
                $('#epkb-config-article-ordering-sidebar').find('input[name=categories_display_sequence][value=user-sequenced]').click();
            }
        }

        function epkb_add_custom_ordering() {

            // Order Top Categories for Tabs layout
            $('.epkb-config-content .epkb-top-categories-list').sortable({
                axis: 'x',
                forceHelperSize: true,
                forcePlaceholderSize: true,
                // handle: '.epkb-sortable-articles',
                opacity: 0.8,
                placeholder: 'epkb-sortable-placeholder',
                update: function (event, ui)
                {
                    epkb_select_categories_custom_ordering();
                    epkb_process_user_sequence_change( 'main_page', epkb_get_new_main_page_sequence() );
                }
            });

            // Order Categories
            $('.epkb-config-content .eckb-categories-list').sortable({
                axis: 'x,y',
                forceHelperSize: true,
                forcePlaceholderSize: true,
                // handle: '.epkb-sortable-articles',
                opacity: 0.8,
                placeholder: 'epkb-sortable-placeholder',
                /* doesn't work well:  start: function (event, ui) {
                 // do not move Uncategorized
                 if ( ! ui.item.find('[data-kb-category-id]').data('kb-category-id') ) {
                 return false;
                 }
                 }, */
                update: function (event, ui)
                {
                    epkb_select_categories_custom_ordering();
                }
            });

            // Order Sub-categories
            $('.epkb-config-content .eckb-sub-category-ordering').sortable({
                axis: 'x,y',
                forceHelperSize: true,
                forcePlaceholderSize: true,
                // handle: '.epkb-sortable-articles',
                opacity: 0.8,
                placeholder: 'epkb-sortable-placeholder',
                update: function (event, ui)
                {
                    epkb_select_categories_custom_ordering();
                }
            });

            // Order Sub-sub-categories
            $('.epkb-config-content .eckb-sub-sub-category-ordering').sortable({
                axis: 'x,y',
                forceHelperSize: true,
                forcePlaceholderSize: true,
                // handle: '.epkb-sortable-articles',
                opacity: 0.8,
                placeholder: 'epkb-sortable-placeholder',
                update: function (event, ui)
                {
                    epkb_select_categories_custom_ordering();
                }
            });

            // Order Articles
            $('.epkb-config-content .eckb-articles-ordering, .eckb-articles-ordering').sortable({
                axis: 'y',
                forceHelperSize: true,
                forcePlaceholderSize: true,
                // handle: '.epkb-sortable-articles',
                opacity: 0.8,
                placeholder: 'epkb-sortable-placeholder',
                update: function (event, ui)
                {
                    epkb_order_sequence_updated = true;

                    if ( $('#epkb-main-page').closest('.epkb-info-pages').hasClass('epkb-active-page') ) {
                        $('#epkb-config-ordering-sidebar').find('input[name=articles_display_sequence][value=user-sequenced]').click();
                    } else if ( $('#epkb-article-page').closest('.epkb-info-pages').hasClass('epkb-active-page') ) {
                        $('#epkb-config-article-ordering-sidebar').find('input[name=articles_display_sequence][value=user-sequenced]').click();
                    }
                }
            });
        }

        function epkb_get_new_main_page_sequence() {
            var new_sequence = '';
            $('#epkb-main-page-content').find('[data-kb-type]').each(function(i, obj) {

                // some layouts like Tabs Layout has top categories and sub-categories "disconnected". Connect them here
                var top_cat_id = $(this).data('kb-top-category-id') ? $(this).data('kb-top-category-id') : '';
                if ( top_cat_id ) {
                    new_sequence += 'xx' + top_cat_id + 'x' + 'category';
                }
                
                if ( typeof $(this).attr("data-kb-type") !== 'undefined' && $(this).attr("data-kb-type") == 'top-category-no-articles' ) {
                    return true;
                }

                // add sub-category or articles
                var category_id = typeof $(this).data('kb-category-id') === 'undefined' ? $(this).data('kb-article-id') : $(this).data('kb-category-id');
                if ( typeof category_id !== 'undefined' ) {
                    new_sequence += 'xx' + category_id + 'x' + $(this).attr("data-kb-type");
                }
            });
            return new_sequence;
        }

        function epkb_get_new_article_page_sequence() {
            var new_sequence = '';
            $('#epkb-article-page-content').find('[data-kb-type]').each(function(i, obj) {

                // some layouts like Tabs Layout has top categories and sub-categories "disconnected". Connect them here
                var top_cat_id = $(this).data('kb-top-category-id') ? $(this).data('kb-top-category-id') : '';
                if ( top_cat_id ) {
                    new_sequence += 'xx' + top_cat_id + 'x' + 'category';
                }

                var category_id = typeof $(this).data('kb-category-id') === 'undefined' ? $(this).data('kb-article-id') : $(this).data('kb-category-id');
                if ( typeof category_id !== 'undefined' ) {
                    new_sequence += 'xx' + category_id + 'x' + $(this).attr("data-kb-type");
                }
            });
            return new_sequence;
        }

        function epkb_get_top_category_seq() {

            var top_cat_sequence = '';
            var kb_article_page_layout = $('#kb_article_page_layout input[name=kb_article_page_layout]:checked').val();
            var kb_main_page_layout = $('#kb_main_page_layout input[name=kb_main_page_layout]:checked').val();
            var use_top_sequence = kb_main_page_layout === 'Tabs' && ! epkb_switch_from_article_sidebar &&
                ! ( $('#epkb-article-page-button').hasClass('epkb-active-page') && kb_article_page_layout === 'Sidebar' );

            // switch from Article Page with Sidebar happens only once
            epkb_switch_from_article_sidebar = false;

            if ( ! use_top_sequence || typeof $('.epkb-top-categories-list') === 'undefined' ) {
                return top_cat_sequence;
            }

            $('.epkb-top-categories-list').children().each(function(i, obj) {
                var top_cat_id = $(this).find('[data-kb-category-id]').data('kb-category-id');
                if ( top_cat_id ) {
                    top_cat_sequence += 'xx' + top_cat_id;
                }
            });

            epkb_order_sequence_updated = true;

            return top_cat_sequence;
        }

        // update left pane when user selects a different article sequence
        $('#epkb-main-page-settings').on( 'change', '#articles_display_sequence', function (e) {
            e.preventDefault();
            epkb_process_user_sequence_change( 'main_page', epkb_get_new_main_page_sequence() );

        });
        $('#epkb-article-page-settings').on( 'change', '#articles_display_sequence', function (e) {
            e.preventDefault();
            epkb_process_user_sequence_change( 'article_page', epkb_get_new_article_page_sequence() );

        });

        // update left pane when user selects a different category sequence
        $('#epkb-main-page-settings').on( 'change', '#categories_display_sequence', function (e) {
            e.preventDefault();
            epkb_process_user_sequence_change( 'main_page', epkb_get_new_main_page_sequence() );

        });
        $('#epkb-article-page-settings').on( 'change', '#categories_display_sequence', function (e) {
            e.preventDefault();
            epkb_process_user_sequence_change( 'article_page', epkb_get_new_article_page_sequence() );

        });

        function epkb_process_user_sequence_change( page, new_sequence ) {

            if ( new_sequence.length == 0 ) {
                return;
            }

            var postData = {
                action: 'epkb_change_article_category_sequence',
                _wpnonce_epkb_save_kb_config: $('#_wpnonce_epkb_save_kb_config').val(),
                epkb_kb_id: $('#epkb_kb_id').val(),
                form: $('#epkb-config-config, #epkb-config-config2').serialize(),
                page_type: page,
                articles_sequence_new_value: epkb_get_field_value('articles_display_sequence', 'articles_display_sequence'),
                categories_sequence_new_value: epkb_get_field_value('categories_display_sequence', 'categories_display_sequence'),
                epkb_new_sequence: new_sequence,
                epkb_chosen_main_page_layout: $('#kb_main_page_layout input[name=kb_main_page_layout]:checked').val(),
                epkb_demo_kb: $('#epkb-layout-preview-data').is(':checked'),
                top_cat_sequence: epkb_get_top_category_seq()
            };
            var msg;

            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: postData,
                url: ajaxurl,
                beforeSend: function (xhr)
                {
                    $('#epkb-ajax-in-progress').text( epkb_vars.switching_article_seq ).dialog('open');
                }
            }).done(function (response)
            {
                response = ( response ? response : '' );
                if ( ! response.error && typeof response.message !== 'undefined' && typeof response.kb_page_output !== 'undefined' )
                {
                    msg = response.message;

                    if ( page == 'main_page' ) {
                        $('#epkb-main-page-content').html(response.kb_page_output);
                        $('#epkb-main-page-content').trigger('main_content_changed');
                    } else {
                        $('#epkb-article-page-content').html(response.kb_page_output);
                        $('#epkb-article-page-content').trigger('article_content_changed');
                    }

                } else {
                    //noinspection JSUnresolvedVariable
                    msg = response.message ? response.message : epkb_admin_notification('', epkb_vars.reload_try_again, 'error');
                }

            }).fail( function ( response, textStatus, error )
            {
                //noinspection JSUnresolvedVariable
                msg = ( error ? ' [' + error + ']' : epkb_vars.unknown_error );
                //noinspection JSUnresolvedVariable
                msg = epkb_admin_notification(epkb_vars.error_occurred + '. ' + epkb_vars.msg_try_again, msg, 'error');
            }).always(function ()
            {
                $('#epkb-ajax-in-progress').dialog('close');

               // epkb_set_tabs_width();

                if ( msg ) {
                    $('.eckb-bottom-notice-message').replaceWith(msg);
                    $( "html, body" ).animate( {scrollTop: 0}, "slow" );
                }
            });
        }
    }


    /********************************************************************************************
     *
     *                TABS LAYOUT
     *
     ********************************************************************************************/

    {
        // Set Tabs with based on how many are there and divide it up based on the container width.
    	/*function epkb_set_tabs_width() {
            var containerWidth = $('#epkb-content-container').outerWidth();
            var tabCount = $('.epkb_top_categories').length;
            $('.epkb_top_categories').css("width", ( containerWidth / tabCount ));
        }
        epkb_set_tabs_width();*/

        //Get the highest height of Tab and make all other tabs the same height
    	function epkb_set_tabs_height(){

            var navTabsLi = $('.epkb-nav-tabs li');

            var tallestHeight = 0;

        	$('#epkb-content-container').find( navTabsLi ).each( function(){

                var this_element = $(this).outerHeight(true);
            	if( this_element > tallestHeight ) {
                    tallestHeight = this_element;
                }
            });
            $('#epkb-content-container').find(navTabsLi).css('min-height', tallestHeight);
        }
        epkb_set_tabs_height();

        //Get the highest height of Tab and make all other tabs the same height when user selects Main page
        $( '#epkb-config-main-info').on( 'click', '.epkb-info-pages', function(){
            epkb_set_tabs_height();
        } );


        // Tabs Layout: switch to the top category user clicked on
        $('#epkb-main-page-content').on('click', '.epkb_top_categories', function () {
            // switch tab
            $(this).parent().find('li').removeClass('active');
            $(this).addClass('active');
            // switch content
            $('#epkb-main-page-container').find('.epkb-tab-panel').removeClass('active');
            $('.epkb-panel-container .epkb-tab-panel:nth-child(' + ($(this).index() + 1) + ')').addClass('active');
        });
    }


    /********************************************************************************************
     *
     *                CATEGORY SECTIONS
     *
     ********************************************************************************************/

    {
        /**
         * 1. ICON TOGGLE for Sub Category - toggle between open icon and close icon
         */
        $('#epkb-main-page-content').on('click', '.epkb-section-body .epkb-category-level-2-3', function (){

            var plus_icons = ['ep_font_icon_plus', 'ep_font_icon_minus'];
            var plus_icons_box = ['ep_font_icon_plus_box', 'ep_font_icon_minus_box'];
            var arrow_icons1 = ['ep_font_icon_right_arrow', 'ep_font_icon_down_arrow'];
            var arrow_icons2 = ['ep_font_icon_arrow_carrot_right', 'ep_font_icon_arrow_carrot_down'];
            var arrow_icons3 = ['ep_font_icon_arrow_carrot_right_circle', 'ep_font_icon_arrow_carrot_down_circle'];
            var folder_icon = ['ep_font_icon_folder_add', 'ep_font_icon_folder_open'];

            var icon = $(this).find('i');
            function toggle_category_icons($array) {

                 //If Parameter Icon exists
                if ( icon.hasClass( $array[0] ) ) {

                    icon.removeClass( $array[0] );
                    icon.addClass( $array[1] );

                } else if ( icon.hasClass( $array[1] )) {

                    icon.removeClass( $array[1] );
                    icon.addClass($array[0]);
                }
            }

            toggle_category_icons(plus_icons);
            toggle_category_icons(plus_icons_box);
            toggle_category_icons(arrow_icons1);
            toggle_category_icons(arrow_icons2);
            toggle_category_icons(arrow_icons3);
            toggle_category_icons(folder_icon);
        });

        /**
         *  2. SHOW ITEMS in SUB-CATEGORY
         */
        $('#epkb-main-page-content').on('click', '.epkb-section-body .epkb-category-level-2-3', function () {
            $(this).next().toggleClass('active');
        });

        /**
         * 3. SHOW ALL articles functionality
         *
         * When user clicks on the "Show all articles" it will toggle the "hide" class on all hidden articles
         */
        $('#epkb-main-page-content').on('click', '.epkb-show-all-articles', function () {

            $(this).toggleClass('active');
            var parent = $(this).parent('ul');
            var article = parent.find('li');

            //If this has class "active" then change the text to Hide extra articles
            if ($(this).hasClass('active')) {

                //If Active
                $(this).find('.epkb-show-text').addClass('epkb-hide-elem');
                $(this).find('.epkb-hide-text').removeClass('epkb-hide-elem');

            } else {
                //If not Active
                $(this).find('.epkb-show-text').removeClass('epkb-hide-elem');
                $(this).find('.epkb-hide-text').addClass('epkb-hide-elem');
            }

            $(article).each(function () {

                //If has class "hide" remove it and replace it with class "Visible"
                if ($(this).hasClass('epkb-hide-elem')) {
                    $(this).removeClass('epkb-hide-elem');
                    $(this).addClass('visible');
                } else if ($(this).hasClass('visible')) {
                    $(this).removeClass('visible');
                    $(this).addClass('epkb-hide-elem');
                }
            });
        });
    }

	/********************************************************************************************
	 *
	 *                CATEGORY ICON
	 *
	 /********************************************************************************************/

    // user clicks location of icon configuration
	$('#epkb-main-page-settings').on( 'change', '#section_head_category_icon_location', function() {
	    // uncheck icon selection in dropdown
		$('#section_head_category_icon_group').find('input').attr('checked', 'unchecked');

		toggle_icon_settings( $( this ).val() );

	});

	/**
	 * Toggle the Icon settings based on the Icon location value
	 * @param {string} value - Select value
	 */
	function toggle_icon_settings( value ){

		//show/Hide Icon List and Size based on Icon location settings.
		if( value === 'no_icons' ){
			$('#epkb-main-page-settings').find( '#section_head_category_icon_group' ).hide();
			$('#epkb-main-page-settings').find( '#section_head_category_icon_size_group' ).hide();
		}else{
			$('#epkb-main-page-settings').find( '#section_head_category_icon_group' ).show();
			$('#epkb-main-page-settings').find( '#section_head_category_icon_size_group' ).show();
		}

    }

    toggle_icon_settings( $( '#section_head_category_icon_location' ).val() );

    // user clicks on a new category box. update its selection in configuration box
    $( '[id$=pkb-main-page-content]' ).on( 'click', '.epkb-top-category-box', function(){

        if ( ! $('[id$=pkb-config-styles-sidebar]').is(':visible') ) {
            return;
        }

        // clear Icon Category selection
        $( '.epkb-top-category-box' ).removeClass('epkb-category-selection-color');

        // select this element
        $(this).addClass('epkb-category-selection-color');

        // enable dropdown
	    $('#epkb-content-container').find('[id^=section_head_category_icon]').attr("disabled", false);

	    //Change the Name in the sidebar "Category Icon for: X " for the selected category.
        $('.epkb-config-category-name').html( '<strong>' + $(this).find('.epkb-cat-name').text()+ '</strong>' );

	    //Get Icon name of selected Category
        var icon_name = $(this).find('.epkb-cat-icon').attr('data-kb-category-icon');

	    //Set Config radio button on sidebar to checked and highlighted.
	    $('#section_head_category_icon_group').find('input[value=' + icon_name + ']').attr('checked', 'checked');

	    show_selected_category_option( icon_name );

    });


	/**
	 * In Config Sidebar show the Radio button that is selected for Category when choosing Icons.
	 *
	 * @since 4.0.0
	 *
	 * @param {string}  icon_name   The Selected Cateogry Icon from the Main page display.
	 */
    function show_selected_category_option( icon_name ){

        // Set the Scroll Bar position to the top as a default.
	    $("#section_head_category_icon").scrollTop( 0 );

        // Get the position of the checked radio button parent list item, this will be relative to the overall container.
        var checked_option = $('#section_head_category_icon_group').find( 'input[value=' + icon_name + ']' ).parents( 'li' ).position();

        // Don't do anything if the selected option is already at the top. This prevents weird jumping behaviour
        if ( checked_option.top != 0 ) {
            // Move scroll position to checked radio option.
	        $("#section_head_category_icon").scrollTop(  checked_option.top  );
        }
    }


	// user clicks back
	$( '#ekb-admin-page-wrap' ).find( '[class$=pkb-menu-back]' ).on( 'click', function(e){
		// clear Icon Category selection
		$('.epkb-top-category-box').removeClass('epkb-category-selection-color');
		$('.epkb-config-category-name').text( '[ select category on the left ]' );
		$('#epkb-content-container').find('[id^=section_head_category_icon]').attr("disabled", true);
	});

	// handle user click on a new icon for a chosen category
    $('#epkb-main-page-settings').on('change', '#section_head_category_icon', function (e) {

        // find selected Category
        var selected_category = $('#epkb-main-page-container').find('.epkb-category-selection-color').find('.epkb-cat-icon');
        if (selected_category.length === 0) {
            return;
        }

        // update the selected Category with new icon
        var icon_name = e.target.value;
        selected_category.removeClass(selected_category.attr('data-kb-category-icon'));
        selected_category.addClass(icon_name);
        selected_category.attr('data-kb-category-icon', icon_name);

        epkb_set_categories_icons();
    });

	// initialize categories icons
	function epkb_set_categories_icons() {
		$('#epkb_save_categories_icons').val('icons-saved');
		var categories_icons = $('#epkb-content-container').find('.epkb-cat-icon').map(function() {
			return $(this).attr('data-kb-category-icon') + '=' + $(this).parent().data('kb-category-id');
		}).get().join('.');
		$('#epkb_categories_icons').val(categories_icons);
	}

	$('#epkb-content-container').find('[id^=section_head_category_icon]').attr("disabled", true);



	/********************************************************************************************
     *
     *                OTHER
     *
     ********************************************************************************************/

    // cleanup after Ajax calls
    var epkb_timeout;
    $(document).ajaxComplete(function () {

        clearTimeout(epkb_timeout);

        //Add fadeout class to notice after set amount of time has passed.
        epkb_timeout = setTimeout(function () {
                ekb_admin_page_wrap.find('.eckb-bottom-notice-message').addClass('fadeOutDown');
            }
            , 10000);

        //Add fadeout class to notice if close icon clicked.
        ekb_admin_page_wrap.find('.eckb-bottom-notice-message').on('click', '.epkb-close-notice', function (){
            $(this).parent().addClass('fadeOutUp');
        });

        // highlight text for clicked on radio button
        $( '.epkb-config-sidebar, #mega-menu-main-page-layout' ).find( ":radio" ).on('click', function(){
            epkb_highlight_text_for_checked_radio_button( $( this ) );
        });

        // Highlight text for checked radio button
        $( '.epkb-config-sidebar, #mega-menu-main-page-layout' ).find( ":radio" ).each( function(){
            epkb_highlight_text_for_checked_radio_button( $(this) );
        });

        // highlight text for clicked on radio button
        $( '#templates_for_kb_group' ).find( ":radio" ).on('click', function(){
            epkb_highlight_text_for_checked_radio_button( $( this ) );
        });
    });

    function epkb_highlight_text_for_checked_radio_button( $this ){
        $this.parents( '.radio-buttons-vertical' ).find('.input_container').removeClass( 'checked-radio' );
        if ( $this.attr( "checked" ) ) {
            $this.parent().addClass( 'checked-radio' );
        }
    }

    // SHOW INFO MESSAGES
    function epkb_admin_notification( $title, $message , $type ) {
        return '<div class="eckb-bottom-notice-message">' +
                   '<div class="contents">' +
                        '<span class="' + $type + '">' +
                             ($title ? '<h4>' + $title + '</h4>' : '' ) +
                             ($message ? $message : '') +
                        '</span>' +
                    '</div>' +
                '<div class="epkb-close-notice epkbfa epkbfa-window-close"></div>'+
            '</div>';
    }

    // get value of a form field
    function epkb_get_field_value( field_name, valueName ) {
        var values = {};
        $.each($("input[id^=" + field_name + "]").serializeArray(), function (i, field) {
            values[field.name] = field.value;
        });

        return typeof values[valueName] === 'undefined' ? '' : values[valueName];
    }

	/**
	 * Show and Hide the Mega Menu with FlexBox
	 * Since Javascript uses display block, we need to handle it now with inline CSS.
	 *
	 * @param {string}   display_type          The display type to handle. ( toggle, show, hide )
	 *
	 */
    function display_mega_menu_with_flexbox( display_type ){

        if( display_type === 'show' ){
	        $( '#epkb-admin-mega-menu' ).css( 'display', 'flex' );
        }
		if( display_type === 'hide' ){
			$( '#epkb-admin-mega-menu' ).css( 'display', 'none' );
		}
    }

    // setup dialog for WP Editor if necessary
    if ( $('#eckb-hidden-wp-editor-container') && typeof tinyMCE !== "undefined" ) {
        tinyMCE.init({selector: '#sidebar_main_page_intro_text_eckb_editor'});

        $( '#epkb-main-page-settings' ).on( 'click', '#eckb-wp-editor-update', function(){
            $('#eckb-hidden-wp-editor-container').addClass('eckb-popup').show();
        });

        $( '#eckb-hidden-wp-editor-container' ).on( 'click', '#eckb-popup-ok-button', function(){

            // IF HTML Tab active, then UPDATE Visual tab by triggering a click event on it.
            if( $( '#wp-sidebar_main_page_intro_text_eckb_editor-wrap' ).hasClass( 'html-active' ) ){
                $( '.switch-tmce' ).trigger( "click" );
            }
            
           // tinyMCE.triggerSave();
            $('#eckb-hidden-wp-editor-container').removeClass('eckb-popup').hide();

            var textarea_id = typeof $('#eckb-hidden-wp-editor-container').find('textarea').attr('id') === 'undefined' ? '' :
                              $('#eckb-hidden-wp-editor-container').find('textarea').attr('id');
            if ( typeof tinyMCE.activeEditor === 'undefined' || textarea_id.length == 0 ) {
                return;
            }

            var content = tinyMCE.activeEditor.getContent();
            var id = '#' + textarea_id.replace('_eckb_editor', '');
            if ( typeof $(id) !== 'undefined' ) {
                $(id).text(content);
                $(id).text(content);
            }

            epkb_update_preview_on_change();
        });

        $( '#eckb-hidden-wp-editor-container' ).on( 'click', '#eckb-popup-cancel-button', function(){
            $('#eckb-hidden-wp-editor-container').removeClass('eckb-popup').hide();
        });
    }


    /********************************************************************************************
     *
     *                Overview
     *
     ********************************************************************************************/
    $( '.epkb-config-container' ).on( 'click', '.overview-toggle', function (e) {
        $( this ).parents( '.overview-info-section').find( '.overview-content').slideToggle();
    });

	//If Overview Welcome section configure KB button was clicked.
	var query = window.location.search;
	if (query.toLowerCase().indexOf("ekb-main-page") >= 0) {
		$( "#epkb-main-page" ).trigger( "click" );
	}

});