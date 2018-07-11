<?php
if(isset($PostID) && isset($_POST['tabs_setting_save_action'])) {
			$tabs_sec_title 	          = sanitize_option('tabs_sec_title', $_POST['tabs_sec_title']);
			$show_tabs_title_icon 		  = sanitize_option('show_tabs_title_icon', $_POST['show_tabs_title_icon']);
			$show_tabs_icon_align 	      = sanitize_option('show_tabs_icon_align', $_POST['show_tabs_icon_align']);
			$enable_tabs_border   		  = sanitize_option('enable_tabs_border',$_POST['enable_tabs_border']);
			$tabs_alignment         	  = sanitize_option('tabs_alignment',$_POST['tabs_alignment']);
			$tabs_position         		  = sanitize_option('tabs_position',$_POST['tabs_position']);
			$tabs_margin          		  = sanitize_option('tabs_margin',$_POST['tabs_margin']);
			$tabs_content_margin          = sanitize_option('tabs_content_margin',$_POST['tabs_content_margin']);
			$tabs_styles          		  = sanitize_option('tabs_styles',$_POST['tabs_styles']);
			$tabs_title_bg_clr            = sanitize_text_field($_POST['tabs_title_bg_clr']);
			$tabs_title_icon_clr          = sanitize_text_field($_POST['tabs_title_icon_clr']);
			$select_tabs_title_bg_clr  	  = sanitize_text_field($_POST['select_tabs_title_bg_clr']);
			$select_tabs_title_icon_clr   = sanitize_text_field($_POST['select_tabs_title_icon_clr']);
			$tabs_desc_bg_clr 		      = sanitize_text_field($_POST['tabs_desc_bg_clr']);
			$tabs_desc_font_clr           = sanitize_text_field($_POST['tabs_desc_font_clr']);
			$title_size                   = sanitize_text_field($_POST['title_size']);
			$des_size                     = sanitize_text_field($_POST['des_size']);
			$font_family                  = sanitize_text_field($_POST['font_family']);
			$tabs_animation               = sanitize_text_field($_POST['tabs_animation']);
			$custom_css                   = stripslashes($_POST['custom_css']);
			$tabs_display_on_mob 	          = sanitize_option('tabs_display_on_mob', $_POST['tabs_display_on_mob']);
			$tabs_display_mode_mob 	          = sanitize_option('tabs_display_mode_mob', $_POST['tabs_display_mode_mob']);
			
			
			$Settings_Array = serialize( array(
				'tabs_sec_title' 		       => $tabs_sec_title,
				'show_tabs_title_icon' 		   => $show_tabs_title_icon,
				'show_tabs_icon_align' 		   => $show_tabs_icon_align,
				'enable_tabs_border' 		   => $enable_tabs_border,
				'tabs_alignment' 	           => $tabs_alignment,
				'tabs_position' 		       => $tabs_position,
				'tabs_margin' 		           => $tabs_margin,
				'tabs_content_margin' 		   => $tabs_content_margin,
				'tabs_styles' 		           => $tabs_styles,
				'tabs_title_bg_clr' 		   => $tabs_title_bg_clr,
				'tabs_title_icon_clr' 		   => $tabs_title_icon_clr,
				'select_tabs_title_bg_clr' 	   => $select_tabs_title_bg_clr,
				'select_tabs_title_icon_clr'   => $select_tabs_title_icon_clr,
				'tabs_desc_bg_clr' 		       => $tabs_desc_bg_clr,
				'tabs_desc_font_clr' 	       => $tabs_desc_font_clr,
				'title_size' 			       => $title_size,
				'des_size' 				       => $des_size,
				'font_family' 			       => $font_family,
				'tabs_animation' 			   => $tabs_animation,
				'custom_css' 			       => $custom_css,
				'tabs_display_on_mob' 		   => $tabs_display_on_mob,
				'tabs_display_mode_mob' 		   => $tabs_display_mode_mob,
				) );

			update_post_meta($PostID, 'Tabs_R_Settings', $Settings_Array);
		}
?>
