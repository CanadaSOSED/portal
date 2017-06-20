<?php

if( ! function_exists( 'br_get_woocommerce_version' ) ){
    /**
     * Public function to get WooCommerce version
     *
     * @return float|NULL
     */
    function br_get_woocommerce_version() {
        if ( ! function_exists( 'get_plugins' ) )
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
        $plugin_folder = get_plugins( '/' . 'woocommerce' );
        $plugin_file = 'woocommerce.php';

        if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
            return $plugin_folder[$plugin_file]['Version'];
        } else {
            return NULL;
        }
    }
}
if( ! function_exists( 'br_woocommerce_version_check' ) ){
    function br_woocommerce_version_check( $version = '2.7' ) {
        $wc_version = br_get_woocommerce_version();
        if( $wc_version !== NULL ) {
            if( version_compare( $wc_version, $version, ">=" ) ) {
                return true;
            }
        }
        return false;
    }
}

if( ! function_exists( 'br_get_template_part' ) ){
    /**
     * Public function to get plugin's template
     *
     * @param string $name Template name to search for
     *
     * @return void
     */
    function br_get_template_part( $name = '' ){
        BeRocket_AAPF::br_get_template_part( $name );
    }
}

if( ! function_exists( 'br_aapf_get_attributes' ) ) {
    /**
     * Get all possible woocommerce attribute taxonomies
     *
     * @return mixed|void
     */
    function br_aapf_get_attributes() {
        $attribute_taxonomies = wc_get_attribute_taxonomies();
        $attributes           = array();

        if ( $attribute_taxonomies ) {
            foreach ( $attribute_taxonomies as $tax ) {
                $attributes[ wc_attribute_taxonomy_name( $tax->attribute_name ) ] = $tax->attribute_label;
            }
        }

        return apply_filters( 'berocket_aapf_get_attributes', $attributes );
    }
}

if( ! function_exists( 'br_parse_order_by' ) ){
    /**
     * br_aapf_parse_order_by - parsing order by data and saving to $args array that was passed into
     *
     * @param $args
     */
    function br_aapf_parse_order_by( &$args ){
        $orderby = $_GET['orderby'] = $_POST['orderby'];
        $order = "ASK";
        if( @ preg_match( "/-/", $orderby ) ){
            list( $orderby, $order ) = explode( "-", $orderby );
        }

        // needed for woocommerce sorting funtionality
        if( ! empty($orderby) and ! empty($order) ) {

            // Get ordering from query string unless defined
            $orderby = strtolower( $orderby );
            $order   = strtoupper( $order );

            // default - menu_order
            $args['orderby']  = 'menu_order title';
            $args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';

            switch ( $orderby ) {
                case 'rand' :
                    $args['orderby']  = 'rand';
                    break;
                case 'date' :
                    $args['orderby']  = 'date';
                    $args['order']    = $order == 'ASC' ? 'ASC' : 'DESC';
                    break;
                case 'price' :
                    $args['orderby']  = 'meta_value_num';
                    $args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
                    $args['meta_key'] = '_price';
                    break;
                case 'popularity' :
                    $args['meta_key'] = 'total_sales';

                    // Sorting handled later though a hook
                    add_filter( 'posts_clauses', array( 'BeRocket_AAPF', 'order_by_popularity_post_clauses' ) );
                    break;
                case 'rating' :
                    // Sorting handled later though a hook
                    add_filter( 'posts_clauses', array( 'BeRocket_AAPF', 'order_by_rating_post_clauses' ) );
                    break;
                case 'title' :
                    $args['orderby']  = 'title';
                    $args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
                    break;
            }
        }
    }
}

