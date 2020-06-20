jQuery(document).ready(function($) {

	/* Variables -----------------------------------------------------------------*/
	var knowledgebase = $( '#epkb-main-page-container' );
	var tabContainer = $('#epkb-content-container');
	var navTabsLi    = $('.epkb-nav-tabs li');
	var tabPanel     = $('.epkb-tab-panel');
	var articleContent = $('#eckb-article-content-body');
	var articleToc     = $('.eckb-article-toc');

	/********************************************************************
	 *                      Search
	 ********************************************************************/

	// handle KB search form
	$( 'body' ).on( 'submit', '#epkb_search_form', function( e ) {
		e.preventDefault();  // do not submit the form

		if ( $('#epkb_search_terms').val() === '' ) {
			return;
		}

		var postData = {
			action: 'epkb-search-kb',
			epkb_kb_id: $('#epkb_kb_id').val(),
			search_words: $('#epkb_search_terms').val()
		};

		var msg = '';

		$.ajax({
			type: 'GET',
			dataType: 'json',
			data: postData,
			url: ajaxurl,
			beforeSend: function (xhr)
			{
				//Loading Spinner
				$( '.loading-spinner').css( 'display','block');
				$('#epkb-ajax-in-progress').show();
			}

		}).done(function (response)
		{
			response = ( response ? response : '' );

			//Hide Spinner
			$( '.loading-spinner').css( 'display','none');

			if ( response.error || response.status !== 'success') {
				//noinspection JSUnresolvedVariable
				msg = epkb_vars.msg_try_again;
			} else {
				msg = response.search_result;
			}

		}).fail(function (response, textStatus, error)
		{
			//noinspection JSUnresolvedVariable
			msg = epkb_vars.msg_try_again + '. [' + ( error ? error : epkb_vars.unknown_error ) + ']';

		}).always(function ()
		{
			$('#epkb-ajax-in-progress').hide();

			if ( msg ) {
				$( '#epkb_search_results' ).css( 'display','block' );
				$( '#epkb_search_results' ).html( msg );

			}

		});
	});

	$("#epkb_search_terms").keyup(function() {
		if (!this.value) {
			$('#epkb_search_results').css( 'display','none' );
		}
	});


	/********************************************************************
	 *                      Tabs / Mobile Select
	 ********************************************************************/

	//Get the highest height of Tab and make all other tabs the same height
	$(window).on('load', function(){
		var tallestHeight = 0;
		tabContainer.find( navTabsLi ).each( function(){

			var this_element = $(this).outerHeight( true );
			if( this_element > tallestHeight ) {
				tallestHeight = this_element;
			}
		});
		tabContainer.find( navTabsLi ).css( 'min-height', tallestHeight );
	});

	function changePanels( Index ){
		$('.epkb-panel-container .epkb-tab-panel:nth-child(' + (Index + 1) + ')').addClass('active');
	}

	function updateTabURL( tab_id, tab_name ) {
		var location = window.location.href;
		location = update_query_string_parameter(location, 'top-category', tab_name);
		window.history.pushState({"tab":tab_id}, "title", location);
		// http://stackoverflow.com/questions/32828160/appending-parameter-to-url-without-refresh
	}

	window.onpopstate = function(e){

		if ( e.state && e.state.tab.indexOf('epkb_tab_') !== -1) {
			//document.title = e.state.pageTitle;

			// hide old section
			tabContainer.find('.epkb_top_panel').removeClass('active');

			// re-set tab; true if mobile drop-down
			if ( $( "#main-category-selection" ).length > 0 )
			{
				$("#main-category-selection").val(tabContainer.find('#' + e.state.tab).val());
			} else {
				tabContainer.find('.epkb_top_categories').removeClass('active');
				tabContainer.find('#' + e.state.tab).addClass('active');
			}

			tabContainer.find('.' + e.state.tab).addClass('active');

		// if user tabs back to the initial state, select the first tab if not selected already
		} else if ( $('#epkb_tab_1').length > 0 && ! tabContainer.find('#epkb_tab_1').hasClass('active') ) {

			// hide old section
			tabContainer.find('.epkb_top_panel').removeClass('active');

			// re-set tab; true if mobile drop-down
			if ( $( "#main-category-selection" ).length > 0 )
			{
				$("#main-category-selection").val(tabContainer.find('#epkb_tab_1').val());
			} else {
				tabContainer.find('.epkb_top_categories').removeClass('active');
				tabContainer.find('#epkb_tab_1').addClass('active');
			}

			tabContainer.find('.epkb_tab_1').addClass('active');
		}
	};

	// Tabs Layout: switch to the top category user clicked on
	tabContainer.find( navTabsLi ).each(function(){

		$(this).on('click', function (){
			tabContainer.find( navTabsLi ).removeClass('active');

			$(this).addClass('active');

			tabContainer.find(tabPanel).removeClass('active');
			changePanels ( $(this).index() );
			updateTabURL( $(this).attr('id'), $(this).data('cat-name') );
		});
	});

	// Tabs Layout: MOBILE: switch to the top category user selected
	$( "#main-category-selection" ).change(function() {
			tabContainer.find(tabPanel).removeClass('active');
			// drop down
			$( "#main-category-selection option:selected" ).each(function() {
				var selected_index = $( this ).index();
				changePanels ( selected_index );
				updateTabURL( $(this).attr('id'), $(this).data('cat-name') );
			});
		});

	function update_query_string_parameter(uri, key, value) {
		var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
		var separator = uri.indexOf('?') !== -1 ? "&" : "?";
		if (uri.match(re)) {
			return uri.replace(re, '$1' + key + "=" + value + '$2');
		}
		else {
			return uri + separator + key + "=" + value;
		}
	}


	/********************************************************************
	 *                      Sections
	 ********************************************************************/

	//Detect if a an div is inside an list item then it's a sub category
	$('.epkb-section-body .epkb-category-level-2-3').each(function(){

		$(this).on('click', function(){

			$(this).parent().children('ul').toggleClass('active'); 

		});
	});

	/**
	 * Sub Category icon toggle
	 *
	 * Toggle between open icon and close icon
	 */
	tabContainer.find('.epkb-section-body .epkb-category-level-2-3').each(function(){

		var $icon = $(this).find('i');

		$(this).on('click', function (){

			var plus_icons = [ 'ep_font_icon_plus' ,'ep_font_icon_minus' ];
			var plus_icons_box = [ 'ep_font_icon_plus_box' ,'ep_font_icon_minus_box' ];
			var arrow_icons1 = [ 'ep_font_icon_right_arrow' ,'ep_font_icon_down_arrow' ];
			var arrow_icons2 = [ 'ep_font_icon_arrow_carrot_right' ,'ep_font_icon_arrow_carrot_down' ];
			var arrow_icons3 = [ 'ep_font_icon_arrow_carrot_right_circle' ,'ep_font_icon_arrow_carrot_down_circle' ];
			var folder_icon = [ 'ep_font_icon_folder_add' ,'ep_font_icon_folder_open' ];

			function toggle_category_icons( $array ){

				//If Parameter Icon exists
				if( $icon.hasClass( $array[0] ) ){

					$icon.removeClass( $array[0] );
					$icon.addClass( $array[1] );

				}else if ( $icon.hasClass( $array[1] )){

					$icon.removeClass( $array[1] );
					$icon.addClass($array[0]);
				}
			}

			toggle_category_icons( plus_icons );
			toggle_category_icons( plus_icons_box );
			toggle_category_icons( arrow_icons1 );
			toggle_category_icons( arrow_icons2 );
			toggle_category_icons( arrow_icons3 );
			toggle_category_icons( folder_icon );
		});
	});

	/**
	 * Show all articles functionality
	 *
	 * When user clicks on the "Show all articles" it will toggle the "hide" class on all hidden articles
	 */
	knowledgebase.find('.epkb-show-all-articles').on( 'click', function () {

		$( this ).toggleClass( 'active' );
		var parent = $( this ).parent( 'ul' );
		var article = parent.find( 'li');

		//If this has class "active" then change the text to Hide extra articles
		if ( $(this).hasClass( 'active')) {

			//If Active
			$(this).find('.epkb-show-text').addClass('epkb-hide-elem');
			$(this).find('.epkb-hide-text').removeClass('epkb-hide-elem');

		} else {
			//If not Active
			$(this).find('.epkb-show-text').removeClass('epkb-hide-elem');
			$(this).find('.epkb-hide-text').addClass('epkb-hide-elem');
		}

		$( article ).each(function() {

			//If has class "hide" remove it and replace it with class "Visible"
			if ( $(this).hasClass( 'epkb-hide-elem')) {
				$(this).removeClass('epkb-hide-elem');
				$(this).addClass('visible');
			}else if ( $(this).hasClass( 'visible')) {
				$(this).removeClass('visible');
				$(this).addClass('epkb-hide-elem');
			}
		});
	});
	
	let search_text = $( '#epkb-search-kb' ).text();
	$( '#epkb-search-kb' ).text( search_text );


	/********************************************************************
	 *                      Article TOC
	 ********************************************************************/
	 
	if (articleToc.length) {
		
		if ( !articleToc.data('min') ) {
			articleToc.data('min', 1);
		}
		
		if ( !articleToc.data('offset') ) {
			articleToc.data('offset', 50);
		}

		let firstLevel = articleToc.data('min');
		let searchStr = 'h' + firstLevel;
		let params = {'scrollOffset' : articleToc.data('offset')};
		let exclude_class = false;
		
		if ( typeof articleToc.data('exclude_class') !== 'undefined' ) {
			exclude_class = articleToc.data('exclude_class');
		}
		
		while ( firstLevel < 6 ) {
			firstLevel++;
			searchStr += ', h' + firstLevel;
		}
		
		// return object with headers and their ids 
		function getArticleHeaders() {
			let headers = [];
			
			articleContent.find(searchStr).each(function(){
				
				if ( $(this).text().length == 0 ) {
					return;
				}
				
				if ( exclude_class && $(this).hasClass( articleToc.data('exclude_class') ) ) {
					return;
				}
				
				let tid;
				let header = {};
					
				if ($(this).prop('id')) {
					tid = $(this).prop('id');
				} else {
					tid = 'articleTOC_' + headers.length;
					$(this).prop('id', tid);
				}
					
				header.id = tid;
				header.title = $(this).text();
					
				if ('H1' == $(this).prop("tagName")) {
					header.level = 1;
				} else if ('H2' == $(this).prop("tagName")) {
					header.level = 2;
				} else if ('H3' == $(this).prop("tagName")) {
					header.level = 3;
				} else if ('H4' == $(this).prop("tagName")) {
					header.level = 4;
				} else if ('H5' == $(this).prop("tagName")) {
					header.level = 5;
				} else if ('H6' == $(this).prop("tagName")) {
					header.level = 6;
				}
				
				headers.push(header);
			
			});
			
			if ( headers.length == 0 ) {
				return headers;
			}
			
			// find max and min header level 
			let maxH = 1;
			let minH = 6;
			
			headers.forEach(function(header){
				if (header.level > maxH) {
					maxH = header.level
				}
				
				if (header.level < minH) {
					minH = header.level
				}
			});
			
			// move down all levels to have 1 lowest 
			if ( minH > 1 ) {
				headers.forEach(function(header, i){
					headers[i].level = header.level - minH + 1;
				});
			}
			
			// now we have levels started from 1 but maybe some levels do not exist

			// check level exist and decrease if not exist 
			let i = 1;
			
			while (i < maxH) {
				let levelExist = false;
				headers.forEach(function(header){
					if (header.level == i) {
						levelExist = true;
					}
				});
				
				if (levelExist) {
					// all right, level exist, go to the next 
					i++;
				} else {
					// no such levelm move all levels that more than current down and check once more
					headers.forEach(function(header, j){
						if (header.level > i) {
							headers[j].level = header.level - 1;
						}
					});
				}
				i++;
			}
			
			return headers;
		}
		
		// return html from headers object 
		function getToCHTML(headers) {
			let html;
			
			if ( articleToc.find('.eckb-article-toc__title').length ) {
				let title = articleToc.find('.eckb-article-toc__title').html();
				html = `
					<div class="eckb-article-toc__inner">
						<div class="eckb-article-toc__title">${title}</div>
						<ul>
					`;
			} else {
				html = `
					<div class="eckb-article-toc__inner">
						<ul>
					`;
			}

			headers.forEach(function(header){
				html += `<li class="eckb-article-toc__level eckb-article-toc__level-${header.level}"><a href="#${header.id}">${header.title}</a></li>`;
			});
			
			html += `
						</ul>
					</div>
				`;
			
			return html;
		}

		let articleHeaders = getArticleHeaders();

		// show TOC only if headers preset
		if ( articleHeaders.length > 0 ) {
			articleToc.html(getToCHTML(articleHeaders));
			articleContent.find(searchStr).scrollSpy(params);

			if( $(' .eckb-article-toc--position-middle ').length > 0 ) {
				articleToc.css('display' , 'inline-block' );

			}else {
				articleToc.fadeIn();
			}
		}else {
			articleToc.hide();
		}


		// Get the Article Content Body Position
		let articleContentBodyPosition = $( '#eckb-article-content-body' ).position();


		//TODO: Dave - Change Sidebar position if TOC is in the Middle
		// If the setting is on, Offset the Sidebar to match the article Content
		if( $('.eckb-article-page--L-sidebar-to-content').length > 0 ){
			$('#eckb-article-page-container-v2').find( '#eckb-article-left-sidebar ').css( "margin-top" , articleContentBodyPosition.top+'px' );
		}
		if( $('.eckb-article-page--R-sidebar-to-content').length > 0 ){
			$('#eckb-article-page-container-v2').find( '#eckb-article-right-sidebar ').css( "margin-top" , articleContentBodyPosition.top+'px' );
		}


	}

});
