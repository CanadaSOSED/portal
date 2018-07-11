<script>
var j = 1000;
	function add_new_content(){
	var output = 	'<li class="wpsm_ac-panel single_color_box" >'+
		'<span class="ac_label"><?php _e("Tab Title",wpshopmart_tabs_r_text_domain); ?></span>'+
		'<input type="text" id="tabs_title[]" name="tabs_title[]" value="" placeholder="Enter Tab Title Here" class="wpsm_ac_label_text">'+
		'<span class="ac_label"l><?php _e("Tab Description",wpshopmart_tabs_r_text_domain); ?></span>'+
		'<textarea  id="tabs_desc[]" name="tabs_desc[]"  placeholder="Enter Tab Description Here" class="wpsm_ac_label_text"></textarea>'+
		'<a type="button" class="btn btn-primary btn-block html_editor_button" data-remodal-target="modal" href="#"  id="'+j+'" onclick="open_editor('+j+')">Use WYSIWYG Editor </a>'+
		'<span class="ac_label"><?php _e("Tab Icon",wpshopmart_tabs_r_text_domain); ?></span>'+
		'<div class="form-group input-group" >'+
		'	<input data-placement="bottomRight" id="tabs_title_icon[]" name="tabs_title_icon[]" class="form-control icp icp-auto" value="fa-laptop" type="text" readonly="readonly" />'+
			'<span class="input-group-addon "></span>'+
		'</div>'+
		'<span class="ac_label"><?php _e('Display Above Icon',wpshopmart_tabs_r_text_domain); ?></span>'+
		'<select name="enable_single_icon[]" style="width:100%" >'+
				'<option value="yes" selected=selected>Yes</option>'+
				'<option value="no" >No</option>'+
		'</select>'+
		'<a class="remove_button" href="#delete" id="remove_bt"><i class="fa fa-trash-o"></i></a>'+
		'</li>';
	jQuery(output).hide().appendTo("#colorbox_panel").slideDown("slow");
	j++;
	call_icon();
	}
	jQuery(document).ready(function(){

	  jQuery('#colorbox_panel').sortable({
	  
	   revert: true,
	     
	  });
	});
	
	
</script>
<script>
	jQuery(function(jQuery)
		{
			var colorbox = 
			{
				colorbox_ul: '',
				init: function() 
				{
					this.colorbox_ul = jQuery('#colorbox_panel');

					this.colorbox_ul.on('click', '.remove_button', function() {
					if (confirm('Are you sure you want to delete this?')) {
						jQuery(this).parent().slideUp(600, function() {
							jQuery(this).remove();
						});
					}
					return false;
					});
					 jQuery('#delete_all_colorbox').on('click', function() {
						if (confirm('Are you sure you want to delete all the Colorbox?')) {
							jQuery(".single_color_box").slideUp(600, function() {
								jQuery(".single_color_box").remove();
							});
							jQuery('html, body').animate({ scrollTop: 0 }, 'fast');
							
						}
						return false;
					});
					
			   }
			};
		colorbox.init();
	});
</script>


<script>
	
	
	function open_editor(id){
		

		var value = jQuery("#"+id).closest('li').find('textarea').val();
		jQuery("#get_text-html").click();
		jQuery("#get_text").val(value);
		jQuery("#get_id").val(jQuery("#"+id).attr('id'));
	 }
	
	
	function insert_html(){
		jQuery("#get_text-html").click();
		var html_text = jQuery("#get_text").val();
		var id = jQuery("#get_id").val();
		jQuery("#"+id).closest('li').find('textarea').val(html_text);
			
	}
	
	
</script>