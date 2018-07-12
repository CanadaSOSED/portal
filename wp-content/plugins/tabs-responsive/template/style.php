#tab_container_<?php echo $post_id; ?> {
	overflow:hidden;
	display:block;
	width:100%;
	border:0px solid #ddd;
	margin-bottom:30px;
	}

#tab_container_<?php echo $post_id; ?> .tab-content{
	padding:20px;
	border: 1px solid <?php echo $tab_content_border_color; ?> !important;
	margin-top: 0px;
	background-color:<?php echo $tabs_desc_bg_clr; ?> !important;
	color: <?php echo $tabs_desc_font_clr; ?> !important;
	font-size:<?php echo $des_size; ?>px !important;
	font-family: <?php echo $font_family; ?> !important;
	
	<?php if($enable_tabs_border=="yes"){ ?>
	border: 1px solid <?php echo $tab_content_border_color; ?> !important;
	<?php 
	} else { ?>
		border: 0px solid <?php echo $tab_content_border_color; ?> !important;
		
	<?php  } ?>
}
#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs {
    border-bottom: 0px solid #ddd;
}
#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li.active > a, #tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li.active > a:hover, #tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li.active > a:focus {
	color: <?php echo $select_tabs_title_icon_clr; ?> !important;
	cursor: default;
	background-color: <?php echo $select_tabs_title_bg_clr; ?> !important;
	border: 1px solid <?php echo $selected_tab_border_color; ?> !important;
}

#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li > a {
    margin-right: 0px !important; 
    line-height: 1.42857143 !important;
    border: 1px solid <?php echo $tab_border_color;  ?> !important;
    border-radius: 0px 0px 0 0 !important; 
	background-color: <?php echo $tabs_title_bg_clr; ?> !important;
	color: <?php echo $tabs_title_icon_clr; ?> !important;
	padding: 15px 18px 15px 18px !important;
	text-decoration: none !important;
	font-size: <?php echo $title_size; ?>px !important;
	text-align:center !important;
	font-family: <?php echo $font_family; ?> !important;
}
#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li > a:focus {
outline: 0px !important;
}

#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li > a:before {
	display:none !important;
}
#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li > a:after {
	display:none !important ;
}
#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li{
padding:0px !important ;
margin:0px;
}

#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li > a:hover , #tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li > a:focus {
    color: <?php echo $tabs_title_icon_clr; ?> !important;
    background-color: <?php echo $tabs_title_bg_clr; ?> !important;
	border: 1px solid <?php echo $tab_border_color; ?> !important;
	
}
#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li > a .fa{

margin-right:5px !important;

margin-left:5px !important;


}

<?php 
 switch($tabs_styles){
		case "1":
		?>
		#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs a{
			background-image: url('');
			background-position: 0 0;
			background-repeat: repeat-x;
		}
		<?php
		break;
		case "2":
		 ?>
		#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs a{
			background-image: url(<?php echo wpshopmart_tabs_r_directory_url.'assets/images/style-soft.png'; ?>);
			background-position: 0 0;
			background-repeat: repeat-x;
		}
		<?php
		break;
		case "3":
		?>
		#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs a{
			background-image: url(<?php echo wpshopmart_tabs_r_directory_url.'assets/images/style-noise.png'; ?>);
			background-position: 0 0;
			background-repeat: repeat-x;
			}
		<?php
		break;
	}
	?>	


#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li {
    float: <?php echo $tabs_position; ?>;
    margin-bottom: -1px !important;
	margin-right:0px !important; 
}


#tab_container_<?php echo $post_id; ?> .tab-content{
overflow:hidden !important;
}

<?php if($tabs_alignment=="vertical") { ?>

@media (min-width: 769px) {

	#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li{
	float:none !important;
	<?php if($tabs_position=="left"){ ?>
	margin-right:-1px !important;
	<?php } ?>
	<?php if($tabs_position=="right"){ ?>
	margin-left:-1px !important;
	<?php } ?>
	}
	#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs{
	float:<?php echo $tabs_position; ?> !important;
	margin:0px !important;
	}
}

#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li {
    <?php if($tabs_margin=="yes"){ ?>
	margin-bottom: 8px !important;
	<?php } ?>
	
}
#tab_container_<?php echo $post_id; ?> .wpsm_nav{
	<?php if($tabs_content_margin=="yes"){?>
		<?php if($tabs_position=="left"){ ?>
			margin-right: 8px !important;
		<?php } ?>	
		<?php if($tabs_position=="right"){ ?>
			margin-left: 8px !important;
		<?php } ?>	
	<?php } ?>
}

<?php } else { ?>

@media (min-width: 769px) {

	#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li{
		float:<?php echo $tabs_position; ?> !important ;
		<?php if($tabs_position=="left"){ ?>
		margin-right:-1px !important;
		<?php } ?>
		<?php if($tabs_position=="right"){ ?>
		margin-left:-1px !important;
		<?php } ?>
	}
	#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs{
		float:none !important;
		margin:0px !important;
	}

	#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li {
		<?php if($tabs_margin=="yes"){ ?>
			<?php if($tabs_position=="left"){ ?>
				margin-right: 8px !important;
			<?php } ?>	
			<?php if($tabs_position=="right"){ ?>
				margin-left: 8px !important;
			<?php } ?>	
		<?php } ?>
		
	}
	#tab_container_<?php echo $post_id; ?> .wpsm_nav{
		<?php if($tabs_content_margin=="yes"){?>
		margin-bottom: 8px !important;
		<?php } ?>
	}

}


<?php } ?>

@media (max-width: 768px) {
	#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li {
		<?php if($tabs_margin=="yes"){ ?>
		margin-bottom: 8px !important;
		margin-right:0px !important;
		margin-left:0px !important;
		<?php } ?>
		
	}
	#tab_container_<?php echo $post_id; ?> .wpsm_nav{
		<?php if($tabs_content_margin=="yes"){?>
		margin-bottom: 8px !important;
		margin-right:0px !important;
		margin-left:0px !important;
		<?php } ?>
	}
}


	.wpsm_nav-tabs li:before{
		display:none !important;
	}

	@media (max-width: 768px) {
		<?php if($tabs_display_on_mob=="2"){ ?>
			
			#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs  li  a  span{
				display: none !important;
			}
			
		<?php } ?>
		
		<?php if($tabs_display_on_mob=="3"){ ?>
			
			#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs  li  a  i{
				display: none !important;
			}
			
		<?php } ?>
		.wpsm_nav-tabs{
			margin-left:0px !important;
			margin-right:0px !important; 
			
		}
		<?php if($tabs_display_mode_mob == "2") { ?>
		#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li{
			float:none !important;
		}
		<?php } else { ?>
			
			#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li {
			<?php if($tabs_margin=="yes"){ ?>
				<?php if($tabs_position=="left"){ ?>
					margin-right: 8px !important;
				<?php } ?>	
				<?php if($tabs_position=="right"){ ?>
					margin-left: 8px !important;
				<?php } ?>	
			<?php } ?>
			
			}
			
			<?php if($tabs_alignment=="vertical") { ?>
				#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs > li{
					float:none !important;
				}
				#tab_container_<?php echo $post_id; ?> .wpsm_nav-tabs{
				float:<?php echo $tabs_position; ?> !important;
				margin:0px !important;
				}
			<?php } ?>
			
		<?php } ?>
	
	}
