	<?php
if(!class_exists('IC_Commerce_Mutlisite_Reporting_Dashboard')){
	
	/*
	 * Class Name IC_Commerce_Mutlisite_Reporting_Dashboard 
	*/
    class IC_Commerce_Mutlisite_Reporting_Dashboard extends IC_Commerce_Mutlisite_Reporting_Functions{
		
		/* variable declaration*/
		var $constants = array();
		
		/*
		 * Function Name __construct
		 *
		 * Initialize Class Default Settings, Assigned Variables
		 *
		 * @param $constants (array) settings
		*/
		function __construct($constants = array()){
			global $wpdb;			
			$this->constants = $constants;
		}
		
		/*
		 * Function Name init
		 *
		 * Initialize Class Default Settings, Assigned Variables, call woocommerce hooks for add  and update order item meta
		 *		 
		*/
		function init(){
				global $wpdb;
				$user 			= wp_get_current_user();
				$user_id 		= $user->ID;			
				$blogs 			= $this->get_blogs_of_user($user_id, true);//$this->print_array($blogs);
				$first_blog_id 	= $this->get_first_blog_id($blogs);
				$default_blog_id= 'all';
				$base_prefix	= $wpdb->base_prefix;
				
				
				$prefix			= $this->get_blog_prefix($first_blog_id,$base_prefix);
				
				$start_date 	= $this->get_request("start_date",	$this->first_order_date($prefix),true);
				$end_date   	= $this->get_request("end_date",	$this->get_date("D"),true);	
				?>
                
            	<h2><?php _e( 'Dashboard','icwoocommerce_textdomains');?></h2>
                <form method="post">
                <div class="ic_box"<?php if(count($blogs)<=0){?> style="display:none;"<?php }?>>
                    <div class="ic_box_header">
                      <h3><?php _e('Site Search','icwoocommerce_textdomains')?></h3>
                    </div>
                    <div class="ic_box_body">
                      <div class="row">
                        <div class="ic_box_space">
                        <div class="error_messange" style="display:none;">
                            <p></p>
                        </div>
                        <div class="ic_box_form_fields prod_name">
                                                      
                          <input type="hidden"  name="searching" 	id="searching" 	value="0"/>                              
                          <input type="hidden"  name="user_id" 		id="user_id" 	value="<?php echo $user_id;?>"/>
                          
                          <?php if(count($blogs)>1){?>
                           <label for="product_name"><?php _e('Site Name','icwoocommerce_textdomains')?>:</label> 
                           
                          <select name="userblog_id" id="userblog_id">
                            <option value="all"><?php _e('All','icwoocommerce_textdomains');?></option>
                            <?php
                                foreach($blogs as $blog){										
                                    $userblog_id 	= $blog->userblog_id;
                                    $blogname 		= $blog->blogname;
                                    if($default_blog_id == $userblog_id){
                                        echo "<option value=\"{$userblog_id}\" selected=\"selected\">{$blogname}</option>";
                                    }else{
                                        echo "<option value=\"{$userblog_id}\">{$blogname}</option>";
                                    }
                                }
                            ?>
                          </select>
                          <?php }else{?>
                            <input type="hidden"  name="userblog_id" id="userblog_id" value="<?php echo $first_blog_id;?>"/>
                          <?php }?>
                        </div>
                            <div class="clear_product"></div>
                            
                            <div class="ic_box_form_fields">
                                <label>Start Date:</label>
                                <div class="form_control">
                                    <input type="text" name="start_date" id="start_date"  class="_proc_date" value="<?php  echo $start_date;?>"  readonly="readonly"/>
                                </div>
                            </div>
                            
                            <div class="ic_box_form_fields">
                                <label>End Date:</label>
                                <div class="form_control">
                                    <input type="text" name="end_date" id="end_date" class="_proc_date" value="<?php  echo $end_date;?>" readonly />
                                </div>
                            </div>
                           
                          
                           <div class="submit_loader">
                              <input type="button" name="btnSearch" class="ic_button" id="btnSearch" value="<?php _e('Refresh')?>" />
                              <span class="please_wait" style="display:none;">
                                  <?php _e("Please Wait!",'icwoocommerce_textdomains')?>
                              </span>
                          </div>
                          
                        </div>
                      </div>
                    </div>
                </div>                
                </form>
                
                <style type="text/css">
                	.ic_plugin_wrap .ic_table-responsive p.item_not_found{ margin-bottom: 8px !important;}
                </style>
                
                <div class="ic_dashboard block_content" style="display:none;">
                
					<!--Summary Boxes-->
					<div class="row ic_summary_box">
						
						<div class="col-sm-3 ic_skyblue_skin">
							<div class="ic_block">
								<span class="ic_refresh_icon"><i class="fa fa-refresh"></i></span>
								<div class="ic_head">
									<span class="ic_icon"><img src="<?php echo $this->constants['plugin_url']?>/images/sales-icon.png" alt=""></span>
									<h3><?php _e('Sales','icwoocommerce_textdomains');?></h3>
								</div>
								
								<div class="ic_block_content">
									<div class="ic_stat">
										<h4><?php _e('ORD. COUNT','icwoocommerce_textdomains');?></h4>
										<span class="total_sales_count"></span>
									</div>
									
									<div class="ic_stat">
										<h4><?php _e('Amount','icwoocommerce_textdomains');?></h4>
										<span class="ic_text total_sales_amount"></span>
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-sm-3 ic_red_skin">
							<div class="ic_block">
								<span class="ic_refresh_icon"><i class="fa fa-refresh"></i></span>
								
								<div class="ic_head">
									<span class="ic_icon"><img src="<?php echo $this->constants['plugin_url']?>/images/refund-icon.png" alt=""></span>
									<h3><?php _e('Refunded','icwoocommerce_textdomains');?></h3>
								</div>
								
								<div class="ic_block_content">
									<div class="ic_stat">
										<h4><?php _e('ORD. COUNT','icwoocommerce_textdomains');?></h4>
										<span class="total_refunded_count"></span>
									</div>
									
									<div class="ic_stat">
										<h4><?php _e('AMOUNT','icwoocommerce_textdomains');?></h4>
										<span class="ic_text total_refunded_amount"></span>
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-sm-3 ic_purple_skin">
							<div class="ic_block">
								<span class="ic_refresh_icon"><i class="fa fa-refresh"></i></span>
								
								<div class="ic_head">
									<span class="ic_icon"><img src="<?php echo $this->constants['plugin_url']?>/images/coupon-icon.png" alt=""></span>
									<h3><?php _e('Discount','icwoocommerce_textdomains');?></h3>
								</div>
								
								<div class="ic_block_content">
									<div class="ic_stat">
										<h4><?php _e('ORD. COUNT','icwoocommerce_textdomains');?></h4>
										<span class="order_discount_count"></span>
									</div>
									
									<div class="ic_stat">
										<h4><?php _e('AMOUNT','icwoocommerce_textdomains');?></h4>
										<span class="ic_text order_discount_amount"></span>
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-sm-3 ic_green_skin">
							<div class="ic_block">
								<span class="ic_refresh_icon"><i class="fa fa-refresh"></i></span>
								
								<div class="ic_head">
									<span class="ic_icon"><img src="<?php echo $this->constants['plugin_url']?>/images/tax-icon.png" alt=""></span>
									<h3><?php _e('Tax','icwoocommerce_textdomains');?></h3>
								</div>
								
								<div class="ic_block_content">
									<div class="ic_stat">
										<h4><?php _e('ORD. COUNT','icwoocommerce_textdomains');?></h4>
										<span class="tax_count"></span>
									</div>
									
									<div class="ic_stat">
										<h4><?php _e('AMOUNT','icwoocommerce_textdomains');?></h4>
										<span class="ic_text tax_amount"></span>
									</div>
								</div>
							</div>
						</div>
						
						<div class="col-sm-3 ic_yellow_skin">
							<div class="ic_block">
								<span class="ic_refresh_icon"><i class="fa fa-refresh"></i></span>
								
								<div class="ic_head">
									<span class="ic_icon"><img src="<?php echo $this->constants['plugin_url']?>/images/shipping-icon.png" alt=""></span>
									<h3><?php _e('Shipping','icwoocommerce_textdomains');?></h3>
								</div>
								
								<div class="ic_block_content">
									<div class="ic_stat">
										<h4><?php _e('ORD. COUNT','icwoocommerce_textdomains');?></h4>
										<span class="shipping_count"></span>
									</div>
									
									<div class="ic_stat">
										<h4><?php _e('AMOUNT','icwoocommerce_textdomains');?></h4>
										<span class="ic_text shipping_amount"></span>
									</div>
								</div>
							</div>
						</div>              
					</div>
								
					<div class="row">
						<div class="col-md-6">
							<div class="ic_box">
								<div class="ic_box_header">
									<h3><?php _e('Site-Wise Sales','icwoocommerce_textdomains')?></h3>
								</div>
								<div class="ic_box_body">
									<div class="ic_table-responsive site_wise_sales_grid"></div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="ic_box">
								<div class="ic_box_header">
									<h3><?php _e('Site-Wise Sales','icwoocommerce_textdomains')?></h3>
								</div>
								<div class="ic_box_body">
									<div class="ic_table-responsive site_wise_sales_chart_div">
										<div class="chart" id="site_wise_sales_chart" style=" height:300px;width:100%;"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-6">
							<div class="ic_box">
								<div class="ic_box_header">
									<h3><?php _e('Low Stock Products','icwoocommerce_textdomains')?></h3>
								</div>
								<div class="ic_box_body">
									<div class="ic_table-responsive low_stock_product_grid"></div>
								</div>                            
							</div>
						</div>
						
						<div class="col-md-6">
							<div class="ic_box">
								<div class="ic_box_header">
									<h3><?php _e('Zero Stock Products','icwoocommerce_textdomains')?></h3>
								</div>
								<div class="ic_box_body">
									<div class="ic_table-responsive zero_stock_products_grid"></div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<div class="ic_box">
								<div class="ic_box_header">
									<h3><?php _e('Top Products','icwoocommerce_textdomains')?></h3>
								</div>
								<div class="ic_box_body">
									<div class="ic_table-responsive top_products_grid"></div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<div class="ic_box">
								<div class="ic_box_header">
									<h3><?php _e('Sales Order Status','icwoocommerce_textdomains')?></h3>
								</div>
								<div class="ic_box_body">
									<div class="ic_table-responsive sales_order_status_grid"></div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<div class="ic_box">
								<div class="ic_box_header">
									<h3><?php _e('Top Categories','icwoocommerce_textdomains')?></h3>
								</div>
								<div class="ic_box_body">
									<div class="ic_table-responsive top_categories_grid"></div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<div class="ic_box">
								<div class="ic_box_header">
									<h3><?php _e('Top Countries','icwoocommerce_textdomains')?></h3>
								</div>
								<div class="ic_box_body">
									<div class="ic_table-responsive top_countries_grid"></div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<div class="ic_box">
								<div class="ic_box_header">
									<h3><?php _e('Top Customers','icwoocommerce_textdomains')?></h3>
								</div>
								<div class="ic_box_body">
									<div class="ic_table-responsive top_customers_grid"></div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<div class="ic_box">
								<div class="ic_box_header">
									<h3><?php _e('Top Coupons','icwoocommerce_textdomains')?></h3>
								</div>
								<div class="ic_box_body">
									<div class="ic_table-responsive top_coupons_grid"></div>
								</div>
							</div>
						</div>
					</div>
					
				</div>
                
                <style type="text/css">
                	th.total_sales, th.quantity, th.order_currency, th.Total, th.Count,th.total_amount, th.OrderCount, th.site_name, th.CompanyName,th.BillingEmail{ width:20%;}
                </style>
            <?php     
		}
		
		function get_option($prefix = '',$option = ''){
			global $wpdb;
			
			$options_table = $prefix.'options';
			
			$wpdb->options = $options_table;
			
			$value = wp_cache_get( $option, $options_table );

			if ( false === $value ) {
				$row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", $option ) );

				// Has to be get_row instead of get_var because of funkiness with 0, false, null values
				if ( is_object( $row ) ) {
					$value = $row->option_value;
					wp_cache_add( $option, $value, $options_table );
				} else { // option does not exist, so we must cache its non-existence
					if ( ! is_array( $notoptions ) ) {
						 $notoptions = array();
					}
					$notoptions[$option] = true;
					wp_cache_set( $prefix.'notoptions', $notoptions, $options_table );

					/** This filter is documented in wp-includes/option.php */
					return apply_filters( "default_option_{$option}", $default, $option, $passed_default );
				}
			}
			
			return apply_filters( "option_{$option}", maybe_unserialize( $value ), $option );
		}
		
		/*
			* Function Name get_dashboard_data
			*
			* @param array $constants
			*
			* return $return 
		*/
		function get_dashboard_data($constants = array()){ 
			global $wpdb,$per_page;			
			$this->constants = $constants;
			
			//error_log($this->print_array($this->constants,false));
			//return '';
			
			$start_date									= $this->constants['start_date'];
			$end_date									= $this->constants['end_date'];	
			$base_prefix								= $wpdb->base_prefix;
			$shop_order_status							= $this->get_selected_order_statusses();
			
			$per_page									= 20;
			
			$userblog_id 								= isset($_REQUEST['userblog_id']) ? $_REQUEST['userblog_id'] : 'all';
			$user_id 									= isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : 0;
			$end_date 									= isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : '';
			$start_date 								= isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : '';
			$return 									= array();
			$count_prefix 								= apply_filters('ic_commerce_count_prefix','#');
			
			if($user_id == 0){
				$user 			= wp_get_current_user();
				$user_id 		= $user->ID;
			}
			
			$blogs 										= $this->get_blogs_of_user($user_id, true);
			
			
			
			if($userblog_id == 'all'){
				/*Testing*/
				//$blog_id 								= $this->get_first_blog_id($blogs);
				//$userblog_id = $blog_id;
			}else{
				$blog_id 								= $userblog_id;
			}
			
			$this->constants['user_id']					= $user_id;
			$this->constants['userblog_id']				= $userblog_id;
			$this->constants['base_prefix']				= $base_prefix;
			
			$return['summary_boxes'] 					= array();
			$site_wise_sales 							= $this->get_site_wise_sales('total',$shop_order_status,$start_date,$end_date);
			$return['site_wise_sales'] 					= $site_wise_sales;
			
			$price_args = array();
			$wc_currency = get_woocommerce_currency();
			$price_args['currency'] = $wc_currency;
			
			if($userblog_id != 'all'){
				switch_to_blog($blog_id);
				
				$this->constants['prefix'] 				= $this->get_blog_prefix($blog_id,$base_prefix);
				$prefix									= $this->constants['prefix'];
				
				
				$wc_currency = $this->get_option($prefix,'woocommerce_currency');
				$price_args['currency'] = $wc_currency;
				
				if (!current_user_can('manage_woocommerce')){
					$return['summary_boxes']['total_refunded_amount']	= '';
					$return['summary_boxes']['total_refunded_count']	= '';
					$return['summary_boxes']['total_sales_amount']		= '';
					$return['summary_boxes']['total_sales_count']		= '';
					
					$return['summary_boxes']['order_discount_amount']	= '';
					$return['summary_boxes']['order_discount_count']	= '';
					
					$return['summary_boxes']['tax_amount']				= '';
					$return['summary_boxes']['tax_count']				= '';
					
					$return['summary_boxes']['shipping_amount']			= '';
					$return['summary_boxes']['shipping_count']			= '';
					
					$return['summary_boxes']['site_wise_sales_grid']	= '';
					$return['summary_boxes']['low_stock_product_grid']	= '';
					$return['summary_boxes']['zero_stock_products_grid']= '';
					$return['summary_boxes']['top_products_grid']		= '';
					$return['summary_boxes']['sales_order_status_grid']	= '';
					$return['summary_boxes']['top_categories_grid']		= '';
					$return['summary_boxes']['top_countries_grid']		= '';
					$return['summary_boxes']['top_customers_grid']		= '';
					$return['summary_boxes']['top_coupons_grid']		= '';
					
					$return['chart_data']								= array();
					$return['site_wise_sales']							= array();
					return $return;
				}
				
				
				
				
				$order_sales							= $this->get_order_sales('total',$prefix,$shop_order_status,$start_date,$end_date);				
				$order_refunded							= $this->get_order_refunded('total',$prefix,$start_date,$end_date);				
				$part_order_refunded					= $this->get_part_order_refunded('total',$prefix,$shop_order_status,$start_date,$end_date);
				$top_products							= $this->get_top_product_list($shop_order_status,$prefix,$start_date,$end_date);
				$sales_order_status						= $this->get_sales_order_status($prefix,$shop_order_status,$start_date,$end_date);
				$top_categories							= $this->get_category_list($prefix,$shop_order_status,$start_date,$end_date);
				$top_countries							= $this->get_top_billing_country($prefix,$shop_order_status,$start_date,$end_date);
				$top_customers							= $this->get_top_customer_list($prefix,$shop_order_status,$start_date,$end_date);
				$top_coupons							= $this->get_top_coupon_list($prefix,$shop_order_status,$start_date,$end_date);
				
				$return['order_sales_amount'] 			= isset($order_sales->amount) ? $order_sales->amount : 0;
				$return['order_sales_count'] 			= isset($order_sales->order_count) ? $order_sales->order_count : 0;
				
				$return['order_refunded_amount'] 		= isset($order_refunded->amount) ? $order_refunded->amount : 0;
				$return['order_refunded_count'] 		= isset($order_refunded->order_count) ? $order_refunded->order_count : 0;
				
				$return['part_order_refunded_amount'] 	= isset($part_order_refunded->amount) ? $part_order_refunded->amount : 0;
				$return['part_order_refunded_count'] 	= isset($part_order_refunded->order_count) ? $part_order_refunded->order_count : 0;
				
				
				$return['total_refunded_amount'] 		= $return['order_refunded_amount'] + $return['part_order_refunded_amount'];
				$return['total_refunded_count'] 		= $return['order_refunded_count'];
				
				$return['total_sales_amount'] 			= $return['order_sales_amount'] - $return['total_refunded_amount'];
				$return['total_sales_count'] 			= $return['order_sales_count'] - $return['total_refunded_count'];
				
				$return['top_products'] 				= $top_products;
				$return['sales_order_status'] 			= $sales_order_status;
				$return['top_categories'] 				= $top_categories;
				$return['top_countries'] 				= $top_countries;
				$return['top_customers'] 				= $top_customers;
				$return['top_coupons'] 					= $top_coupons;
				
				$return['low_stock_product'] 			= $this->get_low_stock_product($prefix,'low_stock_product');				
				$return['zero_stock_products'] 			= $this->get_low_stock_product($prefix,'zero_stock_products');
				
				$order_discount							= $this->get_order_discount('total',$prefix,$shop_order_status,$start_date,$end_date);
				
				$return['order_discount_amount'] 		= isset($order_discount->amount) ? $order_discount->amount : 0;
				$return['order_discount_count'] 		= isset($order_discount->order_count) ? $order_discount->order_count : 0;
				
				$order_tax								= $this->get_total_tax("total","_order_tax","tax",$prefix,$shop_order_status,$start_date,$end_date);
				
				$return['tax_amount'] 					= isset($order_tax->total_amount) ? $order_tax->total_amount : 0;
				$return['tax_count'] 					= isset($order_tax->total_count) ? $order_tax->total_count : 0;
				
				$order_shipping							= $this->get_total_shipping('total',$prefix,$shop_order_status,$start_date,$end_date);		
				
				$return['shipping_amount'] 				= isset($order_shipping->total) ? $order_shipping->total : 0;
				$return['shipping_count'] 				= isset($order_shipping->quantity) ? $order_shipping->quantity : 0;
				
				restore_current_blog();
				
			}else{
				
				$return['total_refunded_amount'] 		= 0;
				$return['total_refunded_count'] 		= 0;				
				$return['total_sales_amount'] 			= 0;
				$return['total_sales_count'] 			= 0;
				
				$return['order_discount_amount'] 		= 0;
				$return['order_discount_count'] 		= 0;
				
				$return['tax_amount'] 					= 0;
				$return['tax_count'] 					= 0;
				
				$return['shipping_amount'] 				= 0;
				$return['shipping_count'] 				= 0;
				
				$return['low_stock_product'] 			= array();
				$return['zero_stock_products'] 			= array();
				$return['top_products'] 				= array();
				$return['sales_order_status'] 			= array();
				$return['top_categories'] 				= array();
				$return['top_countries'] 				= array();
				$return['top_customers'] 				= array();
				$return['top_coupons'] 					= array();
				
				foreach($site_wise_sales as $blog_id => $item){
					$return['total_refunded_amount'] 		= $return['total_refunded_amount'] + $item['refunded_amount'];
					$return['total_refunded_count'] 		= $return['total_refunded_count'] + $item['refunded_count'];
					
					$return['total_sales_amount'] 			= $return['total_sales_amount'] + $item['amoount'];
					$return['total_sales_count'] 			= $return['total_sales_count'] + $item['count'];
				}
				
				foreach($blogs as $blog){					
					$blog_id 								= $blog->userblog_id;
					
					$this->constants['blog_id']				= $blog_id;
					
					$this->constants['prefix'] 				= $this->get_blog_prefix($blog_id,$base_prefix);
					
					$prefix									= $this->constants['prefix'];
					
					$order_discount							= $this->get_order_discount('total',$prefix,$shop_order_status,$start_date,$end_date);				
					$return['order_discount_amount'] 		= isset($order_discount->amount) 		? ($return['order_discount_amount']  + $order_discount->amount) 	: $return['order_discount_amount'];					
					$return['order_discount_count'] 		= isset($order_discount->order_count) 	? ($return['order_discount_count']  + $order_discount->order_count) : $return['order_discount_count'];
					
					$order_tax								= $this->get_total_tax("total","_order_tax","tax",$prefix,$shop_order_status,$start_date,$end_date);				
					$return['tax_amount'] 					= isset($order_tax->total_amount) ? ($return['tax_amount']  + $order_tax->total_amount) 	: $return['tax_amount'];					
					$return['tax_count'] 					= isset($order_tax->total_count) ? ($return['tax_count']  + $order_tax->total_count) : $return['tax_count'];
					
					$order_shipping							= $this->get_total_shipping('total',$prefix,$shop_order_status,$start_date,$end_date);				
					$return['shipping_amount'] 				= isset($order_shipping->total) ? ($return['shipping_amount']  + $order_shipping->total) 	: $return['shipping_amount'];					
					$return['shipping_count'] 				= isset($order_shipping->quantity) ? ($return['shipping_count']  + $order_shipping->quantity) : $return['shipping_count'];
					
					
					$low_stock_product						= $this->get_low_stock_product($prefix,'low_stock_product');
					$return['low_stock_product']			= $this->join_two_array($return['low_stock_product'],$low_stock_product);
					
					$zero_stock_products 					= $this->get_low_stock_product($prefix,'zero_stock_products');
					$return['zero_stock_products']			= $this->join_two_array($return['zero_stock_products'],$zero_stock_products);
					
					$top_products							= $this->get_top_product_list($shop_order_status,$prefix,$start_date,$end_date);
					$return['top_products']					= $this->join_two_array($return['top_products'],$top_products);
					
					$sales_order_status						= $this->get_sales_order_status($prefix,$shop_order_status,$start_date,$end_date);					
					$return['sales_order_status']			= $this->join_two_array($return['sales_order_status'],$sales_order_status);
					
					$top_categories							= $this->get_category_list($prefix,$shop_order_status,$start_date,$end_date);					
					$return['top_categories']				= $this->join_two_array($return['top_categories'],$top_categories);
					
					$top_countries							= $this->get_top_billing_country($prefix,$shop_order_status,$start_date,$end_date);					
					$return['top_countries']				= $this->join_two_array($return['top_countries'],$top_countries);
					
					$top_customers							= $this->get_top_customer_list($prefix,$shop_order_status,$start_date,$end_date);
					$return['top_customers']				= $this->join_two_array($return['top_customers'],$top_customers);
					
					$top_coupons							= $this->get_top_coupon_list($prefix,$shop_order_status,$start_date,$end_date);
					$return['top_coupons']					= $this->join_two_array($return['top_coupons'],$top_coupons);
					
				}
			}
			
			
			
			$return['chart_data'] 					= array();
			$i									= 0;
			foreach($site_wise_sales as $blog_id => $item){
				$return['chart_data'][$i]['amoount']		=  $item['amoount'];
				$return['chart_data'][$i]['blogname']		=  $item['blogname'];
				$i++;
			}
			
			$return['summary_boxes']['total_refunded_amount']	= wc_price($return['total_refunded_amount'],$price_args);
			$return['summary_boxes']['total_refunded_count']	= $count_prefix.$return['total_refunded_count'];
			$return['summary_boxes']['total_sales_amount']		= wc_price($return['total_sales_amount'],$price_args);
			$return['summary_boxes']['total_sales_count']		= $count_prefix.$return['total_sales_count'];
			
			$return['summary_boxes']['order_discount_amount']	= wc_price($return['order_discount_amount'],$price_args);
			$return['summary_boxes']['order_discount_count']	= $count_prefix.$return['order_discount_count'];
			
			$return['summary_boxes']['tax_amount']				= wc_price($return['tax_amount'],$price_args);
			$return['summary_boxes']['tax_count']				= $count_prefix.$return['tax_count'];
			
			$return['summary_boxes']['shipping_amount']			= wc_price($return['shipping_amount'],$price_args);
			$return['summary_boxes']['shipping_count']			= $count_prefix.$return['shipping_count'];
			
			$return['summary_boxes']['site_wise_sales_grid']	= $this->get_grid('site_wise_sales',	$return['site_wise_sales'],		__('Site not found.','icwoocommerce_textdomains'),$wc_currency);
			$return['summary_boxes']['low_stock_product_grid']	= $this->get_grid('low_stock_product',	$return['low_stock_product'],	__('Low level stock not found.','icwoocommerce_textdomains'),$wc_currency);
			$return['summary_boxes']['zero_stock_products_grid']= $this->get_grid('zero_stock_products',$return['zero_stock_products'],	__('Zero level stock not found.','icwoocommerce_textdomains'),$wc_currency);
			
			$return['summary_boxes']['top_products_grid']		= $this->get_grid('top_products',		$return['top_products'],		__('Products not found.','icwoocommerce_textdomains'),$wc_currency);
			
			$return['summary_boxes']['sales_order_status_grid']	= $this->get_grid('sales_order_status',	$return['sales_order_status'], __('Order Status not found.','icwoocommerce_textdomains'),$wc_currency);
			
			$return['summary_boxes']['top_categories_grid']		= $this->get_grid('top_categories',	$return['top_categories'],		__('Categories not found.','icwoocommerce_textdomains'),$wc_currency);
			
			$return['summary_boxes']['top_countries_grid']		= $this->get_grid('top_countries',	$return['top_countries'],		__('Countries not found.','icwoocommerce_textdomains'),$wc_currency);
			
			$return['summary_boxes']['top_customers_grid']		= $this->get_grid('top_customers',	$return['top_customers'],		__('Customers not found.','icwoocommerce_textdomains'),$wc_currency);
			
			$return['summary_boxes']['top_coupons_grid']		= $this->get_grid('top_coupons',	$return['top_coupons'],			__('Coupons not found.','icwoocommerce_textdomains'),$wc_currency);
			
			return $return;
		}
		
		
		/*
			* Function Name get_columns
			*
			* @param string $retport_name
			*
			* return $columns
		*/
		function get_columns($retport_name = ''){
			$columns =  array();
			
			switch($retport_name){
				case "site_wise_sales":
					$columns['blogname'] 			= __('Site Name','icwoocommerce_textdomains');
					$columns['count'] 				= __('Order Count','icwoocommerce_textdomains');
					$columns['amoount'] 			= __('Order Total','icwoocommerce_textdomains');
					break;
				case "low_stock_product":
				case "zero_stock_products":
					$columns['stock_product_name'] 	= __('Product Name','icwoocommerce_textdomains');
					$columns['site_name'] 			= __('Site Name','icwoocommerce_textdomains');
					$columns['product_stock'] 		= __('Stock Count','icwoocommerce_textdomains');
					break;
				case "top_products":
					$columns['product_name'] 		= __('Product Name','icwoocommerce_textdomains');
					$columns['site_name'] 			= __('Site Name','icwoocommerce_textdomains');
					$columns['order_currency'] 		= __('Currency','icwoocommerce_textdomains');
					$columns['quantity'] 			= __('Quantity Sold','icwoocommerce_textdomains');
					$columns['total_sales'] 		= __('Sales Amount','icwoocommerce_textdomains');
					break;
				case "sales_order_status":
					$columns['Status'] 				= __('Order Status','icwoocommerce_textdomains');
					$columns['site_name'] 			= __('Site Name','icwoocommerce_textdomains');
					$columns['order_currency'] 		= __('Currency','icwoocommerce_textdomains');
					$columns['Count'] 				= __('Order Count','icwoocommerce_textdomains');
					$columns['Total'] 				= __('Amount','icwoocommerce_textdomains');
					break;
				case "top_categories":
					$columns['category_name'] 		= __('Category Name','icwoocommerce_textdomains');
					$columns['site_name'] 			= __('Site Name','icwoocommerce_textdomains');
					$columns['order_currency'] 		= __('Currency','icwoocommerce_textdomains');
					$columns['quantity'] 			= __('Quantity Sold','icwoocommerce_textdomains');
					$columns['total_amount'] 		= __('Sales Amount','icwoocommerce_textdomains');
					break;
				case "top_countries":
					$columns['BillingCountry'] 		= __('Country Name','icwoocommerce_textdomains');
					$columns['site_name'] 			= __('Site Name','icwoocommerce_textdomains');
					$columns['order_currency'] 		= __('Currency','icwoocommerce_textdomains');
					$columns['OrderCount'] 			= __('Order Count','icwoocommerce_textdomains');
					$columns['Total'] 				= __('Amount','icwoocommerce_textdomains');
					break;
				case "top_customers":
					$columns['billing_name'] 		= __('Billing Name','icwoocommerce_textdomains');
					$columns['site_name'] 			= __('Site Name','icwoocommerce_textdomains');
					$columns['CompanyName'] 		= __('Company Name','icwoocommerce_textdomains');
					$columns['BillingEmail'] 		= __('Billing Email','icwoocommerce_textdomains');
					$columns['OrderCount'] 			= __('Order Count','icwoocommerce_textdomains');					
					break;
				case "top_coupons":
					$columns['order_item_name'] 	= __('Coupon Code','icwoocommerce_textdomains');
					$columns['site_name'] 			= __('Site Name','icwoocommerce_textdomains');
					$columns['order_currency'] 		= __('Currency','icwoocommerce_textdomains');
					$columns['Count'] 				= __('Coupon Used Count','icwoocommerce_textdomains');
					$columns['Total'] 				= __('Amount','icwoocommerce_textdomains');					
					break;
				
			}
			
			if($this->constants['userblog_id'] != 'all'){
				unset($columns['site_name']);
			}
			return $columns;
		}
		
		/*
			* Function Name get_grid
			*
			* @param string $retport_name
			*
			* @param array $items
			*
			* @param string $item_not_found_text
			*
			* return $output
		*/
		function get_grid($retport_name = '',$items = array(),$item_not_found_text = '',$wc_currency = 'USD'){
			$output = "";
			if(count($items) > 0){
				$columns = $this->get_columns($retport_name);	
				
				$output .= '<div class="scroll">';
				$output .= '<table style="width:100%;" class="widefat ic_table example display responsive nowrap" cellpadding="0" cellspacing="0">';
						$output .= '<thead>';
							$output .= '<tr class="first">';							
									foreach($columns as $key => $value):
										$td_class = $key;
										$td_width = "";
										$th_value = $value;
										switch($key):
											case "count":
											case "amoount":
											case "Count":
											case "OrderCount":
											case "Total":
											case "product_stock":
											case "quantity":
											case "total_amount":
											case "total_sales":
												$td_class .= " align_right";												
												break;							
											default;
												break;
										endswitch;
										$th_value 	= $value;
										$output 	.= "\n\t<th class=\"{$td_class}\">{$th_value}</th>";											
									endforeach;
							$output .= '</tr>';
						$output .= '</thead>';
						$output .= '<tbody>';
							$i = 0;
							foreach ( $items as $key => $order_item){
								
									if($i%2 == 1){$alternate = "alternate ";}else{$alternate = "";};
									
									$output .= '<tr class="'.$alternate.'row_'.$i.'">';								
										foreach($columns as $key => $value):
											$td_class = $key;
											$td_style = '';	
											$td_value = '';
																				
											switch($key):											
												case "blogname":
													$td_value = isset($order_item[$key]) ? $order_item[$key] : '';
													break;
												case "count":
												case "Count":	
												case "OrderCount":											
												case "product_stock":
												case "quantity":										
													$td_class .= " align_right";
													$td_value = isset($order_item[$key]) ? $order_item[$key] : '';
													break;
												case "amoount":	
												case "Total":
												case "total_amount":
												case "total_sales":											
													$td_class .= " align_right";
													$td_value = isset($order_item[$key]) ? $order_item[$key] : 0;
													$order_currency = isset($order_item['order_currency']) ? $order_item['order_currency'] : $wc_currency;
													$td_value = wc_price($td_value,array('currency'=>$order_currency));
													break;
												default:
													$td_value = isset($order_item[$key]) ? $order_item[$key] : '';
													break;
											endswitch;
											$output .= "<td class=\"{$td_class}\"{$td_style}>{$td_value}</td>\n";													
										endforeach; 
									$output .= '</tr>';
									$i++;
							}
						$output .= '</tbody>';
					$output .= '</table><br>';
					$output .= '</div>';
			}else{
				$output = "<p class=\"item_not_found\">{$item_not_found_text}</p>";
			}	
			return $output;
		}
		
		/*
			* Function Name get_site_wise_sales
			*
			* @param string $type
			*
			* @param array $shop_order_status
			*
			* @param string $start_date
			*
			* @param string $end_date
			*
			* return $items
		*/
		function get_site_wise_sales($type = 'total',$shop_order_status = array(),$start_date = '',$end_date = ''){
			global $wpdb;
			$user_id 									= $this->constants['user_id'];
			$base_prefix								= $this->constants['base_prefix'];
			$blogs 										= $this->get_blogs_of_user($user_id, true);
			$items 										= array();
			$i											= 0;
			
			foreach($blogs as $blog){
				
				$blog_id 								= $blog->userblog_id;				
				$prefix 								 = $this->get_blog_prefix($blog_id,$base_prefix);
				$woocommerce_currency 				   = $this->get_option($prefix,'woocommerce_currency');
				
				$return 								 = array();
				
				$order_sales							= $this->get_order_sales('total',$prefix,$shop_order_status,$start_date,$end_date);				
				$order_refunded						 = $this->get_order_refunded('total',$prefix,$start_date,$end_date);				
				$part_order_refunded					= $this->get_part_order_refunded('total',$prefix,$shop_order_status,$start_date,$end_date);
				
				$return['order_sales_amount'] 			= isset($order_sales->amount) ? $order_sales->amount : 0;
				$return['order_sales_count'] 			= isset($order_sales->order_count) ? $order_sales->order_count : 0;
				
				$return['order_refunded_amount'] 		= isset($order_refunded->amount) ? $order_refunded->amount : 0;
				$return['order_refunded_count'] 		= isset($order_refunded->order_count) ? $order_refunded->order_count : 0;
				
				$return['part_order_refunded_amount'] 	= isset($part_order_refunded->amount) ? $part_order_refunded->amount : 0;
				$return['part_order_refunded_count'] 	= isset($part_order_refunded->order_count) ? $part_order_refunded->order_count : 0;
				
				$return['total_refunded_amount'] 		= $return['order_refunded_amount'] + $return['part_order_refunded_amount'];
				$return['total_refunded_count'] 		= $return['order_refunded_count'];
				
				$return['total_sales_amount'] 			= $return['order_sales_amount'] - $return['total_refunded_amount'];
				$return['total_sales_count'] 			= $return['order_sales_count'] - $return['total_refunded_count'];
				
				$items[$blog_id]['blogname'] 			= $blog->blogname;
				$items[$blog_id]['blog_id'] 			= $blog_id;
				$items[$blog_id]['amoount'] 			= $return['total_sales_amount'];
				$items[$blog_id]['count'] 				= $return['total_sales_count'];
				
				$items[$blog_id]['order_currency'] 	   = $woocommerce_currency ;
				$items[$blog_id]['refunded_amount'] 	  = $return['total_refunded_amount'];
				$items[$blog_id]['refunded_count'] 	   = $return['total_refunded_count'];
			}
			
			return $items;
		}
		
		/*
			* Function Name get_order_sales
			*
			* @param string $type
			*
			* @param string $prefix
			*
			* @param array $shop_order_status
			*
			* @param string $start_date
			*
			* @param string $end_date
			*
			* return $items
		*/
		function get_order_sales($type = 'total',$prefix = '',$shop_order_status = array(),$start_date = '',$end_date = ''){
			global $wpdb;
			
			$today_date 			= $this->constants['yesterday_date'];
			
			$yesterday_date 		= $this->constants['today_date'];
			
			$sql = "   SELECT ";
			
			
			$sql .= " SUM(order_total.meta_value) 	AS amount";
			
			$sql .= " , COUNT(*) 					AS order_count";
			
			$sql .= ", order_currency.meta_value 	AS order_currency";
			
			$sql .= "  FROM {$prefix}posts 			AS posts";
			
			$sql .= " LEFT JOIN  {$prefix}postmeta as order_total ON order_total.post_id = posts.ID";
			
			$sql .= " LEFT JOIN  {$prefix}postmeta as order_currency ON order_currency.post_id = posts.ID";
			
			$sql .= " WHERE  post_type = 'shop_order'";
			
			$sql .= " AND order_total.meta_key = '_order_total'";
			
			$sql .= " AND order_currency.meta_key = '_order_currency'";
			
			if($type == "today") 		$sql .= " AND DATE(posts.post_date) = '{$today_date}'";
			
			if($type == "yesterday") 	$sql .= " AND DATE(posts.post_date) = '{$yesterday_date}'";
			
			if(count($shop_order_status)>0){
				$in_shop_order_status		= implode("', '",$shop_order_status);
				$sql .= " AND  posts.post_status IN ('{$in_shop_order_status}')";
			}
			
			if ($start_date != NULL &&  $end_date != NULL && $type != "today"){
				$sql .= " AND DATE(posts.post_date) BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			
			$wpdb->query("SET SQL_BIG_SELECTS=1");
			
			if($type == "today_yesterday"){
				$sql .= " GROUP BY group_date";
				$items =  $wpdb->get_results($sql);				
			}else{
				$items =  $wpdb->get_row($sql);
			}
			
			//error_log($sql);
			
			return $items;
		}
		
		/*
			* Function Name get_order_refunded
			*
			* @param string $type
			*
			* @param string $prefix
			*
			* @param string $start_date
			*
			* @param string $end_date
			*
			* return $items
		*/
		function get_order_refunded($type = 'today',$prefix,$start_date,$end_date)	{
			global $wpdb;
			
			$today_date 			= $this->constants['yesterday_date'];
			
			$yesterday_date 		= $this->constants['today_date'];
			
			$date_field				= 'post_date';
			
			$sql = "   SELECT ";
			
			$sql .= " SUM( order_total.meta_value) As 'amount'";
			
			$sql .= ", COUNT(*) AS 'order_count'";
			
			$sql .= " FROM {$prefix}posts as posts";
			
			$sql .= " LEFT JOIN  {$prefix}postmeta as order_total ON order_total.post_id=posts.ID";
			
			$sql .= " WHERE  post_type = 'shop_order'";
			
			$sql .= " AND order_total.meta_key = '_order_total'";
						
			if($type == "today" || $type == "today") $sql .= " AND DATE(posts.{$date_field}) = '".$today_date."'";
			
			if($type == "yesterday") $sql .=" AND DATE(posts.{$date_field}) = '".$yesterday_date."'";
			
			if ($start_date != NULL &&  $end_date != NULL && $type != "today"){
				$sql .= " AND DATE(posts.{$date_field}) BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			
			$sql .= " AND  posts.post_status IN ('wc-refunded')";
			
			$wpdb->query("SET SQL_BIG_SELECTS=1");
			
			$items =  $wpdb->get_row($sql);
						
			return $items;
		
		}
		
		/*
			* Function Name get_part_order_refunded
			*
			* @param string $type
			*
			* @param string $prefix
			*
			* @param array $shop_order_status
			*
			* @param string $start_date
			*
			* @param string $end_date
			*
			* return $order_items
		*/
		function get_part_order_refunded($type = "today",$prefix,$shop_order_status,$start_date,$end_date){
			global $wpdb;
			
			$today_date 			= $this->constants['yesterday_date'];
			
			$yesterday_date 		= $this->constants['today_date'];
			
			$sql = " SELECT SUM(postmeta.meta_value) 		AS amount";
			
			$sql .= " , COUNT(*) 							AS order_count";
					
			$sql .= " FROM {$prefix}posts as posts";
							
			$sql .= " LEFT JOIN  {$prefix}postmeta as postmeta ON postmeta.post_id	=	posts.ID";
			
			$sql .= " LEFT JOIN  {$prefix}posts as shop_order ON shop_order.ID	=	posts.post_parent";
			
			$sql .= " WHERE posts.post_type = 'shop_order_refund' AND  postmeta.meta_key='_refund_amount'";
			
			$sql .= " AND shop_order.post_type = 'shop_order'";
			
			$sql .= " AND shop_order.post_status NOT IN ('wc-refunded')";
			
			if(count($shop_order_status)>0){
				$in_shop_order_status		= implode("', '",$shop_order_status);
				$sql .= " AND  shop_order.post_status IN ('{$in_shop_order_status}')";
			}
			
			if ($start_date != NULL &&  $end_date != NULL && $type == "total"){
				$sql .= " AND DATE(posts.post_date) BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			
			if($type == "today") $sql .= " AND DATE(posts.post_date) = '{$today_date}'";
			
			if($type == "yesterday") 	$sql .= " AND DATE(posts.post_date) = '{$yesterday_date}'";				
			
			$sql .= " LIMIT 1";
		
			$wpdb->query("SET SQL_BIG_SELECTS=1");
			
			$order_items = $wpdb->get_row($sql);
			
			return $order_items;
			
		}
		
		/*
			* Function Name get_top_product_list
			*
			* @param string $prefix
			*
			* @param array $shop_order_status
			*
			* @param string $start_date
			*
			* @param string $end_date
			*
			* return $return
		*/
		function get_top_product_list($shop_order_status = array(),$prefix = '',$start_date = '',$end_date = ''){
			global $wpdb, $per_page;					
		
			$sql = "
			SELECT 
			woocommerce_order_items.order_item_name			AS product_name
			,woocommerce_order_items.order_item_id			AS order_item_id
			,woocommerce_order_itemmeta3.meta_value			AS product_id
			,SUM(woocommerce_order_itemmeta.meta_value)		AS quantity
			,SUM(woocommerce_order_itemmeta2.meta_value)	AS total_sales
			
			
			, order_currency.meta_value 	AS order_currency
									
			FROM 		{$prefix}woocommerce_order_items 		as woocommerce_order_items
			LEFT JOIN	{$prefix}posts							as posts 						ON posts.ID										=	woocommerce_order_items.order_id
			LEFT JOIN	{$prefix}woocommerce_order_itemmeta 	as woocommerce_order_itemmeta 	ON woocommerce_order_itemmeta.order_item_id		=	woocommerce_order_items.order_item_id
			LEFT JOIN	{$prefix}woocommerce_order_itemmeta 	as woocommerce_order_itemmeta2 	ON woocommerce_order_itemmeta2.order_item_id	=	woocommerce_order_items.order_item_id
			LEFT JOIN	{$prefix}woocommerce_order_itemmeta 	as woocommerce_order_itemmeta3 	ON woocommerce_order_itemmeta3.order_item_id	=	woocommerce_order_items.order_item_id
			
			";
			
			$sql .= " LEFT JOIN  {$prefix}postmeta as order_currency ON order_currency.post_id = posts.ID";
			
			$sql .= "
			WHERE
			posts.post_type 								=	'shop_order'
			AND woocommerce_order_itemmeta.meta_key			=	'_qty'
			AND woocommerce_order_itemmeta2.meta_key		=	'_line_total' 
			AND woocommerce_order_itemmeta3.meta_key 		=	'_product_id'";
			
			$sql .= " AND order_currency.meta_key = '_order_currency'";
			
			$url_shop_order_status	= "";
			
			if(count($shop_order_status)>0){
				$in_shop_order_status		= implode("', '",$shop_order_status);
				$sql .= " AND  posts.post_status IN ('{$in_shop_order_status}')";
			}
			
			if ($start_date != NULL &&  $end_date !=NULL){
				$sql .= " AND DATE(posts.post_date) BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			
			$sql .= " 
			
			GROUP BY  product_id,order_currency.meta_value
			Order By total_sales DESC
			LIMIT {$per_page}";
			
			$order_items = $wpdb->get_results($sql);
			
			$return = $this->convert_object_to_array($order_items);
			
			if($this->constants['userblog_id'] == 'all'){
				$return  = $this->set_site_name_to_list($return);
			}
			return $return;
		}
		
		/*
			* Function Name get_low_stock_product
			*
			* @param string $prefix
			*
			* @param string $report_name
			*
			* return $return
		*/
		function get_low_stock_product($prefix = '',$report_name = 'low_stock_product'){
			global $wpdb, $per_page;
			
			
			$stock_less_than = 0;
				
			if($report_name == 'low_stock_product'){
				$stock_less_than = get_option('woocommerce_notify_low_stock_amount');
			}
			
			$stock_less_than = isset($_REQUEST['stock_less_than']) ? $_REQUEST['stock_less_than'] : $stock_less_than;
			
			$stock_less_than 		=	$stock_less_than +  0;
		
			$sql = "SELECT ";
			
			$sql .= " product.ID 										AS product_id";
			
			$sql .= ", product.post_parent								AS product_parent";
			
			$sql .= ", product.post_title 								AS stock_product_name";
			
			$sql .= ", product.post_type 								AS post_type";
			
			$sql .= ", manage_stock.meta_value 							AS manage_stock";
			
			$sql .= ", (stock.meta_value + 0) 							AS product_stock";
			
			$sql .= " FROM {$prefix}posts 						AS product";
			
			$sql .= " LEFT JOIN {$prefix}postmeta AS manage_stock ON manage_stock.post_id = product.ID AND manage_stock.meta_key = '_manage_stock'";
			
			$sql .= " LEFT JOIN {$prefix}postmeta AS stock ON stock.post_id = product.ID AND stock.meta_key = '_stock'";
			
			$sql .= " WHERE product.post_type IN ('product','product_variation')";
			
			$sql .= " AND product.post_status IN ('publish')";
			
			$sql .= " AND manage_stock.meta_value = 'yes'";
			
			$order = "ASC";
			if($report_name == "most_stocked"){					
				$sql .= " AND stock.meta_value > {$most_stocked}";
				$order = "DESC";
			}else if($report_name == 'low_stock_product'){
				if(strlen($stock_less_than) > 0){
					$sql .= " AND (stock.meta_value >= 1 AND stock.meta_value <= {$stock_less_than})";
				}else{
					$sql .= " AND (stock.meta_value <= 2 AND stock.meta_value >= 1)";
				}
			}else if($report_name == "zero_stock_products"){
				$sql .= " AND (stock.meta_value <= 0)";
			}else{
				if(strlen($stock_less_than) > 0){
					$sql .= " AND stock.meta_value <= {$stock_less_than}";
				}
			}
			
			$sql .= " GROUP BY product_id";
			
			$sql .= " ORDER BY product_stock";
			
			$return = $wpdb->get_results($sql );
			
			$return = $this->convert_object_to_array($return);
			if($this->constants['userblog_id'] == 'all'){
				$return  = $this->set_site_name_to_list($return);
			}
			
			return $return;
		}
		
		/*
			* Function Name get_order_discount
			*
			* @param string $type
			*
			* @param string $prefix
			*
			* @param array $shop_order_status
			*
			* @param string $start_date
			*
			* @param string $end_date
			*
			* return $items
		*/
		function get_order_discount($type = 'total',$prefix = '',$shop_order_status = array(),$start_date = '',$end_date = ''){
			global $wpdb;
			
			$today_date 			= $this->constants['yesterday_date'];
			
			$yesterday_date 		= $this->constants['today_date'];
			
			$sql = "   SELECT ";
			
			
			$sql .= " SUM(cart_discount.meta_value) 	AS amount";
			
			$sql .= " , COUNT(*) 					AS order_count";
			
			$sql .= "  FROM {$prefix}posts 			AS posts";
			
			$sql .= " LEFT JOIN  {$prefix}postmeta as cart_discount ON cart_discount.post_id = posts.ID";
			
			$sql .= " WHERE  post_type = 'shop_order'";
			
			$sql .= " AND cart_discount.meta_key = '_cart_discount'";
			
			$sql .= " AND cart_discount.meta_value != 0";
			
			if($type == "today") 		$sql .= " AND DATE(posts.post_date) = '{$today_date}'";
			
			if($type == "yesterday") 	$sql .= " AND DATE(posts.post_date) = '{$yesterday_date}'";
			
			if(count($shop_order_status)>0){
				$in_shop_order_status		= implode("', '",$shop_order_status);
				$sql .= " AND  posts.post_status IN ('{$in_shop_order_status}')";
			}
			
			$sql .= " AND  posts.post_status NOT IN ('wc-refunded')";
			
			if ($start_date != NULL &&  $end_date != NULL && $type != "today"){
				$sql .= " AND DATE(posts.post_date) BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			
			$wpdb->query("SET SQL_BIG_SELECTS=1");
			
			if($type == "today_yesterday"){
				$sql .= " GROUP BY group_date";
				$items =  $wpdb->get_results($sql);				
			}else{
				$items =  $wpdb->get_row($sql);
			}
			
			return $items;
		}
		
		/*
			* Function Name get_total_tax
			*
			* @param string $type
			*
			* @param string $meta_key
			*
			* @param string $order_item_type
			*
			* @param string $prefix
			*
			* @param array $shop_order_status
			*
			* @param string $start_date
			*
			* @param string $end_date
			*
			* return $order_items
		*/
		function get_total_tax($type = "today", $meta_key="_order_tax",$order_item_type="tax",$prefix = '',$shop_order_status,$start_date,$end_date){
			global $wpdb;
			$today_date 			= $this->today;
			$yesterday_date 		= $this->yesterday;
			
			$sql = "  SELECT";
			$sql .= " SUM(postmeta1.meta_value) 	AS 'total_amount'";
			$sql .= " ,count(posts.ID) 				AS 'total_count'";
			$sql .= " FROM {$prefix}posts as posts";			
			$sql .= " LEFT JOIN  {$prefix}postmeta as postmeta1 ON postmeta1.post_id=posts.ID";
			
			if($this->constants['post_order_status_found'] == 0 ){
				if(count($shop_order_status)>0){
					$sql .= " 
					LEFT JOIN  {$prefix}term_relationships 	as term_relationships 	ON term_relationships.object_id		=	posts.ID
					LEFT JOIN  {$prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	term_relationships.term_taxonomy_id";
				}
			}
			
			$sql .= " WHERE postmeta1.meta_key = '{$meta_key}' AND posts.post_type = 'shop_order' AND postmeta1.meta_value > 0";
							
			$sql .= " AND posts.post_type='shop_order' ";
			
			if($type == "today") $sql .= " AND DATE(posts.post_date) = '{$today_date}'";
			if($type == "yesterday") 	$sql .= " AND DATE(posts.post_date) = '{$yesterday_date}'";
			
			if($this->constants['post_order_status_found'] == 0 ){
				if(count($shop_order_status)>0){
					$in_shop_order_status = implode(",",$shop_order_status);
					$sql .= " AND  term_taxonomy.term_id IN ({$in_shop_order_status})";
				}
			}else{
				if(count($shop_order_status)>0){
					$in_shop_order_status		= implode("', '",$shop_order_status);
					$sql .= " AND  posts.post_status IN ('{$in_shop_order_status}')";
				}
			}
			
			if ($start_date != NULL &&  $end_date != NULL && $type != "today"){
				$sql .= " AND DATE(posts.post_date) BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			
			return $order_items = $wpdb->get_row($sql);
			
			
		}
		
		/*
			* Function Name get_total_shipping
			*
			* @param string $type
			*
			* @param string $prefix
			*
			* @param array $shop_order_status
			*
			* @param string $start_date
			*
			* @param string $end_date
			*
			* return $order_items
		*/
		function get_total_shipping($type = 'total',$prefix = '',$shop_order_status,$start_date,$end_date){
			global $wpdb;
			$today_date 			= $this->today;
			$yesterday_date 		= $this->yesterday;
			
			$id = "_order_shipping";
			$sql = "
			SELECT 					
			SUM(postmeta2.meta_value)		as total
			,COUNT(posts.ID) 				as quantity
			FROM {$prefix}posts as posts					
			LEFT JOIN	{$prefix}postmeta as postmeta2 on postmeta2.post_id = posts.ID";
			
			if($this->constants['post_order_status_found'] == 0 ){
				if(count($shop_order_status)>0){
					$sql .= " 
					LEFT JOIN  {$prefix}term_relationships 	as term_relationships 	ON term_relationships.object_id		=	posts.ID
					LEFT JOIN  {$prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	term_relationships.term_taxonomy_id";
				}
			}
			
			$sql .= " WHERE posts.post_type	= 'shop_order'";
			$sql .= " AND postmeta2.meta_value > 0";
			$sql .= " AND postmeta2.meta_key 	= '{$id}'";
			
			
			if($type == "today") $sql .= " AND DATE(posts.post_date) = '{$today_date}'";
			if($type == "yesterday") 	$sql .= " AND DATE(posts.post_date) = '{$yesterday_date}'";
			if($this->constants['post_order_status_found'] == 0 ){
				if(count($shop_order_status)>0){
					$in_shop_order_status = implode(",",$shop_order_status);
					$sql .= " AND  term_taxonomy.term_id IN ({$in_shop_order_status})";
				}
			}else{
				if(count($shop_order_status)>0){
					$in_shop_order_status		= implode("', '",$shop_order_status);
					$sql .= " AND  posts.post_status IN ('{$in_shop_order_status}')";
				}
			}
			
			if ($start_date != NULL &&  $end_date != NULL && $type == "total"){
				$sql .= " AND DATE(posts.post_date) BETWEEN '{$start_date}' AND '{$end_date}'";
			}
									
			return $order_items = $wpdb->get_row($sql);
			
			//return isset($order_items->total) ? $order_items->total : 0;
		}
			
		/*
			* Function Name get_sales_order_status
			*
			* @param string $prefix
			*
			* @param array $shop_order_status
			*
			* @param string $start_date
			*
			* @param string $end_date
			*
			* return $return
		*/	
		function get_sales_order_status($prefix = '',$shop_order_status = array(),$start_date = '',$end_date = ''){
			
			global $wpdb;
			
			$sql = "SELECT			
			COUNT(postmeta.meta_value) AS 'Count'
			,SUM(postmeta.meta_value) AS 'Total'";
			$sql .= "  ,posts.post_status As 'Status' ,posts.post_status As 'StatusID'";
			
			$sql .= ", order_currency.meta_value 	AS order_currency";
			
			$sql .= "  FROM {$prefix}posts as posts";
			
			$sql .= "
			LEFT JOIN  {$prefix}postmeta as postmeta ON postmeta.post_id=posts.ID";
			$sql .= " LEFT JOIN  {$prefix}postmeta as order_currency ON order_currency.post_id = posts.ID";
			$sql .= "
			WHERE postmeta.meta_key = '_order_total'  AND posts.post_type='shop_order' ";
			
			if ($start_date != NULL &&  $end_date !=NULL){
				$sql .= " AND DATE(posts.post_date) BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			
			if(count($shop_order_status)>0){
				$in_shop_order_status		= implode("', '",$shop_order_status);
				$sql .= " AND  posts.post_status IN ('{$in_shop_order_status}')";
			}
			
			$sql .= " AND order_currency.meta_key = '_order_currency'";
			
			$sql .= " Group BY posts.post_status,order_currency.meta_value ORDER BY Total DESC";
			
			$order_items = $wpdb->get_results($sql);
			
			if(count($order_items)>0){
			
				if(function_exists('wc_get_order_statuses')){
					$order_statuses = wc_get_order_statuses();
				}else{
					$order_statuses = array();
				}
				
				foreach($order_items as $key  => $value){
					$order_items[$key]->Status = isset($order_statuses[$value->Status]) ? $order_statuses[$value->Status] : $value->Status;
				}
			}
			
			$return = $this->convert_object_to_array($order_items);
			
			if($this->constants['userblog_id'] == 'all'){
				$return  = $this->set_site_name_to_list($return);
			}		
					
			return $return;
			
		}
		
		/*
			* Function Name get_category_list
			*
			* @param string $prefix
			*
			* @param array $shop_order_status
			*
			* @param string $start_date
			*
			* @param string $end_date
			*
			* return $return
		*/
		function get_category_list($prefix = '',$shop_order_status = array(),$start_date = '',$end_date = ''){
			global $wpdb;
			$per_page 	= 20;
			
			$sql ="";
			$sql .= " SELECT ";
			$sql .= " SUM(woocommerce_order_itemmeta_product_qty.meta_value) AS quantity";
			$sql .= " ,SUM(woocommerce_order_itemmeta_product_line_total.meta_value) AS total_amount";
			$sql .= " ,terms_product_id.term_id AS category_id";
			$sql .= " ,terms_product_id.name AS category_name";
			$sql .= " ,term_taxonomy_product_id.parent AS parent_category_id";
			$sql .= " ,terms_parent_product_id.name AS parent_category_name";
			
			$sql .= ", order_currency.meta_value 	AS order_currency";
			
			$sql .= " FROM {$prefix}woocommerce_order_items as woocommerce_order_items";
			
			$sql .= " LEFT JOIN  {$prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta_product_id ON woocommerce_order_itemmeta_product_id.order_item_id=woocommerce_order_items.order_item_id";
			$sql .= " LEFT JOIN  {$prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta_product_qty ON woocommerce_order_itemmeta_product_qty.order_item_id=woocommerce_order_items.order_item_id";
			$sql .= " LEFT JOIN  {$prefix}woocommerce_order_itemmeta as woocommerce_order_itemmeta_product_line_total ON woocommerce_order_itemmeta_product_line_total.order_item_id=woocommerce_order_items.order_item_id";
			
			
			$sql .= " 	LEFT JOIN  {$prefix}term_relationships 	as term_relationships_product_id 	ON term_relationships_product_id.object_id		=	woocommerce_order_itemmeta_product_id.meta_value 
						LEFT JOIN  {$prefix}term_taxonomy 		as term_taxonomy_product_id 		ON term_taxonomy_product_id.term_taxonomy_id	=	term_relationships_product_id.term_taxonomy_id
						LEFT JOIN  {$prefix}terms 				as terms_product_id 				ON terms_product_id.term_id						=	term_taxonomy_product_id.term_id";
			
			$sql .= " 	LEFT JOIN  {$prefix}terms 				as terms_parent_product_id 				ON terms_parent_product_id.term_id						=	term_taxonomy_product_id.parent";
			
			$sql .= " LEFT JOIN  {$prefix}posts as posts ON posts.id=woocommerce_order_items.order_id";
			
			$sql .= " LEFT JOIN  {$prefix}postmeta as order_currency ON order_currency.post_id = posts.ID";
				
			$sql .= " WHERE 1*1 ";
			$sql .= " AND woocommerce_order_items.order_item_type 					= 'line_item'";
			$sql .= " AND woocommerce_order_itemmeta_product_id.meta_key 			= '_product_id'";
			$sql .= " AND woocommerce_order_itemmeta_product_qty.meta_key 			= '_qty'";
			$sql .= " AND woocommerce_order_itemmeta_product_line_total.meta_key 	= '_line_total'";
			$sql .= " AND term_taxonomy_product_id.taxonomy 						= 'product_cat'";
			$sql .= " AND posts.post_type 											= 'shop_order'";				
			
			$sql .= " AND order_currency.meta_key = '_order_currency'";
			
			$url_shop_order_status	= "";
					
			if(count($shop_order_status)>0){
				$in_shop_order_status		= implode("', '",$shop_order_status);
				$sql .= " AND  posts.post_status IN ('{$in_shop_order_status}')";
			}
			
			if ($start_date != NULL &&  $end_date !=NULL){
				$sql .= " AND DATE(posts.post_date) BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			
			$sql .= " GROUP BY category_id,order_currency.meta_value";
			$sql .= " Order By total_amount DESC";
			$sql .= " LIMIT {$per_page}";
			 
			$order_items = $wpdb->get_results($sql); 
			
			$return = $this->convert_object_to_array($order_items);
		
			if($this->constants['userblog_id'] == 'all'){
				$return  = $this->set_site_name_to_list($return);
			}		
					
			return $return;
				
		}
			
		/*
			* Function Name get_top_billing_country
			*
			* @param string $prefix
			*
			* @param array $shop_order_status
			*
			* @param string $start_date
			*
			* @param string $end_date
			*
			* return $return
		*/
		function get_top_billing_country($prefix = '',$shop_order_status = array(),$start_date = '',$end_date = ''){
			global $wpdb;		
			
			$per_page 	= 20;
			
			$sql = "
			SELECT SUM(postmeta1.meta_value) AS 'Total' 
			,postmeta2.meta_value AS 'BillingCountry'
			,Count(*) AS 'OrderCount'";
			
			$sql .= ", order_currency.meta_value 	AS order_currency";
			$sql .= "
			FROM {$prefix}posts as posts
			LEFT JOIN  {$prefix}postmeta as postmeta1 ON postmeta1.post_id=posts.ID
			LEFT JOIN  {$prefix}postmeta as postmeta2 ON postmeta2.post_id=posts.ID";
			$sql .= " LEFT JOIN  {$prefix}postmeta as order_currency ON order_currency.post_id = posts.ID";
			$sql .= "
			WHERE
			posts.post_type			=	'shop_order'  
			AND postmeta1.meta_key	=	'_order_total' 
			AND postmeta2.meta_key	=	'_billing_country'";
			
			$sql .= " AND order_currency.meta_key = '_order_currency'";
			
			$url_shop_order_status	= "";
			
			if(count($shop_order_status)>0){
				$in_shop_order_status		= implode("', '",$shop_order_status);
				$sql .= " AND  posts.post_status IN ('{$in_shop_order_status}')";
			}
				
			if ($start_date != NULL &&  $end_date !=NULL){
				$sql .= " AND DATE(posts.post_date) BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			
			$sql .= " 
			GROUP BY  postmeta2.meta_value ,order_currency.meta_value
			Order By Total DESC 						
			LIMIT {$per_page}";
			
			$order_items = $wpdb->get_results($sql); 
			
			$return = $this->convert_object_to_array($order_items);
				
			if($this->constants['userblog_id'] == 'all'){
				$return  = $this->set_site_name_to_list($return);
			}
			return $return;
		}
		
		/*
			* Function Name get_top_customer_list
			*
			* @param string $prefix
			*
			* @param array $shop_order_status
			*
			* @param string $start_date
			*
			* @param string $end_date
			*
			* return $return
		*/
		function get_top_customer_list($prefix = '',$shop_order_status = array(),$start_date = '',$end_date = ''){
			global $wpdb;
			$per_page 	= 20;
			
			$sql = "SELECT SUM(postmeta1.meta_value) AS 'Total' 
					,postmeta2.meta_value AS 'BillingEmail'
					,postmeta3.meta_value AS 'FirstName'
					,postmeta5.meta_value AS 'LastName'
					,postmeta6.meta_value AS 'CompanyName'
					,CONCAT(postmeta3.meta_value, ' ',postmeta5.meta_value) AS billing_name
					,Count(postmeta2.meta_value) AS 'OrderCount'";
			
			$sql .= " ,postmeta4.meta_value AS  customer_user";
			//
			$sql .= " FROM {$prefix}posts as posts
			LEFT JOIN  {$prefix}postmeta as postmeta1 ON postmeta1.post_id=posts.ID
			LEFT JOIN  {$prefix}postmeta as postmeta2 ON postmeta2.post_id=posts.ID
			LEFT JOIN  {$prefix}postmeta as postmeta3 ON postmeta3.post_id=posts.ID
			LEFT JOIN  {$prefix}postmeta as postmeta5 ON postmeta5.post_id=posts.ID
			LEFT JOIN  {$prefix}postmeta as postmeta6 ON postmeta6.post_id=posts.ID";
			
			$sql .= " LEFT JOIN  {$prefix}postmeta as postmeta4 ON postmeta4.post_id=posts.ID";
			
			$sql .= " 
			WHERE  
				posts.post_type='shop_order'  
				AND postmeta1.meta_key='_order_total' 
				AND postmeta2.meta_key='_billing_email'  
				AND postmeta3.meta_key='_billing_first_name'
				AND postmeta5.meta_key='_billing_last_name'
				AND postmeta6.meta_key='_billing_company'";
						
			$sql .= " AND postmeta4.meta_key='_customer_user'";
						
			$url_shop_order_status	= "";
			
			if(count($shop_order_status)>0){
				$in_shop_order_status		= implode("', '",$shop_order_status);
				$sql .= " AND  posts.post_status IN ('{$in_shop_order_status}')";
			}
					
			if ($start_date != NULL &&  $end_date !=NULL){
				$sql .= " AND DATE(posts.post_date) BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			
			$sql .= " GROUP BY  postmeta2.meta_value
			Order By Total DESC
			LIMIT {$per_page}";
					
			$order_items = $wpdb->get_results($sql );
			
			$return = $this->convert_object_to_array($order_items);
				
			if($this->constants['userblog_id'] == 'all'){
				$return  = $this->set_site_name_to_list($return);
			}
			return $return;
			
		}
		
		/*
			* Function Name get_top_coupon_list
			*
			* @param string $prefix
			*
			* @param array $shop_order_status
			*
			* @param string $start_date
			*
			* @param string $end_date
			*
			* return $return
		*/
		function get_top_coupon_list($prefix = '',$shop_order_status = array(),$start_date = '',$end_date = ''){
			global $wpdb;
			
			$per_page 	= 20;
			
			$sql = "SELECT *, 
			woocommerce_order_items.order_item_name, 
			SUM(woocommerce_order_itemmeta.meta_value) As 'Total', 
			woocommerce_order_itemmeta.meta_value AS 'coupon_amount' , 
			Count(*) AS 'Count' ";
			
			$sql .= ", order_currency.meta_value 	AS order_currency";
			$sql .= "
			FROM {$prefix}woocommerce_order_items as woocommerce_order_items 
			LEFT JOIN	{$prefix}posts						as posts 						ON posts.ID										=	woocommerce_order_items.order_id
			LEFT JOIN  {$prefix}woocommerce_order_itemmeta 	as woocommerce_order_itemmeta	ON woocommerce_order_itemmeta.order_item_id		=	woocommerce_order_items.order_item_id";
			if($this->constants['post_order_status_found'] == 0 ){
				if(count($shop_order_status)>0){
					$sql .= " 
					LEFT JOIN  {$prefix}term_relationships 	as term_relationships 	ON term_relationships.object_id		=	posts.ID
					LEFT JOIN  {$prefix}term_taxonomy 		as term_taxonomy 		ON term_taxonomy.term_taxonomy_id	=	term_relationships.term_taxonomy_id";
				}
			}
			$sql .= " LEFT JOIN  {$prefix}postmeta as order_currency ON order_currency.post_id = posts.ID";
			$sql .= "			
			WHERE 
			posts.post_type 								=	'shop_order'
			AND woocommerce_order_items.order_item_type		=	'coupon' 
			AND woocommerce_order_itemmeta.meta_key			=	'discount_amount'";
			$sql .= " AND order_currency.meta_key = '_order_currency'";
					
			$url_shop_order_status	= "";
			if(count($shop_order_status)>0){
				$in_shop_order_status		= implode("', '",$shop_order_status);
				$sql .= " AND  posts.post_status IN ('{$in_shop_order_status}')";
			}
			
			if ($start_date != NULL &&  $end_date !=NULL){
				$sql .= " AND DATE(posts.post_date) BETWEEN '{$start_date}' AND '{$end_date}'";
			}
			
			$sql .= " 
			Group BY woocommerce_order_items.order_item_name,order_currency.meta_value
			ORDER BY Total DESC
			LIMIT {$per_page}";
			 
			$order_items = $wpdb->get_results($sql); 
			
			$return = $this->convert_object_to_array($order_items);
				
			if($this->constants['userblog_id'] == 'all'){
				$return  = $this->set_site_name_to_list($return);
			}
			return $return;
			
		}
		
		
    }/*End Class*/
	
}/*End Class Exists*/
