<?php
/**
 * html code for admin sidebar
 */
?>
<div class="cev_admin_sidebar">
	<div class="cev_admin_sidebar_inner">		
			
		<div class="cev_sidebar__section">
			<h3 class="cev-top-border">More plugins by zorem</h3>
			<?php
				$response = wp_remote_get('https://www.zorem.com/wp-json/pluginlist/v1' );
				if ( is_array( $response ) ) {
				$plugin_list = json_decode($response['body']);		
			?>	
			<ul>
				<?php foreach($plugin_list as $plugin){ 
					if( 'Customer Email verification for WooCommerce' != $plugin->title ) { 
				?>
					<li><img class="cev_plugin_thumbnail" src="<?php echo $plugin->image_url; ?>"><a class="cev_plugin_url" href="<?php echo $plugin->url; ?>" target="_blank"><?php echo $plugin->title; ?></a></li>
				<?php }
				}?>
			</ul>  
			<?php } ?>	
		</div>
	</div>
</div>