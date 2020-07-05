<?php

/**
* 
*
*
* Automatically delete or make draft old meeting products
*/
add_action('admin_init','woocommerce_to_zoom_meetings_old_meeting_products');

function woocommerce_to_zoom_meetings_old_meeting_products(){

    //we want to only run this once per a day
    $transient_name = 'old_zoom_meeting_check';
    
    //check if transient doesn't exist i.e. run
    if(!get_transient($transient_name)){

        //set the transient to prevent anything further happening
        set_transient($transient_name, 'RAN', 1*DAY_IN_SECONDS);

        //check the users setting
        $setting = get_option('wc_settings_zoom_meetings_old_meeting_products');

        //only continue if we need to take an action
        if(isset($setting) && $setting != 'nothing'){

            //get meetings
            //only continue if authenticated
            $meeting_data = array();
            if(get_option('wc_settings_zoom_refresh_token')){
                $url = woocommerce_to_zoom_meetings_get_api_base().'users/me/meetings?page_size=300';


                $response = wp_remote_get( $url, array(
                    'headers' => array(
                        'Authorization' => 'Bearer '.woocommerce_to_zoom_meetings_get_access_token(),
                    )
                ));

                $status = wp_remote_retrieve_response_code( $response );

                if($status == 200){
                    $decodedBody = json_decode(preg_replace('/("\w+"):(\d+(\.\d+)?)/', '\\1:"\\2"', $response['body']), true);
                    $meetings = $decodedBody['meetings'];
                    foreach($meetings as $meeting){


                        $meeting_start_timestamp = strtotime($meeting['start_time']);
                        $meeting_id = $meeting['id'];
                        $meeting_data[$meeting_id] = $meeting_start_timestamp;  
                    }

                }
            }

            //only continue if we have data
            if(count($meeting_data)>0){

                //now we need to loop through the products and check if they have a meeting id
                $args = array( 
                    'post_type' => 'product', 
                    'posts_per_page' => -1,
                    'post_status' => 'publish', 
                );
                
                $products = get_posts( $args ); 

                //only continue if products exist
                if($products){
                    foreach($products as $product){
                        $product_id = $product->ID;
                        $meeting_id = get_post_meta( $product_id, 'zoom_meeting_selection', true );
                        $meeting_start_time = $meeting_data[$meeting_id];

                        //check to see if meeting id exists
                        if(strlen($meeting_id)>0 && strlen($meeting_start_time)>0){

                            //get the wordpress start time
                            $current_time = current_time( 'timestamp' );

                            //check time
                            if($current_time > $meeting_start_time){
                                //do actions
                                //do draft
                                if($setting == 'draft'){
                                    wp_update_post(array(
                                        'ID'    =>  $product_id,
                                        'post_status'   =>  'draft'
                                    ));
                                }
                                //do delete
                                if($setting == 'delete'){
                                    wp_trash_post( $product_id ); 
                                }


                            } //end check of time
                        } //end check if meeting id exists
                    } //end for each product
                } //end check if products exist
            } //end check of meeting data exists
        } //end check if user setting is relevant
    } //end transient check
}

?>