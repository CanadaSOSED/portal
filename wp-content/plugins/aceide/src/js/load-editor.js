var autocompleting = false;
var autocompletelength = 2;
var editor = '';

var saved_editor_sessions = [];
var saved_undo_manager = [];
var last_added_editor_session = 0;
var current_editor_session = 0;

var EditSession = require('ace/edit_session').EditSession;
var UndoManager = require('ace/undomanager').UndoManager;
var Search = require("ace/search").Search;
var TokenIterator = require("ace/token_iterator").TokenIterator;

var oHandler;

var editor_options = {resizer:{}};

// Fullscreen cross-browser fill
document.fullscreenEnabled = document.fullscreenEnabled ||
                             document.mozFullScreenEnabled ||
                             document.msFullscreenEnabled ||
                             document.documentElement.webkitRequestFullScreen;

function requestFullscreen(element) {
	if (element.requestFullscreen) {
		element.requestFullscreen();
		return true;
	} else if (element.mozRequestFullScreen) {
		element.mozRequestFullScreen();
		return true;
	} else if (element.webkitRequestFullScreen) {
		element.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
		return true;
	} else if (element.msRequestFullscreen) {
	    element.msRequestFullscreen();
	    return true;
	}

    return false;
}

function fullscreenOnChange(cb) {
    if (document.exitFullscreen) {
        document.addEventListener('fullscreenchange', cb);
    } else if (document.webkitExitFullscreen) {
        document.addEventListener('webkitfullscreenchange', cb);
    } else if (document.mozCancelFullScreen) {
        document.addEventListener('mozfullscreenchange', cb);
    } else if (document.msExitFullscreen) {
        document.addEventListener('msfullscreenchange', cb);
    }
}

function exitFullscreen() {
    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
    } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
    }
}

if (!document.hasOwnProperty('fullscreenElement')) {
    Object.defineProperty(document, 'fullscreenElement', {
        'get': function() {
            return document.webkitFullscreenElement || document.mozFullscreenElement || document.msFullscreenElement || null;
        }
    });
}

function onSessionChange(e)  {

	//set the document as unsaved
	jQuery(".aceide_tab.active", "#aceide_toolbar").data( "unsaved", true);
	jQuery("#aceide_footer_message_unsaved").html("[ Document contains unsaved content &#9998; ]").show();

	if( editor.getSession().enable_autocomplete === false){
		return;
	}

	//don't continue with autocomplete if /n entered
	try {
		if ( e.data.text.charCodeAt(0) === 10 ){
			return;
		}
	}catch(error){}

	try {
		if ( e.data.action == 'removeText' ){

		    if (autocompleting) {
				autocompletelength = (autocompletelength - 1) ;
			}else{
				return;
			}
		}
	}catch(error){}


	//get current cursor position
	range = editor.getSelectionRange();
	//take note of selection row to compare with search
	cursor_row = range.start.row;

	try{
	//quit autocomplete if we are writing a "string"
		var iterator = new TokenIterator(editor.getSession(), range.start.row, range.start.column);
		var current_token_type = iterator.getCurrentToken().type;
		if(current_token_type == "string" || current_token_type == "comment"){
			return;
		}
	}catch(error){}

	if (range.start.column > 0){

		//search for command text user has entered that we need to try match functions against
		var search = new Search().set({
			needle: "[\\n \.\)\(]",
			backwards: true,
			wrap: false,
			caseSensitive: false,
			wholeWord: false,
			regExp: true
		      });
		      //console.log(search.find(editor.getSession()));

		range = search.find(editor.getSession());

		if (range) range.start.column++;

	}else{ //change this to look char position, if it's starting at 0 then do this

		range.start.column = 0;
	}

	if (! range || range.start.row < cursor_row ){
		//forse the autocomplete check on this row starting at column 0
		range = editor.getSelectionRange();
		range.start.column = 0;
	}


	//console.log("search result - start row " + range.start.row + "-" + range.end.row + ", column " + range.start.column+ "-" + range.end.column);
	//console.log(editor.getSelection().getRange());

	range.end.column = editor.getSession().getSelection().getCursor().column +1;//set end column as cursor pos

	//console.log("[ \.] based: " + editor.getSession().doc.getTextRange(range));

	//no column lower than 1 thanks
	if (range.start.column < 1) {
		range.start.column = 0;
	}

	//console.log("after row " + range.start.row + "-" + range.end.row + ", column " + range.start.column+ "-" + range.end.column);
	//get the editor text based on that range
	var text = editor.getSession().doc.getTextRange(range);
	$quit_onchange = false;

	//console.log(text);

	//console.log("Searching for text \""+text+"\" length: "+ text.length);
	if (text.length < 3){

		aceide_close_autocomplete();
		return;
	}

	autocompletelength = text.length;

	//create the dropdown for autocomplete
	var sel = editor.getSelection();
	var session = editor.getSession();
	var lead = sel.getSelectionLead();

	var pos = editor.renderer.textToScreenCoordinates(lead.row, lead.column);
	var ac = document.getElementById('ac'); // #ac is auto complete html select element



	if( typeof ac !== 'undefined' ){

		//add editor click listener
		//editor clicks should hide the autocomplete dropdown
		editor.container.addEventListener('click', function(e){

			aceide_close_autocomplete();

			autocompleting=false;
			autocompletelength = 2;

		}, false);

	} //end - create initial autocomplete dropdown and related actions


	//calulate the editor container offset
	var obj=editor.container;

	var curleft = 0;
	var curtop = 0;

	if (obj.offsetParent) {

		do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
		} while (obj = obj.offsetParent);

	}


	//position autocomplete
	ac.style.top= ((pos.pageY - curtop)+20) + "px";
	ac.style.left= ((pos.pageX - curleft)+10) + "px";
	ac.style.display='block';
	ac.style.background='white';


	//remove all options, starting a fresh list
	ac.options.length = 0;


	//loop through WP tags and check for a match
	if (autocomplete_wordpress){
		var tag;
		for(i in autocomplete_wordpress) {
			//if(!html_tags.hasOwnProperty(i) ){
			//	continue;
			//}

			tag= i;
			//see if the tag is a match
			if( text !== tag.substr(0,text.length) ){
				continue;
			}

			//add parentheses
			tag = tag + "()";

			var option = document.createElement('option');
			option.text = tag;
			option.value = tag;
			option.setAttribute('title', aceide_app_path + 'images/wpac.png');//path to icon image or wpac.png


			try {
				ac.add(option, null); // standards compliant; doesn't work in IE
			}
			catch(ex) {
				ac.add(option); // IE only
			}

		}//end for
	}//end php autocomplete

	//loop through PHP tags and check for a match
	if (autocomplete_php){
		var tag;
		for(i in autocomplete_php) {
			//if(!html_tags.hasOwnProperty(i) ){
			//	continue;
			//}

			tag= i;
			//see if the tag is a match
			if( text !== tag.substr(0,text.length) ){
				continue;
			}

			//add parentheses
			tag = tag + "()";

			var option = document.createElement('option');
			option.text = tag;
			option.value = tag;
			option.setAttribute('title', aceide_app_path + 'images/phpac.png');//path to icon image or wpac.png

			try {
				ac.add(option, null); // standards compliant; doesn't work in IE
			}
			catch(ex) {
				ac.add(option); // IE only
			}


		}//end for
	}//end php autocomplete


	//check for matches
	if ( ac.length === 0 ) {
		aceide_close_autocomplete();
	} else {

		ac.selectedIndex=0;
		autocompleting=true;
		oHandler = jQuery("#ac").msDropDown({visibleRows:10, rowHeight:20}).data("dd");

		jQuery("#ac_child").click(function(item){
			//get the link node and pass to select AC item function
			if (typeof item.srcElement != 'undefined'){
				var link_node = item.srcElement; //works on chrome
			}else{
				var link_node = item.target; //works on Firefox etc
			}

			selectACitem(link_node);
		});

		jQuery("#ac_child a").mouseover(function(item){
			//show the code in the info panel

			//get the link ID
			if (typeof item.srcElement != 'undefined'){
				var link_id = item.srcElement.id; //works on chrome
			}else{
				var link_id = item.target.id; //works on Firefox etc
			}

			if (link_id == '') return; //if the link doesn't have an id it's not valid so just stop


			//if this command item is enabled
			if (jQuery("#"+link_id).hasClass("enabled")){

				var selected_item_index = jQuery("#"+link_id).index();

				if (selected_item_index > -1){ //if select item is valid

					//set the selected menu item
					oHandler.selectedIndex(selected_item_index);
					//show command help panel for this command
					aceide_function_help();

				}
			}

		});


		jQuery("#ac_child").css("z-index", "9999");
		jQuery("#ac_child").css("background-color", "#ffffff");
		jQuery("#ac_msdd").css("z-index", "9999");
		jQuery("#ac_msdd").css("position", "absolute");
		jQuery("#ac_msdd").css("top", ac.style.top);
		jQuery("#ac_msdd").css("left", ac.style.left);

		//show command help panel for this command
		aceide_function_help();

	}

}

