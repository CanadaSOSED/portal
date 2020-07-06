<?php
/**
* 
*
*
* Add custom column to order page
*/
add_filter( 'manage_edit-product_columns', 'woocommerce_to_zoom_meetings_add_column_to_product_page' );
 
function woocommerce_to_zoom_meetings_add_column_to_product_page( $columns ){
   $columns['zoom_meeting'] = __( 'Zoom Meeting ID', 'woocommerce-to-zoom-meetings' );
   return $columns;
}


/**
* 
*
*
* Add custom column to order page - content
*/
add_action( 'manage_product_posts_custom_column', 'woocommerce_to_zoom_meetings_add_column_content_to_product_page', 10, 2 );
 
function woocommerce_to_zoom_meetings_add_column_content_to_product_page( $column, $product_id ){
    if ( $column == 'zoom_meeting' ) {
        // $product = wc_get_product( $product_id );

        $zoom_meeting_selection = get_post_meta($product_id,'zoom_meeting_selection',true);

        if(strlen($zoom_meeting_selection)>0){
            echo '<a href="https://zoom.us/meeting/'.$zoom_meeting_selection.'" target="_blank">'.$zoom_meeting_selection.'</a>';
        }

        
    }
}
?>