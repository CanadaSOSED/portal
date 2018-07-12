<?php
/**
 * Plugin Name: Tabs Responsive
 * Version: 1.8.5
 * Description:  Tabs Responsive is the most easiest drag & drop Tabs builder for WordPress. You can add unlimited Tabs with unlimited color Scheme.
 * Author: wpshopmart
 * Author URI: https://www.wpshopmart.com
 * Plugin URI: https://www.wpshopmart.com/plugins
 */

/**
 * DEFINE PATHS
 */
define("wpshopmart_tabs_r_directory_url", plugin_dir_url(__FILE__));
define("wpshopmart_tabs_r_text_domain", "wpsm_tabs_r");

/**
 * PLUGIN Install
 */
require_once("ink/install/installation.php");

function wpsm_tabs_r_default_data() {
	$Settings_Array = serialize( array(
		"tabs_sec_title" 	 => "yes",
		"show_tabs_title_icon" => "1",
		"show_tabs_icon_align" => "left",
		"enable_tabs_border"   => "yes",
		"tabs_title_bg_clr"   => "#e8e8e8",
		"tabs_title_icon_clr" => "#000000",
		"select_tabs_title_bg_clr"   => "#ffffff",
		"select_tabs_title_icon_clr" => "#000000",
		"tabs_desc_bg_clr"    => "#ffffff",
		"tabs_desc_font_clr"  => "#000000",
		"title_size"         => "14",
		"des_size"     		 => "16",
		"font_family"     	 => "Open Sans",
		"tabs_styles"      =>1,
		"custom_css"      =>"",
		"tabs_animation"      =>"fadeIn",
		"tabs_alignment"      =>"horizontal",
		"tabs_position"      =>"left",
		"tabs_margin"      =>"no",
		"tabs_content_margin"      =>"no",
		) );
	add_option('Tabs_R_default_Settings', $Settings_Array);
}
register_activation_hook( __FILE__, 'wpsm_tabs_r_default_data' );

/**
 * tabs responsive cpt + admin panel
 */
require_once("ink/admin/menu.php");

/**
 * SHORTCODE
 */
require_once("template/shortcode.php");

/**
 * WIDGET
 */
 require_once("ink/widget/widget.php");
 
// darken color code 
 function ColorDarken($color, $dif=50){
 
    $color = str_replace('#', '', $color);
    if (strlen($color) != 6){ return '000000'; }
    $rgb = '';
 
    for ($x=0;$x<3;$x++){
        $c = hexdec(substr($color,(2*$x),2)) - $dif;
        $c = ($c < 0) ? 0 : dechex($c);
        $rgb .= (strlen($c) < 2) ? '0'.$c : $c;
    }
	return '#'.$rgb;
}

// Add settings link on plugin page
function wpsm_tabs_r_settings_link($links) { 
  $settings_link = '<a href="edit.php?post_type=tabs_responsive">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'wpsm_tabs_r_settings_link' );
add_action('admin_menu' , 'wpsm_tabs_r_recom_menu');
function wpsm_tabs_r_recom_menu() {
	$submenu = add_submenu_page('edit.php?post_type=tabs_responsive', __('More_Free_Plugins', wpshopmart_tabs_r_text_domain), __('More Free Plugins', wpshopmart_tabs_r_text_domain), 'administrator', 'wpsm_tabs_r_recom_page', 'wpsm_tabs_rrecom_page_funct');
	
	//add hook to add styles and scripts for Tabs Plugin admin page
    add_action( 'admin_print_styles-' . $submenu, 'wpsm_tabs_r_recom_js_css' );
}
function wpsm_tabs_r_recom_js_css(){
	wp_enqueue_style('wpsm_tabs_r_bootstrap_css_recom', wpshopmart_tabs_r_directory_url.'assets/css/bootstrap.css');
	wp_enqueue_style('wpsm_tabs_ac_help_css', wpshopmart_tabs_r_directory_url.'assets/css/help.css');
}
function wpsm_tabs_rrecom_page_funct(){
	require_once('ink/admin/free.php');
}
 
?>