function onSessionScroll(e) {
	var lh = editor.renderer.lineHeight,
		new_height = 0, mod = 0;

	// If we scroll to exactly the top of a line, this condition shall be 0.
	if (e % lh) {
		// We have not landed where we need to.
		// The variable "mod" will know how many pixels to move.
		mod = e % lh;

		// Now we know how many pixels to move, we need to deterime direction.
		//  We need to figure out another method
		if (mod > (lh/2)) {
			new_height = (e+mod);   // Down
		} else {
			new_height = (e-mod);   // Up
		}

		// This will trigger this event call again however will not trigger more
		// than once because new_height will be a perfect modulus of 0.
		//editor.session.setScrollTop(new_height);
	}
}

function token_test(){

	var iterator = new TokenIterator(editor.getSession(), range.start.row, range.start.column);
	var current_token_type = iterator.getCurrentToken().type;
	return iterator.getCurrentToken();
}

function aceide_close_autocomplete(){
	if (typeof document.getElementById('ac') != 'undefined') document.getElementById('ac').style.display='none';
	if (typeof oHandler != 'undefined') oHandler.close();

	autocompleting = false;

	//clear the text in the command help panel
	//jQuery("#aceide_info_content").html("");
}

function selectionChanged(e)  {
	var selected_text = editor.getSession().doc.getTextRange(editor.getSelectionRange());

	//check for hex colour match
	if ( selected_text.match('^#?([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?$')  != null ){

		var therange = editor.getSelectionRange();
		therange.end.column = therange.start.column;
		therange.start.column = therange.start.column-1;

		// only show color assist if the character before the selection indicates a hex color (#)
	    if ( editor.getSession().doc.getTextRange( therange ) == "#" ){
			jQuery("#aceide_color_assist").show();
	    }

	}
}

function aceide_function_help() {
  //mouse over

	try
	{
		var selected_command_item = jQuery("#ac_child a.selected");


			key = selected_command_item.find("span.ddTitleText").text().replace("()","");

			//wordpress autocomplete
			if ( selected_command_item.find("img").attr("src").indexOf("wpac.png")  >= 0){

			  if (autocomplete_wordpress[key].desc != undefined){

				//compose the param info
				var param_text ="";
				for(i=0; i<autocomplete_wordpress[key].params.length; i++) {

					//wrap params in a span to highlight not required
					if (autocomplete_wordpress[key].params[i].required == "no"){
						param_text = param_text + "<span class='aceide_func_arg_notrequired'>" + autocomplete_wordpress[key].params[i]['param'] + "<em>optional</em></span><br /> <br />";
					}else{
						param_text = param_text + autocomplete_wordpress[key].params[i]['param'] + "<br /> <br />";
					}

				}
				//compose returns text
				if (autocomplete_wordpress[key].returns.length > 0){
					returns_text = "<br /><br /><strong>Returns:</strong> " + autocomplete_wordpress[key].returns;
				}else{
					returns_text = "";
				}


				//output command info
				jQuery("#aceide_info_content").html(
					"<strong class='aceide_func_highlight_black'>Function: </strong><strong class='aceide_func_highlight'>" + key  + "(</strong><br />" +
								   "<span class='aceide_func_desc'>" + autocomplete_wordpress[key].desc + "</span><br /><br /><em class='aceide_func_params'>" +
								   param_text + "</em>"+
								   "<strong class='aceide_func_highlight'>)</strong> " +
								   returns_text +
									"<p><a href='http://codex.wordpress.org/Function_Reference/" + key  + "' target='_blank'>See " + key  + "() in the WordPress codex</a></p>"
								   );
			  }

			}

			//php autocomplete
			if ( selected_command_item.find("img").attr("src").indexOf("phpac.png") >= 0){

			  if (autocomplete_php[key].returns != undefined){

				//params text
				var param_text ="";
				for(i=0; i<autocomplete_php[key].params.length; i++) {

					//wrap params in a span to highlight not required
					if (autocomplete_php[key].params[i].required == "no"){
						param_text = param_text + "<span class='aceide_func_arg_notrequired'>" + autocomplete_php[key].params[i]['param'] + "<em>optional</em></span><br /> <br />";
					}else{
						param_text = param_text + autocomplete_php[key].params[i]['param'] + "<br /> <br />";
					}

				}
				//compose returns text
				if (autocomplete_php[key].returns.length > 0){
					returns_text = "<br /><br /><strong>Returns:</strong> " + autocomplete_php[key].returns;
				}else{
					returns_text = "";
				}

				jQuery("#aceide_info_content").html(
								"<strong class='aceide_func_highlight_black'>Function: </strong><strong class='aceide_func_highlight'>" + key + "(</strong><br />" +
								   autocomplete_php[key].desc + "<br /><br /><em class='aceide_func_params'>" +
								   param_text + "</em>" +
								   "<strong class='aceide_func_highlight'>)</strong>" +
								   returns_text +
									"<p><a href='http://php.net/manual/en/function." + key.replace(/_/g, "-")  + ".php' target='_blank'>See " + key  + "() in the PHP manual</a></p>"
								   );

			  }

			}



	}
	  catch(err)
	{
	//Handle errors here
	}

}

