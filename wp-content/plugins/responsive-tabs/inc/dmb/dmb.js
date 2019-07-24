/**
 * Team Members Admin JS
 */

;(function($){
$(document).ready(function (){

  /* Spencer Tipping jQuery's clone method fix (for select fields). */
  (function (original) {
    jQuery.fn.clone = function () {
      var result           = original.apply(this, arguments),
          my_textareas     = this.find('textarea').add(this.filter('textarea')),
          result_textareas = result.find('textarea').add(result.filter('textarea')),
          my_selects       = this.find('select').add(this.filter('select')),
          result_selects   = result.find('select').add(result.filter('select'));
  
      for (var i = 0, l = my_textareas.length; i < l; ++i) $(result_textareas[i]).val($(my_textareas[i]).val());
      for (var i = 0, l = my_selects.length;   i < l; ++i) result_selects[i].selectedIndex = my_selects[i].selectedIndex;
  
      return result;
    };
  }) (jQuery.fn.clone);


  /* Defines folder slug. */
  var pluginFolderSlug = 'responsive-tabs';


  /* Color conversions. */
  var hexDigits = new Array("0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f");
  function dmb_rgb2hex(rgb) {
    if (rgb) {
      rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
      return "#" + dmb_hex(rgb[1]) + dmb_hex(rgb[2]) + dmb_hex(rgb[3]);
    } else {
      return;
    }
  }
  function dmb_hex(x) {
    return isNaN(x) ? "00" : hexDigits[(x - x % 16) / 16] + hexDigits[x % 16];
  } 


  /* Inits color pickers. */
  $('.dmb_color_picker').wpColorPicker();
  

  /* Processes tab's content fields. */
  /* Initial single input update. */
  $('.dmb_main').not('.dmb_empty_row').each(function(i, obj){

    $(this).find('.dmb_tab_content').each(function(i, obj){
      if ($.trim($(this).text()) == ''){
        $(this).hide();
      } else {
        $(this).show();
      }
      $(this).html($.parseHTML($(this).text()));
    });

  });


  /* Shows/hides no row notice. */
  function refreshRowCountRelatedUI(){
    /* Shows notice when tab set has no tabs. */
    if($('.dmb_main').not('.dmb_empty_row').length > 0){
      $( '.dmb_no_row_notice' ).hide();
    } else {
      $( '.dmb_no_row_notice' ).show();
    }
  }

  refreshRowCountRelatedUI();


  /* Adds a member to the team. */
  $( '.dmb_add_row' ).on('click', function() {
    
    /* Clones/cleans/displays the empty row. */
    var row = $( '.dmb_empty_row' ).clone(true);
    row.removeClass( 'dmb_empty_row' ).addClass('dmb_main').show();
    row.insertBefore( $('.dmb_empty_row') );

    row.find('.dmb_tab_title').focus();

    /* Inits color picker. */
    row.find('.dmb_color_picker_ready').removeClass('.dmb_color_picker_ready').addClass('.dmb_color_picker').wpColorPicker().css({'padding':'3px'});
    
    /* Defaults handle title. */
    row.find('.dmb_handle_title').html(objectL10n.untitled);
    
    /* Hides empty member description. */
    row.find('.dmb_tab_content').hide();

    refreshRowCountRelatedUI();
    return false;

  });


  /* Removes a row. */
  $('.dmb_remove_row_btn').click(function(e) {

    $(this).closest('.dmb_main').remove();

    refreshRowCountRelatedUI();
    return false;

  });


  /* Expands/collapses row. */
  $('.dmb_handle').click(function(e) {
    
    $(this).siblings('.dmb_inner').slideToggle(50);
    
    ($(this).hasClass('closed')) 
      ? $(this).removeClass('closed') 
      : $(this).addClass('closed');

    return false;

  });


  /* Collapses all rows. */
  $('.dmb_collapse_rows').click(function(e) {

    $('.dmb_handle').each(function(i, obj){
      if(!$(this).closest('.dmb_empty_row').length){ // Makes sure not to collapse empty row.
        if($(this).hasClass('closed')){
          
        } else {
          
          $(this).siblings('.dmb_inner').slideToggle(50);
          $(this).addClass('closed');

        }
      }
    });

    return false;

  });


  /* Expands all rows. */
  $('.dmb_expand_rows').click(function(e) {

    $('.dmb_handle').each(function(i, obj){
      if($(this).hasClass('closed')){
        
        $(this).siblings('.dmb_inner').slideToggle(50);
        $(this).removeClass('closed');

      }
    });

    return false;

  });


  /* Shifts a row down (clones and deletes). */
  $('.dmb_move_row_down').click(function(e) {

    if($(this).closest('.dmb_main').next().hasClass('dmb_main')){ // If there's a next row.
      /* Clones the row. */
      var movingRow = $(this).closest('.dmb_main').clone(true);
      /* Inserts it after next row. */
      movingRow.insertAfter($(this).closest('.dmb_main').next());
      /* Removes original row. */
      $(this).closest('.dmb_main').remove();
    }

    return false;

  });


  /* Shifts a row up (clones and deletes). */
  $('.dmb_move_row_up').click(function(e) {

    if($(this).closest('.dmb_main').prev().hasClass('dmb_main')){ // If there's a previous row.
      /* Clones the row. */
      var movingRow = $(this).closest('.dmb_main').clone(true);
      /* Inserts it before previous row. */
      movingRow.insertBefore($(this).closest('.dmb_main').prev());
      /* Removes original row. */
      $(this).closest('.dmb_main').remove();
    }

    return false;

  });


  /* Duplicates a row. */
  $('.dmb_clone_row').click(function(e) {

    /* Clones the row. */
    var clone = $(this).closest('.dmb_main').clone(true);
    /* Inserts it after original row. */
    clone.insertAfter($(this).closest('.dmb_main'));
    /* Adds 'copy' to title. */
    clone.find('.dmb_handle_title').html(clone.find('.dmb_tab_title').val() + ' (copy)');
    clone.find('.dmb_tab_title').focus();

    updateHandleTitle(clone.find('.dmb_tab_title'), true);
    refreshRowCountRelatedUI(); 
    return false;

  });


  /* Adds row title to handle. */
  $('.dmb_main').not('.dmb_empty_row').each(function(i, obj){

    if($(this).find('.dmb_tab_title').val() != ''){

      /* Sets tab title in handle bar title. */
      updateHandleTitle($(this).find('.dmb_tab_title'));

    }

  });

  
  /* Updates handle bar title. */
  function updateHandleTitle(titleField, wasCloned) {

    if (!wasCloned) { wasCloned = false }

    /* Gets current title. */
    var titleField = titleField,
    handleTitle = titleField.closest('.dmb_main').find('.dmb_handle_title');
    cloneCopyText = '';
    (wasCloned) ? cloneCopyText = ' copy' : cloneCopyText = '';
    
    /* Updates handle title. */
    (titleField.val() != '')
      ? handleTitle.html(titleField.val() + cloneCopyText)
      : handleTitle.html(objectL10n.untitled + cloneCopyText);

  }


  /* Watches member firstname/lastname and updates handle. */
  $('.dmb_tab_title').live("keyup", function(e) { updateHandleTitle($(this)); });


  /* Previews tab set. */
  $('.dmb_show_preview_tab_set').click(function(){
    
    var settings = {};
    var tabs = {};
    var preview_html = '';

    settings.breakpoint = $(".dmb_breakpoint").val();    
    settings.color = dmb_rgb2hex($(".wp-color-result").css('backgroundColor')) || '#8dba09';
    settings.tabBackground = $("select[name='tabs_tbgs']").val();

    /* Counts the tabs. */
    tabs.tabCount = $('.dmb_main').not('.dmb_empty_row').size();

    /* Outputing the options in invisible divs */
    preview_html += '<div class="rtbs" style="margin-top:100px; margin-bottom:60px;">';

      preview_html += '<div class="rtbs_breakpoint" style="display:none">' + settings.breakpoint + '</div>';
      preview_html += '<div class="rtbs_inactive_tab_background" style="display:none">' + settings.tabBackground + '</div>';
      preview_html += '<div class="rtbs_color" style="display:none">' + settings.color + '</div>';

      preview_html += '<div class="rtbs_menu">'
        preview_html += '<ul><li class="mobile_toggle">&zwnj;</li>';

        $('.dmb_main').not('.dmb_empty_row').each(function(i, obj){

          /* Gets row fields. */
          var fields = {};

          fields.title = $(this).find(".dmb_tab_title").val();

          if ($('#acf-fallback-bio').length ) {
            fields.content = $.trim($(this).find('.dmb_tab_content_fb').text()) || '';
          } else {
            fields.content = $.trim($(this).find('.dmb_tab_content').html()) || '';
          }
        
          if (i == 0){

            preview_html += "<style>.rtbs li {font-size:15px;} .rtbs_content, .rtbs_content p {font-size:15px;}</style>";


            preview_html += '<li class="current">';
              preview_html += '<a style="background:' + settings.color + '" class="active prev-tab-link-' + i + '" href="#" data-tab="#prev-tab-' + i + '">';

                (fields.title) ?
                  preview_html += fields.title :
                  preview_html += '&nbsp;';

              preview_html += '</a>';
            preview_html += '</li>';
          } else {
            preview_html += '<li style="font-size:15px;">';
              preview_html += '<a href="#" data-tab="#prev-tab-' + i + '" class="prev-tab-link-' + i + '">';

                (fields.title) ?
                  preview_html += fields.title :
                  preview_html += '&nbsp;';

              preview_html += '</a>';
            preview_html += '</li>';
          }
        });

      preview_html += '</ul></div>';

      $('.dmb_main').not('.dmb_empty_row').each(function(i, obj){

        /* Gets row fields. Duplicate. */
        var fields = {};

        fields.title = $(this).find(".dmb_tab_title").val();

        if ($('#acf-fallback-bio').length ) {
          fields.content = $.trim($(this).find('.dmb_tab_content_fb').text()) || '';
        } else {
          fields.content = $.trim($(this).find('.dmb_tab_content').html()) || '';
        }

        if (i == 0){
          preview_html += '<div style="border-top:7px solid ' + settings.color + '; " id="prev-tab-' + i + '" class="rtbs_content active">';
            preview_html += fields.content;
          preview_html += '<div style="margin-top:30px; clear:both;"></div></div>';
        } else {
          preview_html += '<div style="border-top:7px solid ' + settings.color + '; " id="prev-tab-' + i + '" class="rtbs_content">';
            preview_html += fields.content;
          preview_html += '<div style="margin-top:30px; clear:both;"></div></div>';
        }

      });

    preview_html += '</div>';
    preview_html += '<div style="clear:both;"></div>';
    preview_html += '<div class="dmb_accuracy_preview_notice">' + objectL10n.previewAccuracy + '</div>';

    /* Attaches content the preview to container. */
    (tabs.tabCount == 0)
    ? $('#dmb_preview_tabs').append('<div class="dmb_no_row_preview_notice">' + objectL10n.noTabNotice + '</div>')
    : $('#dmb_preview_tabs').append(preview_html);
    
    /* Shows the preview box. */
    $('#dmb_preview_tabs').fadeIn(100);
    
  });

  
  /* Closes the preview. */
  $('.dmb_preview_tabs_close').click(function(){
    $('#dmb_preview_tabs .rtbs, .dmb_accuracy_preview_notice, .dmb_no_row_preview_notice').remove();
    $('#dmb_preview_tabs').fadeOut(100);
  });


  /* Unique editor. */
  var lastEditedBio = '';
  /* Opens the UE to edit bios. */
  $('.dmb_edit_tab_content').click(function(){

    lastEditedBio = $(this).parent().find('.dmb_tab_content');
    var currentContent = lastEditedBio.html();

    if ($("#wp-dmb_editor-wrap").hasClass("tmce-active")){
      tinyMCE.editors[0].setContent(currentContent);
    } else {
      $('#dmb_editor').val($.trim(currentContent));
    }
    $('#dmb_unique_editor').fadeIn(100);
    if (tinyMCE.activeEditor !== null) { tinyMCE.activeEditor.focus(); } 
    
  });


  /* Saves the UE data. */
  $('.dmb_ue_update').click(function(){

    if ($("#wp-dmb_editor-wrap").hasClass("tmce-active")){
      var dmb_ue_content = tinyMCE.activeEditor.getContent();
    } else {
      var dmb_ue_content = $('#dmb_editor').val();
    }
    
    /* Hides bio block if empty. */
    (!dmb_ue_content) ? lastEditedBio.hide() : lastEditedBio.show();

    /* Adds bio content if there is. */
    lastEditedBio.html($.parseHTML(dmb_ue_content));
    lastEditedBio.siblings('.biofield').val(dmb_ue_content);

    /* Closes and empties UE. */
    $('#dmb_unique_editor').fadeOut(100);
    if (tinymce.activeEditor !== null) { tinymce.activeEditor.setContent(''); }

    return false;

  });


  /* Cancels the UE updates. */
  $('.dmb_ue_cancel').click(function(){
    $('#dmb_unique_editor').fadeOut(100);
  });

});
})(jQuery);