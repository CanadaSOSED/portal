<?php
/**
 * The template for displaying archive vendor info
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/archive_vendor_info.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */
 
global $WCMp;
$vendor_hide_address = get_user_meta($vendor_id,'_vendor_hide_address', true);
$vendor_hide_phone = get_user_meta($vendor_id,'_vendor_hide_phone', true);
$vendor_hide_email = get_user_meta($vendor_id,'_vendor_hide_email', true);
?>
<div class="vendor_description_background" style="background: url(<?php echo $banner; ?>) no-repeat; width: 100%; height: 245px; color: white; margin-bottom: 10px; background-size: 100% 100%;">
	<div class="vendor_description">
		<div class="vendor_img_add">
			<div class="img_div"><img height="400" width="200" src=<?php echo $profile;?> /></div>
			<div class="vendor_address">
				<?php if(!empty($location) && $vendor_hide_address != 'Enable') { ?><p><img height="25" width="25" src=<?php echo $WCMp->plugin_url . 'assets/images/location_pin.png';?> /><label><?php echo apply_filters( 'vendor_shop_page_location', $location, $vendor_id ); ?></label></p><?php } ?>
				<?php if(!empty($mobile) && $vendor_hide_phone != 'Enable') { ?><p><img height="25" width="25" src=<?php echo $WCMp->plugin_url . 'assets/images/mobile-phone.png';?> /><label><?php echo apply_filters( 'vendor_shop_page_contact', $mobile, $vendor_id ); ?></label></p><?php } ?>
				<?php if(!empty($email) && $vendor_hide_email != 'Enable') { ?><p><img height="25" width="25" src=<?php echo $WCMp->plugin_url . 'assets/images/email_envelope_message.png';?> /><label><?php echo apply_filters( 'vendor_shop_page_email', $email, $vendor_id ); ?></label></p><?php } ?>
				<?php  $is_vendor_add_external_url_field = apply_filters('is_vendor_add_external_url_field', true);
					if ( $WCMp->vendor_caps->vendor_capabilities_settings('is_vendor_add_external_url') && $is_vendor_add_external_url_field ) {
						$external_store_url = get_user_meta( $vendor_id, '_vendor_external_store_url', true );
						$external_store_label = get_user_meta( $vendor_id, '_vendor_external_store_label', true );
						if( empty($external_store_label) ) $external_store_label = __('External Store URL', $WCMp->text_domain);
						if( isset($external_store_url) && !empty($external_store_url) ) {
							?><p class="external_store_url"><label><a style="color: white;" target="_blank" href="<?php echo apply_filters( 'vendor_shop_page_external_store', esc_url_raw($external_store_url), $vendor_id ); ?>"><?php echo $external_store_label; ?></a></label></p><?php
						}
					}
				?>
			</div>
		</div>
		<div class="description">
			<div class="social_profile">
			<?php
				$vendor_fb_profile = get_user_meta($vendor_id,'_vendor_fb_profile', true);
				$vendor_twitter_profile = get_user_meta($vendor_id,'_vendor_twitter_profile', true);
				$vendor_linkdin_profile = get_user_meta($vendor_id,'_vendor_linkdin_profile', true);
				$vendor_google_plus_profile = get_user_meta($vendor_id,'_vendor_google_plus_profile', true);
				$vendor_youtube = get_user_meta($vendor_id,'_vendor_youtube', true);
				$vendor_instagram = get_user_meta($vendor_id,'_vendor_instagram', true);
			?>
				<?php if($vendor_fb_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_fb_profile); ?>"><img src="<?php echo $WCMp->plugin_url . 'assets/images/facebook.png';?>" alt="facebook" height="20" width="20" ></a><?php } ?>
				<?php if($vendor_twitter_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_twitter_profile); ?>"><img src="<?php echo $WCMp->plugin_url . 'assets/images/twitter.png';?>" alt="twitter" height="20" width="20" ></a><?php } ?>
				<?php if($vendor_linkdin_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_linkdin_profile); ?>"><img src="<?php echo $WCMp->plugin_url . 'assets/images/linkedin.png';?>" alt="linkedin" height="20" width="20" ></a><?php } ?>
				<?php if($vendor_google_plus_profile) { ?> <a target="_blank" href="<?php echo esc_url($vendor_google_plus_profile); ?>"><img src="<?php echo $WCMp->plugin_url . 'assets/images/google-plus.png';?>" alt="google_plus" height="20" width="20" ></a><?php } ?>
				<?php if($vendor_youtube) { ?> <a target="_blank" href="<?php echo esc_url($vendor_youtube); ?>"><img src="<?php echo $WCMp->plugin_url . 'assets/images/youtube.png';?>" alt="youtube" height="20" width="20" ></a><?php } ?>
				<?php if($vendor_instagram) { ?> <a target="_blank" href="<?php echo esc_url($vendor_instagram); ?>"><img src="<?php echo $WCMp->plugin_url . 'assets/images/instagram.png';?>" alt="instagram" height="20" width="20" ></a><?php } ?>
			</div>
		</div>
	</div>
</div>	
<?php
if(get_wcmp_vendor_settings('is_sellerreview', 'general') == 'Enable') {
	$queried_object = get_queried_object();
	if(isset($queried_object->term_id) && !empty($queried_object)) {		
		$rating_val_array = wcmp_get_vendor_review_info($queried_object->term_id);
		$WCMp->template->get_template( 'review/rating.php', array('rating_val_array' => $rating_val_array));
	}
}
$vendor_hide_description = get_user_meta($vendor_id,'_vendor_hide_description', true);
if(!$vendor_hide_description) { ?>
<div class="description_data">
	<?php
		$string = $description;
	?>
	<table>
		<tbody>
			<tr>
				<td>
					<label><strong><?php _e('Description', $WCMp->text_domain) ?></strong></label>
				</td>
				<td style="padding: 15px;">
					<?php echo stripslashes($string); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?php } ?>
