jQuery(document).ready(function ($) {

    $('body').on('click','.woocommerce-to-zoom-meetings-copy-from-billing', function(event){

        event.preventDefault();
        
        // these field replacements are just occuring on the first item of each new webinar   
        $('.1-first_name input').each(function() {
            $(this).val($('#billing_first_name').val());    
        }); 
    
        $('.1-last_name input').each(function() {
            $(this).val($('#billing_last_name').val());    
        }); 
                
        $('.1-phone input').each(function() {
            $(this).val($('#billing_phone').val());    
        });
    
        $('.1-email input').each(function() {
            $(this).val($('#billing_email').val());    
        });       
                
        // these field replacements are occuring on all webinar fields
        $('.org input').each(function() {
            $(this).val($('#billing_company').val());    
        });   

        $('.state').each(function() {
            var stateValue = $('#billing_state option:selected').val(); 
            $(this).find('select').val(stateValue).trigger('change');  
        });   

        $('.country').each(function() {
            var countryValue = $('#billing_country option:selected').val(); 
            $(this).find('select').val(countryValue).trigger('change');  
        });   
                   
        $('.address input').each(function() {
            $(this).val($('#billing_address_1').val()+' '+$('#billing_address_2').val());    
        });      
            
        $('.city input').each(function() {
            $(this).val($('#billing_city').val());    
        });        
            
        $('.zip input').each(function() {
            $(this).val($('#billing_postcode').val());    
        });

    });

});