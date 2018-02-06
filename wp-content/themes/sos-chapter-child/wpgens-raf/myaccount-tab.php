<?php
/**
 * Refer a Friend My Account Page HTML
 *
 * Available variables: $rafLink (Referral link), $share_text(share text option), $title (twitter title option), $twitter_via(twitter via option), $coupons [array], $referrer_data [array]
 *
 * @see     http://wpgens.helpscoutdocs.com/article/34-how-to-edit-template-files-and-keep-them-after-plugin-update
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div class="gens-refer-a-friend" data-link="<?php echo $rafLink; ?>">
    <div class="gens-refer-a-friend--share-text"><?php echo $share_text; ?></div>    
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
                <input type="email" placeholder="<?php _e( 'Enter email','gens-raf'); ?>" />
                <input type="text" placeholder="<?php _e( 'Enter name','gens-raf'); ?>" />
            </div>
            <a href="#" id="js--gens-email-clone">+</a>
            <input type="submit" value="<?php _e( 'Send Emails','gens-raf'); ?>">
        </form>
    </div>
    <?php
    // Coupons section
    if($coupons) { ?>
    <h3 class="gens-referral_coupons__title"><?php echo apply_filters( 'wpgens_raf_title', __( 'Unused Refer a Friend Coupons', 'gens-raf' ) ); ?></h3>
        <table class="shop_table shop_table_responsive gens-referral_coupons__table">
            <tr>
                <th><?php _e('Coupon code','gens-raf'); ?></th>
                <th><?php _e('Coupon discount','gens-raf'); ?></th>
                <th><?php _e('Expiry date','gens-raf'); ?></th>
            </tr>
            <?php foreach ( $coupons as $coupon ) { ?>
            <tr>
                <td><?php echo $coupon['title']; ?></td>
                <td><?php echo $coupon['discount']; ?></td>
                <td><?php echo $coupon['expiry']; ?></td>
            </tr>
            <?php } ?>
        </table>
    <?php } ?>

    <h3 class="gens-referral_stats__title"><?php echo apply_filters( 'wpgens_raf_title', __( 'Track your invites', 'gens-raf' ) ); ?></h3>
    <div class="gens-referral_stats">
        <div><?php _e('Earned Coupons:','gens-raf'); ?> <span><?php echo $referrer_data['num_friends_refered']; ?></span></div>
        <div><?php _e('Potential Coupons:','gens-raf'); ?> <span><?php echo $referrer_data['potential_orders']; ?></span></div>
    </div>
    <?php
    // Friends section
    if($referrer_data['friends']) { ?>
        <table class="shop_table shop_table_responsive gens-referral_stats__table">
            <tr>
                <th><?php _e('Friend','gens-raf'); ?></th>
                <th><?php _e('Referred On','gens-raf'); ?></th>
                <th><?php _e('Status','gens-raf'); ?></th>
            </tr>
            <?php foreach ( $referrer_data['friends'] as $friend ) { ?>
            <tr>
                <td><?php echo $friend['name']; ?></td>
                <td><?php echo $friend['date']; ?></td>
                <td><?php _e($friend['status'], 'gens-raf'); ?></td>
            </tr>
            <?php } ?>
        </table>
    <?php } ?>
</div>