jQuery(document).ready(function($){
  var _custom_media = true,
  _orig_send_attachment = wp.media.editor.send.attachment;

  $('.dc-wp-fields-uploader .upload_button').click(function(e) {
    var send_attachment_bkp = wp.media.editor.send.attachment;
    var button = $(this);
    var id = button.attr('id').replace('_button', '');
    _custom_media = true;
    wp.media.editor.send.attachment = function(props, attachment) {
      if ( _custom_media ) {
        if(attachment.type  == 'image') {
          $("#"+id+'_display').attr('src', attachment.url).removeClass('placeHolder');
          if($("#"+id+'_preview').length > 0)
            $("#"+id+'_preview').attr('src', attachment.url);
        }
        $("#"+id).val(attachment.url);
        $("#"+id).hide();
        button.hide();
        $("#"+id+'_remove_button').show();
      } else {
        return _orig_send_attachment.apply( this, [props, attachment] );
      };
    }

    wp.media.editor.open(button);
    return false;
  });
  
  $('.dc-wp-fields-uploader .remove_button').each(function() {
    var button = $(this);
    var id = button.attr('id').replace('_remove_button', '');
    var attachment_url = $("#"+id+'_display').attr('src');
    if(attachment_url.length == 0) button.hide();
    else $("#"+id+'_button').hide();
    button.click(function(e) {
      $("#"+id+'_display').attr('src', '').addClass('placeHolder');
      $("#"+id+'_preview').attr('src', '');
      $("#"+id).val('').show();
      button.hide();
      $("#"+id+'_button').show();
      return false;
    });
  });

  $('.add_media').on('click', function(){
    _custom_media = false;
  });
});