<?php 

/* Handles shortcode column display. */
add_action( 'manage_rtbs_tabs_posts_custom_column' , 'rtbs_tabs_custom_columns', 10, 2 );
function rtbs_tabs_custom_columns( $column, $post_id ) {
  switch ( $column ) {
    case 'dk_shortcode' :
      global $post;
      $slug = '' ;
      $slug = $post->post_name;
      $shortcode = '<span style="display:inline-block;border:solid 2px lightgray; background:white; padding:0 8px; font-size:13px; line-height:25px; vertical-align:middle;">[rtbs name="'.$slug.'"]</span>';
      echo $shortcode;
      break;
  }
}


/* Adds the shortcode column in admin. */
add_filter( 'manage_rtbs_tabs_posts_columns' , 'add_rtbs_tabs_columns' );
function add_rtbs_tabs_columns( $columns ) {
  return array_merge( $columns, array('dk_shortcode' => 'Shortcode') );
}

?>