jQuery(document).ready(function ($) {




    //during the authorisation process get the code and send it to php

    //this function can find a parameter in a query string
    function getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
    var code = getParameterByName('code');
    var authenticate = getParameterByName('authenticate');
    var tab = getParameterByName('tab');
    // var realmId = getParameterByName('realmId');

    if (code != null && authenticate != 'false' && tab == 'zoommeetings') {
    
        alertify.log("Please wait while we complete the connection...");

        var data = {
                    'action': 'save_authentication_details_zoom_meetings',
                    'code': code,
                    // 'realmId': realmId,
                    };


        jQuery.post(ajaxurl, data, function (response) {
            
           console.log(response);
            
            if(response == 200){
                
                alertify.success('The connection has now been completed'); 

                var currentUrl = window.location.href;

                currentUrl += '&authenticate=false'

                window.location.replace(currentUrl);


            } else {
                alertify.error('There\'s been some kind of error, please try again. If the error persists please contact support.');        
            }
            
        });   
        
    }




    //disconnect
    $('body').on('click','.disconnect-from-zoom-meetings', function(event){

        event.preventDefault();
        event.stopPropagation();

        alertify.log("Please wait while we disconnect...");


        var data = {
            'action': 'zoom_meetings_disconnect',
        };

        jQuery.post(ajaxurl, data, function (response) {
            
            console.log('Complete');
            alertify.success('You have been disconnected.');
            location.reload();
            
        });

        return false;

    });




    //do manual sync
    $('.wrap').on("click","#create-zoom-meetings-registrants", function(event){

        event.preventDefault();
        event.stopPropagation();

        alertify.log("Please wait while we sync the order...");

        var order_id = $(this).attr('data');

        var data = {
            'action': 'zoom_meetings_process_shop_order',
            'order_id': order_id,
        };


        jQuery.post(ajaxurl, data, function (response) {
            
            console.log('Complete');
            alertify.success('The order has been synced');
            location.reload();
            
        });

        return false;

    });




    //clear transients
    $('body').on('click','#clear-zoom-meetings-transients', function(event){

        event.preventDefault();
        // event.stopPropagation();

        alertify.log("Please wait while we clear the cache...");


        var data = {
            'action': 'zoom_meetings_clear_transients',
        };

        jQuery.post(ajaxurl, data, function (response) {
            
            console.log('Complete');
            alertify.success('The cache has been cleared');
            // location.reload();
            
        });

        return false;

    });



    //expand faq
    $('body').on('click','.faq-container-meetings .question', function(event){

        $(this).next().toggle();

        if($(this).next().css('display') == 'block'){
            $(this).find('span').removeClass('dashicons-plus').addClass('dashicons-minus');
        } else {
            $(this).find('span').removeClass('dashicons-minus').addClass('dashicons-plus');
        }

    });

    //move the faq to below settings
    if($('.faq-container-meetings').length){
        $('.faq-container-meetings').insertAfter('.submit');
    }



});    