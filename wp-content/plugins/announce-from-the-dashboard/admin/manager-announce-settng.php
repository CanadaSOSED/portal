<?php

if ( !class_exists( 'Afd_Admin_Controller_Announce_Setting' ) ) :

final class Afd_Admin_Controller_Announce_Setting extends Afd_Admin_Abstract_Manager
{

	public function __construct()
	{
		
		global $Afd;

		$this->name           = 'announce_setting';
		$this->do_screen_slug = $Afd->main_slug;
		$this->menu_title     = $Afd->name;
		$this->page_title     = $Afd->name;
		
		$this->MainModel      = new Afd_Model_Announces();

		parent::__construct();

	}

	public function admin_menu()
	{

		global $Afd;

		if( $Afd->Site->is_multisite ) {
			
			add_menu_page( $this->page_title , $this->menu_title , $Afd->Plugin->capability , $this->do_screen_slug , array( $this , 'view' ) );

		} else {

			add_options_page( $this->page_title , $this->menu_title , $Afd->Plugin->capability , $this->do_screen_slug , array( $this , 'view' ) );
			
		}

	}

	public function view()
	{
		
		global $Afd;
		
		include_once( $this->view_dir . 'manager-announce-setting.php' );

	}
	
	private function get_data()
	{
		
		global $Afd;

		$settings_data = $this->MainModel->get_datas();
		
		return $settings_data;
		
	}

	protected function post_data()
	{

		global $Afd;
		
		if( !empty( $_POST[$Afd->Form->nonce . 'add_' . $this->name] ) ) {
			
			$nonce_key = $Afd->Form->nonce . 'add_' . $this->name;

			if(	check_admin_referer( $nonce_key , $nonce_key ) ) {
				
				$errors = $this->MainModel->add_data( $_POST );
				$notice = 'update_' . $this->name;
				
			}

		} elseif( !empty( $_POST[$Afd->Form->nonce . 'update_' . $this->name] ) ) {
			
			$nonce_key = $Afd->Form->nonce . 'update_' . $this->name;
			
			if(	check_admin_referer( $nonce_key , $nonce_key ) ) {
				
				$errors = $this->MainModel->update_data( $_POST );
				$notice = 'update_' . $this->name;
				
			}

		} elseif( !empty( $_POST[$Afd->Form->nonce . 'remove_' . $this->name] ) ) {
			
			$nonce_key = $Afd->Form->nonce . 'remove_' . $this->name;
			
			if(	check_admin_referer( $nonce_key , $nonce_key ) ) {
				
				$errors = $this->MainModel->remove_datas( $_POST );
				$notice = 'remove_' . $this->name;
				
			}

		}
		
		if( !isset( $errors ) )
			return false;
		
		$error_codes = $errors->get_error_codes();
		
		if( !empty( $error_codes ) ) {
			
			$this->errors = $errors;
			
		} else {
			
			wp_redirect( esc_url_raw( add_query_arg( array( $Afd->Plugin->msg_notice => $notice , 'page' => $this->do_screen_slug ) , $Afd->Helper->get_action_link() ) ) );
			exit;

		}
		
	}
	
