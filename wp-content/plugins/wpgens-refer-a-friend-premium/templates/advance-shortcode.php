<?php
/**
 * Refer a Friend Advance Shortcode HTML
 *
 * Available variables: $rafLink (Referral link), $title (twitter title option), $twitter_via(twitter via option)
 *
 * @see     http://wpgens.helpscoutdocs.com/article/34-how-to-edit-template-files-and-keep-them-after-plugin-update
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div class="gens-refer-a-friend" data-link="<?php echo $rafLink; ?>">
    <div id="gens-raf-message"><?php _e( 'Your Referral URL:','gens-raf'); ?> <strong><?php echo $rafLink; ?></strong></div>
    <?php 
        if($referral_code === "yes") {
    ?>
    <div id="gens-raf-message"><?php _e( 'Your Coupon Code to share:','gens-raf'); ?> <strong><?php echo $raf_id; ?></strong></div>
    <?php 
        }
        do_action('gens_after_referral_url');
    ?>
    <div class="gens-referral_share">
        <a href="<?php echo $rafLink; ?>" class="gens-referral_share__fb"><i class="gens_raf_icn-facebook"></i> <?php _e("Share via Facebook","gens-raf"); ?></a>
        <a href="<?php echo $rafLink; ?>" class="gens-referral_share__tw" data-via="<?php echo $twitter_via; ?>" data-title="<?php echo $title; ?>" ><i class="gens_raf_icn-twitter"></i> <?php _e("Share via Twitter","gens-raf"); ?></a>
        <a href="" class="gens-referral_share__gp"><i class="gens_raf_icn-gplus"></i> <?php _e("Share via Google+","gens-raf"); ?></a>
        <a href="<?php echo $rafLink; ?>" class="gens-referral_share__wa" data-title="<?php echo $title. ' '.$rafLink; ?>"><i class="gens_raf_icn-whatsapp"></i> <?php _e("Share via Whatsapp","gens-raf"); ?></a>
    </div>
    <div class="gens-referral_share__email">
        <span class="gens-referral_share__email__title"><?php _e( 'or share via email','gens-raf'); ?></span>
        <form id="gens-referral_share__email" action="" type="post" novalidate>
            <div class="gens-referral_share__email__inputs">
                <input type="email" placeholder="<?php _e( 'Enter email','gens-raf'); ?>">
                <input type="text" placeholder="<?php _e( 'Enter name','gens-raf'); ?>">
            </div>
            <a href="#" id="js--gens-email-clone">+</a>
            <input type="submit" value="<?php _e( 'Send Emails','gens-raf'); ?>">
        </form>
    </div>
</div>