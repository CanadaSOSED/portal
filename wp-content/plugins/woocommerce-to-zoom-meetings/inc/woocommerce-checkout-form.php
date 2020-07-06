<?php



/**
* 
*
*
* Gets registration fields for a meeting
*/
function woocommerce_to_zoom_meetings_get_meeting_registration_fields($meeting_id) {

    $meeting_id = woocommerce_to_zoom_meetings_sanitize_meeting_id($meeting_id);
    $transient_name = 'zoom_meetings_registration_fields_'.$meeting_id;
    $transient = get_transient($transient_name);

    if ($transient != false){
        return $transient;
    } else {

        $url = woocommerce_to_zoom_meetings_get_api_base().'meetings/'.$meeting_id.'/registrants/questions';

        $response = wp_remote_get( $url, array(
            'headers' => array(
                'Authorization' => 'Bearer '.woocommerce_to_zoom_meetings_get_access_token(),
            )
        ));

        $status = wp_remote_retrieve_response_code( $response );

        if($status == 200){

            $decodedBody = json_decode(preg_replace('/("\w+"):(\d+(\.\d+)?)/', '\\1:"\\2"', $response['body']), true);
            
            //set the transient for 1 hour
            set_transient($transient_name,$decodedBody, 60*60); 

            return $decodedBody;

        }

    }
}



/**
* 
*
*
* Adds custom fields to the woocommerce checkout area
*/
add_action( 'woocommerce_after_order_notes', 'woocommerce_to_zoom_meetings_checkout_fields' );

