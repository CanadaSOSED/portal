<?php
/**
 * html code for customize tab
 */
?>
<section id="cev_content_customize" class="cev_tab_section">
	<div class="cev_tab_inner_container">		
		<table class="form-table heading-table">
			<tbody>
				<tr valign="top">
					<td>
						<h3 style=""><?php _e( 'Customize the verification email', 'customer-email-verification-for-woocommerce' ); ?></h3>
					</td>
				</tr>
			</tbody>
		</table><table class="form-table">
			<tbody>
				<tr valign="top">						
					<td class="button-column">
						<a href="<?php echo cev_initialise_customizer_settings::get_customizer_url('cev_controls_section'); ?>" class="button-primary cev-btn-large"><?php _e( 'Launch Customizer', 'customer-email-verification-for-woocommerce' ); ?> <span class="dashicons dashicons-welcome-view-site"></span></a>
					</td>
			</tbody>	
		</table>		
		<form method="post" id="cev_frontend_messages_form" action="" enctype="multipart/form-data">
			<?php #nonce?>
					
			<table class="form-table heading-table">
				<tbody>
					<tr valign="top">
						<td>
							<h3 style=""><?php _e( 'Customize frontend messages', 'customer-email-verification-for-woocommerce' ); ?></h3>
						</td>
					</tr>
				</tbody>
			</table>
			<?php $this->get_html( $this->get_cev_frontend_messages_data() );?>	
			<table class="form-table">
				<tbody>
					<tr valign="top">						
						<td class="button-column">
							<div class="submit">								
								<button name="save" class="button-primary cev_frontend_messages_save" type="submit" value="Save changes"><?php _e( 'Save Changes', 'customer-email-verification-for-woocommerce' ); ?></button>
								<div class="spinner"></div>
								<div class="success_msg" style="display:none;"><?php _e( 'Data saved successfully.', 'customer-email-verification-for-woocommerce' ); ?></div>							
								<?php wp_nonce_field( 'cev_frontend_messages_form_nonce', 'cev_frontend_messages_form_nonce' );?>
								<input type="hidden" name="action" value="cev_frontend_messages_form_update">
							</div>	
						</td>
					</tr>
				</tbody>
			</table>									
		</form>
	</div>	
	<?php include 'admin_sidebar.php';?>	
</section>