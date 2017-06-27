<?php
/**
 * Single Product Multiple vendors
 *
 * This template can be overridden by copying it to yourtheme/dc-product-vendor/single-product/multiple_vendors_products_body.php.
 *
 * HOWEVER, on occasion WCMp will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * 
 * @author  WC Marketplace
 * @package dc-woocommerce-multi-vendor/Templates
 * @version 2.3.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $WCMp;
if(isset($more_product_array) && is_array($more_product_array) && count($more_product_array) > 0) {
	if(isset($sorting) && !empty($sorting)) {	
		/*function wcmp_sort_by_price($a, $b) {
			return $a['price_val'] - $b['price_val'];
		}*/		
		if($sorting == 'price') {		
			usort($more_product_array, function($a, $b){return $a['price_val'] - $b['price_val'];});
		}
		elseif($sorting == 'price_high') {		
			usort($more_product_array, function($a, $b){return $a['price_val'] - $b['price_val'];});
			$more_product_array = array_reverse (  $more_product_array);
		}
		elseif($sorting == 'rating') {
			$more_product_array = wcmp_sort_by_rating_multiple_product($more_product_array);			
		}
		elseif($sorting == 'rating_low') {
			$more_product_array = wcmp_sort_by_rating_multiple_product($more_product_array);
			$more_product_array = array_reverse (  $more_product_array);			
		}
	}
	foreach ($more_product_array as $more_product ) {	
            $_product = wc_get_product($more_product['product_id']);
		?>
		<div class="row rowbody">						
			<div class="rowsub centerclass">
				<a href="<?php echo $more_product['shop_link']; ?>" class="wcmp_seller_name"><?php echo $more_product['seller_name']; ?></a>
				
			</div>
			<div class="rowsub">
				<?php 
					if(isset($more_product['rating_data']) && is_array($more_product['rating_data']) && isset($more_product['rating_data']['avg_rating']) && $more_product['rating_data']['avg_rating']!=0 && $more_product['rating_data']['avg_rating']!=''){ 
						$rating_class = '';
						if($more_product['rating_data']['avg_rating'] > 4.5 ) {
							$rating_class = 'wcmp_superb_rating';
						}
						elseif($more_product['rating_data']['avg_rating'] <= 4.5 && $more_product['rating_data']['avg_rating'] > 4.0) {
							$rating_class = 'wcmp_excellent_rating';
						}
						elseif($more_product['rating_data']['avg_rating'] <= 4.0 && $more_product['rating_data']['avg_rating'] > 3.5) {
							$rating_class = 'wcmp_good_rating';
						}
						elseif($more_product['rating_data']['avg_rating'] <= 3.5 && $more_product['rating_data']['avg_rating'] > 2.5) {
							$rating_class = 'wcmp_above_averege_rating';
						}
						elseif($more_product['rating_data']['avg_rating'] <= 2.5 && $more_product['rating_data']['avg_rating'] >= 2.0) {
							$rating_class = 'wcmp_averege_rating';
						}
						elseif($more_product['rating_data']['avg_rating'] < 2.0 ) {
							$rating_class = 'wcmp_bad_rating';
						}
						echo '<span title="'.__(sprintf("Based on %s Rating",$more_product['rating_data']['total_rating']), 'dc-woocommerce-multi-vendor').'" class="'.$rating_class.'"> '.number_format($more_product['rating_data']['avg_rating'],2).'/5.0 </span>';										
					}else {
						echo "<span class='wcmp_norating'> ".__('no ratings','dc-woocommerce-multi-vendor' )." </span>";
					}
				?>								
			</div>
			<div class="rowsub">
                            <?php echo $_product->get_price_html(); ?>
			</div>
			<div class="rowsub">
				<?php if($more_product['product_type'] == 'simple') {?>
					<a href="<?php echo '?add-to-cart='.$more_product['product_id']; ?>" class="buttongap button" ><?php echo apply_filters('add_to_cart_text', __('Add to Cart','dc-woocommerce-multi-vendor')); ?></a>
					<br/><br/>
				<?php } ?>
				<a href="<?php echo get_permalink($more_product['product_id']); ?>" class="buttongap button" ><?php echo __('Details','dc-woocommerce-multi-vendor'); ?></a>
			</div>
			<div style="clear:both;"></div>							
		</div>
		
		
	<?php
	}
}
?>