<?php
/**
 * WCMp Vendor Info Widget
 *
 * @author    WC Marketplace
 * @category  Widgets
 * @package   WCMp/Widgets
 * @version   2.2.0
 * @extends   WP_Widget
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class DC_Widget_Vendor_Info extends WP_Widget {

    /**
     * constructor
     *
     * @access public
     * @return void
     */
    function __construct() {
        global $WCMp, $wp_version;

        // Widget variable settings
        $this->widget_idbase = 'dc_product_vendors_info';
        $this->widget_title = __("WCMp Product Vendor's Info", 'dc-woocommerce-multi-vendor');
        $this->widget_cssclass = 'widget_product_vendor_info';
        $this->widget_description = __('Display selected or current product vendor info.', 'dc-woocommerce-multi-vendor');

        // Widget settings
        $widget_ops = array('classname' => $this->widget_cssclass, 'description' => $this->widget_description);

        // Widget control settings
        $control_ops = array('width' => 250, 'height' => 350, 'id_base' => $this->widget_idbase);

        // Create the widget
        if ($wp_version >= 4.3) {
            parent::__construct($this->widget_idbase, $this->widget_title, $widget_ops, $control_ops);
        } else {
            $this->WP_Widget($this->widget_idbase, $this->widget_title, $widget_ops, $control_ops);
        }
    }

    /**
     * widget function.
     *
     * @see WP_Widget
     * @access public
     * @param array $args
     * @param array $instance
     * @return void
     */
    function widget($args, $instance) {
        global $WCMp, $woocommerce;
        extract($args, EXTR_SKIP);
        $vendor_id = false;
        $vendor = false;
        // Only show current vendor widget when showing a vendor's product(s)
        $show_widget = true;
        if (is_singular('product')) {
            global $post;
            $vendor = get_wcmp_product_vendors($post->ID);
            if (!$vendor) {
                $show_widget = false;
            }
        }
        if (is_archive() && !is_tax('dc_vendor_shop')) {
            $show_widget = false;
        }

        if ($show_widget) {
            if (is_tax('dc_vendor_shop')) {
                $vendor_id = get_queried_object()->term_id;
                if ($vendor_id) {
                    $vendor = get_wcmp_vendor_by_term($vendor_id);
                }
            }
            if ($vendor) {
                // Set up widget title
                if ($instance['title']) {
                    $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
                } else {
                    $title = false;
                }
                // Before widget (defined by themes)
                echo $before_widget;

                // Display the widget title if one was input (before and after defined by themes).
                if ($title) {
                    echo $before_title . $title . $after_title;
                }


                // Action for plugins/themes to hook onto
                do_action($this->widget_cssclass . '_top');

                // Widget content
                $WCMp->template->get_template('widget/vendor-info.php', array('vendor' => $vendor));

                // Action for plugins/themes to hook onto
                do_action($this->widget_cssclass . '_bottom');

                // After widget (defined by themes).
                echo $after_widget;
            }
        }
    }

    /**
     * update function.
     *
     * @see WP_Widget->update
     * @access public
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        // Sanitise inputs
        $instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }

    /**
     * The form on the widget control in the widget administration area
     * @since  1.0.0
     * @param  array $instance The settings for this instance.
     * @return void
     */
    public function form($instance) {
        global $WCMp;
        // Set up the default widget settings
        $defaults = array(
            'title' => '',
        );
        $instance = wp_parse_args((array) $instance, $defaults);
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title (optional):', 'dc-woocommerce-multi-vendor'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('title'); ?>"  value="<?php echo $instance['title']; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
        </p>
        <span class="description"><?php _e('This widget shows..', 'dc-woocommerce-multi-vendor') ?> </span>
        <?php
    }

}
