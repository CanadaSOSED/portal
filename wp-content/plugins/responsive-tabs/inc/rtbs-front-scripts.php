<?php

/* Enqueues front scripts. */
add_action( 'wp_enqueue_scripts', 'add_rtbs_scripts', 99 );
function add_rtbs_scripts() {

  /* Front end CSS. */
  wp_enqueue_style( 'rtbs', plugins_url('css/rtbs_style.min.css', __FILE__));
  /* Front end JS. */
  wp_enqueue_script( 'rtbs', plugins_url('js/rtbs.min.js', __FILE__), array( 'jquery' ));

}

?>