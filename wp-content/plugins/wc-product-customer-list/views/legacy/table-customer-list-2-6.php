<?php 

/**
 * @package WC_Product_Customer_List
 * @version 2.4.4
 */

	// Load metabox at bottom of product admin screen

	if( ! function_exists('wpcl_post_meta_boxes_setup') ) {
		add_action( 'load-post.php', 'wpcl_post_meta_boxes_setup' );
		function wpcl_post_meta_boxes_setup() {
			add_action( 'add_meta_boxes', 'wpcl_add_post_meta_boxes' );
		}
	}

	// Set metabox defaults

	if( ! function_exists('wpcl_add_post_meta_boxes') ) {
		function wpcl_add_post_meta_boxes() {
			add_meta_box(
				'customer-bought',
				esc_html__( 'Customers who bought this product', 'wc-product-customer-list' ),
				'wpcl_post_class_meta_box',
				'product',
				'normal',
				'default'
			);
		}
	}

	// Output customer list inside metabox

	if( ! function_exists('wpcl_post_class_meta_box') ) {
		function wpcl_post_class_meta_box( $object, $box )  {
			global $sitepress, $post, $wpdb;
			$post_id = $post->ID;

			// Check for translated products if WPML is activated

			if(isset($sitepress)) {
				$trid = $sitepress->get_element_trid($post_id, 'post_product');
				$translations = $sitepress->get_element_translations($trid, 'product');
				$post_id = Array();
				foreach( $translations as $lang=>$translation){
				    $post_id[] = $translation->element_id;
				}
			}

			// Query the orders related to the product

			$order_statuses = array_map( 'esc_sql', (array) get_option( 'wpcl_order_status_select', array('wc-completed') ) );
			$order_statuses_string = "'" . implode( "', '", $order_statuses ) . "'";
			$post_id = array_map( 'esc_sql', (array) $post_id );
			$post_string = "'" . implode( "', '", $post_id ) . "'";

			$item_sales = $wpdb->get_results( $wpdb->prepare(
				"SELECT o.ID as order_id, oi.order_item_id FROM
				{$wpdb->prefix}woocommerce_order_itemmeta oim
				INNER JOIN {$wpdb->prefix}woocommerce_order_items oi
				ON oim.order_item_id = oi.order_item_id
				INNER JOIN $wpdb->posts o
				ON oi.order_id = o.ID
				WHERE oim.meta_key = %s
				AND oim.meta_value IN ( $post_string )
				AND o.post_status IN ( $order_statuses_string )
				ORDER BY o.ID DESC",
				'_product_id'
			));

			// Get selected columns from the options page

			$product = WC()->product_factory->get_product( $post );
			$columns = array();
			if(get_option( 'wpcl_order_number', 'yes' ) == 'yes') { $columns[] = __('Order', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_order_date', 'no' ) == 'yes') {$columns[] = __('Date', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_billing_first_name', 'yes' ) == 'yes') { $columns[] = __('Billing First name', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_billing_last_name', 'yes' ) == 'yes') { $columns[] = __('Billing Last name', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_billing_email', 'yes' ) == 'yes') { $columns[] = __('Billing E-mail', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_billing_phone', 'yes' ) == 'yes') { $columns[] = __('Billing Phone', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_billing_address_1','no' ) == 'yes') { $columns[] = __('Billing Address 1', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_billing_address_2','no' ) == 'yes') { $columns[] = __('Billing Address 2', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_billing_city','no' ) == 'yes') { $columns[] = __('Billing City', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_billing_state','no' ) == 'yes') { $columns[] = __('Billing State', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_billing_postalcode','no' ) == 'yes') { $columns[] = __('Billing Postal Code / Zip', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_billing_country','no' ) == 'yes') { $columns[] = __('Billing Country', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_shipping_first_name','no' ) == 'yes') { $columns[] = __('Shipping First name', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_shipping_last_name','no' ) == 'yes') { $columns[] = __('Shipping Last name','wc-product-customer-list'); }
			if(get_option( 'wpcl_shipping_address_1','no' ) == 'yes') { $columns[] = __('Shipping Address 1', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_shipping_address_2','no' ) == 'yes') { $columns[] = __('Shipping Address 2', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_shipping_city','no' ) == 'yes') { $columns[] = __('Shipping City', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_shipping_state','no' ) == 'yes') { $columns[] = __('Shipping State', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_shipping_postalcode','no' ) == 'yes') { $columns[] = __('Shipping Postal Code / Zip', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_shipping_country','no' ) == 'yes') { $columns[] = __('Shipping Country', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_customer_message','yes' ) == 'yes') { $columns[] = __('Customer Message', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_customer_id','no' ) == 'yes') { $columns[] = __('Customer ID', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_order_status','no' ) == 'yes') { $columns[] = __('Order Status', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_order_payment', 'no' ) == 'yes') { $columns[] = __('Payment method', 'wc-product-customer-list'); }
			if ( 'variable' == $product->get_type() ) { $columns[] = __('Variation', 'wc-product-customer-list'); }
			if(get_option( 'wpcl_order_qty', 'yes' ) == 'yes') { $columns[] = __('Qty', 'wc-product-customer-list'); }
			?>
			
			<div class="wpcl-init"></div>
			<div id="postcustomstuff" class="wpcl">
				<?php if($item_sales) {
					$emaillist = array();
					$productcount = array();
					?>
					<table id="list-table" style="width:100%">
						<thead>
							<tr>
								<?php foreach($columns as $column) { ?>
									<th>
										<strong><?php echo $column; ?></strong>
									</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
						<?php
						foreach( $item_sales as $sale ) {
							$order = wc_get_order( $sale->order_id );
							if($order->get_type !== 'refund') {
							?>
									<tr>
										<?php if(get_option( 'wpcl_order_number', 'yes' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo '<a href="' . admin_url( 'post.php' ) . '?post=' . $order->id . '&action=edit" target="_blank">' . $order->id . '</a>'; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_order_date', 'no' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->order_date; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_billing_first_name', 'yes' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->billing_first_name; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_billing_last_name', 'yes' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->billing_last_name; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_billing_email', 'yes' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo '<a href="mailto:' . $order->billing_email . '">' . $order->billing_email . '</a>'; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_billing_phone', 'yes' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo '<a href="tel:' . $order->billing_phone . '">' . $order->billing_phone . '</a>'; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_billing_address_1','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->billing_address_1; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_billing_address_2','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->billing_address_2; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_billing_city','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->billing_city; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_billing_state','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->billing_state; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_billing_postalcode','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->billing_postcode; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_billing_country','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->billing_country; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_shipping_first_name','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->shipping_first_name; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_shipping_last_name','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->shipping_last_name; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_shipping_address_1','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->shipping_address_1; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_shipping_address_2','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->shipping_address_2; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_shipping_city','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->shipping_city; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_shipping_state','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->shipping_state; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_shipping_postalcode','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->shipping_postcode; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_shipping_country','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php
														echo $order->shipping_country;
														echo $order->get_shipping_country();
													?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_customer_message','yes' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->customer_message; ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_customer_ID','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php 
														if($order->customer_user) {
															echo '<a href="' . get_admin_url() . 'user-edit.php?user_id=' . $order->customer_user . '">' . $order->customer_user . '</a>';
														}
													?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_order_status','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo wc_get_order_status_name($order->status); ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_order_payment','no' ) == 'yes') { ?>
											<td>
												<p>
													<?php echo $order->payment_method; ?>
												</p>
											</td>
										<?php } ?>
										<?php if( 'variable' == $product->get_type() ) {
											$variation = wc_get_product( $order->get_item_meta( $sale->order_item_id, '_variation_id', true) );
											?>
											<td>
												<p>
													<?php 
													if($variation) {
														echo $variation->get_formatted_variation_attributes(true); 

													} else {
														_e('Variation no longer exists', 'wc-product-customer-list');
													} ?>
												</p>
											</td>
										<?php } ?>
										<?php if(get_option( 'wpcl_order_qty', 'yes' ) == 'yes') {
											$quantity = $order->get_item_meta( $sale->order_item_id, '_qty', true);
											$productcount[] = $quantity;
											?>
											<td>
												<p>
													<?php echo $quantity; ?>
												</p>
											</td>
										<?php } ?>
									</tr>
						
								
									<?php 
								
							}
							if ( $order->billing_email ) {
								$emaillist[] = $order->billing_email;
							}
						}
						$emaillist = implode( ',', array_unique( $emaillist ) );
						?>
						</tbody>
					</table>
					<?php if(get_option( 'wpcl_order_qty' ) == 'yes') { ?>
						<p class="total">
							<?php echo '<strong>' . __('Total', 'wc-product-customer-list') . ' : </strong>' . array_sum($productcount); ?>
						</p>
					<?php } ?>
					<a href="mailto:?bcc=<?php echo $emaillist; ?>" class="button"><?php _e('Email all customers', 'wc-product-customer-list'); ?></a>
				<?php
				} else {
					_e('This product currently has no customers', 'wc-product-customer-list');
				}
				?>
			</div>
			<?php
		}
	}