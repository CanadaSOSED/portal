(function( $ ) {
	'use strict';

	//Javascript GET cookie parameter
	var $_GET = {};
	document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
	    function decode(s) {
	        return decodeURIComponent(s.split("+").join(" "));
	    }

	    $_GET[decode(arguments[1])] = decode(arguments[2]);
	});

    // Get time var defined in woo backend
    var $time = 1;
    if(typeof gens_raf !== 'undefined' && gens_raf.timee !== '') {
        $time = parseInt(gens_raf.timee);
    }

	//If raf is set, add cookie.
	if( typeof $_GET["raf"] !== 'undefined' && $_GET["raf"] !== null ){
		//console.log(window.location.hostname);
		cookie.set("gens_raf",$_GET["raf"],{ expires: $time, path:'/' });
	}

	// Share Shortcode
    $.fn.rafSocShare = function(opts) {
    	var $this = this;
    	var $win = $(window);
    	
    	opts = $.extend({
    		attr : 'href',
    		facebook : false,
    		google_plus : false,
    		twitter : false,
    		linked_in : false,
    		pinterest : false,
            whatsapp : false
    	}, opts);
    	
    	for(var opt in opts) {
    		
    		if(opts[opt] === false) {
    			continue;
    		}
    		
    		switch (opt) {
    			case 'facebook':
    				var url = 'https://www.facebook.com/sharer/sharer.php?u=';
    				var name = 'Facebook';
    				_popup(url, name, opts[opt], 400, 640);
    				break;
    			
    			case 'twitter':
                    var posttitle = $(".gens-referral_share__tw").data("title");
                    var via = $(".gens-referral_share__tw").data("via");
                    var url = 'https://twitter.com/intent/tweet?via='+via+'&text='+posttitle+'&url=';
    				var name = 'Twitter';
    				_popup(url, name, opts[opt], 440, 600);
    				break;
    			
				case 'google_plus':
    				var url = 'https://plus.google.com/share?url=';
    				var name = 'Google+';
    				_popup(url, name, opts[opt], 600, 600);
    				break;
    			
    			case 'linked_in':
    				var url = 'https://www.linkedin.com/shareArticle?mini=true&url=';
    				var name = 'LinkedIn';
    				_popup(url, name, opts[opt], 570, 520);
    				break;
				
				case 'pinterest':
    				var url = 'https://www.pinterest.com/pin/find/?url=';
    				var name = 'Pinterest';
    				_popup(url, name, opts[opt], 500, 800);
    				break;
                    
                case 'whatsapp':
                    var posttitle = $(".gens-referral_share__wa").data("title");
                    var name = 'Whatsapp';
                    var url = 'whatsapp://send?text='+posttitle+'%20';
                    _popup(url, name, opts[opt], 500, 800);
				default:
					break;
    		}
    	}
    	
    	function _popup(url, name, opt, height, width) {
            if(opt !== false && $this.find(opt).length) {               
                $this.on('click', opt, function(e){
                    e.preventDefault();
                    
                    var top = (screen.height/2) - height/2;
                    var left = (screen.width/2) - width/2;
                    var share_link = $(this).attr(opts.attr);
                    
                    if(name != "Whatsapp") {
                        window.open(
                            url+encodeURIComponent(share_link),
                            name,
                            'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height='+height+',width='+width+',top='+top+',left='+left
                        );
                    } else {
                        window.location = url;
                        return true;
                    }
                    
                    return false;
                });
            }
        }
        return;
	};

	jQuery(document).ready(function(){
		$('.gens-referral_share').rafSocShare({
			facebook : '.gens-referral_share__fb',
			twitter : '.gens-referral_share__tw',
			google_plus : '.gens-referral_share__gp',
		    /*	
            linked_in : '.gens_raf_linked',
			pinterest : '.gens_raf_pint',
            */
	        whatsapp : '.gens-referral_share__wa'
		});

        $("#js--gens-email-clone").on("click",function(e){
            e.preventDefault();
            var $clone = $("#gens-referral_share__email").children().first().clone();
            $clone.insertBefore("#js--gens-email-clone").find("input").val("");
        })

        $("#gens-referral_share__email").submit(function(e){
            e.preventDefault();
            if($('.gens-referral_share__email__inputs').find("input[type='email']").val() !== '') {
                gensAjaxSubmit();                
            }

        });

        $("#billing_email").on("blur",function(){
            var val = $(this).val();
            if (val.indexOf('@') > -1) {
                jQuery(document.body).trigger("update_checkout");
            }
        });

        function gensAjaxSubmit(){
            var data = new Array();
            var link = $(".gens-refer-a-friend").data("link");
            $(".gens-referral_share__email__inputs").each(function(){
                var email = $(this).find("input[type='email']").val();
                var text  = $(this).find("input[type='text']").val();
                if( email != "") {
                    var valueToPush = { };
                    valueToPush.email = email;
                    valueToPush.name  = text;
                    data.push(valueToPush); 
                }
            });

            jQuery.ajax({
                type:"POST",
                url: gens_raf.ajax_url,
                data: { data : data, link : link, action: 'gens_share_via_email' },
                success:function(data){
                    // Remove form and say thx 
                    var $success = "<div id='gens-raf-mail-share'>"+gens_raf.success_msg+"</div>";
                    $("#gens-referral_share__email").remove();
                    $(".gens-referral_share__email").append($success);
                }
            });
            return false;

        }
	});

})( jQuery );