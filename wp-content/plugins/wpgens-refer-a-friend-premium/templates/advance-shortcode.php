<?php
/**
 * Refer a Friend Advance Shortcode HTML
 *
 * Available variables: $rafLink (Referral link), $title (twitter title option), $twitter_via(twitter via option)
 *
 * @see     http://wpgens.helpscoutdocs.com/article/34-how-to-edit-template-files-and-keep-them-after-plugin-update
 * @version 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div class="gens-refer-a-friend" data-link="<?php echo $rafLink; ?>">
    <?php include WPGens_RAF::get_template_path('link.php','/parts'); ?>
    <?php
        do_action('gens_after_referral_url');
    ?>
    <?php include WPGens_RAF::get_template_path('share.php','/parts'); ?>
</div>