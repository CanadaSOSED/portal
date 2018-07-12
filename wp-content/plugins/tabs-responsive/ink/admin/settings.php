<?php 
  $De_Settings = unserialize(get_option('Tabs_R_default_Settings'));
  $PostId = $post->ID;
  $Settings = unserialize(get_post_meta( $PostId, 'Tabs_R_Settings', true));

	$option_names = array(
		"tabs_sec_title" 	 => $De_Settings['tabs_sec_title'],
		"show_tabs_title_icon" => $De_Settings['show_tabs_title_icon'],
		"show_tabs_icon_align" => $De_Settings['show_tabs_icon_align'],
        "enable_tabs_border"   => $De_Settings['enable_tabs_border'],
        "tabs_title_bg_clr"   => $De_Settings['tabs_title_bg_clr'],
		"tabs_title_icon_clr" => $De_Settings['tabs_title_icon_clr'],
		"select_tabs_title_bg_clr"   => $De_Settings['select_tabs_title_bg_clr'],
		"select_tabs_title_icon_clr" => $De_Settings['select_tabs_title_icon_clr'],
		"tabs_desc_bg_clr"    => $De_Settings['tabs_desc_bg_clr'],
        "tabs_desc_font_clr"  => $De_Settings['tabs_desc_font_clr'],
        "title_size"         => $De_Settings['title_size'],
        "des_size"     		 => $De_Settings['des_size'],
        "font_family"     	 => $De_Settings['font_family'],
        "tabs_styles"      =>$De_Settings['tabs_styles'],
		"custom_css"      =>$De_Settings['custom_css'],
		"tabs_animation"      =>$De_Settings['tabs_animation'],
		"tabs_alignment"      =>$De_Settings['tabs_alignment'],
		"tabs_position"      =>$De_Settings['tabs_position'],
		"tabs_margin"      =>$De_Settings['tabs_margin'],
		"tabs_content_margin"   =>$De_Settings['tabs_content_margin'],
		"tabs_display_on_mob"      =>"1",
		"tabs_display_mode_mob"      =>"2",
		
		);
		
		foreach($option_names as $option_name => $default_value) {
			if(isset($Settings[$option_name])) 
				${"" . $option_name}  = $Settings[$option_name];
			else
				${"" . $option_name}  = $default_value;
		}
	
		
?>

<Script>

 //font slider size script
  jQuery(function() {
    jQuery( "#title_size_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 22,
		min:8,
		slide: function( event, ui ) {
		jQuery( "#title_size" ).val( ui.value );
      }
		});
		
		jQuery( "#title_size_id" ).slider("value",<?php echo $title_size; ?> );
		jQuery( "#title_size" ).val( jQuery( "#title_size_id" ).slider( "value") );
    
  });
</script>
<Script>

 //font slider size script
  jQuery(function() {
    jQuery( "#des_size_id" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 30,
		min:5,
		slide: function( event, ui ) {
		jQuery( "#des_size" ).val( ui.value );
      }
		});
		
		jQuery( "#des_size_id" ).slider("value",<?php echo $des_size; ?>);
		jQuery( "#des_size" ).val( jQuery( "#des_size_id" ).slider( "value") );
    
  });
</script>  
<Script>
function wpsm_update_default(){
	 jQuery.ajax({
		url: location.href,
		type: "POST",
		data : {
			    'action123':'default_settins_action',
			     },
                success : function(data){
									alert("Default Settings Updated");
									location.reload(true);
                                   }	
	});
	
}
</script>
<?php

