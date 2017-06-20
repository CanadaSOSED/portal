<p>
    <label><?php _e('Widget Type:', 'BeRocket_AJAX_domain') ?>
        <select id="<?php echo $this->get_field_id( 'widget_type' ); ?>" name="<?php echo $this->get_field_name( 'widget_type' ); ?>" class="berocket_aapf_widget_admin_widget_type_select">
            <option <?php if ($instance['widget_type'] == 'filter' or ! $instance['widget_type']) echo 'selected'; ?> value="filter"><?php _e('Filter', 'BeRocket_AJAX_domain') ?></option>
            <option <?php if ($instance['widget_type'] == 'update_button') echo 'selected'; ?> value="update_button"><?php _e('Update Products button', 'BeRocket_AJAX_domain') ?></option>
        </select>
    </label>
</p>

<hr />

<p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'BeRocket_AJAX_domain') ?> </label>
    <input id="<?php echo $this->get_field_id( 'title' ); ?>" type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
</p>
<div class="berocket_aapf_admin_filter_widget_content" <?php if ( $instance['widget_type'] == 'update_button' or $instance['widget_type'] == 'selected_area' ){ echo 'style="display: none;"';} else { echo 'style="float: none;"'; } ?>>
    <p>
        <label><?php _e('Attribute:', 'BeRocket_AJAX_domain') ?>
            <select id="<?php echo $this->get_field_id( 'attribute' ); ?>" name="<?php echo $this->get_field_name( 'attribute' ); ?>" class="berocket_aapf_widget_admin_attribute_select">
                <option <?php if ($instance['attribute'] == 'price') echo 'selected'; ?> value="price"><?php _e('Price', 'BeRocket_AJAX_domain') ?></option>
                <?php foreach( $attributes as $k => $v ){ ?>
                    <option <?php if ($instance['attribute'] == $k) echo 'selected'; ?> value="<?php echo $k ?>"><?php echo $v ?></option>
                <?php } ?>
            </select>
        </label>
    </p>
    <p>
        <label><?php _e('Type:', 'BeRocket_AJAX_domain') ?>
            <select id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>" class="berocket_aapf_widget_admin_type_select">
                <?php if ( $instance['attribute'] != 'price' ){ ?>
                    <option <?php if ($instance['type'] == 'checkbox') echo 'selected'; ?> value="checkbox">Checkbox</option>
                    <option <?php if ($instance['type'] == 'radio') echo 'selected'; ?> value="radio">Radio</option>
                    <option <?php if ($instance['type'] == 'select') echo 'selected'; ?> value="select">Select</option>
                <?php } else { ?>
                <option <?php if ($instance['type'] == 'slider') echo 'selected'; ?> value="slider">Slider</option>
                <?php } ?>
            </select>
        </label>
    </p>

    <p <?php if ( $instance['attribute'] == 'price' or $instance['type'] == 'slider' ) echo " style='display: none;'"; ?> >
        <label><?php _e('Operator:', 'BeRocket_AJAX_domain') ?>
            <select id="<?php echo $this->get_field_id( 'operator' ); ?>" name="<?php echo $this->get_field_name( 'operator' ); ?>" class="berocket_aapf_widget_admin_operator_select">
                <option <?php if ($instance['operator'] == 'AND') echo 'selected'; ?> value="AND">AND</option>
                <option <?php if ($instance['operator'] == 'OR') echo 'selected'; ?> value="OR">OR</option>
            </select>
        </label>
    </p>
    <p <?php if ( $instance['attribute'] != 'price' ) echo " style='display: none;'"; ?> class="berocket_aapf_widget_admin_price_attribute" >
        <label for="<?php echo $this->get_field_id( 'text_before_price' ); ?>">Text before price: </label>
        <input id="<?php echo $this->get_field_id( 'text_before_price' ); ?>" type="text" name="<?php echo $this->get_field_name( 'text_before_price' ); ?>" value="<?php echo $instance['text_before_price']; ?>" />
    </p>
    <p <?php if ( $instance['attribute'] != 'price' ) echo " style='display: none;'"; ?> class="berocket_aapf_widget_admin_price_attribute" >
        <label for="<?php echo $this->get_field_id( 'text_after_price' ); ?>">Text after price: </label>
        <input id="<?php echo $this->get_field_id( 'text_after_price' ); ?>" type="text" name="<?php echo $this->get_field_name( 'text_after_price' ); ?>" value="<?php echo $instance['text_after_price']; ?>" />
    </p>
    <p>
        <a href="#" class='berocket_aapf_advanced_settings_pointer'><?php _e('Advanced Settings', 'BeRocket_AJAX_domain') ?></a>
    </p>
    <div class='berocket_aapf_advanced_settings' style="display:none;">
        <p>
            <label for="<?php echo $this->get_field_id( 'css_class' ); ?>"><?php _e('CSS Class:', 'BeRocket_AJAX_domain') ?> </label>
            <input id="<?php echo $this->get_field_id( 'css_class' ); ?>" type="text" name="<?php echo $this->get_field_name( 'css_class' ); ?>" value="<?php echo @ $instance['css_class']; ?>" class="berocket_aapf_widget_admin_css_class_input" />
            <small>(use white space for multiple classes)</small>
        </p>
        <p>
            <label><?php _e('Product Category:', 'BeRocket_AJAX_domain') ?>
                <label class="berocket_aapf_advanced_settings_subcategory">
                    <input type="checkbox" name="<?php echo $this->get_field_name( 'cat_propagation' ); ?>" <?php if ( @ $instance['cat_propagation'] ) echo 'checked'; ?> value="1" class="berocket_aapf_widget_admin_height_input" />
                    <?php _e('include subcats?', 'BeRocket_AJAX_domain') ?>
                </label>
            </label>
            <ul class="berocket_aapf_advanced_settings_categories_list">
                <?php
                $p_cat = @json_decode( $instance['product_cat'] );

                foreach( $categories as $category ){
                    $selected_category = false;

                    if( $p_cat )
                        foreach( $p_cat as $cat ){
                            if( $cat == $category->slug )
                                $selected_category = true;
                        }
                ?>
                <li>
                    <?php
                    if ( @ (int)$category->depth ) for ( $depth_i = 0; $depth_i < $category->depth*3; $depth_i++ ) echo "&nbsp;";
                    ?>
                    <input type="checkbox" name="<?php echo $this->get_field_name( 'product_cat' ); ?>[]" <?php if ( $selected_category ) echo 'checked'; ?> value="<?php echo $category->slug ?>" class="berocket_aapf_widget_admin_height_input" />
                    <?php echo $category->name ?>
                </li>
                <?php } ?>
            </ul>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e('Filter Box Height:', 'BeRocket_AJAX_domain') ?> </label>
            <input id="<?php echo $this->get_field_id( 'height' ); ?>" type="text" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']; ?>" class="berocket_aapf_widget_admin_height_input" />px
        </p>
        <p>
            <label for="<?php echo $this->get_field_id( 'scroll_theme' ); ?>"><?php _e('Scroll Theme:', 'BeRocket_AJAX_domain') ?> </label>
            <select id="<?php echo $this->get_field_id( 'scroll_theme' ); ?>" name="<?php echo $this->get_field_name( 'scroll_theme' ); ?>" class="berocket_aapf_widget_admin_scroll_theme_select">
                <?php
                $scroll_themes = array("light", "dark", "minimal", "minimal-dark", "light-2", "dark-2", "light-3", "dark-3", "light-thick", "dark-thick", "light-thin",
                    "dark-thin", "inset", "inset-dark", "inset-2", "inset-2-dark", "inset-3", "inset-3-dark", "rounded", "rounded-dark", "rounded-dots",
                    "rounded-dots-dark", "3d", "3d-dark", "3d-thick", "3d-thick-dark");
                foreach( $scroll_themes as $theme ): ?>
                    <option <?php if ($instance['scroll_theme'] == $theme) echo 'selected'; ?>><?php echo $theme; ?></option>
                <?php endforeach; ?>
            </select>
        </p>
    </div>
</div>