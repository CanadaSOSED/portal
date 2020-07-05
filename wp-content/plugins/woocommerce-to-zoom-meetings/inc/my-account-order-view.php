<?php
/**
* 
*
*
* Adds data to the order view on the my account page of woocommerce
*/
add_action( 'woocommerce_view_order', 'woocommerce_to_zoom_meetings_my_account_view_order', 20 );
function woocommerce_to_zoom_meetings_my_account_view_order($order_id){
    
    //start output
    $html = '';

    if(get_post_meta($order_id,'zoom_meetings_registrant_ids',false)){

        $registrants = get_post_meta($order_id,'zoom_meetings_registrant_ids',true);
        
        $html .= '<h2>'.__('Meeting registrants','woocommerce-to-zoom-meetings').'</h2>';

        $html .= '<ul style="list-style: inherit; margin-bottom: 40px;">';

        foreach($registrants as $key => $value){
            $html .= '<li data="'.$key.'">';
            // $html .= '<a target="_blank" href="'.$value['join_url'].'"><strong>'.$value['first_name'].' '.$value['last_name'].'</strong> ('.$value['email'].')</a>';
                $html .= '<strong>'.$value['first_name'].' '.$value['last_name'].'</strong> ('.$value['email'].') - <a target="_blank" href="'.$value['join_url'].'">'.__('Join link','woocommerce-to-zoom-meetings').'</a>';
            $html .= '</li>';
        }
        $html .= '</ul>';

    }

    echo $html;

}

?>