if(isset($_POST['action123']) == "default_settins_action")
	{
	
		$Settings_Array2 = serialize( array(
				"tabs_sec_title" 	 => $tabs_sec_title,
				"show_tabs_title_icon" => $show_tabs_title_icon,
				"show_tabs_icon_align" => $show_tabs_icon_align,
				"enable_tabs_border"   => $enable_tabs_border,
				"tabs_title_bg_clr"   => $tabs_title_bg_clr,
				"tabs_title_icon_clr" => $tabs_title_icon_clr,
				"select_tabs_title_bg_clr"   => $select_tabs_title_bg_clr,
				"select_tabs_title_icon_clr" => $select_tabs_title_icon_clr,
				"tabs_desc_bg_clr"    => $tabs_desc_bg_clr,
				"tabs_desc_font_clr"  => $tabs_desc_font_clr,
				"title_size"         => $title_size,
				"des_size"     		 => $des_size,
				"font_family"     	 => $font_family,
				"tabs_styles"      =>$tabs_styles,
				"custom_css"      =>$custom_css,
				"tabs_animation"      =>$tabs_animation,
				"tabs_alignment"      =>$tabs_alignment,
				"tabs_position"      =>$tabs_position,
				"tabs_margin"      =>$tabs_margin,
				"tabs_content_margin"      =>$tabs_content_margin,
				) );

			update_option('Tabs_R_default_Settings', $Settings_Array2);
}

 ?>
<input type="hidden" id="tabs_setting_save_action" name="tabs_setting_save_action" value="tabs_setting_save_action">
	
