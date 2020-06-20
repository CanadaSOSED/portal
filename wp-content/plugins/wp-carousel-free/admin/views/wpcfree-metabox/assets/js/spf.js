/**
 *
 * -----------------------------------------------------------
 *
 * SP Framework
 * A Simple and Lightweight WordPress Option Framework
 *
 * -----------------------------------------------------------
 *
 */
;(function( $, window, document, undefined ) {
  'use strict';

  //
  // Constants
  //
  var SP_WPCF   = SP_WPCF || {};

  SP_WPCF.funcs = {};

  SP_WPCF.vars  = {
    onloaded: false,
    $body: $('body'),
    $window: $(window),
    $document: $(document),
    is_rtl: $('body').hasClass('rtl'),
    code_themes: [],
  };

  //
  // Helper Functions
  //
  SP_WPCF.helper = {

    //
    // Generate UID
    //
    uid: function( prefix ) {
      return ( prefix || '' ) + Math.random().toString(36).substr(2, 9);
    },

    // Quote regular expression characters
    //
    preg_quote: function( str ) {
      return (str+'').replace(/(\[|\-|\])/g, "\\$1");
    },

    //
    // Reneme input names
    //
    name_nested_replace: function( $selector, field_id ) {

      var checks = [];
      var regex  = new RegExp('('+ SP_WPCF.helper.preg_quote(field_id) +')\\[(\\d+)\\]', 'g');

      $selector.find(':radio').each(function() {
        if( this.checked || this.orginal_checked ) {
          this.orginal_checked = true;
        }
      });

      $selector.each( function( index ) {
        $(this).find(':input').each(function() {
          this.name = this.name.replace(regex, field_id +'['+ index +']');
          if( this.orginal_checked ) {
            this.checked = true;
          }
        });
      });

    },

    //
    // Debounce
    //
    debounce: function( callback, threshold, immediate ) {
      var timeout;
      return function() {
        var context = this, args = arguments;
        var later = function() {
          timeout = null;
          if( !immediate ) {
            callback.apply(context, args);
          }
        };
        var callNow = ( immediate && !timeout );
        clearTimeout( timeout );
        timeout = setTimeout( later, threshold );
        if( callNow ) {
          callback.apply(context, args);
        }
      };
    },

    //
    // Get a cookie
    //
    get_cookie: function( name ) {

      var e, b, cookie = document.cookie, p = name + '=';

      if( ! cookie ) {
        return;
      }

      b = cookie.indexOf( '; ' + p );

      if( b === -1 ) {
        b = cookie.indexOf(p);

        if( b !== 0 ) {
          return null;
        }
      } else {
        b += 2;
      }

      e = cookie.indexOf( ';', b );

      if( e === -1 ) {
        e = cookie.length;
      }

      return decodeURIComponent( cookie.substring( b + p.length, e ) );

    },

    //
    // Set a cookie
    //
    set_cookie: function( name, value, expires, path, domain, secure ) {

      var d = new Date();

      if( typeof( expires ) === 'object' && expires.toGMTString ) {
        expires = expires.toGMTString();
      } else if( parseInt( expires, 10 ) ) {
        d.setTime( d.getTime() + ( parseInt( expires, 10 ) * 1000 ) );
        expires = d.toGMTString();
      } else {
        expires = '';
      }

      document.cookie = name + '=' + encodeURIComponent( value ) +
        ( expires ? '; expires=' + expires : '' ) +
        ( path    ? '; path=' + path       : '' ) +
        ( domain  ? '; domain=' + domain   : '' ) +
        ( secure  ? '; secure'             : '' );

    },

    //
    // Remove a cookie
    //
    remove_cookie: function( name, path, domain, secure ) {
      SP_WPCF.helper.set_cookie( name, '', -1000, path, domain, secure );
    },

  };

  //
  // Custom clone for textarea and select clone() bug
  //
  $.fn.spf_clone = function() {

    var base   = $.fn.clone.apply(this, arguments),
        clone  = this.find('select').add(this.filter('select')),
        cloned = base.find('select').add(base.filter('select'));

    for( var i = 0; i < clone.length; ++i ) {
      for( var j = 0; j < clone[i].options.length; ++j ) {

        if( clone[i].options[j].selected === true ) {
          cloned[i].options[j].selected = true;
        }

      }
    }

    this.find(':radio').each( function() {
      this.orginal_checked = this.checked;
    });

    return base;

  };

  //
  // Expand All Options
  //
  $.fn.spf_expand_all = function() {
    return this.each( function() {
      $(this).on('click', function( e ) {

        e.preventDefault();
        $('.spf-wrapper').toggleClass('spf-show-all');
        $('.spf-section').spf_reload_script();
        $(this).find('.fa').toggleClass('fa-indent').toggleClass('fa-outdent');

      });
    });
  };

  //
  // Options Navigation
  //
  $.fn.spf_nav_options = function() {
    return this.each( function() {

      var $nav    = $(this),
          $links  = $nav.find('a'),
          $hidden = $nav.closest('.spf').find('.spf-section-id'),
          $last_section;

      $(window).on('hashchange', function() {

        var hash  = window.location.hash.match(new RegExp('tab=([^&]*)'));
        var slug  = hash ? hash[1] : $links.first().attr('href').replace('#tab=', '');
        var $link = $('#spf-tab-link-'+ slug);

        if( $link.length > 0 ) {

          $link.closest('.spf-tab-depth-0').addClass('spf-tab-active').siblings().removeClass('spf-tab-active');
          $links.removeClass('spf-section-active');
          $link.addClass('spf-section-active');

          if( $last_section !== undefined ) {
            $last_section.hide();
          }

          var $section = $('#spf-section-'+slug);
          $section.css({display: 'block'});
          $section.spf_reload_script();

          $hidden.val(slug);

          $last_section = $section;

        }

      }).trigger('hashchange');

    });
  };

  //
  // Metabox Tabs
  //
  $.fn.spf_nav_metabox = function() {
    return this.each( function() {

      var $nav      = $(this),
          $links    = $nav.find('a'),
          unique_id = $nav.data('unique'),
          post_id   = $('#post_ID').val() || 'global',
          $last_section,
          $last_link;

      $links.on('click', function( e ) {

        e.preventDefault();

        var $link      = $(this),
            section_id = $link.data('section');

        if( $last_link !== undefined ) {
          $last_link.removeClass('spf-section-active');
        }

        if( $last_section !== undefined ) {
          $last_section.hide();
        }

        $link.addClass('spf-section-active');

        var $section = $('#spf-section-'+section_id);
        $section.css({display: 'block'});
        $section.spf_reload_script();

        SP_WPCF.helper.set_cookie('spf-last-metabox-tab-'+ post_id +'-'+ unique_id, section_id);

        $last_section = $section;
        $last_link    = $link;

      });

      var get_cookie = SP_WPCF.helper.get_cookie('spf-last-metabox-tab-'+ post_id +'-'+ unique_id);

      if( get_cookie ) {
        $nav.find('a[data-section="'+ get_cookie +'"]').trigger('click');
      } else {
        $links.first('a').trigger('click');
      }

    });
  };

  //
  // Metabox Page Templates Listener
  //
  $.fn.spf_page_templates = function() {
    if( this.length ) {

      $(document).on('change', '.editor-page-attributes__template select, #page_template', function() {

        var maybe_value = $(this).val() || 'default';

        $('.spf-page-templates').removeClass('spf-show').addClass('spf-hide');
        $('.spf-page-'+maybe_value.toLowerCase().replace(/[^a-zA-Z0-9]+/g,'-')).removeClass('spf-hide').addClass('spf-show');

      });

    }
  };

  //
  // Metabox Post Formats Listener
  //
  $.fn.spf_post_formats = function() {
    if( this.length ) {

      $(document).on('change', '.editor-post-format select, #formatdiv input[name="post_format"]', function() {

        var maybe_value = $(this).val() || 'default';

        // Fallback for classic editor version
        maybe_value = ( maybe_value === '0' ) ? 'default' : maybe_value;

        $('.spf-post-formats').removeClass('spf-show').addClass('spf-hide');
        $('.spf-post-format-'+maybe_value).removeClass('spf-hide').addClass('spf-show');

      });

    }
  };

  //
  // Search
  //
  $.fn.spf_search = function() {
    return this.each( function() {

      var $this    = $(this),
          $input   = $this.find('input');

      $input.on('change keyup', function() {

        var value    = $(this).val(),
            $wrapper = $('.spf-wrapper'),
            $section = $wrapper.find('.spf-section'),
            $fields  = $section.find('> .spf-field:not(.hidden)'),
            $titles  = $fields.find('> .spf-title, .spf-search-tags');

        if( value.length > 3 ) {

          $fields.addClass('spf-hidden');
          $wrapper.addClass('spf-search-all');

          $titles.each( function() {

            var $title = $(this);

            if( $title.text().match( new RegExp('.*?' + value + '.*?', 'i') ) ) {

              var $field = $title.closest('.spf-field');

              $field.removeClass('spf-hidden');
              $field.parent().spf_reload_script();

            }

          });

        } else {

          $fields.removeClass('spf-hidden');
          $wrapper.removeClass('spf-search-all');

        }

      });

    });
  };

  //
  // Sticky Header
  //
  $.fn.spf_sticky = function() {
    return this.each( function() {

      var $this     = $(this),
          $window   = $(window),
          $inner    = $this.find('.spf-header-inner'),
          padding   = parseInt( $inner.css('padding-left') ) + parseInt( $inner.css('padding-right') ),
          offset    = 32,
          scrollTop = 0,
          lastTop   = 0,
          ticking   = false,
          stickyUpdate = function() {

            var offsetTop = $this.offset().top,
                stickyTop = Math.max(offset, offsetTop - scrollTop ),
                winWidth  = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);

            if( stickyTop <= offset && winWidth > 782 ) {
              $inner.css({width: $this.outerWidth()-padding});
              $this.css({height: $this.outerHeight()}).addClass( 'spf-sticky' );
            } else {
              $inner.removeAttr('style');
              $this.removeAttr('style').removeClass( 'spf-sticky' );
            }

          },
          requestTick = function() {

            if( !ticking ) {
              requestAnimationFrame( function() {
                stickyUpdate();
                ticking = false;
              });
            }

            ticking = true;

          },
          onSticky  = function() {

            scrollTop = $window.scrollTop();
            requestTick();

          };

      $window.on( 'scroll resize', onSticky);

      onSticky();

    });
  };

  //
  // Dependency System
  //
  $.fn.spf_dependency = function() {
    return this.each( function() {

      var $this     = $(this),
          ruleset   = $.spf_deps.createRuleset(),
          depends   = [],
          is_global = false;

      $this.children('[data-controller]').each( function() {

        var $field      = $(this),
            controllers = $field.data('controller').split('|'),
            conditions  = $field.data('condition').split('|'),
            values      = $field.data('value').toString().split('|'),
            rules       = ruleset;

        if( $field.data('depend-global') ) {
          is_global = true;
        }

        $.each(controllers, function( index, depend_id ) {

          var value     = values[index] || '',
              condition = conditions[index] || conditions[0];

          rules = rules.createRule('[data-depend-id="'+ depend_id +'"]', condition, value);

          rules.include($field);

          depends.push(depend_id);

        });

      });

      if( depends.length ) {

        if( is_global ) {
          $.spf_deps.enable(SP_WPCF.vars.$body, ruleset, depends);
        } else {
          $.spf_deps.enable($this, ruleset, depends);
        }

      }

    });
  };

  //
  // Field: accordion
  //
  $.fn.spf_field_accordion = function() {
    return this.each( function() {

      var $titles = $(this).find('.spf-accordion-title');

      $titles.on('click', function() {

        var $title   = $(this),
            $icon    = $title.find('.spf-accordion-icon'),
            $content = $title.next();

        if( $icon.hasClass('fa-angle-right') ) {
          $icon.removeClass('fa-angle-right').addClass('fa-angle-down');
        } else {
          $icon.removeClass('fa-angle-down').addClass('fa-angle-right');
        }

        if( !$content.data( 'opened' ) ) {

          $content.spf_reload_script();
          $content.data( 'opened', true );

        }

        $content.toggleClass('spf-accordion-open');

      });

    });
  };

  //
  // Field: code_editor
  //
  $.fn.spf_field_code_editor = function() {
    return this.each( function() {

      if( typeof CodeMirror !== 'function' ) { return; }

      var $this       = $(this),
          $textarea   = $this.find('textarea'),
          $inited     = $this.find('.CodeMirror'),
          data_editor = $textarea.data('editor');

      if( $inited.length ) {
        $inited.remove();
      }

      var interval = setInterval(function () {
        if( $this.is(':visible') ) {

          var code_editor = CodeMirror.fromTextArea( $textarea[0], data_editor );

          // load code-mirror theme css.
          if( data_editor.theme !== 'default' && SP_WPCF.vars.code_themes.indexOf(data_editor.theme) === -1 ) {

            var $cssLink = $('<link>');

            $('#spf-codemirror-css').after( $cssLink );

            $cssLink.attr({
              rel: 'stylesheet',
              id: 'spf-codemirror-'+ data_editor.theme +'-css',
              href: data_editor.cdnURL +'/theme/'+ data_editor.theme +'.min.css',
              type: 'text/css',
              media: 'all'
            });

            SP_WPCF.vars.code_themes.push(data_editor.theme);

          }

          CodeMirror.modeURL = data_editor.cdnURL +'/mode/%N/%N.min.js';
          CodeMirror.autoLoadMode(code_editor, data_editor.mode);

          code_editor.on( 'change', function( editor, event ) {
            $textarea.val( code_editor.getValue() ).trigger('change');
          });

          clearInterval(interval);

        }
      });

    });
  };

  //
  // Field: date
  //
  $.fn.spf_field_date = function() {
    return this.each( function() {

      var $this    = $(this),
          $inputs  = $this.find('input'),
          settings = $this.find('.spf-date-settings').data('settings'),
          wrapper  = '<div class="spf-datepicker-wrapper"></div>',
          $datepicker;

      var defaults = {
        showAnim: '',
        beforeShow: function(input, inst) {
          $(inst.dpDiv).addClass('spf-datepicker-wrapper');
        },
        onClose: function( input, inst ) {
          $(inst.dpDiv).removeClass('spf-datepicker-wrapper');
        },
      };

      settings = $.extend({}, settings, defaults);

      if( $inputs.length === 2 ) {

        settings = $.extend({}, settings, {
          onSelect: function( selectedDate ) {

            var $this  = $(this),
                $from  = $inputs.first(),
                option = ( $inputs.first().attr('id') === $(this).attr('id') ) ? 'minDate' : 'maxDate',
                date   = $.datepicker.parseDate( settings.dateFormat, selectedDate );

            $inputs.not(this).datepicker('option', option, date );

          }
        });

      }

      $inputs.each( function(){

        var $input = $(this);

        if( $input.hasClass('hasDatepicker') ) {
          $input.removeAttr('id').removeClass('hasDatepicker');
        }

        $input.datepicker(settings);

      });

    });
  };

  //
  // Field: fieldset
  //
  $.fn.spf_field_fieldset = function() {
    return this.each( function() {
      $(this).find('.spf-fieldset-content').spf_reload_script();
    });
  };

  //
  // Field: gallery
  //
  $.fn.spf_field_gallery = function() {
    return this.each( function() {

      var $this  = $(this),
          $edit  = $this.find('.spf-edit-gallery'),
          $clear = $this.find('.spf-clear-gallery'),
          $list  = $this.find('ul.sp-gallery-images'),
          $input = $this.find('input'),
          $img   = $this.find('img'),
          wp_media_frame;

      $this.on('click', '.spf-button, .spf-edit-gallery', function( e ) {

        var $el   = $(this),
            ids   = $input.val(),
            what  = ( $el.hasClass('spf-edit-gallery') ) ? 'edit' : 'add',
            state = ( what === 'add' && !ids.length ) ? 'gallery' : 'gallery-edit';

        e.preventDefault();

        if( typeof window.wp === 'undefined' || ! window.wp.media || ! window.wp.media.gallery ) { return; }

         // Open media with state
        if( state === 'gallery' ) {

          wp_media_frame = window.wp.media({
            library: {
              type: 'image'
            },
            frame: 'post',
            state: 'gallery',
            multiple: true
          });

          wp_media_frame.open();

        } else {

          wp_media_frame = window.wp.media.gallery.edit( '[gallery ids="'+ ids +'"]' );

          if( what === 'add' ) {
            wp_media_frame.setState('gallery-library');
          }

        }

        // Media Update
        wp_media_frame.on( 'update', function( selection ) {

          $list.empty();

          var selectedIds = selection.models.map( function( attachment ) {

            var item  = attachment.toJSON();
            var thumb = ( typeof item.sizes.thumbnail !== 'undefined' ) ? item.sizes.thumbnail.url : item.url;

            $list.append('<li><img src="'+ thumb +'"></li>');

            return item.id;

          });

          $input.val( selectedIds.join( ',' ) ).trigger('change');
          $clear.removeClass('hidden');
          $edit.removeClass('hidden');

        });

      });

      $clear.on('click', function( e ) {
        e.preventDefault();
        $list.empty();
        $input.val('').trigger('change');
        $clear.addClass('hidden');
        $edit.addClass('hidden');
      });

    });

  };

  //
  // Field: group
  //
  $.fn.spf_field_group = function() {
    return this.each( function() {

      var $this     = $(this),
          $fieldset = $this.children('.spf-fieldset'),
          $group    = $fieldset.length ? $fieldset : $this,
          $wrapper  = $group.children('.spf-cloneable-wrapper'),
          $hidden   = $group.children('.spf-cloneable-hidden'),
          $max      = $group.children('.spf-cloneable-max'),
          $min      = $group.children('.spf-cloneable-min'),
          field_id  = $wrapper.data('field-id'),
          unique_id = $wrapper.data('unique-id'),
          is_number = Boolean( Number( $wrapper.data('title-number') ) ),
          max       = parseInt( $wrapper.data('max') ),
          min       = parseInt( $wrapper.data('min') );

      // clear accordion arrows if multi-instance
      if( $wrapper.hasClass('ui-accordion') ) {
        $wrapper.find('.ui-accordion-header-icon').remove();
      }

      var update_title_numbers = function( $selector ) {
        $selector.find('.spf-cloneable-title-number').each( function( index ) {
          $(this).html( ( $(this).closest('.spf-cloneable-item').index()+1 ) + '.' );
        });
      };

      $wrapper.accordion({
        header: '> .spf-cloneable-item > .spf-cloneable-title',
        collapsible : true,
        active: false,
        animate: false,
        heightStyle: 'content',
        icons: {
          'header': 'spf-cloneable-header-icon fa fa-angle-right',
          'activeHeader': 'spf-cloneable-header-icon fa fa-angle-down'
        },
        activate: function( event, ui ) {

          var $panel  = ui.newPanel;
          var $header = ui.newHeader;

          if( $panel.length && !$panel.data( 'opened' ) ) {

            var $fields = $panel.children();
            var $first  = $fields.first().find(':input').first();
            var $title  = $header.find('.spf-cloneable-value');

            $first.on('keyup', function( event ) {
              $title.text($first.val());
            });

            $panel.spf_reload_script();
            $panel.data( 'opened', true );
            $panel.data( 'retry', false );

          } else if( $panel.data( 'retry' ) ) {

            $panel.spf_reload_script_retry();
            $panel.data( 'retry', false );

          }

        }
      });

      $wrapper.sortable({
        axis: 'y',
        handle: '.spf-cloneable-title,.spf-cloneable-sort',
        helper: 'original',
        cursor: 'move',
        placeholder: 'widget-placeholder',
        start: function( event, ui ) {

          $wrapper.accordion({ active:false });
          $wrapper.sortable('refreshPositions');
          ui.item.children('.spf-cloneable-content').data('retry', true);

        },
        update: function( event, ui ) {

          SP_WPCF.helper.name_nested_replace( $wrapper.children('.spf-cloneable-item'), field_id );
          $wrapper.spf_customizer_refresh();

          if( is_number ) {
            update_title_numbers($wrapper);
          }

        },
      });

      $group.children('.spf-cloneable-add').on('click', function( e ) {

        e.preventDefault();

        var count = $wrapper.children('.spf-cloneable-item').length;

        $min.hide();

        if( max && (count+1) > max ) {
          $max.show();
          return;
        }

        var new_field_id = unique_id + field_id + '['+ count +']';

        var $cloned_item = $hidden.spf_clone(true);

        $cloned_item.removeClass('spf-cloneable-hidden');

        $cloned_item.find(':input').each( function() {
          this.name = new_field_id + this.name.replace( ( this.name.startsWith('_nonce') ? '_nonce' : unique_id ), '');
        });

        $cloned_item.find('.spf-data-wrapper').each( function(){
          $(this).attr('data-unique-id', new_field_id );
        });

        $wrapper.append($cloned_item);
        $wrapper.accordion('refresh');
        $wrapper.accordion({active: count});
        $wrapper.spf_customizer_refresh();
        $wrapper.spf_customizer_listen({closest: true});

        if( is_number ) {
          update_title_numbers($wrapper);
        }

      });

      var event_clone = function( e ) {

        e.preventDefault();

        var count = $wrapper.children('.spf-cloneable-item').length;

        $min.hide();

        if( max && (count+1) > max ) {
          $max.show();
          return;
        }

        var $this           = $(this),
            $parent         = $this.parent().parent(),
            $cloned_helper  = $parent.children('.spf-cloneable-helper').spf_clone(true),
            $cloned_title   = $parent.children('.spf-cloneable-title').spf_clone(),
            $cloned_content = $parent.children('.spf-cloneable-content').spf_clone(),
            cloned_regex    = new RegExp('('+ SP_WPCF.helper.preg_quote(field_id) +')\\[(\\d+)\\]', 'g');

        $cloned_content.find('.spf-data-wrapper').each( function(){
          var $this = $(this);
          $this.attr('data-unique-id', $this.attr('data-unique-id').replace(cloned_regex, field_id +'['+ ($parent.index()+1) +']') );
        });

        var $cloned = $('<div class="spf-cloneable-item" />');

        $cloned.append($cloned_helper);
        $cloned.append($cloned_title);
        $cloned.append($cloned_content);

        $wrapper.children().eq($parent.index()).after($cloned);

        SP_WPCF.helper.name_nested_replace( $wrapper.children('.spf-cloneable-item'), field_id );

        $wrapper.accordion('refresh');
        $wrapper.spf_customizer_refresh();
        $wrapper.spf_customizer_listen({closest: true});

        if( is_number ) {
          update_title_numbers($wrapper);
        }

      };

      $wrapper.children('.spf-cloneable-item').children('.spf-cloneable-helper').on('click', '.spf-cloneable-clone', event_clone);
      $group.children('.spf-cloneable-hidden').children('.spf-cloneable-helper').on('click', '.spf-cloneable-clone', event_clone);

      var event_remove = function( e ) {

        e.preventDefault();

        var count = $wrapper.children('.spf-cloneable-item').length;

        $max.hide();
        $min.hide();

        if( min && (count-1) < min ) {
          $min.show();
          return;
        }

        $(this).closest('.spf-cloneable-item').remove();

        SP_WPCF.helper.name_nested_replace( $wrapper.children('.spf-cloneable-item'), field_id );

        $wrapper.spf_customizer_refresh();

        if( is_number ) {
          update_title_numbers($wrapper);
        }

      };

      $wrapper.children('.spf-cloneable-item').children('.spf-cloneable-helper').on('click', '.spf-cloneable-remove', event_remove);
      $group.children('.spf-cloneable-hidden').children('.spf-cloneable-helper').on('click', '.spf-cloneable-remove', event_remove);

    });
  };

  //
  // Field: media
  //
  $.fn.spf_field_media = function() {
    return this.each( function() {

      var $this          = $(this),
          $upload_button = $this.find('.spf--button'),
          $remove_button = $this.find('.spf--remove'),
          $library       = $upload_button.data('library') && $upload_button.data('library').split(',') || '',
          wp_media_frame;

      $upload_button.on('click', function( e ) {

        e.preventDefault();

        if( typeof window.wp === 'undefined' || ! window.wp.media || ! window.wp.media.gallery ) {
          return;
        }

        if( wp_media_frame ) {
          wp_media_frame.open();
          return;
        }

        wp_media_frame = window.wp.media({
          library: {
            type: $library
          }
        });

        wp_media_frame.on( 'select', function() {

          var thumbnail;
          var attributes   = wp_media_frame.state().get('selection').first().attributes;
          var preview_size = $upload_button.data('preview-size') || 'thumbnail';

          $this.find('.spf--url').val( attributes.url );
          $this.find('.spf--id').val( attributes.id );
          $this.find('.spf--width').val( attributes.width );
          $this.find('.spf--height').val( attributes.height );
          $this.find('.spf--alt').val( attributes.alt );
          $this.find('.spf--title').val( attributes.title );
          $this.find('.spf--description').val( attributes.description );

          if( typeof attributes.sizes !== 'undefined' && typeof attributes.sizes.thumbnail !== 'undefined' && preview_size === 'thumbnail' ) {
            thumbnail = attributes.sizes.thumbnail.url;
          } else if( typeof attributes.sizes !== 'undefined' && typeof attributes.sizes.full !== 'undefined' ) {
            thumbnail = attributes.sizes.full.url;
          } else {
            thumbnail = attributes.icon;
          }

          $remove_button.removeClass('hidden');
          $this.find('.spf--preview').removeClass('hidden');
          $this.find('.spf--src').attr('src', thumbnail);
          $this.find('.spf--thumbnail').val( thumbnail ).trigger('change');

        });

        wp_media_frame.open();

      });

      $remove_button.on('click', function( e ) {
        e.preventDefault();
        $remove_button.addClass('hidden');
        $this.find('.spf--preview').addClass('hidden');
        $this.find('input').val('');
        $this.find('.spf--thumbnail').trigger('change');
      });

    });

  };

  //
  // Field: spinner
  //
  $.fn.spf_field_spinner = function() {
    return this.each( function() {

      var $this   = $(this),
          $input  = $this.find('input'),
          $inited = $this.find('.ui-spinner-button');

      if( $inited.length ) {
        $inited.remove();
      }

      $input.spinner({
        max: $input.data('max') || 100,
        min: $input.data('min') || 0,
        step: $input.data('step') || 1,
        spin: function (event, ui ) {
          $input.val(ui.value).trigger('change');
        }
      });


    });
  };

  //
  // Field: switcher
  //
  $.fn.spf_field_switcher = function() {
    return this.each( function() {

      var $switcher = $(this).find('.spf--switcher');

      $switcher.on('click', function() {

        var value  = 0;
        var $input = $switcher.find('input');

        if( $switcher.hasClass('spf--active') ) {
          $switcher.removeClass('spf--active');
        } else {
          value = 1;
          $switcher.addClass('spf--active');
        }

        $input.val(value).trigger('change');

      });

    });
  };

  //
  // Field: typography
  //
  $.fn.spf_field_typography = function() {
    return this.each(function () {

      var base          = this;
      var $this         = $(this);
      var loaded_fonts  = [];
      var webfonts      = spf_typography_json.webfonts;
      var googlestyles  = spf_typography_json.googlestyles;
      var defaultstyles = spf_typography_json.defaultstyles;

      //
      //
      // Sanitize google font subset
      base.sanitize_subset = function( subset ) {
        subset = subset.replace('-ext', ' Extended');
        subset = subset.charAt(0).toUpperCase() + subset.slice(1);
        return subset;
      };

      //
      //
      // Sanitize google font styles (weight and style)
      base.sanitize_style = function( style ) {
        return googlestyles[style] ? googlestyles[style] : style;
      };

      //
      //
      // Load google font
      base.load_google_font = function( font_family, weight, style ) {

        if( font_family && typeof WebFont === 'object' ) {

          weight = weight ? weight.replace('normal', '') : '';
          style  = style ? style.replace('normal', '') : '';

          if( weight || style ) {
            font_family = font_family +':'+ weight + style;
          }

          if( loaded_fonts.indexOf( font_family ) === -1 ) {
            WebFont.load({ google: { families: [font_family] } });
          }

          loaded_fonts.push( font_family );

        }

      };

      //
      //
      // Append select options
      base.append_select_options = function( $select, options, condition, type, is_multi ) {

        $select.find('option').not(':first').remove();

        var opts = '';

        $.each( options, function( key, value ) {

          var selected;
          var name = value;

          // is_multi
          if( is_multi ) {
            selected = ( condition && condition.indexOf(value) !== -1 ) ? ' selected' : '';
          } else {
            selected = ( condition && condition === value ) ? ' selected' : '';
          }

          if( type === 'subset' ) {
            name = base.sanitize_subset( value );
          } else if( type === 'style' ){
            name = base.sanitize_style( value );
          }

          opts += '<option value="'+ value +'"'+ selected +'>'+ name +'</option>';

        });

        $select.append(opts).trigger('spf.change').trigger('chosen:updated');

      };

      base.init = function () {

        //
        //
        // Constants
        var selected_styles = [];
        var $typography     = $this.find('.spf--typography');
        var $type           = $this.find('.spf--type');
        var unit            = $typography.data('unit');
        var exclude_fonts   = $typography.data('exclude') ? $typography.data('exclude').split(',') : [];

        //
        //
        // Chosen init
        if( $this.find('.spf--chosen').length ) {

          var $chosen_selects = $this.find('select');

          $chosen_selects.each( function(){

            var $chosen_select = $(this),
                $chosen_inited = $chosen_select.parent().find('.chosen-container');

            if( $chosen_inited.length ) {
              $chosen_inited.remove();
            }

            $chosen_select.chosen({
              allow_single_deselect: true,
              disable_search_threshold: 15,
              width: '100%'
            });

          });

        }

        //
        //
        // Font family select
        var $font_family_select = $this.find('.spf--font-family');
        var first_font_family   = $font_family_select.val();

        // Clear default font family select options
        $font_family_select.find('option').not(':first-child').remove();

        var opts = '';

        $.each(webfonts, function( type, group ) {

          // Check for exclude fonts
          if( exclude_fonts && exclude_fonts.indexOf(type) !== -1 ) { return; }

          opts += '<optgroup label="' + group.label + '">';

          $.each(group.fonts, function( key, value ) {

            // use key if value is object
            value = ( typeof value === 'object' ) ? key : value;
            var selected = ( value === first_font_family ) ? ' selected' : '';
            opts += '<option value="'+ value +'" data-type="'+ type +'"'+ selected +'>'+ value +'</option>';

          });

          opts += '</optgroup>';

        });

        // Append google font select options
        $font_family_select.append(opts).trigger('chosen:updated');

        //
        //
        // Font style select
        var $font_style_block = $this.find('.spf--block-font-style');

        if( $font_style_block.length ) {

          var $font_style_select = $this.find('.spf--font-style-select');
          var first_style_value  = $font_style_select.val() ? $font_style_select.val().replace(/normal/g, '' ) : '';

          //
          //
          // Extra font style select
          var $extra_font_style_block = $this.find('.spf--block-extra-styles');

          if( $extra_font_style_block.length ) {
            var $extra_font_style_select = $this.find('.spf--extra-styles');
            var first_extra_style_value  = $extra_font_style_select.val();
          }

        }

        //
        //
        // Subsets select
        var $subset_block = $this.find('.spf--block-subset');
        if( $subset_block.length ) {
          var $subset_select = $this.find('.spf--subset');
          var first_subset_select_value = $subset_select.val();
          var subset_multi_select = $subset_select.data('multiple') || false;
        }

        //
        //
        // Backup font family
        var $backup_font_family_block = $this.find('.spf--block-backup-font-family');

        //
        //
        // Font Family on Change Listener
        $font_family_select.on('change spf.change', function( event ) {

          // Hide subsets on change
          if( $subset_block.length ) {
            $subset_block.addClass('hidden');
          }

          // Hide extra font style on change
          if( $extra_font_style_block.length ) {
            $extra_font_style_block.addClass('hidden');
          }

          // Hide backup font family on change
          if( $backup_font_family_block.length ) {
            $backup_font_family_block.addClass('hidden');
          }

          var $selected = $font_family_select.find(':selected');
          var value     = $selected.val();
          var type      = $selected.data('type');

          if( type && value ) {

            // Show backup fonts if font type google or custom
            if( ( type === 'google' || type === 'custom' ) && $backup_font_family_block.length ) {
              $backup_font_family_block.removeClass('hidden');
            }

          } else {

            // Clear subsets options if type and value empty
            if( $subset_block.length ) {
              $subset_select.find('option').not(':first-child').remove();
              $subset_select.trigger('chosen:updated');
            }

            // Clear font styles options if type and value empty
            if( $font_style_block.length ) {
              $font_style_select.find('option').not(':first-child').remove();
              $font_style_select.trigger('chosen:updated');
            }

          }

          // Update font type input value
          $type.val(type);

        }).trigger('spf.change');

      };

      base.init();

    });
  };

  //
  // Field: upload
  //
  $.fn.spf_field_upload = function() {
    return this.each( function() {

      var $this          = $(this),
          $input         = $this.find('input'),
          $upload_button = $this.find('.spf--button'),
          $remove_button = $this.find('.spf--remove'),
          $library       = $upload_button.data('library') && $upload_button.data('library').split(',') || '',
          wp_media_frame;

      $input.on('change', function( e ) {
        if( $input.val() ) {
          $remove_button.removeClass('hidden');
        } else {
          $remove_button.addClass('hidden');
        }
      });

      $upload_button.on('click', function( e ) {

        e.preventDefault();

        if( typeof window.wp === 'undefined' || ! window.wp.media || ! window.wp.media.gallery ) {
          return;
        }

        if( wp_media_frame ) {
          wp_media_frame.open();
          return;
        }

        wp_media_frame = window.wp.media({
          library: {
            type: $library
          },
        });

        wp_media_frame.on( 'select', function() {
          $input.val( wp_media_frame.state().get('selection').first().attributes.url ).trigger('change');
        });

        wp_media_frame.open();

      });

      $remove_button.on('click', function( e ) {
        e.preventDefault();
        $input.val('').trigger('change');
      });

    });

  };

   //
  // Confirm
  //
  $.fn.spf_confirm = function() {
    return this.each( function() {
      $(this).on('click', function( e ) {

        var confirm_text    = $(this).data('confirm') || window.spf_vars.i18n.confirm;
        var confirm_answer  = confirm( confirm_text );
        SP_WPCF.vars.is_confirm = true;

        if( !confirm_answer ) {
          e.preventDefault();
          SP_WPCF.vars.is_confirm = false;
          return false;
        }

      });
    });
  };

  $.fn.serializeObject = function(){

    var obj = {};

    $.each( this.serializeArray(), function(i,o){
      var n = o.name,
        v = o.value;

        obj[n] = obj[n] === undefined ? v
          : $.isArray( obj[n] ) ? obj[n].concat( v )
          : [ obj[n], v ];
    });

    return obj;

  };

  //
  // Options Save
  //
  $.fn.spf_save = function() {
    return this.each( function() {

      var $this    = $(this),
          $buttons = $('.spf-save'),
          $panel   = $('.spf-options'),
          flooding = false,
          timeout;

      $this.on('click', function( e ) {

        if( !flooding ) {

          var $text  = $this.data('save'),
              $value = $this.val();

          $buttons.attr('value', $text);

          if( $this.hasClass('spf-save-ajax') ) {

            e.preventDefault();

            $panel.addClass('spf-saving');
            $buttons.prop('disabled', true);

            window.wp.ajax.post( 'spf_'+ $panel.data('unique') +'_ajax_save', {
              data: $('#spf-form').serializeJSONSP_WPCF()
            })
            .done( function( response ) {

              clearTimeout(timeout);

              var $result_success = $('.spf-form-success');

              $result_success.empty().append(response.notice).slideDown('fast', function() {
                timeout = setTimeout( function() {
                  $result_success.slideUp('fast');
                }, 2000);
              });

              // clear errors
              $('.spf-error').remove();

              var $append_errors = $('.spf-form-error');

              $append_errors.empty().hide();

              if( Object.keys( response.errors ).length ) {

                var error_icon = '<i class="spf-label-error spf-error">!</i>';

                $.each(response.errors, function( key, error_message ) {

                  var $field = $('[data-depend-id="'+ key +'"]'),
                      $link  = $('#spf-tab-link-'+ ($field.closest('.spf-section').index()+1)),
                      $tab   = $link.closest('.spf-tab-depth-0');

                  $field.closest('.spf-fieldset').append( '<p class="spf-text-error spf-error">'+ error_message +'</p>' );

                  if( !$link.find('.spf-error').length ) {
                    $link.append( error_icon );
                  }

                  if( !$tab.find('.spf-arrow .spf-error').length ) {
                    $tab.find('.spf-arrow').append( error_icon );
                  }

                  console.log(error_message);

                  $append_errors.append( '<div>'+ error_icon +' '+ error_message + '</div>' );

                });

                $append_errors.show();

              }

              $panel.removeClass('spf-saving');
              $buttons.prop('disabled', false).attr('value', $value);
              flooding = false;

            })
            .fail( function( response ) {
              alert( response.error );
            });

          }

        }

        flooding = true;

      });

    });
  };

  //
  // Taxonomy Framework
  //
  $.fn.spf_taxonomy = function() {
    return this.each( function() {

      var $this = $(this),
          $form = $this.parents('form');

      if( $form.attr('id') === 'addtag' ) {

        var $submit = $form.find('#submit'),
            $cloned = $this.find('.spf-field').spf_clone();

        $submit.on( 'click', function() {

          if( !$form.find('.form-required').hasClass('form-invalid') ) {

            $this.data('inited', false);

            $this.empty();

            $this.html( $cloned );

            $cloned = $cloned.spf_clone();

            $this.spf_reload_script();

          }

        });

      }

    });
  };

  //
  // Shortcode Framework
  //
  $.fn.spf_shortcode = function() {

    var base = this;

    base.shortcode_parse = function( serialize, key ) {

      var shortcode = '';

      $.each(serialize, function( shortcode_key, shortcode_values ) {

        key = ( key ) ? key : shortcode_key;

        shortcode += '[' + key;

        $.each(shortcode_values, function( shortcode_tag, shortcode_value ) {

          if( shortcode_tag === 'content' ) {

            shortcode += ']';
            shortcode += shortcode_value;
            shortcode += '[/'+ key +'';

          } else {

            shortcode += base.shortcode_tags( shortcode_tag, shortcode_value );

          }

        });

        shortcode += ']';

      });

      return shortcode;

    };

    base.shortcode_tags = function( shortcode_tag, shortcode_value ) {

      var shortcode = '';

      if( shortcode_value !== '' ) {

        if( typeof shortcode_value === 'object' && !$.isArray( shortcode_value ) ) {

          $.each(shortcode_value, function( sub_shortcode_tag, sub_shortcode_value ) {

            // sanitize spesific key/value
            switch( sub_shortcode_tag ) {

              case 'background-image':
                sub_shortcode_value = ( sub_shortcode_value.url  ) ? sub_shortcode_value.url : '';
              break;

            }

            if( sub_shortcode_value !== '' ) {
              shortcode += ' ' + sub_shortcode_tag.replace('-', '_') + '="' + sub_shortcode_value.toString() + '"';
            }

          });

        } else {

          shortcode += ' ' + shortcode_tag.replace('-', '_') + '="' + shortcode_value.toString() + '"';

        }

      }

      return shortcode;

    };

    base.insertAtChars = function( _this, currentValue ) {

      var obj = ( typeof _this[0].name !== 'undefined' ) ? _this[0] : _this;

      if( obj.value.length && typeof obj.selectionStart !== 'undefined' ) {
        obj.focus();
        return obj.value.substring( 0, obj.selectionStart ) + currentValue + obj.value.substring( obj.selectionEnd, obj.value.length );
      } else {
        obj.focus();
        return currentValue;
      }

    };

    base.send_to_editor = function( html, editor_id ) {

      var tinymce_editor;

      if( typeof tinymce !== 'undefined' ) {
        tinymce_editor = tinymce.get( editor_id );
      }

      if( tinymce_editor && !tinymce_editor.isHidden() ) {
        tinymce_editor.execCommand( 'mceInsertContent', false, html );
      } else {
        var $editor = $('#'+editor_id);
        $editor.val( base.insertAtChars( $editor, html ) ).trigger('change');
      }

    };

    return this.each( function() {

      var $modal   = $(this),
          $load    = $modal.find('.spf-modal-load'),
          $content = $modal.find('.spf-modal-content'),
          $insert  = $modal.find('.spf-modal-insert'),
          $loading = $modal.find('.spf-modal-loading'),
          $select  = $modal.find('select'),
          modal_id = $modal.data('modal-id'),
          nonce    = $modal.data('nonce'),
          editor_id,
          target_id,
          gutenberg_id,
          sc_key,
          sc_name,
          sc_view,
          sc_group,
          $cloned,
          $button;

      $(document).on('click', '.spf-shortcode-button[data-modal-id="'+ modal_id +'"]', function( e ) {

        e.preventDefault();

        $button      = $(this);
        editor_id    = $button.data('editor-id')    || false;
        target_id    = $button.data('target-id')    || false;
        gutenberg_id = $button.data('gutenberg-id') || false;

        $modal.show();

        // single usage trigger first shortcode
        if( $modal.hasClass('spf-shortcode-single') && sc_name === undefined ) {
          $select.trigger('change');
        }

      });

      $select.on( 'change', function() {

        var $option   = $(this);
        var $selected = $option.find(':selected');

        sc_key   = $option.val();
        sc_name  = $selected.data('shortcode');
        sc_view  = $selected.data('view') || 'normal';
        sc_group = $selected.data('group') || sc_name;

        $load.empty();

        if( sc_key ) {

          $loading.show();

          window.wp.ajax.post( 'spf-get-shortcode-'+ modal_id, {
            shortcode_key: sc_key,
            nonce: nonce
          })
          .done( function( response ) {

            $loading.hide();

            var $appended = $(response.content).appendTo($load);

            $insert.parent().removeClass('hidden');

            $cloned = $appended.find('.spf--repeat-shortcode').spf_clone();

            $appended.spf_reload_script();
            $appended.find('.spf-fields').spf_reload_script();

          });

        } else {

          $insert.parent().addClass('hidden');

        }

      });

      $insert.on('click', function( e ) {

        e.preventDefault();

        var shortcode = '';
        var serialize = $modal.find('.spf-field:not(.hidden)').find(':input').serializeObjectSP_WPCF();

        switch ( sc_view ) {

          case 'contents':
            var contentsObj = ( sc_name ) ? serialize[sc_name] : serialize;
            $.each(contentsObj, function( sc_key, sc_value ) {
              var sc_tag = ( sc_name ) ? sc_name : sc_key;
              shortcode += '['+ sc_tag +']'+ sc_value +'[/'+ sc_tag +']';
            });
          break;

          case 'group':

            shortcode += '[' + sc_name;
            $.each(serialize[sc_name], function( sc_key, sc_value ) {
              shortcode += base.shortcode_tags( sc_key, sc_value );
            });
            shortcode += ']';
            shortcode += base.shortcode_parse( serialize[sc_group], sc_group );
            shortcode += '[/' + sc_name + ']';

          break;

          case 'repeater':
            shortcode += base.shortcode_parse( serialize[sc_group], sc_group );
          break;

          default:
            shortcode += base.shortcode_parse( serialize );
          break;

        }

        if( gutenberg_id ) {

          var content = window.spf_gutenberg_props.attributes.hasOwnProperty('shortcode') ? window.spf_gutenberg_props.attributes.shortcode : '';
          window.spf_gutenberg_props.setAttributes({shortcode: content + shortcode});

        } else if( editor_id ) {

          base.send_to_editor( shortcode, editor_id );

        } else {

          var $textarea = (target_id) ? $(target_id) : $button.parent().find('textarea');
          $textarea.val( base.insertAtChars( $textarea, shortcode ) ).trigger('change');

        }

        $modal.hide();

      });

      $modal.on('click', '.spf--repeat-button', function( e ) {

        e.preventDefault();

        var $repeatable = $modal.find('.spf--repeatable');
        var $new_clone  = $cloned.spf_clone();
        var $remove_btn = $new_clone.find('.spf-repeat-remove');

        var $appended = $new_clone.appendTo( $repeatable );

        $new_clone.find('.spf-fields').spf_reload_script();

        SP_WPCF.helper.name_nested_replace( $modal.find('.spf--repeat-shortcode'), sc_group );

        $remove_btn.on('click', function() {

          $new_clone.remove();

          SP_WPCF.helper.name_nested_replace( $modal.find('.spf--repeat-shortcode'), sc_group );

        });

      });

      $modal.on('click', '.spf-modal-close, .spf-modal-overlay', function() {
        $modal.hide();
      });

    });
  };

  //
  // Helper Checkbox Checker
  //
  $.fn.spf_checkbox = function() {
    return this.each( function() {

      var $this     = $(this),
          $input    = $this.find('.spf--input'),
          $checkbox = $this.find('.spf--checkbox');

      $checkbox.on('click', function() {
        $input.val( Number( $checkbox.prop('checked') ) ).trigger('change');
      });

    });
  };

  //
  // Field: wp_editor
  //
  $.fn.spf_field_wp_editor = function() {
    return this.each( function() {

      if( typeof window.wp.editor === 'undefined' || typeof window.tinyMCEPreInit === 'undefined' || typeof window.tinyMCEPreInit.mceInit.spf_wp_editor === 'undefined' ) {
        return;
      }

      var $this     = $(this),
          $editor   = $this.find('.spf-wp-editor'),
          $textarea = $this.find('textarea');

      // If there is wp-editor remove it for avoid dupliated wp-editor conflicts.
      var $has_wp_editor = $this.find('.wp-editor-wrap').length || $this.find('.mce-container').length;

      if( $has_wp_editor ) {
        $editor.empty();
        $editor.append($textarea);
        $textarea.css('display', '');
      }

      // Generate a unique id
      var uid = SP_WPCF.helper.uid('spf-editor-');

      $textarea.attr('id', uid);

      // Get default editor settings
      var default_editor_settings = {
        tinymce: window.tinyMCEPreInit.mceInit.spf_wp_editor,
        quicktags: window.tinyMCEPreInit.qtInit.spf_wp_editor
      };

      // Get default editor settings
      var field_editor_settings = $editor.data('editor-settings');

      // Add on change event handle
      var editor_on_change = function( editor ) {
        editor.on('change', SP_WPCF.helper.debounce( function() {
          editor.save();
          $textarea.trigger('change');
        }, 250 ) );
      };

      // Extend editor selector and on change event handler
      default_editor_settings.tinymce = $.extend( {}, default_editor_settings.tinymce, { selector: '#'+ uid, setup: editor_on_change } );

      // Override editor tinymce settings
      if( field_editor_settings.tinymce === false ) {
        default_editor_settings.tinymce = false;
        $editor.addClass('spf-no-tinymce');
      }

      // Override editor quicktags settings
      if( field_editor_settings.quicktags === false ) {
        default_editor_settings.quicktags = false;
        $editor.addClass('spf-no-quicktags');
      }

      // Wait until :visible
      var interval = setInterval(function () {
        if( $this.is(':visible') ) {
          window.wp.editor.initialize(uid, default_editor_settings);
          clearInterval(interval);
        }
      });

      // Add Media buttons
      if( field_editor_settings.media_buttons && window.spf_media_buttons ) {

        var $editor_buttons = $editor.find('.wp-media-buttons');

        if( $editor_buttons.length ) {

          $editor_buttons.find('.spf-shortcode-button').data('editor-id', uid);

        } else {

          var $media_buttons = $(window.spf_media_buttons);

          $media_buttons.find('.spf-shortcode-button').data('editor-id', uid);

          $editor.prepend( $media_buttons );

        }

      }

    });

  };

  //
  // Siblings
  //
  $.fn.spf_siblings = function() {
    return this.each( function() {

      var $this     = $(this),
          $siblings = $this.find('.spf--sibling'),
          multiple  = $this.data('multiple') || false;

      $siblings.on('click', function() {

        var $sibling = $(this);

        if( multiple ) {

          if( $sibling.hasClass('spf--active') ) {
            $sibling.removeClass('spf--active');
            $sibling.find('input').prop('checked', false).trigger('change');
          } else {
            $sibling.addClass('spf--active');
            $sibling.find('input').prop('checked', true).trigger('change');
          }

        } else {

          $this.find('input').prop('checked', false);
          $sibling.find('input').prop('checked', true).trigger('change');
          $sibling.addClass('spf--active').siblings().removeClass('spf--active');

        }

      });

    });
  };

  //
  // WP Color Picker
  //
  if( typeof Color === 'function' ) {

    Color.fn.toString = function() {

      if( this._alpha < 1 ) {
        return this.toCSS('rgba', this._alpha).replace(/\s+/g, '');
      }

      var hex = parseInt( this._color, 10 ).toString( 16 );

      if( this.error ) { return ''; }

      if( hex.length < 6 ) {
        for (var i = 6 - hex.length - 1; i >= 0; i--) {
          hex = '0' + hex;
        }
      }

      return '#' + hex;

    };

  }

  SP_WPCF.funcs.parse_color = function( color ) {

    var value = color.replace(/\s+/g, ''),
        trans = ( value.indexOf('rgba') !== -1 ) ? parseFloat( value.replace(/^.*,(.+)\)/, '$1') * 100 ) : 100,
        rgba  = ( trans < 100 ) ? true : false;

    return { value: value, transparent: trans, rgba: rgba };

  };

  $.fn.spf_color = function() {
    return this.each( function() {

      var $input        = $(this),
          picker_color  = SP_WPCF.funcs.parse_color( $input.val() ),
          palette_color = window.spf_vars.color_palette.length ? window.spf_vars.color_palette : true,
          $container;

      // Destroy and Reinit
      if( $input.hasClass('wp-color-picker') ) {
        $input.closest('.wp-picker-container').after($input).remove();
      }

      $input.wpColorPicker({
        palettes: palette_color,
        change: function( event, ui ) {

          var ui_color_value = ui.color.toString();

          $container.removeClass('spf--transparent-active');
          $container.find('.spf--transparent-offset').css('background-color', ui_color_value);
          $input.val(ui_color_value).trigger('change');

        },
        create: function() {

          $container = $input.closest('.wp-picker-container');

          var a8cIris = $input.data('a8cIris'),
              $transparent_wrap = $('<div class="spf--transparent-wrap">' +
                                '<div class="spf--transparent-slider"></div>' +
                                '<div class="spf--transparent-offset"></div>' +
                                '<div class="spf--transparent-text"></div>' +
                                '<div class="spf--transparent-button button button-small">transparent</div>' +
                                '</div>').appendTo( $container.find('.wp-picker-holder') ),
              $transparent_slider = $transparent_wrap.find('.spf--transparent-slider'),
              $transparent_text   = $transparent_wrap.find('.spf--transparent-text'),
              $transparent_offset = $transparent_wrap.find('.spf--transparent-offset'),
              $transparent_button = $transparent_wrap.find('.spf--transparent-button');

          if( $input.val() === 'transparent' ) {
            $container.addClass('spf--transparent-active');
          }

          $transparent_button.on('click', function() {
            if( $input.val() !== 'transparent' ) {
              $input.val('transparent').trigger('change').removeClass('iris-error');
              $container.addClass('spf--transparent-active');
            } else {
              $input.val( a8cIris._color.toString() ).trigger('change');
              $container.removeClass('spf--transparent-active');
            }
          });

          $transparent_slider.slider({
            value: picker_color.transparent,
            step: 1,
            min: 0,
            max: 100,
            slide: function( event, ui ) {

              var slide_value = parseFloat( ui.value / 100 );
              a8cIris._color._alpha = slide_value;
              $input.wpColorPicker( 'color', a8cIris._color.toString() );
              $transparent_text.text( ( slide_value === 1 || slide_value === 0 ? '' : slide_value ) );

            },
            create: function() {

              var slide_value = parseFloat( picker_color.transparent / 100 ),
                  text_value  = slide_value < 1 ? slide_value : '';

              $transparent_text.text(text_value);
              $transparent_offset.css('background-color', picker_color.value);

              $container.on('click', '.wp-picker-clear', function() {

                a8cIris._color._alpha = 1;
                $transparent_text.text('');
                $transparent_slider.slider('option', 'value', 100);
                $container.removeClass('spf--transparent-active');
                $input.trigger('change');

              });

              $container.on('click', '.wp-picker-default', function() {

                var default_color = SP_WPCF.funcs.parse_color( $input.data('default-color') ),
                    default_value = parseFloat( default_color.transparent / 100 ),
                    default_text  = default_value < 1 ? default_value : '';

                a8cIris._color._alpha = default_value;
                $transparent_text.text(default_text);
                $transparent_slider.slider('option', 'value', default_color.transparent);

              });

              $container.on('click', '.wp-color-result', function() {
                $transparent_wrap.toggle();
              });

              $('body').on( 'click.wpcolorpicker', function() {
                $transparent_wrap.hide();
              });

            }
          });
        }
      });

    });
  };

  //
  // ChosenJS
  //
  $.fn.spf_chosen = function() {
    return this.each( function() {

      var $this       = $(this),
          $inited     = $this.parent().find('.chosen-container'),
          is_multi    = $this.attr('multiple') || false,
          set_width   = is_multi ? '100%' : 'auto',
          set_options = $.extend({
            allow_single_deselect: true,
            disable_search_threshold: 15,
            width: set_width
          }, $this.data());

      if( $inited.length ) {
        $inited.remove();
      }

      $this.chosen(set_options);

    });
  };

  //
  // Number (only allow numeric inputs)
  //
  $.fn.spf_number = function() {
    return this.each( function() {

      $(this).on('keypress', function( e ) {

        if( e.keyCode !== 0 && e.keyCode !== 8 && e.keyCode !== 45 && e.keyCode !== 46 && ( e.keyCode < 48 || e.keyCode > 57 ) ) {
          return false;
        }

      });

    });
  };

  //
  // Help Tooltip
  //
  $.fn.spf_help = function() {
    return this.each( function() {

      var $this = $(this),
          $tooltip,
          offset_left;

      $this.on({
        mouseenter: function() {

          $tooltip = $( '<div class="spf-tooltip"></div>' ).html( $this.find('.spf-help-text').html() ).appendTo('body');
          offset_left = ( SP_WPCF.vars.is_rtl ) ? ( $this.offset().left + 24 ) : ( $this.offset().left - $tooltip.outerWidth() );

          $tooltip.css({
            top: $this.offset().top - ( ( $tooltip.outerHeight() / 2 ) - 14 ),
            left: offset_left,
          });

        },
        mouseleave: function() {

          if( $tooltip !== undefined ) {
            $tooltip.remove();
          }

        }

      });

    });
  };

  //
  // Customize Refresh
  //
  $.fn.spf_customizer_refresh = function() {
    return this.each( function() {

      var $this    = $(this),
          $complex = $this.closest('.spf-customize-complex');

      if( $complex.length ) {

        var $input  = $complex.find(':input'),
            $unique = $complex.data('unique-id'),
            $option = $complex.data('option-id'),
            obj     = $input.serializeObjectSP_WPCF(),
            data    = ( !$.isEmptyObject(obj) ) ? obj[$unique][$option] : '',
            control = wp.customize.control($unique +'['+ $option +']');

        // clear the value to force refresh.
        control.setting._value = null;

        control.setting.set( data );

      } else {

        $this.find(':input').first().trigger('change');

      }

      $(document).trigger('spf-customizer-refresh', $this);

    });
  };

  //
  // Customize Listen Form Elements
  //
  $.fn.spf_customizer_listen = function( options ) {

    var settings = $.extend({
      closest: false,
    }, options );

    return this.each( function() {

      if( window.wp.customize === undefined ) { return; }

      var $this     = ( settings.closest ) ? $(this).closest('.spf-customize-complex') : $(this),
          $input    = $this.find(':input'),
          unique_id = $this.data('unique-id'),
          option_id = $this.data('option-id');

      if( unique_id === undefined ) { return; }

      $input.on('change keyup', SP_WPCF.helper.debounce( function() {

        var obj = $this.find(':input').serializeObjectSP_WPCF();

        if( !$.isEmptyObject(obj) && obj[unique_id] ) {

          window.wp.customize.control( unique_id +'['+ option_id +']' ).setting.set( obj[unique_id][option_id] );

        }

      }, 250 ) );

    });
  };

  //
  // Customizer Listener for Reload JS
  //
  $(document).on('expanded', '.control-section-spf', function() {

    var $this = $(this);

    if( $this.hasClass('open') && !$this.data('inited') ) {
      $this.spf_dependency();
      $this.find('.spf-customize-field').spf_reload_script({dependency: false});
      $this.find('.spf-customize-complex').spf_customizer_listen();
      $this.data('inited', true);
    }

  });

  //
  // Window on resize
  //
  SP_WPCF.vars.$window.on('resize spf.resize', SP_WPCF.helper.debounce( function( event ) {

    var window_width = navigator.userAgent.indexOf('AppleWebKit/') > -1 ? SP_WPCF.vars.$window.width() : window.innerWidth;

    if( window_width <= 782 && !SP_WPCF.vars.onloaded ) {
      $('.spf-section').spf_reload_script();
      SP_WPCF.vars.onloaded  = true;
    }

  }, 200)).trigger('spf.resize');

  //
  // Widgets Framework
  //
  $.fn.spf_widgets = function() {
    if( this.length ) {

      $(document).on('widget-added widget-updated', function( event, $widget ) {
        $widget.find('.spf-fields').spf_reload_script();
      });

      $('.widgets-sortables, .control-section-sidebar').on('sortstop', function( event, ui ) {
        ui.item.find('.spf-fields').spf_reload_script_retry();
      });

      $(document).on('click', '.widget-top', function( event ) {
        $(this).parent().find('.spf-fields').spf_reload_script();
      });

    }
  };

  //
  // Retry Plugins
  //
  $.fn.spf_reload_script_retry = function() {
    return this.each( function() {

      var $this = $(this);

      if( $this.data('inited') ) {
        $this.children('.spf-field-wp_editor').spf_field_wp_editor();
      }

    });
  };

  //
  // Reload Plugins
  //
  $.fn.spf_reload_script = function( options ) {

    var settings = $.extend({
      dependency: true,
    }, options );

    return this.each( function() {

      var $this = $(this);

      // Avoid for conflicts
      if( !$this.data('inited') ) {

        // Field plugins
        $this.children('.spf-field-accordion').spf_field_accordion();
    //    $this.children('.spf-field-backup').spf_field_backup();
        // $this.children('.spf-field-background_adv').spf_field_background();
        // $this.children('.spf-field-background').spf_field_background();
        $this.children('.spf-field-code_editor').spf_field_code_editor();
      //  $this.children('.spf-field-date').spf_field_date();
        $this.children('.spf-field-fieldset').spf_field_fieldset();
        $this.children('.spf-field-gallery').spf_field_gallery();
        $this.children('.spf-field-group').spf_field_group();
      //  $this.children('.spf-field-icon').spf_field_icon();
        $this.children('.spf-field-media').spf_field_media();
      //  $this.children('.spf-field-repeater').spf_field_repeater();
      //  $this.children('.spf-field-slider').spf_field_slider();
        // $this.children('.spf-field-sortable').spf_field_sortable();
        // $this.children('.spf-field-sorter').spf_field_sorter();
        $this.children('.spf-field-spinner').spf_field_spinner();
        $this.children('.spf-field-switcher').spf_field_switcher();
      //  $this.children('.spf-field-tabbed').spf_field_tabbed();
        $this.children('.spf-field-typography').spf_field_typography();
        $this.children('.spf-field-upload').spf_field_upload();
        $this.children('.spf-field-wp_editor').spf_field_wp_editor();

        // Field colors
        $this.children('.spf-field-border').find('.spf-color').spf_color();
        $this.children('.spf-field-dimensions_advanced').find('.spf-color').spf_color();
        $this.children('.spf-field-background_adv').find('.spf-color').spf_color();
        $this.children('.spf-field-background').find('.spf-color').spf_color();
        $this.children('.spf-field-color').find('.spf-color').spf_color();
        $this.children('.spf-field-color_group').find('.spf-color').spf_color();
        $this.children('.spf-field-link_color').find('.spf-color').spf_color();
        $this.children('.spf-field-typography').find('.spf-color').spf_color();

        // Field allows only number
        $this.children('.spf-field-dimensions').find('.spf-number').spf_number();
        $this.children('.spf-field-slider').find('.spf-number').spf_number();
        $this.children('.spf-field-spacing').find('.spf-number').spf_number();
        $this.children('.spf-field-column').find('.spf-number').spf_number();
        $this.children('.spf-field-dimensions_advanced').find('.spf-number').spf_number();
        $this.children('.spf-field-spinner').find('.spf-number').spf_number();
        $this.children('.spf-field-typography').find('.spf-number').spf_number();

        // Field chosenjs
        $this.children('.spf-field-select').find('.spf-chosen').spf_chosen();

        // Field Checkbox
        $this.children('.spf-field-checkbox').find('.spf-checkbox').spf_checkbox();

        // Field Siblings
        $this.children('.spf-field-button_set').find('.spf-siblings').spf_siblings();
        $this.children('.spf-field-image_select').find('.spf-siblings').spf_siblings();
        $this.children('.spf-field-carousel_type').find('.spf-siblings').spf_siblings();
        $this.children('.spf-field-palette').find('.spf-siblings').spf_siblings();

        // Help Tooptip
        $this.children('.spf-field').find('.spf-help').spf_help();

        if( settings.dependency ) {
          $this.spf_dependency();
        }

        $this.data('inited', true);

        $(document).trigger('spf-reload-script', $this);

      }

    });
  };

  //
  // Document ready and run scripts.
  //
  $(document).ready( function() {

    $('.spf-save').spf_save();
    $('.spf-confirm').spf_confirm();
    $('.spf-nav-options').spf_nav_options();
    $('.spf-nav-metabox').spf_nav_metabox();
    $('.spf-expand-all').spf_expand_all();
    $('.spf-search').spf_search();
    $('.spf-sticky-header').spf_sticky();
    $('.spf-taxonomy').spf_taxonomy();
    $('.spf-shortcode').spf_shortcode();
    $('.spf-page-templates').spf_page_templates();
    $('.spf-post-formats').spf_post_formats();
    $('.spf-onload').spf_reload_script();
    $('.widget').spf_widgets();

  });


  // ======================================================
  // Post 
  // ------------------------------------------------------
  // Trigger taxonomy list when post type is selected.
  $('.sp_wpcp_post_type select').change(function (event) {
    event.preventDefault();
    var data = {
      action: 'wpcp_get_taxonomies',
      wpcf_post_type: $(this).val(),
    }
    $.post(ajaxurl, data, function (resp) {
      $('.sp_wpcp_post_taxonomy select').html(resp);
      $(".sp_wpcp_post_taxonomy select").trigger("chosen:updated");
    });
  });

  // Update terms list on the change of post taxonomy.
  $('.sp_wpcp_post_taxonomy select').change(function (event) {
    event.preventDefault();
    var data = {
      action: 'wpcp_get_terms', // Callback function.
      wpcf_post_taxonomy: $(this).val(), 
    }
    $.post(ajaxurl, data, function (resp) {
      $('.sp_wpcp_taxonomy_terms select').html(resp);
      $(".sp_wpcp_taxonomy_terms select").trigger("chosen:updated");

    });
  });


  // Populate specific post list in the drop-down.
  $('.sp_wpcp_post_type select').change(function (event) {
    event.preventDefault();
    var data = {
      action: 'wpcp_get_posts', // Callback function.
      wpcf_post_type: $(this).val(), // Callback function's value.
    }
    $.post(ajaxurl, data, function (resp) {
      $('.sp_wpcp_specific_posts select').html(resp);
      $(".sp_wpcp_specific_posts select").trigger("chosen:updated");
    });
  });


})( jQuery, window, document );
