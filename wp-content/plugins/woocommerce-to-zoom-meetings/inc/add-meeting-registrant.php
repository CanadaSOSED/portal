<?php

    // add_action( 'woocommerce_checkout_update_order_meta', 'woocommerce_to_zoom_meetings_add_registrant',999);
    add_action( 'woocommerce_order_status_completed', 'woocommerce_to_zoom_meetings_add_registrant',0 );
    
    function woocommerce_to_zoom_meetings_add_registrant( $order_id ) {

        //start WooCommerce logging
        $logger = wc_get_logger();
        $context = array( 'source' => 'woocommerce-to-zoom-meetings' );
        $logger->info( 'STARTING PROCESSING ORDER: '.$order_id, $context );

        //get order object inc ase we need it
        $order = new WC_Order( $order_id );

        //get the metadata for the order
        $all_post_meta = get_post_meta($order_id);

        foreach($all_post_meta as $meta_key => $meta_value){

            //only proceed for keys which contain zoom_meeting
            if (strpos($meta_key, 'zoom_meeting') !== false) {
                //key looks like: zoom_meeting-217072930-1
                $key_exploded = explode('-',$meta_key);
                $meeting_id = $key_exploded[1];
                $meeting_id = woocommerce_to_zoom_meetings_sanitize_meeting_id($meeting_id);

                $logger->info( 'Meeting ID: '.$meeting_id, $context );
                

                //if meeting id is bookable product then lets crate the meeting first in Zoom Meetings
                //by checking if $woocommerce_booking_meeting_id is not set it prevents multiple meetings from being created
                if($meeting_id == 'WooCommerceBookings' && !isset($woocommerce_booking_meeting_id)){
                    
                    $logger->info( 'We are going to try to create a meeting in Zoom as this is a bookable product', $context );

                    if ( is_callable( 'WC_Booking_Data_Store::get_booking_ids_from_order_id') ) {
                        $booking_data = new WC_Booking_Data_Store();
                        $booking_ids = $booking_data->get_booking_ids_from_order_id( $order_id );
            
                        foreach($booking_ids as $booking_id){

                            if (class_exists( 'sitepress' ) && (apply_filters( 'wpml_post_language_details', NULL, $booking_id )['language_code'] != $all_post_meta['wpml_language'][0])) {
                                continue;
                            }

                            $booking_start = get_post_meta($booking_id, '_booking_start', true); //20200417070000
                            $booking_end = get_post_meta($booking_id, '_booking_end', true); //2020 04 17 08 00 00

                            //we need to convert date times like 20200417070000 to 2020-03-31T12:02:00Z
                            $booking_start_year = substr($booking_start,0,4); 
                            $booking_start_month = substr($booking_start,4,2); 
                            $booking_start_day = substr($booking_start,6,2); 
                            $booking_start_hour = substr($booking_start,8,2); 
                            $booking_start_minute = substr($booking_start,10,2); 
                            $booking_start_second = substr($booking_start,12,2); 
                            $booking_start_nice = $booking_start_year.'-'.$booking_start_month.'-'.$booking_start_day.'T'.$booking_start_hour.':'.$booking_start_minute.':'.$booking_start_second.'Z';

                            $booking_end_year = substr($booking_end,0,4); 
                            $booking_end_month = substr($booking_end,4,2); 
                            $booking_end_day = substr($booking_end,6,2); 
                            $booking_end_hour = substr($booking_end,8,2); 
                            $booking_end_minute = substr($booking_end,10,2); 
                            $booking_end_second = substr($booking_end,12,2); 
                            $booking_end_nice = $booking_end_year.'-'.$booking_end_month.'-'.$booking_end_day.'T'.$booking_end_hour.':'.$booking_end_minute.':'.$booking_end_second.'Z';

                            //now we need to work out the agenda
                            $booking_start_timestamp = strtotime($booking_start_nice);
                            $booking_end_timestamp = strtotime($booking_end_nice);
                            $duration = round(abs($booking_end_timestamp - $booking_start_timestamp) / 60,2);

                            //get other order details
                            $billing_first_name = $order->get_billing_first_name();
                            $billing_last_name = $order->get_billing_last_name();
                            $billing_full_name = $billing_first_name.' '.$billing_last_name;


                            //we need to get the product title and description
                            $items = $order->get_items();
                            foreach ( $items as $item ) {
                                $product_id = $item->get_product_id();
                                $meeting_selection = get_post_meta($product_id,'zoom_meeting_selection',true);
                                // $logger->info( 'PRODUCT ID FOUND: '.$product_id.' MEETING SELECTION: '.$meeting_selection, $context );
                                if($meeting_selection == 'WooCommerce Bookings'){

                                    // $logger->info( 'I WAS RAN', $context );

                                    $product_title = get_the_title($product_id).' ('.$billing_full_name.')'; // we will put the billing name in the meeting to make it more identifiable
                                    $product_description = wp_strip_all_tags(get_the_content(null,false,$product_id));
                                    $product_description = trim(preg_replace('/\s+/', ' ', $product_description));
                                    $product_description = substr($product_description, 0, 1999);
                                }

                            }
                            

                            //lets actually create the meeting
                            //register the person for the webinar
                            $url = woocommerce_to_zoom_meetings_get_api_base().'users/me/meetings';

                            $access_token = woocommerce_to_zoom_meetings_get_access_token();

                            $data = array(
                                'topic' => $product_title,
                                'type' => 2,
                                'start_time' => $booking_start_nice,
                                'duration' => $duration,
                                'timezone' => get_option('timezone_string'),
                                'agenda' => $product_description,
                                'settings' => array('approval_type' => 0),
                            );


                            $response = wp_remote_post( $url, array(
                                'headers' => array(
                                    'Authorization' => 'Bearer '.$access_token,
                                    'Content-Type' => 'application/json; charset=utf-8',
                                ),
                                'body' => json_encode($data),
                            ));

                            $logger->info( 'URL: '.$url.' DATA: '.json_encode($data).' ACCESSTOKEN: '. $access_token , $context);

                            $status = wp_remote_retrieve_response_code( $response );

                            if($status == 201){
                                //do something
                                $decodedBody = json_decode(preg_replace('/("\w+"):(\d+(\.\d+)?)/', '\\1:"\\2"', $response['body']), true);
                                $woocommerce_booking_meeting_id = $decodedBody['id'];
                                $logger->info( 'NEW MEETING ID: '.$woocommerce_booking_meeting_id, $context);
                            } else {
                                $logger->info( 'SOMETHING WENT WRONG, STATUS: '.$status, $context);   
                            }

                        }

            
                    }
                    
                    
                    

                }

                if(isset($woocommerce_booking_meeting_id)){
                    $meeting_id = $woocommerce_booking_meeting_id;
                }


                // $logger->info( 'CHECK ME: '.$meeting_id, $context);



                $meta_value = get_post_meta($order_id,$meta_key,true);

                $body_data_to_json = json_encode($meta_value);

                //register the person for the webinar
                $url = woocommerce_to_zoom_meetings_get_api_base().'meetings/'.$meeting_id.'/registrants';

                //prevent getting the access token twice in quick succession 
                if(!isset($access_token)){
                    $access_token = woocommerce_to_zoom_meetings_get_access_token();
                }

                

                $response = wp_remote_post( $url, array(
                    'headers' => array(
                        'Authorization' => 'Bearer '.$access_token,
                        'Content-Type' => 'application/json; charset=utf-8',
                    ),
                    'body' => $body_data_to_json,
                ));

                $status = wp_remote_retrieve_response_code( $response );

                

                if($status == 201){
                    //do something
                    $decodedBody = json_decode(preg_replace('/("\w+"):(\d+(\.\d+)?)/', '\\1:"\\2"', $response['body']), true);
                    $registrant_id = $decodedBody['registrant_id'];
                    $join_url = $decodedBody['join_url'];

                    //lets add meta to the order id
                    if(get_post_meta($order_id,'zoom_meetings_registrant_ids',false)){
                        $existing_registrants = get_post_meta($order_id,'zoom_meetings_registrant_ids',true);

                        $existing_registrants[$registrant_id] = array(
                            'first_name' => $meta_value['first_name'],
                            'last_name' => $meta_value['last_name'],
                            'email' => $meta_value['email'],
                            'join_url' => $join_url,
                        ); 

                        update_post_meta( $order_id, 'zoom_meetings_registrant_ids', $existing_registrants );

                    } else {
                        update_post_meta( $order_id, 'zoom_meetings_registrant_ids', array($registrant_id => array(
                            'first_name' => $meta_value['first_name'],
                            'last_name' => $meta_value['last_name'],
                            'email' => $meta_value['email'],
                            'join_url' => $join_url,
                        )));
                    }

                    

                } else {
                    //something bad happened
                    $logger->info('URL: '.$url,$context);
                    $logger->info('BODY: '.$body_data_to_json,$context);
                    $logger->info('ACCESS TOKEN: '.$access_token,$context);
                    $logger->info('STATUS: '.$status,$context);
                    $logger->info('RETURN BODY: '.$response['body'],$context);
                }

            }
        }   
        

    }


?>