<table class="form-table acc_table">
	<tbody>
		
		<tr>
			<th scope="row"><label><?php _e('Display Tabs Section Title ',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<div class="switch">
					<input type="radio" class="switch-input" name="tabs_sec_title" value="yes" id="enable_tabs_sec_title" <?php if($tabs_sec_title == 'yes' ) { echo "checked"; } ?>   >
					<label for="enable_tabs_sec_title" class="switch-label switch-label-off"><?php _e('Yes',wpshopmart_tabs_r_text_domain); ?></label>
					<input type="radio" class="switch-input" name="tabs_sec_title" value="no" id="disable_tabs_sec_title"  <?php if($tabs_sec_title == 'no' ) { echo "checked"; } ?> >
					<label for="disable_tabs_sec_title" class="switch-label switch-label-on"><?php _e('No',wpshopmart_tabs_r_text_domain); ?></label>
					<span class="switch-selection"></span>
				</div>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tab_r_sec_title_tp">help</a>
				<div id="tab_r_sec_title_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Display Tabs Section Title ',wpshopmart_tabs_r_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/sec-title.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><label><?php _e('Display Option For Title and icon ',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="show_tabs_title_icon" id="show_tabs_title_icon" value="1" <?php if($show_tabs_title_icon == '1' ) { echo "checked"; } ?> /> Show Tabs Title + Icon (both) </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="show_tabs_title_icon" id="show_tabs_title_icon" value="2" <?php if($show_tabs_title_icon == '2' ) { echo "checked"; } ?> /> Show Only Tabs Title </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="show_tabs_title_icon" id="show_tabs_title_icon" value="3" <?php if($show_tabs_title_icon == '3' ) { echo "checked"; } ?>  /> Show Only Icon </span>
				
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_title_icon_tp">help</a>
				<div id="tabs_r_title_icon_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Display Tabs Title And Icon ',wpshopmart_tabs_r_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/tab-title.png'; ?>">
						<br>
						
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/tab-icon.png'; ?>">
						
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><label><?php _e('Tabs Icon Position Alignment',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="show_tabs_icon_align" id="show_tabs_icon_align" value="left" <?php if($show_tabs_icon_align == 'left' ) { echo "checked"; } ?> /> Before Tab Title </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="show_tabs_icon_align" id="show_tabs_icon_align" value="right" <?php if($show_tabs_icon_align == 'right' ) { echo "checked"; } ?> /> After Tab Title </span>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_title_icon_align_tp">help</a>
				<div id="tabs_r_title_icon_align_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Align your Tab Icon Position before title or after title',wpshopmart_tabs_r_text_domain); ?></h2>
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><label><?php _e('Display Tabs Border',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<div class="switch">
					<input type="radio" class="switch-input" name="enable_tabs_border" value="yes" id="enable_tabs_border" <?php if($enable_tabs_border == 'yes' ) { echo "checked"; } ?>   >
					<label for="enable_tabs_border" class="switch-label switch-label-off"><?php _e('Yes',wpshopmart_tabs_r_text_domain); ?></label>
					<input type="radio" class="switch-input" name="enable_tabs_border" value="no" id="disable_tabs_border"  <?php if($enable_tabs_border == 'no' ) { echo "checked"; } ?> >
					<label for="disable_tabs_border" class="switch-label switch-label-on"><?php _e('No',wpshopmart_tabs_r_text_domain); ?></label>
					<span class="switch-selection"></span>
				</div>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#enable_ac_border_tp" data-tooltip="#enable_tabs_r_border_tp">help</a>
				<div id="enable_tabs_r_border_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Display Or Hide Tabs Border Here',wpshopmart_tabs_r_text_domain); ?></h2>
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><label><?php _e('Tabs Styles',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_styles" id="tabs_styles" value="1" <?php if($tabs_styles == '1' ) { echo "checked"; } ?> /> Default </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_styles" id="tabs_styles" value="2" <?php if($tabs_styles == '2' ) { echo "checked"; } ?>  /> Soft </span>
				<span style="display:block"><input type="radio" name="tabs_styles" id="tabs_styles" value="3"  <?php if($tabs_styles == '3' ) { echo "checked"; } ?> /> Noise </span>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_styles_tp">help</a>
				<div id="tabs_r_styles_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Tab Styles',wpshopmart_tabs_r_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/tab-title.png'; ?>">
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/soft.png'; ?>">
						<br>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/noise.png'; ?>">
					</div>
		    	</div>
				<div style="margin-top:10px;display:block;overflow:hidden;width:100%;"> <a style="margin-top:10px" href="http://wpshopmart.com/plugins/tabs-pro-plugin/" target="_balnk">Unlock 2 More Overlays Styles In Premium Version</a> </div>
			
			</td>
		</tr>
		
		<tr >
			<th scope="row"><label><?php _e('Tabs Title Background Colour',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<input id="tabs_title_bg_clr" name="tabs_title_bg_clr" type="text" value="<?php echo $tabs_title_bg_clr; ?>" class="my-color-field" data-default-color="#e8e8e8" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_title_bg_clr_tp">help</a>
				<div id="tabs_r_title_bg_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Tabs Title Background Colour',wpshopmart_tabs_r_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/tabs-bg.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr >
			<th scope="row"><label><?php _e('Tabs Title/Icon Font Colour',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<input id="tabs_title_icon_clr" name="tabs_title_icon_clr" type="text" value="<?php echo $tabs_title_icon_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_title_icon_clr_tp">help</a>
				<div id="tabs_r_title_icon_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Tabs Title/Icon Font Colour',wpshopmart_tabs_r_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/tabs-ft-color.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		
		
		<tr >
			<th scope="row"><label><?php _e('Selected Tabs Title Background Colour',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<input id="select_tabs_title_bg_clr" name="select_tabs_title_bg_clr" type="text" value="<?php echo $select_tabs_title_bg_clr; ?>" class="my-color-field" data-default-color="#e8e8e8" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_sel_bg_clr_tp">help</a>
				<div id="tabs_r_sel_bg_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Selected/Open Tabs Title Background Colour',wpshopmart_tabs_r_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/sel-tab-color.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr >
			<th scope="row"><label><?php _e('Selected Tabs Title/Icon Font Colour',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<input id="select_tabs_title_icon_clr" name="select_tabs_title_icon_clr" type="text" value="<?php echo $select_tabs_title_icon_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_sel_icon_clr_tp">help</a>
				<div id="tabs_r_sel_icon_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Selected/Open Tabs Title/Icon Font Colour',wpshopmart_tabs_r_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/tabs-ft-color.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr >
			<th scope="row"><label><?php _e('Tabs Description Background Colour',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<input id="tabs_desc_bg_clr" name="tabs_desc_bg_clr" type="text" value="<?php echo $tabs_desc_bg_clr; ?>" class="my-color-field" data-default-color="#ffffff" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_desc_bg_clr_tp">help</a>
				<div id="tabs_r_desc_bg_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Tabs Description Background Colour',wpshopmart_tabs_r_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/desc-bg-color.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr >
			<th scope="row"><label><?php _e('Tabs Description Font Colour',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<input id="tabs_desc_font_clr" name="tabs_desc_font_clr" type="text" value="<?php echo $tabs_desc_font_clr; ?>" class="my-color-field" data-default-color="#000000" />
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_desc_font_clr_tp">help</a>
				<div id="tabs_r_desc_font_clr_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Tabs Description Font Colour',wpshopmart_tabs_r_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/noise.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr class="setting_color">
			<th><?php _e('Tabs Title/Icon Font Size',wpshopmart_tabs_r_text_domain); ?> </th>
			<td>
				<div id="title_size_id" class="size-slider" ></div>
				<input type="text" class="slider-text" id="title_size" name="title_size"  readonly="readonly">
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#title_size_tp">help</a>
				<div id="title_size_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;max-width: 300px;">
						<h2 style="color:#fff !important;">You can update Tabs Title and Icon Font Size from here. Just Scroll it to change size.</h2>
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr class="setting_color">
			<th><?php _e('Tabs Description Font Size',wpshopmart_tabs_r_text_domain); ?> </th>
			<td>
				<div id="des_size_id" class="size-slider" ></div>
				<input type="text" class="slider-text" id="des_size" name="des_size"  readonly="readonly">
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#des_size_tp">help</a>
				<div id="des_size_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;max-width: 300px;">
						<h2 style="color:#fff !important;">You can update Tabs Description/content Font Size from here. Just Scroll it to change size.</h2>
						
					</div>
		    	</div>
			</td>
		</tr>
		<tr >
			<th><?php _e('Font Style/Family',wpshopmart_tabs_r_text_domain); ?> </th>
			<td>
				<select name="font_family" id="font_family" class="standard-dropdown" style="width:100%" >
					<optgroup label="Default Fonts">
						<option value="Arial"           <?php if($font_family == 'Arial' ) { echo "selected"; } ?>>Arial</option>
						<option value="Arial Black"    <?php if($font_family == 'Arial Black' ) { echo "selected"; } ?>>Arial Black</option>
						<option value="Courier New"     <?php if($font_family == 'Courier New' ) { echo "selected"; } ?>>Courier New</option>
						<option value="Georgia"         <?php if($font_family == 'Georgia' ) { echo "selected"; } ?>>Georgia</option>
						<option value="Grande"          <?php if($font_family == 'Grande' ) { echo "selected"; } ?>>Grande</option>
						<option value="Helvetica" 	<?php if($font_family == 'Helvetica' ) { echo "selected"; } ?>>Helvetica Neue</option>
						<option value="Impact"         <?php if($font_family == 'Impact' ) { echo "selected"; } ?>>Impact</option>
						<option value="Lucida"         <?php if($font_family == 'Lucida' ) { echo "selected"; } ?>>Lucida</option>
						<option value="Lucida Grande"         <?php if($font_family == 'Lucida Grande' ) { echo "selected"; } ?>>Lucida Grande</option>
						<option value="Open Sans"   <?php if($font_family == 'Open Sans' ) { echo "selected"; } ?>>Open Sans</option>
						<option value="OpenSansBold"   <?php if($font_family == 'OpenSansBold' ) { echo "selected"; } ?>>OpenSansBold</option>
						<option value="Palatino Linotype"       <?php if($font_family == 'Palatino Linotype' ) { echo "selected"; } ?>>Palatino</option>
						<option value="Sans"           <?php if($font_family == 'Sans' ) { echo "selected"; } ?>>Sans</option>
						<option value="sans-serif"           <?php if($font_family == 'sans-serif' ) { echo "selected"; } ?>>Sans-Serif</option>
						<option value="Tahoma"         <?php if($font_family == 'Tahoma' ) { echo "selected"; } ?>>Tahoma</option>
						<option value="Times New Roman"          <?php if($font_family == 'Times New Roman' ) { echo "selected"; } ?>>Times New Roman</option>
						<option value="Trebuchet"      <?php if($font_family == 'Trebuchet' ) { echo "selected"; } ?>>Trebuchet</option>
						<option value="Verdana"        <?php if($font_family == 'Verdana' ) { echo "selected"; } ?>>Verdana</option>
					</optgroup>
				</select>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#font_family_tp">help</a>
				<div id="font_family_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;max-width: 300px;">
						<h2 style="color:#fff !important;">You can update Tabs Title and Description Font Family/Style from here. Select any one form these options.</h2>
					
					</div>
		    	</div>
				<div style="margin-top:10px;display:block;overflow:hidden;width:100%;"> <a style="margin-top:10px" href="http://wpshopmart.com/plugins/tabs-pro-plugin/" target="_balnk">Get 500+ Google Fonts In Premium Version</a> </div>
			
			</td>
		</tr>
		
		
		<tr >
			<th><?php _e('Tabs Description Animation',wpshopmart_tabs_r_text_domain); ?> </th>
			<td>
				<select name="tabs_animation" id="tabs_animation" class="standard-dropdown" style="width:100%" >
						<option value="fadeIn"           <?php if($tabs_animation == 'fadeIn' ) { echo "selected"; } ?>>Fade Animation</option>
						<option value="fadeInUp"     <?php if($tabs_animation == 'fadeInUp' ) { echo "selected"; } ?>>Fade In Up Animation</option>
						<option value="fadeInDown"         <?php if($tabs_animation == 'fadeInDown' ) { echo "selected"; } ?>>Fade In Down Animation</option>
						<option value="fadeInLeft"          <?php if($tabs_animation == 'fadeInLeft' ) { echo "selected"; } ?>>Fade In Left Animation</option>
						<option value="fadeInRight" 	<?php if($tabs_animation == 'fadeInRight' ) { echo "selected"; } ?>>Fade In Right Animation</option>
						<option value="None"         <?php if($tabs_animation == 'None' ) { echo "selected"; } ?>>No Animation</option>
						<option value="flip"  disabled   		<?php if($tabs_animation == 'flip' ) { echo "selected"; } ?> >flip (Available in Pro)</option>
					<option value="flipInX"  disabled  		<?php if($tabs_animation == 'flipInX' ) { echo "selected"; } ?> >flipInX (Available in Pro)</option>
					<option value="flipInY"   disabled 		<?php if($tabs_animation == 'flipInY' ) { echo "selected"; } ?> >flipInY (Available in Pro)option>
					<option value="flipOutX"  disabled  	<?php if($tabs_animation == 'flipOutX' ) { echo "selected"; } ?> >flipOutX (Available in Pro)</option>
					<option value="flipOutY"   disabled 	<?php if($tabs_animation == 'flipOutY' ) { echo "selected"; } ?> >flipOutY (Available in Pro)</option>
					<option value="zoomIn"    disabled		<?php if($tabs_animation == 'zoomIn' ) { echo "selected"; } ?> >ZoomIn (Available in Pro)</option>
					<option value="zoomInLeft"  disabled  	<?php if($tabs_animation == 'zoomInLeft' ) { echo "selected"; } ?> >ZoomInLeft (Available in Pro)</option>
					<option value="zoomInRight" disabled   	<?php if($tabs_animation == 'zoomInRight' ) { echo "selected"; } ?> >ZoomInRight (Available in Pro)</option>
					<option value="zoomInUp"   disabled 	<?php if($tabs_animation == 'zoomInUp' ) { echo "selected"; } ?> >ZoomInUp (Available in Pro)</option>
					<option value="zoomInDown" disabled   	<?php if($tabs_animation == 'zoomInDown' ) { echo "selected"; } ?> >ZoomInDown (Available in Pro)</option>
					<option value="bounce"   disabled 		<?php if($tabs_animation == 'bounce' ) { echo "selected"; } ?> >bounce (Available in Pro)</option>
					<option value="bounceIn"   disabled 	<?php if($tabs_animation == 'bounceIn' ) { echo "selected"; } ?> >bounceIn (Available in Pro)</option>
					<option value="bounceInLeft" disabled   <?php if($tabs_animation == 'bounceInLeft' ) { echo "selected"; } ?> >bounceInLeft (Available in Pro)</option>
					<option value="bounceInRight" disabled   <?php if($tabs_animation == 'bounceInRight' ) { echo "selected"; } ?> >bounceInRight (Available in Pro)</option>
					<option value="bounceInUp"   disabled 	<?php if($tabs_animation == 'bounceInUp' ) { echo "selected"; } ?> >bounceInUp (Available in Pro)</option>
					<option value="bounceInDown"  disabled   <?php if($tabs_animation == 'bounceInDown' ) { echo "selected"; } ?> >bounceInDown (Available in Pro)</option>
					<option value="flash"    disabled		<?php if($tabs_animation == 'flash' ) { echo "selected"; } ?> >flash (Available in Pro)</option>
					<option value="pulse"  disabled  		<?php if($tabs_animation == 'pulse' ) { echo "selected"; } ?> >pulse (Available in Pro)</option>
					<option value="shake"    disabled		<?php if($tabs_animation == 'shake' ) { echo "selected"; } ?> >shake (Available in Pro)</option>
					<option value="swing"   disabled 		<?php if($tabs_animation == 'swing' ) { echo "selected"; } ?> >swing (Available in Pro)</option>
					<option value="tada"    disabled		<?php if($tabs_animation == 'tada' ) { echo "selected"; } ?> >tada (Available in Pro)</option>
					<option value="wobble"   disabled 		<?php if($tabs_animation == 'wobble' ) { echo "selected"; } ?> >wobble (Available in Pro)</option>
					<option value="lightSpeedIn" disabled    <?php if($tabs_animation == 'lightSpeedIn' ) { echo "selected"; } ?> >lightSpeedIn (Available in Pro)</option>
					<option value="rollIn"    	disabled	<?php if($tabs_animation == 'rollIn' ) { echo "selected"; } ?> >rollIn (Available in Pro)</option>
					<option value="slideInDown"  disabled  		<?php if($tabs_animation == 'slideInDown' ) { echo "selected"; } ?> >slideInDown (Available in Pro)</option>
					<option value="slideInLeft"  disabled  		<?php if($tabs_animation == 'slideInLeft' ) { echo "selected"; } ?> >slideInLeft (Available in Pro)</option>
					<option value="slideInRight" disabled   		<?php if($tabs_animation == 'slideInRight' ) { echo "selected"; } ?> >slideInRight (Available in Pro)</option>
					<option value="slideInUp"   disabled 		<?php if($tabs_animation == 'slideInUp' ) { echo "selected"; } ?> >slideInUp (Available in Pro)</option>
					<option value="rotateIn"    disabled		<?php if($tabs_animation == 'rotateIn' ) { echo "selected"; } ?> >rotateIn (Available in Pro)</option>
					<option value="rotateInDownLeft" disabled   		<?php if($tabs_animation == 'rotateInDownLeft' ) { echo "selected"; } ?> >rotateInDownLeft (Available in Pro)</option>
					<option value="rotateInDownRight"  disabled  		<?php if($tabs_animation == 'rotateInDownRight' ) { echo "selected"; } ?> >rotateInDownRight (Available in Pro)</option>
					<option value="rotateInUpLeft"    disabled		<?php if($tabs_animation == 'rotateInUpLeft' ) { echo "selected"; } ?> >rotateInUpLeft (Available in Pro)</option>
					<option value="rotateInUpRight"   disabled 		<?php if($tabs_animation == 'rotateInUpRight' ) { echo "selected"; } ?> >rotateInUpRight (Available in Pro)</option>
				
				</select>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_animation">help</a>
				<div id="tabs_r_animation" style="display:none;">
					<div style="color:#fff !important;padding:10px;max-width: 300px;">
						<h2 style="color:#fff !important;">Animation your tabs content on click , select your animation form here</h2>
					</div>
		    	</div>
				<div style="margin-top:10px;display:block;overflow:hidden;width:100%;"> <a style="margin-top:10px" href="http://wpshopmart.com/plugins/tabs-pro-plugin/" target="_balnk">Unlock 25+ MOre Animation Effect In Premium Version</a> </div>
			
			</td>
		</tr>
		
		<tr>
			<th scope="row"><label><?php _e('Tabs Alignment ',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
					<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_alignment" id="tabs_alignment" value="horizontal" <?php if($tabs_alignment == 'horizontal' ) { echo "checked"; } ?> /> Horizontal </span>
				    <span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_alignment" id="tabs_alignment" value="vertical" <?php if($tabs_alignment == 'vertical') { echo "checked"; } ?> /> Vertical </span>
				
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_align">help</a>
				<div id="tabs_r_align" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Align Your Tabs from here',wpshopmart_tabs_r_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/margin-con-tab.png'; ?>">
					
						<br>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/vertical-left.png'; ?>">
					
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><label><?php _e('Tabs Position ',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<div class="switch">
					<input type="radio" class="switch-input" name="tabs_position" value="left" id="enable_tabs_position" <?php if($tabs_position == 'left' ) { echo "checked"; } ?>  >
					<label for="enable_tabs_position" class="switch-label switch-label-off"><?php _e('left',wpshopmart_tabs_r_text_domain); ?></label>
					<input type="radio" class="switch-input" name="tabs_position" value="right" id="disable_tabs_position" <?php if($tabs_position == 'right' ) { echo "checked"; } ?> >
					<label for="disable_tabs_position" class="switch-label switch-label-on"><?php _e('right',wpshopmart_tabs_r_text_domain); ?></label>
					<span class="switch-selection"></span>
				</div>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_pos">help</a>
				<div id="tabs_r_pos" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Align Your Tabs position here ',wpshopmart_tabs_r_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/vertical-left.png'; ?>">
						<br>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/vertical-right.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><label><?php _e('Margin Between Two Tabs',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<div class="switch">
					<input type="radio" class="switch-input" name="tabs_margin" value="yes" id="enable_tabs_margin" <?php if($tabs_margin == 'yes' ) { echo "checked"; } ?>  >
					<label for="enable_tabs_margin" class="switch-label switch-label-off"><?php _e('Yes',wpshopmart_tabs_r_text_domain); ?></label>
					<input type="radio" class="switch-input" name="tabs_margin" value="no" id="disable_tabs_margin" <?php if($tabs_margin == 'no' ) { echo "checked"; } ?> >
					<label for="disable_tabs_margin" class="switch-label switch-label-on"><?php _e('No',wpshopmart_tabs_r_text_domain); ?></label>
					<span class="switch-selection"></span>
				</div>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_2_margin">help</a>
				<div id="tabs_r_2_margin" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Margin Between Two Tabs ',wpshopmart_tabs_r_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/margin-2-tab.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><label><?php _e('Margin Between Tabs And Content',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<div class="switch">
					<input type="radio" class="switch-input" name="tabs_content_margin" value="yes" id="enable_tabs_content_margin" <?php if($tabs_content_margin == 'yes' ) { echo "checked"; } ?>  >
					<label for="enable_tabs_content_margin" class="switch-label switch-label-off"><?php _e('Yes',wpshopmart_tabs_r_text_domain); ?></label>
					<input type="radio" class="switch-input" name="tabs_content_margin" value="no" id="disable_tabs_content_margin" <?php if($tabs_content_margin == 'no' ) { echo "checked"; } ?> >
					<label for="disable_tabs_content_margin" class="switch-label switch-label-on"><?php _e('No',wpshopmart_tabs_r_text_domain); ?></label>
					<span class="switch-selection"></span>
				</div>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_r_con_marg">help</a>
				<div id="tabs_r_con_marg" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Margin Between Tabs And Content',wpshopmart_tabs_r_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/margin-con-tab.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label><?php _e('Tabs Mobile display Settings',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_display_on_mob" id="tabs_display_on_mob" value="1" <?php if($tabs_display_on_mob == '1' ) { echo "checked"; } ?> /> Display Both Title + Icon </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_display_on_mob" id="tabs_display_on_mob" value="2" <?php if($tabs_display_on_mob == '2' ) { echo "checked"; } ?>  /> Display only Icon </span>
				<span style="display:block"><input type="radio" name="tabs_display_on_mob" id="tabs_display_on_mob" value="3"  <?php if($tabs_display_on_mob == '3' ) { echo "checked"; } ?> /> Display Only Title </span>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_display_on_mob_tp">help</a>
				<div id="tabs_display_on_mob_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Hide/display your icon and title on mobile and tablets',wpshopmart_tabs_r_text_domain); ?></h2>
					
					</div>
		    	</div>
			</td>
		</tr>
		
		<tr>
			<th scope="row"><label><?php _e('Title Display Mode In Mobile',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_display_mode_mob" id="tabs_display_mode_mob" value="1" <?php if($tabs_display_mode_mob == '1' ) { echo "checked"; } ?> /> Display As a tabs  </span>
				<span style="display:block;margin-bottom:10px"><input type="radio" name="tabs_display_mode_mob" id="tabs_display_mode_mob" value="2" <?php if($tabs_display_mode_mob == '2' ) { echo "checked"; } ?>  /> Display  As A vertical Button </span>
				<!-- Tooltip -->
				<a  class="ac_tooltip" href="#help" data-tooltip="#tabs_display_mode_mob_tp">help</a>
				<div id="tabs_display_mode_mob_tp" style="display:none;">
					<div style="color:#fff !important;padding:10px;">
						<h2 style="color:#fff !important;"><?php _e('Display Your Title as Vrtical Button or as tabs in Mobile',wpshopmart_tabs_r_text_domain); ?></h2>
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/noise.png'; ?>">
						
						<img src="<?php echo wpshopmart_tabs_r_directory_url.'assets/tooltip/img/as-a-button.png'; ?>">
					</div>
		    	</div>
			</td>
		</tr>
		<tr>
			<th scope="row"><label><?php _e('Tabs On Hover',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<img style="width:100px; "class="wpsm_img_responsive"  src="<?php echo wpshopmart_tabs_r_directory_url.'assets/images/snap.png'; ?>" />
				<br />
				<a style="margin-top:10px" href="http://wpshopmart.com/plugins/tabs-pro-plugin/" target="_balnk">Available In Premium Version</a>
			</td>
		</tr>
	
		<tr>
			<th scope="row"><label><?php _e('',wpshopmart_tabs_r_text_domain); ?></label></th>
			<td>
				<img class="wpsm_img_responsive"  src="<?php echo wpshopmart_tabs_r_directory_url.'assets/images/more-setting.jpg'; ?>" />
				<br />
				<a style="margin-top:10px" href="http://wpshopmart.com/plugins/tabs-pro-plugin/" target="_balnk">Available In Premium Version</a>
			</td>
		</tr>
		<script>
		
		jQuery('.ac_tooltip').darkTooltip({
				opacity:1,
				gravity:'east',
				size:'small'
			});
			

		</script>
	</tbody>
</table>