<?php
if(!class_exists('IC_Commerce_Mutlisite_Reporting_Settings')){
	
	/*
	 * Class Name IC_Commerce_Mutlisite_Reporting_Settings 
	*/
    class IC_Commerce_Mutlisite_Reporting_Settings extends IC_Commerce_Mutlisite_Reporting_Functions{
		
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
				
				//delete_option('icmsreporting_settings');
				
				$order_statuses			 = $this->wc_get_order_statuses();
				$order_status_field_size = count($order_statuses) + 1;
					
				
				$icmsreporting_settings = get_option('icmsreporting_settings',array());				
				$shop_order_status		= $this->constants['shop_order_status'];
				$order_status			= isset($icmsreporting_settings['order_status']) ? $icmsreporting_settings['order_status'] : array();
				if(count($order_status) == 0 and count($shop_order_status) > 0){
					foreach($shop_order_status as $key => $value){
						$order_status[] = 'wc-'.$value;
					}
				}
				
				$order_status			= implode(",",$order_status);
				
				//$selected_order_statusses = $this->get_selected_order_statusses();
				//$this->print_array($selected_order_statusses);
				
				?>
                
            	<h2><?php _e( 'Settings','icwoocommerce_textdomains');?></h2>
                <form method="post" action="">
                    <div class="ic_commerce_settings">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="order_status"><?php _e("Order Status",'icwoocommerce_textdomains')?></label>
                                </th>
                                <td>
                                    <?php  $this->create_dropdown($order_statuses,"icmsreporting_settings[order_status][]","order_status","Select All","order_status",$order_status, 'array', true, $order_status_field_size,'all');?>
                                </td>
                            </tr>
                        </table>
                        <div class="submit_btn savebtn">
                            <input type="submit" name="btnSaveSettings" class="ic_button" id="btnSaveSettings" value="<?php _e('Save Sttings')?>" />
                        </div>
                        <input type="hidden" value="icmsreporting_setting" name="icmsreporting_setting" />
                    </div>                                
                </form>
                
                <style type="text/css">
                	
                </style>
            <?php     
		}
		
		function save_settings(){
			$icmsreporting_setting   = $this->get_request("icmsreporting_setting");	
			if($icmsreporting_setting == 'icmsreporting_setting'){
				if(isset($_REQUEST['icmsreporting_settings'])){
					$icmsreporting_settings = $_REQUEST['icmsreporting_settings'];					
					$order_status			= isset($icmsreporting_settings['order_status']) ? $icmsreporting_settings['order_status'] : array();
					
					if(count($order_status)>1){
						if($order_status[0] == 'all'){
							unset($icmsreporting_settings['order_status'][0]);
						}
					}
					
					update_option('icmsreporting_settings',$icmsreporting_settings);
					add_action( 'admin_notices', 	array( $this, 'admin_notices'));
					
					
					$error_txt = '<div class="updated fade"><p>'.__("Settings saved",'icwoocommerce_textdomains')." </p></div>\n";
					$msg = get_option($this->constants['plugin_key'].'_admin_notice_message','');
					if($msg){
						update_option($this->constants['plugin_key'].'_admin_notice_message',$error_txt);
					}else{
						delete_option($this->constants['plugin_key'].'_admin_notice_message');
						add_option($this->constants['plugin_key'].'_admin_notice_message',$error_txt);
					}
					
				}
			}
		}
		
		var $admin_notice = ""; 
		function admin_notices(){
			if(isset($_GET['page']) and ($_GET['page'] == $this->constants['plugin_key'].'_settings')){
				
				
				$msg = get_option($this->constants['plugin_key'].'_admin_notice_error','');
				if($msg){
					update_option($this->constants['plugin_key'].'_admin_notice_error','');
				}
				
				echo $msg;
				
				$msg = get_option($this->constants['plugin_key'].'_admin_notice_message','');
				if($msg){
					update_option($this->constants['plugin_key'].'_admin_notice_message','');
				}
				
				echo $msg;
			}
		}
    }/*End Class*/
	
}/*End Class Exists*/
