$j=jQuery.noConflict();
$j(document).ready(function (){
    
    var rtbs_def_colors = {
        backgroundColor : 'transparent',
    };
        
    $j('.rtbs').each(function(){
        var color = $j(this).find('.rtbs_color').html();
        var breakpoint = $j(this).find('.rtbs_breakpoint').html();
        var rtbssize = $j(this).width();
        if (rtbssize > breakpoint) {
            $j(this).removeClass('rtbs_full');
            $j(this).find(".mobile_toggle").hide();
            $j(this).find('.rtbs_menu li:not(".mobile_toggle")').show();
            $j(this).find('.rtbs_menu li:not(".mobile_toggle")').css( "display", "inline-block" );
        }
        else {
            $j(this).addClass('rtbs_full');
            $j(this).find('.rtbs_menu li:not(".mobile_toggle")').css( "display", "block" );
            $j(this).find(".mobile_toggle").css( 'background', color );
            $j(this).find('.rtbs_menu li:not(".mobile_toggle")').hide();
        }
    });
        
    $j(window).resize(function() {
        var resizeTimer;
        $j('.rtbs').each(function(){
            var breakpoint = $j(this).find('.rtbs_breakpoint').html();
            var rtbssize = $j(this).width();
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (rtbssize > breakpoint) {
                    $j(this).removeClass('rtbs_full');
                } else {
                    $j(this).addClass('rtbs_full');
                }
            }, 10);
        });
    });
        
    $j('.rtbs').each(function(){
        var current_text = $j(this).find( "a.active" ).text();
        $j(this).find( ".mobile_toggle" ).empty();
        $j(this).find( ".mobile_toggle" ).append( current_text );
    });   
        
    $j(window).resize(function() {
        $j('.rtbs').each(function(){
            var breakpoint = $j(this).find('.rtbs_breakpoint').html();
            var color = $j(this).find('.rtbs_color').html();
            var rtbssize = $j(this).width();
            if (rtbssize > breakpoint) {
                $j(this).removeClass('rtbs_full');
                $j(this).find(".mobile_toggle").hide();
                $j(this).find('.rtbs_menu li:not(".mobile_toggle")').show();
                $j(this).find('.rtbs_menu li:not(".mobile_toggle")').css( "display", "inline-block" );
                $j(this).find('.rtbs_menu li > a').css(rtbs_def_colors);
                $j(this).find('.rtbs_menu li').find('.active').css( 'background', color );
            } else {
                $j(this).addClass('rtbs_full');
                $j(this).find(".mobile_toggle").show();
                $j(this).find(".mobile_toggle").css( 'background', color );
                $j(this).find('.rtbs_menu li:not(".mobile_toggle")').css( "display", "block" );  
                $j(this).find('.rtbs_menu li:not(".mobile_toggle")').hide();
            }
        });
    });
        
    $j(".mobile_toggle").click(function(){ 
        var color = $j(this).closest('.rtbs').find('.rtbs_color').html();
        $j(this).parent().children('li').not(".rtbs_menu li.mobile_toggle").slideToggle(90);
        $j(this).siblings('.current').css( "display", "none" );
        $j(this).css( 'background', color );
        $j(this).siblings().find('a').css( 'background' , '#f1f1f1' );
        return false;
    });
        
    $j(".rtbs_menu li > a").click(function(){
        var color = $j(this).closest('.rtbs').find('.rtbs_color').html();
        var breakpoint = $j(this).closest('.rtbs').find('.rtbs_breakpoint').html();
        var rtbssize = $j(this).closest('.rtbs').width();
            if (rtbssize > breakpoint) {
                $j(this).addClass('active');
                $j(this).css('background', color);
                $j(this).parent().siblings().children().css(rtbs_def_colors);
                $j(this).parent().siblings().children().removeClass('active');
                $j(this).closest('.rtbs').children('.rtbs_content').hide();
                var current_id = $j(this).attr('data-tab');
                $j(current_id).fadeToggle(0);
                var current_text = $j(this).closest('.rtbs').find( "a.active" ).text();
                $j(this).closest('.rtbs').find( ".mobile_toggle" ).empty();
                $j(this).closest('.rtbs').find( ".mobile_toggle" ).append( current_text );
                $j(this).parent().siblings().removeClass('current');
                $j(this).parent().addClass('current');
                return false;
            } else {
                $j(this).closest('.rtbs').find('.rtbs_menu li').css("display", "block");
                $j(this).addClass('active');
                $j(this).parent().siblings().children().removeClass('active');
                $j(this).closest('.rtbs').find('.rtbs_content').hide();
                var current_id = $j(this).attr('data-tab');
                $j(current_id).fadeToggle(0);
                $j(this).closest('.rtbs').find('.rtbs_menu li').not('.mobile_toggle').slideToggle(0); 
                var current_text = $j(this).closest('.rtbs').find( "a.active" ).text();
                $j(this).closest('.rtbs').find( ".mobile_toggle" ).empty();
                $j(this).closest('.rtbs').find( ".mobile_toggle" ).append( current_text );
                $j(this).parent().siblings().removeClass('current');
                $j(this).parent().addClass('current');
                return false;
            }    
            return false;
        
    });
});