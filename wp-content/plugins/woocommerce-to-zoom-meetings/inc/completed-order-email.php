<?php
    add_action( 'woocommerce_email_order_details', 'woocommerce_to_zoom_meetings_completed_order_email', 20, 4 );
    function woocommerce_to_zoom_meetings_completed_order_email( $order, $sent_to_admin = false, $plain_text = false, $email = '' ) {

        //only run if enabled in the plugin settings
        $completed_order_email_enabled = get_option('wc_settings_zoom_meetings_enable_completed_order_email');

        if(isset($completed_order_email_enabled) && $completed_order_email_enabled == 'yes' && $email->get_title() == 'Completed order'){

            //start output
            $html = '';

            $order_id = $order->get_id();

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
    }

    
?>