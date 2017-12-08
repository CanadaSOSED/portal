<?php

if (!defined('ABSPATH'))
    exit;

/**
 * @class 		WCMp Shortcode Class
 *
 * @version	  2.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
class WCMp_Shortcode {

    public $list_product;

    public function __construct() {
        //new vendor dashboard
        add_shortcode('wcmp_vendor', array(&$this, 'wcmp_vendor_dashboard_shortcode'));
        
        //Vendor Registration
        add_shortcode('vendor_registration', array(&$this, 'wcmp_vendor_registration_shortcode'));
        
        // Vendor Coupons
        add_shortcode('vendor_coupons', array(&$this, 'vendor_coupons_shortcode'));
        
        // Recent Products 
        add_shortcode('wcmp_recent_products', array(&$this, 'wcmp_show_recent_products'));
        
        // Products by vendor
        add_shortcode('wcmp_products', array(&$this, 'wcmp_show_products'));
        
        //Featured products by vendor
        add_shortcode('wcmp_featured_products', array(&$this, 'wcmp_show_featured_products'));
        
        // Sale products by vendor
        add_shortcode('wcmp_sale_products', array(&$this, 'wcmp_show_sale_products'));
        
        // Top Rated products by vendor 
        add_shortcode('wcmp_top_rated_products', array(&$this, 'wcmp_show_top_rated_products'));
        
        // Best Selling product 
        add_shortcode('wcmp_best_selling_products', array(&$this, 'wcmp_show_best_selling_products'));
        
        // List products in a category shortcode
        add_shortcode('wcmp_product_category', array(&$this, 'wcmp_show_product_category'));
        
        // List of paginated vendors 
        add_shortcode('wcmp_vendorslist', array(&$this, 'wcmp_show_vendorslist'));
    }
    /**
     * Render vendor dashboard
     * @return object
     */
    public static function wcmp_vendor_dashboard_shortcode() {
        self::load_class('vendor-dashboard');
        return self::shortcode_wrapper(array('WCMp_Vendor_Dashboard_Shortcode', 'output'));
    }
    /**
     * Render Vendor Registration page
     * @return object
     */
    public static function wcmp_vendor_registration_shortcode() {
        self::load_class('vendor-registration');
        return self::shortcode_wrapper(array('WCMp_Vendor_Registration_Shortcode', 'output'));
    }
    
    /**
     * vendor orer detail
     *
     * @return void
     */
    public static function vendor_coupons_shortcode($attr) {
        self::load_class('vendor-used-coupon');
        return self::shortcode_wrapper(array('WCMp_Vendor_Coupon_Shortcode', 'output'));
    }
    
    /**
     * Helper Functions
     * Shortcode Wrapper
     * 
     * @access public
     * @param mixed $function
     * @param array $atts (default: array())
     * @return string
     */
    public static function shortcode_wrapper($function, $atts = array()) {
        ob_start();
        call_user_func($function, $atts);
        return ob_get_clean();
    }

    /**
     * Shortcode CLass Loader
     *
     * @access public
     * @param mixed $class_name
     * @return void
     */
    public static function load_class($class_name = '') {
        global $WCMp;
        if ('' != $class_name && '' != $WCMp->token) {
            require_once ('shortcode/class-' . esc_attr($WCMp->token) . '-shortcode-' . esc_attr($class_name) . '.php');
        }
    }

    /**
     * get vendor
     *
     * @return void
     */
    public static function get_vendor($slug) {

        $vendor_id = get_user_by('slug', $slug);

        if (!empty($vendor_id)) {
            $author = $vendor_id->ID;
        } else {
            $author = '';
        }

        return $author;
    }

    /**
     * list all recent products
     *
     * @return void
     */
    public static function wcmp_show_recent_products($atts) {
        global $woocommerce_loop, $WCMp;

        extract(shortcode_atts(array(
            'per_page' => '12',
            'vendor' => '',
            'columns' => '4',
            'orderby' => 'date',
            'order' => 'desc'
                        ), $atts));

        $meta_query = WC()->query->get_meta_query();

        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => $per_page,
            'orderby' => $orderby,
            'order' => $order,
            'meta_query' => $meta_query
        );
        if (!empty($vendor)) {
            $user = get_user_by('login', $vendor);
        }

        if (!empty($vendor)) {
            $term_id = get_user_meta($user->ID, '_vendor_term_id', true);
            $args['tax_query'][] = array(
                'taxonomy' => $WCMp->taxonomy->taxonomy_name,
                'field' => 'term_id',
                'terms' => $term_id
            );
        }

        ob_start();

        $products = new WP_Query(apply_filters('woocommerce_shortcode_products_query', $args, $atts));

        $woocommerce_loop['columns'] = $columns;

        if ($products->have_posts()) :
            ?>

            <?php woocommerce_product_loop_start(); ?>

            <?php while ($products->have_posts()) : $products->the_post(); ?>

                <?php wc_get_template_part('content', 'product'); ?>

            <?php endwhile; // end of the loop.  ?>

            <?php woocommerce_product_loop_end(); ?>

            <?php

        endif;

        wp_reset_postdata();

        return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
    }

    /**
     * list all products
     *
     * @access public
     * @param array $atts
     * @return string
     */
    public static function wcmp_show_products($atts) {
        global $woocommerce_loop, $WCMp;

        if (empty($atts))
            return '';

        extract(shortcode_atts(array(
            'id' => '',
            'vendor' => '',
            'columns' => '4',
            'orderby' => 'title',
            'order' => 'asc'
                        ), $atts));

        if (!empty($vendor)) {
            $user = get_user_by('login', $vendor);
        }

        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'orderby' => $orderby,
            'order' => $order,
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_visibility',
                    'value' => array('catalog', 'visible'),
                    'compare' => 'IN'
                )
            )
        );

        if (!empty($vendor) && !empty($user)) {
            $term_id = get_user_meta($user->ID, '_vendor_term_id', true);
            $args['tax_query'][] = array(
                'taxonomy' => $WCMp->taxonomy->taxonomy_name,
                'field' => 'term_id',
                'terms' => $term_id
            );
        } else if (!empty($id)) {
            $term_id = get_user_meta($id, '_vendor_term_id', true);
            $args['tax_query'][] = array(
                'taxonomy' => $WCMp->taxonomy->taxonomy_name,
                'field' => 'term_id',
                'terms' => $term_id
            );
        }

        if (isset($atts['skus'])) {
            $skus = explode(',', $atts['skus']);
            $skus = array_map('trim', $skus);
            $args['meta_query'][] = array(
                'key' => '_sku',
                'value' => $skus,
                'compare' => 'IN'
            );
        }

        if (isset($atts['ids'])) {
            $ids = explode(',', $atts['ids']);
            $ids = array_map('trim', $ids);
            $args['post__in'] = $ids;
        }


        ob_start();

        $products = new WP_Query(apply_filters('woocommerce_shortcode_products_query', $args, $atts));


        $woocommerce_loop['columns'] = $columns;

        if ($products->have_posts()) :
            ?>

            <?php woocommerce_product_loop_start(); ?>

            <?php while ($products->have_posts()) : $products->the_post(); ?>

                <?php wc_get_template_part('content', 'product'); ?>

            <?php endwhile; // end of the loop.  ?>

            <?php woocommerce_product_loop_end(); ?>

            <?php

        endif;

        wp_reset_postdata();

        return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
    }

    /*
     * list vendor recent products
     *
     * @access public
     * @param array $atts
     * @return string
     */

    public static function wcmp_recent_products($atts) {
        global $woocommerce_loop, $WCMp;

        if (empty($atts))
            return '';

        extract(shortcode_atts(array(
            'id' => '',
            'vendor' => '',
            'count' => get_option('posts_per_page'),
            'columns' => '4',
            'orderby' => 'date',
            'order' => 'DESC'
                        ), $atts));

        if (!empty($vendor)) {
            $user = get_user_by('login', $vendor);
        }
        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'orderby' => $orderby,
            'order' => $order,
            'posts_per_page' => $count,
            'meta_query' => array(
                array(
                    'key' => '_visibility',
                    'value' => array('catalog', 'visible'),
                    'compare' => 'IN'
                )
            )
        );

        if (!empty($vendor) && !empty($user)) {
            $term_id = get_user_meta($user->ID, '_vendor_term_id', true);
            $args['tax_query'][] = array(
                'taxonomy' => $WCMp->taxonomy->taxonomy_name,
                'field' => 'term_id',
                'terms' => $term_id
            );
        } else if (!empty($id)) {
            $term_id = get_user_meta($id, '_vendor_term_id', true);
            $args['tax_query'][] = array(
                'taxonomy' => $WCMp->taxonomy->taxonomy_name,
                'field' => 'term_id',
                'terms' => $term_id
            );
        }

        if (isset($atts['skus'])) {
            $skus = explode(',', $atts['skus']);
            $skus = array_map('trim', $skus);
            $args['meta_query'][] = array(
                'key' => '_sku',
                'value' => $skus,
                'compare' => 'IN'
            );
        }

        if (isset($atts['ids'])) {
            $ids = explode(',', $atts['ids']);
            $ids = array_map('trim', $ids);
            $args['post__in'] = $ids;
        }

        ob_start();

        $products = new WP_Query(apply_filters('woocommerce_shortcode_recent_products_query', $args, $atts));


        $woocommerce_loop['columns'] = $columns;

        if ($products->have_posts()) :
            ?>

            <?php woocommerce_product_loop_start(); ?>

            <?php while ($products->have_posts()) : $products->the_post(); ?>

                <?php wc_get_template_part('content', 'product'); ?>

            <?php endwhile; // end of the loop.  ?>

            <?php woocommerce_product_loop_end(); ?>

            <?php

        endif;

        wp_reset_postdata();

        return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
    }

    /**
     * list all featured products
     *
     * @access public
     * @param array $atts
     * @return string
     */
    public static function wcmp_show_featured_products($atts) {
        global $woocommerce_loop, $WCMp;

        extract(shortcode_atts(array(
            'vendor' => '',
            'per_page' => '12',
            'columns' => '4',
            'orderby' => 'date',
            'order' => 'desc'
                        ), $atts));

        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => $per_page,
            'orderby' => $orderby,
            'order' => $order,
            'meta_query' => array(
                array(
                    'key' => '_visibility',
                    'value' => array('catalog', 'visible'),
                    'compare' => 'IN'
                ),
                array(
                    'key' => '_featured',
                    'value' => 'yes'
                )
            )
        );
        if (!empty($vendor)) {
            $user = get_user_by('login', $vendor);
        }
        if (!empty($vendor)) {
            $term_id = get_user_meta($user->ID, '_vendor_term_id', true);
            $args['tax_query'][] = array(
                'taxonomy' => $WCMp->taxonomy->taxonomy_name,
                'field' => 'term_id',
                'terms' => $term_id
            );
        }

        ob_start();

        $products = new WP_Query(apply_filters('woocommerce_shortcode_products_query', $args, $atts));

        $woocommerce_loop['columns'] = $columns;

        if ($products->have_posts()) :
            ?>

            <?php woocommerce_product_loop_start(); ?>

            <?php while ($products->have_posts()) : $products->the_post(); ?>

                <?php wc_get_template_part('content', 'product'); ?>

            <?php endwhile; // end of the loop.  ?>

            <?php woocommerce_product_loop_end(); ?>

            <?php

        endif;

        wp_reset_postdata();

        return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
    }

    /**
     * List all products on sale
     *
     * @access public
     * @param array $atts
     * @return string
     */
    public static function wcmp_show_sale_products($atts) {
        global $woocommerce_loop, $WCMp;

        extract(shortcode_atts(array(
            'vendor' => '',
            'per_page' => '12',
            'columns' => '4',
            'orderby' => 'title',
            'order' => 'asc'
                        ), $atts));

        // Get products on sale
        $product_ids_on_sale = wc_get_product_ids_on_sale();

        $meta_query = array();
        $meta_query[] = WC()->query->visibility_meta_query();
        $meta_query[] = WC()->query->stock_status_meta_query();
        $meta_query = array_filter($meta_query);

        $args = array(
            'posts_per_page' => $per_page,
            'orderby' => $orderby,
            'order' => $order,
            'no_found_rows' => 1,
            'post_status' => 'publish',
            'post_type' => 'product',
            'meta_query' => $meta_query,
            'post__in' => array_merge(array(0), $product_ids_on_sale)
        );
        if (!empty($vendor)) {
            $user = get_user_by('login', $vendor);
        }

        if (!empty($vendor)) {
            $term_id = get_user_meta($user->ID, '_vendor_term_id', true);
            $args['tax_query'][] = array(
                'taxonomy' => $WCMp->taxonomy->taxonomy_name,
                'field' => 'term_id',
                'terms' => $term_id
            );
        }
        ob_start();

        $products = new WP_Query(apply_filters('woocommerce_shortcode_products_query', $args, $atts));

        $woocommerce_loop['columns'] = $columns;

        if ($products->have_posts()) :
            ?>

            <?php woocommerce_product_loop_start(); ?>

            <?php while ($products->have_posts()) : $products->the_post(); ?>

                <?php wc_get_template_part('content', 'product'); ?>

            <?php endwhile; // end of the loop.  ?>

            <?php woocommerce_product_loop_end(); ?>

            <?php

        endif;

        wp_reset_postdata();

        return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
    }

    /**
     * List top rated products on sale by vendor
     *
     * @access public
     * @param array $atts
     * @return string
     */
    public static function wcmp_show_top_rated_products($atts) {
        global $woocommerce_loop, $WCMp;

        extract(shortcode_atts(array(
            'vendor' => '',
            'per_page' => '12',
            'columns' => '4',
            'orderby' => 'title',
            'order' => 'asc'
                        ), $atts));

        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'orderby' => $orderby,
            'order' => $order,
            'posts_per_page' => $per_page,
            'meta_query' => array(
                array(
                    'key' => '_visibility',
                    'value' => array('catalog', 'visible'),
                    'compare' => 'IN'
                )
            )
        );
        if (!empty($vendor)) {
            $user = get_user_by('login', $vendor);
        }

        if (!empty($vendor)) {
            $term_id = get_user_meta($user->ID, '_vendor_term_id', true);
            $args['tax_query'][] = array(
                'taxonomy' => $WCMp->taxonomy->taxonomy_name,
                'field' => 'term_id',
                'terms' => $term_id
            );
        }

        ob_start();

        add_filter('posts_clauses', array('WC_Shortcodes', 'order_by_rating_post_clauses'));

        $products = new WP_Query(apply_filters('woocommerce_shortcode_products_query', $args, $atts));

        remove_filter('posts_clauses', array('WC_Shortcodes', 'order_by_rating_post_clauses'));

        $woocommerce_loop['columns'] = $columns;

        if ($products->have_posts()) :
            ?>

            <?php woocommerce_product_loop_start(); ?>

            <?php while ($products->have_posts()) : $products->the_post(); ?>

                <?php wc_get_template_part('content', 'product'); ?>

            <?php endwhile; // end of the loop.  ?>

            <?php woocommerce_product_loop_end(); ?>

            <?php

        endif;

        wp_reset_postdata();

        return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
    }

    /**
     * List best selling products on sale per vendor
     *
     * @access public
     * @param array $atts
     * @return string
     */
    public static function wcmp_show_best_selling_products($atts) {
        global $woocommerce_loop, $WCMp;

        extract(shortcode_atts(array(
            'vendor' => '',
            'per_page' => '12',
            'columns' => '4'
                        ), $atts));

        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => $per_page,
            'meta_key' => 'total_sales',
            'orderby' => 'meta_value_num',
            'meta_query' => array(
                array(
                    'key' => '_visibility',
                    'value' => array('catalog', 'visible'),
                    'compare' => 'IN'
                )
            )
        );

        if (!empty($vendor)) {
            $user = get_user_by('login', $vendor);
        }

        if (!empty($vendor)) {
            $term_id = get_user_meta($user->ID, '_vendor_term_id', true);
            $args['tax_query'][] = array(
                'taxonomy' => $WCMp->taxonomy->taxonomy_name,
                'field' => 'term_id',
                'terms' => $term_id
            );
        }



        ob_start();

        $products = new WP_Query(apply_filters('woocommerce_shortcode_products_query', $args, $atts));

        $woocommerce_loop['columns'] = $columns;

        if ($products->have_posts()) :
            ?>

            <?php woocommerce_product_loop_start(); ?>

            <?php while ($products->have_posts()) : $products->the_post(); ?>

                <?php wc_get_template_part('content', 'product'); ?>

            <?php endwhile; // end of the loop.  ?>

            <?php woocommerce_product_loop_end(); ?>

            <?php

        endif;

        wp_reset_postdata();

        return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
    }

    /**
     * List products in a category shortcode
     *
     * @access public
     * @param array $atts
     * @return string
     */
    public static function wcmp_show_product_category($atts) {
        global $woocommerce_loop, $WCMp;

        extract(shortcode_atts(array(
            'vendor' => '',
            'per_page' => '12',
            'columns' => '4',
            'orderby' => 'title',
            'order' => 'desc',
            'category' => '', // Slugs
            'operator' => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
                        ), $atts));

        if (!$category) {
            return '';
        }

        // Default ordering args
        $ordering_args = WC()->query->get_catalog_ordering_args($orderby, $order);
        $meta_query = WC()->query->get_meta_query();
        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'orderby' => $ordering_args['orderby'],
            'order' => $ordering_args['order'],
            'posts_per_page' => $per_page,
            'meta_query' => $meta_query,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'terms' => array_map('sanitize_title', explode(',', $category)),
                    'field' => 'slug',
                    'operator' => $operator
                )
            )
        );

        if (!empty($vendor)) {
            $user = get_user_by('login', $vendor);
        }

        if (!empty($vendor)) {
            $term_id = get_user_meta($user->ID, '_vendor_term_id', true);
            $args['tax_query'][] = array(
                'taxonomy' => $WCMp->taxonomy->taxonomy_name,
                'field' => 'term_id',
                'terms' => $term_id
            );
        }



        if (isset($ordering_args['meta_key'])) {
            $args['meta_key'] = $ordering_args['meta_key'];
        }

        ob_start();

        $products = new WP_Query(apply_filters('woocommerce_shortcode_products_query', $args, $atts));

        $woocommerce_loop['columns'] = $columns;

        if ($products->have_posts()) :
            ?>

            <?php woocommerce_product_loop_start(); ?>

            <?php while ($products->have_posts()) : $products->the_post(); ?>

                <?php wc_get_template_part('content', 'product'); ?>

            <?php endwhile; // end of the loop.  ?>

            <?php woocommerce_product_loop_end(); ?>

            <?php

        endif;

        woocommerce_reset_loop();
        wp_reset_postdata();

        $return = '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';

        // Remove ordering query arguments
        WC()->query->remove_ordering_args();

        return $return;
    }

    /**
     * 	list of vendors 
     * 
     * 	@param $atts shortcode attributs 
     */
    public static function wcmp_show_vendorslist($atts) {
        global $WCMp;
        self::load_class('vendor-list');
        return self::shortcode_wrapper(array('WCMp_Shortcode_Vendor_List', 'output'), $atts);
    }

}