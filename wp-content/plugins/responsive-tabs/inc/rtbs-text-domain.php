<?php 

/* Loads plugin's text domain. */
add_action( 'plugins_loaded', 'rtbs_load_plugin_textdomain' );
function rtbs_load_plugin_textdomain() {
  load_plugin_textdomain( RTBS_TXTDM, FALSE, RTBS_PATH . 'lang/' );
}

?>