function woocommerce_to_zoom_meetings_checkout_fields( $checkout ) {

    global $woocommerce;
    $cart = $woocommerce->cart->get_cart();
    $cart_items_ids = array();



    //do pre-query to find out whether meetings exist for the purpose of the button
    $meetings_exist = false;
    foreach ( $cart as $item_key => $item_value ) {
        $cart_product_id = $item_value[ 'data' ] -> get_id();
        $meeting_id = get_post_meta( $cart_product_id, 'zoom_meeting_selection', true );

        if(strlen($meeting_id)>0){
            $meetings_exist = true;
        }
    }
    
    //loop through each item in the cart    
    foreach ( $cart as $item_key => $item_value ) {

        //get variables for the cart item
        $cart_product_id = $item_value[ 'data' ] -> get_id();
        $cart_product_title = $item_value[ 'data' ] -> get_title();  
        $cart_product_slug = $item_value[ 'data' ]->get_slug();   
        $cart_product_qty = $item_value['quantity'];

        //if it is a booking instead of using the quantity use the persons instead
        if(array_key_exists('booking',$item_value)){

            $number_of_persons = $item_value['booking']['Persons'];;

            //we need to make sure there's at least 1 registrant!
            if($number_of_persons == 0){
                $number_of_persons = 1;
            }

            $cart_product_qty = $number_of_persons;

        }

        $meeting_id = get_post_meta( $cart_product_id, 'zoom_meeting_selection', true );  

        $meeting_id = woocommerce_to_zoom_meetings_sanitize_meeting_id($meeting_id);
        
        if(strlen($meeting_id)>0){

            // var_dump($meeting_id);

            echo '<div class="zoom-meeting-section" style="margin-top: 30px;">';

                do_action( 'woocommerce_to_zoom_meetings_before_product_title' );

                //do button if meetings exist
                if($meetings_exist){
                    echo '<button class="woocommerce-to-zoom-meetings-copy-from-billing">'.__('Copy from billing details','woocommerce-to-zoom-meetings').'</button>';
                }
                

                //do heading
                echo '<h3>'.$cart_product_title.'</h3>';

                do_action( 'woocommerce_to_zoom_meetings_after_product_title' );

                //lets get the registration fields for the webinar
                $registration_fields = woocommerce_to_zoom_meetings_get_meeting_registration_fields($meeting_id);

                // var_dump($registration_fields);



                //lets now loop through each registrant by using the QTY as a guide
                for ($i = 1 ; $i <= $cart_product_qty; $i++){

                    echo '<strong class="zoom-meeting-registrant-section">'.__('Registrant','woocommerce-to-zoom-meetings').' '.$i.'</strong>';

                    //first we need to do the required fields which is the first name and email
                    //DO FIRST NAME
                    $field_identifier = $meeting_id.'-'.$i.'-first_name';

                    $field_class = array('form-row-wide '.$field_identifier.' '.$i.'-first_name first_name');   

                    $field_options = array();

                    woocommerce_form_field( $field_identifier, array(
                        'type'          => 'text',
                        'required'      => true,   
                        'class'         => $field_class,
                        'label'         => __('First Name','woocommerce-to-zoom-meetings'),
                        'maxlength'     => false,   
                        'placeholder'   => __('','woocommerce-to-zoom-meetings'),
                        'options'       => $field_options,    
                    ), $checkout->get_value( $field_identifier )); 

                    //DO LAST NAME
                    $field_identifier = $meeting_id.'-'.$i.'-last_name';

                    $field_class = array('form-row-wide '.$field_identifier.' '.$i.'-last_name last_name');   

                    $field_options = array();

                    woocommerce_form_field( $field_identifier, array(
                        'type'          => 'text',
                        'required'      => true,   
                        'class'         => $field_class,
                        'label'         => __('Last Name','woocommerce-to-zoom-meetings'),
                        'maxlength'     => false,   
                        'placeholder'   => __('','woocommerce-to-zoom-meetings'),
                        'options'       => $field_options,    
                    ), $checkout->get_value( $field_identifier )); 

                    //DO EMAIL
                    $field_identifier = $meeting_id.'-'.$i.'-email';

                    $field_class = array('form-row-wide '.$field_identifier.' '.$i.'-email email');   

                    $field_options = array();

                    woocommerce_form_field( $field_identifier, array(
                        'type'          => 'email',
                        'required'      => true,   
                        'class'         => $field_class,
                        'label'         => __('Email','woocommerce-to-zoom-meetings'),
                        'maxlength'     => false,   
                        'placeholder'   => __('','woocommerce-to-zoom-meetings'),
                        'options'       => $field_options,    
                    ), $checkout->get_value( $field_identifier )); 


                    //now we need to do our API registration fields

                    //lets do our standard fields first
                    if(is_array($registration_fields) && count($registration_fields['questions'])>0 && $meeting_id != 'WooCommerceBookings'){

                        $field_type_helper = array(
                            // 'last_name' => 'text',
                            'address' => 'text',
                            'city' => 'text',
                            'country' => 'country',
                            'zip' => 'text',
                            'state' => 'state',
                            'phone' => 'tel',
                            'industry' => 'text',
                            'org' => 'text',
                            'job_title' => 'text',
                            'purchasing_time_frame' => 'text',
                            'role_in_purchase_process' => 'text',
                            'no_of_employees' => 'number',
                            'comments' => 'text',
                        );

                        foreach($registration_fields['questions'] as $question){

                            $field_name = $question['field_name'];

                            //dont do last name field because we have already done it
                            if($field_name != 'last_name'){
                                $required = $question['required'];

                                $field_identifier = $meeting_id.'-'.$i.'-'.$field_name;
                                $field_class = array('form-row-wide '.$field_identifier.' '.$i.'-'.$field_name.' '.$field_name);  

                                $label = str_replace('_',' ',$field_name);
                                $label = ucwords($label);


                                woocommerce_form_field( $field_identifier, array(
                                    'type'          => $field_type_helper[$field_name],
                                    'required'      => $required,   
                                    'class'         => $field_class,
                                    'label'         => __($label,'woocommerce-to-zoom-meetings'),   
                                ), $checkout->get_value( $field_identifier )); 
                            }
                        }
                    }

                    //now lets do our custom questions
                    if(is_array($registration_fields) && count($registration_fields['custom_questions'])>0 && $meeting_id != 'WooCommerceBookings'){

                        foreach($registration_fields['custom_questions'] as $question){

                            $field_title = $question['title'];
                            $field_title_translated = base64_encode($field_title);
                            $required = $question['required'];
                            $type = $question['type'];
                            $answers = $question['answers'];

                            //we want to replace the keys with the values
                            $new_answers = array();

                            foreach($answers as $key => $value){
                                $new_answers[$value] = $value;
                            }

                            $field_identifier = $meeting_id.'-'.$i.'-'.$field_title_translated;
                            $field_class = array('form-row-wide '.$field_identifier);  

                            // $label = str_replace('_',' ',$field_name);
                            // $label = ucwords($label);

                            //translate the field type
                            if($type == 'short'){
                                $field_type = 'text';
                            }

                            if($type == 'multiple'){
                                $field_type = 'radio';
                            }

                            if($type == 'single_radio'){
                                $field_type = 'radio';
                            }

                            if($type == 'single_dropdown'){
                                $field_type = 'select';
                            }

                            $select_types = array('multiple','single_radio','single_dropdown');

                            $temp_array = array(
                                'type'          => $field_type,
                                'required'      => $required,   
                                'class'         => $field_class,
                                'label'         => __($field_title,'woocommerce-to-zoom-meetings'),   
                            );

                            if(in_array($type,$select_types)){
                                $temp_array['options'] = $new_answers;  
                            }


                            woocommerce_form_field( $field_identifier,$temp_array , $checkout->get_value( $field_identifier )); 



                        }
                    }


                }
            
            echo '</div>';

        }
        
    }
}


