/**
 *
 * -----------------------------------------------------------
 *
 * ShapedPlugin Framework
 * A Lightweight and easy-to-use WordPress Options Framework
 *
 * Copyright 2017 www.shapedplugin.com
 *
 * -----------------------------------------------------------
 *
 */
;
(function ($, window, document, undefined) {
  'use strict';

  $.SPFRAMEWORK = $.SPFRAMEWORK || {};

  // caching selector
  var $sp_body = $('body');

  // caching variables
  var sp_is_rtl = $sp_body.hasClass('rtl');

  // ======================================================
  // SPFRAMEWORK TAB NAVIGATION
  // ------------------------------------------------------
  $.fn.SPFRAMEWORK_TAB_NAVIGATION = function () {
    return this.each(function () {

      var $this = $(this),
        $nav = $this.find('.sp-nav'),
        $reset = $this.find('.sp-reset'),
        $expand = $this.find('.sp-expand-all');

      $nav.find('ul:first a').on('click', function (e) {

        e.preventDefault();

        var $el = $(this),
          $next = $el.next(),
          $target = $el.data('section');

        if ($next.is('ul')) {

          $next.slideToggle('fast');
          $el.closest('li').toggleClass('sp-tab-active');

        } else {

          $('#sp-tab-' + $target).fadeIn('fast').siblings().hide();
          $nav.find('a').removeClass('sp-section-active');
          $el.addClass('sp-section-active');
          $reset.val($target);

        }

      });

      $expand.on('click', function (e) {
        e.preventDefault();
        $this.find('.sp-body').toggleClass('sp-show-all');
        $(this).find('.fa').toggleClass('fa-eye-slash').toggleClass('fa-eye');
      });

    });
  };
  // ======================================================

  // ======================================================
  // SPFRAMEWORK DEPENDENCY
  // ------------------------------------------------------
  $.SPFRAMEWORK.DEPENDENCY = function (el, param) {

    // Access to jQuery and DOM versions of element
    var base = this;
    base.$el = $(el);
    base.el = el;

    base.init = function () {

      base.ruleset = $.deps.createRuleset();

      // required for shortcode attrs
      var cfg = {
        show: function (el) {
          el.removeClass('hidden');
        },
        hide: function (el) {
          el.addClass('hidden');
        },
        log: false,
        checkTargets: false
      };

      if (param !== undefined) {
        base.depSub();
      } else {
        base.depRoot();
      }

      $.deps.enable(base.$el, base.ruleset, cfg);

    };

    base.depRoot = function () {

      base.$el.each(function () {

        $(this).find('[data-controller]').each(function () {

          var $this = $(this),
            _controller = $this.data('controller').split('|'),
            _condition = $this.data('condition').split('|'),
            _value = $this.data('value').toString().split('|'),
            _rules = base.ruleset;

          $.each(_controller, function (index, element) {

            var value = _value[index] || '',
              condition = _condition[index] || _condition[0];

            _rules = _rules.createRule('[data-depend-id="' + element + '"]', condition, value);
            _rules.include($this);

          });

        });

      });

    };

    base.depSub = function () {

      base.$el.each(function () {

        $(this).find('[data-sub-controller]').each(function () {

          var $this = $(this),
            _controller = $this.data('sub-controller').split('|'),
            _condition = $this.data('sub-condition').split('|'),
            _value = $this.data('sub-value').toString().split('|'),
            _rules = base.ruleset;

          $.each(_controller, function (index, element) {

            var value = _value[index] || '',
              condition = _condition[index] || _condition[0];

            _rules = _rules.createRule('[data-sub-depend-id="' + element + '"]', condition, value);
            _rules.include($this);

          });

        });

      });

    };


    base.init();
  };

  $.fn.SPFRAMEWORK_DEPENDENCY = function (param) {
    return this.each(function () {
      new $.SPFRAMEWORK.DEPENDENCY(this, param);
    });
  };
  // ======================================================

  // ======================================================
  // SPFRAMEWORK CHOSEN
  // ------------------------------------------------------
  $.fn.SPFRAMEWORK_CHOSEN = function () {
    return this.each(function () {
      $(this).chosen({
        allow_single_deselect: true,
        disable_search_threshold: 15,
        width: parseFloat($(this).actual('width') + 25) + 'px'
      });
    });
  };
  // ======================================================

  // ======================================================

  // ======================================================
  // SPFRAMEWORK BUTTON SET -- SHAMIM
  // ------------------------------------------------------
  $.fn.SPFRAMEWORK_BUTTON_SET = function () {
    return this.each(function () {

      $(this).find('label').on('click', function () {
        $(this).siblings().find('input').prop('checked', false);
      });

    });
  };
  // ======================================================

  // ======================================================
  // SPFRAMEWORK CAROUSEL TYPE -- SHAMIM
  // ------------------------------------------------------
  $.fn.SPFRAMEWORK_CAROUSEL_TYPE = function () {
    return this.each(function () {

      $(this).find('label').on('click', function () {
        $(this).siblings().find('input').prop('checked', false);
      });

    });
  };
  // ======================================================

  // ======================================================
  // SPFRAMEWORK IMAGE GALLERY
  // ------------------------------------------------------
  $.fn.SPFRAMEWORK_IMAGE_GALLERY = function () {
    return this.each(function () {
      var $this = $(this),
        $edit = $this.find('.sp-edit'),
        $remove = $this.find('.sp-remove'),
        // $list   = $this.find('ul'),
        $list = $this.find('ul.sp-gallery-images'),
        $input = $this.find('input'),
        $img = $this.find('img'),
        wp_media_frame,
        wp_media_click;

      $this.on('click', '.sp-add, .sp-edit', function (e) {

        var $el = $(this),
          what = ($el.hasClass('sp-edit')) ? 'edit' : 'add',
          state = (what === 'edit') ? 'gallery-edit' : 'gallery-library';

        e.preventDefault();

        // Check if the `wp.media.gallery` API exists.
        if (typeof wp === 'undefined' || !wp.media || !wp.media.gallery) {
          return;
        }

        // If the media frame already exists, reopen it.
        if (wp_media_frame) {
          wp_media_frame.open();
          wp_media_frame.setState(state);
          return;
        }

        // Create the media frame.
        wp_media_frame = wp.media({
          library: {
            type: 'image'
          },
          frame: 'post',
          state: 'gallery',
          multiple: true
        });

        // Open the media frame.
        wp_media_frame.on('open', function () {

          var ids = $input.val();

          if (ids) {

            var get_array = ids.split(',');
            var library = wp_media_frame.state('gallery-edit').get('library');

            wp_media_frame.setState(state);

            get_array.forEach(function (id) {
              var attachment = wp.media.attachment(id);
              library.add(attachment ? [attachment] : []);
            });

          }
        });

        // When an image is selected, run a callback.
        wp_media_frame.on('update', function () {

          var inner = '';
          var ids = [];
          var images = wp_media_frame.state().get('library');

          images.each(function (attachment) {

            var attributes = attachment.attributes;
            var thumbnail = (typeof attributes.sizes.thumbnail !== 'undefined') ? attributes.sizes.thumbnail.url : attributes.url;

            inner += '<li><img src="' + thumbnail + '"></li>';
            ids.push(attributes.id);

          });

          $input.val(ids).trigger('change');
          $list.html('').append(inner);
          $remove.removeClass('hidden');
          $edit.removeClass('hidden');

        });

        // Finally, open the modal.
        wp_media_frame.open();
        wp_media_click = what;

      });

      // Remove image
      $remove.on('click', function (e) {
        e.preventDefault();
        $list.html('');
        $input.val('').trigger('change');
        $remove.addClass('hidden');
        $edit.addClass('hidden');
      });

    });

  };
  // ======================================================


  // ======================================================
  // SPFRAMEWORK RESET CONFIRM
  // ------------------------------------------------------
  $.fn.SPFRAMEWORK_CONFIRM = function () {
    return this.each(function () {
      $(this).on('click', function (e) {
        if (!confirm('Are you sure?')) {
          e.preventDefault();
        }
      });
    });
  };
  // ======================================================

  // ======================================================
  // SPFRAMEWORK SAVE OPTIONS
  // ------------------------------------------------------
  $.fn.SPFRAMEWORK_SAVE = function () {
    return this.each(function () {

      var $this = $(this),
        $text = $this.data('save'),
        $value = $this.val(),
        $ajax = $('#sp-save-ajax');

      $(document).on('keydown', function (event) {
        if (event.ctrlKey || event.metaKey) {
          if (String.fromCharCode(event.which).toLowerCase() === 's') {
            event.preventDefault();
            $this.trigger('click');
          }
        }
      });

      $this.on('click', function (e) {

        if ($ajax.length) {

          if (typeof tinyMCE === 'object') {
            tinyMCE.triggerSave();
          }

          $this.prop('disabled', true).attr('value', $text);

          var serializedOptions = $('#sp_framework_form').serialize();

          $.post('options.php', serializedOptions).error(function () {
            alert('Error, Please try again.');
          }).success(function () {
            $this.prop('disabled', false).attr('value', $value);
            $ajax.hide().fadeIn().delay(250).fadeOut();
          });

          e.preventDefault();

        } else {

          $this.addClass('disabled').attr('value', $text);

        }

      });

    });
  };
  // ======================================================

  // ======================================================
  // SPFRAMEWORK UI DIALOG OVERLAY HELPER
  // ------------------------------------------------------
  if (typeof $.widget !== 'undefined' && typeof $.ui !== 'undefined' && typeof $.ui.dialog !== 'undefined') {
    $.widget('ui.dialog', $.ui.dialog, {
      _createOverlay: function () {
        this._super();
        if (!this.options.modal) {
          return;
        }
        this._on(this.overlay, {
          click: 'close'
        });
      }
    });
  }

  // ======================================================
  // SPFRAMEWORK COLORPICKER
  // ------------------------------------------------------
  if (typeof Color === 'function') {

    // adding alpha support for Automattic Color.js toString function.
    Color.fn.toString = function () {

      // check for alpha
      if (this._alpha < 1) {
        return this.toCSS('rgba', this._alpha).replace(/\s+/g, '');
      }

      var hex = parseInt(this._color, 10).toString(16);

      if (this.error) {
        return '';
      }

      // maybe left pad it
      if (hex.length < 6) {
        for (var i = 6 - hex.length - 1; i >= 0; i--) {
          hex = '0' + hex;
        }
      }

      return '#' + hex;

    };

  }

  $.SPFRAMEWORK.PARSE_COLOR_VALUE = function (val) {

    var value = val.replace(/\s+/g, ''),
      alpha = (value.indexOf('rgba') !== -1) ? parseFloat(value.replace(/^.*,(.+)\)/, '$1') * 100) : 100,
      rgba = (alpha < 100) ? true : false;

    return {
      value: value,
      alpha: alpha,
      rgba: rgba
    };

  };

  $.fn.SPFRAMEWORK_COLORPICKER = function () {

    return this.each(function () {

      var $this = $(this);

      // check for rgba enabled/disable
      if ($this.data('rgba') !== false) {

        // parse value
        var picker = $.SPFRAMEWORK.PARSE_COLOR_VALUE($this.val());

        // wpColorPicker core
        $this.wpColorPicker({

          // wpColorPicker: clear
          clear: function () {
            $this.trigger('keyup');
          },

          // wpColorPicker: change
          change: function (event, ui) {

            var ui_color_value = ui.color.toString();

            // update checkerboard background color
            $this.closest('.wp-picker-container').find('.sp-alpha-slider-offset').css('background-color', ui_color_value);
            $this.val(ui_color_value).trigger('change');

          },

          // wpColorPicker: create
          create: function () {

            // set variables for alpha slider
            var a8cIris = $this.data('a8cIris'),
              $container = $this.closest('.wp-picker-container'),

              // appending alpha wrapper
              $alpha_wrap = $('<div class="sp-alpha-wrap">' +
                '<div class="sp-alpha-slider"></div>' +
                '<div class="sp-alpha-slider-offset"></div>' +
                '<div class="sp-alpha-text"></div>' +
                '</div>').appendTo($container.find('.wp-picker-holder')),

              $alpha_slider = $alpha_wrap.find('.sp-alpha-slider'),
              $alpha_text = $alpha_wrap.find('.sp-alpha-text'),
              $alpha_offset = $alpha_wrap.find('.sp-alpha-slider-offset');

            // alpha slider
            $alpha_slider.slider({

              // slider: slide
              slide: function (event, ui) {

                var slide_value = parseFloat(ui.value / 100);

                // update iris data alpha && wpColorPicker color option && alpha text
                a8cIris._color._alpha = slide_value;
                $this.wpColorPicker('color', a8cIris._color.toString());
                $alpha_text.text((slide_value < 1 ? slide_value : ''));

              },

              // slider: create
              create: function () {

                var slide_value = parseFloat(picker.alpha / 100),
                  alpha_text_value = slide_value < 1 ? slide_value : '';

                // update alpha text && checkerboard background color
                $alpha_text.text(alpha_text_value);
                $alpha_offset.css('background-color', picker.value);

                // wpColorPicker clear for update iris data alpha && alpha text && slider color option
                $container.on('click', '.wp-picker-clear', function () {

                  a8cIris._color._alpha = 1;
                  $alpha_text.text('');
                  $alpha_slider.slider('option', 'value', 100).trigger('slide');

                });

                // wpColorPicker default button for update iris data alpha && alpha text && slider color option
                $container.on('click', '.wp-picker-default', function () {

                  var default_picker = $.SPFRAMEWORK.PARSE_COLOR_VALUE($this.data('default-color')),
                    default_value = parseFloat(default_picker.alpha / 100),
                    default_text = default_value < 1 ? default_value : '';

                  a8cIris._color._alpha = default_value;
                  $alpha_text.text(default_text);
                  $alpha_slider.slider('option', 'value', default_picker.alpha).trigger('slide');

                });

                // show alpha wrapper on click color picker button
                $container.on('click', '.wp-color-result', function () {
                  $alpha_wrap.toggle();
                });

                // hide alpha wrapper on click body
                $sp_body.on('click.wpcolorpicker', function () {
                  $alpha_wrap.hide();
                });

              },

              // slider: options
              value: picker.alpha,
              step: 1,
              min: 1,
              max: 100

            });
          }

        });

      } else {

        // wpColorPicker default picker
        $this.wpColorPicker({
          clear: function () {
            $this.trigger('keyup');
          },
          change: function (event, ui) {
            $this.val(ui.color.toString()).trigger('change');
          }
        });

      }

    });

  };
  // ======================================================

  // ======================================================
  // ON WIDGET-ADDED RELOAD FRAMEWORK PLUGINS
  // ------------------------------------------------------
  $.SPFRAMEWORK.WIDGET_RELOAD_PLUGINS = function () {
    $(document).on('widget-added widget-updated', function (event, $widget) {
      $widget.SPFRAMEWORK_RELOAD_PLUGINS();
      $widget.SPFRAMEWORK_DEPENDENCY();
    });
  };

  // ======================================================
  // TOOLTIP HELPER
  // ------------------------------------------------------
  $.fn.SPFRAMEWORK_TOOLTIP = function () {
    return this.each(function () {
      var placement = (sp_is_rtl) ? 'right' : 'left';
      $(this).sptooltip({
        html: true,
        placement: placement,
        container: 'body'
      });
    });
  };

  // ======================================================
  // RELOAD FRAMEWORK PLUGINS
  // ------------------------------------------------------
  $.fn.SPFRAMEWORK_RELOAD_PLUGINS = function () {
    return this.each(function () {
      $('.chosen', this).SPFRAMEWORK_CHOSEN();
      $('.sp-field-button-set', this).SPFRAMEWORK_BUTTON_SET();
      $('.sp-field-carousel-type', this).SPFRAMEWORK_CAROUSEL_TYPE(); // 
      $('.sp-field-gallery', this).SPFRAMEWORK_IMAGE_GALLERY();
      $('.sp-field-color-picker', this).SPFRAMEWORK_COLORPICKER();
      $('.sp-help', this).SPFRAMEWORK_TOOLTIP();
    });
  };

  // ======================================================
  // JQUERY DOCUMENT READY
  // ------------------------------------------------------
  $(document).ready(function () {
    $('.sp-wpcp-framework').SPFRAMEWORK_TAB_NAVIGATION();
    $('.sp-reset-confirm, .sp-import-backup').SPFRAMEWORK_CONFIRM();
    $('body.post-type-sp_wp_carousel, .sp-taxonomy').SPFRAMEWORK_DEPENDENCY();
    $('.sp-save').SPFRAMEWORK_SAVE();
    $sp_body.SPFRAMEWORK_RELOAD_PLUGINS();
    $.SPFRAMEWORK.WIDGET_RELOAD_PLUGINS();
  });

 })(jQuery, window, document);