<?php
	/*
	Plugin Name: Advanced uploader
	Plugin URI: 
	Description: This plugin provides an interface for uploading files.  Features - large files to upload to your site even on shared host with http upload limit.  creates thumbnails in the browser including pdf thumbnails.
	Version: 3.2
	Author: Oli Redmond
	Author URI: 
	*/
	
	//initailise variables
	$adv_file_upload_admin_page = "";
	$version = "3.2";
	
	add_action( 'admin_enqueue_scripts', 'adv_file_upload_admin_init' );
	add_action( 'admin_menu', 'adv_file_upload_admin_menu', 0);
	add_action( 'wp_ajax_adv_upload_plupload', 'adv_upload_plupload' );
	add_action( 'wp_ajax_adv_file_upload_thumbs', 'adv_upload_thumbs' );
	add_action( 'wp_ajax_adv_file_upload_set_loader', 'adv_file_upload_set_loader' );
	add_action( 'wp_ajax_adv_file_upload_scan', 'adv_file_upload_scan' );
	add_action( 'wp_ajax_adv_file_upload_new_post', 'adv_upload_new_post' );  //????

	function adv_file_upload_admin_init() {
		//Register Plupload 2.1.1
		wp_register_script( 'plupload2', plugins_url('/js/plupload.full.min.js', __FILE__), array( 'jquery', 'jquery-ui-dialog' ), '2.1.1');
		
		// Register settings scripts
		wp_register_script( 'adv-file-upload-settings', plugins_url('/js/upload-settings.min.js', __FILE__), array( 'jquery', 'jquery-ui-dialog' ), '1.0');

		// register style
		wp_register_style('adv-file-upload-css', plugins_url('/css/upload.css', __FILE__), array( 'wp-jquery-ui-dialog') );

		//check version number and include newer version plupload if wordpress version is older then 3.9
		global $wp_version;
		if( get_option('adv_file_upload_replace_default') && version_compare( $wp_version, 3.9 ) < 0 ) {
			wp_deregister_script('plupload');
			wp_deregister_script('plupload-html5');
			wp_deregister_script('plupload-flash');
			wp_deregister_script('plupload-silverlight');
			wp_deregister_script('plupload-html4');
			wp_register_script( 'plupload', null, array( 'plupload2' ), '1.0');
			wp_register_script( 'plupload-html5', null, array( 'plupload2' ), '1.0');
			wp_register_script( 'plupload-flash', null, array( 'plupload2' ), '1.0');
			wp_register_script( 'plupload-silverlight', null, array( 'plupload2' ), '1.0');
			wp_register_script( 'plupload-html4', null, array( 'plupload2' ), '1.0');
		}
	}
	
	//function to recursively go through categories
	function adv_file_upload_cat_list($parnet, $cats, $excludes) {
		$args = array(
			'orderby'	=> 'name',
			'hide_empty'	=> false,
			'parent'	=> $parnet
		);
		$categories = get_categories($args);
		foreach ($categories as $category) {
			if( !in_array( $category->name, $excludes ) ) {
				$cats[] = array( 'id' => $category->term_id, 'name' => $category->name, 'parent' => $category->parent);
				$cats = adv_file_upload_cat_list( $category->term_id, $cats, $excludes );
			}
		}
		return $cats;
	}

	function adv_admin_inline_js($hook) {
		global $adv_file_upload_admin_page, $wpdb;
		//get plugin's options
  		$destinations = get_option('adv_file_upload_destination');
  		if( $destinations == false )
  			$destinations = array();
  		$gallery = get_option('adv_file_upload_gallery');
  		$bws = get_option('adv_file_upload_bws');
  		$cat = get_option('adv_file_upload_cat');
  		$cats = array();
  		$progress = get_option('adv_file_upload_progress');
		$override = get_option('adv_file_upload_overide_header_calc');
  		$maxFileSize = '1073741824';

		//set the default location
		$upload_dir = wp_upload_dir();
		$default_dir = str_replace ( ABSPATH, '', $upload_dir['path'] );
		
		$js_var = '';

		// get list wordpress galleries
		if ($gallery) {
			$query = "
			        SELECT	$wpdb->posts.ID, $wpdb->posts.post_title, $wpdb->posts.post_name
			        FROM	$wpdb->posts 
				WHERE	$wpdb->posts.post_type IN ('post','page')
				AND	$wpdb->posts.post_status IN ('publish','private','protected')
				AND	$wpdb->posts.post_content LIKE '%[gallery%ids=%'
			";
			$galleries = $wpdb->get_results($query);
			
			//option to create new gallery
			$destinations[] = Array ('label' => 'Create New Gallery',
						 'dest' => $default_dir,
					 	 'library' => true,
						 'type' => "Wordpress Gallery",
						 'id' => 'new');
						 
			//show existing Galleries
			foreach( $galleries as $gallery ) {
				$destinations[] = Array ('label' => $gallery->post_title,
							 'dest' => $default_dir,
						 	 'library' => true,
							 'type' => "Wordpress Gallery",
							 'id' => $gallery->ID);
			}
		}
				
		// get list BWS galleries
		if ($bws) {
			//get BWS galleries
			$args = array(
				'post_type'		=> 'gallery',
		        	'post_status'      => array('publish','private','protected'),
			);	
			
			$query = new WP_Query( $args ); 
			$gllr_options = get_option( 'gllr_options' );
			if ( $query->have_posts() ) { 
				foreach ($query->posts as $post) {
					$destinations[] = Array ('label' => $post->post_title,
								 'dest' => $default_dir,
							 	 'library' => true,
								 'type' => "BWS Gallery",
								 'id' => $post->ID);
				}
			}
			/* Restore original Post Data */
			wp_reset_postdata();
		}

		// get list of Categories
		if ($cat) {
			$excludes = explode (',', get_option( 'adv_file_upload_exc_cat', 'Uncategorized' ));
			$cats = adv_file_upload_cat_list( 0, array(), $excludes );
		}
		
		if ($progress) {
			//added Style sheet based on settings
			echo "<style type='text/css'>\n";
			echo ".media-item .bar{background-color:$progress !important;";
			echo "background-image:-webkit-linear-gradient(bottom, rgba(0,0,0,0.3), rgba(0,0,0,0)) !important;";
			echo "background-image:-moz-linear-gradient(bottom, rgba(0,0,0,0.3), rgba(0,0,0,0)) !important;";
			echo "background-image:-o-linear-gradient(bottom, rgba(0,0,0,0.3), rgba(0,0,0,0)) !important;";
			echo "background-image:linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0)) !important;}";
			echo ".drag-drop.drag-over #drag-drop-area{border-color:$progress !important;}\n";
			echo ".uploader-window{background-color:$progress !important;";
			echo "filter: alpha(opacity=90) !important;";
			echo "opacity:0.9 !important;}\n";
			echo "</style>\n";
		}
		
		if ( $adv_file_upload_admin_page == $hook ) {
			echo "<style type='text/css'>\n";
			echo ".upload-flash-bypass{display:none;}\n";
			echo ".max-upload-size{display:none;}\n";
			echo "</style>\n";
		}
		
		if ( $adv_file_upload_admin_page != $hook 
			&& (get_user_setting('adv_uploader') == 'false' || isset( $_GET['default-uploader'] )) )
			$js_var .= "var adv_uploader = false;\n";
		else
			$js_var .= "var adv_uploader = true;\n";

		if ( get_option('adv_file_upload_replace_default') )
			$js_var .= "var adv_replace_default = true;\n";
		else
			$js_var .= "var adv_replace_default = false;\n";

		if ( is_numeric( $override ) )
			$js_var .= "var override_header_calc = $override;\n";
		else
			$js_var .= "var override_header_calc = false;\n";

		$js_var .= "var adv_max_file_size = $maxFileSize;\n";

		//get images sizes
                // make thumbnails and other intermediate sizes
                global $_wp_additional_image_sizes;

		$js_var .= "var sizes = new Array();\n";
		$intermediate_sizes = get_intermediate_image_sizes();
		if (!is_array($intermediate_sizes)) {
			$intermediate_sizes = array();
		}
		foreach ( $intermediate_sizes as $s ) {
			$js_var .= "sizes['" . $s . "'] = new Array();\n";

                        if ( isset( $_wp_additional_image_sizes[$s]['width'] ) )
                                $js_var .= "sizes['" . $s . "']['width'] = '" . intval( $_wp_additional_image_sizes[$s]['width'] ) . "';\n"; // For theme-added sizes
                        else
                                $js_var .= "sizes['" . $s . "']['width'] = '" . get_option( "{$s}_size_w" ) . "';\n"; // For default sizes set in options
                        if ( isset( $_wp_additional_image_sizes[$s]['height'] ) )
                                $js_var .= "sizes['" . $s . "']['height'] = '" . intval( $_wp_additional_image_sizes[$s]['height'] ) . "';\n"; // For theme-added sizes
                        else
                                $js_var .= "sizes['" . $s . "']['height'] = '" . get_option( "{$s}_size_h" ) . "';\n"; // For default sizes set in options
                        if ( isset( $_wp_additional_image_sizes[$s]['crop'] ) )
                                $js_var .= "sizes['" . $s . "']['crop'] = '" . intval( $_wp_additional_image_sizes[$s]['crop'] ) . "';\n"; // For theme-added sizes
                        else
                                $js_var .= "sizes['" . $s . "']['crop'] = '" . get_option( "{$s}_crop" ) . "';\n"; // For default sizes set in options
                }

		$js_var .= 'var destinations = new Array();'."\n";
		//add default location
		$js_var .= "destinations[0] = new Array();\n";
		$js_var .= "destinations[0][0] = \"Default\";\n";
		$js_var .= 'destinations[0][1] = "'.$default_dir."\";\n";
		$js_var .= 'destinations[0][2] = "";'."\n";
		$js_var .= 'destinations[0][3] = "";'."\n";
		$js_var .= 'destinations[0][4] = true;'."\n";

		$index = 1;
		foreach( $destinations as $dest ) {
			if( !isset( $dest['error'] ) && is_dir(ABSPATH . $dest['dest']) ) {
				$js_var .= 'destinations[' . $index . '] = new Array();'."\n";
				$js_var .= 'destinations[' . $index . '][0] = "' . $dest['label'] . "\";\n";
				$js_var .= 'destinations[' . $index . '][1] = "' . $dest['dest'] . "\";\n";
				$js_var .= 'destinations[' . $index . '][2] = "' . $dest['type'] . "\";\n";
				$js_var .= 'destinations[' . $index . '][3] = "' . $dest['id'] . "\";\n";
				$lib = ($dest['library']) ? "true" : "false";
				$js_var .= 'destinations[' . $index . '][4] = ' .$lib . ';'."\n";
				$index++;
			}
		}

		//show categories
		if ($cat) {
				$js_var .= 'destinations[' . $index . '] = new Array();'."\n";
				$js_var .= 'destinations[' . $index . '][0] = "Add to category";'."\n";
				$js_var .= 'destinations[' . $index . '][1] = "'.$default_dir."\";\n";
				$js_var .= 'destinations[' . $index . '][2] = "Category";'."\n";
				$js_var .= 'destinations[' . $index . '][3] = "";'."\n";
				$js_var .= 'destinations[' . $index . '][4] = true;'."\n";
		}
		
		$js_var .= 'var categories = new Array();'."\n";
		$index = 0;
		foreach( $cats as $value ) {
			$js_var .= 'categories[' . $index . "] = new Array();\n";
			$js_var .= 'categories[' . $index . '][0] = "' . $value['id'] . "\";\n";
			$js_var .= 'categories[' . $index . '][1] = "' . $value['name'] . "\";\n";
			$js_var .= 'categories[' . $index . '][2] = "' . $value['parent'] . "\";\n";
			$index++;
		}

		$js_var .= 'var pluginPath = "'.plugins_url( '' , __FILE__ ).'/js/";'."\n";
		$js_var .= 'var security = "' . wp_create_nonce( 'alt_upl_nonce' . get_current_user_id() ) . "\";\n";
		$js_var .= 'PDFJS.workerSrc = "'.plugins_url( '' , __FILE__ ).'/js/pdf.worker.js";'."\n";

		return $js_var;
	}
	
	function adv_file_upload_admin_menu() {
		global $adv_file_upload_admin_page;
		/* Register our plugin page */
		if (!get_option('adv_file_upload_replace_default')) {
			$adv_file_upload_admin_page = add_media_page( 
					__('Advanced uploader','adv-file-upload'), // The Page title
	        			__('Advanced uploader','adv-file-upload'), // The Menu title
					'upload_files', // The capability required for access to this item
					'adv-file-upload', // the slug to use for the page in the URL
					'adv_file_upload_manage_menu' // The function to call to render the page
	                       );
	        }
	}
	
	function adv_file_upload_admin_scripts($hook) {
		global $adv_file_upload_admin_page, $version;
		if( $adv_file_upload_admin_page == $hook || get_option('adv_file_upload_replace_default') ) {
			//register scripts here to make them work on plugin pages
			wp_register_script( 'adv-file-upload', plugins_url('/js/upload.min.js', __FILE__), array( 'plupload', 'adv-file-upload-pdf-js', 'adv-file-upload-id3-js', 'jquery-ui-autocomplete', 'jquery-ui-dialog' ), $version);

			//TBD wp_register_script( 'adv-file-upload-spark-md5', plugins_url('/js/spark-md5.min.js', __FILE__) );
			wp_register_script( 'adv-file-upload-pdf-js', plugins_url('/js/pdf.js', __FILE__) );
			wp_register_script( 'adv-file-upload-id3-js', plugins_url('/js/id3.min.js', __FILE__) );

			//enqueue scripts and style
			wp_enqueue_script( 'adv-file-upload' );
			wp_enqueue_style( 'adv-file-upload-css' );
		}
	}
	add_action('admin_enqueue_scripts', 'adv_file_upload_admin_scripts');
	add_action('wp_enqueue_media', 'adv_file_upload_admin_scripts');

	function adv_file_upload_set_loader() {
		set_user_setting('adv_uploader', $_REQUEST['loader']);
		exit();
	}
	
	function adv_file_upload_scan() {
		//check_ajax_referer('alt_upl_nonce' . get_current_user_id(),'security');
		$upload_dir = wp_upload_dir();
		$directories = array();
		$cur_dest = array();
		
		//search through existing destinations so that they can be matched
		$dests = get_option('adv_file_upload_destination');
		if( $dests != false )
	  		foreach( $dests as $dest )
	  			$cur_dest[$dest[dest]] = array(
							"label" => $dest[label],
							"library" => $dest[library],
						);

		//scan directory forpossible destinations
		$directories = adv_file_upload_traverseDirTree( $upload_dir['basedir'], '/', $directories, $cur_dest );
		echo json_encode( $directories );
		wp_die();
	}
	
	function adv_file_upload_traverseDirTree($root, $base, $directories, $cur_dest){
		$default_dir = str_replace ( ABSPATH, '', $root );
		$subdirectories=opendir($root.$base);
		while (($subdirectory=readdir($subdirectories))!==false){
			$path=$base.$subdirectory;
			if (is_dir( $root.$path )) {
				if ( ($subdirectory!='..') && ($subdirectory!='.') 
				     && !(ctype_digit( $subdirectory ) && strlen( $subdirectory ) == 4 && ( 1970 <= $subdirectory ) && ($subdirectory <= date('Y') )) ) {
					if( array_key_exists( $default_dir.$path, $cur_dest ) )
						$directories[] = array(
							"dest" => $default_dir.$path,
							"label" => $cur_dest[$default_dir.$path]["label"],
							"library" => $cur_dest[$default_dir.$path]["library"],
							"current" => true,
						);
					else
						$directories[] = array(
							"dest" => $default_dir.$path,
							"label" => "",
							"library" => "1",
							"current" => false,
						);
					$directories = adv_file_upload_traverseDirTree( $root, $path.'/', $directories, $cur_dest );
				}
			}
		}
		
		return $directories;
	}
		
	function adv_file_upload_manage_menu() {
		if ( !current_user_can( 'upload_files' ) )  {
			wp_die( __( 'You do not have permission to upload files.' ) );
		}

		wp_enqueue_script('plupload-handlers');

		echo "<div id='upload_form' class='wrap'>";
		screen_icon('upload');
		echo "<h2>Advanced uploader</h2>";
		echo "<form id='file-form' class='media-upload-form'>";
		media_upload_form();
		echo '<div id="media-items" class="hide-if-no-js"></div>';
		echo "</form>";
		echo "<script type='text/javascript'>\n";
		echo "var post_id=0;\n";
		echo "</script>\n";
	}

	function adv_upload_add_file ($name, $target_path, $parent_id, $sizes, $dest, $galleryType, $galleryID) {
		$upload_dir = wp_upload_dir();
		$wp_filetype = wp_check_filetype($name);

		//make sure target_path has a DIRECTORY_SEPARATOR at the end
		if(substr($target_path,-1)!=DIRECTORY_SEPARATOR)
			$target_path .= DIRECTORY_SEPARATOR;
		
		//set parent ID to add picture to BWS gallery
		if ($galleryType == 'BWS Gallery')
			$parent_id = $galleryID;
		
		//change target path to url
		$guid = str_replace ( $upload_dir['basedir'], $upload_dir ['baseurl'], $target_path );
		$guid = str_replace ( "\\", "/", $guid );
		
		$attachment = array(
		     'guid' => $guid . $name, 
		     'post_mime_type' => $wp_filetype['type'],
		     'post_title' => preg_replace('/\.[^.]+$/', '', $name),
		     'post_content' => '',
		     'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment, $target_path . $name, $parent_id);
		// you must first include the image.php file
		// for the function wp_generate_attachment_metadata() to work
		require_once(ABSPATH . 'wp-admin/includes/image.php');

		//get reduced size images imformation from file
		if($sizes != null && count($sizes) > 0) {
			if (pathinfo($name, PATHINFO_EXTENSION) == 'pdf') {
				//Horizontal size of large image of the pdf, in pixels.
				$attach_data["width"] = $sizes["large"]["width"];
				//Vertical size of large image of the pdf, in pixels.
				$attach_data["height"] = $sizes["large"]["height"];
			} else {
				//get image dimentions
				$size = getimagesize ($target_path . $name);
				//Horizontal size of image attachment, in pixels.
				$attach_data["width"] = $size[0];
				//Vertical size of image attachment, in pixels.
				$attach_data["height"] = $size[1];
				//get additional picture meta data
				$attach_data["image_meta"] = wp_read_image_metadata($target_path . $name);
			}
			
			//Path to image attachment, relative to the currently configured uploads directory.
			//need to add logic to check if the upload dir is organised into year/month.
			$rel_path = str_replace ( $upload_dir['basedir'] . DIRECTORY_SEPARATOR, '', $target_path );
			$rel_path = untrailingslashit( $rel_path ) . DIRECTORY_SEPARATOR;
			$attach_data["file"] =  $rel_path . urlencode($name);
			if (pathinfo($name, PATHINFO_EXTENSION) == 'pdf')
			    $sizes["full"] = $sizes["large"];
			
			$attach_data["sizes"] = $sizes;
		} else {
			$attach_data = wp_generate_attachment_metadata( $attach_id, $target_path . $name );
		}
		wp_update_attachment_metadata( $attach_id, $attach_data );
		return $attach_id;
	}
	
	// ------------------------------------------------------------------
	// fields and settings during admin_init
	// ------------------------------------------------------------------
	//
	
	function adv_file_upload_settings_api_init() {
		// Add the section to reading settings so we can add our
		// fields to it
		add_settings_section('adv_file_upload',
			'Advanced uploader',
			'adv_file_upload_setting_section',
			'media');

		// Add the field with the names and function to use for our new
		// settings, put it in our new section
		add_settings_field('adv_file_upload_destination',
			'Destinations',
			'adv_file_upload_setting_dest',
			'media',
			'adv_file_upload');

		add_settings_field('adv_file_upload_progress',
			'Colour of progress bar',
			'adv_file_upload_setting_prog',
			'media',
			'adv_file_upload');

		add_settings_field('adv_file_upload_gallery',
			'Include Wordpress Galleries',
			'adv_file_upload_setting_gallery',
			'media',
			'adv_file_upload');

		add_settings_field('adv_file_upload_bws',
			'Include BWS Galleries',
			'adv_file_upload_setting_bws',
			'media',
			'adv_file_upload');

		add_settings_field('adv_file_upload_cat',
			'Include Categories',
			'adv_file_upload_setting_cat',
			'media',
			'adv_file_upload');

		add_settings_field('adv_file_upload_exc_cat',
			'Categories to exclude',
			'adv_file_upload_setting_exc_cat',
			'media',
			'adv_file_upload');

		add_settings_field('adv_file_upload_overide_header_calc',
			'Overide form header calulation',
			'adv_file_upload_settings_overide_header_calc',
			'media',
			'adv_file_upload');
			
		add_settings_field('adv_file_upload_replace_default',
			'Replace Default Uploader',
			'adv_file_upload_setting_replace_default',
			'media',
			'adv_file_upload');

		// Register our setting so that $_POST handling is done for us and
		// our callback function just has to echo the <input>
		register_setting('media','adv_file_upload_destination','adv_file_upload_validate_destination');
		register_setting('media','adv_file_upload_progress','adv_file_upload_validate_progress');
		register_setting('media','adv_file_upload_gallery');
		register_setting('media','adv_file_upload_bws');
		register_setting('media','adv_file_upload_cat');
		register_setting('media','adv_file_upload_exc_cat');
		register_setting('media','adv_file_upload_overide_header_calc', 'adv_file_upload_validate_overide_header_calc');
		register_setting('media','adv_file_upload_replace_default');
	}
	add_action('admin_init', 'adv_file_upload_settings_api_init');
	
	
	// ------------------------------------------------------------------
	// Settings link
	// ------------------------------------------------------------------
	//
	// This function adds settings link to plugin page.  
	//
	
	//Add the filter with your plugin information
	add_filter( 'plugin_action_links_' . 'advanced-uploader/upload.php', 'adv_file_upload_setting_action_links' );
	
	//The callback function to add the settings link
	function adv_file_upload_setting_action_links( $links ) {
		array_unshift( $links, '<a href="'. get_admin_url(null, 'options-media.php') .'">Settings</a>' );
		return $links;
	}

	// ------------------------------------------------------------------
	// Settings section callback function
	// ------------------------------------------------------------------
	//
	// This function is needed for the new section.  
	//
	
	function adv_file_upload_setting_section () {
		wp_enqueue_script( 'adv-file-upload-settings' );
		wp_enqueue_style (  'wp-jquery-ui-dialog' );
		echo '<p>Settings for the Advanced uploader plugin</p>';
	}
	
	// ------------------------------------------------------------------
	// Default Override Callback function
	// ------------------------------------------------------------------
	//
	// creates a checkbox for overiding default 
	//
	
	function adv_file_upload_setting_replace_default () {
		$default = get_option('adv_file_upload_replace_default');
 		echo "<input name='adv_file_upload_replace_default' id='adv_file_upload_replace_default' type='checkbox' value='1' class='code' " . checked( 1, $default, false ) . " />";
	}
	
	// ------------------------------------------------------------------
	// Wordpress Galleries Callback function
	// ------------------------------------------------------------------
	//
	// creates a checkbox for Wordpress Galleries settings
	//
	
	function adv_file_upload_setting_gallery () {
		$gallery = get_option('adv_file_upload_gallery');
 		echo "<input name='adv_file_upload_gallery' id='adv_file_upload_gallery' type='checkbox' value='1' class='code' " . checked( 1, $gallery, false ) . " />";
 		echo " Selecting this will include Wordpress Galleries as destinations<br />";
	}
	
	// ------------------------------------------------------------------
	// BWS Galleries Callback function
	// ------------------------------------------------------------------
	//
	// creates a checkbox for BWS Galleries settings
	//
	
	function adv_file_upload_setting_bws () {
		$bws = get_option('adv_file_upload_bws');
 		echo "<input name='adv_file_upload_bws' id='adv_file_upload_bws' type='checkbox' value='1' class='code' " . checked( 1, $bws, false ) . " />";
 		echo " Selecting this will include BWS Galleries as destinations<br />";
 		echo "<i>Note: BWS Gallery needs to be active to use this feature</i>";
	}
	
	// ------------------------------------------------------------------
	// Category Callback functions
	// ------------------------------------------------------------------
	//
	// creates a checkbox for Category settings
	//
	
	function adv_file_upload_setting_cat () {
		$cat = get_option('adv_file_upload_cat');
 		echo "<input name='adv_file_upload_cat' id='adv_file_upload_cat' type='checkbox' value='1' class='code' " . checked( 1, $cat, false ) . " />";
 		echo " Selecting this will include Categories as destinations<br />";
 		echo "<i>Note: It is recommend to have Media Categories By Eddie Moya to be active to use this feature</i>";
	}
	
	//
	// creates a text box for Category exclusion settings
	//
	
	function adv_file_upload_setting_exc_cat () {
		$cat = get_option('adv_file_upload_exc_cat','Uncategorized');
 		echo "<input name='adv_file_upload_exc_cat' id='adv_file_upload_cat' type='test' value='$cat' style='width:100%'/>";
 		echo "<i>Note: Enter category names separated with a comma, parent categories will exclude child categories</i>";
	}
	
	// ------------------------------------------------------------------
	// Destinations Callback function
	// ------------------------------------------------------------------
	//
	// creates a boxes for destination settings
	//
	
	function adv_file_upload_setting_dest() {
		$destinations = get_option('adv_file_upload_destination');

		echo "<div style='float:left;'>";
		$index=0;

		//headings
		echo "<div id='adv_file_upload_destination_headiings'  style='overflow:hidden;'>\n";
		echo "<div style='float:left;width:135px;'>Label</div>\n";
		echo "<div style='float:left;width:285px;'>Destination</div>\n";
		echo "<div style='float:left;'>Add to Library</div>\n";
		echo "</div>\n";
		echo "<div id='adv_file_upload_destinations' style='clear:both'>\n";
		
		// show default location
		$upload_dir = wp_upload_dir();
		$default_dir = str_replace ( ABSPATH, '', $upload_dir['path'] );
		$base_dir = str_replace ( ABSPATH, '', $upload_dir['basedir'] );
		
		echo "<script type='text/javascript'>var adv_upload_base_dir = '$base_dir'</script>\n";
		echo "<div id='adv_file_upload_destination_default'  style='overflow:hidden;'>\n";
		echo "<input id='adv_file_upload_destination_default' type='text' value='Default' style='float:left;width:135px;' disabled />\n";
		echo "<input id='adv_file_upload_destination_destination_default' type='text' value='$default_dir' style='float:left;width:285px;' disabled />\n";
 		echo "<input id='adv_file_upload_destination_library_default' type='checkbox' value='1' style='float:left;margin:5px;' checked disabled />\n";
		echo "</div>\n";

		if (isset($destinations) && $destinations) {
		    foreach ($destinations as $dest) {
		        $labelStyle = $destStyle = $libStyle = "";
    			if( array_key_exists( 'error', $dest ) ) {
    				if( array_key_exists( 'label', $dest['error'] ) )
    					$labelStyle = "background-color: #ffebe8;";
    				if( array_key_exists( 'dest', $dest['error'] ) )
    					$destStyle = "background-color: #ffebe8;";
    				if( array_key_exists( 'library', $dest['error'] ) )
    					$libStyle = "background-color: #ffebe8;";
    			}
    			echo "<div id='adv_file_upload_destination_$index'  style='overflow:hidden;'>";
    			echo "<input id='adv_file_upload_destination_label_$index' name='adv_file_upload_destination[$index][label]' type='text' value='{$dest['label']}' style='float:left;width:135px;$labelStyle' />";
    			echo "<input id='adv_file_upload_destination_destination_$index' name='adv_file_upload_destination[$index][dest]' type='text' value='{$dest['dest']}' style='float:left;width:285px;$destStyle' />";
     			echo "<input name='adv_file_upload_destination[$index][library]' id='adv_file_upload_destination_library_$index' type='checkbox' value='1' style='float:left;margin:5px;$libStyle' " . checked( 1, $dest['library'], false ) . " />";
    			// add delete button
    			echo "<input type='button' name='del_dest' id='del_dest_$index' class='button button-primary' value='-' style='width:2.5em;float:right;' onClick='removeButton(this)'/>";
    			echo "</div>";
    			$index++;
		    }
	    }
		
		// add new button
		echo "</div>\n";
		echo "<input type='hidden' id='index' value='$index' />\n";
		echo '<input type="button" name="new_dest" id="new_dest" class="button button-primary" value="+" style="width:2.5em;float:right;" onClick="addButton()" />'."\n";
		echo "</div>\n";
		echo "<p class='clear'>";
		echo "<input type='button' name='del_dest' id='del_dest_$index' class='button button-primary' value='Scan' onClick='scanButton()'/>";
		echo " Scan Uploads directory for new destinations</p>\n";
 		echo "<p><i>Note: Thumbnail images are only created when adding to Wordpress Library</i></br>\n";
 		echo "<i>Note: When adding to Wordpress Library your directory needs to be within the default upload directory</i></p>\n";
	}
	
	// ------------------------------------------------------------------
	// Validate destination field
	// ------------------------------------------------------------------
	//
	// This function is needed to check that the destiantion exist 
	//
	
	function adv_file_upload_validate_destination ($input) {
		if (!isset($input))
			return $input;
		$valid_input = array();  
		foreach ($input as $id => $dest) {
            // register destination requires a label  
            if( $dest['label'] == '' ) {  
                    add_settings_error(  
                        'Destination', // setting title  
                        0, // error ID  
                        __('A label is required for all destinations','wptuts_textdomain'), // error message  
                        'error' // type of message  
                    );  
             	$valid_input[$id]['error']['label'] = true;
             }
             
            // register destination does not exist error  
            if( is_dir( ABSPATH . $dest['dest'] ) == FALSE ) {  
                    add_settings_error(  
                        'Destination', // setting title  
                        1, // error ID  
                        __('Expecting a valid directory! Please fix, the directory needs to exist in the filesystem first.','wptuts_textdomain'), // error message  
                        'error' // type of message  
                    );  
             	$valid_input[$id]['error']['dest'] = true;
             }
             
            // register destination does is not in uploads directory error 
            $upload_dir = wp_upload_dir(); //get upload base dir
            $default_dir = str_replace ( ABSPATH, '', $upload_dir['basedir'] ); //remove site root
            if( array_key_exists('library',$dest) && !preg_match( '#^' . $default_dir . '/#', $dest['dest'] ) 
                && !preg_match( '#^' . $default_dir . '$#', $dest['dest'] ) ) {  
                    add_settings_error(  
                        'Destination', // setting title  
                        2, // error ID  
                        __('To add to Wordpress Library Destination must be in within uploads directory! Please fix.','wptuts_textdomain'), // error message  
                        'error' // type of message  
                    );  
             	$valid_input[$id]['error']['library'] = true;
             }
             $valid_input[$id]['label'] = $dest['label'];
             $valid_input[$id]['dest'] = rtrim($dest['dest'], "/");
             $valid_input[$id]['library'] = array_key_exists('library',$dest) ? $dest['library'] : 0;
             $valid_input[$id]['type'] = "";
             $valid_input[$id]['id'] = "";
		}
		return $valid_input;
	}
	
	// ------------------------------------------------------------------
	// Progress Bar Colour Callback function
	// ------------------------------------------------------------------
	//
	// creates a boxes for destination settings
	//
	
	function adv_file_upload_setting_prog() {
		$progress = get_option('adv_file_upload_progress');
		
		$errors = get_settings_errors( 'adv_file_upload_progress');

        $destStyle = "";
		foreach ($errors as $error) {
			if ($error['code']) {
				$destStyle = "background-color: #ffebe8;";
				$progress = $error['code'];
			}
		}
		// show Progress colour
		echo "<input id='adv_file_upload_progress' name='adv_file_upload_progress' size='10' type='text' value='$progress' style='float:left;$destStyle'/>";
 		echo "This should be an HTML colour code. e.g. #0063a6<br />";
 		echo "<i>Note: Leave blank for browser default</i>";
	}
	
	// ------------------------------------------------------------------
	// Validate Progress Bar Colour field
	// ------------------------------------------------------------------
	//
	// This function is needed to check that the Progress Bar Colour is a valid colour
	//
	
	function adv_file_upload_validate_progress ($input) {
		//add hash to front of colour code if missing
		if(preg_match('/^[a-f0-9]{6}$/i', $input))
			$input = '#' . $input;
		
		//validate valid input (empty for browser default)
		if(preg_match('/^#[a-f0-9]{6}$/i', $input) || $input == '')
			return $input;
		
                add_settings_error(	'adv_file_upload_progress', // setting title
					$input, // error ID
					__('Expecting a valid HTML Colour Code! Please fix Colour of progress bar field.','wptuts_textdomain'), // error message
					'error' // type of message
					);

		return get_option('adv_file_upload_progress');
	}
	
	// ------------------------------------------------------------------
	// Override form header calulation Callback function
	// ------------------------------------------------------------------
	//
	// creates a boxes for Override form header calulation settings
	//
	
	function adv_file_upload_settings_overide_header_calc() {
		$override = get_option('adv_file_upload_overide_header_calc');
		
		$errors = get_settings_errors( 'adv_file_upload_overide_header_calc');

        $destStyle = "";
		foreach ($errors as $error) {
			if ($error['code']) {
				$destStyle = "background-color: #ffebe8;";
				$progress = $error['code'];
			}
		}
		// show Progress colour
		echo "<input id='adv_file_upload_overide_header_calc' name='adv_file_upload_overide_header_calc' size='10' type='text' value='$override' style='float:left;$destStyle'/>";
 		echo "This should be a number of kbytes. e.g. 1<br />";
 		echo "<i>Note: Leave blank for automatic calulation</i>";
	}
	
	// ------------------------------------------------------------------
	// Validate Override form header calulation field
	// ------------------------------------------------------------------
	//
	// This function is needed to check that the Override value is a number
	//
	
	function adv_file_upload_validate_overide_header_calc ($input) {
		//validate valid input (empty for automatic calulation)
		if(preg_match('/^[0-9]+$/i', $input) || $input == '')
			return $input;
		
                add_settings_error(	'adv_file_upload_overide_header_calc', // setting title
					$input, // error ID
					__('Expecting a number! Please fix Overide form header calulation.','wptuts_textdomain'), // error message
					'error' // type of message
					);

		return get_option('adv_file_upload_overide_header_calc');
	}
	
	function adv_file_upload_show_attachment_thumb( $link, $id, $size, $permalink, $icon, $text) {
		$id = intval( $id );
		//get meta data
		$meta = wp_get_attachment_metadata($id);

		if ($meta == false) return $link;

		$upload_dir = wp_upload_dir();
		$rel_path = pathinfo ($meta['file'] , PATHINFO_DIRNAME);
		$thumb_path = $upload_dir['basedir'] . '/' . $rel_path . '/' . $meta['sizes'][$size]['file'];
		$thumb_url = $upload_dir['baseurl'] . '/' . $rel_path . '/' . $meta['sizes'][$size]['file'];
		
		//if file exists use thumbnail else use icon
		if (file_exists( $thumb_path )) {
			$link = preg_replace( '/src=\"(.*?)\"/', 'src="'.$thumb_url.'"', $link );
		}
		
		return $link;
	}
	add_filter( 'wp_get_attachment_link', 'adv_file_upload_show_attachment_thumb', 100, 6 );

    function adv_file_upload_attachment_image_src ( $image, $attachment_id, $size, $icon ) {
        return $image;
    }
	add_filter( 'wp_get_attachment_image_src', 'adv_file_upload_attachment_image_src', 100, 6 );

	//if thumbnail exist replace default icon
	function adv_file_upload_change_mime_icon($icon, $mime = null, $post_id = null){
		//get the path and URL to thumbnail and  store globally
		global $thumb_path;
		$thumb_path = "";
		
		//get meta data
		$meta = wp_get_attachment_metadata($post_id);

		if ($meta == false || !isset($meta['sizes'])) return $icon;

		$upload_dir = wp_upload_dir();
		$rel_path = pathinfo ($meta['file'] , PATHINFO_DIRNAME);
		$thumb_path = $upload_dir['basedir'] . '/' . $rel_path . '/' . $meta['sizes']['thumbnail']['file'];
		$thumb_url = $upload_dir['baseurl'] . '/' . $rel_path . '/' . $meta['sizes']['thumbnail']['file'];

		//if file exists use thumbnail else use icon
		if (file_exists( $thumb_path ))
			return $thumb_url;
		else
			return $icon;
	}
	add_filter('wp_mime_type_icon', 'adv_file_upload_change_mime_icon', 100, 3);	//if thumbnail exist replace default icon

	function adv_file_upload_change_icon_dir($icon_dir) {
		//retrive globally stored thumb path
		global $thumb_path;

		//if thumbnail exists use instead icon
		if (file_exists( $thumb_path ))
			return dirname ($thumb_path);
		else
			return $icon_dir;
	}
	add_filter('icon_dir', 'adv_file_upload_change_icon_dir', 100, 1);

	function adv_file_upload_upload_mimes($mimes = array()) {
		// allow SVG file upload
		$mimes['svg'] = 'image/svg+xml';
		
		return $mimes;
	}
	add_filter( 'upload_mimes', 'adv_file_upload_upload_mimes' );

	// Hide image overflow for icons replace with thumbnails in the ADMIN AREA
	function hideoverflow() {
	   echo '<style type="text/css">
	           .attachment{overflow:hidden}
		   .attachment-preview {line-height: 110px;}
		   .attachment-preview img {vertical-align: middle;padding-top:0 !important;max-height: 100%;}
		   .attachment-preview div {line-height: normal;}
	         </style>';
	}
	add_action('admin_head', 'hideoverflow');


	function change_media_send_to_editor($html, $post_id, $attachment) {
		//get display_attachment_image post data
		$size = "";
		$att_img = get_post_meta($post_id,'display_attachment_image');
		if( is_array($att_img) )
			$size = $att_img[0];

		//get meta data
		$meta = wp_get_attachment_metadata($post_id);
		if ($size == '' || $meta == false) return $html;
		
		//remove display_attachment_image from post meta so isn't used incorrectly
		delete_post_meta($post_id, 'display_attachment_image');
		
		$upload_dir = wp_upload_dir();
		$rel_path = pathinfo ($meta['file'] , PATHINFO_DIRNAME);
		$thumb_path = $upload_dir['basedir'] . '/' . $rel_path . '/' . $meta['sizes'][$size]['file'];
		$thumb_url = $upload_dir['baseurl'] . '/' . $rel_path . '/' . $meta['sizes'][$size]['file'];

		//if file exists use thumbnail else use icon
		if (file_exists( $thumb_path )) {
			$html = preg_replace('/(.*href=.*>)(.*)(<.*)/', '\1<img class="alignnone size-' . $size . ' wp-image-' . $post_id 
			. '" src="'.$thumb_url.'" alt="\2" width="' . $meta['sizes'][$size]['width'] 
			. '" height="' . $meta['sizes'][$size]['height'] . '">\3', $html);
		}
		
		return $html;
	}
	add_filter('media_send_to_editor', 'change_media_send_to_editor', 20, 3);


	//add images sizes to pdf attachemnts (if stored in meta) for selection in media library
	function advupl_attachment_fields_to_edit($form_fields, $post) {
	        if ( !preg_match("/^image/", $post->post_mime_type) ) {
			$attachment_url = wp_get_attachment_url( $post->ID );
			$meta = wp_get_attachment_metadata( $post->ID );
            $sizes = array();
            $possible_sizes = apply_filters( 'image_size_names_choose', array(
                    'thumbnail' => __('Thumbnail'),
                    'medium'    => __('Medium'),
                    'large'     => __('Large'),
                    //'full'      => __('Full Size'),
            ) );
            //unset( $possible_sizes['full'] );

            if( isset( $meta['sizes'] ) ) {
	                // Loop through all potential sizes that may be chosen.
 	                foreach ( $possible_sizes as $size => $label ) {
				if ( isset( $meta['sizes'][ $size ] ) ) {
	                                if ( ! isset( $base_url ) )
	                                        $base_url = str_replace( wp_basename( $attachment_url ), '', $attachment_url );
	
	                                // Nothing from the filter, so consult image metadata if we have it.
	                                $size_meta = $meta['sizes'][ $size ];
	
	                                // We have the actual image size, but might need to further constrain it if content_width is narrower.
	                                // Thumbnail, medium, and full sizes are also checked against the site's height/width options.
	                                list( $width, $height ) = image_constrain_size_for_editor( $size_meta['width'], $size_meta['height'], $size, 'edit' );
	
	                                $sizes[ $size ] = "<option value='" . $size . "'>" . $possible_sizes[ $size ] . " - $width x $height</option>";
	                        }
	                }
	                
	                //add image size select if attachment has other images associated
	                if (count ($sizes) > 0) {
		                $sizes['link'] = "<option value='link' selected>Link Only</option>";
	
    				$sizes_array = array(
    					'display_attachment_image_heading' => array(
    						'label'	=> __('<h3>Attachment Image</h3>', 'display_attachment_image'),
    						'input'	=> 'html',
    						'html'	=> ' '
    					),
    					'display_attachment_image' => array(
    						'label'	=> __('Sizes', 'display_attachment_image'),
    						'input'	=> 'html',
    						'html'	=> "<select class='size' name='attachments[$post->ID][display_attachment_image]' "
    							. "id='display_attachment_image-{$post->ID}' data-setting='size' data-user-setting='imgsize'>\n"
    							. implode ($sizes, "\n")
    							. "</select>\n"
    					));
				

		            $form_fields = array_merge( $form_fields, $sizes_array  );
			    }
		    }
        }

        return $form_fields;
	}
	add_filter('attachment_fields_to_edit', 'advupl_attachment_fields_to_edit', 20, 2);
	
	function save_display_attachment_image($post, $attachment_data) {
		// use this filter to add post meta if key exists or delete it if not
		if ( !empty($attachment_data['display_attachment_image']) && $attachment_data['display_attachment_image'] != 'link' )
			update_post_meta($post['ID'], 'display_attachment_image', $attachment_data['display_attachment_image']);
		else
			delete_post_meta($post['ID'], 'display_attachment_image');
		
		// return $post in any case, things will break otherwise
		return $post;
	}
	add_filter('attachment_fields_to_save', 'save_display_attachment_image', 20, 2);
	
	function adv_plupload_default_settings($defaults) {
		if ( get_option('adv_file_upload_replace_default') ) {
			$defaults['flash_swf_url'] = plugins_url('/js/Moxie.swf', __FILE__);
			$defaults['silverlight_xap_url'] = plugins_url('/js/Moxie.xap', __FILE__);
			$defaults['max_retries'] = 3;
		}
		return $defaults;
	}
	add_filter('plupload_default_settings', 'adv_plupload_default_settings');
	add_filter('plupload_init', 'adv_plupload_default_settings');

	//add functions to plupload default settings
	function adv_pre_plupload() {
		global $pagenow, $adv_file_upload_admin_page;
		$screen = get_current_screen();

		if( ( $pagenow == 'media-new.php' && get_option('adv_file_upload_replace_default') ) || $screen->id == $adv_file_upload_admin_page ) {
			echo "<script type='text/javascript'>\n"
			. adv_admin_inline_js($screen->id)
			. "adv_plupload_defaults();\n"
			.  "</script>\n";
		}
	}
	add_action( 'pre-html-upload-ui', 'adv_pre_plupload');

	function adv_plupload_default_script () {
		global $wp_scripts, $pagenow, $adv_file_upload_admin_page;
		$screen = get_current_screen();

		if( get_option('adv_file_upload_replace_default') && 
			!( $pagenow == 'media-new.php' || $screen->id == $adv_file_upload_admin_page ) ) {
			$script = adv_admin_inline_js($screen->id) . "jQuery().ready( function() { adv_plupload_defaults(); } );\n";
	
			$data = $wp_scripts->get_data( 'wp-plupload', 'data' );
			if ( $data )
				$wp_scripts->add_data( 'wp-plupload', 'data', "$data\n$script" );
				
			$data = $wp_scripts->get_data( 'plupload-handlers', 'data' );
			if ( $data )
				$wp_scripts->add_data( 'plupload-handlers', 'data', "$data\n$script" );
		}
	}
	add_action( 'customize_controls_print_footer_scripts', 'adv_plupload_default_script');
	add_action( 'admin_footer', 'adv_plupload_default_script');

	//create new post
	function adv_upload_new_post () {
		check_ajax_referer('alt_upl_nonce' . get_current_user_id(),'security');
		
		$titles = json_decode(stripcslashes($_REQUEST["title"]));
		$results = array();

		foreach( $titles as $title ) {
			// Create post object
			$new_post = array(
			  'post_title'    => $title,
			);
			
			// Insert the post into the database
			$results[] = array( 'id' => wp_insert_post( $new_post ), 'title' => $title);
		}
		
		echo json_encode( $results );
		wp_die();

	}
	
	function adv_upload_thumbs () {
		//check nounce is correct
		check_ajax_referer('alt_upl_nonce' . get_current_user_id(),'security');

		// 5 minutes execution time
		@set_time_limit(5 * 60);
		
		// Get parameters
		$post_id = isset($_REQUEST["post_id"]) ? intval($_REQUEST["post_id"]) : 0;
		$name = isset($_REQUEST["filename"]) ? $_REQUEST["filename"] : '';
		$sizesObj = json_decode(stripcslashes($_REQUEST['meta']));
		//get destinations from JSON object
		$destinations = json_decode(stripcslashes($_REQUEST['destinations']));
		$dest = isset($_REQUEST["fileDest"]) ? intval($_REQUEST["fileDest"]) : 0;
		$album = isset($_REQUEST["album"]) ? stripcslashes($_REQUEST["album"]) : '';
		$targetDir = ABSPATH . $destinations [$dest][1];
		$targetUrl = site_url() . DIRECTORY_SEPARATOR . $destinations [$dest][1];

	    	if( $destinations[$dest][2] == 'Wordpress Gallery' ) {
	    		if( $destinations[$dest][3] == 'new' ) {
	    			$post_id = intval( $album );
	    		} else {
	    			$post_id = $destinations[$dest][3];
	    		}
	    	}
		    	
		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
		
		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];
		
		if (strpos($contentType, "multipart") !== false) {
			for ($i = 0; $i < count($_FILES['thumbs']['name']); $i++) {
				$file = array();
				$file['name'] = $_FILES['thumbs']['name'][$i];
				$file['tmp_name'] = $_FILES['thumbs']['tmp_name'][$i];

				if (isset($file['tmp_name']) && is_uploaded_file($file['tmp_name'])) {
					// write to desired location
					$out = @fopen($targetDir . DIRECTORY_SEPARATOR . $file['name'], "wb");
					if ($out) {
						// Read binary input stream and append it to temp file
						$in = @fopen($file['tmp_name'], "rb");
			
						if ($in) {
							while ($buff = fread($in, 4096))
								fwrite($out, $buff);
						} else {
							echo json_encode( array(
								'success' => false,
								'data'    => array(
									'message'  => 'Failed to open input stream.',
									'code'  => 101,
									'filename' => $_FILES['async-upload']['name'],
								)
							) );
					
							wp_die();
						}
						@fclose($in);
						@fclose($out);
						@unlink($file['tmp_name']);
					} else {
						echo json_encode( array(
							'success' => false,
							'data'    => array(
								'message'  => 'Failed to open output stream.',
								'code'  => 102,
								'filename' => $_FILES['async-upload']['name'],
							)
						) );
				
						wp_die();
					}
				} else {
					echo json_encode( array(
						'success' => false,
						'data'    => array(
							'message'  => 'Failed to move uploaded file.',
							'code'  => 103,
							'filename' => $_FILES['async-upload']['name'],
						)
					) );
			
					wp_die();
				}
			}
		} else {
			echo json_encode( array(
				'success' => false,
				'data'    => array(
					'message'  => 'Failed to open input stream.',
					'code'  => 104,
					'filename' => $_FILES['async-upload']['name'],
				)
			) );
	
			wp_die();
		}

		if(file_exists($targetDir . DIRECTORY_SEPARATOR . $name)) {
		    	if($sizesObj != null)
	    			foreach ($sizesObj  as $sizeDesc => $array) 
	    				foreach ($array as $element => $content)
	    					$sizes[$sizeDesc][$element] = $content;
	    		else
	    			$sizes = null;
	    		
	    		//add to wordpress library if relevant
	    		if( $destinations[$dest][4] ) {
		    		$attachment_id = adv_upload_add_file ($name,
		    				$targetDir . DIRECTORY_SEPARATOR,
		    				$post_id,
		    				$sizes,
		    				$destinations [$dest][0],
		    				$destinations [$dest][2],
		    				$destinations [$dest][3]);
		    	} else {
		    		//set url to mime type icon or file dependant on Mime type
				$fileInfo = wp_check_filetype( $name );
				$type = wp_ext2type( $fileInfo['ext'] );
				if( preg_match( '/^image/', $fileInfo['type'] ) )
			    		$url = $targetUrl . DIRECTORY_SEPARATOR . $name;
			    	else
			    		$url = wp_mime_type_icon( $type );
			    	
				echo json_encode( array(
					'success' => true,
					'data'    => array( 'id' => false,
							    'url' => $url,
							    'name' => $name)
				) );
				wp_die();
		    	}
		    	
		    	if( $destinations[$dest][2] == 'Category' ) {
		    		$category = get_term_by( 'name', $album, 'category' );
		    		if( $category != false )
		    			wp_set_object_terms( $attachment_id, intval($category->term_id), 'category' );
		    	}

		    	if( $destinations[$dest][2] == 'Wordpress Gallery' ) {
		    		$gallery = get_post( $post_id );
		    		if( $gallery->post_content == "" ) {
					$gal_upd = array(
						'ID'           => $post_id,
						'post_content' => '[gallery link="file" ids="' . $attachment_id . '"]'
					);
					
					// Update the post into the database
					wp_update_post( $gal_upd );
				} elseif( preg_match( '/^(.*\[gallery.+ids=".+)(".*)$/', $gallery->post_content, $matches) ) {
					$gal_upd = array(
						'ID'           => $post_id,
						'post_content' => $matches[1] . ',' . $attachment_id . $matches[2]
					);
					
					// Update the post into the database
					wp_update_post( $gal_upd );
				}
		    		
		    	}
			if ( ! $attachment = wp_prepare_attachment_for_js( $attachment_id ) )
				wp_die();
		
			echo json_encode( array(
				'success' => true,
				'data'    => $attachment,
			) );
			wp_die();
		}
		
		echo json_encode( array(
			'success' => false,
			'data'    => array(
				'message'  => 'Failed to find uploaded file.',
				'code'  => 104,
				'filename' => $_FILES['async-upload']['name'],
			)
		) );
		wp_die();
	}
	
	function adv_upload_plupload() {
		//check nounce is correct
		check_ajax_referer('alt_upl_nonce' . get_current_user_id(),'security');
		
		if ( $_FILES['async-upload']['error'] == 1 ) {
			$displayMaxSize = ini_get('upload_max_filesize');
			
			switch ( substr($displayMaxSize,-1) ) {
				case 'G':
					$displayMaxSize = $displayMaxSize * 1024;
				case 'M':
					$displayMaxSize = $displayMaxSize * 1024;
				case 'K':
					$displayMaxSize = $displayMaxSize * 1024;
			}
			
			$error = 'Posted data is too large. '.$_SERVER[CONTENT_LENGTH].' bytes exceeds the maximum size of '.$displayMaxSize.' bytes.';
			echo json_encode( array(
				'success' => false,
				'data'    => array(
					'message'  => $error,
					'code'  => 105,
					'filename' => $_FILES['async-upload']['name'],
				)
			) );
	
			wp_die();
		}
		
		// Settings
		$cleanupTargetDir = true; // Remove old files
		$maxFileAge = 5 * 3600; // Temp file age in seconds
		
		// 5 minutes execution time
		@set_time_limit(5 * 60);
		
		// Get parameters
		$post_id = isset($_REQUEST["post_id"]) ? intval($_REQUEST["post_id"]) : 0;
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';
		//get destinations from JSON object
		$destinations = json_decode(stripcslashes($_REQUEST['destinations']));
		$dest = isset($_REQUEST["fileDest"]) ? intval($_REQUEST["fileDest"]) : 0;
		$targetDir = ABSPATH . $destinations [$dest][1];
		$targetUrl = site_url() . DIRECTORY_SEPARATOR . $destinations [$dest][1];
		
		// get a valid wordpress filesname
		$fileName = wp_unique_filename($targetDir, $fileName);
		
		$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
		$fileurl = $targetUrl . DIRECTORY_SEPARATOR . $fileName;
		
		// Create target dir
		if (!file_exists($targetDir))
			@mkdir($targetDir);
		
		// Remove old temp files	
		if ($cleanupTargetDir) {
			if (is_dir($targetDir) && ($dir = opendir($targetDir))) {
				while (($file = readdir($dir)) !== false) {
					$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;
		
					// Remove temp file if it is older than the max age and is not the current file
					if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge) && ($tmpfilePath != "{$filePath}.part")) {
						@unlink($tmpfilePath);
					}
				}
				closedir($dir);
			} else {
				echo json_encode( array(
					'success' => false,
					'data'    => array(
						'message'  => 'Failed to open temp directory.',
						'code'  => 100,
						'filename' => $_FILES['async-upload']['name'],
					)
				) );
		
				wp_die();
			}
		}	
		
		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];
		
		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];
		
		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['async-upload']['tmp_name']) && is_uploaded_file($_FILES['async-upload']['tmp_name'])) {
				// Open temp file
				$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
				if ($out) {
					// Read binary input stream and append it to temp file
					$in = @fopen($_FILES['async-upload']['tmp_name'], "rb");
		
					if ($in) {
						while ($buff = fread($in, 4096))
							fwrite($out, $buff);
					} else {
						echo json_encode( array(
							'success' => false,
							'data'    => array(
								'message'  => 'Failed to open input stream.',
								'code'  => 101,
								'filename' => $_FILES['async-upload']['name'],
							)
						) );
				
						wp_die();
					}
					@fclose($in);
					@fclose($out);
					@unlink($_FILES['async-upload']['tmp_name']);
				} else {
					echo json_encode( array(
						'success' => false,
						'data'    => array(
							'message'  => 'Failed to open input stream.',
							'code'  => 102,
							'filename' => $_FILES['async-upload']['name'],
						)
					) );
			
					wp_die();
				}
			} else {
				echo json_encode( array(
					'success' => false,
					'data'    => array(
						'message'  => 'Failed to move uploaded file.',
						'code'  => 103,
						'filename' => $_FILES['async-upload']['name'],
					)
				) );
		
				wp_die();
			}
		} else {
			// Open temp file
			$out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = @fopen("php://input", "rb");
		
				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else {
					echo json_encode( array(
						'success' => false,
						'data'    => array(
							'message'  => 'Failed to open input stream.',
							'code'  => 101,
							'filename' => $_FILES['async-upload']['name'],
						)
					) );
			
					wp_die();
				}
		
				@fclose($in);
				@fclose($out);
			} else {
				echo json_encode( array(
					'success' => false,
					'data'    => array(
						'message'  => 'Failed to open output stream.',
						'code'  => 102,
						'filename' => $_FILES['async-upload']['name'],
					)
				) );
		
				wp_die();
			}
		}
		
		// Check if file has been uploaded
		if (!$chunks || $chunk == $chunks - 1) {
			// rename from temp filename 
			rename("{$filePath}.part", $filePath);
			echo json_encode( array(
				'success' => 'file_complete',
				'data'    => array(
					'filename' => $_FILES['async-upload']['name'],
					'name' => $fileName,
					'file' => $fileurl,
				)
			) );
	
			wp_die();
		}
		
		echo json_encode( array(
			'success' => true,
			'data'    => array(
				'filename' => $_FILES['async-upload']['name'],
			)
		) );

		wp_die();
	}
	
	// fix issue with missing full image for PDF file.
	function adv_uploader_update_attachment_metadata( $metadata, $attachment_id ) {
	    //if( is_admin() && isset( $metadata["sizes"]) && !isset( $metadata["sizes"]["full"] ))
	    //    $metadata["sizes"]["full"] = $metadata["sizes"]["large"];

        $attachment = get_post( $attachment_id );

	    if ( get_post_mime_type( $attachment ) == "image/svg+xml" && empty($metadata) ) {
            $svgfile = simplexml_load_file(get_attached_file( $attachment_id ));
            $metadata['width'] = (int) $svgfile['width'];
            $metadata['height'] = (int) $svgfile['height'];
	    }

	    return $metadata;
	}
	add_filter( 'wp_generate_attachment_metadata', 'adv_uploader_update_attachment_metadata', 20, 2 );

    function adv_uploader_pdf_srcset_meta( $image_meta, $size_array, $image_src, $attachment_id ){
        if( isset( $image_meta['file'] ) ) {
            $file_info = new SplFileInfo( $image_meta['file'] );
        
            if( $file_info->getExtension() == 'pdf' ) {
                $image_meta['file'] = $file_info->getPath() . '/' . $image_meta['sizes']['full']['file'];
            }
        }
        return $image_meta;
    }
    add_filter( 'wp_calculate_image_srcset_meta', 'adv_uploader_pdf_srcset_meta', 10, 4 );

    function adv_uploader_pdf_correct( $downsize, $id, $size ){
        $image_meta = wp_get_attachment_metadata($id);
        if( isset( $image_meta['file'] ) ) {
            $file_info = new SplFileInfo( $image_meta['file'] );
        
            $update_meta = false;
            if( $file_info->getExtension() == 'pdf' ) {
                if( !isset( $image_meta['sizes']['full'] ) ) {
                    $image_meta['sizes']['full'] = $image_meta['sizes']['large'];
                    $update_meta = true;
                }
                
                if( !isset( $image_meta['width'] ) || !is_int( $image_meta['width'] ) ) {
                    $image_meta['width'] = $image_meta['width']['full']['file']['width'];
                    $update_meta = true;
                }
                
                if( !isset( $image_meta['height'] ) || !is_int( $image_meta['height'] ) ) {
                    $image_meta['height'] = $image_meta['width']['full']['file']['height'];
                    $update_meta = true;
                }

                if( $update_meta ) {
                    wp_update_attachment_metadata( $id, $image_meta );
                }
            }
        }
        return false;
    }
    add_filter( 'image_downsize', 'adv_uploader_pdf_correct', 10, 3 );
?>