<?php 
	$post_type = "tabs_responsive";
	
    $AllTabs = array(  'p' => $WPSM_Tabs_ID, 'post_type' => $post_type, 'orderby' => 'ASC');
    $loop = new WP_Query( $AllTabs );
	
	
	
	while ( $loop->have_posts() ) : $loop->the_post();
		//get the post id
		$post_id = get_the_ID();
		
		$Tabs_Settings = unserialize(get_post_meta( $post_id, 'Tabs_R_Settings', true));
		if(count($Tabs_Settings)) 
		{
			$option_names = array(
				"tabs_sec_title" 	 => "yes",
				"show_tabs_title_icon" => "1",
				"show_tabs_icon_align" => "left",
				"enable_tabs_border"   => "yes",
				"tabs_title_bg_clr"   => "#e8e8e8",
				"tabs_title_icon_clr" => "#000000",
				"select_tabs_title_bg_clr"   => "#e8e8e8",
				"select_tabs_title_icon_clr" => "#000000",
				"tabs_desc_bg_clr"    => "#ffffff",
				"tabs_desc_font_clr"  => "#000000",
				"title_size"         => "14",
				"des_size"     		 => "16",
				"font_family"     	 => "Open Sans",
				"tabs_styles"      =>1,
				"custom_css"      =>"",
				"tabs_animation"      =>"fadeIn",
				"tabs_alignment"      =>"horizontal",
				"tabs_position"      =>"left",
				"tabs_margin"      =>"no",
				"tabs_content_margin" =>"no",
				"tabs_display_on_mob"      =>"1",
				"tabs_display_mode_mob"      =>"2",
				);
				
				foreach($option_names as $option_name => $default_value) {
					if(isset($Tabs_Settings[$option_name])) 
						${"" . $option_name}  = $Tabs_Settings[$option_name];
					else
						${"" . $option_name}  = $default_value;
				}
		}
			
		
		 $tab_border_color = ColorDarken($tabs_title_bg_clr,19);
		 $selected_tab_border_color = ColorDarken($select_tabs_title_bg_clr,25);
		 $tab_content_border_color = ColorDarken($tabs_desc_bg_clr,25);

		$tabs_data = unserialize(get_post_meta( $post_id, 'wpsm_tabs_r_data', true));
		$TotalCount =  get_post_meta( $post_id, 'wpsm_tabs_r_count', true );
		$i=1;
		$j=1;
		if($TotalCount>0) 
		{
		?>
		<?php  if($tabs_sec_title == 'yes' ) { ?>
					<h3 style="margin-bottom:20px ;display:block;width:100%;margin-top:10px"><?php echo get_the_title( $post_id ); ?> </h3>
				<?php } ?>
				<style>
				
					<?php 
					require('style.php');
					
					echo $custom_css; ?>
				</style>
				<div id="tab_container_<?php echo $post_id; ?>" >
	 
					<ul class="wpsm_nav wpsm_nav-tabs" role="tablist" id="myTab_<?php echo $post_id; ?>">
						<?php
						foreach($tabs_data as $tabs_single_data)
						{
							$tabs_title         = $tabs_single_data['tabs_title'];
							$tabs_desc          = $tabs_single_data['tabs_desc'];
							$tabs_title_icon    = $tabs_single_data['tabs_title_icon'];
							$enable_single_icon = $tabs_single_data['enable_single_icon'];
						?>	
							<li role="presentation" <?php if($i==1){ ?> class="active" <?php } ?> >
								<a href="#tabs_desc_<?php echo $post_id; ?>_<?php echo $i; ?>" aria-controls="tabs_desc_<?php echo $post_id; ?>_<?php echo $i; ?>" role="tab" data-toggle="tab">
									
											<?php if($show_tabs_icon_align=="left"){ ?>
												<?php if($show_tabs_title_icon=="1" || $show_tabs_title_icon=="3") { ?>
													<?php if($enable_single_icon=="yes") { ?>	<i class="fa <?php echo $tabs_title_icon; ?>"></i> <?php }?>
												<?php } 
											} ?>
											
											<?php if($show_tabs_title_icon=="1" || $show_tabs_title_icon=="2") { ?>
											
											<span><?php echo $tabs_title; ?></span>
											
											<?php } ?>
											
											<?php if($show_tabs_icon_align=="right"){ ?>
												<?php if($show_tabs_title_icon=="1" || $show_tabs_title_icon=="3") { ?>
													<?php if($enable_single_icon=="yes") { ?>	<i class="fa <?php echo $tabs_title_icon; ?>"></i> <?php }?>
												<?php } 
											} ?>
									
										
									
								</a>
							</li>
						<?php $i++; } ?>
					 </ul>

					  <!-- Tab panes -->
					  <div class="tab-content" id="tab-content_<?php echo $post_id; ?>">
						<?php  foreach($tabs_data as $tabs_single_data)
						{
							$tabs_title         = $tabs_single_data['tabs_title'];
							$tabs_desc          = $tabs_single_data['tabs_desc'];
							$tabs_title_icon    = $tabs_single_data['tabs_title_icon'];
							$enable_single_icon = $tabs_single_data['enable_single_icon'];
						?>
						 <div role="tabpanel" class="tab-pane <?php if($j==1){ ?> in active <?php } ?>" id="tabs_desc_<?php echo $post_id; ?>_<?php echo $j; ?>">
								<?php  echo do_shortcode($tabs_desc); ?>
						 </div>
						<?php $j++; } ?>	
					 </div>
					 
				 </div>
 <script>
		jQuery(function () {
			jQuery('#myTab_<?php echo $post_id; ?> a:first').tab('show')
		});
		
		<?php if($tabs_animation!="None") { ?>
		jQuery(function(){
			var b="<?php echo $tabs_animation ?>";
			var c;
			var a;
			d(jQuery("#myTab_<?php echo $post_id; ?> a"),jQuery("#tab-content_<?php echo $post_id; ?>"));function d(e,f,g){
				e.click(function(i){
					i.preventDefault();
					jQuery(this).tab("show");
					var h=jQuery(this).data("easein");
					if(c){c.removeClass(a);}
					if(h){f.find("div.active").addClass("animated "+h);a=h;}
					else{if(g){f.find("div.active").addClass("animated "+g);a=g;}else{f.find("div.active").addClass("animated "+b);a=b;}}c=f.find("div.active");
				});
			}
		});
		<?php } ?>
	</script>
				
			<?php
		}
		else{
			echo "<h3> No tabs Found </h3>";
		}
	endwhile; ?>