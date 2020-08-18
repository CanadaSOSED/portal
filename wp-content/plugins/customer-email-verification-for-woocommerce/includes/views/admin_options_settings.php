<?php
/**
 * html code for settings tab
 */
?>
<section id="cev_content_settings" class="cev_tab_section">
	<div class="cev_tab_inner_container">
		<form method="post" id="cev_settings_form" action="" enctype="multipart/form-data">
			<?php #nonce?>
					
			<table class="form-table heading-table">
				<tbody>
					<tr valign="top">
						<td>
							<h3 style=""><?php _e( 'General Settings', 'customer-email-verification-for-woocommerce' ); ?></h3>
						</td>
					</tr>
				</tbody>
			</table>
			<?php $this->get_html( $this->get_cev_settings_data() );?>	
			<table class="form-table">
				<tbody>
					<tr valign="top">						
						<td class="button-column">
							<div class="submit">								
								<button name="save" class="button-primary cev_settings_save" type="submit" value="Save changes"><?php _e( 'Save Changes', 'customer-email-verification-for-woocommerce' ); ?></button>
								<div class="spinner"></div>
								<div class="success_msg" style="display:none;"><?php _e( 'Data saved successfully.', 'customer-email-verification-for-woocommerce' ); ?></div>							
								<?php wp_nonce_field( 'cev_settings_form_nonce', 'cev_settings_form_nonce' );?>
								<input type="hidden" name="action" value="cev_settings_form_update">
							</div>	
						</td>
					</tr>
				</tbody>
			</table>									
		</form>		
	</div>	
	<?php include 'admin_sidebar.php';?>	
</section>