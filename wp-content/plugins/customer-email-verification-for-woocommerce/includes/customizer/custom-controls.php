<?php
/**
 * Skyrocket Customizer Custom Controls
 *
 */
if ( class_exists( 'WP_Customize_Control' ) ) {	
		
	class WP_Customize_cev_codeinfoblock_Control extends WP_Customize_Control {		

		public function render_content() {
			?>
			<label>
				<h3 class="customize-control-title"><?php _e( $this->label, 'customer-email-verification-for-woocommerce' ); ?></h3>
				<?php if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
				<?php endif; ?>
			</label>
			<?php
		}
	}	
}
