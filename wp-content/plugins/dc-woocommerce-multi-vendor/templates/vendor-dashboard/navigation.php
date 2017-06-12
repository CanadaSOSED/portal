<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('ABSPATH')) {
    exit;
}
global $WCMp;

sksort($nav_items, 'position', true);
$vendor = get_wcmp_vendor(get_current_user_id());
if (!$vendor->image) {
    $vendor->image = $WCMp->plugin_url . 'assets/images/WP-stdavatar.png';
}
do_action('wcmp_before_vendor_dashboard_navigation');
?>
<div class="wcmp_side_menu">
    <div class="wcmp_top_logo_div"> <img src="<?php echo $vendor->image; ?>" alt="vendordavatar">
        <h3>
            <?php echo get_user_meta(get_current_user_id(), '_vendor_page_title', true) ? get_user_meta(get_current_user_id(), '_vendor_page_title', true) : __('Shop Name', $WCMp->text_domain); ?>
        </h3>
        <ul>
            <li><a target="_blank" href="<?php echo apply_filters('wcmp_vendor_shop_permalink', $vendor->permalink); ?>"><?php _e('Shop', $WCMp->text_domain); ?></a> </li>
            <?php if (apply_filters('wcmp_show_vendor_announcements', true)) : ?>
                <li><a target="_self" href="<?php echo wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_announcements_endpoint', 'vendor', 'general', 'vendor-announcements')); ?>"><?php _e('Announcements', $WCMp->text_domain); ?></a></li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="wcmp_main_menu">
        <ul>
            <?php foreach ($nav_items as $key => $item): ?>
                <?php if (current_user_can($item['capability']) || $item['capability'] === true): ?>
                    <li class="<?php if(!empty($item['submenu'])){ echo 'hasmenu';} ?>">
                        <?php if(array_key_exists($WCMp->endpoints->get_current_endpoint(), $item['submenu'])){ $force_active = true;} else {$force_active = false;}?>
                        <a href="<?php echo esc_url($item['url']); ?>" target="<?php echo $item['link_target'] ?>" data-menu_item="<?php echo $key ?>" class="<?php echo implode(' ', array_map('sanitize_html_class', wcmp_get_vendor_dashboard_nav_item_css_class($key, $force_active))); ?>">
                            <i class="icon_stand dashicons-before <?php echo $item['nav_icon'] ?>"></i>
                            <span class="writtings"><?php echo esc_html($item['label']); ?></span>
                        </a>
                        <?php if (!empty($item['submenu']) && is_array($item['submenu'])): sksort($item['submenu'], 'position', true) ?>
                        <ul class="submenu" <?php if(!in_array('active', wcmp_get_vendor_dashboard_nav_item_css_class($key, $force_active))){ echo 'style="display:none"'; } ?>>
                                <?php foreach ($item['submenu'] as $submenukey => $submenu): ?>
                                    <?php if(current_user_can($submenu['capability']) || $submenu['capability'] === true): ?>
                                        <li class="<?php echo implode(' ', array_map('sanitize_html_class', wcmp_get_vendor_dashboard_nav_item_css_class($submenukey))); ?>">
                                            <a href="<?php echo esc_url($submenu['url']); ?>" target="<?php echo $submenu['link_target'] ?>">- <?php echo esc_html($submenu['label']); ?></a>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php do_action('wcmp_after_vendor_dashboard_navigation'); ?>