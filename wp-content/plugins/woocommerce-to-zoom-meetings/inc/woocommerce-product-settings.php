<?php
/**
* 
*
*
* Add new tab to WooCommerce Product Page
*/
add_filter( 'woocommerce_product_data_tabs', 'woocommerce_to_zoom_meetings_add_product_tab' );

function woocommerce_to_zoom_meetings_add_product_tab( $product_data_tabs ) {

	$product_data_tabs['zoom-meetings'] = array(
		'label' => __( 'Meeting Selection', 'woocommerce-to-zoom-meetings' ),
		'target' => 'zoom_meeting_selection',
        'class'  => array( 'show_if_virtual'),
	);
    
	return $product_data_tabs;
}


/**
* 
*
*
* Gets upcoming meetings from Zoom
*/
function woocommerce_to_zoom_meetings_get_upcoming_meetings() {

    $transient_name = 'zoom_upcoming_meetings';
    $transient = get_transient($transient_name);

    if ($transient != false){
        return $transient;
    } else {

        //setup return variable
        //add an initial selection for WooCommerce Bookings
        $return_array = array('' => '','WooCommerce Bookings' => 'WooCommerce Bookings');

        //only continue if authenticated
        if(get_option('wc_settings_zoom_refresh_token')){

            // $url = woocommerce_to_zoom_meetings_get_api_base().'users/me/meetings?page_size=300&type=upcoming'; //we are going to remove upcoming because it affected a customer
			$url = woocommerce_to_zoom_meetings_get_api_base().apply_filters( 'woocommerce_to_zoom_meetings_get_meetings_args', 'users/me/meetings?page_size=300' );

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

                    $date = date_i18n(get_option( 'date_format' ),strtotime($meeting['start_time']));

                    $return_array[$meeting['id']] = $meeting['topic'].' ('.$date.')';    
                }
                
                //set the transient for 10 minutes
                set_transient($transient_name,$return_array, 60*10); 

            }
        }

        return $return_array;
    }
}


/**
* 
*
*
* Add new tab to WooCommerce Product Page
*/
add_action( 'woocommerce_product_data_panels', 'woocommerce_to_zoom_meetings_tab_content' );
function woocommerce_to_zoom_meetings_tab_content() {

	?>
	<!-- id below must match target registered in above add_my_custom_product_data_tab function -->
	<div id="zoom_meeting_selection" class="panel woocommerce_options_panel">
		<?php
    


		woocommerce_wp_select(array( 
			'id'            => 'zoom_meeting_selection', 
			'wrapper_class' => 'show_if_virtual', 
			'label'         => __( 'Please select a meeting', 'woocommerce-to-zoom-meetings' ),
            'description'   => __( '', 'woocommerce-to-zoom-meetings' ), 
			'options' => woocommerce_to_zoom_meetings_get_upcoming_meetings()
		));

    
		?>
	</div>



	<?php
}

/**
* 
*
*
* Add new tab to WooCommerce Product Page - Variations
*/
add_action( 'woocommerce_product_after_variable_attributes', 'woocommerce_to_zoom_meetings_tab_content_variations', 10, 3 );
function woocommerce_to_zoom_meetings_tab_content_variations($loop, $variation_data, $variation) {

	?>
	<!-- id below must match target registered in above add_my_custom_product_data_tab function -->
	<div id="zoom_meeting_selection" class="panel woocommerce_options_panel">
		<?php
    


		woocommerce_wp_select(array( 
			'id'            => 'zoom_meeting_selection['.$variation->ID.']', 
			'wrapper_class' => 'show_if_variation_virtual', 
			'label'         => __( 'Please select a meeting', 'woocommerce-to-zoom-meetings' ),
            'description'   => __( '', 'woocommerce-to-zoom-meetings' ), 
            'value'       => get_post_meta( $variation->ID, 'zoom_meeting_selection', true ),
			'options' => woocommerce_to_zoom_meetings_get_upcoming_meetings()
		));

    
		?>
	</div>

	<?php
}



/**
* 
*
*
* Save the data from the tab
*/
add_action('woocommerce_process_product_meta', 'woocommerce_to_zoom_meetings_save_tab_settings');
function woocommerce_to_zoom_meetings_save_tab_settings( $post_id ) {

    if ( isset( $_POST['zoom_meeting_selection'] ) ) {
			update_post_meta( $post_id, 'zoom_meeting_selection', wc_clean( $_POST['zoom_meeting_selection'] ) );
	}

}
/**
* 
*
*
* Save the data from the tab - Variations
*/
add_action( 'woocommerce_save_product_variation', 'woocommerce_to_zoom_meetings_save_tab_settings_variations', 10, 2 );
function woocommerce_to_zoom_meetings_save_tab_settings_variations( $post_id ) {

	// Select
	$select = $_POST['zoom_meeting_selection'][ $post_id ];
	if( ! empty( $select ) ) {
		update_post_meta( $post_id, 'zoom_meeting_selection', esc_attr( $select ) );
	}
	
}


?>