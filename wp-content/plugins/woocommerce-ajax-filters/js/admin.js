(function ($) {
    $(document).ready(function () {

        $(document).on('change', '.berocket_aapf_widget_admin_attribute_select', function () {
            $parent = $(this).parents('form');
            if ($(this).val() == 'price') {
                $('.berocket_aapf_widget_admin_type_select', $parent).html('<option value="slider">Slider</option>');
                $('.berocket_aapf_widget_admin_operator_select', $parent).parent().parent().hide(0);
                $('.berocket_aapf_widget_admin_price_attribute', $parent).show(0);
            } else {
                $('.berocket_aapf_widget_admin_type_select', $parent).html('<option value="checkbox">Checkbox</option><option value="radio">Radio</option><option value="select">Select</option>');
                $('.berocket_aapf_widget_admin_operator_select', $parent).parent().parent().show(0);
                $('.berocket_aapf_widget_admin_price_attribute', $parent).hide(0);
            }
        });

        $(document).on('change', '.berocket_aapf_widget_admin_type_select', function () {
            $parent = $(this).parents('form');
            if ($(this).val() == 'slider') {
                $('.berocket_aapf_widget_admin_operator_select', $parent).parent().parent().hide(0);
            } else {
                $('.berocket_aapf_widget_admin_operator_select', $parent).parent().parent().show(0);
            }
        });

        $(document).on('click', '.berocket_aapf_advanced_settings_pointer', function (event) {
            event.preventDefault();
            $(this).parent().next().slideDown(300);
            $(this).parent().slideUp(200);
        });

        $('.colorpicker_field').each(function (i,o){
            $(o).css('backgroundColor', '#'+$(o).data('color')).next().val($(o).data('color'));
            $(o).colpick({
                layout: 'hex',
                submit: 0,
                color: '#'+$(o).data('color'),
                onChange: function(hsb,hex,rgb,el,bySetColor) {
                    $(el).css('backgroundColor', '#'+hex).next().val(hex);
                }
            })
        });

        $('.filter_settings_tabs').on('click', 'a', function (event) {
            event.preventDefault();
            $('#br_opened_tab').val( $(this).attr('href').replace('#', '') );
            $id = $(this).attr('href');
            $('.tab-item.current').removeClass('current');
            $($id).addClass('current');

            $('.filter_settings_tabs .nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');
        });

        $(document).on('change', '.berocket_aapf_widget_admin_widget_type_select', function () {
            $parent = $(this).parents('form');
            if ( $(this).val() == 'filter' ) {
                $('.berocket_aapf_admin_filter_widget_content', $parent).show();
            } else if( $(this).val() == 'update_button' ) {
                $('.berocket_aapf_admin_filter_widget_content', $parent).hide();
            } else if( $(this).val() == 'selected_area' ) {
                $('.berocket_aapf_admin_filter_widget_content', $parent).hide();
            }
        });
    })
})(jQuery);