<?php
/**
 * Refer a Friend Guest Text
 *
 * Available variables: $guest_text (Backend Guest text),
 *
 * @see     http://wpgens.helpscoutdocs.com/article/34-how-to-edit-template-files-and-keep-them-after-plugin-update
 * @version 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="gens-refer-a-friend--generate <?php echo $guest_cookie_class; ?>">
    <?php if($allow_guests === "yes") { ?>
        <div class="gens-raf-generate-guest">
            <span><?php _e( $guest_text,'gens-raf'); ?></span>
            <input class="gens-raf-guest-email" type="email" placeholder="<?php _e( 'Your Email','gens-raf'); ?>" /> 
            <button class="gens-raf-generate-link"><?php _e( 'Generate','gens-raf'); ?></button>
        </div>        
    <?php } else { ?>
        <div class="gens-raf-message"><?php _e( $guest_text,'gens-raf'); ?></div>
    <?php } ?>
</div>
<div class="gens-refer-a-friend gens-refer-a-friend--guest <?php echo $guest_cookie_class; ?>" data-link="<?php echo $rafLink; ?>">
    <?php include WPGens_RAF::get_template_path('link.php','/parts'); ?>
    <?php
        do_action('gens_after_referral_url');
    ?>
    <?php include WPGens_RAF::get_template_path('share.php','/parts'); ?>
</div>