/**
* 
*
*
* Save the data
*/
add_action( 'woocommerce_checkout_update_order_meta', 'woocommerce_to_zoom_meetings_save_checkout_fields' );

function woocommerce_to_zoom_meetings_save_checkout_fields( $order_id ) {
    

    global $woocommerce;
    $cart = $woocommerce->cart->get_cart();
    $cart_items_ids = array();
    
    //start the main loop
    foreach ( $cart as $item_key => $item_value ) {
    
        $cart_product_id = $item_value[ 'data' ]->get_id(); 
        $cart_product_qty = $item_value['quantity'];    

        $meeting_id = get_post_meta( $cart_product_id, 'zoom_meeting_selection', true );  

        $meeting_id = woocommerce_to_zoom_meetings_sanitize_meeting_id($meeting_id);

        if(strlen($meeting_id)>0){

            $registration_fields = woocommerce_to_zoom_meetings_get_meeting_registration_fields($meeting_id);

            for ($i = 1 ; $i <= $cart_product_qty; $i++){

                $order_meta = array();

                //do compulsory fields
                //first name
                $field_identifier = $meeting_id.'-'.$i.'-first_name';

                if ( !empty($_POST[$field_identifier]) ) {
                    $order_meta['first_name'] = sanitize_text_field($_POST[$field_identifier]); 
                }

                //last name
                $field_identifier = $meeting_id.'-'.$i.'-last_name';

                if ( !empty($_POST[$field_identifier]) ) {
                    $order_meta['last_name'] = sanitize_text_field($_POST[$field_identifier]); 
                }

                //email
                $field_identifier = $meeting_id.'-'.$i.'-email';

                if ( !empty($_POST[$field_identifier]) ) {
                    $order_meta['email'] = sanitize_text_field($_POST[$field_identifier]); 
                }

                //do standard fields
                if(is_array($registration_fields) && count($registration_fields['questions'])>0 && $meeting_id != 'WooCommerceBookings'){
                    foreach($registration_fields['questions'] as $question){
                        $field_name = $question['field_name'];
                        $field_identifier = $meeting_id.'-'.$i.'-'.$field_name;

                        if ( !empty($_POST[$field_identifier]) ) {
                            $order_meta[$field_name] = sanitize_text_field($_POST[$field_identifier]); 
                        }
                    }
                }

                //do custom fields
                if(is_array($registration_fields) && count($registration_fields['custom_questions'])>0  && $meeting_id != 'WooCommerceBookings'){

                    $temp_array = array();

                    foreach($registration_fields['custom_questions'] as $question){

                        $field_title = $question['title'];
                        $field_title_translated = base64_encode($field_title);

                        $field_identifier = $meeting_id.'-'.$i.'-'.$field_title_translated;

                        if ( !empty($_POST[$field_identifier]) ) {

                            array_push($temp_array,array(
                                'title' => $field_title,
                                'value' => sanitize_text_field($_POST[$field_identifier]),
                            ));

                        }

                    }
                    //now add to main array
                    $order_meta['custom_questions'] = $temp_array; 

                }

                update_post_meta( $order_id, 'zoom_meeting-'.$meeting_id.'-'.$i, $order_meta );


            }

        }

    }
    
}