	private function print_form_fields( $field_type = false , $edit_type = 'add' , $announce_id = false , $announce = array() )
	{

		global $Afd;
		
		$announce_types = $Afd->Helper->get_announce_types();
		$date_periods = $Afd->Helper->get_date_periods();
		$multisite_show_standard = $Afd->Helper->get_multisite_show_standard();
		$all_user_roles = $Afd->Helper->get_all_user_roles();
		
		if( $Afd->Site->is_multisite ) {
		
			$all_sites = $Afd->Helper->get_sites();
			
		}

		$field_base_name = 'data[' . $edit_type . ']';
		$field_base_id = $edit_type;

		if( $edit_type == 'add' ) {
			
			$announce = $this->MainModel->get_default_data();
			
		} else {
			
			$field_base_name .= '[' . $announce_id . ']';
			$field_base_id .= '_' . $announce_id;

		}
		
		if( $field_type == 'title' ) {
			
			printf( '<input type="text" class="large-text title-field" name="%1$s[title]" value="%3$s" />' , $field_base_name , $field_base_id , $announce['title'] );
			
		} elseif( $field_type == 'content' ) {
			
			echo wp_editor( $announce['content'] , $field_base_id . '_content' , array( 'textarea_name' => $field_base_name . '[content]' , 'media_buttons' => false ) );
			
		} elseif( $field_type == 'type' ) {
			
			printf( '<select name="%s[type]" class="type-field">', $field_base_name );
			
			foreach( $announce_types as $type => $setting ) {
				
				printf( '<option value="%1$s" %4$s>%2$s (%3$s)</option>' , $type , $setting['color'] , $setting['label'] , selected( $type , $announce['type'] , false ) );
				
			}
			
			echo '</select>';
			
		} elseif( $field_type == 'date_range' ) {
			
			printf( '<p class="date_range_error">%s</p>', sprintf( __( 'Please <strong>%1$s</strong> is later than the <strong>%2$s</strong>.' , $Afd->ltd ) , __( 'End Date' ) , __( 'Start Date' ) ) );
			
			foreach( $date_periods as $period => $period_label ) {
				
				$add_class = $period;

				if( !empty( $announce['range'][$period] ) ) {
					
					$add_class .= ' specify';
					
				}
				
				printf( '<div class="date_range %s">' , $add_class );
				
				echo '<p>';
				
				printf( '<span class="description">%s</span>: ' , $period_label );
				
				echo '<label>';
				
				printf( '<input type="checkbox" name="%1$s[range][%2$s]" value="1" class="change-date-range range-field" %3$s />' , $field_base_name , $period , checked( $announce['range'][$period] , true , false ) );
				
				_e( 'Specify' , $Afd->ltd );

				echo '</label>';

				echo '</p>';
				
				printf( '<div class="date-range-setting %s">' , $period );
				
				echo '<p class="range-date">';
				
				echo '<a href="javascript:void(0);" class="button button-secondary change-ymd"><span class="dashicons dashicons-clock"></span></a>';

				printf( '<span class="date-range-ymd">%s</span>' , mysql2date( get_option( 'date_format' ) , $announce['date'][$period] ) );
				
				printf( '<input type="hidden" value="%s" class="date-ymd-field" />' , date( 'Y-m-d' , strtotime( $announce['date'][$period] ) ) );
				
				echo '<br />';
				
				echo '<select class="change-h date-h-field">';
				
				$hour = date( 'G' , strtotime( $announce['date'][$period] ) );

				for( $i = 0; $i < 24; $i ++ ) {
					
					printf( '<option value="%1$s" %3$s>%2$s</option>' , sprintf( '%02d' , $i ) , $i , selected( $i , $hour , false ) );
					
				}
				
				echo '</select>: ';

				echo '<select class="change-i date-i-field">';
				
				$min = intval( date( 'i' , strtotime( $announce['date'][$period] ) ) );

				for( $i = 0; $i < 60; $i ++ ) {
					
					printf( '<option value="%1$s" %3$s>%2$s</option>' , sprintf( '%02d' , $i ) , $i , selected( $i , $min , false ) );
					
				}
				
				echo '</select>';
				
				printf( '<p class="description">%1$s: %2$s</p>' ,  __( 'Now' , $Afd->ltd ) , mysql2date( get_option( 'date_format' ) . get_option( 'time_format' ) , current_time( 'timestamp' ) ) );

				echo '</p>';

				printf( '<input type="hidden" name="%1$s[date][%2$s]" value="%3$s" class="date-range-field" />' , $field_base_name , $period , $announce['date'][$period] );

				echo '</div>';

				echo '</div>';

			}
			
		} elseif( $field_type == 'user_role' ) {
			
			printf( '<select name="%s[role][]" multiple="multiple" class="multiple-select user-role-field">', $field_base_name );
			
			foreach( $all_user_roles as $role_name => $user_role ) {
				
				printf( '<option value="%1$s" %3$s>%2$s</option>' , $role_name , $user_role['label'] , selected( array_key_exists( $role_name , $announce['role'] ) , true , false ) );
				
			}
			
			echo '</select>';

			printf( '<p class="description">%s</p>' , __( 'Hold the CTRL key and click the items in a list to choose them.' , $Afd->ltd ) );

		} elseif( $field_type == 'show_standard' ) {
			
			printf( '<select name="%s[standard]" class="change-show-standard">', $field_base_name );
			
			foreach( $multisite_show_standard as $show_type => $show_label ) {
				
				printf( '<option value="%1$s" %3$s>%2$s</option>' , $show_type , $show_label , selected( $show_type , $announce['standard'] , false ) );
				
			}
			
			echo '</select>';

		} elseif( $field_type == 'subsites' ) {
			
			$add_class = $announce['standard'];

			printf( '<div class="show-subsite-descriptions %s">' , $add_class );
			
			printf( '<p class="show-subsite-description all">%s</p>' , __( 'Choose the site if you want to <strong>hide announce</strong>.' , $Afd->ltd ) );
			printf( '<p class="show-subsite-description not">%s</p>' , __( 'Choose the site if you want to <strong>show announce</strong>.' , $Afd->ltd ) );
			
			echo '</div>';

			printf( '<select name="%s[subsites][]" multiple="multiple" class="multiple-select">', $field_base_name );
			
			foreach( $all_sites as $blog ) {
				
				$child_blog = get_blog_details( array( 'blog_id' => $blog['blog_id'] ) );
				printf( '<option value="%1$s" %3$s>[%1$s] %2$s</option>' , $blog['blog_id'] , $child_blog->blogname , selected( array_key_exists( $blog['blog_id'] , $announce['subsites'] ) , true , false ) );
				
			}

			echo '</select>';

			printf( '<p class="description">%s</p>' , __( 'Hold the CTRL key and click the items in a list to choose them.' , $Afd->ltd ) );

		}

	}

