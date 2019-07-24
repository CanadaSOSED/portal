<?php

/* Checks for PRO version. */
add_action( 'admin_init', 'rtbs_free_pro_check' );
function rtbs_free_pro_check() {

  if (is_plugin_active('responsive-tabs-pro/rtbs_pro.php')) {

    /* Shows admin notice. */
    add_action('admin_notices', 'rtbs_free_pro_notice');
    function rtbs_free_pro_notice(){
      echo '<div class="updated"><p><span class="dashicons dashicons-unlock"></span> Responsive Tabs <strong>PRO</strong> was activated and is now taking over the Free version.</p></div>';
    }
    
    /* Deactivates free version. */
    deactivate_plugins( RTBS_PATH.'/rtbs.php' );

  }

}

?>