/**
* 
*
*
* Make required fields actually required
*/
add_action( 'woocommerce_checkout_process', 'woocommerce_to_zoom_meetings_require_checkout_fields' );
function woocommerce_to_zoom_meetings_require_checkout_fields() {
    
    global $woocommerce;
    $cart = $woocommerce->cart->get_cart();
    $cart_items_ids = array();
    
    //start the main loop
    foreach ( $cart as $item_key => $item_value ) {
    
        $cart_product_id = $item_value[ 'data' ]->get_id(); 
        $cart_product_qty = $item_value['quantity'];    

        $meeting_id = get_post_meta( $cart_product_id, 'zoom_meeting_selection', true ); 
        
        $meeting_id = woocommerce_to_zoom_meetings_sanitize_meeting_id($meeting_id);
        
        if(strlen($meeting_id)>0){

            $registration_fields = woocommerce_to_zoom_meetings_get_meeting_registration_fields($meeting_id);

            for ($i = 1 ; $i <= $cart_product_qty; $i++){

                //do compulsory fields
                //first name
                $field_identifier = $meeting_id.'-'.$i.'-first_name';

                if ( ! $_POST[$field_identifier]) {
                    wc_add_notice( __( 'The first name meeting registration field is required.','woocommerce-to-zoom-meetings' ), 'error' );
                }

                //last name
                $field_identifier = $meeting_id.'-'.$i.'-last_name';

                if ( ! $_POST[$field_identifier]) {
                    wc_add_notice( __( 'This last name meeting registration field is required.','woocommerce-to-zoom-meetings' ), 'error' );
                }

                //email
                $field_identifier = $meeting_id.'-'.$i.'-email';

                if ( ! $_POST[$field_identifier]) {
                    wc_add_notice( __( 'This email meeting registration field is required.','woocommerce-to-zoom-meetings' ), 'error' );
                }

                //do standard fields
                if(is_array($registration_fields) && count($registration_fields['questions'])>0 && $meeting_id != 'WooCommerceBookings'){
                    foreach($registration_fields['questions'] as $question){
                        $field_name = $question['field_name'];
                        $required = $question['required'];
                        $field_identifier = $meeting_id.'-'.$i.'-'.$field_name;

                        if ( !$_POST[$field_identifier] && $required) {
                            wc_add_notice( __( 'This meeting registration field is required.','woocommerce-to-zoom-meetings' ), 'error' ); 
                        }
                    }
                }

                //do custom fields
                if(is_array($registration_fields) && count($registration_fields['custom_questions'])>0 && $meeting_id != 'WooCommerceBookings'){

                    foreach($registration_fields['custom_questions'] as $question){

                        $field_title = $question['title'];
                        $field_title_translated = base64_encode($field_title);
                        $required = $question['required'];
                        $field_identifier = $meeting_id.'-'.$i.'-'.$field_title_translated;

                        if ( !$_POST[$field_identifier] &&  $required) {
                            wc_add_notice( __( 'This meeting registration field is required.','woocommerce-to-zoom-meetings' ), 'error' );
                        }

                    }
                }

            }

        }

    }

}
  


?>