//open another file and add to editor
function aceide_set_file_contents(file, callback_func){
	"use strict";

	//ajax call to get file contents we are about to edit
	var data = { action: 'aceide_get_file', filename: file, _wpnonce: jQuery('#_wpnonce').val(), _wp_http_referer: jQuery('#_wp_http_referer').val() };

	jQuery.post(aceajax.url, data, function(response) {
		var the_path = file.replace(/^.*[\\\/]/, '').trim();
		var the_id = "aceide_tab_" + last_added_editor_session;

		//enable editor now we have a file open
		jQuery('#fancyeditordiv textarea').removeAttr("disabled");
		editor.setReadOnly(false);

		jQuery("#aceide_toolbar_tabs").append('<span id="'+the_id+'" sessionrel="'+last_added_editor_session+'"  title="  '+file+' " rel="'+file+'" class="aceide_tab">'+ the_path +'<a class="close_tab" href="#">x</a></span>');

		saved_editor_sessions[last_added_editor_session] = new EditSession(response);//set saved session
		saved_editor_sessions[last_added_editor_session].on('change', onSessionChange);
		saved_undo_manager[last_added_editor_session] = new UndoManager(editor.getSession().getUndoManager());//new undo manager for this session

		saved_editor_sessions[last_added_editor_session].on("changeScrollTop", onSessionScroll);

		last_added_editor_session++; //increment session counter

		//add click event for the new tab.
		//We are actually clearing the click event and adding it again for all tab elements, it's the only way I could get the click handler listening on all dynamically added tabs
		jQuery(".aceide_tab").off('click').on("click", function(event){
			event.preventDefault();

			jQuery('input[name=filename]').val( jQuery(this).attr('rel') );

			//save current editor into session
			//get old editor out of session and apply to editor
			var clicksesh = jQuery(this).attr('sessionrel'); //editor id number
			saved_editor_sessions[ clicksesh ].setUndoManager(saved_undo_manager[ clicksesh ]);
			editor.setSession( saved_editor_sessions[ clicksesh ] );

			//set this tab as active
			jQuery(".aceide_tab").removeClass('active');
			jQuery(this).addClass('active');

			var currentFilename = jQuery(this).attr('rel');
			var mode;

			//turn autocomplete off initially, then enable as needed
			editor.getSession().enable_autocomplete = false;

			//set the editor mode based on file name
			if (/\.css$/.test(currentFilename)) {
				mode = require("ace/mode/css").Mode;
			}
			else if (/\.scss$/.test(currentFilename)) {
				mode = require("ace/mode/scss").Mode;
			}
			else if (/\.less$/.test(currentFilename)) {
				mode = require("ace/mode/less").Mode;
			}
			else if (/\.js$/.test(currentFilename)) {
				mode = require("ace/mode/javascript").Mode;
			}
			else if (/\.twig$/.test(currentFilename)) {
			    mode = require("ace/mode/twig").Mode;
			}
			else {
				mode = require("ace/mode/php").Mode; //default to PHP

				//only enable session change / auto complete for PHP
				if (/\.php$/.test(currentFilename))
					editor.getSession().enable_autocomplete = true;
			}
			editor.getSession().setMode(new mode());

			editor.getSession().on('change', onSessionChange);

			editor.getSession().selection.on('changeSelection', selectionChanged);

			editor.resize();
			editor.focus();
			//make a note of current editor
			current_editor_session = clicksesh;

			//hide/show the restore button if it's a php file and the restore url is set (i.e saved in this session)
			if ( /\.php$/i.test( currentFilename ) && jQuery(".aceide_tab.active", "#aceide_toolbar").data( "backup" ) != undefined ){
				jQuery("#aceide_toolbar_buttons .button.restore").show();
			}else{
				jQuery("#aceide_toolbar_buttons .button.restore").hide();
			}

			//show hide unsaved content message
			if (  jQuery(".aceide_tab.active", "#aceide_toolbar").data( "unsaved" ) ){
				jQuery("#aceide_footer_message_unsaved").html("[ Document contains unsaved content &#9998; ]").show();
			}else{
				jQuery("#aceide_footer_message_unsaved").hide();
			}

			//show last saved message if it's been saved
			if ( jQuery(".aceide_tab.active", "#aceide_toolbar").data( "lastsave" ) != undefined){
				jQuery("#aceide_footer_message_last_saved").html("<strong>Last saved: </strong>" + jQuery(".aceide_tab.active", "#aceide_toolbar").data( "lastsave" ) ).show();
			}else{
				jQuery("#aceide_footer_message_last_saved").hide();
			}

			//hide the message if we have a fresh tab
			jQuery("#aceide_message").hide();
		});

		//add click event for tab close.
		//We are actually clearing the click event and adding it again for all tab elements, it's the only way I could get the click handler listening on all dynamically added tabs
		jQuery(".close_tab").off('click').on("click", function(event){
		event.preventDefault();
		var clicksesh = jQuery(this).parent().attr('sessionrel');
		var activeFallback;

			if (jQuery("#aceide_footer_message_unsaved").is(":visible")) {
				if (!confirm('Are you sure you wish to close the unsaved document?'))
					return;
			}

			//if the currently selected tab is being removed then remember to make the first tab active
			if ( jQuery("#aceide_tab_"+clicksesh).hasClass('active') ) {
				activeFallback = true;
			}else{
				activeFallback = false;
			}

			//remove tab
			jQuery(this).parent().remove();

			//clear session and undo
		   saved_undo_manager[clicksesh] = undefined;
		   saved_editor_sessions[clicksesh] = undefined;

		   //Clear the active editor if all tabs closed or activate first tab if required since the active tab may have been deleted
		   if (jQuery(".aceide_tab").length == 0){
			   editor.getSession().setValue( "" );
		   }else if ( activeFallback ){
			   jQuery( "#" + jQuery(".aceide_tab")[0].id ).click();
		   }

		});

			jQuery("#"+the_id).click();

	if (callback_func != null) {
		callback_func(response);
	}

	});


}

