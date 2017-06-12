<?php
class WCMp_Settings_To_Do_List {
		/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	private $tab;

	/**
	 * Start up
	 */
	public function __construct($tab) {
		$this->tab = $tab;
		$this->options = get_option( "wcmp_{$this->tab}_settings_name" );
		$this->settings_page_init();
	}

	/**
	 * Register and add settings
	 */
	public function settings_page_init() {
		global $WCMp;

		//pending vendor
		$get_pending_vendors  = get_users( 'role=dc_pending_vendor' );
		if(!empty($get_pending_vendors)) {
		?>
		<h3><?php  echo apply_filters('to_do_pending_vendor_text',__('Pending Vendor Approval', $WCMp->text_domain)); ?></h3>
		<table class="form-table" id="to_do_list">
			<tbody>
				<tr>
					<th style="width:50%" ><?php _e('Pending User', $WCMp->text_domain ); ?> </th>
					<th><?php _e('Edit', $WCMp->text_domain ); ?></th>
					<th><?php _e('Activate', $WCMp->text_domain ); ?></th>
					<th><?php _e('Reject', $WCMp->text_domain ); ?></th>
					<th><?php _e('Dismiss', $WCMp->text_domain ); ?></th>
				</tr>
				<?php foreach($get_pending_vendors as $pending_vendor) {
					$dismiss = get_user_meta($pending_vendor->ID, '_dismiss_to_do_list', true);
					if($dismiss) continue;
					?>
					<tr>
						<td style="width:50%" class="username column-username"><img alt="" src="<?php echo $WCMp->plugin_url .'assets/images/wp-avatar-frau.jpg'; ?>" class="avatar avatar-32 photo" height="32" width="32"><?php echo $pending_vendor->user_login; ?></td>
						<td class="edit"><a target="_blank" href="user-edit.php?user_id=<?php echo $pending_vendor->ID; ?>&amp;wp_http_referer=%2Fwordpress%2Fdc_vendor%2Fwp-admin%2Fusers.php%3Frole%3Ddc_pending_vendor"><input type="button" class="vendor_edit_button" value="Edit" /> </a> </td>
						<td class="activate"><input class="activate_vendor" type="button" class="activate_vendor" data-id="<?php echo $pending_vendor->ID; ?>" value="Activate" ></td>
						<td class="reject"><input class="reject_vendor" type="button" class="reject_vendor" data-id="<?php echo $pending_vendor->ID; ?>" value="Reject"></td>
						<td class="dismiss"><input class="vendor_dismiss_button" type="button" data-type="user" data-id="<?php echo $pending_vendor->ID; ?>" id="dismiss_request" name="dismiss_request" value="Dismiss"></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php }
		$vendor_ids = array();
		$vendors = get_wcmp_vendors();
		if(!empty($vendors) && is_array($vendors) ) {			
			foreach($vendors as $vendor){
				$vendor_ids[] = $vendor->id;
			}
		}
		//coupon
		$args = array(
			'posts_per_page'   => -1,
			'author__in' => $vendor_ids,
			'post_type'        => 'shop_coupon',
			'post_status'      => 'pending',
		);
		$get_pending_coupons =  new WP_Query($args);
		$get_pending_coupons  = $get_pending_coupons->get_posts();
		if(!empty($get_pending_coupons)) {
		?>
		<h3><?php _e('Pending Coupons Approval', $WCMp->text_domain); ?></h3>
		<table class="form-table" id="to_do_list">
			<tbody>
				<tr>
					<th><?php _e('Vendor Name', $WCMp->text_domain ); ?> </th>
					<th><?php _e('Coupon Name', $WCMp->text_domain ); ?></th>
					<th><?php _e('Edit', $WCMp->text_domain ); ?></th>
					<th><?php _e('Dismiss', $WCMp->text_domain ); ?></th>
				</tr>
				<?php foreach($get_pending_coupons as $get_pending_coupon) {
					$dismiss = get_post_meta($get_pending_coupon->ID, '_dismiss_to_do_list', true);
					if($dismiss) continue;
					?>
					<tr>
						<?php $currentvendor = get_userdata( $get_pending_coupon->post_author ); ?>
						<td class="coupon column-coupon"><a href="user-edit.php?user_id=<?php echo $get_pending_coupon->post_author; ?>&amp;wp_http_referer=%2Fwordpress%2Fdc_vendor%2Fwp-admin%2Fusers.php%3Frole%3Ddc_vendor" target="_blank"><?php echo $currentvendor->display_name; ?></a></td>
						<td class="coupon column-coupon"><?php echo $get_pending_coupon->post_title; ?></td>
						<td class="edit"><a target="_blank" href="post.php?post=<?php echo $get_pending_coupon->ID; ?>&action=edit"><input type="button" class="vendor_edit_button" value="Edit" /> </a> </td>
						<td class="dismiss"><input class="vendor_dismiss_button" type="button" data-type="shop_coupon" data-id="<?php echo $get_pending_coupon->ID; ?>" id="dismiss_request" name="dismiss_request" value="Dismiss"></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php }

		//product
		$args = array(
			'posts_per_page'   => -1,
			'author__in' => $vendor_ids,
			'post_type'        => 'product',
			'post_status'      => 'pending',
		);
		$get_pending_products =  new WP_Query($args);
		$get_pending_products  = $get_pending_products->get_posts();
		if(!empty($get_pending_products)) {
		?>
		<h3><?php _e('Pending Products Approval', $WCMp->text_domain); ?></h3>
		<table class="form-table" id="to_do_list">
			<tbody>
				<tr>
						<th><?php _e('Vendor Name', $WCMp->text_domain ); ?></th>
						<th><?php _e('Product Name', $WCMp->text_domain ); ?></th>
						<th><?php _e('Edit', $WCMp->text_domain ); ?></th>
						<th><?php _e('Dismiss', $WCMp->text_domain ); ?></th>
				</tr>
				<?php foreach($get_pending_products as $get_pending_product) {
					$dismiss = get_post_meta($get_pending_product->ID, '_dismiss_to_do_list', true);
					if($dismiss) continue;
					?>
					<tr>
						<?php $currentvendor = get_userdata( $get_pending_product->post_author ); ?>
						<td class="vendor column-coupon"><a href="user-edit.php?user_id=<?php echo $get_pending_product->post_author; ?>&amp;wp_http_referer=%2Fwordpress%2Fdc_vendor%2Fwp-admin%2Fusers.php%3Frole%3Ddc_vendor" target="_blank"><?php echo $currentvendor->display_name; ?></a></td>
						<td class="coupon column-coupon"><?php echo $get_pending_product->post_title; ?></td>
						<td class="edit"><a target="_blank" href="post.php?post=<?php echo $get_pending_product->ID; ?>&action=edit"><input type="button" class="vendor_edit_button" value="Edit" /> </a> </td>
						<td class="dismiss"><input class="vendor_dismiss_button" data-type="product" data-id="<?php echo $get_pending_product->ID; ?>"  type="button" id="dismiss_request" name="dismiss_request" value="Dismiss"></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php }


		//commission
		$args = array(
			'post_type' => 'wcmp_transaction',
			'post_status' => 'wcmp_processing',
			'meta_key' => 'transaction_mode',
			'meta_value' => 'direct_bank',
			'posts_per_page' => -1
		);
		$transactions = get_posts( $args );

		if(!empty($transactions)) {
		?>
		<h3><?php _e('Pending Bank Transfer', $WCMp->text_domain); ?></h3>
		<table class="form-table" id="to_do_list">
			<tbody>
				<tr>
						<th><?php _e('Vendor', $WCMp->text_domain ); ?> </th>
						<th><?php _e('Commission', $WCMp->text_domain ); ?> </th>
						<th><?php _e('Amount', $WCMp->text_domain ); ?></th>
						<th><?php _e('Account Detail', $WCMp->text_domain ); ?></th>
						<th><?php _e('Notify the Vendor', $WCMp->text_domain ); ?></th>
						<th><?php _e('Dismiss', $WCMp->text_domain ); ?></th>
				</tr>
				<?php foreach($transactions as $transaction) {
					$dismiss = get_post_meta($transaction->ID, '_dismiss_to_do_list', true);
					if($dismiss) continue;
					?>
					<tr>
						<?php
						$vendor_term_id = $transaction->post_author;
						$currentvendor = get_wcmp_vendor_by_term($vendor_term_id);
						$account_name = get_user_meta($currentvendor->id, '_vendor_account_holder_name', true);
						$account_no = get_user_meta($currentvendor->id, '_vendor_bank_account_number', true);
						$bank_name = get_user_meta($currentvendor->id, '_vendor_bank_name', true);
						$iban = get_user_meta($currentvendor->id, '_vendor_iban', true);

						$amount =  get_post_meta( $transaction->ID, 'amount', true );
						?>
						<td class="vendor column-coupon"><a href="user-edit.php?user_id=<?php echo $currentvendor->id; ?>&amp;wp_http_referer=%2Fwordpress%2Fdc_vendor%2Fwp-admin%2Fusers.php%3Frole%3Ddc_vendor" target="_blank"><?php echo $currentvendor->user_data->display_name; ?></a></td>
						<td class="commission column-coupon"><?php echo $transaction->post_title; ?></td>
						<td class="commission_val column-coupon"><?php echo get_woocommerce_currency_symbol().$amount; ?></td>
						<td class="account_detail"><?php echo __('Account Name- ', $WCMp->text_domain) .' '.$account_name.'<br>'.__('Account No - ', $WCMp->text_domain) .$account_no.'<br>'.__('Bank Name - ', $WCMp->text_domain) .$bank_name.'<br>'. __('IBAN - ', $WCMp->text_domain) .$iban; ?></td>
						<td class="done"><input class="vendor_transaction_done_button" data-transid="<?php echo $transaction->ID; ?>" data-vendorid="<?php echo $vendor_term_id; ?>" type="button" id="done_request" name="done_request" value="Done"></td>
						<td class="dismiss"><input class="vendor_dismiss_button" data-type="dc_commission" data-id="<?php echo $transaction->ID; ?>" type="button" id="dismiss_request" name="dismiss_request" value="Dismiss"></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
		<?php }
	}
}
