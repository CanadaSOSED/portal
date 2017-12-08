<?php
/**
 * WCFM plugin view
 *
 * WCFM Settings View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   1.1.6
 */

global $WCFM;

$wcfm_is_allow_manage_settings = apply_filters( 'wcfm_is_allow_manage_settings', true );
if( !$wcfm_is_allow_manage_settings ) {
	wcfm_restriction_message_show( "Settings" );
	return;
}

$wcfm_options = (array) get_option( 'wcfm_options' );

$is_menu_disabled = isset( $wcfm_options['menu_disabled'] ) ? $wcfm_options['menu_disabled'] : 'no';
$is_headpanel_disabled = isset( $wcfm_options['headpanel_disabled'] ) ? $wcfm_options['headpanel_disabled'] : 'no';
$ultimate_notice_disabled = isset( $wcfm_options['ultimate_notice_disabled'] ) ? $wcfm_options['ultimate_notice_disabled'] : 'no';
$noloader = isset( $wcfm_options['noloader'] ) ? $wcfm_options['noloader'] : 'no';
$logo = ! empty( get_option( 'wcfm_site_logo' ) ) ? get_option( 'wcfm_site_logo' ) : '';
$logo_image_url = wp_get_attachment_image_src( $logo, 'full' );

if ( !empty( $logo_image_url ) ) {
	$logo_image_url = $logo_image_url[0];
}

$is_marketplece = wcfm_is_marketplace();
?>

