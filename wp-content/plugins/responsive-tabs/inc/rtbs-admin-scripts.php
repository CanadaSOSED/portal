<?php

/* Enqueues admin scripts. */
add_action( 'admin_enqueue_scripts', 'add_admin_rtbs_style' );
function add_admin_rtbs_style() {

  /* Gets the post type. */
  global $post_type;

  if( 'rtbs_tabs' == $post_type ) {

    /* CSS for metaboxes. */
    wp_enqueue_style( 'rtbs_dmb_styles', plugins_url('dmb/dmb.min.css', __FILE__));
    /* CSS for preview.s */
    wp_enqueue_style( 'rtbs_styles', plugins_url('css/rtbs_style.min.css', __FILE__));
    /* Others. */
    wp_enqueue_style( 'wp-color-picker' );

    /* JS for metaboxes. */
    wp_enqueue_script( 'rtbs_admin_js', plugins_url('dmb/dmb.min.js', __FILE__), array( 'jquery', 'thickbox', 'wp-color-picker' ));
    wp_enqueue_script( 'rtbs_js', plugins_url('js/rtbs.min.js', __FILE__), array( 'jquery' ));

    /* Localizes string for JS file. */
    wp_localize_script( 'rtbs_admin_js', 'objectL10n', array(
      'untitled' => __( 'Untitled', RTBS_TXTDM ),
      'noTabNotice' => __( 'Add at least <strong>1</strong> tab to preview this tab set.', RTBS_TXTDM ),
      'previewAccuracy' => __( 'This is only a preview, shortcodes used in the fields will not be rendered and results may vary depending on your container\'s width.', RTBS_TXTDM )
    ));
    wp_enqueue_style( 'thickbox' );
    
  }

}

?>