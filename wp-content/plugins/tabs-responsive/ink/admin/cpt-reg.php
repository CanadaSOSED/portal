<?php
$labels = array(
				'name'                => _x( 'Tabs Responsive', 'Tabs Responsive', wpshopmart_tabs_r_text_domain ),
				'singular_name'       => _x( 'Tabs Responsive', 'Tabs Responsive', wpshopmart_tabs_r_text_domain ),
				'menu_name'           => __( 'Tabs Responsive', wpshopmart_tabs_r_text_domain ),
				'parent_item_colon'   => __( 'Parent Item:', wpshopmart_tabs_r_text_domain ),
				'all_items'           => __( 'All Tabs', wpshopmart_tabs_r_text_domain ),
				'view_item'           => __( 'View Tabs', wpshopmart_tabs_r_text_domain ),
				'add_new_item'        => __( 'Add New Tabs', wpshopmart_tabs_r_text_domain ),
				'add_new'             => __( 'Add New Tabs', wpshopmart_tabs_r_text_domain ),
				'edit_item'           => __( 'Edit Tabs', wpshopmart_tabs_r_text_domain ),
				'update_item'         => __( 'Update Tabs', wpshopmart_tabs_r_text_domain ),
				'search_items'        => __( 'Search Tabs', wpshopmart_tabs_r_text_domain ),
				'not_found'           => __( 'No Tabs Found', wpshopmart_tabs_r_text_domain ),
				'not_found_in_trash'  => __( 'No Tabs found in Trash', wpshopmart_tabs_r_text_domain ),
			);
			$args = array(
				'label'               => __( 'Tabs Responsive', wpshopmart_tabs_r_text_domain ),
				'description'         => __( 'Tabs Responsive', wpshopmart_tabs_r_text_domain ),
				'labels'              => $labels,
				'supports'            => array( 'title', '', '', '', '', '', '', '', '', '', '', ),
				//'taxonomies'          => array( 'category', 'post_tag' ),
				 'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => false,
				'menu_position'       => 5,
				'menu_icon'           => wpshopmart_tabs_r_directory_url.'assets/images/tabs_48.png',
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => false,
				'publicly_queryable'  => false,
				'capability_type'     => 'page',
			);
			register_post_type( 'tabs_responsive', $args );
			
 ?>