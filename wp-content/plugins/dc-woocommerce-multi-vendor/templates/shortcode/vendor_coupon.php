<?php
/**
 * The template for displaying vendor coupon
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/shortcode/vendor_coupon.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce, $WCMp;
$user = wp_get_current_user();
$vendor = get_wcmp_vendor($user->ID);
if($vendor) {
	echo  '<h3>'.__('Coupons', 'dc-woocommerce-multi-vendor').'</h3>';
	if($WCMp->vendor_caps->vendor_capabilities_settings('is_submit_coupon') && get_user_meta($user->ID, '_vendor_submit_coupon' ,true)) { 
		if($coupons) {?> 
			<table>
				<tbody>
				<th><?php _e('Coupon Code', 'dc-woocommerce-multi-vendor' ) ?></th>
				<th><?php _e('Usage Count', 'dc-woocommerce-multi-vendor' ) ?></th>
				<?php
				foreach($coupons as $coupon) {
					$usage_count = get_post_meta($coupon, 'usage_count', true);
					if(!$usage_count) $usage_count = 0;
					$coupon_post = get_post($coupon);
					echo '<tr>';
					echo '<td>'.$coupon_post->post_title.'</td>';
					echo '<td>'.$usage_count.'</td>';
					echo '</tr>';
				}
				?>
				</tbody>
			</table>
			<p><?php echo  __('Submit another coupon by', 'dc-woocommerce-multi-vendor').'  <a class="shop_url button button-primary" target="_blank" href='.admin_url( 'edit.php?post_type=shop_coupon' ).'><strong>'.__('Submit Coupons', 'dc-woocommerce-multi-vendor').'</strong></a></p>' ?>
		<?php		
		} else {
			echo __('Sorry! You have not created any coupon till now.You can create your product specific coupon from -', 'dc-woocommerce-multi-vendor').'<a class="shop_url button button-primary" target="_blank" href='.admin_url( 'edit.php?post_type=shop_coupon' ).'><strong>'.__('Submit Coupons', 'dc-woocommerce-multi-vendor').'</strong></a>';
		}
	} else {
		echo __('Sorry ! You do not have the capability to add coupons.', 'dc-woocommerce-multi-vendor');
	}
}
?>