	public function admin_ajax()
	{
		
		global $Afd;

		add_action( 'wp_ajax_' . $Afd->ltd . '_announce_validate_data' , array( $this , 'ajax_announce_validate_data' ) );
		add_action( 'wp_ajax_' . $Afd->ltd . '_announce_update_sort' , array( $this , 'ajax_announce_update_sort' ) );

	}
	
	public function ajax_announce_validate_data()
	{
		
		global $Afd;

		if( empty( $_POST ) )
			return false;
		
		$nonce_key = $Afd->Form->nonce . 'announce_validate_data';

		if( empty( $_POST[$nonce_key] ) )
			return false;

		check_ajax_referer( $nonce_key , $nonce_key );
		
		if( empty( $_POST['data'] ) )
			return false;
		
		$errors = $this->MainModel->validate_data( $_POST['data'] );
		$error_codes = $errors->get_error_codes();
		
		if( !empty( $error_codes ) ) {
				
			$return_errors = array();
						
			foreach( $error_codes as $code ) {
							
				$return_errors[$code] = array( 'msg' => $errors->get_error_message( $code ) , 'data' => $errors->get_error_data( $code ) );
							
			}
	
			wp_send_json_error( array( 'errors' => $return_errors ) );
				
		} else {
			
			wp_send_json_success( array( true ) );

		}

		die();

	}
	
	public function ajax_announce_update_sort()
	{
		
		global $Afd;

		if( empty( $_POST ) )
			return false;
		
		$nonce_key = $Afd->Form->nonce . 'announce_update_sort';

		if( empty( $_POST[$nonce_key] ) )
			return false;

		check_ajax_referer( $nonce_key , $nonce_key );
		
		if( empty( $_POST['sort_lists'] ) )
			return false;
		
		$sort_lists = array();
		$not_flag = false;
		
		foreach( $_POST['sort_lists'] as $sort_id ) {
			
			if( is_array( $sort_id ) ) {
			
				$not_flag = true;
			
			} else {
				
				$sort_lists[] = intval( $sort_id );

			}
			
		}
		
		if( empty( $sort_lists ) or $not_flag ) {
			
			$return_errors = array();
			$return_errors['not_sorted'] = array( 'msg' => 'Not sorted' );

			wp_send_json_error( array( 'errors' => $return_errors ) );

		}
		
		$errors = $settings_data = $this->MainModel->update_sort_datas( $sort_lists );
		$error_codes = $errors->get_error_codes();

		if( !empty( $error_codes ) ) {
				
			$return_errors = array();
						
			foreach( $error_codes as $code ) {
							
				$return_errors[$code] = array( 'msg' => $errors->get_error_message( $code ) , 'data' => $errors->get_error_data( $code ) );
							
			}
	
			wp_send_json_error( array( 'errors' => $return_errors ) );
				
		} else {
				
			wp_send_json_success( array( true ) );

		}

		die();

	}
	
}

new Afd_Admin_Controller_Announce_Setting();

endif;
