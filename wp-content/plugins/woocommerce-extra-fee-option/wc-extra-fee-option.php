<?php
/*
Plugin Name: WooCommerce Extra Fee Option
Plugin URI: http://terrytsang.com/shop/shop/woocommerce-extra-fee-option/
Description: Allow you to add extra fee with minimum order to WooCommerce
Version: 1.0.7
Author: Terry Tsang
Author URI: http://shop.terrytsang.com
*/

/*  Copyright 2012-2016 Terry Tsang (email: terrytsang811@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

// Define plugin name
define('wc_plugin_name_extra_fee_option', 'WooCommerce Extra Fee Option');

// Define plugin version
define('wc_version_extra_fee_option', '1.0.7');


// Checks if the WooCommerce plugins is installed and active.
if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))){
	if(!class_exists('WooCommerce_Extra_Fee_Option')){
		class WooCommerce_Extra_Fee_Option{

			public static $plugin_prefix;
			public static $plugin_url;
			public static $plugin_path;
			public static $plugin_basefile;

			var $textdomain;
		    var $types;
		    var $options_extra_fee_option;
		    var $saved_options_extra_fee_option;

			/**
			 * Gets things started by adding an action to initialize this plugin once
			 * WooCommerce is known to be active and initialized
			 */
			public function __construct(){
				load_plugin_textdomain('wc-extra-fee-option', false, dirname(plugin_basename(__FILE__)) . '/languages/');
				
				WooCommerce_Extra_Fee_Option::$plugin_prefix = 'wc_extra_fee_option_';
				WooCommerce_Extra_Fee_Option::$plugin_basefile = plugin_basename(__FILE__);
				WooCommerce_Extra_Fee_Option::$plugin_url = plugin_dir_url(WooCommerce_Extra_Fee_Option::$plugin_basefile);
				WooCommerce_Extra_Fee_Option::$plugin_path = trailingslashit(dirname(__FILE__));
				
				$this->textdomain = 'wc-extra-fee-option';

				$this->types = array('fixed' => 'Fixed Fee', 'percentage' => 'Cart Percentage(%)');
				
				$this->options_extra_fee_option = array(
					'extra_fee_option_enabled' => '',
					'extra_fee_option_label' => 'Extra Fee',
					'extra_fee_option_type' => 'fixed',
					'extra_fee_option_cost' => 0,
					'extra_fee_option_taxable' => false,
					'extra_fee_option_minorder' => 0,
				);
	
				$this->saved_options_extra_fee_option = array();
				
				add_action('woocommerce_init', array(&$this, 'init'));
			}

			/**
			 * Initialize extension when WooCommerce is active
			 */
			public function init(){
				
				//add menu link for the plugin (backend)
				add_action( 'admin_menu', array( &$this, 'add_menu_extra_fee_option' ) );

				//add admin css3 button stylesheet
				//add_action('admin_init', array( &$this, 'tsang_plugin_admin_init') );
				
				if(get_option('extra_fee_option_enabled'))
				{
					//add_action( 'woocommerce_before_calculate_totals', array( &$this, 'woo_add_extra_fee') );
					add_action( 'woocommerce_cart_calculate_fees', array( &$this, 'woo_add_extra_fee') );
				}
			}
			
			function tsang_plugin_admin_init() {
				/* Register admin stylesheet. */
				wp_register_style( 'tsangPluginStylesheet', plugins_url('css/admin.css', __FILE__) );
			}
			
			function tsang_plugin_admin_styles() {
				/*
				 * It will be called only on your plugin admin page, enqueue our stylesheet here
				*/
				wp_enqueue_style( 'tsangPluginStylesheet' );
			}
		
			/**
			 * Set the extra fee with min order total limit
			 */
			public function woo_add_extra_fee() {
				global $woocommerce;
			
				$extra_fee_option_label		= get_option( 'extra_fee_option_label' ) ? get_option( 'extra_fee_option_label' ) : 'Extra Fee';
				$extra_fee_option_cost		= get_option( 'extra_fee_option_cost' ) ? get_option( 'extra_fee_option_cost' ) : '0';
				$extra_fee_option_type		= get_option( 'extra_fee_option_type' ) ? get_option( 'extra_fee_option_type' ) : 'fixed';
				$extra_fee_option_taxable	= get_option( 'extra_fee_option_taxable' ) ? get_option( 'extra_fee_option_taxable' ) : false;
				$extra_fee_option_minorder	= get_option( 'extra_fee_option_minorder' ) ? get_option( 'extra_fee_option_minorder' ) : '0';
				
				//get cart total
				$total = $woocommerce->cart->subtotal;
				
				//check for fee type (fixed fee or cart %)
				if($extra_fee_option_type == 'percentage'){
					$extra_fee_option_cost = ($extra_fee_option_cost / 100) * $total;
				} 
			
				//round the cost to 2 decimal points - fixed Paypal problem raised by Robbo870
				$extra_fee_option_cost = round($extra_fee_option_cost, 2);
				
				//if cart total less or equal than $min_order, add extra fee
				if($extra_fee_option_minorder > 0){
					if($total <= $extra_fee_option_minorder) {
						$woocommerce->cart->add_fee( __($extra_fee_option_label, 'woocommerce'), $extra_fee_option_cost, $extra_fee_option_taxable );
					}
				} else {
					$woocommerce->cart->add_fee( __($extra_fee_option_label, 'woocommerce'), $extra_fee_option_cost, $extra_fee_option_taxable );
				}
			}
			
			/**
			 * Add a menu link to the woocommerce section menu
			 */
			function add_menu_extra_fee_option() {
				$wc_page = 'woocommerce';
				$comparable_settings_page = add_submenu_page( $wc_page , __( 'Extra Fee Option', $this->textdomain ), __( 'Extra Fee Option', $this->textdomain ), 'manage_options', 'wc-extra-fee-option', array(
						&$this,
						'settings_page_extra_fee_option'
				));
				
				add_action( 'admin_print_styles-' . $comparable_settings_page, array( &$this, 'tsang_plugin_admin_styles') );
			}
			
			/**
			 * Create the settings page content
			 */
			public function settings_page_extra_fee_option() {
			
				// If form was submitted
				if ( isset( $_POST['submitted'] ) )
				{
					check_admin_referer( $this->textdomain );
	
					$this->saved_options_extra_fee_option['extra_fee_option_enabled'] = ! isset( $_POST['extra_fee_option_enabled'] ) ? '1' : $_POST['extra_fee_option_enabled'];
					$this->saved_options_extra_fee_option['extra_fee_option_label'] = ! isset( $_POST['extra_fee_option_label'] ) ? 'Extra Fee' : $_POST['extra_fee_option_label'];
					$this->saved_options_extra_fee_option['extra_fee_option_cost'] = ! isset( $_POST['extra_fee_option_cost'] ) ? 0 : $_POST['extra_fee_option_cost'];
					$this->saved_options_extra_fee_option['extra_fee_option_type'] = ! isset( $_POST['extra_fee_option_type'] ) ? 'fixed' : $_POST['extra_fee_option_type'];
					$this->saved_options_extra_fee_option['extra_fee_option_taxable'] = ! isset( $_POST['extra_fee_option_taxable'] ) ? false : $_POST['extra_fee_option_taxable'];
					$this->saved_options_extra_fee_option['extra_fee_option_minorder'] = ! isset( $_POST['extra_fee_option_minorder'] ) ? 0 : $_POST['extra_fee_option_minorder'];
						
					foreach($this->options_extra_fee_option as $field => $value)
					{
						$option_extra_fee_option = get_option( $field );
			
						if($option_extra_fee_option != $this->saved_options_extra_fee_option[$field])
							update_option( $field, $this->saved_options_extra_fee_option[$field] );
					}
						
					// Show message
					echo '<div id="message" class="updated fade"><p>' . __( 'WooCommerce Extra Fee Option options saved.', $this->textdomain ) . '</p></div>';
				}
			
				$extra_fee_option_enabled	= get_option( 'extra_fee_option_enabled' );
				$extra_fee_option_label		= get_option( 'extra_fee_option_label' ) ? get_option( 'extra_fee_option_label' ) : 'Extra Fee';
				$extra_fee_option_cost		= get_option( 'extra_fee_option_cost' ) ? get_option( 'extra_fee_option_cost' ) : '0';
				$extra_fee_option_type		= get_option( 'extra_fee_option_type' ) ? get_option( 'extra_fee_option_type' ) : 'fixed';
				$extra_fee_option_taxable	= get_option( 'extra_fee_option_taxable' ) ? get_option( 'extra_fee_option_taxable' ) : false;
				$extra_fee_option_minorder	= get_option( 'extra_fee_option_minorder' ) ? get_option( 'extra_fee_option_minorder' ) : '0';
				
				$checked_enabled = '';
				$checked_taxable = '';
			
				if($extra_fee_option_enabled)
					$checked_enabled = 'checked="checked"';
				
				if($extra_fee_option_taxable)
					$checked_taxable = 'checked="checked"';

			
				$actionurl = $_SERVER['REQUEST_URI'];
				$nonce = wp_create_nonce( $this->textdomain );
			
			
				// Configuration Page
			
				?>
				<div id="icon-options-general" class="icon32"></div>
				<h3><?php _e( 'Extra Fee Option', $this->textdomain); ?></h3>
				
				
				<table width="90%" cellspacing="2">
				<tr>
					<td width="70%" valign="top">
						<form action="<?php echo $actionurl; ?>" method="post">
						<table>
								<tbody>
									<tr>
										<td colspan="2">
											<table class="widefat auto" cellspacing="2" cellpadding="2" border="0">
												<tr>
													<td width="25%"><?php _e( 'Enable', $this->textdomain ); ?></td>
													<td>
														<input class="checkbox" name="extra_fee_option_enabled" id="extra_fee_option_enabled" value="0" type="hidden">
														<input class="checkbox" name="extra_fee_option_enabled" id="extra_fee_option_enabled" value="1" <?php echo $checked_enabled; ?> type="checkbox">
													</td>
												</tr>
												<tr>
													<td><?php _e( 'Label', $this->textdomain ); ?></td>
													<td>
														<input type="text" id="extra_fee_option_label" name="extra_fee_option_label" value="<?php echo $extra_fee_option_label; ?>" size="30" />
													</td>
												</tr>
												<tr>
													<td><?php _e( 'Amount', $this->textdomain ); ?></td>
													<td>
														<input type="text" id="extra_fee_option_cost" name="extra_fee_option_cost" value="<?php echo $extra_fee_option_cost; ?>" size="10" />
													</td>
												</tr>
												<tr>
													<td width="25%"><?php _e( 'Type', $this->textdomain ); ?></td>
													<td>
														<select name="extra_fee_option_type">
															<option value="fixed" <?php if($extra_fee_option_type == 'fixed') { echo 'selected="selected"'; } ?>><?php _e( 'Fixed Fee', $this->textdomain ); ?></option>
															<option value="percentage" <?php if($extra_fee_option_type == 'percentage') { echo 'selected="selected"'; } ?>><?php _e( 'Cart Percentage(%)', $this->textdomain ); ?></option>
														</select>
													</td>
												</tr>
												<tr>
													<td width="25%"><?php _e( 'Taxable', $this->textdomain ); ?></td>
													<td>
														<input class="checkbox" name="extra_fee_option_taxable" id="extra_fee_option_taxable" value="0" type="hidden">
														<input class="checkbox" name="extra_fee_option_taxable" id="extra_fee_option_taxable" value="1" <?php echo $checked_taxable; ?> type="checkbox">
													</td>
												</tr>
												<tr>
													<td><?php _e( 'Minumum Order<br><span style="color:#999;">(Optional, apply extra fee when cart total is less or equal than this amount)</span>', $this->textdomain ); ?></td>
													<td>
															<?php echo get_woocommerce_currency_symbol(); ?>&nbsp;<input type="text" id="extra_fee_option_minorder" name="extra_fee_option_minorder" value="<?php echo $extra_fee_option_minorder; ?>" size="10" />
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td colspan=2">
											<input class="button-primary" type="submit" name="Save" value="<?php _e('Save Options', $this->textdomain); ?>" id="submitbutton" />
											<input type="hidden" name="submitted" value="1" /> 
											<input type="hidden" id="_wpnonce" name="_wpnonce" value="<?php echo $nonce; ?>" />
										</td>
									</tr>
								</tbody>
						</table>
						</form>
					
					</td>
					
					<td width="30%" style="background:#ececec;padding:10px 5px;" valign="top">
						<p><b>WooCommerce Extra Fee Option</b> is a FREE woocommerce plugin developed by <a href="http://shop.terrytsang.com" target="_blank" title="Terry Tsang - a PHP Developer and Wordpress Consultant">Terry Tsang</a>. This plugin aims to add extra fee minimum order for WooCommerce.</p>
						
						<?php
							$get_pro_image = WooCommerce_Extra_Fee_Option::$plugin_url . '/images/pro-version.png';
						?>
						<p align="center"><a href="http://terrytsang.com/shop/shop/woocommerce-extra-fee-option-pro/" target="_blank" title="WooCommerce Extra Fee Options PRO"><img src="<?php echo $get_pro_image; ?>" border="0" /></a></p>
						
						<h3>Spreading the Word</h3>

						<ul style="list-style:dash">If you find this plugin helpful, you can:	
							<li>- Write and review about it in your blog</li>
							<li>- Rate it on <a href="http://wordpress.org/extend/plugins/woocommerce-extra-fee-option/" target="_blank">wordpress plugin page</a></li>
							<li>- Share on your social media<br />
							<a href="http://www.facebook.com/sharer.php?u=http://terrytsang.com/shop/shop/woocommerce-extra-fee-option/&amp;t=WooCommerce Extra Fee Option" title="Share this WooCommerce Extra Fee Option on Facebook" target="_blank"><img src="http://terrytsang.com/shop/images/social_facebook.png" alt="Share this WooCommerce Extra Fee Option plugin on Facebook"></a> 
							<a href="https://twitter.com/intent/tweet?url=http%3A%2F%2Fterrytsang.com%2Fshop%2Fshop%2Fwoocommerce-extra-fee-option%2F&text=WooCommerce Extra Fee Option - &via=terrytsang811" target="_blank"><img src="http://terrytsang.com/shop/images/social_twitter.png" alt="Tweet about WooCommerce Extra Fee Option plugin"></a>
							<a href="http://linkedin.com/shareArticle?mini=true&amp;url=http://terrytsang.com/shop/shop/woocommerce-extra-fee-option/&amp;title=WooCommerce Extra Fee Option plugin" title="Share this WooCommerce Extra Fee Option plugin on LinkedIn" target="_blank"><img src="http://terrytsang.com/shop/images/social_linkedin.png" alt="Share this WooCommerce Extra Fee Option plugin on LinkedIn"></a>
							</li>
							<li>- Or make a donation</li>
						</ul>
	
						<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LJWSJDBBLNK7W" target="_blank"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" alt="" /></a>

						<h3>Thank you for your support!</h3>
					</td>
					
				</tr>
				</table>
				
				
				<br />
				
			<?php
			}
			
			/**
			 * Get the setting options
			 */
			function get_options() {
				
				foreach($this->options_extra_fee_option as $field => $value)
				{
					$array_options[$field] = get_option( $field );
				}
					
				return $array_options;
			}
			
			/**
			 * Load javascript for the page
			 */
			/*public function script_extra_fee_option()
			{
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_script( 'custom-plugin-script', plugins_url('/js/script.js', __FILE__));
			}*/
				
			/**
			 * Load stylesheet for the page
			 */
			/*public function stylesheet_extra_fee_option() {
				wp_register_style( 'custom-plugin-stylesheet', plugins_url('/css/style.css', __FILE__) );
				wp_enqueue_style( 'custom-plugin-stylesheet' );
			}*/
			
		}//end class
			
	}//if class does not exist
	
	$woocommerce_extra_fee_option = new WooCommerce_Extra_Fee_Option();
}
else{
	add_action('admin_notices', 'wc_extra_fee_option_error_notice');
	function wc_extra_fee_option_error_notice(){
		global $current_screen;
		if($current_screen->parent_base == 'plugins'){
			echo '<div class="error"><p>'.__(wc_plugin_name_extra_fee_option.' requires <a href="http://www.woothemes.com/woocommerce/" target="_blank">WooCommerce</a> to be activated in order to work. Please install and activate <a href="'.admin_url('plugin-install.php?tab=search&type=term&s=WooCommerce').'" target="_blank">WooCommerce</a> first.').'</p></div>';
		}
	}
}

?>