if( ! function_exists( 'br_aapf_args_parser' ) ){
    /**
     * br_aapf_args_parser - extend $args based on passed filters
     *
     * @param array $args
     *
     * @return array
     */
    function br_aapf_args_parser( $args = array() ) {
        $attributes_terms = $tax_query = array();
        $attributes       = apply_filters( 'berocket_aapf_listener_get_attributes', br_aapf_get_attributes() );
        $taxonomies       = array();

        if ( ! empty($attributes) ) {
            foreach ( $attributes as $k => $v ) {
                $terms = get_terms( array( $k ), $args = array( 'orderby' => 'name', 'order' => 'ASC', 'fields' => 'id=>slug' ) );
                if ( $terms ) {
                    foreach ( $terms as $term_id => $term_slug ) {
                        $attributes_terms[ $k ][ $term_id ] = $term_slug;
                    }
                }
                unset( $terms );
            }
        }
        unset( $attributes );

        if ( ! empty($_POST['terms']) ) {
            foreach ( $_POST['terms'] as $t ) {
                if( !isset($taxonomies[ $t[0] ]) ){
                    $taxonomies[ $t[0] ] = array();
                }
                $taxonomies[ $t[0] ][]        = @ $attributes_terms[ $t[0] ][ $t[1] ];
                $taxonomies_operator[ $t[0] ] = $t[2];
            }
        }
        unset( $attributes_terms );

        $taxonomies          = apply_filters( 'berocket_aapf_listener_taxonomies', @$taxonomies );
        $taxonomies_operator = apply_filters( 'berocket_aapf_listener_taxonomies_operator', @$taxonomies_operator );

        if ( ! empty($taxonomies) ) {
            $tax_query['relation'] = 'AND';
            if ( $taxonomies ) {
                foreach ( $taxonomies as $k => $v ) {
                    if ( $taxonomies_operator[ $k ] == 'AND' ) {
                        $op = 'AND';
                    } else {
                        $op = 'IN';
                    }

                    $tax_query[] = array(
                        'taxonomy' => $k,
                        'field'    => 'slug',
                        'terms'    => $v,
                        'operator' => $op
                    );
                }
            }
        }

        if ( isset($_POST['product_cat']) and $_POST['product_cat'] != '-1' ) {
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => strip_tags( $_POST['product_cat'] ),
                'operator' => 'IN'
            );
        }

        $args['tax_query'] = $tax_query;
        $args['post_type'] = 'product';

        if ( ! empty($_POST['orderby']) ) {
            br_aapf_parse_order_by( $args );
        }

        return $args;
    }
}

if( ! function_exists( 'br_aapf_args_converter' ) ) {
    /**
     * convert args-url to normal filters
     */
    function br_aapf_args_converter() {
        if ( preg_match( "~\|~", $_GET['filters'] ) ) {
            $filters = explode( "|", $_GET['filters'] );
        } else {
            $filters[0] = $_GET['filters'];
        }

        foreach ( $filters as $filter ) {

            if ( preg_match( "~\[~", $filter ) ) {
                list( $attribute, $value ) = explode( "[", trim( preg_replace( "~\]~", "", $filter) ), 2 );
                if ( preg_match( "~\-~", $value ) ) {
                    $value    = explode( "-", $value );
                    $operator = 'OR';
                } elseif ( preg_match( "~\_~", $value ) ) {
                    list( $min, $max ) = explode( "_", $value );
                    $operator = '';
                } else {
                    $value    = explode( " ", $value );
                    $operator = 'AND';
                }
            }else{
                list( $attribute, $value ) = explode( "-", $filter, 2 );
            }

            if ( $attribute == 'price' ) {
                $_POST['price'] = apply_filters('berocket_min_max_filter', array( $min, $max ));
            } elseif ( $attribute == 'order' ) {
                $_GET['orderby'] = $value;
            } else {
                if ( $operator ) {
                    foreach ( $value as $v ) {
                        $_POST['terms'][] = array( "pa_" . $attribute, $v, $operator );
                    }
                } else {
                    $_POST['limits'][] = array( "pa_" . $attribute, $min, $max );
                }
            }
        }
    }
}
