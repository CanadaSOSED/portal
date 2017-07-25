<?php
/**
 * WCFM plugin view
 *
 * WCFM Bookings Dashboard View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   2.3.5
 */

global $WCFM;

if( !current_user_can( 'manage_bookings' ) ) {
	wcfm_restriction_message_show( "Bookings" );
	return;
}

?>

<div class="collapse wcfm-collapse" id="">
  <div class="wcfm-page-headig">
		<span class="fa fa-calendar-check-o"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Bookings', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>
		<?php do_action( 'before_wcfm_wcvendors_bookings_dashboard' ); ?>
		
		<div class="wcfm-container-box">
		  <?php if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) { ?>
				<div>
					<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?>
						<a class="wcfm_bookings_gloabl_settings text_tip" href="<?php echo get_wcfm_bookings_settings_url(); ?>" data-tip="<?php _e( 'Global Availability', 'woocommerce-bookings' ); ?>"><span class="fa fa-cog"></span></a>
					<?php } else { ?>
						<a class="wcfm_bookings_gloabl_settings text_tip" href="#" onClick="return false;" data-tip="<?php wcfmu_feature_help_text_show( 'Global Availability', false, true ); ?>"><span class="fa fa-cog"></span></a>
					<?php } ?>
				</div>
				<div class="wcfm_clearfix"></div>
			<?php } ?>
		  
		  <div class="wcfm-container">
				<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?>
					<a href="<?php echo get_wcfm_create_bookings_url(); ?>">
				<?php } ?>
					<div id="wcfm_bookings_product_add_expander" class="wcfm-content">
						<div class="booking_dashboard_section_icon"><span class="fa fa-calendar-plus-o"></span></div>
						<div class="booking_dashboard_section_label">
							<h2 title="<?php if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) { wcfmu_feature_help_text_show( 'Manual Create Booking', false, true ); } ?>"><?php _e( 'Create Booking', 'wc-frontend-manager' ); ?></h2>
						</div>
					</div>
				<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?></a><?php } ?>
			</div>
			
			<div class="wcfm-container">
			  <?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?>
					<a href="<?php echo get_wcfm_bookings_resources_url(); ?>">
				<?php } ?>
					<div id="wcfm_bookings_resources_expander" class="wcfm-content">
						<div class="booking_dashboard_section_icon"><span class="fa fa-briefcase"></span></div>
						<div class="booking_dashboard_section_label">
							<h2 title="<?php if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) { wcfmu_feature_help_text_show( 'Manage Resources', false, true ); } ?>"><?php _e( 'Manage Resources', 'wc-frontend-manager' ); ?></h2>
						</div>
					</div>
			  <?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?></a><?php } ?>
			</div>
			
		</div>
		
		
		<div class="wcfm-container-box">
		  <div class="wcfm-container wcfm-container-single">
			  <a href="<?php echo get_wcfm_edit_product_url(); ?>">
					<div id="wcfm_bookings_product_add_expander" class="wcfm-content">
						<div class="booking_dashboard_section_icon"><span class="fa fa-edit"></span></div>
						<div class="booking_dashboard_section_label"><h2><?php _e( 'Create Bookable', 'wc-frontend-manager' ); ?></h2></div>
					</div>
				</a>
			</div>
		</div>
			
		
		<div class="wcfm-container-box">
		
		  <div class="wcfm-container">
		  	<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?>
					<a href="<?php echo get_wcfm_bookings_url(); ?>">
				<?php } ?>
					<div id="wcfm_bookings_list_expander" class="wcfm-content">
						<div class="booking_dashboard_section_icon"><span class="fa fa-calendar"></span></div>
						<div class="booking_dashboard_section_label">
							<h2 title="<?php if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) { wcfmu_feature_help_text_show( 'Bookings List', false, true ); } ?>"><?php _e( 'Bookings List', 'wc-frontend-manager' ); ?></h2>
						</div>
					</div>
		    <?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?></a><?php } ?>
		  </div>
			
			<div class="wcfm-container">
			  <?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?>
					<a href="<?php echo get_wcfm_bookings_calendar_url(); ?>">
				<?php } ?>
					<div id="wcfm_bookings_calendar_expander" class="wcfm-content">
						<div class="booking_dashboard_section_icon"><span class="fa fa-calendar-o"></span></div>
						<div class="booking_dashboard_section_label">
							<h2 title="<?php if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) { wcfmu_feature_help_text_show( 'Bookings Calendar', false, true ); } ?>"><?php _e( 'Bookings Calendar', 'wc-frontend-manager' ); ?></h2>
						</div>
					</div>
			  <?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?></a><?php } ?>
			</div>
			
		</div>
		<div class="wcfm_clearfix"></div><br />
		
		<?php do_action( 'after_wcfm_wcvendors_bookings_dashboard' ); ?>
	</div>
</div>