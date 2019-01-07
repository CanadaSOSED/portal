<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handle article front-end setup
 *
 */
class EPKB_Articles_Setup {

	private $cached_comments_flag;

	public function __construct() {
		add_filter( 'comments_open', array( $this, 'setup_comments'), 1, 2 );
	}

    /**
     * Output SBL + article
     *
     * @param $article_content - article + features
     * @param $kb_config
     * @param bool $is_builder_on
     * @param array $article_seq
     * @param array $categories_seq
     * @return string
     */
    public static function output_article_page_with_layout( $article_content, $kb_config, $is_builder_on=false, $article_seq=array(), $categories_seq=array() ) {

        // get Article Page Layout
        ob_start();
        apply_filters( 'epkb_article_page_layout_output', $article_content, $kb_config, $is_builder_on, $article_seq, $categories_seq );
        $layout_output = ob_get_clean();

        // if no layout found then just display the article
        if ( empty($layout_output) ) {
            $layout_output = $article_content;
        }

        return $layout_output;
	}

    /**
     * Return single article content surrounded by features like breadcrumb and tags.
     *
     * NOTE: Assumes shortcodes already ran.
     *
     * @param $article
     * @param $content
     * @param $kb_config - front end or back end temporary KB config
     * @return string
     */
	public static function get_article_content_and_features( $article, $content, $kb_config ) {

		global $epkb_password_checked;

		if ( empty($epkb_password_checked) && post_password_required() ) {
			return get_the_password_form();
		}
		
        // if global post is empty initialize it
        if ( empty($GLOBALS['post']) ) {
            $GLOBALS['post'] = $article;
        }

        // if necessary get KB configuration
        if ( empty($kb_config) ) {
            $kb_id = EPKB_KB_Handler::get_kb_id_from_post_type( $article->post_type );
            if ( is_wp_error($kb_id) ) {
                $kb_id = EPKB_KB_Config_DB::DEFAULT_KB_ID;
            }

            // initialize KB config to be accessible to templates
            $kb_config = epkb_get_instance()->kb_config_obj->get_kb_config_or_default( $kb_id );
        }

        self::setup_article_content_hooks( $kb_config );

        $article_page_container_classes = apply_filters( 'eckb-article-page-container-classes', array(), $kb_config['id'] );
        $article_page_container_classes = isset($article_page_container_classes) && is_array($article_page_container_classes) ? $article_page_container_classes : array();

		ob_start();		?>

        <div id="eckb-article-page-container" class="<?php echo implode(" ", $article_page_container_classes); ?>" >    <?php

            self::article_section( 'eckb-article-header', array( 'id' => $kb_config['id'], 'config' => $kb_config, 'article' => $article ) ); ?>

            <div id="eckb-article-body">  <?php

                self::article_section( 'eckb-article-left-sidebar', array( 'id' => $kb_config['id'], 'config' => $kb_config, 'article' => $article ) ); ?>

                <div id="eckb-article-content">                        <?php

                    self::article_section( 'eckb-article-content-header', array( 'id' => $kb_config['id'], 'config' => $kb_config, 'article' => $article ) );
                    self::article_section( 'eckb-article-content-body',   array( 'id' => $kb_config['id'], 'config' => $kb_config, 'article' => $article, 'content' => $content ) );
                    self::article_section( 'eckb-article-content-footer', array( 'id' => $kb_config['id'], 'config' => $kb_config, 'article' => $article ) );                        ?>

                </div><!-- /#eckb-article-content -->     <?php

                self::article_section( 'eckb-article-right-sidebar', array( 'id' => $kb_config['id'], 'config' => $kb_config, 'article' => $article ) ); ?>

            </div><!-- /#eckb-article-body -->              <?php

            self::article_section( 'eckb-article-footer', array( 'id' => $kb_config['id'], 'config' => $kb_config, 'article' => $article ) ); ?>

        </div><!-- /#eckb-article-page-container -->        <?php

		$article_content = ob_get_clean();

        return str_replace( ']]>', ']]&gt;', $article_content );
	}

    /**
     * Call all hooks for given article section.
     *
     * @param $hook - both hook name and div id
     * @param $args
     */
	public static function article_section( $hook, $args ) {
        echo '<div id="' . $hook . '">';
        do_action( $hook, $args );
        echo '</div>';
	}

