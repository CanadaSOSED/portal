<?php

add_action( 'wp_ajax_zoom_meetings_clear_transients', 'zoom_meetings_clear_transients' );
function zoom_meetings_clear_transients(){

    //clear the transients beginning with: zoom_meetings_registration_fields_
    global $wpdb; 
    $sql = "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_zoom_meetings_registration_fields_%' or option_name like '_transient_timeout_zoom_meetings_registration_fields_%'";
    $wpdb->query($sql);  

    echo 'SUCCESS';

    wp_die();

}

?>