<?php
add_shortcode( 'TABS_R', 'TABS_R_ShortCode' );
function TABS_R_ShortCode( $Id ) {
	ob_start();	
	if(!isset($Id['id'])) 
	 {
		$WPSM_Tabs_ID = "";
	 } 
	else 
	{
		$WPSM_Tabs_ID = $Id['id'];
	}
	require("content.php"); 
	
	wp_reset_query();
    return ob_get_clean();
}
?>