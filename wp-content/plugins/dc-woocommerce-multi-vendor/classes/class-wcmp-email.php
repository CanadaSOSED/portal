<?php
/**
 * WCMp Email Class
 *
 * @version		2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
 
class WCMp_Email {
	
	public function __construct() {		
	  	global $WCMp;
	  	add_action( 'woocommerce_email_customer_details', array( $this, 'wcmp_vendor_messages_customer_support' ), 30, 3 );	
	  	// Intialize WCMp Email Footer text settings
        add_filter('woocommerce_get_settings_email', array($this, 'wcmp_settings_email'));
        // WCMp Email Footer hook
        add_action( 'wcmp_email_footer', array( $this, 'wcmp_email_footer' ) );
	}

	/**
     * Register WCMp emails footer text settings
     *
     * @access public
     * @return array
     */
    public function wcmp_settings_email($settings) {
    	global $WCMp;
        $wcmp_footer_settings = array(
	        array(
	            'title'       => __( 'WCMp Footer text', 'dc-woocommerce-multi-vendor' ),
	            'desc'        => __( 'The text to appear in the footer of WCMp emails.', 'dc-woocommerce-multi-vendor' ),
	            'id'          => 'wcmp_email_footer_text',
	            'css'         => 'width:300px; height: 75px;',
	            'placeholder' => __( 'N/A', 'dc-woocommerce-multi-vendor' ),
	            'type'        => 'textarea',
	            /* translators: %s: site name */
	            'default'     => sprintf( __( '%s - Powered by WC Marketplace', 'dc-woocommerce-multi-vendor' ), get_bloginfo( 'name', 'display' ) ),
	            'autoload'    => false,
	            'desc_tip'    => true,
	        )
        );
        array_splice($settings, 11, 0, $wcmp_footer_settings);
        return $settings;
    }

    /**
	 * Get the WCMp email footer.
	 */
	public function wcmp_email_footer() {
		global $WCMp;
		$WCMp->template->get_template('emails/email-footer.php');
	}
	
	public function wcmp_vendor_messages_customer_support( $order, $sent_to_admin = false, $plain_text = false ) {
		global $WCMp;
		$WCMp->load_class( 'template' );
		$WCMp->template = new WCMp_Template();
		$items = $order->get_items( 'line_item' );
		$vendor_array = array();
		$author_id = '';
		$customer_support_details_settings = get_option('wcmp_general_customer_support_details_settings_name');
		$is_csd_by_admin = '';
		
		foreach( $items as $item_id => $item ) {			
			$product_id = wc_get_order_item_meta( $item_id, '_product_id', true );
			if( $product_id ) {				
				$author_id = wc_get_order_item_meta( $item_id, '_vendor_id', true );
				if( empty($author_id) ) {
					$product_vendors = get_wcmp_product_vendors($product_id);
					if(isset($product_vendors) && (!empty($product_vendors))) {
						$author_id = $product_vendors->id;
					}
					else {
						$author_id = get_post_field('post_author', $product_id);
					}
				}
				if(isset($vendor_array[$author_id])){
					$vendor_array[$author_id] = $vendor_array[$author_id].','.$item['name'];
				}
				else {
					$vendor_array[$author_id] = $item['name'];
				}								
			}						
		}		
		if($plain_text) {
			
		}
		else {	$can_vendor_add_message_on_email_and_thankyou_page = apply_filters('can_vendor_add_message_on_email_and_thankyou_page', true);
                        $is_customer_support_details = apply_filters('is_customer_support_details', true);
			if( isset( $customer_support_details_settings['can_vendor_add_message_on_email_and_thankyou_page'] ) && $can_vendor_add_message_on_email_and_thankyou_page ) {
				$WCMp->template->get_template( 'vendor_message_to_buyer.php', array( 'vendor_array'=>$vendor_array, 'capability_settings'=>$customer_support_details_settings, 'customer_support_details_settings'=>$customer_support_details_settings ));
			}
			elseif(get_wcmp_vendor_settings ('is_customer_support_details', 'general') == 'Enable' && $is_customer_support_details) {
				$WCMp->template->get_template( 'customer_support_details_to_buyer.php', array( 'vendor_array'=>$vendor_array, 'capability_settings'=>$customer_support_details_settings, 'customer_support_details_settings'=>$customer_support_details_settings ));
			}
		}		
	}
	
	public function get_custom_support_message_by_vendor_id($vendor_id, $products) {
		global $WCMp;
		$html = '';
		$user_meta = get_user_meta( $vendor_id );
		$capability_settings = get_option('wcmp_general_customer_support_details_settings_name');
		ob_start();
		echo '<td valign="top" align="left" style=" background:#f4f4f4; padding:0px 40px"><h3 style="color:#557da1;display:block;font-family:Arial,sans-serif; font-size:16px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left">';
		echo __('Customer Support Details of : ','dc-woocommerce-multi-vendor');
		echo '<span style="color:#555;">';
		echo $products;
		echo '</span>';
		echo '<table style="width:100%;vertical-align:top;color:#a4a4a4; padding:10px 0 20px 0" border="0" cellpadding="2" cellspacing="0" >';
		echo '<tr>';
		echo '<td valign="top" align="left" >';
		echo __('Email : ','dc-woocommerce-multi-vendor'); 
		echo '</td>';
		echo '<td valign="top" align="left" >: <a style="color:#505050;" href="mailto:'.$user_meta['_vendor_customer_email'][0].'" target="_blank">';
    echo  $user_meta['_vendor_customer_email'][0];
		echo '</a></td>';
		echo '</tr>';		
		echo '<tr><td valign="top" align="left" >';
		echo  __('Phone : ','dc-woocommerce-multi-vendor'); 
		echo '</td><td valign="top" align="left" >:';
		echo $user_meta['_vendor_customer_phone'][0];
		echo '</td></tr>';		
		echo '<tr><td valign="top" align="left" >';
		echo __('Return Address of : ','dc-woocommerce-multi-vendor');
		echo '</td><td valign="top" align="left" >: <b>';
		echo  $products;
		echo '</b></td></tr>';		
		echo '<tr><td valign="top" align="left" >';
		echo  __('Address Line 1 : ','dc-woocommerce-multi-vendor'); 
		echo '</td><td valign="top" align="left" >:';
		echo $user_meta['_vendor_csd_return_address1'][0];
		echo '</td></tr>';
    echo '<tr><td valign="top" align="left" >';
    echo  __('Address Line 2 : ','dc-woocommerce-multi-vendor');
    echo '</td><td valign="top" align="left" >:';
    echo $user_meta['_vendor_csd_return_address2'][0];
    echo '</td></tr>'; 
    echo '<tr><td valign="top" align="left" >';
    echo  __('State : ','dc-woocommerce-multi-vendor'); 
    echo '</td><td valign="top" align="left" >:';
    echo $user_meta['_vendor_csd_return_state'][0];
    echo '</td></tr>'; 
    echo '<tr><td valign="top" align="left" >';
    echo  __('City : ','dc-woocommerce-multi-vendor');
    echo '</td><td valign="top" align="left" >:';
    echo $user_meta['_vendor_csd_return_city'][0];
    echo '</td></tr>'; 
    echo '<tr><td valign="top" align="left" >';
    echo  __('Country : ','dc-woocommerce-multi-vendor');  
    echo '</td><td valign="top" align="left" >:';
    echo $user_meta['_vendor_csd_return_country'][0];
    echo '</td></tr>'; 
    echo '<tr><td valign="top" align="left" >';
    echo  __('Zip Code : ','dc-woocommerce-multi-vendor');
    echo '</td><td valign="top" align="left" >:';
    echo $user_meta['_vendor_csd_return_zip'][0];
    echo '</td></tr>';
		echo '</table></td>'; 	
		$html = ob_get_clean();		
		return $html;
		
	}
	
	public function get_csd_admin_address() {
		global $WCMp;
		$html = '';
		$capability_settings = get_option('wcmp_general_customer_support_details_settings_name');		
		ob_start();
		?>
		<table>
			<tr>
				<th colspan="2">
				<?php echo __('Customer Support Details :','dc-woocommerce-multi-vendor'); ?>
				</th>				
			</tr>
			<?php if(isset($capability_settings['csd_email'])) { ?>
			<tr>
				<td>
					<b><?php echo __('Email : ','dc-woocommerce-multi-vendor'); ?></b>
				</td>
				<td>
					<?php echo $capability_settings['csd_email']; ?>
				</td>
			</tr>
			<?php }?>
			<?php if(isset($capability_settings['csd_phone'])) { ?>
			<tr>
				<td>
					<b><?php echo __('Phone : ','dc-woocommerce-multi-vendor'); ?></b>
				</td>
				<td>
					<?php echo $capability_settings['csd_phone']; ?>
				</td>
			</tr>
			<?php }?>
			<tr>
				<th colspan="2">
				<?php echo __('Our Return Address :','dc-woocommerce-multi-vendor'); ?>
				</th>				
			</tr>
			
			<?php if(isset($capability_settings['csd_return_address_1'])) { ?>
			<tr>
				<td>
					<b><?php echo __('Address Line 1 : ','dc-woocommerce-multi-vendor'); ?></b>
				</td>
				<td>
					<?php echo $capability_settings['csd_return_address_1']; ?>
				</td>
			</tr>
			<?php }?>
			<?php if(isset($capability_settings['csd_return_address_2'])) { ?>
			<tr>
				<td>
					<b><?php echo __('Address Line 2 : ','dc-woocommerce-multi-vendor'); ?></b>
				</td>
				<td>
					<?php echo $capability_settings['csd_return_address_2']; ?>
				</td>
			</tr>
			<?php }?>
			<?php if(isset($capability_settings['csd_return_state'])) { ?>
			<tr>
				<td>
					<b><?php echo __('State : ','dc-woocommerce-multi-vendor'); ?></b>
				</td>
				<td>
					<?php echo $capability_settings['csd_return_state']; ?>
				</td>
			</tr>
			<?php }?>
			<?php if(isset($capability_settings['csd_return_city'])) { ?>
			<tr>
				<td>
					<b><?php echo __('City : ','dc-woocommerce-multi-vendor'); ?></b>
				</td>
				<td>
					<?php echo $capability_settings['csd_return_city']; ?>
				</td>
			</tr>
			<?php }?>
			<?php if(isset($capability_settings['csd_return_country'])) { ?>
			<tr>
				<td>
					<b><?php echo __('Country : ','dc-woocommerce-multi-vendor'); ?></b>
				</td>
				<td>
					<?php echo $capability_settings['csd_return_country']; ?>
				</td>
			</tr>
			<?php }?>
			<?php if(isset($capability_settings['csd_return_zipcode'])) { ?>
			<tr>
				<td>
					<b><?php echo __('Zip Code : ','dc-woocommerce-multi-vendor'); ?></b>
				</td>
				<td>
					<?php echo $capability_settings['csd_return_zipcode']; ?>
				</td>
			</tr>
			<?php }?>
		</table>				
		<?php	
		$html = ob_get_clean();
		return $html;		
	}
	
	
	
}