<div class="collapse wcfm-collapse" id="">
  <div class="wcfm-page-headig">
		<span class="fa fa-cogs"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Settings', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>
		<?php do_action( 'before_wcfm_settings' ); ?>
		<h2><?php _e('WCfM Settings', 'wc-frontend-manager' ); ?></h2>
		
		<?php if( wcfm_is_booking() ) { ?>
			<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?>
				<a class="wcfm_gloabl_settings text_tip" href="<?php echo get_wcfm_bookings_settings_url(); ?>" data-tip="<?php _e( 'Bookings Global Settings', 'wc-frontend-manager' ); ?>"><span class="fa fa-cog"></span></a>
			<?php } else { ?>
				<a class="wcfm_gloabl_settings text_tip" href="#" onClick="return false;" data-tip="<?php wcfmu_feature_help_text_show( 'Bookings Global Settings', false, true ); ?>"><span class="fa fa-cog"></span></a>
			<?php } ?>
		<?php } ?>
		
		<?php if( WCFM_Dependencies::wcfmu_plugin_active_check() ) { ?>
			<?php if( WCFMu_Dependencies::wcfm_wc_appointments_active_check() ) { ?>
		    <a class="wcfm_gloabl_settings text_tip" href="<?php echo get_wcfm_appointment_settings_url(); ?>" data-tip="<?php _e( 'Appointments Global Settings', 'wc-frontend-manager' ); ?>"><span class="fa fa-cog"></span></a>
		  <?php } ?>
		<?php } ?>
		
		<?php 
		if( $wcfm_is_allow_capability_controller = apply_filters( 'wcfm_is_allow_capability_controller', true ) ) {
			echo '<a id="wcfm_capability_settings" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_capability_url().'" data-tip="' . __('Capability Controller', 'wc-frontend-manager') . '"><span class="fa fa-user-times"></span><span class="text">' . __( 'Capabiity', 'wc-frontend-manager') . '</span></a>';
		}
		?>
		<div class="wcfm_clearfix"></div>
		
		<form id="wcfm_settings_form" class="wcfm">
	
			<?php do_action( 'begin_wcfm_settings_form' ); ?>
			
			<!-- collapsible -->
			<div class="page_collapsible" id="wcfm_settings_form_style_head">
				<label class="fa fa-image"></label>
				<?php _e('Style', 'wc-frontend-manager'); ?><span></span>
			</div>
			<div class="wcfm-container">
				<div id="wcfm_settings_form_style_expander" class="wcfm-content">
					<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_style', array(
																																															"wcfm_logo" => array('label' => __('Logo', 'wc-frontend-manager') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'prwidth' => 150, 'value' => $logo_image_url ),
																																															"menu_disabled" => array('label' => __('Disabled WCFM Menu', 'wc-frontend-manager') , 'name' => 'menu_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_menu_disabled),
																																															"headpanel_disabled" => array('label' => __('Disabled WCFM Header Panel', 'wc-frontend-manager') , 'name' => 'headpanel_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_headpanel_disabled),
																																															"ultimate_notice_disabled" => array('label' => __('Disabled Ultimate Notice', 'wc-frontend-manager') , 'name' => 'ultimate_notice_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $ultimate_notice_disabled),
																																															//"noloader" => array('label' => __('Disabled WCFM Loader', 'wc-frontend-manager') , 'name' => 'noloader','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $noloader),
																																															) ) );
						$color_options = $WCFM->wcfm_color_setting_options();
						$color_options_array = array();
		
						foreach( $color_options as $color_option_key => $color_option ) {
							$color_options_array[$color_option['name']] = array( 'label' => $color_option['label'] , 'type' => 'colorpicker', 'class' => 'wcfm-text wcfm_ele colorpicker', 'label_class' => 'wcfm_title wcfm_ele', 'value' => ( isset($wcfm_options[$color_option['name']]) ) ? $wcfm_options[$color_option['name']] : $color_option['default'] );
						}
						$WCFM->wcfm_fields->wcfm_generate_form_field( $color_options_array );
					?>
				</div>
			</div>
			<div class="wcfm_clearfix"></div>
			<!-- end collapsible -->
			
			<!-- collapsible -->
			<div class="page_collapsible" id="wcfm_settings_form_pages_head">
				<label class="fa fa-newspaper-o"></label>
				<?php _e('WCFM Pages', 'wc-frontend-manager'); ?><span></span>
			</div>
			<div class="wcfm-container">
				<div id="wcfm_settings_form_pages_expander" class="wcfm-content">
					<?php
						$wcfm_page_options = get_option( 'wcfm_page_options' );
						$pages = get_pages(); 
						$pages_array = array();
						$woocommerce_pages = array ( wc_get_page_id('shop'), wc_get_page_id('cart'), wc_get_page_id('checkout'), wc_get_page_id('myaccount'));
						foreach ( $pages as $page ) {
							if(!in_array($page->ID, $woocommerce_pages)) {
								$pages_array[$page->ID] = $page->post_title;
							}
						}
						
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_pages', array(
																																															"wc_frontend_manager_page_id" => array( 'label' => __('Dashboard', 'wc-frontend-manager'), 'type' => 'select', 'name' => 'wcfm_page_options[wc_frontend_manager_page_id]', 'options' => $pages_array, 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $wcfm_page_options['wc_frontend_manager_page_id'], 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'This page should have shortcode - wc_frontend_manager', 'wc-frontend-manager') )
																																															) ) );
					
						if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
							if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
								wcfmu_feature_help_text_show( __( 'WCFM Endpoints', 'wc-frontend-manager' ) );
							}
						} else {
							do_action( 'wcfm_settings_endpoints' );
						}
					?>
				</div>
			</div>
			<div class="wcfm_clearfix"></div>
			<!-- end collapsible -->
			
			<?php do_action( 'end_wcfm_settings', $wcfm_options ); ?>
			
			<div class="wcfm-message" tabindex="-1"></div>
			
			<div id="wcfm_settings_submit">
				<input type="submit" name="save-data" value="<?php _e( 'Save', 'wc-frontend-manager' ); ?>" id="wcfm_settings_save_button" class="wcfm_submit_button" />
			</div>
		</form>	
		<?php
		do_action( 'after_wcfm_settings' );
		?>
	</div>
</div>