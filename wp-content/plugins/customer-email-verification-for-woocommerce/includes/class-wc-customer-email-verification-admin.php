<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_customer_email_verification_admin {		
	
	public $my_account_id;
	
	/**
	 * Initialize the main plugin function
	*/
    public function __construct() {	
		$this->my_account_id = get_option( 'woocommerce_myaccount_page_id' );
	}
	
	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	 */
	private static $instance;
	
	/**
	 * Get the class instance
	 *
	 * @return woo_customer_email_verification_Admin
	*/
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
	/*
	* init from parent mail class
	*/
	public function init(){
		add_action( 'wp_ajax_cev_settings_form_update', array( $this, 'cev_settings_form_update_fun') );
		add_action( 'wp_ajax_cev_frontend_messages_form_update', array( $this, 'cev_frontend_messages_form_update_fun') );
		add_filter( 'manage_users_columns', array( $this, 'add_column_users_list' ), 10, 1 );
		add_filter( 'manage_users_custom_column', array( $this, 'add_details_in_custom_users_list' ), 10, 3 );
		add_action( 'show_user_profile', array( $this, 'show_cev_fields_in_single_user' ) );
		add_action( 'edit_user_profile', array( $this, 'show_cev_fields_in_single_user' ) );
		add_action( 'admin_head', array( $this, 'cev_manual_verify_user' ) );      

		/*** Sort and Filter Users ***/
		add_action('restrict_manage_users', array( $this, 'filter_user_by_verified' ));	
		add_filter('pre_get_users', array( $this, 'filter_users_by_user_by_verified_section' ));
		
		/*** Bulk actions for Users ***/
		add_filter( 'bulk_actions-users', array( $this, 'add_custom_bulk_actions_for_user' ) );
		add_filter( 'handle_bulk_actions-users', array( $this, 'users_bulk_action_handler' ), 10, 3 );
		add_action( 'admin_notices', array( $this, 'user_bulk_action_notices' ) );
	}
	
	/*
	* Admin Menu add function
	* WC sub menu
	*/
	public function register_woocommerce_menu() {
		add_submenu_page( 'woocommerce', 'Customer Verification', 'Customer Verification', 'manage_woocommerce', 'customer-email-verification-for-woocommerce', array( $this, 'wc_customer_email_verification_page_callback' ) ); 
	}
	
	/**
	* Load admin styles.
	*/
	public function admin_styles($hook) {						
		
		if(!isset($_GET['page'])) {
			return;
		}
		if( $_GET['page'] != 'customer-email-verification-for-woocommerce') {
			return;
		}
		
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';				

		wp_register_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), '4.0.3' );
		wp_enqueue_script( 'select2');
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'customer_email_verification_styles',  woo_customer_email_verification()->plugin_dir_url() . 'assets/css/admin.css', array(), woo_customer_email_verification()->version );
				
		wp_enqueue_script( 'customer_email_verification_script', woo_customer_email_verification()->plugin_dir_url() . 'assets/js/admin.js', array( 'jquery','wp-util' ), woo_customer_email_verification()->version , true);
		wp_localize_script( 'customer_email_verification_script', 'customer_email_verification_script', array() );
		
		wp_register_script( 'selectWoo', WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full' . $suffix . '.js', array( 'jquery' ), '1.0.4' );
		wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', array( 'jquery', 'selectWoo' ), WC_VERSION );
		wp_register_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
		
		wp_enqueue_script( 'selectWoo');
		wp_enqueue_script( 'wc-enhanced-select');
		
		wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
		wp_enqueue_style( 'woocommerce_admin_styles' );
		
		wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery' ), WC_VERSION, true );
		wp_enqueue_script( 'jquery-tiptip' );
		wp_enqueue_script( 'jquery-blockui' );
		wp_enqueue_script( 'wp-color-picker' );									
	}
	
	/*
	* callback for Customer Email Verification page
	*/
	public function wc_customer_email_verification_page_callback(){ ?>
		<div class="white-bg">			
			<img class="cev-plugin-logo" src="<?php echo woo_customer_email_verification()->plugin_dir_url()?>assets/images/cev-logo.png">
		</div>
        <div class="woocommerce cev_admin_layout">
            <div class="cev_admin_content" >					
				<input id="setting_tab" type="radio" name="tabs" class="cev_tab_input" data-tab="settings" checked>
				<label for="setting_tab" class="cev_tab_label first_label"><?php _e('Settings', 'woocommerce'); ?></label>
				
				<input id="customize_tab" type="radio" name="tabs" class="cev_tab_input" data-tab="customize">
				<label for="customize_tab" class="cev_tab_label"><?php _e('Customize', 'customer-email-verification-for-woocommerce'); ?></label>
				
				<?php require_once( 'views/admin_options_settings.php' ); ?>
				<?php require_once( 'views/admin_options_customize.php' ); ?>	
			</div>				 
        </div>
	<?php }
	
	/*
	* get html of fields
	*/
	public function get_html( $arrays ){
		
		$checked = '';
		?>
		<table class="form-table">
			<tbody>
            	<?php foreach( (array)$arrays as $id => $array ){
				
					if($array['show']){
					?>
                	<?php if($array['type'] == 'title'){ ?>
                		<tr valign="top titlerow">
                        	<th colspan="2"><h3><?php echo $array['title']?></h3></th>
                        </tr>    	
                    <?php continue;} ?>
				<tr valign="top" class="<?php echo $array['class']; ?>">
					<?php if($array['type'] != 'desc'){ ?>										
					<th scope="row" class="titledesc"  >
						<label for=""><?php echo $array['title']?><?php if(isset($array['title_link'])){ echo $array['title_link']; } ?>
							<?php if( isset($array['tooltip']) ){?>
                            	<span class="woocommerce-help-tip tipTip" title="<?php echo $array['tooltip']?>"></span>
                            <?php } ?>
                        </label>
					</th>
					<?php } ?>
					<td class="forminp"  <?php if($array['type'] == 'desc'){ ?> colspan=2 <?php } ?>>
                    	<?php if( $array['type'] == 'checkbox' ){								
							if($id === 'wcast_enable_delivered_email'){
								$wcast_enable_delivered_email = get_option('woocommerce_customer_delivered_order_settings');
								//echo '<pre>';print_r($wcast_enable_delivered_email);echo '</pre>';								
		
								if($wcast_enable_delivered_email['enabled'] == 'yes' || $wcast_enable_delivered_email['enabled'] == 1){
									$checked = 'checked';
								} else{
									$checked = '';									
								}								
							} elseif($id === 'wcast_enable_partial_shipped_email'){
								$wcast_enable_partial_shipped_email = get_option('woocommerce_customer_partial_shipped_order_settings');

								if($wcast_enable_partial_shipped_email['enabled'] == 'yes' || $wcast_enable_partial_shipped_email['enabled'] == 1){
									$checked = 'checked';
								} else{
									$checked = '';									
								}								
							} else{																		
								if(get_option($id)){
									$checked = 'checked';
								} else{
									$checked = '';
								} 
							} 
							
							if(isset($array['disabled']) && $array['disabled'] == true){
								$disabled = 'disabled';
								$checked = '';
							} else{
								$disabled = '';
							}							
							?>
						<span class="">
							<label class="" for="<?php echo $id?>">
								<input type="hidden" name="<?php echo $id?>" value="0"/>
								<input type="checkbox" id="<?php echo $id?>" name="<?php echo $id?>" class="" <?php echo $checked ?> value="1" <?php echo $disabled; ?>/>
							</label>
						</span>
                        <?php } elseif( $array['type'] == 'multiple_checkbox' ){ ?>
								<?php 
								$op = 1;	
								foreach((array)$array['options'] as $key => $val ){
																		
										$multi_checkbox_data = get_option($id);
										if(isset($multi_checkbox_data[$key]) && $multi_checkbox_data[$key] == 1){
											$checked="checked";
										} else{
											$checked="";
										}?>
								<span class="multiple_checkbox">
									<label class="" for="<?php echo $key?>">
										<input type="hidden" name="<?php echo $id?>[<?php echo $key?>]" value="0"/>
										<input type="checkbox" id="<?php echo $key?>" name="<?php echo $id?>[<?php echo $key?>]" class=""  <?php echo $checked; ?> value="1"/>
										<span class="multiple_label"><?php echo $val; ?></span>	
										</br>
									</label>																		
								</span>												
								<?php 								
								}								
								?>
						
                        <?php }  elseif( isset( $array['type'] ) && $array['type'] == 'dropdown' ){ ?>
                        	<?php
								if( isset($array['multiple']) ){
									$multiple = 'multiple';
									$field_id = $array['multiple'];
								} else {
									$multiple = '';
									$field_id = $id;
								}
							?>
                        	<fieldset>
								<select class="select select2" id="<?php echo $field_id?>" name="<?php echo $id?>" <?php echo $multiple;?>>    <?php foreach((array)$array['options'] as $key => $val ){?>
                                    	<?php
											$selected = '';
											if( isset($array['multiple']) ){
												if (in_array($key, (array)$this->data->$field_id ))$selected = 'selected';
											} else {
												if( get_option($id) == (string)$key )$selected = 'selected';
											}
                                        
										?>
										<option value="<?php echo $key?>" <?php echo $selected?> ><?php echo $val?></option>
                                    <?php } ?>
								</select>
							</fieldset>
                        <?php } elseif( isset( $array['type'] ) && $array['type'] == 'radio' ){ ?>                        	
                        	<fieldset>
								<?php foreach((array)$array['options'] as $key => $val ){
									$selected = '';
									if( get_option($id,$array['default']) == (string)$key )$selected = 'checked';
									?>
									<span class="radio_section">
										<label class="" for="<?php echo $id?>_<?php echo $key?>">												
											<input type="radio" id="<?php echo $id?>_<?php echo $key?>" name="<?php echo $id?>" class="<?php echo $id?>"  value="<?php echo $key?>" <?php echo $selected?>/>
											<span class=""><?php echo $val; ?></span>	
											</br>
										</label>																		
									</span></br>	
                                <?php } ?>								
							</fieldset>
                        <?php } elseif( $array['type'] == 'title' ){?>
						<?php }
						elseif( $array['type'] == 'label' ){ ?>
							<fieldset>
                               <label><?php echo $array['value']; ?></label>
                            </fieldset>
						<?php }
						elseif( $array['type'] == 'tooltip_button' ){ ?>
							<fieldset>
								<a href="<?php echo $array['link']; ?>" class="button-primary" target="<?php echo $array['target'];?>"><?php echo $array['link_label'];?></a>
                            </fieldset>
						<?php }
						elseif( $array['type'] == 'link' ){ ?>
							<fieldset>
								<a href="<?php echo $array['url'];?>" class="button-primary"><?php echo $array['label'];?></a>								
							</fieldset>
						<?php }
						elseif( $array['type'] == 'textarea' ){ ?>
							<fieldset>
								<textarea placeholder="<?php if(!empty($array['placeholder'])){echo $array['placeholder'];} ?>" class="input-text regular-input" name="<?php echo $id?>" id="<?php echo $id?>"><?php echo get_option($id)?></textarea>                                
                            </fieldset>
						<?php }
						elseif( $array['type'] == 'tag_block' ){ ?>
							<fieldset class="tag_block">
								<code>{{customer_email_verification_code}}</code><code>{{cev_user_verification_link}}</code><code>{{cev_resend_email_link}}</code><code>{{cev_display_name}}</code><code>{{cev_user_login}}</code><code>{{cev_user_email}}</code> 								
                            </fieldset>
						<?php }
						else { ?>
                                                    
                        	<fieldset>
                                <input class="input-text regular-input " type="text" name="<?php echo $id?>" id="<?php echo $id?>" style="" value="<?php echo get_option($id)?>" placeholder="<?php if(!empty($array['placeholder'])){echo $array['placeholder'];} ?>">
                            </fieldset>
                        <?php } ?>
                        
					</td>
				</tr>
				<?php if(isset($array['desc']) && $array['desc'] != ''){ ?>
					<tr class="<?php echo $array['class']; ?>"><td colspan="2" style=""><p class="description"><?php echo (isset($array['desc']))? $array['desc']: ''?></p></td></tr>
				<?php } ?>				
	<?php } } ?>
			</tbody>
		</table>
	<?php 
	}

	/*
	* get settings tab array data
	* return array
	*/
	function get_cev_settings_data(){		
		$page_list = wp_list_pluck( get_pages(), 'post_title', 'ID' );
		global $wp_roles;
		$all_roles = $wp_roles->roles;
		$all_roles_array = array();
		foreach($all_roles as $key=>$role){
			if($key != 'administrator'){
				$role = array( $key => $role['name'] );
				$all_roles_array = array_merge($all_roles_array,$role);	
			}
		}
		
		$form_data = array(
			'cev_enable_email_verification' => array(
				'type'		=> 'checkbox',
				'title'		=> __( 'Enable customer email verification', 'customer-email-verification-for-woocommerce' ),'show' => true,
				'class'     => '',
			),
			'cev_email_for_verification' => array(
				'type'		=> 'radio',
				'title'		=> __( 'Email for Verification', 'customer-email-verification-for-woocommerce' ),				
				'show'		=> true,
				'options'   => array( 									
								"1" =>__( 'Separate Verification Email', 'customer-email-verification-for-woocommerce' ),
								"2" =>__( 'Verification in Account Email', 'customer-email-verification-for-woocommerce' ),
							),	
				'default'	=> 1,			
				'class'     => '',
			),
			'cev_skip_verification_for_selected_roles' => array(
				'type'		=> 'multiple_checkbox',
				'title'		=> __( 'Skip email verification for the selected user roles', 'customer-email-verification-for-woocommerce' ),
				'options'   => $all_roles_array,				
				'show' => true,				
				'class'     => '',
			),	
			'cev_enter_account_after_registration' => array(
				'type'		=> 'checkbox',
				'title'		=> __( 'Allow first login after registration without email verification', 'customer-email-verification-for-woocommerce' ),'show' => true,
				'class'     => '',
			),	
			'cev_verification_form_theme_color' => array(
				'type'		=> 'color',
				'title'		=> __( 'Select email verification form theme color', 'customer-email-verification-for-woocommerce' ),				
				'class'		=> 'cev_color_field',
				'show' => true,				
			),			
		);
		return $form_data;
	}

	/*
	* get settings tab array data
	* return array
	*/
	function get_cev_frontend_messages_data(){		
		$form_data = array(						
			'cev_verification_message' => array(
				'type'		=> 'textarea',
				'title'		=> __( 'Verification Message', 'customer-email-verification-for-woocommerce' ),				
				'show'		=> true,
				'placeholder' => __( 'We sent you a verification email. Check and verify your account.', 'customer-email-verification-for-woocommerce' ),	
				'class'     => '',
			),
			'cev_verification_success_message' => array(
				'type'		=> 'textarea',
				'title'		=> __( 'Verification Success Message', 'customer-email-verification-for-woocommerce' ),				
				'show'		=> true,
				'placeholder' => __( 'Your Email is verified!', 'customer-email-verification-for-woocommerce' ),	
				'class'     => '',
			),
			'cev_resend_verification_email_message' => array(
				'type'		=> 'textarea',
				'title'		=> __( 'Resend verification email message', 'customer-email-verification-for-woocommerce' ),				
				'show'		=> true,
				'placeholder' => __( 'You need to verify your account before login. {{cev_resend_email_link}}', 'customer-email-verification-for-woocommerce' ),	
				'class'     => '',
			),
			'cev_verified_user_message' => array(
				'type'		=> 'textarea',
				'title'		=> __( 'Message For Verified Users', 'customer-email-verification-for-woocommerce' ),				
				'show'		=> true,
				'placeholder' => __( 'Your Email is already verified', 'customer-email-verification-for-woocommerce' ),	
				'class'     => '',
			),	
			'cev_re_verification_message' => array(
				'type'		=> 'textarea',
				'title'		=> __( 'Re-Verification Message', 'customer-email-verification-for-woocommerce' ),				
				'show'		=> true,
				'placeholder' => __( 'A new verification link is sent. Check email. {{cev_resend_email_link}}', 'customer-email-verification-for-woocommerce' ),	
				'class'     => '',
			),
			'cev_shorttags' => array(
				'type'		=> 'tag_block',
				'title'		=> __( 'You can use following tag in email and message', 'customer-email-verification-for-woocommerce' ),
				'show' => true,				
				'class'     => '',
			),			
		);
		return $form_data;
	}	
	
	public function cev_settings_form_update_fun(){
		if ( ! empty( $_POST ) && check_admin_referer( 'cev_settings_form_nonce', 'cev_settings_form_nonce' ) ) {
			$data = $this->get_cev_settings_data();				
			foreach( $data as $key => $val ){				
				if(isset($_POST[ $key ])){						
					update_option( $key, $_POST[ $key ] );
				}
			}	
		}
	}
	
	public function cev_frontend_messages_form_update_fun(){
		if ( ! empty( $_POST ) && check_admin_referer( 'cev_frontend_messages_form_nonce', 'cev_frontend_messages_form_nonce' ) ) {
			$data = $this->get_cev_frontend_messages_data();	
			foreach( $data as $key => $val ){				
				if(isset($_POST[ $key ])){						
					update_option( $key, $_POST[ $key ] );
				}
			}	
		}
	}
	
	/**
	 * This function adds custom columns in user listing screen in wp-admin area.
	 */
	public function add_column_users_list( $column ){
		$column['cev_verified']            = __( 'Verification Status', 'customer-email-verification-for-woocommerce' );
		return $column;
	}

	/**
	 * This function adds custom values to custom columns in user listing screen in wp-admin area.
	 */	
	public function add_details_in_custom_users_list( $val, $column_name, $user_id ){
		
		$user_role = get_userdata( $user_id );
		$verified  = get_user_meta( $user_id, 'customer_email_verified', true );
		$cev_skip_verification_for_selected_roles = get_option('cev_skip_verification_for_selected_roles');
		if ( 'cev_verified' === $column_name ) {
			if(isset($user_role->roles[0])){
				if ( 'administrator' !== $user_role->roles[0]) {
					if(isset($cev_skip_verification_for_selected_roles[$user_role->roles[0]]) && $cev_skip_verification_for_selected_roles[$user_role->roles[0]] == 0){
						if ( 'true' === $verified ) {
							$text = __( 'Unverify', 'customer-email-verification-for-woocommerce' );
							return '<span class="dashicons dashicons-yes-alt" title="Verified" style="color: #4caf50;"></span> <a class="" href=' . add_query_arg( array(
								'user_id'    => $user_id,
								'wp_nonce'   => wp_create_nonce( 'wc_cev_email' ),
								'wc_cev_confirm' => 'false',
							), get_admin_url() . 'users.php' ) . '><img title="unverify manual" class="icon" src="'.woo_customer_email_verification()->plugin_dir_url().'assets/images/unvarify.png" ></a>';
						} else {
							$text = __( 'Verify', 'customer-email-verification-for-woocommerce' );
							$text2 = __( 'Resend Email', 'customer-email-verification-for-woocommerce' );
							return '<span class="dashicons dashicons-dismiss" title="Unverified" style="color: #e14d43;"></span> <a class="" href=' . add_query_arg( array(
									'user_id'    => $user_id,
									'wp_nonce'   => wp_create_nonce( 'wc_cev_email' ),
									'wc_cev_confirm' => 'true',
								), get_admin_url() . 'users.php' ) . '><img title="verify manual" class="icon" src="'.woo_customer_email_verification()->plugin_dir_url().'assets/images/varify.png" ></a> <a class="" href=' . add_query_arg( array(
							'user_id'         => $user_id,
							'wp_nonce'        => wp_create_nonce( 'wc_cev_email_confirmation' ),
							'wc_cev_confirmation' => 'true',
						), get_admin_url() . 'users.php' ) . '><img title="send verification email" class="icon" src="'.woo_customer_email_verification()->plugin_dir_url().'assets/images/resend.png" ></span></a>';
						}
					}
				} else {
					return $user_role->roles[0];
				}
			}
		}
		
		return $val;
	}
	
	/**
	 * This function manually verifies a user from wp-admin area.
	 */
	public function cev_manual_verify_user() {
		if ( isset( $_GET['user_id'] ) && isset( $_GET['wp_nonce'] ) && wp_verify_nonce( $_GET['wp_nonce'], 'wc_cev_email' ) ) { 
			if ( isset( $_GET['wc_cev_confirm'] ) && 'true' === $_GET['wc_cev_confirm'] ) { 
				update_user_meta( $_GET['user_id'], 'customer_email_verified', 'true' );
				add_action( 'admin_notices', array( $this, 'manual_cev_verify_email_success_admin' ) );
			} else {
				delete_user_meta( $_GET['user_id'], 'customer_email_verified' ); 
				add_action( 'admin_notices', array( $this, 'manual_cev_verify_email_unverify_admin' ) );
			}
		}

		if ( isset( $_GET['user_id'] ) && isset( $_GET['wp_nonce'] ) && wp_verify_nonce( $_GET['wp_nonce'], 'wc_cev_email_confirmation' ) ) {			
			$current_user           = get_user_by( 'id', $_GET['user_id'] );
			$is_secret_code_present = get_user_meta( $_GET['user_id'], 'customer_email_verification_code', true );

			if ( '' === $is_secret_code_present ) {
				$secret_code = md5( $_GET['user_id'] . time() );
				update_user_meta( $_GET['user_id'], 'customer_email_verification_code', $secret_code );
			}					
			
			WC_customer_email_verification_email_Common::$wuev_user_id           = $_GET['user_id']; // WPCS: input var ok, CSRF ok.
			WC_customer_email_verification_email_Common::$wuev_myaccount_page_id = $this->my_account_id;
			
			WC_customer_email_verification_email_Common::code_mail_sender( $current_user->user_email );
			add_action( 'admin_notices', array( $this, 'manual_confirmation_email_success_admin' ) );
		}
		?>
		<style>
			.cev_verified.column-cev_verified {display: inline-table;width: 85px;}
			.icon {border-radius: 3px;border: 1px solid #0073aa;padding: 3px;}
			.cev_verified.column-cev_verified .dashicons {font-size: 20px; padding: 1px;margin: 1px 2px;border: 1px solid;border-radius: 3px;}
		</style>	
		<?php
	}
	
	public function manual_confirmation_email_success_admin() {
		$text = __( 'Verification Email Successfully Sent.', 'customer-email-verification-for-woocommerce' );
		?>
        <div class="updated notice">
            <p><?php echo $text; ?></p>
        </div>
		<?php
	}
	
	public function manual_cev_verify_email_success_admin() {
		$text = __( 'User Verified Successfully.', 'customer-email-verification-for-woocommerce' );
		?>
        <div class="updated notice">
            <p><?php echo $text; ?></p>
        </div>
		<?php
	}

	public function manual_cev_verify_email_unverify_admin() {
		$text = __( 'User Unverified.', 'customer-email-verification-for-woocommerce' );
		?>
        <div class="updated notice">
            <p><?php echo $text; ?></p>
        </div>
		<?php
	}
	
	// define the woocommerce_login_form_end callback 
	public function action_woocommerce_login_form_end() { ?>
		<p class="woocommerce-LostPassword lost_password">
			<a href="<?php echo get_home_url(); ?>?p=reset-verification-email"><?php esc_html_e( 'Resend Verification Email', 'customer-email-verification-for-woocommerce' ); ?></a>
		</p>
	<?php } 

	public function show_cev_fields_in_single_user( $user ){ 
		$user_id = $user->ID; 
		$verified  = get_user_meta( $user_id, 'customer_email_verified', true );
		$user_role = get_userdata( $user_id );
		$cev_skip_verification_for_selected_roles = get_option('cev_skip_verification_for_selected_roles');
		?>
		<h3><?php esc_html_e( 'Customer Email Verification for WooCommerce', 'customer-email-verification-for-woocommerce' ); ?></h3>

		<table class="form-table">
			<tr>
				<th><label for="year_of_birth"><?php esc_html_e( 'Verification Status', 'customer-email-verification-for-woocommerce' ); ?></label></th>
				<td><?php 
				if ( 'administrator' !== $user_role->roles[0] && $cev_skip_verification_for_selected_roles[$user_role->roles[0]] == 0) {
					if ( 'true' === $verified ) {
						echo __( 'Verified', 'customer-email-verification-for-woocommerce' );
					} else {
						echo __( 'Not Verified', 'customer-email-verification-for-woocommerce' );
					}
				} else {
					echo 'Admin';
				} ?></td>
			</tr>
			<tr>
				<th><label for="year_of_birth"><?php esc_html_e( 'Manual Verify', 'customer-email-verification-for-woocommerce' ); ?></label></th>
				<td><?php 
				if ( 'administrator' !== $user_role->roles[0] && $cev_skip_verification_for_selected_roles[$user_role->roles[0]] == 0) {
					remove_query_arg( array( 'user_id', 'wc_cev_confirm', 'wp_nonce', 'wc_cev_confirmation', 'send_verification_emails', 'verify_users_email' ));
					if ( 'true' !== $verified ) {
						$text = __( 'Verify', 'customer-email-verification-for-woocommerce' );
	
						echo '<a class="button-primary" href=' . add_query_arg( array(
								'user_id'    => $user_id,
								'wp_nonce'   => wp_create_nonce( 'wc_cev_email' ),
								'wc_cev_confirm' => 'true',
							), get_admin_url() . 'users.php' ) . '>' . $text . '</a>';
					} else {
						$text = __( 'Unverify', 'customer-email-verification-for-woocommerce' );
	
						echo '<a class="button-primary" href=' . add_query_arg( array(
								'user_id'    => $user_id,
								'wp_nonce'   => wp_create_nonce( 'wc_cev_email' ),
								'wc_cev_confirm' => 'false',
							), get_admin_url() . 'users.php' ) . '>' . $text . '</a>';
					}
				} ?></td>
			</tr>
			<tr>
				<th><label for="year_of_birth"><?php esc_html_e( 'Verification Email', 'customer-email-verification-for-woocommerce' ); ?></label></th>
				<td><?php 
					
					if ( 'administrator' != $user_role->roles[0] && $cev_skip_verification_for_selected_roles[$user_role->roles[0]] == 0) {
						remove_query_arg( array( 'user_id', 'wc_cev_confirm', 'wp_nonce', 'wc_cev_confirmation', 'send_verification_emails', 'verify_users_email' ));
						$text = __( 'Send Verification Email', 'customer-email-verification-for-woocommerce' );
	
						if ( 'true' === $verified ) {
							echo '';
						}
		
						echo '<a class="button-primary" href=' . add_query_arg( array(
								'user_id'         => $user_id,
								'wp_nonce'        => wp_create_nonce( 'wc_cev_email_confirmation' ),
								'wc_cev_confirmation' => 'true',
							), get_admin_url() . 'users.php' ) . '>' . $text . '</a>';
					} ?></td>
			</tr>
		</table>
	<?php }

	public function filter_user_by_verified( $which ){
		
		$true_selected = '';
		$false_selected = '';
		// figure out which button was clicked. The $which in filter_by_job_role()
		if(isset($_GET['customer_email_verified_top'])){
			$top = $_GET['customer_email_verified_top'] ? $_GET['customer_email_verified_top'] : null;
		}
		
		if(isset($_GET['customer_email_verified_bottom'])){
			$bottom = $_GET['customer_email_verified_bottom'] ? $_GET['customer_email_verified_bottom'] : null;
		}
		
		if (!empty($top) OR !empty($bottom))
		{
			$section = !empty($top) ? $top : $bottom;
			if($section == 'true'){
				$true_selected = 'selected';	
			}
			if($section == 'false'){
				$false_selected = 'selected';	
			}
		}
		
		// template for filtering
		$st = '<select name="customer_email_verified_%s" style="float:none;margin-left:10px;">
			<option value="">%s</option>%s</select>';
		
		
		// generate options
		$options = '<option value="true" '.$true_selected.'>'.__( 'Verified', 'customer-email-verification-for-woocommerce' ).'</option>
			<option value="false" '.$false_selected.'>'.__( 'Non Verified', 'customer-email-verification-for-woocommerce' ).'</option>';
		
		// combine template and options
		$select = sprintf( $st, $which, __( 'User Verification', 'customer-email-verification-for-woocommerce' ), $options );
		
		// output <select> and submit button
		echo $select;
		submit_button(__( 'Filter' ), null, $which, false);	
	}
	
	public function filter_users_by_user_by_verified_section( $query ){
		global $pagenow;
		if (is_admin() && 'users.php' == $pagenow) {
			
			// figure out which button was clicked. The $which in filter_by_job_role()
			if(isset($_GET['customer_email_verified_top'])){
				$top = $_GET['customer_email_verified_top'] ? $_GET['customer_email_verified_top'] : null;
			}
			
			if(isset($_GET['customer_email_verified_bottom'])){
				$bottom = $_GET['customer_email_verified_bottom'] ? $_GET['customer_email_verified_bottom'] : null;
			}
			
			if (!empty($top) OR !empty($bottom))
			{
				$section = !empty($top) ? $top : $bottom;
				if($section == 'true'){
					// change the meta query based on which option was chosen
					$meta_query = array (array (
						'key' => 'customer_email_verified',
						'value' => $section,
						'compare' => 'LIKE'
					));
				} else{
					$meta_query = array (
						'relation' => 'AND',
						array (
							'key' => 'cev_email_verification_pin',							
							'compare' => 'EXISTS'
						),
						array (
							'key' => 'customer_email_verified',
							'value' => $section,
							'compare' => 'NOT EXISTS'
						),	
					);
				}
				$query->set('meta_query', $meta_query);				
			}
		}	
	}
 
	function add_custom_bulk_actions_for_user( $bulk_array ) {
	 
		$bulk_array['verify_users_email'] = 'Verify users email';
		$bulk_array['send_verification_email'] = 'Send verification email';
		return $bulk_array;
	 
	}
	
 
	function users_bulk_action_handler( $redirect, $doaction, $object_ids ) {
	 
		$redirect = remove_query_arg( array( 'user_id', 'wc_cev_confirm', 'wp_nonce', 'wc_cev_confirmation', 'verify_users_emails', 'send_verification_emails' ), $redirect );

		if ( $doaction == 'verify_users_email' ) {
	 
			foreach ( $object_ids as $user_id ) {
				update_user_meta( $user_id, 'customer_email_verified', 'true' );
			}
	 
			$redirect = add_query_arg( 'verify_users_emails', count( $object_ids ), $redirect );
	 
		}
	 
		if ( $doaction == 'send_verification_email' ) {
			foreach ( $object_ids as $user_id ) {
				$current_user = get_user_by( 'id', $user_id );
				$this->user_id                         = $current_user->ID;
				$this->email_id                        = $current_user->user_email;
				$this->user_login                      = $current_user->user_login;
				$this->user_email                      = $current_user->user_email;
				WC_customer_email_verification_email_Common::$wuev_user_id  = $current_user->ID;
				WC_customer_email_verification_email_Common::$wuev_myaccount_page_id = $this->my_account_id;
				$this->is_user_created                 = true;		
				$is_secret_code_present                = get_user_meta( $this->user_id, 'customer_email_verification_code', true );
		
				if ( '' === $is_secret_code_present ) {
					$secret_code = md5( $this->user_id . time() );
					update_user_meta( $user_id, 'customer_email_verification_code', $secret_code );
				}
				$cev_email_for_verification = get_option('cev_email_for_verification',1);
				$verified = get_user_meta( $this->user_id, 'customer_email_verified', true );
				if($cev_email_for_verification == 1 && $verified != 'true' ){
					WC_customer_email_verification_email_Common::code_mail_sender( $current_user->user_email );
				}
			}
			$redirect = add_query_arg( 'send_verification_emails', count( $object_ids ), $redirect );
		}
	 
		return $redirect;
	 
	}
 
	function user_bulk_action_notices() {
	 
		if ( ! empty( $_REQUEST['verify_users_emails'] ) ) {
			printf( '<div id="message" class="updated notice is-dismissible"><p>' .
				_n( 'Varification Status updated from  %s user.',
				'Varification Status updated from  %s users.',
				intval( $_REQUEST['verify_users_emails'] )
			) . '</p></div>', intval( $_REQUEST['verify_users_emails'] ) );
		}
	 
		if( ! empty( $_REQUEST['send_verification_emails'] ) ) {
	 
			printf( '<div id="message" class="updated notice is-dismissible"><p>' .
				_n( 'Sent Varification email from  %s user.',
				'Sent Varification email from  %s users.',
				intval( $_REQUEST['send_verification_emails'] )
			) . '</p></div>', intval( $_REQUEST['send_verification_emails'] ) );
	 
		}
	 
	}
	
}