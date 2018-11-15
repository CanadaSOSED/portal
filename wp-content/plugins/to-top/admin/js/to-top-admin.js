(function( $ ) {
	'use strict';

	/**
	 * Custom jQuery functions and trigger events
	 */
	jQuery(document).ready(function($){
		$("#setting-error-settings_updated").hide();

		// Show Hide Toggle Box
		$(".option-content").hide();

		$(".open").show();

		$("h3.option-toggle").click(function(e){
			e.preventDefault();
			if( !$(this).hasClass('option-active') ){
				$(this).siblings('.option-content').stop(true, true).hide(400);
				$(this).siblings('.option-toggle').removeClass('option-active');
				$(this).toggleClass("option-active").next().stop(true, true).slideToggle(400);
				return false;
			}
		});

	    setTimeout(function () {
	        $(".fade").fadeOut("slow", function () {
	            $(".fade").remove();
	        });

	    }, 2000);

	    var custom_uploader;

	    $( '.to_top_upload_image' ).click(function(e) {
	        e.preventDefault();

	        var title, this_selector, button_text, attachment;

	        title 			= $(this).val();

	        this_selector 	= $(this); //For later use

	        button_text 	= $(this).attr("ref");

	        //Extend the wp.media object
	        custom_uploader = wp.media.frames.file_frame = wp.media({
	            title: title,
	            button: {
	                text: button_text
	            },
	            multiple: true
	        });

	        //When a file is selected, grab the URL and set it as the text field's value
	        custom_uploader.on( 'select', function() {
	            attachment = custom_uploader.state().get( 'selection' ).first().toJSON();
	        	this_selector.prev().val( attachment.url );
	        });

	        //Open the uploader dialog
	        custom_uploader.open();
	    });

	    //For Color picker in icon color
		var myOptions = {
		    change: function(event, ui){
		    	$(".dashicon_to_top_admin").css({
					'color' 	:	ui.color.toString(),
				});
		    },
		};

		$('.to_top_icon_color').wpColorPicker( myOptions );

		//For Color picker in icon background color
		var myOptions2 = {
		    change: function(event, ui){
		    	$(".dashicon_to_top_admin").css({
					'background-color' 	:	ui.color.toString(),
				});
		    },
		};

		$('.to_top_icon_bg_color').wpColorPicker( myOptions2 );

		$('#to_top_border_radius').change(function(){
			$(".dashicon_to_top_admin").css({
						'-webkit-border-radius'	: $('#to_top_border_radius').val() + '%',
					    '-moz-border-radius'	: $('#to_top_border_radius').val() + '%',
					    'border-radius'			: $('#to_top_border_radius').val() + '%',
					});
		});

		$('#to_top_icon_size').change(function(){
			$(".dashicon_to_top_admin").css({
						'font-size'	: $('#to_top_icon_size').val() + 'px',
					    'height'	: $('#to_top_icon_size').val() + 'px',
					    'width'		: $('#to_top_icon_size').val() + 'px',
					});
		});

		$(".dashicon_to_top_admin").css({
			'-webkit-border-radius'	: $('#to_top_border_radius').val() + '%',
		    '-moz-border-radius'	: $('#to_top_border_radius').val() + '%',
		    'border-radius'			: $('#to_top_border_radius').val() + '%',
			'color' 				: $('.to_top_icon_color').val(),
			'background-color' 		: $('.to_top_icon_bg_color').val(),
			'font-size'				: $('#to_top_icon_size').val() + 'px',
		    'height'				: $('#to_top_icon_size').val() + 'px',
			'width'					: $('#to_top_icon_size').val() + 'px',
		});

	    $( '#to_top_options_style' ).change(function(){
	    	var value;
	    	value = $(this).val();
	    	if ( 'image' == value ) {
	    		$( '.to_top_image_settings' ).show();
	    		$( '.to_top_icon_settings' ).hide();
	    	}
	    	else {
				$( '.to_top_icon_settings' ).show();
				$( '.to_top_image_settings' ).hide();
	    	}
	    });

	    var value;
		value =  $( '#to_top_options_style' ).val();
		if ( 'image' == value ) {
			$( '.to_top_image_settings' ).show();
			$( '.to_top_icon_settings' ).hide();
		}
		else {
			$( '.to_top_icon_settings' ).show();
			$( '.to_top_image_settings' ).hide();
		}
	});

	$(function() {

        // Tabs
        $('.catchp_widget_settings .nav-tab-wrapper a').on('click', function(e){
            e.preventDefault();

            if( !$(this).hasClass('ui-state-active') ){
                $('.nav-tab').removeClass('nav-tab-active');
                $('.wpcatchtab').removeClass('active').fadeOut(0);

                $(this).addClass('nav-tab-active');

                var anchorAttr = $(this).attr('href');

                $(anchorAttr).addClass('active').fadeOut(0).fadeIn(500);
            }

        });
    });

    // jQuery Match Height init for sidebar spots
    $(document).ready(function() {
        $('.catchp-sidebar-spot .sidebar-spot-inner, .col-2 .catchp-lists li, .col-3 .catchp-lists li').matchHeight();
    });

})( jQuery );


jQuery(function($) {
    $('#image-settings').hide();
    $('#to_top_options_style').change(function(){
        if ($(this).val() == 'image')
        {
			$('#icon-settings').hide();
			$('#image-settings').show();
        } else {
        	$('#icon-settings').show();
			$('#image-settings').hide();
        }
    });
});


/**
 * Facebook Script
 */
(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];

	if (d.getElementById(id)) return;

	js = d.createElement(s); js.id = id;

	js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=276203972392824";

	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

/**
 * Twitter Script
 */
!function(d,s,id){
	var js,fjs=d.getElementsByTagName(s)[0];

	if(!d.getElementById(id)){
		js=d.createElement(s);

		js.id=id;

		js.src="//platform.twitter.com/widgets.js";

		fjs.parentNode.insertBefore(js,fjs);
	}
}(document,"script","twitter-wjs");
