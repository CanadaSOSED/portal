<?php
if(!class_exists('IC_Commerce_Mutlisite_Reporting_Functions')){

	/*
	 * Class Name IC_Commerce_Mutlisite_Reporting_Functions
	*/
    class IC_Commerce_Mutlisite_Reporting_Functions{

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
			$this->constants = $constants;
		}

		/*
			* Function Name get_request
			*
			* @param string $name
			*
			* @param string $default
			*
			* @param string $set
			*
		*/
		public function get_request($name,$default = NULL,$set = false){
			if(isset($_REQUEST[$name])){
				$newRequest = $_REQUEST[$name];

				if(is_array($newRequest)){
					$newRequest = implode(",", $newRequest);
				}else{
					$newRequest = trim($newRequest);
				}

				if($set) $_REQUEST[$name] = $newRequest;

				return $newRequest;
			}else{
				if($set) 	$_REQUEST[$name] = $default;
				return $default;
			}
		}

		/*
			* Function Name get_order_status
			*
			* return $order_status
		*/
		function get_order_status(){
			$order_status = "wc-".implode(",wc-", apply_filters( 'woocommerce_reports_order_statuses',array('completed','processing','on-hold','refunded')));
			$order_status = explode(",",$order_status);
			return $order_status;
		}

		/*
			* Function Name first_order_date
			*
			* @param string $prefix
			*
			* return $first_order_date
		*/
		function first_order_date($prefix){
			global $wpdb;
			$sql = "SELECT DATE_FORMAT(posts.post_date, '%Y-%m-%d') AS 'OrderDate' FROM {$prefix}posts  AS posts	WHERE posts.post_type='shop_order' Order By posts.post_date ASC LIMIT 1";
			$first_order_date = $wpdb->get_var($sql);
			if(empty($first_order_date)){
				$first_order_date = $this->get_date('D');
			}
			return $first_order_date;
		}

		/*
			* Function Name get_blogs_of_user
			*
			* @param string $user_id
			*
			* @param string $all
			*
		*/
		function get_blogs_of_user($user_id = 0, $all = false){
			if(!isset($this->constants['blogs'])){
				$blogs = get_blogs_of_user($user_id, $all);
				foreach ( (array) $blogs as $blog ) {
					$blog_id = $blog->userblog_id;
					switch_to_blog( $blog_id);
					if (current_user_can('manage_woocommerce')){
						$this->constants['blogs'][$blog_id] = $blog;
					}
					restore_current_blog();
				}
				if(!isset($this->constants['blogs'])){
					$this->constants['blogs'] = array();
				}
			}else{
				$blogs = $this->constants['blogs'];
				//$this->print_array($blogs);
			}
			return $this->constants['blogs'];
		}

		/*
			* Function Name get_first_blog_id
			*
			* @param array $blogs
			*
			* @param string $first_blog_id
			*
			* return $first_blog_id
			*
		*/
		function get_first_blog_id($blogs = array(), $first_blog_id = 0){
			$i = 0;
			foreach($blogs as $key => $blog){
				if($i == 0){
					$first_blog_id = $blog->userblog_id;
					return $first_blog_id;
				}
				$i++;
			}
			return $first_blog_id;
		}

		/*
			* Function Name join_two_array
			*
			* @param array $list1
			*
			* @param array $list2
			*
			* return $list1
			*
		*/
		function join_two_array($list1 = array(),$list2 = array()){
			foreach($list2 as $item){
				$list1[] = $item;
			}
			return $list1;
		}

		/*
			* Function Name get_blog_prefix
			*
			* @param string $blog_id
			*
			* @param string $base_prefix
			*
			* return $base_prefix
			*
		*/
		function get_blog_prefix( $blog_id = 0, $base_prefix = '') {
			if ( is_multisite() ) {

				$blog_id = (int) $blog_id;

				if ( defined( 'MULTISITE' ) && ( 0 == $blog_id || 1 == $blog_id ) ){
					return $base_prefix;
				}else{
					return $base_prefix . $blog_id . '_';
				}

			} else {
				return $base_prefix;
			}
		}

		/*
			* Function Name set_site_name_to_list
			*
			* @param string $return
			*
			* return $return
			*
		*/
		function set_site_name_to_list($return){
			$blog_id = $this->constants['blog_id'];
			$blogs   = $this->constants['blogs'];
			foreach($return as $key => $list){
				$return[$key]['site_name'] = isset($blogs[$blog_id]->blogname) ? $blogs[$blog_id]->blogname : '';
				$return[$key]['blog_id'] = $blog_id;
			}
			return $return;
		}

		/*
			* Function Name print_array
			*
			* @param string $ar
			*
			* @param string $display
			*
			* return $output
			*
		*/
		function print_array($ar = NULL,$display = true){
			if($ar){
				$output = "<pre>";
				$output .= print_r($ar,true);
				$output .= "</pre>";

				if($display){
					echo $output;
				}else{
					return $output;
				}
			}
		}

		/*
			* Function Name convert_object_to_array
			*
			* @param string $order_items
			*
			* @param array $return
			*
			* return $return
			*
		*/
		function convert_object_to_array($order_items = NULL,$return = array()){
			if(count($order_items) > 0){
				foreach($order_items as $key => $order_item){
					foreach($order_item as $column_key => $column_vlaue){
						$return[$key][$column_key] = $column_vlaue;
					}
				}
			}

			return $return;
		}

		/*
			* Function Name get_date
			*
			* @param string $default
			*
			* return $mydate
			*
		*/
		function get_date($default ="D"){
			$mydate;
			if ($default=="DT"){
				$mydate= date_i18n("Y-m-d H:i:s");
			}else if ($default=="D"){
				$mydate= date_i18n("Y-m-d");
			}else{
				$mydate= date_i18n("Y-m-d H:i:s");
			}
			return $mydate;
		}

		/*
			* Function Name print_sql
			*
			* @param string $string
			*
			* return $new_str
			*
		*/
		function print_sql($string){

			$string = str_replace("\t", "",$string);
			$string = str_replace("\r\n", "",$string);
			$string = str_replace("\n", "",$string);

			$string = str_replace("SELECT ", "\n\tSELECT \n\t",$string);
			//$string = str_replace(",", "\n\t,",$string);

			$string = str_replace("FROM", "\n\nFROM",$string);
			$string = str_replace("LEFT", "\n\tLEFT",$string);

			$string = str_replace("AND", "\r\n\tAND",$string);
			$string = str_replace("WHERE", "\n\nWHERE",$string);

			$string = str_replace("LIMIT", "\nLIMIT",$string);
			$string = str_replace("ORDER", "\nORDER",$string);
			$string = str_replace("GROUP", "\nGROUP",$string);

			$new_str = "<pre>";
				$new_str .= $string;
			$new_str .= "</pre>";

			echo $new_str;
		}

		/*
			* Function Name get_number_only
			*
			* @param string $value
			*
			* @param string $default
			*
			* return $per_page
			*
		*/
		function get_number_only($value, $default = 0){
			global $options;
			$per_page = (isset($options[$value]) and strlen($options[$value]) > 0)? $options[$value] : $default;
			$per_page = is_numeric($per_page) ? $per_page : $default;
			return $per_page;
		}


		/*
			* Function Name wc_get_order_statuses
			*
			* return $order_statuses
			*
		*/
		function wc_get_order_statuses(){
			if(!isset($this->constants['wc_order_statuses'])){
				if(function_exists('wc_get_order_statuses')){
					$order_statuses = wc_get_order_statuses();
				}else{
					$order_statuses = array();
				}

				$order_statuses['trash']	=	"Trash";

				$this->constants['wc_order_statuses'] = $order_statuses;
			}else{
				$order_statuses = $this->constants['wc_order_statuses'];
			}
			return $order_statuses;
		}

		/**
		* Create HTML dropdown by passing data array or object
		* @param string $data
		* @param string $name
		* @param integer $id
		* @param string $show_option_none
		* @param string $class
		* @param integer $default
		* @param array $type
		* @param bool $multiple = false
		* @param integer $size
		* @param integer $d
		* @param string $display = true
		* @param string $default
		*/
		function create_dropdown($data = NULL, $name = "",$id='', $show_option_none="Select One", $class='', $default ="-1", $type = "array", $multiple = false, $size = 0, $d = "-1", $display = true){
			$count 				= count($data);
			$dropdown_multiple 	= '';
			$dropdown_size 		= '';

			$selected =  explode(",",$default);

			if($count<=0) return '';

			if($multiple == true and $size >= 0){
				//$this->print_array($data);

				if($count < $size) $size = $count + 1;
				$dropdown_multiple 	= ' multiple="multiple"';
				//echo $count;
				$dropdown_size 		= ' size="'.$size.'"  data-size="'.$size.'"';
			}
			$output = "";
			$output .= '<select name="'.$name.'" id="'.$id.'" class="'.$class.'"'.$dropdown_multiple.$dropdown_size.'>';

			//if(!$dropdown_multiple)

			//$output .= '<option value="-1">'.$show_option_none.'</option>';

			if($show_option_none){
				if($default == "all"){
					$output .= '<option value="'.$d.'" selected="selected">'.$show_option_none.'</option>';
				}else{
					$output .= '<option value="'.$d.'">'.$show_option_none.'</option>';
				}
			}

			if($type == "object"){
				foreach($data as $key => $value):
					$s = '';

					if(in_array($value->id,$selected)) $s = ' selected="selected"';
					//if($value->id == $default ) $s = ' selected="selected"';

					$c = (isset($value->counts) and $value->counts > 0) ? " (".$value->counts.")" : '';

					$output .= "\n<option value=\"".$value->id."\"{$s}>".$value->label.$c."</option>";
				endforeach;
			}else if($type == "array"){
				foreach($data as $key => $value):
					$s = '';
					if(in_array($key,$selected)) $s = ' selected="selected"';
					//if($key== $default ) $s = ' selected="selected"';

					$output .= "\n".'<option value="'.$key.'"'.$s.'>'.$value.'</option>';
				endforeach;
			}else{
				foreach($data as $key => $value):
					$s = '';
					if(in_array($key,$selected)) $s = ' selected="selected"';
					//if($key== $default ) $s = ' selected="selected"';
					$output .= "\n".'<option value="'.$key.'"'.$s.'>'.$value.'</option>';
				endforeach;
			}

			$output .= '</select>';
			if($display){
				echo $output;
			}else{
				return  $output;
			}
		}

		function get_selected_order_statusses(){
			$icmsreporting_settings = get_option('icmsreporting_settings',array());
			$shop_order_status		= $this->constants['shop_order_status'];
			$order_status			= isset($icmsreporting_settings['order_status']) ? $icmsreporting_settings['order_status'] : array();

			$order_status			= isset($icmsreporting_settings['order_status']) ? $icmsreporting_settings['order_status'] : array();
			if(count($order_status) == 1){
				if($order_status[0] == 'all'){
					unset($order_status[0]);
				}
			}else{
				if(count($order_status) == 0 and count($shop_order_status) > 0){
					foreach($shop_order_status as $key => $value){
						$order_status[] = 'wc-'.$value;
					}
				}
			}
			return $order_status;
		}
    }
}