function saveDocument() {
	// Make sure there is actually a document open
	jQuery("#aceide_message").stop(true,true);
	if (jQuery("#aceide_message").is(":visible")) {
		jQuery("#aceide_message").fadeOut(50);
	}

	var the_tab = jQuery("#aceide_toolbar_tabs .active");
	var current_document_index = the_tab.attr('id').replace('aceide_tab_', '');
	the_tab.removeClass( 'modified' );

	// Display notification to show we are attempting to save
	jQuery("#aceide_message")
		.removeClass('error')
		.removeClass('teapot')
		.addClass('success')
		.html('Saving...')
		.fadeIn(200);

	//ajax call to save the file and generate a backup if needed
	var data = { action: 'aceide_save_file', filename: jQuery('input[name=filename]').val(),  _wpnonce: jQuery('#_wpnonce').val(), _wp_http_referer: jQuery('#_wp_http_referer').val(), content: editor.getSession().getValue() };
	jQuery.post(aceajax.url, data, function(response) {
		var regexchk=/\".*:::.*\"/;
		var saved_when = Date();

		if ( regexchk.test(response) ){
			//store the resulting backup file name just incase we need to restore later
			//temp note: you can then access the data like so  jQuery(".aceide_tab.active", "#aceide_toolbar").data( "backup" );
			user_nonce_addition = response.match(/:::(.*)\"$/)[1]; //need this to send with restore request
			jQuery(".aceide_tab.active", "#aceide_toolbar").data( "backup", response.replace(/(^\"|:::.*\"$)/g, "") );
			jQuery(".aceide_tab.active", "#aceide_toolbar").data( "lastsave",  saved_when );
			jQuery(".aceide_tab.active", "#aceide_toolbar").data( "unsaved", false);

			if ( /\.php$/i.test( data.filename ) )
				jQuery("#aceide_toolbar_buttons .button.restore").show();

			jQuery("#aceide_footer_message_last_saved").html("<strong>Last saved: </strong>" + saved_when).show();
			jQuery("#aceide_footer_message_unsaved").hide();

			jQuery("#aceide_message").html('<strong>File saved &#10004;</strong>')
			.show()
			.delay(2000)
			.fadeOut(600);
		}else{
			alert("error: " + response);
		}
	});
}

//enter/return command
function selectACitem (item) {
	if( document.getElementById('ac').style.display === 'block' && oHandler.visible() == 'block' ){
		var ac_dropdwn = document.getElementById('ac');
		var tag = ac_dropdwn.options[ac_dropdwn.selectedIndex].value;
		var sel = editor.selection.getRange();
		var line = editor.getSession().getLine(sel.start.row);
		sel.start.column = sel.start.column - autocompletelength;

		if (item.length){
			tag = item; //get tag from new msdropdown passed as arg
		}else{
			tag = jQuery("#ac_msdd a.selected").children("span.ddTitleText").text(); //get tag from new msdropdown
		}

		//clean up the tag/command
		tag = tag.replace(")", ""); //remove end parenthesis

		//console.log(tag);
		editor.selection.setSelectionRange(sel);
		editor.insert(tag);

		aceide_close_autocomplete();
	} else {
		editor.insert('\n');
	}
}

function update_scroll_bars_for_resizer() {
	if ( jQuery( ".ace_scroller" ).css( 'overflowX' ) != 'scroll' ) {
		var height = jQuery( ".ace_sb" ).height();
		jQuery( ".ace_sb" ).height( height-16 );
	}
}

// Initiate editor settings
function load_editor_settings() {
	var theme        = localStorage.custom_editor_theme;
	var fontSize     = localStorage.custom_editor_fontSize;
	var folding      = localStorage.custom_editor_folding;
	var fade_fold    = localStorage.custom_editor_fade_folding;
	var wrap         = localStorage.custom_editor_wrap;
	var wrap_limit   = localStorage.custom_editor_wrap_limit;
	var select_style = localStorage.custom_editor_full_line_select;
	var highlight    = localStorage.custom_editor_highlight_current_line;
	var invisibles   = localStorage.custom_editor_invisibles;
	var indents      = localStorage.custom_editor_indent_guides;
	var anim_scroll  = localStorage.custom_editor_animate_scrolling;
	var show_gutter  = localStorage.custom_editor_show_gutter;
	var use_tabs     = localStorage.custom_editor_use_tabs;
	var word_hglt    = localStorage.custom_editor_highlight_selected_word;
	var behaviours   = localStorage.custom_editor_behaviours;

	// Defaults
	if ( theme === undefined )          theme       = "ace/theme/textmate";
	if ( fontSize === undefined )       fontSize    = "12";
	if ( fade_fold === undefined )      fade_fold   = "0";
	if ( wrap === undefined )           wrap        = "1";
	if ( wrap_limit === undefined )     wrap_limit  = "0";
	if ( highlight === undefined )      highlight   = "1";
	if ( invisibles === undefined )     invisibles  = "0";
	if ( indents === undefined )        indents     = "1";
	if ( anim_scroll === undefined )    anim_scroll = "1";
	if ( show_gutter === undefined )    show_gutter = "1";
	if ( use_tabs === undefined )       use_tabs    = "1";
	if ( word_hglt === undefined )      word_hglt   = "1";
	if ( behaviours === undefined )     behaviours  = "0";

	// Check invalid fold styling
	if ( !editor.session.$foldStyles[folding] )        folding = "markbegin";
	if ( select_style != "text" && select_style != "line" ) select_style = "line";

	// Set
	editor.setTheme( theme );
	editor.setFontSize( fontSize + 'px' );
	editor.session.setFoldStyle( folding );
	editor.setSelectionStyle( select_style );

	editor.$blockScrolling = Infinity;
	editor.setOption("showPrintMargin", false);

	// Boolean values
	editor.session.setUseSoftTabs( use_tabs == false );         // soft tab is space
	editor.setHighlightActiveLine( highlight == true );
	editor.renderer.setShowInvisibles( invisibles == true );

    try {
    	editor.renderer.setDisplayIndentGuides( indents == true );
    } catch(error) {
        window.console && console.error( 'setDisplayIndentGuides not supported' );
    }

	editor.renderer.setAnimatedScroll( anim_scroll == true );
	editor.renderer.setShowGutter( show_gutter == true );
	editor.setHighlightSelectedWord( word_hglt == true );
	editor.setBehavioursEnabled( behaviours == true );
	editor.setFadeFoldWidgets( fade_fold == true );

	// To also allow for free range - free by default
	if ( wrap == true ) {
		if ( wrap_limit != "0" ) {
			// Normal wrap
			editor.session.setWrapLimitRange(wrap_limit,wrap_limit);
		} else {
			// 0 represents free scrolling
			editor.session.setUseWrapMode(true);
			editor.session.setWrapLimitRange(null, null);
			editor.renderer.setPrintMarginColumn(80);
		}
	} else {
		editor.session.setUseWrapMode(false);
		editor.renderer.setPrintMarginColumn(80);
	}
}

function display_editor_settings() {
	// Create HTML dialog
	if (!jQuery(".ui-dialog #editor_settings_dialog").length) {
		// Ensure settings are loaded...
		load_editor_settings();

		// Add Listeners
		jQuery("#editor_theme_setting").blur(function() {
			localStorage.custom_editor_theme = jQuery(this).val();
		});
		jQuery("#editor_folding_setting").change(function() {
			localStorage.custom_editor_folding = jQuery(this).val();
		});
		jQuery("#editor_font_size_setting").change(function() {
			localStorage.custom_editor_fontSize = jQuery( this ).val();
		});
		jQuery("#editor_fade_fold_setting").change(function() {
			localStorage.custom_editor_fade_folding = (jQuery(this).is(":checked") ? 1 : 0);
		});
		jQuery("#editor_wrap_setting").change(function() {
			localStorage.custom_editor_wrap = (jQuery(this).is(":checked") ? 1 : 0);
		});
		jQuery("#editor_wrap_limit_setting").change(function() {
			localStorage.custom_editor_wrap_limit = jQuery(this).val();
		});
		jQuery("#editor_highlight_line_setting").change(function() {
			localStorage.custom_editor_highlight_current_line = (jQuery(this).is(":checked") ? 1 : 0);
		});
		jQuery("#editor_show_invisibles").change(function() {
			localStorage.custom_editor_invisibles = (jQuery(this).is(":checked") ? 1 : 0);
		});
		jQuery("#editor_display_indent_guides_setting").change(function() {
			localStorage.custom_editor_indent_guides = (jQuery(this).is(":checked") ? 1 : 0);
		});
		jQuery("#editor_animate_scroll_setting").change(function() {
			localStorage.custom_editor_animate_scrolling = (jQuery(this).is(":checked") ? 1 : 0);
		});
		jQuery("#editor_show_gutter_setting").change(function() {
			localStorage.custom_editor_show_gutter = (jQuery(this).is(":checked") ? 1 : 0);
		});
		jQuery("#editor_use_tabs_setting").change(function() {
			localStorage.custom_editor_use_tabs = (jQuery(this).is(":checked") ? 1 : 0);
		});
		jQuery("#editor_word_highlight").change(function() {
			localStorage.custom_editor_highlight_selected_word = (jQuery(this).is(":checked") ? 1 : 0);
		});
		jQuery("#editor_behaviours_setting").change(function() {
			localStorage.custom_editor_behaviours = (jQuery(this).is(":checked") ? 1 : 0);
		});
	}

	// Update values
	jQuery('#editor_theme_setting').val(editor.getTheme());
	jQuery('#editor_font_size_setting').val(editor.container.style.fontSize.replace('px','') || 12);
	jQuery('#editor_fade_fold_setting').prop('checked', editor.getFadeFoldWidgets());
	jQuery('#editor_wrap_setting').prop('checked', editor.session.getUseWrapMode());
	jQuery('#editor_wrap_limit_setting').prop('checked', editor.session.getWrapLimit() || 0);
	jQuery('#editor_highlight_line_setting').prop('checked', editor.getHighlightActiveLine());
	jQuery('#editor_show_invisibles').prop('checked', editor.renderer.getShowInvisibles());
	jQuery('#editor_display_indent_guides_setting').prop('checked', editor.renderer.getDisplayIndentGuides());
	jQuery('#editor_animate_scroll_setting').prop('checked', editor.getAnimatedScroll());
	jQuery('#editor_show_gutter_setting').prop('checked', editor.renderer.getShowGutter());
	jQuery('#editor_use_tabs_setting').prop('checked', !editor.session.getUseSoftTabs());
	jQuery('#editor_word_highlight').prop('checked', editor.getHighlightSelectedWord());
	jQuery('#editor_behaviours_setting').prop('checked', editor.getBehavioursEnabled());
	jQuery('#editor_folding_setting option').prop('selected', false);
	jQuery('#editor_folding_setting option[value="' + editor.session.$foldStyle + '"]').prop('selected', true);

	// Display Dialog
	jQuery("#editor_settings_dialog").dialog({
		width: "550",
		modal: true,
		resizable: false,
		show: "fade",
		close: load_editor_settings,
		appendTo: jQuery("#aceide_container")
	}).dialog("moveToTop");
}

function display_find_dialog() {
	// Initiate the search box with the current selection
	if ( !editor.session.selection.$isEmpty )
		var value = editor.session.doc.getTextRange( editor.session.selection.getRange() );
	else
		var value = '';

	jQuery( "#editor_find_dialog" ).find( "input[name='find']" ).val( value );

	jQuery( "#editor_find_dialog" ).dialog({
		height: "206",
		width: "408",
		resizable: false,
		show: "fade",
		hide: "fade",
		appendTo: jQuery("#aceide_container")
	}).dialog("moveToTop");

}

function display_goto_dialog() {
	jQuery( "#editor_goto_dialog" ).find( "input[name='line']").val( editor.session.selection.getCursor().row+1 );
	jQuery( "#editor_goto_dialog" ).dialog({
		height: "100",
		width: "300",
		resizable: false,
		show: "fade",
		hide: "fade",
		appendTo: jQuery("#aceide_container")
	});
}

function filetree_drag_initializer() {
	var coverup = document.getElementById("drag_coverup"),
	    dragged_element = null;
	// Allows us to keep dataTransfer in the jQuery event
	jQuery.event.props.push("dataTransfer");
	jQuery('#aceide_file_browser').on('dragstart', '[draggable=true]', function(e) {
		e.stopPropagation();

		dragged_element = this;

		var drag_html = this.cloneNode();
		drag_html.appendChild(this.getElementsByTagName('a')[0].cloneNode(true));
		coverup.appendChild(drag_html);

		e.dataTransfer.effectAllowed = 'move';
		e.dataTransfer.setData("text", this.childNodes[0].getAttribute("rel"));
		e.dataTransfer.setDragImage(coverup, 0, 0);
	}).on('dragover', 'li.directory > a', function(e) {
		// Stop child nodes displaying the ability to receive this item
		if (jQuery(dragged_element).find(this).size())
			return;

		e.preventDefault();
		e.dataTransfer.dropEffect = "move";

		jQuery(this).parent().addClass("allowDrop");

		return false;
	}).on('dragleave', 'li.directory > a', function(e) {
		jQuery(this).parent().removeClass("allowDrop");
	}).on('dragend', function() {
		// Just to make sure it's always empty
		jQuery(coverup).empty();

		// And our styling resets
		jQuery("#aceide_file_browser .allowDrop").removeClass("allowDrop");
	}).on('drop', 'li.directory', function(e) {
		e.stopPropagation();
		e.preventDefault();

		if (jQuery(dragged_element).find(this).size())
			return;

		var source      = jQuery(dragged_element).children("a").attr("rel");
		var destination = jQuery(this).children("a").attr("rel");

		var data = { action: 'aceide_move_file', source: source, destination: destination, _wpnonce: jQuery('#_wpnonce').val(), _wp_http_referer: jQuery('#_wp_http_referer').val() };

		jQuery.post(aceajax.url, data, function(response) {
			if (response == "1") {
				if (jQuery("ul.jqueryFileTree a[rel='"+ source +"']").parents('ul').size() < 2) {
					// We are moving something to the root folder so regenerate the whole filetree
					the_filetree();
					return;
				}

				// click the source once to hide
				jQuery("ul.jqueryFileTree a[rel='"+ source +"']").closest('ul').parent().click();

				// click the destination once to hide
				jQuery("ul.jqueryFileTree a[rel='" + destination + "']").parent().children("a").click();

				//click the parent once again to show with new folder and focus on this area
				jQuery("ul.jqueryFileTree a[rel='"+ source +"']").parent().children("a").click();
				jQuery("ul.jqueryFileTree a[rel='" + destination + "']").parent().children("a").click();
			} else if (response == "-1") {
				alert("Permission/security problem. Refresh AceIDE and try again.");
			} else {
				alert("Error: " + response);
			}
		});

		dragged_element = null;

		return false;
	});
}

jQuery(document).ready(function($) {
	$("#aceide_save").click(saveDocument);

	// Find dialog actions
	$("#editor_find_dialog form" ).submit(function( e ) {
		e.preventDefault();
		var options = {};

		var direction   = jQuery( "#editor_find_dialog input[name='direction']" ).prop("checked");
		var start;

		if ( direction ) {
			start = editor.getSelectionRange().start;
		} else {
			start = editor.getSelectionRange().end;
		}

		options.needle          = jQuery( "#editor_find_dialog input[name='find']" ).val();
		options.backwards       = direction;
		options.wrap            = jQuery( "#editor_find_dialog input[name='wrap']" ).prop("checked");
		options.caseSensitive   = jQuery( "#editor_find_dialog input[name='case']" ).prop("checked");
		options.wholeWord       = jQuery( "#editor_find_dialog input[name='whole']" ).prop("checked");
		options.range           = null;
		options.regExp          = jQuery( "#editor_find_dialog input[name='regexp']" ).prop("checked");
		options.start           = start;
		options.skipCurrent     = false;

		editor.find(options.needle, options);
		return false;
	});
	$("#editor_find_dialog input[name='replace']").click(function( e ) {
		e.preventDefault();
		var options = {};

		var direction   = jQuery( "#editor_find_dialog input[name='direction']" ).prop("checked");
		var replacement = jQuery( "#editor_find_dialog input[name='replacement']" ).val();
		var start       = editor.getSelectionRange().start;


		options.needle          = jQuery( "#editor_find_dialog input[name='find']" ).val();
		options.backwards       = direction;
		options.wrap            = jQuery( "#editor_find_dialog input[name='wrap']" ).prop("checked");
		options.caseSensitive   = jQuery( "#editor_find_dialog input[name='case']" ).prop("checked");
		options.wholeWord       = jQuery( "#editor_find_dialog input[name='whole']" ).prop("checked");
		options.range           = null;
		options.regExp          = jQuery( "#editor_find_dialog input[name='regexp']" ).prop("checked");
		options.start           = start;
		options.skipCurrent     = false;

		editor.replace(replacement, options);
		editor.find(options.needle, options);
	});
	$("#editor_find_dialog input[name='replace_all']").click(function( e ) {
		e.preventDefault();
		var options = {};

		var direction   = jQuery( "#editor_find_dialog input[name='direction']" ).prop("checked");
		var replacement = jQuery( "#editor_find_dialog input[name='replacement']" ).val();
		var start       = editor.getSelectionRange().start;

		options.needle          = jQuery( "#editor_find_dialog input[name='find']" ).val();
		options.backwards       = direction;
		options.wrap            = jQuery( "#editor_find_dialog input[name='wrap']" ).prop("checked");
		options.caseSensitive   = jQuery( "#editor_find_dialog input[name='case']" ).prop("checked");
		options.wholeWord       = jQuery( "#editor_find_dialog input[name='whole']" ).prop("checked");
		options.range           = null;
		options.regExp          = jQuery( "#editor_find_dialog input[name='regexp']" ).prop("checked");
		options.start           = start;
		options.skipCurrent     = false;

		editor.replaceAll(replacement, options);
	});
	$("#editor_find_dialog input[name='cancel']").click(function( e ) {
		e.preventDefault();
		jQuery( "#editor_find_dialog" ).dialog( "close" );
		editor.focus();
	});

	// drag and drop colour picker image
	$("#aceide_color_assist").on('drop', function(e) {
		e.preventDefault();
		e.originalEvent.dataTransfer.items[0].getAsString(function(url){
				$(".ImageColorPickerCanvas", $("#side-info-column") ).remove();
				$("img", $("#aceide_color_assist")).attr('src', url );

		});
	});

	$("#aceide_color_assist").on('dragover', function(e) {
		$(this).addClass("hover");
	}).on('dragleave', function(e) {
		$(this).removeClass("hover");
	});


	//add div for ace editor to latch on to
	$('#template').prepend("<div style='width:80%;height:500px;margin-right:0!important;' id='fancyeditordiv'></div>");
	//create the editor instance
	editor = ace.edit("fancyeditordiv");
	//turn off print margin
	editor.setPrintMarginColumn(false);
	//set the editor theme
	editor.setTheme("ace/theme/dawn");
	//must always use scrollbar
	editor.renderer.setVScrollBarAlwaysVisible(true);
	editor.renderer.setHScrollBarAlwaysVisible(true);
	//get a copy of the initial file contents (the file being edited)
	//var intialData = $('#newcontent').val()
	var intialData = "Use the file manager to find a file you wish edit, click the file name to edit. \n\n";


	//startup info - usefull for debugging
		var data = { action: 'aceide_startup_check', _wpnonce: jQuery('#_wpnonce').val(), _wp_http_referer: jQuery('#_wp_http_referer').val() };

		jQuery.post(aceajax.url, data, function(response) {
			if (response == "-1"){
				intialData = intialData + "Permission/security problem with ajax request. Refresh AceIDE and try again. \n\n";
			} else {
			    intialData = intialData + response;
			}

			editor.getSession().setValue( intialData );

		});



	//make initial editor read only
	// $('#fancyeditordiv textarea').attr("disabled", "disabled");
	editor.setReadOnly(true);

	//use editors php mode
	var phpMode = require("ace/mode/php").Mode;
	editor.getSession().setMode(new phpMode());

	//START AUTOCOMPLETE
	//create the autocomplete dropdown
	var ac = document.createElement('select');
	ac.id = 'ac';
	ac.name = 'ac';
	ac.style.position='absolute';
	ac.style.zIndex=100;
	ac.style.width='auto';
	ac.style.display='none';
	ac.style.height='auto';
	ac.size=10;
	editor.container.appendChild(ac);

	//hook onto any change in editor contents
	editor.getSession().on('change', onSessionChange);//end editor change event



	//START COMMANDS

	//Key up command
	editor.commands.addCommand({
		name: "up",
		bindKey: "Up",
		exec: function(env, args, request) {
			if (oHandler && oHandler.visible() === 'block'){
				oHandler.previous();

				//show command help panel for this command
				aceide_function_help();
				//console.log("handler is visible");

			}else if( document.getElementById('ac').style.display === 'block'  ) {
				var select=document.getElementById('ac');
				if( select.selectedIndex === 0 ) {
					select.selectedIndex = select.options.length-1;
				} else {
					select.selectedIndex = select.selectedIndex-1;
				}
				 //console.log("ac is visible");
			} else {
				var range = editor.getSelectionRange();
				editor.clearSelection();

/*				// If we have folds, let's skip past them
				var fold    = editor.getSession().getFoldAt( range.start.row );
				if ( fold !== null ) {
					range.end.row = fold.start.row;
				}


				// Do not go up on a tabbed space
				if ( editor.session.getUseSoftTabs() ) {
					var tabsize     = editor.session.getTabSize();
					range.end.column = Math.round( range.end.column / tabsize ) * tabsize;
				}
*/
				editor.moveCursorTo(range.end.row-1, range.end.column);
			}
		},
		scrollIntoView: "cursor"
	});


	//key down command
	editor.commands.addCommand({
		name: "down",
		bindKey: "Down",
		exec: function(env, args, request) {

			if (oHandler && oHandler.visible() === 'block'){
				oHandler.next();

				//show command help panel for this command
				aceide_function_help();

			}else if ( document.getElementById('ac').style.display === 'block' ) {
				var select=document.getElementById('ac');
				if ( select.selectedIndex === select.options.length-1 ) {
					select.selectedIndex=0;
				} else {
					select.selectedIndex=select.selectedIndex+1;
				}
			} else {
				var range = editor.getSelectionRange();
				editor.clearSelection();
				editor.moveCursorTo(range.end.row +1, range.end.column);
			}
		},
		scrollIntoView: "cursor"
	});


	editor.commands.addCommand({
		name: "enter",
		bindKey: "Return",
		exec: selectACitem,
		scrollIntoView: "cursor"
	});

	// save command:
	editor.commands.addCommand({
		name: "save",
		bindKey: {
			win: "Ctrl-S",
			mac: "Command-S",
			sender: "editor"
		},
		exec: saveDocument
	});

	// duplicate line:
	editor.commands.addCommand({
		name: "duplicateLines",
		bindKey: {
			win: "Ctrl-D",
			mac: "Command-D",
			sender: "editor"
		},
		exec: function() {
			var rows = editor.$getSelectedRows();
			editor.session.duplicateLines( rows.first, rows.last );
		},
		scrollIntoView: "cursor"
	});

	// delete line:
	editor.commands.addCommand({
		name: "removeLines",
		bindKey: {
			win: "Ctrl-Shift-D",
			mac: "Command-Shift-D",
			sender: "editor"
		},
		exec: function() {
			editor.removeLines();
			editor.selection.moveCursorUp();
		},
		scrollIntoView: "cursor"
	});

	// Move lines up
	editor.commands.addCommand({
		name: "shiftLinesUp",
		bindKey: {
			win: "Ctrl-Shift-Up",
			mac: "Command-Shift-Up",
			sender: "editor"
		},
		exec: function() {
			editor.moveLinesUp();
		},
		scrollIntoView: "cursor"
	});

	// Move lines down
	editor.commands.addCommand({
		name: "shiftLinesDown",
		bindKey: {
			win: "Ctrl-Shift-Down",
			mac: "Command-Shift-Down",
			sender: "editor"
		},
		exec: function() {
			// Move rows down
			editor.moveLinesDown();
		},
		scrollIntoView: "cursor"
	});

	// Show find dialog
	editor.commands.addCommand({
		name: "findDialog",
		bindKey: {
			win: "Ctrl-F",
			mac: "Command-F",
			sender: "editor"
		},
		exec: function() {
			display_find_dialog();
		}
	});

	// Show goto dialog
	editor.commands.addCommand({
		name: "gotoDialog",
		bindKey: {
			win: "Ctrl-G",
			mac: "Command-G",
			sender: "editor"
		},
		exec: function() {
			display_goto_dialog();
		}
	});

    editor.commands.addCommand({
        name: "escape",
        bindKey: "esc",
        exec: function(editor) {
            var element = editor.container.parentNode;

            if (document.getElementById('ac').style.display === 'block') {
                aceide_close_autocomplete();
                return;
            }

            if (document.fullscreenElement !== null) {
                element.className = element.className.replace(/\s?fullScreen/, '');
                exitFullscreen();
                editor.resize();
                return;
            }
        }
    });

    editor.commands.addCommand({
        name: "toggleFullscreen",
        bindKey: "F11",
        exec: function(editor) {
            var element = editor.container.parentNode;

            if (!requestFullscreen(element)) {
                alert("Could not open full screen.");
            }

            element.className += ' fullScreen';
            editor.resize();
        }
    });
	//END COMMANDS

    fullscreenOnChange(function() {
        if (document.fullscreenElement === null) {
            var element = document.getElementById("aceide_container");
            element.className = element.className.replace(/\s?fullScreen/, '');
            exitFullscreen();
            editor.resize();
        }
    });

    window.addEventListener('keydown', function(e) {
        // Disable default fullscreen
        if (e.keyCode === 122) {
            e.preventDefault();
        }
    });

	//click action for new directory/file submit link
	$("#aceide_create_new_directory, #aceide_create_new_file").click(function(e){
		e.preventDefault();

		var data_input = jQuery(this).parent().find("input.has_data");
		var item = eval('('+ data_input.attr("rel") +')');

		//item.path file|directory
		var data = { action: 'aceide_create_new', path: item.path, type: item.type, file: data_input.val(), _wpnonce: jQuery('#_wpnonce').val(), _wp_http_referer: jQuery('#_wp_http_referer').val() };

		jQuery.post(aceajax.url, data, function(response) {

			if (response == "1"){
				//remove the file/dir name from the text input
				data_input.val("");

				if ( jQuery("ul.jqueryFileTree a[rel='"+ item.path +"']").length == 0){

					//if no parent then we are adding something to the wp-content folder so regenerate the whole filetree
					the_filetree();

				}

				//click the parent once to hide
				jQuery("ul.jqueryFileTree a[rel='"+ item.path +"']").click();

				//hide the parent input block
				data_input.parent().hide();

				//click the parent once again to show with new folder and focus on this area
				jQuery("ul.jqueryFileTree a[rel='"+ item.path +"']").click();
				jQuery("ul.jqueryFileTree a[rel='"+ item.path +"']").focus();

			}else if (response == "-1"){
				alert("Permission/security problem. Refresh AceIDE and try again.");
			}else{
				alert(response);
			}


		});

	});


	// Add our resizer to the
	$("#fancyeditordiv").prepend('<span id="resizer"></span>');

	// Create resizer handle
	$("#fancyeditordiv span#resizer").bind('mousedown', function(e) {
		e = e || window.event;

		var offset = e.offsetY;

		if ( offset === undefined ) {
			offset = 0;
		}

		window.editor_options.resizer.handler_offset = (15-offset) - 30;

		function movement_handler(e) {
			var line_height = editor.renderer.lineHeight;
			var curr_height = $("#fancyeditordiv").height();

			var o           = jQuery('#aceide_toolbar_buttons').offset();

			// Calculate new height.
			var new_height	 = e.clientY;                                        // Get mouse Y position in window.
			new_height		-= o.top;                                            // Add offset from top of window.
			new_height		+= window.editor_options.resizer.handler_offset;     // Add offset from resizer handle.
			new_height		+= jQuery(window).scrollTop();                     // Add window scroll offset.
			new_height		 = Math.round(new_height / line_height) * line_height;     // Round to nearest line height

			// Do not allow if less than 230px
			if (new_height > 230 || new_height > curr_height) {
				$("#fancyeditordiv").height(++new_height);
				editor.resize();
			}
		}

		function cancel_drag() {
			$(window)
				.unbind('mousemove', movement_handler)
				.unbind('mouseup', cancel_drag);
		}

		// Detect movements on mouse
		$(window).bind('mousemove', movement_handler);
		$(window).bind('mouseup', cancel_drag);

	});


	$("#submitdiv h3")
		.append('<a href="#" class="aceide-settings">')
		.find('a')
		.bind('click', display_editor_settings);

	$("#aceide_file_browser").on("contextmenu", ".directory > a, .file > a", display_context_menu);

	// Figure out the correct size for the editor
	(function() {
		var size    = Math.max( ( $(document.body).height() - 230 ), 250 ),
			lh      = editor.renderer.lineHeight;

		size += lh - (size % lh);
		size++;                     // Editor seems to always be 1px small

		$("#fancyeditordiv").height(size + 'px');
	})();

	editor.on('changeSession', function(e) {
		update_scroll_bars_for_resizer();
	});

	load_editor_settings();
	editor.resize();

	filetree_drag_initializer();
});//end jquery load
