<?php

define('BEROCKETAAPF', 'BeRocket_AAPF_Widget');

/* Widget */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
function BeRocket_AAPF_load_widgets() {
    register_widget( 'BeRocket_AAPF_widget' );
}
require_once dirname( __FILE__ ).'/functions.php';
if ( ( is_plugin_active( 'woocommerce/woocommerce.php' ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) && br_get_woocommerce_version() >= 2.1 ) {
    add_action( 'widgets_init', 'BeRocket_AAPF_load_widgets' );
    add_action( 'wp_ajax_nopriv_berocket_aapf_listener', array( 'BeRocket_AAPF_Widget', 'listener' ) );
    add_action( 'wp_ajax_berocket_aapf_listener', array( 'BeRocket_AAPF_Widget', 'listener' ) );
}

/**
 * BeRocket_AAPF_Widget - main filter widget. One filter for any needs
 */
class BeRocket_AAPF_Widget extends WP_Widget {

    /**
     * Constructor
     */
    function __construct() {
        global $wp_version;
        /* Widget settings. */
        $widget_ops = array( 'classname' => 'widget_berocket_aapf', 'description' => __('Add Filters to Products page', 'BeRocket_AJAX_domain') );

        /* Widget control settings. */
        $control_ops = array( 'id_base' => 'berocket_aapf_widget' );

        /* Create the widget. */
        parent::__construct( 'berocket_aapf_widget', __('AJAX Product Filters', 'BeRocket_AJAX_domain'), $widget_ops, $control_ops );

        add_filter( 'berocket_aapf_listener_wp_query_args', 'br_aapf_args_parser' );
    }

    /**
     * Show widget to user
     *
     * @param array $args
     * @param array $instance
     */
    function widget( $args, $instance ) {
        $br_options = apply_filters( 'berocket_aapf_listener_br_options', get_option('br_filters_options') );
        
        if( ! empty($br_options['filters_turn_off']) || ( ! is_shop() && ! is_product_category() && ! is_product_taxonomy() && ! is_product_tag() ) ) return false;

        global $wp_query, $wp, $sitepress;

        /* main scripts */
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_script( 'berocket_aapf_widget-script', plugins_url( '../js/widget.min.js', __FILE__ ), array( 'jquery' ), BeRocket_AJAX_filters_version );
        wp_enqueue_script( 'berocket_aapf_widget-hack-script', plugins_url( '../js/mobiles.min.js', __FILE__ ), array( 'jquery' ), BeRocket_AJAX_filters_version );

        $wp_query_product_cat = '-1';
        if ( ! empty($wp_query->query['product_cat']) ) {
            $wp_query_product_cat = explode( "/", $wp_query->query['product_cat'] );
            $wp_query_product_cat = $wp_query_product_cat[ count( $wp_query_product_cat ) - 1 ];
        }

        if ( ! $br_options['products_holder_id'] ) $br_options['products_holder_id'] = 'ul.products';

        $post_temrs = "[]";
        if ( ! empty($_POST['terms']) ) {
            $post_temrs = json_encode( $_POST['terms'] );
        }

        if ( method_exists($sitepress, 'get_current_language') ) {
            $current_language = $sitepress->get_current_language();
        } else {
            $current_language = '';
        }

        $product_taxonomy = '-1';
        if ( is_product_taxonomy() ) {
            $product_taxonomy = $wp_query->query_vars['taxonomy'].'|'.$wp_query->query_vars['term'];
        }

        wp_localize_script(
            'berocket_aapf_widget-script',
            'the_ajax_script',
            array(
                'version'                              => BeRocket_AJAX_filters_version,
                'current_language'                     => $current_language,
                'current_page_url'                     => preg_replace( "~paged?/[0-9]+/?~", "", home_url( $wp->request ) ),
                'ajaxurl'                              => admin_url( 'admin-ajax.php' ),
                'product_cat'                          => $wp_query_product_cat,
                'product_taxonomy'                     => $product_taxonomy,
                'products_holder_id'                   => ( ! empty($br_options['products_holder_id']) ? $br_options['products_holder_id'] : BeRocket_AAPF::$defaults['products_holder_id'] ),
                'result_count_class'                   => ( ! empty($br_options['woocommerce_result_count_class']) ? $br_options['woocommerce_result_count_class'] : BeRocket_AAPF::$defaults['woocommerce_result_count_class'] ),
                'ordering_class'                       => ( ! empty($br_options['woocommerce_ordering_class']) ? $br_options['woocommerce_ordering_class'] : BeRocket_AAPF::$defaults['woocommerce_ordering_class'] ),
                'pagination_class'                     => ( ! empty($br_options['woocommerce_pagination_class']) ? $br_options['woocommerce_pagination_class'] : BeRocket_AAPF::$defaults['woocommerce_pagination_class'] ),
                'no_products_message'                  => ( isset($br_options['no_products_message']) ? $br_options['no_products_message'] : '' ),
                'no_products_class'                    => ( isset($br_options['no_products_class']) ? $br_options['no_products_class'] : '' ),
                'control_sorting'                      => ( isset($br_options['control_sorting']) ? $br_options['control_sorting'] : '' ),
                'seo_friendly_urls'                    => ( isset($br_options['seo_friendly_urls']) ? $br_options['seo_friendly_urls'] : '' ),
                'berocket_aapf_widget_product_filters' => $post_temrs,
                'user_func'                            => apply_filters( 'berocket_aapf_user_func', ( isset($br_options['user_func']) ? $br_options['user_func'] : '' ) ),
                'default_sorting'                      => get_option('woocommerce_default_catalog_orderby'),
                'first_page'                           => ( isset($br_options['first_page_jump']) ? $br_options['first_page_jump'] : '' ),
                'scroll_shop_top'                      => ( isset($br_options['scroll_shop_top']) ? $br_options['scroll_shop_top'] : '' ),
                'hide_sel_value'                       => ( isset($br_options['hide_value']['sel']) ? $br_options['hide_value']['sel'] : '' ),
                'ajax_request_load'                    => ( isset($br_options['ajax_request_load']) ? $br_options['ajax_request_load'] : '' ),
                'ajax_request_load_style'              => ( isset($br_options['ajax_request_load_style']) ? $br_options['ajax_request_load_style'] : BeRocket_AAPF::$defaults['ajax_request_load_style'] ),
                's'                                    => ( isset($_GET['s']) ? $_GET['s'] : '' ),
            )
        );
        unset( $current_language );
        unset( $post_temrs );

        extract( $args );
        extract( $instance );
        unset( $args );
        unset( $instance );

        if ( $widget_type == 'update_button' ) {
            set_query_var( 'title', apply_filters( 'berocket_aapf_widget_title', $title ) );
            echo $before_widget;
            br_get_template_part( 'widget_update_button' );
            echo $after_widget;
            return '';
        }

        $product_cat = @ json_decode( $product_cat );

        if ( $product_cat ) {
            $hide_widget = true;

            $cur_cat = get_term_by( 'slug', $wp_query_product_cat, 'product_cat' );
            $cur_cat_ancestors = get_ancestors( $cur_cat->term_id, 'product_cat' );
            $cur_cat_ancestors[] = $cur_cat->term_id;

            if( $product_cat ) {
                if ( $cat_propagation ) {
                    foreach ( $product_cat as $cat ) {
                        $cat = get_term_by( 'slug', $cat, 'product_cat' );

                        if ( is_array($cur_cat_ancestors) && in_array( $cat->term_id, $cur_cat_ancestors ) ) {
                            $hide_widget = false;
                        }
                    }
                } else {
                    foreach ( $product_cat as $cat ) {
                        if ( $cat == $wp_query_product_cat ) {
                            $hide_widget = false;
                        }
                    }
                }
            }

            if ( $hide_widget ) {
                return true;
            }
        }
        unset( $product_cat );

        $woocommerce_hide_out_of_stock_items = BeRocket_AAPF_Widget::woocommerce_hide_out_of_stock_items();
        $terms = $sort_terms = $price_range = array();

        if ( $attribute == 'price' ) {
            $price_range = BeRocket_AAPF_Widget::get_price_range( $wp_query_product_cat, $woocommerce_hide_out_of_stock_items );
            if ( ! $price_range or count( $price_range ) < 2 ) return false;
        } else {
            $sort_array = array();
            $wc_order_by = wc_attribute_orderby( $attribute );

            if ( ! empty($br_options['show_all_values']) ) {
                $terms = BeRocket_AAPF_Widget::get_attribute_values( $attribute );
            } else {
                $terms = BeRocket_AAPF_Widget::get_attribute_values( $attribute, 'id', true );
            }

            if ( empty($terms) || ! is_array($terms) || count( $terms ) < 1 ) return false;

            if ( $wc_order_by == 'menu_order' ) {
                foreach ( $terms as $term ) {
                    $sort_array[] = get_woocommerce_term_meta( $term->term_id, 'order_' . $attribute );
                }
                array_multisort( $sort_array, $terms );
            } elseif ( $wc_order_by == 'name' or $wc_order_by == 'name_num' ) {
                foreach ( $terms as $term ) {
                    $sort_array[] = $term->name;
                }
                $sort_as = SORT_STRING;
                if ( $wc_order_by == 'name_num' ) {
                    $sort_as = SORT_NUMERIC;
                }
                array_multisort( $sort_array, $terms, SORT_ASC, $sort_as );
            }

            set_query_var( 'terms', apply_filters( 'berocket_aapf_widget_terms', $terms ) );
        }

        $style = $class = '';
        if( ! empty($height) and $height != 'auto' ){
            $style = "style='height: {$height}px; overflow: hidden;'";
            $class = "berocket_aapf_widget_height_control";
        }

        if( !$scroll_theme ) $scroll_theme = 'dark';

        set_query_var( 'operator', $operator );
        set_query_var( 'type', $type );
        set_query_var( 'title', apply_filters( 'berocket_aapf_widget_title', $title ) );
        set_query_var( 'class', apply_filters( 'berocket_aapf_widget_class', $class ) );
        set_query_var( 'css_class', apply_filters( 'berocket_aapf_widget_css_class', ( isset($css_class) ? $css_class : '' ) ) );
        set_query_var( 'style', apply_filters( 'berocket_aapf_widget_style', $style ) );
        set_query_var( 'scroll_theme', $scroll_theme );
        set_query_var( 'x', time() );
        set_query_var( 'hide_o_value', ! empty($br_options['hide_value']['o']) );
        set_query_var( 'hide_sel_value', ! empty($br_options['hide_value']['sel']) );
        set_query_var( 'before_title', ( isset($before_title) ? $before_title : '' ) );
        set_query_var( 'after_title', ( isset($after_title) ? $after_title : '' ) );

        echo $before_widget;

        // widget title and start tag ( <ul> ) can be found in templates/widget_start.php
        br_get_template_part('widget_start');

        if( $type == 'slider' ){
            $min = $max = false;
            $main_class = 'slider';
            $slider_class = 'berocket_filter_slider';

            if( $attribute == 'price' ){
                wp_localize_script(
                    'berocket_aapf_widget-script',
                    'br_price_text',
                    array(
                        'before'  => ( isset($text_before_price) ? $text_before_price : '' ),
                        'after'   => ( isset($text_after_price) ? $text_after_price : '' ),
                    )
                );
                if( $price_range ) {
                    foreach ( $price_range as $price ) {
                        if ( $min === false or $min > (int) $price ) {
                            $min = $price;
                        }
                        if ( $max === false or $max < (int) $price ) {
                            $max = $price;
                        }
                    }
                }
                $id = 'br_price';
                $slider_class .= ' berocket_filter_price_slider';
                $main_class .= ' price';

                $min = number_format( floor( $min ), 2, '.', '' );
                $max = number_format( ceil( $max ), 2, '.', '' );
            } else {
                if( ! empty($terms) ) {
                    foreach ( $terms as $term ) {
                        if ( $min === false or $min > (int) $term->name ) {
                            $min = floor( (float) $term->name );
                        }
                        if ( $max === false or $max < (int) $term->name ) {
                            $max = ceil( (float) $term->name );
                        }
                    }
                }
                $id = $term->taxonomy;
            }

            $slider_value1 = $min;
            $slider_value2 = $max;

            if( $attribute == 'price' and ! empty($_POST['price']) ){
                $slider_value1 = apply_filters('woocommerce_price_filter_widget_min_amount', $_POST['price'][0]);
                $slider_value2 = apply_filters('woocommerce_price_filter_widget_max_amount', $_POST['price'][1]);
            }
            if( $attribute != 'price' and ! empty($_POST['limits']) ){
                foreach( $_POST['limits'] as $p_limit ){
                    if( $p_limit[0] == $attribute ){
                        $slider_value1 = $p_limit[1];
                        $slider_value2 = $p_limit[2];
                    }
                }
            }

            set_query_var( 'slider_value1', $slider_value1 );
            set_query_var( 'slider_value2', $slider_value2 );
            set_query_var( 'filter_slider_id', $id );
            set_query_var( 'main_class', $main_class );
            set_query_var( 'slider_class', $slider_class );
            set_query_var( 'min', $min );
            set_query_var( 'max', $max );
            set_query_var( 'text_before_price', ( isset($text_before_price) ? $text_before_price : '' ) );
            set_query_var( 'text_after_price', ( isset($text_after_price) ? $text_after_price : '' ) );
        }
        set_query_var( 'first_page_jump', ( isset($first_page_jump) ? $first_page_jump : '' ) );

        br_get_template_part( $type );

        br_get_template_part('widget_end');
        echo $after_widget;
    }

    public static function woocommerce_hide_out_of_stock_items(){
        $hide = get_option( 'woocommerce_hide_out_of_stock_items', null );

        if ( is_array( $hide ) ) {
            $hide = array_map( 'stripslashes', $hide );
        } elseif ( ! is_null( $hide ) ) {
            $hide = stripslashes( $hide );
        }

        return apply_filters( 'berocket_aapf_hide_out_of_stock_items', $hide );
    }

    public static function get_price_range( $wp_query_product_cat, $woocommerce_hide_out_of_stock_items ){
        global $wpdb, $wp_query;
        
        if( method_exists('WC_Query', 'get_main_tax_query') && method_exists('WC_Query', 'get_main_tax_query') && 
        class_exists('WP_Meta_Query') && class_exists('WP_Tax_Query') ) {
            $query_string = array(
                'select' => "SELECT MIN(cast(FLOOR(br_prices.meta_value) as decimal)) as min_price, MAX(cast(CEIL(br_prices.meta_value) as decimal)) as max_price
                    FROM {$wpdb->posts}",
                'join'   => " INNER JOIN {$wpdb->postmeta} as br_prices ON ({$wpdb->posts}.ID = br_prices.post_id)",
                'where'  => " WHERE {$wpdb->posts}.post_type = 'product'
                    AND {$wpdb->posts}.post_status = 'publish'
                    AND  br_prices.meta_key = '_price' AND br_prices.meta_value > 0");
            $tax_query  = array();
            $args       = $wp_query->query_vars;
            $meta_query = isset( $args['meta_query'] ) ? $args['meta_query'] : array();
            foreach ( $meta_query as $key => $query ) {
                if ( ! empty( $query['price_filter'] ) || ! empty( $query['rating_filter'] ) ) {
                    unset( $meta_query[ $key ] );
                }
            }
            if ( ! empty( $args['product_cat'] ) ) {
                $tax_query[ 'product_cat' ] = array(
                    'taxonomy' => 'product_cat',
                    'terms'    => array( $args['product_cat'] ),
                    'field'    => 'slug',
                );
            }
            $queried_object = $wp_query->get_queried_object_id();
            if( ! empty($queried_object) ) {
                $query_object = $wp_query->get_queried_object();
                if( ! empty($query_object->taxonomy) && ! empty($query_object->slug) ) {
                    $tax_query[ $query_object->taxonomy ] = array(
                        'taxonomy' => $query_object->taxonomy,
                        'terms'    => array( $query_object->slug ),
                        'field'    => 'slug',
                    );
                }
            }
            $meta_query = new WP_Meta_Query( $meta_query );
            $tax_query  = new WP_Tax_Query( $tax_query );
            $meta_query_sql  = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
            $tax_query_sql   = $tax_query->get_sql( $wpdb->posts, 'ID' );
            $query_string['join'] = $query_string['join'].' '. $tax_query_sql['join'] . $meta_query_sql['join'];
            $query_string['where'] = $query_string['where'].' '. $tax_query_sql['where'] . $meta_query_sql['where'];
            $query_string = implode( ' ', $query_string );
        } else {
            $_POST['product_cat'] = $wp_query_product_cat;
            $extra_inner = $extra_where = '';

            if ( isset($_POST['product_cat']) and $_POST['product_cat'] != '-1' ) {
                $product_cat = get_term_by( 'slug', $_POST['product_cat'], 'product_cat' );
                $sub_categories = get_term_children($product_cat->term_id, 'product_cat');
                if( empty($sub_categories) || ! is_array($sub_categories) ) {
                    $sub_categories = array();
                }
                $sub_categories[] = $product_cat->term_id;
                $sub_categories = implode(',', $sub_categories);

                $extra_inner = " INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id) ";
                $extra_where = " AND ( {$wpdb->term_relationships}.term_taxonomy_id IN (" . $sub_categories . ") ) ";

                unset( $sub_categories );
            } elseif ( isset($wp_query->query_vars['product_tag']) ) {
                $product_tag = get_term_by( 'slug', $wp_query->query_vars['product_tag'], 'product_tag' );
                $extra_inner = " INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id) ";
                $extra_where = " AND ( {$wpdb->term_relationships}.term_taxonomy_id IN (" . $product_tag->term_id . ") ) ";
            }
            $hide_out_of_stock = BeRocket_AAPF_Widget::woocommerce_hide_out_of_stock_items();
            $out_of_stock1 = '';
            $out_of_stock2 = '';
            if ( $hide_out_of_stock == 'yes' ) {
                $out_of_stock1 = "INNER JOIN {$wpdb->postmeta} AS pm2 ON ({$wpdb->posts}.ID = pm2.post_id)";
                $out_of_stock2 = "AND ( pm2.meta_key = '_stock_status' AND CAST(pm2.meta_value AS CHAR) = 'instock' )";
            }
            

            $query_string = "
                    SELECT MIN(cast({$wpdb->postmeta}.meta_value as unsigned)) as min_price, MAX(cast({$wpdb->postmeta}.meta_value as unsigned)) as max_price
                    FROM {$wpdb->posts}
                    INNER JOIN {$wpdb->postmeta} ON ({$wpdb->posts}.ID = {$wpdb->postmeta}.post_id)
                    INNER JOIN {$wpdb->postmeta} AS pm1 ON ({$wpdb->posts}.ID = pm1.post_id)
                    {$out_of_stock1}
                    {$extra_inner}
                    WHERE {$wpdb->posts}.post_type = 'product'
                    AND {$wpdb->posts}.post_status = 'publish'
                    {$extra_where}
                    AND ( {$wpdb->postmeta}.meta_key = '_price' AND {$wpdb->postmeta}.meta_value > 0
                    AND ( pm1.meta_key = '_visibility' AND CAST(pm1.meta_value AS CHAR) IN ('visible','catalog') )
                    {$out_of_stock2} ) ";
        }

        $prices = $wpdb->get_row($query_string);
        $price_range = false;
        if( isset($prices->min_price) && isset($prices->max_price) && $prices->min_price != $prices->max_price ) {
            $price_range = array(
                apply_filters( 'woocommerce_price_filter_widget_min_amount', $prices->min_price ), 
                apply_filters( 'woocommerce_price_filter_widget_max_amount', $prices->max_price )
            );
        }
        return apply_filters( 'berocket_aapf_get_price_range', $price_range );
    }

    public static function get_attribute_values( $taxonomy = '', $order_by = 'id', $hide_empty = false ) {
        if ( ! $taxonomy ) return array();
        $re = array();
        if( $hide_empty ) {
            global $wp_query, $post, $wp_the_query;
            $old_wp_the_query = $wp_the_query;
            $wp_the_query = $wp_query;
            if( method_exists('WC_Query', 'get_main_tax_query') && method_exists('WC_Query', 'get_main_tax_query') && 
            class_exists('WP_Meta_Query') && class_exists('WP_Tax_Query') ) {
                $args = array(
                    'orderby'    => $order_by,
                    'order'      => 'ASC',
                    'hide_empty' => false,
                );
                $re = get_terms( $taxonomy, $args );
                global $wpdb;
                $meta_query = WC_Query::get_main_meta_query();
                $args      = $wp_the_query->query_vars;
                $tax_query = array();
                if ( ! empty( $args['product_cat'] ) ) {
                    $tax_query[ 'product_cat' ] = array(
                        'taxonomy' => 'product_cat',
                        'terms'    => array( $args['product_cat'] ),
                        'field'    => 'slug',
                    );
                }

                $meta_query      = new WP_Meta_Query( $meta_query );
                $tax_query       = new WP_Tax_Query( $tax_query );
                $meta_query_sql  = $meta_query->get_sql( 'post', $wpdb->posts, 'ID' );
                $tax_query_sql   = $tax_query->get_sql( $wpdb->posts, 'ID' );
                $term_ids = wp_list_pluck( $re, 'term_id' );

                // Generate query
                $results = array();
                if( is_array($term_ids) && count($term_ids) > 0 ) {
                    $query           = array();
                    $query['select'] = "SELECT COUNT( DISTINCT {$wpdb->posts}.ID ) as term_count, terms.term_id as term_count_id";
                    $query['from']   = "FROM {$wpdb->posts}";
                    $query['join']   = "
                        INNER JOIN {$wpdb->term_relationships} AS term_relationships ON {$wpdb->posts}.ID = term_relationships.object_id
                        INNER JOIN {$wpdb->term_taxonomy} AS term_taxonomy USING( term_taxonomy_id )
                        INNER JOIN {$wpdb->terms} AS terms USING( term_id )
                        " . $tax_query_sql['join'] . $meta_query_sql['join'];
                    $query['where']   = "
                        WHERE {$wpdb->posts}.post_type IN ( 'product' )
                        AND {$wpdb->posts}.post_status = 'publish'
                        " . $tax_query_sql['where'] . $meta_query_sql['where'] . "
                        AND terms.term_id IN (" . implode( ',', array_map( 'absint', $term_ids ) ) . ")
                    ";
                    $query['group_by'] = "GROUP BY terms.term_id";
                    $query             = apply_filters( 'woocommerce_get_filtered_term_product_counts_query', $query );
                    $query             = implode( ' ', $query );
                    $results           = $wpdb->get_results( $query );
                    $results           = wp_list_pluck( $results, 'term_count', 'term_count_id' );
                }
                $term_count = array();
                $terms = array();
                foreach($re as &$res_count) {
                    if( ! empty($results[$res_count->term_id] ) ) {
                        $res_count->count = $results[$res_count->term_id];
                    } else {
                        $res_count->count = 0;
                    }
                    if( $res_count->count > 0 ) {
                        $terms[] = $res_count;
                    }
                }
                $re = $terms;
            } else {
                $terms = array();
                $q_args = $wp_query->query_vars;
                $q_args['posts_per_page'] = 2000;
                $q_args['post__in']       = '';
                $q_args['tax_query']      = '';
                $q_args['taxonomy']       = '';
                $q_args['term']           = '';
                $q_args['meta_query']     = '';
                $q_args['attribute']      = '';
                $q_args['title']          = '';
                $q_args['post_type']      = 'product';
                $q_args['fields']         = 'ids';
                $paged                    = 1;
                do{
                    $q_args['paged'] = $paged;
                    $the_query = new WP_Query($q_args);
                    if ( $the_query->have_posts() ) {
                        foreach ( $the_query->posts as $post_id ) {
                            $curent_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );
                            foreach ( $curent_terms as $t ) {
                                if ( ! in_array( $t,$terms ) ) {
                                    $terms[] = $t;
                                }
                            }
                        }
                    }
                    $paged++;
                } while($paged <= $the_query->max_num_pages);
                unset( $q_args );
                unset( $the_query );
                wp_reset_query();
                $args = array(
                    'orderby'           => $order_by,
                    'order'             => 'ASC',
                    'hide_empty'        => false,
                );
                $terms2 = get_terms( $taxonomy, $args );
                foreach ( $terms2 as $t ) {
                    if ( in_array( $t->term_id, $terms ) ) {
                        $re[] = $t;
                    }
                }
            }
            $wp_the_query = $old_wp_the_query;
            return $re;
        } else {
            $args = array(
                'orderby'           => $order_by,
                'order'             => 'ASC',
                'hide_empty'        => false,
            );
            return get_terms( $taxonomy, $args );
        }
    }

    public static function get_filter_products( $wp_query_product_cat, $woocommerce_hide_out_of_stock_items, $use_filters = true ) {
        global $wp_query, $wp_rewrite;
        $_POST['product_cat'] = $wp_query_product_cat;

        add_filter( 'woocommerce_pagination_args', array( __CLASS__, 'pagination_args' ) );

        $args = apply_filters( 'berocket_aapf_listener_wp_query_args', array() );

        if ( $use_filters ) {
            $args['post__in'] = BeRocket_AAPF::limits_filter( array() );
            $args['post__in'] = BeRocket_AAPF::price_filter( $args['post__in'] );
        } else {
            $args = array( 'posts_per_page' => -1 );
            if ( isset($_POST['product_cat']) and $_POST['product_cat'] != '-1' ) {
                $args['tax_query'][] = array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => strip_tags( $_POST['product_cat'] ),
                    'operator' => 'IN'
                );
            }
        }

        $args['post_status'] = 'publish';
        $args['post_type'] = 'product';

        $wp_query = new WP_Query( $args );

        // here we get max products to know if current page is not too big
        if( isset($_POST['location']) ) {
            if ( $wp_rewrite->using_permalinks() and preg_match( "~/page/([0-9]+)~", $_POST['location'], $mathces ) or preg_match( "~paged?=([0-9]+)~", $_POST['location'], $mathces ) ) {
                $args['paged'] = min( $mathces[1], $wp_query->max_num_pages );
                $wp_query = new WP_Query( $args );
            }
        }

        $products = array();
        if ( $wp_query->have_posts() ) {
            while ( have_posts() ) {
                the_post();
                $products[] = get_the_ID();
            }
        }

        wp_reset_query();

        return $products;
    }

    /**
     * Validating and updating widget data
     *
     * @param array $new_instance
     * @param array $old_instance
     *
     * @return array - new merged instance
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;

        /* Strip tags (if needed) and update the widget settings. */
        $instance['widget_type']       = strip_tags( $new_instance['widget_type'] );
        $instance['title']             = strip_tags( $new_instance['title'] );
        $instance['attribute']         = strip_tags( $new_instance['attribute'] );
        $instance['type']              = strip_tags( $new_instance['type'] );
        $instance['product_cat']       = ( $new_instance['product_cat'] ) ? json_encode( $new_instance['product_cat'] ) : '';
        $instance['scroll_theme']      = strip_tags( $new_instance['scroll_theme'] );
        $instance['cat_propagation']   = (int) $new_instance['cat_propagation'];
        $instance['css_class']         = strip_tags( $new_instance['css_class'] );
        $instance['text_before_price'] = strip_tags( $new_instance['text_before_price'] );
        $instance['text_after_price']  = strip_tags( $new_instance['text_after_price'] );

        if( $new_instance['height'] != 'auto' ) $new_instance['height'] = (float) $new_instance['height'];
        if( !$new_instance['height'] ) $new_instance['height'] = 'auto';
        $instance['height'] = $new_instance['height'];

        if( $new_instance['operator'] != 'OR' ) $new_instance['operator'] = 'AND';
        $instance['operator'] = $new_instance['operator'];

        if( $instance['attribute'] == 'price' ) $instance['type'] = 'slider';

        do_action( 'berocket_aapf_admin_update', $instance, $new_instance, $old_instance );

        return apply_filters( 'berocket_aapf_admin_update_instance', $instance );
    }

    /**
     * Output admin form
     *
     * @param array $instance
     *
     * @return string|void
     */
    function form( $instance ) {
        wp_enqueue_script( 'berocket_aapf_widget-admin-script', plugins_url('../js/admin.js', __FILE__), array('jquery'), BeRocket_AJAX_filters_version );
        wp_register_style( 'berocket_aapf_widget-style', plugins_url('../css/admin.css', __FILE__), array(), BeRocket_AJAX_filters_version );
        wp_enqueue_style( 'berocket_aapf_widget-style' );

        /* Set up some default widget settings. */
        $defaults = array(
            'widget_type'       => 'filter',
            'title'             => '',
            'attribute'         => 'price',
            'type'              => 'slider',
            'operator'          => '',
            'product_cat'       => '',
            'text_before_price' => '',
            'text_after_price'  => '',
            'height'            => 'auto',
            'scroll_theme'      => 'dark',
        );

        $defaults = apply_filters( 'berocket_aapf_form_defaults', $defaults );

        $instance = wp_parse_args( (array) $instance, $defaults );
        $attributes = br_aapf_get_attributes();
        $categories = self::get_product_categories( @ json_decode( $instance['product_cat'] ) );

        include AAPF_TEMPLATE_PATH . "admin.php";
    }

    /**
     * Widget ajax listener
     */
    public static function listener(){
        global $wp_query, $wp_rewrite;
        $br_options = apply_filters( 'berocket_aapf_listener_br_options', get_option('br_filters_options') );

        add_filter( 'post_class', array( __CLASS__, 'add_product_class' ) );
        add_filter( 'woocommerce_pagination_args', array( __CLASS__, 'pagination_args' ) );

        $args = apply_filters( 'berocket_aapf_listener_wp_query_args', array() );

        if( ! isset($args['post__in']) ) {
            $args['post__in'] = array();
        }
        $woocommerce_hide_out_of_stock_items = BeRocket_AAPF_Widget::woocommerce_hide_out_of_stock_items();
        if( $woocommerce_hide_out_of_stock_items == 'yes' ) {
            $args['post__in'] = BeRocket_AAPF::remove_out_of_stock( $args['post__in'] );
        }
        if( ! br_woocommerce_version_check() ) {
            $args['post__in'] = BeRocket_AAPF::remove_hidden( $args['post__in'] );
        }

        $args['post__in'] = BeRocket_AAPF::limits_filter( $args['post__in'] );
        if( isset($_POST['price']) && is_array($_POST['price']) ) {
            $_POST['price'] = apply_filters('berocket_min_max_filter', $_POST['price']);
        }
        $args['post__in'] = BeRocket_AAPF::price_filter( $args['post__in'] );
        $args['post_status'] = 'publish';
        $args['post_type'] = 'product';
        $default_posts_per_page = get_option( 'posts_per_page' );
        $args['posts_per_page'] = apply_filters( 'loop_shop_per_page', $default_posts_per_page );
        unset( $default_posts_per_page );

        if( isset($_POST['product_taxonomy']) && $_POST['product_taxonomy'] != '-1' && strpos( $_POST['product_taxonomy'], '|' ) !== FALSE ) {
            $product_taxonomy = explode( '|', $_POST['product_taxonomy'] );
            $args['taxonomy'] = $product_taxonomy[0];
            $args['term'] = $product_taxonomy[1];
        }
        if( isset($_POST['s']) && strlen($_POST['s']) > 0 ) {
            $args['s'] = $_POST['s'];
        }
        $wp_query = new WP_Query( $args );

        // here we get max products to know if current page is not too big
        if ( $wp_rewrite->using_permalinks() and preg_match( "~/page/([0-9]+)~", $_POST['location'], $mathces ) or preg_match( "~paged?=([0-9]+)~", $_POST['location'], $mathces ) ) {
            $args['paged'] = min( $mathces[1], $wp_query->max_num_pages );
            $wp_query = new WP_Query( $args );
        }
        //echo '<pre>', print_r($wp_query, true), '</pre>';
        unset( $args );

        if( empty($br_options['ajax_request_load']) ) {
            ob_start();

            if ( $wp_query->have_posts() ) {

                do_action('woocommerce_before_shop_loop');

                woocommerce_product_loop_start();
                woocommerce_product_subcategories();

                while ( have_posts() ) {
                    the_post();
                    wc_get_template_part( 'content', 'product' );
                }

                woocommerce_product_loop_end();

                do_action('woocommerce_after_shop_loop');

                wp_reset_postdata();

                $_RESPONSE['products'] = ob_get_contents();
            } else {
                echo apply_filters( 'berocket_aapf_listener_no_products_message', "<div class='no-products" . ( ( $br_options['no_products_class'] ) ? ' '.$br_options['no_products_class'] : '' ) . "'>" . $br_options['no_products_message'] . "</div>" );

                $_RESPONSE['no_products'] = ob_get_contents();
            }
            ob_end_clean();
        }

        echo json_encode( $_RESPONSE );

        die();
    }
    public static function woocommerce_before_main_content() {
        ?>||EXPLODE||<?php
    }
    public static function woocommerce_after_main_content() {
        ?>||EXPLODE||<?php
    }
    public static function pre_get_posts() {
        add_action('woocommerce_before_shop_loop', array( __CLASS__, 'woocommerce_before_main_content' ), 1);
        add_action('woocommerce_after_shop_loop', array( __CLASS__, 'woocommerce_after_main_content' ), 999999);
    }
    public static function end_clean() {
        $_RESPONSE['products'] = explode('||EXPLODE||', ob_get_contents());
        $_RESPONSE['products'] = $_RESPONSE['products'][1];
        ob_end_clean();
        global $wp_query, $wp_rewrite;
        
        if( $_RESPONSE['products'] == null ) {
	        unset( $_RESPONSE['products'] );
	        ob_start();
            $br_options = apply_filters( 'berocket_aapf_listener_br_options', get_option('br_filters_options') );
            echo apply_filters( 'berocket_aapf_listener_no_products_message', "<p class='no-products woocommerce-info" . ( ( $br_options['no_products_class'] ) ? ' '.$br_options['no_products_class'] : '' ) . "'>" . $br_options['no_products_message'] . "</p>" );
            unset( $br_options );
            $_RESPONSE['no_products'] = ob_get_contents();
            ob_end_clean();
        } else {
            $_RESPONSE['products'] = str_replace( 'explode=explode#038;', '', $_RESPONSE['products'] );
            $_RESPONSE['products'] = str_replace( '&#038;explode=explode', '', $_RESPONSE['products'] );
            $_RESPONSE['products'] = str_replace( '?explode=explode', '', $_RESPONSE['products'] );
        }
        echo json_encode( $_RESPONSE );

        die();
    }
    public static function start_clean() {
        ob_start();
    }

    function get_product_categories( $current_product_cat = '', $parent = 0, $data = array(), $depth = 0 ) {
        $args = array(
            'pad_counts'         => 1,
            'show_counts'        => 1,
            'hierarchical'       => 0,
            'hide_empty'         => 0,
            'show_uncategorized' => 0,
            'parent'             => $parent,
            'selected'           => $current_product_cat,
            'menu_order'         => false
        );

        $product_cats = get_terms( 'product_cat', apply_filters( 'wc_product_dropdown_categories_get_terms_args', $args ) );
        unset( $args );
        if ( ! empty( $product_cats ) ) {
            foreach ( $product_cats as $single_cat ) {
                $single_cat->depth = $depth;
                $data[] = $single_cat;
                $data = self::get_product_categories( $current_product_cat, $single_cat->term_id, $data, $depth + 1 );
            }
        }

        return $data;
    }

    public static function add_product_class( $classes ) {
        $classes[] = 'product';
        return apply_filters( 'berocket_aapf_add_product_class', $classes );
    }

    public static function pagination_args( $args = array() ) {
        if ( defined('DOING_AJAX') && DOING_AJAX ) {
            $args['base'] = str_replace( 999999999, '%#%', self::get_pagenum_link( 999999999 ) );
        }
        return $args;
    }

    // 99% copy of WordPress' get_pagenum_link.
    public static function get_pagenum_link($pagenum = 1, $escape = true ) {
        global $wp_rewrite;
        if( empty( $_POST['location'] ) ) {
            $_POST['location'] = '';
        }

        $pagenum = (int) $pagenum;

        $request = remove_query_arg( 'paged', preg_replace( "~".home_url()."~", "", $_POST['location'] ) );

        $home_root = parse_url(home_url());
        $home_root = ( isset($home_root['path']) ) ? $home_root['path'] : '';
        $home_root = preg_quote( $home_root, '|' );

        $request = preg_replace('|^'. $home_root . '|i', '', $request);
        unset( $home_root );
        $request = preg_replace('|^/+|', '', $request);

        if ( !$wp_rewrite->using_permalinks() ) {
            $base = trailingslashit( get_bloginfo( 'url' ) );

            if ( $pagenum > 1 ) {
                $result = add_query_arg( 'paged', $pagenum, $base . $request );
            } else {
                $result = $base . $request;
            }
        } else {
            $qs_regex = '|\?.*?$|';
            preg_match( $qs_regex, $request, $qs_match );

            if ( !empty( $qs_match[0] ) ) {
                $query_string = $qs_match[0];
                $request = preg_replace( $qs_regex, '', $request );
            } else {
                $query_string = '';
            }

            $request = preg_replace( "|$wp_rewrite->pagination_base/\d+/?$|", '', $request);
            $request = preg_replace( '|^' . preg_quote( $wp_rewrite->index, '|' ) . '|i', '', $request);
            $request = ltrim($request, '/');

            $base = trailingslashit( get_bloginfo( 'url' ) );

            if ( $wp_rewrite->using_index_permalinks() && ( $pagenum > 1 || '' != $request ) )
                $base .= $wp_rewrite->index . '/';

            if ( $pagenum > 1 ) {
                $request = ( ( !empty( $request ) ) ? trailingslashit( $request ) : $request ) . user_trailingslashit( $wp_rewrite->pagination_base . "/" . $pagenum, 'paged' );
            }

            $result = $base . $request . $query_string;
        }

        /**
         * Filter the page number link for the current request.
         *
         * @since 2.5.0
         *
         * @param string $result The page number link.
         */
        $result = apply_filters( 'get_pagenum_link', $result );

        if ( $escape )
            return esc_url( $result );
        else
            return esc_url_raw( $result );
    }
}
