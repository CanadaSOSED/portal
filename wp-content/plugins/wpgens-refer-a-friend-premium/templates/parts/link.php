<?php
/**
 * Refer a Friend Product Share Link HTML
 *
 * Available variables: $rafLink (Referral link), $share_text(share text option)
 *
 * @see     http://wpgens.helpscoutdocs.com/article/34-how-to-edit-template-files-and-keep-them-after-plugin-update
 * @version 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<?php if(isset($share_text)) { ?>
<div class="gens-refer-a-friend--share-text"><?php echo $share_text; ?></div>    
<?php } ?>
<div class="gens-raf-message gens-raf__url"><?php _e( 'Your Referral URL:','gens-raf'); ?> <strong><?php echo $rafLink; ?></strong><span class="gens-ctc" data-text="<?php _e('Copied!','gens-raf'); ?>"><?php _e("Click to copy","gens-raf"); ?></span></div>
<?php if($referral_code === "yes" && $raf_id != '') { ?>
    <div class="gens-raf-message gens-raf__code">
        <?php _e( 'Your Coupon Code to share:','gens-raf'); ?> <strong><?php echo $raf_id; ?></strong>
        <span class="gens-ctc" data-text="<?php _e('Copied!','gens-raf'); ?>"><?php _e("Click to copy","gens-raf"); ?></span>
    </div>
<?php } ?>