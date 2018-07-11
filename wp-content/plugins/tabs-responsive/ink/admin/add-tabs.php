<div style=" overflow: hidden;padding: 10px;">
	<style>
	.html_editor_button{
		border-radius:0px;
		background-color: #9C9C9C;
		border-color: #9C9C9C;
		margin-bottom:20px;
	}
	</style>
	<h1>Tabs Design Template </h1>
		<div style="overflow:hidden;display:block;width:100%;padding-top:20px">
			
			<div class="col-md-3">
				<div class="demoftr">	
					
					<div class="">
						<div class="wpsm_home_portfolio_showcase">
							<div class="wpsm_ribbon"><a target="_blank" href="https://wpshopmart.com/plugins/tabs-pro-plugin/"><span> Selected </span></a></div>
							<img class="wpsm_img_responsive ftr_img" src="<?php echo wpshopmart_tabs_r_directory_url.'assets/images/accordion-1.png'?>">
							</div>
					</div>
					<div style="padding:13px;overflow:hidden; background: #EFEFEF; border-top: 1px dashed #ccc;">
						<h3 class="text-center pull-left" style="margin-top: 10px;margin-bottom: 10px;font-weight:900">Selected Design</h3>
						<a type="button"  class="pull-right btn btn-danger design_btn" id="templates_btn1" target="_blank" href="http://demo.wpshopmart.com/tabs-pro-plugin-demo-for-wordpress/" >Check Demo</a>
							</div>		
				</div>	
			</div>
			
			<div class="col-md-3">
				<div class="demoftr">	
					
					<div class="">
						<div class="wpsm_home_portfolio_showcase">
							<div class="wpsm_ribbon wpsm_ribbon2"><a target="_blank" href="https://wpshopmart.com/plugins/tabs-pro-plugin/"><span>Buy Now</span></a></div>
							<img class="wpsm_img_responsive ftr_img" src="<?php echo wpshopmart_tabs_r_directory_url.'assets/images/accordion-2.png'?>">
							</div>
					</div>
					<div style="padding:13px;overflow:hidden; background: #EFEFEF; border-top: 1px dashed #ccc;">
						<h3 class="text-center pull-left" style="margin-top: 10px;margin-bottom: 10px;font-weight:900">Pro Templates </h3>
						<a type="button"  class="pull-right btn btn-danger design_btn" id="templates_btn2" target="_blank" href="http://demo.wpshopmart.com/tabs-pro-plugin-demo-for-wordpress/" >Check Demo</a>
							</div>		
				</div>	
			</div>
			
			</div>
		
	
	
	<h3><?php _e('Add Tabs',wpshopmart_tabs_r_text_domain); ?></h3>
	<input type="hidden" name="tabs_r_save_data_action" value="tabs_r_save_data_action" />
	<ul class="clearfix" id="colorbox_panel">
	<?php
	 
   
			$i=1;
			$All_data = unserialize(get_post_meta( $post->ID, 'wpsm_tabs_r_data', true));
			 $TotalCount =  get_post_meta( $post->ID, 'wpsm_tabs_r_count', true );
			if($TotalCount) 
			{
				if($TotalCount!=-1)
				{
					foreach($All_data as $single_data)
					{
						 $tabs_title = $single_data['tabs_title'];
						 $tabs_desc = $single_data['tabs_desc'];
						 $tabs_title_icon = $single_data['tabs_title_icon'];
						 $enable_single_icon = $single_data['enable_single_icon'];
						
						?>
						<li class="wpsm_ac-panel single_color_box" >
							<span class="ac_label"><?php _e('Tab Title',wpshopmart_tabs_r_text_domain); ?></span>
							<input type="text" id="tabs_title[]" name="tabs_title[]" value="<?php echo esc_attr($tabs_title); ?>" placeholder="Enter Tab Title Here" class="wpsm_ac_label_text">
							<span class="ac_label"><?php _e('Tab Description',wpshopmart_tabs_r_text_domain); ?></span>
							<textarea  id="tabs_desc[]" name="tabs_desc[]"  placeholder="Enter Tab Description Here" class="wpsm_ac_label_text"><?php echo esc_html($tabs_desc); ?></textarea>
							<a type="button" class="btn btn-primary btn-block html_editor_button" data-remodal-target="modal" href="#" id="<?php echo $i; ?>"  onclick="open_editor(<?php echo $i; ?>)">Use WYSIWYG Editor </a>
							
							<span class="ac_label"><?php _e('Tab Icon',wpshopmart_tabs_r_text_domain); ?></span>
							<div class="form-group input-group">
								<input data-placement="bottomRight" id="tabs_title_icon[]" name="tabs_title_icon[]" class="form-control icp icp-auto" value="<?php echo  $tabs_title_icon; ?>" type="text" readonly="readonly" />
								<span class="input-group-addon "></span>
							</div>
							<span class="ac_label"><?php _e('Display Above Icon',wpshopmart_tabs_r_text_domain); ?></span>
							<select name="enable_single_icon[]" style="width:100%" >
								<option value="yes" <?php if($enable_single_icon == 'yes') echo "selected=selected"; ?>>Yes</option>
								<option value="no" <?php if($enable_single_icon == 'no') echo "selected=selected"; ?>>No</option>
								
							</select>
							
							<a class="remove_button" href="#delete" id="remove_bt" ><i class="fa fa-trash-o"></i></a>
							
						</li>
						<?php 
						$i++;
					} // end of foreach
				}else{
				echo "<h2>No Tabs Found</h2>";
				}
			}
			else 
			{
				  for($i=1; $i<=3; $i++)
				  {
					  ?>
					 <li class="wpsm_ac-panel single_color_box" >
							<span class="ac_label"><?php _e('Tab Title',wpshopmart_tabs_r_text_domain); ?></span>
							<input type="text" id="tabs_title[]" name="tabs_title[]" value="Sample Title" placeholder="Enter Tab Title Here" class="wpsm_ac_label_text">
							<span class="ac_label"><?php _e('Tab Description',wpshopmart_tabs_r_text_domain); ?></span>
							<textarea  id="tabs_desc[]" name="tabs_desc[]"  placeholder="Enter Tab Description Here" class="wpsm_ac_label_text">Sample Description</textarea>
							<a type="button" class="btn btn-primary btn-block html_editor_button" data-remodal-target="modal" href="#" id="<?php echo $i; ?>"  onclick="open_editor(<?php echo $i; ?>)">Use WYSIWYG Editor </a>
							
							<span class="ac_label"><?php _e('Tab Icon',wpshopmart_tabs_r_text_domain); ?></span>
							<div class="form-group input-group">
								<input data-placement="bottomRight" id="tabs_title_icon[]" name="tabs_title_icon[]" class="form-control icp icp-auto" value="fa-laptop" type="text" readonly="readonly" />
								<span class="input-group-addon "></span>
							</div>
							<span class="ac_label"><?php _e('Display Above Icon',wpshopmart_tabs_r_text_domain); ?></span>
							<select name="enable_single_icon[]" style="width:100%" >
								<option value="yes" selected>Yes</option>
								<option value="no" >No</option>
								
							</select>
							
							<a class="remove_button" href="#delete" id="remove_bt" ><i class="fa fa-trash-o"></i></a>
							
						</li>
					 <?php
				}
			}
			?>
	</ul>
	<!-- Modal -->
	<!-- Modal -->
	<div class="remodal" data-remodal-options=" closeOnOutsideClick: false" data-remodal-id="modal" role="dialog" aria-labelledby="modal1Title" aria-describedby="modal1Desc">
	  <button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
	  <div>
		<h2 id="modal1Title">Accordion Editor</h2>
		<p id="modal1Desc">
		  <?php
			$content = '';
			$editor_id = 'get_text';
			wp_editor( $content, $editor_id );
		?>
		<input type="hidden" value="" id="get_id" />
		</p>
	  </div>
	  <br>
	  <button data-remodal-action="cancel" class="remodal-cancel">Cancel</button>
	  <button data-remodal-action="confirm" class="remodal-confirm" onclick="insert_html()">Insert Code</button>
	</div>

	<div style="display:block;margin-top:20px;overflow:hidden;width: 100%;float:left;">
		<a class="wpsm_ac-panel add_wpsm_ac_new" id="add_new_ac" onclick="add_new_content()"   >
			<?php _e('Add New Tabs', wpshopmart_tabs_r_text_domain); ?>
		</a>
		<a  style="float: left;padding:10px !important;background:#31a3dd;" class=" add_wpsm_ac_new delete_all_acc" id="delete_all_colorbox"    >
			<i style="font-size:57px;"class="fa fa-trash-o"></i>
			<span style="display:block"><?php _e('Delete All',wpshopmart_tabs_r_text_domain); ?></span>
		</a>
	</div>
	
</div>
<h1>Get Support Help Here</h1>
<h3>If You have any issue then please ask us any time</h3>
<a href="https://wordpress.org/support/plugin/tabs-responsive" target="_blank" class="button button-primary button-hero ">Get Support</a>
<br> <br>
<?php require('add-tabs-js-footer.php'); ?>