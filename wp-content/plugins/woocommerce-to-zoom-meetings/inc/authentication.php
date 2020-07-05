<?php
/**
* 
*
*
* Get client ID
*/
function woocommerce_to_zoom_meetings_get_client_id() {


    $sandbox_mode_setting = get_option('wc_settings_zoom_meetings_sandbox_mode');

    if(isset($sandbox_mode_setting) && $sandbox_mode_setting == 'yes'){
        $clientId = 'rLjwXmDhRyKJDqt1AMhIAA'; //development
    } else {
        $clientId = '9eHIPv9TTrWh7JgciptoKw'; 
    }
    
    return $clientId;
 
}   

/**
* 
*
*
* Get redirect URI
*/
function woocommerce_to_zoom_meetings_get_redirect_uri() {

    $redirect_uri = 'https://northernbeacheswebsites.com.au/redirectzoom/'; 
    return $redirect_uri;
 
}   


/**
* 
*
*
* Get authorisation
*/
function woocommerce_to_zoom_meetings_get_authorisation() {

    $clientId = woocommerce_to_zoom_meetings_get_client_id();

    $sandbox_mode_setting = get_option('wc_settings_zoom_meetings_sandbox_mode');

    if(isset($sandbox_mode_setting) && $sandbox_mode_setting == 'yes'){
        $clientSecret = 'KVSkmbAV680M4wqFz5AqYUmttF6ZY4Id'; //development
    } else {
        $clientSecret = 'GKuyYKqpbjp7ukdlXAvcliIJuenaDfiZ'; 
    }

    return base64_encode($clientId.':'.$clientSecret);

}   
/**
* 
*
*
* Get API base URL
*/
function woocommerce_to_zoom_meetings_get_api_base() {

    $api_base = 'https://api.zoom.us/v2/'; 

    return $api_base;
 
} 

/**
* 
*
*
* Save authentication details
*/
add_action( 'wp_ajax_save_authentication_details_zoom_meetings', 'woocommerce_to_zoom_meetings_save_authentication_details' );
function woocommerce_to_zoom_meetings_save_authentication_details() {
    

    //get board name from ajax call
    $code = $_POST['code'];

    $url = 'https://zoom.us/oauth/token';
    $url .= '?grant_type=authorization_code';
    $url .= '&code='.$code;
    $url .= '&redirect_uri='.woocommerce_to_zoom_meetings_get_redirect_uri();
    
    //get access token
    $response = wp_remote_post($url , array(
        'headers' => array(
            'Authorization' => 'Basic '.woocommerce_to_zoom_meetings_get_authorisation(),
            // 'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
        ),
        // 'body' => array(
        //     'code' => $code,
        //     'redirect_uri' => 'https://northernbeacheswebsites.com.au/redirectintuit/',
        //     'grant_type' => 'authorization_code',
        // ),
    ));

    $status = wp_remote_retrieve_response_code( $response );

    if($status == 200){
        //decode the response and get the variables
        $decodedBody = json_decode(preg_replace('/("\w+"):(\d+(\.\d+)?)/', '\\1:"\\2"', $response['body']), true);

        $refresh_token = $decodedBody['refresh_token'];

        //update the options with the result
        update_option('wc_settings_zoom_refresh_token', $refresh_token);


        // echo 'SUCCESS';
    } else {
        // echo 'ERROR';
    }

    echo $status;
    //die
    wp_die();
 
}

/**
* 
*
*
* Get access token
*/
function woocommerce_to_zoom_meetings_get_access_token() {

    $refresh_token = get_option('wc_settings_zoom_refresh_token');

    $response = wp_remote_post( 'https://zoom.us/oauth/token', array(
        'headers' => array(
            'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
            'Authorization' => 'Basic '.woocommerce_to_zoom_meetings_get_authorisation(),
            'Cache-Control' => 'no-cache',
        ),
        'body' => array(
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token',
        ),
    ));

    $status = wp_remote_retrieve_response_code( $response );

    if($status == 200){

        $decodedBody = json_decode(preg_replace('/("\w+"):(\d+(\.\d+)?)/', '\\1:"\\2"', $response['body']), true);

        $refresh_token = $decodedBody['refresh_token'];
        $access_token = $decodedBody['access_token'];
      
        //update the options with the result
        update_option('wc_settings_zoom_refresh_token', $refresh_token);

        return $access_token;

    } else {
        return 'ERROR';
    } 
   
}  




?>