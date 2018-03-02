jQuery(document).ready(function($){
	$("select[name='chose_cpt']").on("change",function(){
		var $cpt = $(this).val();
		$.ajax({
			url:am2_nl_script_params.ajax_url,
			type:'POST',
			data:{
				action : 'am2_nl_get_post_list',
				term : $cpt
			}
		}).done(function(response, textStatus, jqXHR){
			if(response.data != '') {
				$("select[name='choose_posts']").empty();
				$.each(response.data, function(i, item){
					$("select[name='choose_posts']").append($("<option>", {
						value: item.id,
						text: item.name
					}));
				});
			} else {
				$("select[name='choose_posts']").empty();
			}
		});
	});

	$("#sortable").sortable({
		opacity: 0.6,
		cursor: 'move',
		receive: function(e, ui) { sortableIn = 1; },
		over: function(e, ui) { sortableIn = 1; },
		out: function(e, ui) { sortableIn = 0; },
		beforeStop: function(e, ui) {
		   if (sortableIn == 0) { 
		      ui.item.remove(); 
		   } 
		},
		update: function(e, ui) {

		}
	});

	$("#am2-nl-append-post").on("click",function(e){
		e.preventDefault();
		var id = $("select[name='choose_posts']").val();
		var section = $("select[name='choose_section']").val();
		var tax = $("select[name='chose_cpt'] option:selected").text();
		$.ajax({
			url:am2_nl_script_params.ajax_url,
			type:'POST',
			data:{
				action : 'am2_nl_append_post',
				post : id,
				section : section,
				tax : tax
			}
		}).done(function(response, textStatus, jqXHR){
			if(section == 'ads') {
				$(".top_ads").before(response);
				// Reset text area
				var html = $("#am2_nl_textarea").val();
				var new_html = html.replace('<span class="top_ads"></span>',response+'<span class="top_ads"></span>');
				$("#am2_nl_textarea").val(new_html);
			} else if(section == 'many') {
				$(".before_item").last().before(response);
			} else {
				$(".am2_appender").before(response);			
			}
		});
	});

	$("#am2-nl-generate-html").on("click",function(e){
		e.preventDefault();
		var html = $("#sortable").html();
		$.ajax({
			url:am2_nl_script_params.ajax_url,
			type:'POST',
			data:{
				action : 'am2_nl_generate_html',
				html : html
			}

		}).done(function(response, textStatus, jqXHR){
			$("#am2_nl_textarea").val(response);
			$("#Preheader").remove();
		});
	});

	$("#am2-nl-append-text").on("click",function(e){
		e.preventDefault();
		var text = $("#am2_nl_text_to_append").val();
		$.ajax({
			url:am2_nl_script_params.ajax_url,
			type:'POST',
			data:{
				action : 'am2_nl_append_text',
				text : text
			}

		}).done(function(response, textStatus, jqXHR){
			$(".top_note").before(response);
			// Reset text area
			var html = $("#am2_nl_textarea").val();
			var new_html = html.replace('<span class="top_note"></span>',response+'<span class="top_note"></span>');
			$("#am2_nl_textarea").val(new_html);
		});
	});

});