<?php
/**
 * WCFMu plugin view
 *
 * WCFM Listings view
 *
 * @author 		WC Lovers
 * @package 	wcfm/views
 * @version   2.4.6
 */
 
global $WCFM;

$wcfm_capability_options = apply_filters( 'wcfm_capability_options_rules', (array) get_option( 'wcfm_capability_options' ) );
$wc_frontend_manager_associate_listings = ( isset( $wcfm_capability_options['associate_listings'] ) ) ? $wcfm_capability_options['associate_listings'] : 'no';
if( 'yes' == $wc_frontend_manager_associate_listings ) {
	wcfm_restriction_message_show( "Listings" );
	return;
}

$post_a_job = get_permalink ( get_option( 'job_manager_submit_job_form_page_id' ) );
?>

<div class="collapse wcfm-collapse" id="wcfm_listings_listing">

  <div class="wcfm-page-headig">
		<span class="fa fa-briefcase"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Listings', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>
		<?php do_action( 'before_wcfm_listings' ); ?>
		<h2><?php _e('Job Listings', 'wc-frontend-manager' ); ?></h2>
		<?php
		if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
			if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
				if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
					?>
					<span class="wcfm_screen_manager_dummy text_tip" data-tip="<?php wcfmu_feature_help_text_show( 'Screen Manager', false, true ); ?>"><span class="fa fa-television"></span></span>
					<?php
				}
			} else {
				?>
				<a class="wcfm_screen_manager text_tip" href="#" data-screen="listing" data-tip="<?php _e( 'Screen Manager', 'wc-frontend-manager' ); ?>"><span class="fa fa-television"></span></a>
				<?php
			}
			?>
			<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=job_listing'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fa fa-wordpress"></span></a>
			<?php
		}
		if( $has_new = apply_filters( 'wcfm_add_new_listing_sub_menu', true ) ) {
			echo '<a target="_blank" id="add_new_listing_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.$post_a_job.'" data-tip="' . __('Add New Listing', 'wc-frontend-manager') . '"><span class="fa fa-briefcase"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager') . '</span></a>';
		}
		?>
		<div class="wcfm-clearfix"></div>
		<div class="wcfm-container">
			<div id="wcfm_listings_listing_expander" class="wcfm-content">
				<table id="wcfm-listings" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php _e( 'Listing', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Status', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Filled?', 'wp-job-manager' ); ?></th>
							<th><?php _e( 'Date Posted', 'wp-job-manager' ); ?></th>
							<th><?php _e( 'Listing Expires', 'wp-job-manager' ); ?></th>
							<th><?php _e( 'Action', 'wc-frontend-manager' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Listing', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Status', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Filled?', 'wp-job-manager' ); ?></th>
							<th><?php _e( 'Date Posted', 'wp-job-manager' ); ?></th>
							<th><?php _e( 'Listing Expires', 'wp-job-manager' ); ?></th>
							<th><?php _e( 'Action', 'wc-frontend-manager' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_listings' );
		?>
	</div>
</div>