<?php
/**
 * Single Product Multiple vendors
 *
 * This template can be overridden by copying it to yourtheme/dc-product-vendor/single-product/multiple_vendors_products.php.
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
if (!defined('ABSPATH')) {
    exit;
}
global $WCMp, $post, $wpdb;
if (count($results) > 1) {
    $i = 0;
    ?>
    <div class="ajax_loader_class_msg"><img src="<?php echo $WCMp->plugin_url ?>assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>
    <div class="container">		
        <div class="row rowhead">
            <div class="rowsub centerclass"><?php echo __('Vendor', $WCMp->text_domain); ?></div>
            <div class="rowsub"><?php echo __('Rating', $WCMp->text_domain); ?></div>
            <div class="rowsub"><?php echo __('Price', $WCMp->text_domain); ?></div>
            <div class="rowsub">
                <select name="wcmp_multiple_product_sorting" id="wcmp_multiple_product_sorting" class="wcmp_multiple_product_sorting" attrid="<?php echo $post->ID; ?>" >
                    <option value="price"><?php echo __('Price Low To High', $WCMp->text_domain); ?></option>
                    <option value="price_high"><?php echo __('Price High To Low', $WCMp->text_domain); ?></option>
                    <option value="rating"><?php echo __('Rating High To Low', $WCMp->text_domain); ?></option>
                    <option value="rating_low"><?php echo __('Rating Low To High', $WCMp->text_domain); ?></option>
                </select>
            </div>
            <div style="clear:both;"></div>
        </div>			
        <?php
        $WCMp->template->get_template('single-product/multiple_vendors_products_body.php', array('more_product_array' => $more_product_array, 'sorting' => 'price'));
        ?>		
    </div>		
    <?php
} else {
    ?>
    <div class="container">
        <div class="row">
    <?php echo __('Sorry no more offers available', $WCMp->text_domain); ?>
        </div>
    </div>	
<?php }
?>

