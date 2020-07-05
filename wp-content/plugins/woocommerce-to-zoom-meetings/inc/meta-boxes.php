<?php
/**
* 
*
*
* Add metabox to order page
*/
add_action( 'add_meta_boxes', 'woocommerce_to_zoom_meetings_add_meta_boxes' );

function woocommerce_to_zoom_meetings_add_meta_boxes(){
    add_meta_box( 'zoom_meeting_sync', __('Zoom Meeting Registrants','woocommerce-to-zoom-meetings'), 'woocommerce_to_zoom_meetings_meta_box_order', 'shop_order', 'side', 'core' );
}
/**
* 
*
*
* Add metabox content order
*/
function woocommerce_to_zoom_meetings_meta_box_order(){
    
    global $post;

    $order_id = $post->ID;

    if(get_post_meta($order_id,'zoom_meetings_registrant_ids',false)){

        $registrants = get_post_meta($order_id,'zoom_meetings_registrant_ids',true);

        echo '<ul style="list-style: inherit; margin-left: 20px;">';

        foreach($registrants as $key => $value){
            echo '<li data="'.$key.'">';
            echo '<a target="_blank" href="'.$value['join_url'].'"><strong>'.$value['first_name'].' '.$value['last_name'].'</strong> ('.$value['email'].')</a>';
            echo '</li>';
        }
        echo '</ul>';

    } else {
        //show create registrants button
        echo '<button id="create-zoom-meetings-registrants" data="'.$order_id.'" class="button button-primary">'.__('Create Registrants','woocommerce-to-zoom-meetings').'</button>';
    }

}


?>