    private static function setup_article_content_hooks( $kb_config ) {
        add_action( 'eckb-article-content-header', array('EPKB_Articles_Setup', 'article_title'), 10, 3 );

        if ( $kb_config[ 'last_udpated_on'] == 'article_top' ) {
            add_action( 'eckb-article-content-header', array('EPKB_Articles_Setup', 'last_updated_on'), 10, 3 );
        }

        add_action( 'eckb-article-content-header', array('EPKB_Articles_Setup', 'breadcrumbs'), 10, 3 );
        add_action( 'eckb-article-content-header', array('EPKB_Articles_Setup', 'navigation'), 10, 3 );

        add_action( 'eckb-article-content-body', array('EPKB_Articles_Setup', 'article_content'), 10, 4 );

        if ( $kb_config[ 'last_udpated_on'] == 'article_bottom' ) {
            add_action( 'eckb-article-content-footer', array('EPKB_Articles_Setup', 'last_updated_on'), 10, 3 );
        }

        add_action( 'eckb-article-footer', array('EPKB_Articles_Setup', 'tags'), 10, 3 );
        add_action( 'eckb-article-footer', array('EPKB_Articles_Setup', 'comments'), 10, 3 );
    }

    // ARTICLE TITLE
    public static function article_title( $args ) {
        $show_title = $args['config']['templates_for_kb'] == 'kb_templates';
        $article_title = $show_title ? get_the_title( $args['article'] ) : '';

        $tag = $show_title ? 'h1' : 'div';
        $article_seq_no = empty($_REQUEST['seq_no']) ? '' : EPKB_Utilities::sanitize_int( $_REQUEST['seq_no'] );
        $article_seq_no = empty($article_seq_no) ? '' : ' data-kb_article_seq_no=' . $article_seq_no;
        echo '<' . $tag . ' class="eckb-article-title kb-article-id" id="' . $args['article']->ID . '"' . $article_seq_no . '>' . $article_title . '</' . $tag . '>';
    }

    // LAST UPDATED ON
    public static function last_updated_on( $args ) {
        echo '<div class="eckb-last-update">' . esc_html( $args['config']['last_udpated_on_text'] ) . ' ' . EPKB_Utilities::get_formatted_datetime_string( $args['article']->post_modified, 'F d, Y' ) . '</div>';
    }

    // BREADCRUMB
    public static function breadcrumbs( $args ) {
        if ( $args['config'][ 'breadcrumb_toggle'] == 'on' ) {
            EPKB_Templates::get_template_part( 'feature', 'breadcrumb', $args['config'], $args['article'] );
        };
    }

    // BACK NAVIGATION
    public static function navigation( $args ) {
        if ( $args['config'][ 'back_navigation_toggle'] == 'on' ) {
            EPKB_Templates::get_template_part( 'feature', 'navigation-back', $args['config'], $args['article'] );
        }
    }

    // ARTICLE CONTENT
    public static function article_content( $args ) {
        echo '<div id="kb-article-content">';
        echo $args['content'];
        echo '</div>';
    }

    // TAGS
    public static function tags( $args ) {
        EPKB_Templates::get_template_part( 'feature', 'tags', $args['config'], $args['article'] );
    }


    // COMMENTS
    public static function comments( $args ) {
        // only show if using our KB template as theme templates display comments
        if ( $args['config'][ 'templates_for_kb' ] == 'kb_templates' && ! self::is_demo_article( $args['article'] ) ) {
            EPKB_Templates::get_template_part( 'feature', 'comments', array(), $args['article'] );
        }
    }

	/**
	 * Disable comments.
	 * Enable comments but it is up to WP, article and theme settings whether comments are actually displayed.
	 *
	 * @param $open
	 * @param $post_id
	 * @return bool
	 */
	public function setup_comments( $open, $post_id ) {

        global $eckb_kb_id;

		// verify it is a KB article
		$post = get_post();
		if ( empty($post) || ! $post instanceof WP_Post || ( ! EPKB_KB_Handler::is_kb_post_type( $post->post_type ) && empty($eckb_kb_id) ) ) {
			return $open;
		}

		$kb_id = empty($eckb_kb_id) ? EPKB_KB_Handler::get_kb_id_from_post_type( $post->post_type ) : $eckb_kb_id;
		if ( is_wp_error($kb_id) ) {
			return $open;
		}

		if ( empty($this->cached_comments_flag) ) {
			$this->cached_comments_flag = epkb_get_instance()->kb_config_obj->get_value( 'articles_comments_global', $kb_id, 'off' );
		}

		return 'on' == $this->cached_comments_flag;
	}

    private static function is_demo_article( $article ) {
        return empty($article->ID) || empty($GLOBALS['post']) || empty($GLOBALS['post']->ID);
    }
}