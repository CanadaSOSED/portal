;(function ($) {
    'use strict'
    jQuery('body').find('.wpcp-carousel-section.wpcp-preloader').each(function () {
          var carousel_id         = $(this).attr('id'),
              parents_class       = jQuery('#' + carousel_id).parent('.wpcp-carousel-wrapper'),
              parents_siblings_id = parents_class.find('.wpcp-carousel-preloader').attr('id');
        jQuery(window).load(function () {
          jQuery('#' + parents_siblings_id).animate({ opacity: 0 }, 600).remove();
          jQuery('#' + carousel_id).animate({ opacity: 1 }, 600)
        })
      })
  })(jQuery)