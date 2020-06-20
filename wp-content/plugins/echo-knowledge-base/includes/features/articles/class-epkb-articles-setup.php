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

		// setup article structure - either old version 1 or new version 2
		self::setup_article_content_hooks( $kb_config );
		
        $article_page_container_classes = apply_filters( 'eckb-article-page-container-classes', array(), $kb_config['id'], $kb_config );  // used for old Widgets KB Sidebar
        $article_page_container_classes = isset($article_page_container_classes) && is_array($article_page_container_classes) ? $article_page_container_classes : array();

		if ( $kb_config['article-left-sidebar-match'] == 'on' ) {
			$article_page_container_classes[] = 'eckb-article-page--L-sidebar-to-content';
		}
		
		if ( $kb_config['article-right-sidebar-match'] == 'on' ) {
			$article_page_container_classes[] = 'eckb-article-page--R-sidebar-to-content';
		}
		
		ob_start();

		// Article Structure - V1 or V2
		$article_container_structure_version = 'eckb-article-page-container';
		if ( self::is_article_structure_v2( $kb_config ) ) {
			$article_container_structure_version = 'eckb-article-page-container-v2';
			
			if ( !empty( $kb_config['theme_name'] ) ) {
				$article_page_container_classes[] = 'eckb-theme-' . $kb_config['theme_name'];
			}
			
			self::generate_article_structure_css_v2( $kb_config );
		}				?>

        <div id="<?php echo $article_container_structure_version; ?>" class="<?php echo implode(" ", $article_page_container_classes); ?>" >    <?php

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

	/**
	 * REGISTER all article hooks we need
	 *
	 * @param $kb_config
	 */
    private static function setup_article_content_hooks( $kb_config ) {

	    // HEADER
	    add_action( 'eckb-article-header', array('EPKB_Articles_Setup', 'search_box') );

    	// CONTENT HEADER
        add_action( 'eckb-article-content-header', array('EPKB_Articles_Setup', 'article_title'), 9, 3 );
        add_action( 'eckb-article-content-header', array('EPKB_Articles_Setup', 'meta_container_header'), 9, 3 );
        add_action( 'eckb-article-content-header', array('EPKB_Articles_Setup', 'breadcrumbs'), 9, 3 );
        add_action( 'eckb-article-content-header', array('EPKB_Articles_Setup', 'navigation'), 9, 3 );

	    // BODY
        add_action( 'eckb-article-content-body', array('EPKB_Articles_Setup', 'article_content'), 10, 4 );

	    $sidebar_priority = EPKB_KB_Config_Specs::add_sidebar_component_priority_defaults( $kb_config['article_sidebar_component_priority'] );

	    // KB widgets sidebar for V2
	    if ( self::is_article_structure_v2( $kb_config ) ) {
		    if ( $sidebar_priority['kb_sidebar_left'] ) {
			    add_action( 'eckb-article-left-sidebar', array('EPKB_Articles_Setup', 'display_article_sidebar'), 10 * $sidebar_priority['kb_sidebar_left']);
		    } else if ( $sidebar_priority['kb_sidebar_right'] ) {
			    add_action( 'eckb-article-right-sidebar', array('EPKB_Articles_Setup', 'display_article_sidebar'), 10 * $sidebar_priority['kb_sidebar_right'] );
		    }
	    }

	    // elay Sidebar Layout
		if ( self::is_article_structure_v2( $kb_config ) && self::is_elay_sidebar_on( $kb_config, $sidebar_priority ) ) {
			add_action( 'eckb-article-left-sidebar', array('EPKB_Articles_Setup', 'display_elay_sidebar'), 10 * $sidebar_priority['elay_sidebar_left'] );
		}

	    // Categories Focused Layout
	    if ( self::is_article_structure_v2( $kb_config ) && $kb_config['kb_main_page_layout'] == EPKB_KB_Config_Layout_Categories::LAYOUT_NAME ) {
	    	if ( $sidebar_priority['categories_left'] ) {
		        add_action( 'eckb-article-left-sidebar', array('EPKB_Articles_Setup', 'focused_layout_categories'), 10 * $sidebar_priority['categories_left'], 3 );
	    	} else if ( $sidebar_priority['categories_right'] ) {
			    add_action( 'eckb-article-right-sidebar', array('EPKB_Articles_Setup', 'focused_layout_categories'), 10 * $sidebar_priority['categories_right'], 3 );
		    }
	    }

        //TOC Needs to be styled with CSS differently based on Article version. --------------
	    // Version 2: We can place the TOC into their appropriate HTML container ( Left Sidebar , Main Content , Right Sidebar )
	    // priority: 5 (top of the default right sidebar)
	    if ( self::is_article_structure_v2( $kb_config ) ) {

			// check TOC 
			if ( $sidebar_priority['toc_left'] ) {
				add_action( 'eckb-article-left-sidebar', array('EPKB_Articles_Setup', 'table_of_content'), 10 * $sidebar_priority['toc_left'], 3 );
			} else if ( $sidebar_priority['toc_content'] ) {
				add_action( 'eckb-article-content-header', array('EPKB_Articles_Setup', 'table_of_content'), 10 * $sidebar_priority['toc_content'], 3 );
			} else if ( $sidebar_priority['toc_right'] ) {
				add_action( 'eckb-article-right-sidebar', array('EPKB_Articles_Setup', 'table_of_content'), 10 * $sidebar_priority['toc_right'], 3 );
			}
			
	    }
	    // Version 1: We need to insert the TOC into the Article Body.
	    else {
			if ($kb_config['article_toc_enable'] == 'on') {
				add_action( 'eckb-article-content-body', array('EPKB_Articles_Setup', 'table_of_content'), 8, 4 );
			}
	    }

	    // CONTENT FOOTER
	    add_action( 'eckb-article-content-footer', array('EPKB_Articles_Setup', 'meta_container_footer'), 10, 3 );

	    add_action( 'eckb-article-content-footer', array('EPKB_Articles_Setup', 'tags'), 10, 3 );
        add_action( 'eckb-article-footer', array('EPKB_Articles_Setup', 'comments'), 10, 3 );
    }

	// SEARCH BOX
	public static function search_box( $args ) {
		if ( self::is_article_structure_v2( $args['config'] ) && $args['config']['kb_main_page_layout'] != 'Categories' ) {
			do_action( 'eckb-article-v2-search-box', $args );  // Elegant Layouts hook
		}	
	}

    // ARTICLE TITLE
    public static function article_title( $args ) {
        $show_title = $args['config']['templates_for_kb'] == 'kb_templates';
        $article_title = $show_title ? get_the_title( $args['article'] ) : '';

		if ( isset($_POST['epkb-wizard-demo-data']) && $_POST['epkb-wizard-demo-data'] == true ) {
			$article_title =  __( 'Demo Article', 'echo-knowledge-base' );
		}

        $tag = $show_title ? 'h1' : 'div';
        $article_seq_no = empty($_REQUEST['seq_no']) ? '' : EPKB_Utilities::sanitize_int( $_REQUEST['seq_no'] );
        $article_seq_no = empty($article_seq_no) ? '' : ' data-kb_article_seq_no=' . $article_seq_no;
        echo '<' . $tag . ' class="eckb-article-title kb-article-id" id="' . $args['article']->ID . '"' . $article_seq_no . '>' . $article_title . '</' . $tag . '>';
    }

	public static function meta_container_header( $args ) {
		self::output_meta_container( $args, 'header' );
	}

	public static function meta_container_footer( $args ) {
		self::output_meta_container( $args, 'footer' );
	}

	private static function output_meta_container( $args, $location ) {

		$args['output_location'] = $location == 'header' ? 'top' : 'bottom';
		$args['is_meta_container_on'] = false;

		$check_value = 'article_' . ( $location == 'header' ? 'top' : 'bottom' );
		$is_created_on      = $args['config']['created_on'] == $check_value;
		$is_last_updated_on = $args['config']['last_udpated_on'] == $check_value;
		$is_author          = $args['config']['author_mode'] == $check_value;

		// Sidebar layout on Main Page should not have meta data
		if ( ! empty($args['config']['sidebar_welcome']) ) {
			return;
		}

		$data = apply_filters( 'eckb-article-meta-container-end-on', $args );
		$is_meta_on = empty($data['is_meta_container_on']) ? false : $data['is_meta_container_on'];

		// if nothing is set then exit
		if ( ! $is_created_on && ! $is_last_updated_on && ! $is_author && ! $is_meta_on ) {
			return;
		}

		/** below is class eckb-article-content-header__article-meta and eckb-article-content-footer__article-meta */
		echo '<div class="' . 'eckb-article-content-' . $location . '__article-meta' . '">';

		if ( $is_created_on ) {
			self::created_on( $args );
		}

		if ( $is_last_updated_on ) {
			self::last_updated_on( $args );
		}

		if ( $is_author ) {
			self::author( $args );
		}

		// add Hook for future meta to inject here.
		do_action( 'eckb-article-meta-container-end', $args );

		echo '</div>';
	}

	/**
	 * Display a message in the Widget Container, Indicating that there are no Widgets assigned to this element.
	 */
	public static function wizard_widget_demo_data() {

		if ( ! empty( $_POST['epkb-wizard-demo-data'] ) && ! is_active_sidebar( 'eckb_articles_sidebar' ) ) { ?>
			<div class="eckb-no-widget">
				<?php _e( 'No widgets', 'echo-widgets' ); ?><br>
				<a href="<?php echo admin_url( 'widgets.php' ); ?>"><?php _e( 'Add your widgets here', 'echo-widgets' ); ?></a>
			</div> <?php
		}
	}

	/**
	 * Display Article KB Sidebar
	 * @param $args
	 */
	public static function display_article_sidebar( $args ) {
 
		if ( $args['config']['templates_for_kb_widget_sidebar_defaults'] == 'on' ) {
			$article_widget_sidebar_default_styles = 'eckb-article-widget-sidebar--default-styles';
		} else {
			$article_widget_sidebar_default_styles = '';
		} ?>

		<div id="eckb-article-widget-sidebar-container" class="<?php echo $article_widget_sidebar_default_styles;?>">
			<div class="eckb-article-widget-sidebar-body">				<?php
				self:self::wizard_widget_demo_data();
				dynamic_sidebar( 'eckb_articles_sidebar' );				?>
			</div>
		</div>    <?php
	}

	/**
	 * Display elay Sidebar
	 * @param $args
	 */
	public static function display_elay_sidebar( $args ) {
		do_action( 'eckb-article-v2-elay_sidebar', $args );
	}

	/**
	 * Output Table of Content
	 *
	 * @param $args
	 */
	public static function table_of_content( $args ) {

		// Both Versions
		$styles = '
			#eckb-article-body .eckb-article-toc ul a.active {
				background-color:   ' . $args['config']['article_toc_active_bg_color'] . ';
				color:              ' . $args['config']['article_toc_active_text_color'] . ';
			}
			#eckb-article-body .eckb-article-toc ul a:hover {
				background-color:   ' . $args['config']['article_toc_cursor_hover_bg_color'] . ';
				color:              ' . $args['config']['article_toc_cursor_hover_text_color'] . ';
			}
			#eckb-article-body .eckb-article-toc__inner {
				border-color: ' . $args['config']['article_toc_border_color'] . ';
				font-size:          ' . $args['config']['article_toc_font_size'] . 'px;
				background-color:   ' . $args['config']['article_toc_background_color'] . ';
			}
			#eckb-article-body .eckb-article-toc__inner a {
				color:              ' . $args['config']['article_toc_text_color'] . ';
				font-size:          ' . $args['config']['article_toc_font_size'] . 'px;
			}
		';

		// Run only for old V1 structure
		if ( ! self::is_article_structure_v2( $args['config'] ) ) {

			$media_1_gutter = $args['config']['article_toc_width_1']+$args['config']['article_toc_gutter'] + 25;
			$media_2_gutter = $args['config']['article_toc_width_2']+$args['config']['article_toc_gutter'] + 25;
			$toc_gutter = -325 - $args['config']['article_toc_gutter'];

			// Version 1 Only
			$styles .= '
			#eckb-article-body .eckb-article-toc--position-'.$args['config']['article_toc_position'].' .eckb-article-toc__inner {
					'.$args['config']['article_toc_position'].': ' . $toc_gutter . 'px;
				}
			';

			// Media Queries
			$styles .= '
				@media only screen and ( max-width: ' . $args['config']['article_toc_media_1'] . 'px ) {
                    #eckb-article-body .eckb-article-toc .eckb-article-toc__inner {
                        width:          ' . $args['config']['article_toc_width_1'] . 'px !important;
                        '.$args['config']['article_toc_position'].': -' . $media_1_gutter . 'px !important;
                    }
                }
                @media only screen and ( max-width: ' . $args['config']['article_toc_media_2'] . 'px ) {
                    #eckb-article-body .eckb-article-toc .eckb-article-toc__inner {
                        width:          ' . $args['config']['article_toc_width_2'] . 'px !important;
                        '.$args['config']['article_toc_position'].': -' . $media_2_gutter . 'px !important;
                    }
                }
                @media only screen and ( max-width: ' . $args['config']['article_toc_media_3'] . 'px ) {
                
                    #eckb-article-body .eckb-article-toc {
                          width:    100% !important;
					      float:    none !important;
					      position: relative !important;
					      height:   auto !important;
					      height:   fit-content !important;
                          display:  inline-block;
                          top:      0 !important;
                    }
                    #eckb-article-body .eckb-article-toc .eckb-article-toc__inner {
                          display:          block;
					      width:            100% !important;
					      float:            none !important;
					      margin-bottom:    20px !important;
					      left:             0 !important;
					      position:         relative !important;
					      
                    }
                } ';
		}

		$classes = 'eckb-article-toc--position-' . $args['config']['article_toc_position'];
		$classes .= ' eckb-article-toc--bmode-' . $args['config']['article_toc_border_mode'];

		$title = empty($args['config']['article_toc_title']) ? '' : '<div class="eckb-article-toc__title">' . $args['config']['article_toc_title'] . '</div>';

		$wrap = '
			<style id="eckb-article-toc-styles" type="text/css">' . $styles . '</style>
			<div class="eckb-article-toc ' . $classes . ' eckb-article-toc-reset "				
				data-offset="' . $args['config']['article_toc_scroll_offset'] . '"
				data-min="' . $args['config']['article_toc_start_level'] . '"
				data-exclude_class="' . $args['config']['article_toc_exclude_class'] . '"
				>' . $title . '</div>
			';

		echo $wrap;
	}

	/**
	 * For Category Focused Layout show top level or sibling categories in the left sidebar
	 *
	 * @param $args
	 */
	public static function focused_layout_categories( $args ) {

		// for Category Focused Layout show sidebar with list of top-level categories
		if ( $args['config']['kb_main_page_layout'] != EPKB_KB_Config_Layout_Categories::LAYOUT_NAME ) {
			return;
		}

		$parent_category_id = 0;
		$active_id = 0;
		$breadcrumb_tree = EPKB_Templates_Various::get_article_breadcrumb( $args['config'], $args['article']->ID );
		$breadcrumb_tree = array_keys( $breadcrumb_tree );
		if ( $args['config']['categories_layout_list_mode'] == 'list_top_categories' ) {
			if ( isset( $breadcrumb_tree[0] ) ) {
				$active_id = $breadcrumb_tree[0];
			}
		} else {
			$tree_count = count( $breadcrumb_tree );
			if ( $tree_count > 1 ) {
				$parent_category_id = $breadcrumb_tree[$tree_count - 2];
				$active_id = $breadcrumb_tree[$tree_count - 1];
			}

			if ( $tree_count == 1 ) {
				$active_id = $breadcrumb_tree[0];
			}
		}

		echo EPKB_Categories_DB::get_layout_categories_list( $args['config']['id'], $args['config'], $parent_category_id, $active_id );
	}

	// CREATED ON
	public static function created_on( $args ) {
		echo '<div class="eckb-ach__article-meta__date-created">';
			if ( 'on' == $args['config']['article_meta_icon_on'] ) {
				echo '<span class="eckb-ach__article-meta__date-created__date-icon epkbfa epkbfa-calendar"></span>';
			}
			if ( $args['config']['created_on_text'] && ! empty($args['article']->post_date) ) {
				echo '<span class="eckb-ach__article-meta__date-created__text">' . esc_html( $args['config']['created_on_text'] ) . '</span>';
			}

			echo '<span class="eckb-ach__article-meta__date-created__date">';
            printf(
                '<time class="entry-date" datetime="%1$s">%2$s</time>',
                esc_attr( get_the_date( DATE_W3C ) ),
                esc_html( get_the_date() )
            );
			echo '</span>';
		echo '</div>';
	}

    // LAST UPDATED ON
    public static function last_updated_on( $args ) {
	    echo '<div class="eckb-ach__article-meta__date-updated">';
			if ( 'on' == $args['config']['article_meta_icon_on'] ) {
				echo '<span class="eckb-ach__article-meta__date-updated__date-icon epkbfa epkbfa-pencil-square-o"></span>';
			}
		    if ( $args['config']['last_udpated_on_text'] && ! empty($args['article']->post_modified) ) {
			    echo '<span class="eckb-ach__article-meta__date-updated__text">' . esc_html( $args['config']['last_udpated_on_text'] ) . '</span>';
		    }

		    echo '<span class="eckb-ach__article-meta__date-updated__date">';
            printf(
                '<time class="entry-date" datetime="%1$s">%2$s</time>',
                esc_attr( get_the_modified_date( DATE_W3C ) ),
                esc_html( get_the_modified_date() )
            );
		    echo '</span>';
	    echo '</div>';
    }

	// AUTHOR
    public static function author( $args ) {

		$post_author = empty($_POST['epkb-wizard-demo-data']) ? get_the_author_meta( 'display_name', $args['article']->post_author ) : __( 'Admin', 'echo-knowledge-base' );

	    echo '<div class="eckb-ach__article-meta__author">';
		    if ( 'on' == $args['config']['article_meta_icon_on'] ) {
				echo '<span class="eckb-ach__article-meta__author__author-icon epkbfa epkbfa-user"></span>';
			}
		    if ( $args['config']['author_text'] && ! empty($post_author) ) {
			    echo '<span class="eckb-ach__article-meta__author__text">' . esc_html( $args['config']['author_text'] ) . '</span>';
		    }
	        echo '<span class="eckb-ach__article-meta__author__name">' . $post_author . '</span>';
	    echo '</div>';
    }

    // BREADCRUMB
    public static function breadcrumbs( $args ) {
	    // Sidebar layout on Main Page should not have meta data
	    if ( ! empty($args['config']['sidebar_welcome']) ) {
		    return;
	    }

	    if ( $args['config'][ 'breadcrumb_toggle'] == 'on' ) {
            EPKB_Templates::get_template_part( 'feature', 'breadcrumb', $args['config'], $args['article'] );
        }
    }

    // BACK NAVIGATION
    public static function navigation( $args ) {
        if ( $args['config'][ 'back_navigation_toggle'] == 'on' ) {
            EPKB_Templates::get_template_part( 'feature', 'navigation-back', $args['config'], $args['article'] );
        }
    }

    // ARTICLE CONTENT
    public static function article_content( $args ) {
	    do_action( 'eckb-article-before-content', $args );
            echo $args['content'];
	    do_action( 'eckb-article-after-content', $args );

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

    public static function is_elay_sidebar_on( $kb_config, $sidebar_priority ) {
		return
	    EPKB_Utilities::is_elegant_layouts_enabled() &&
	    ( $kb_config['kb_main_page_layout'] == EPKB_KB_Config_Layouts::SIDEBAR_LAYOUT                                                                       // Sidebar Layout on Main Page needs the sidebar all the time
	      || ( $kb_config['kb_main_page_layout'] != EPKB_KB_Config_Layout_Categories::LAYOUT_NAME  && $sidebar_priority['elay_sidebar_left'] )              // Sidebar Layout for Basic/Tabs/Grid
	      || ( ! $sidebar_priority['elay_sidebar_left'] && ( ! empty($_REQUEST['action']) && $_REQUEST['action'] == 'epkb_wizard_update_order_view' ) ) );     // Ordering Wizard needs Sidebar
    }

	/**
	 * Generate new article VERSION 2 style from configuration
	 *
	 * @param $kb_config
	 */
	private static function generate_article_structure_css_v2($kb_config ) {

		// Left Sidebar Settings
		$article_left_sidebar_padding               = $kb_config['article-left-sidebar-padding-v2'];
		$article_left_sidebar_bgColor               = $kb_config['article-left-sidebar-background-color-v2'];
		$article_left_sidebar_starting_position     = $kb_config['article-left-sidebar-starting-position'];

		// Content Settings
		$article_content_padding                    = $kb_config['article-content-padding-v2'];
		$article_content_bgColor                    = $kb_config['article-content-background-color-v2'];

		// Right Sidebar Settings
		$article_right_sidebar_padding              = $kb_config['article-right-sidebar-padding-v2'];
		$article_right_sidebar_bgColor              = $kb_config['article-right-sidebar-background-color-v2'];
		$article_right_sidebar_starting_position    = $kb_config['article-right-sidebar-starting-position'];


		// Desktop Settings
		$article_container_desktop_width        = $kb_config['article-container-desktop-width-v2'];
		$article_container_desktop_width_units  = $kb_config['article-container-desktop-width-units-v2'];

		$article_body_desktop_width             = $kb_config['article-body-desktop-width-v2'];
		$article_body_desktop_width_units       = $kb_config['article-body-desktop-width-units-v2'];

		$article_left_sidebar_desktop_width     = $kb_config['article-left-sidebar-desktop-width-v2'];
		$article_content_desktop_width          = $kb_config['article-content-desktop-width-v2'];
		$article_right_sidebar_desktop_width    = $kb_config['article-right-sidebar-desktop-width-v2'];


		// Tablet Settings
		$tablet_breakpoint                      = $kb_config['article-tablet-break-point-v2'];
		$article_container_tablet_width         = $kb_config['article-container-tablet-width-v2'];
		$article_container_tablet_width_units   = $kb_config['article-container-tablet-width-units-v2'];

		$article_body_tablet_width              = $kb_config['article-body-tablet-width-v2'];
		$article_body_tablet_width_units        = $kb_config['article-body-tablet-width-units-v2'];

		$article_left_sidebar_tablet_width      = $kb_config['article-left-sidebar-tablet-width-v2'];
		$article_content_tablet_width           = $kb_config['article-content-tablet-width-v2'];
		$article_right_sidebar_tablet_width     = $kb_config['article-right-sidebar-tablet-width-v2'];
		$theme_class 							= ! empty($kb_config['theme_name']) ? '.eckb-theme-' . $kb_config['theme_name'] : '';

		// Mobile Settings
		$mobile_breakpoint                      = $kb_config['article-mobile-break-point-v2'];

		$sidebar_priority = EPKB_KB_Config_Specs::add_sidebar_component_priority_defaults( $kb_config['article_sidebar_component_priority'] );

		// auto-determine whether we need sidebar or let user override it to be displayed
		$is_left_sidebar_on = self::is_left_sidebar_on( $kb_config, $sidebar_priority );
		$is_right_sidebar_on = self::is_right_sidebar_on( $kb_config, $sidebar_priority );


		/**
		 *  Grid Columns start at lines.
		 *
		 *  Left Sidebar Grid Start:    1 - 2;
		 *  Content Grid Start:         2 - 3;
		 *  Right Sidebar Grid Start:    3 - 4;
		 *
		 *  LEFT   Content  Right
		 *  1 - 2   2 - 3   3 - 4
		 */
			?>
		<!-- Article Structure Version 2 Style -->

		<style>
			<?php
			self::article_media_structure( array(
					'is_left_sidebar_on'            => $is_left_sidebar_on,
					'is_right_sidebar_on'           => $is_right_sidebar_on,
					'article_container_width'       => $article_container_desktop_width,
					'article_container_width_units' => $article_container_desktop_width_units,
					'article_body_width'            => $article_body_desktop_width,
					'article_body_width_units'      => $article_body_desktop_width_units,
					'article_left_sidebar_width'    => $article_left_sidebar_desktop_width,
					'article_content_width'         => $article_content_desktop_width,
					'article_right_sidebar_width'   => $article_right_sidebar_desktop_width,
					'breakpoint'                    => 'desktop',
					'type'                          => 'DESKTOP',
					'theme_class'                   => $theme_class
			));

			self::article_media_structure( array(
					'is_left_sidebar_on'            => $is_left_sidebar_on,
					'is_right_sidebar_on'           => $is_right_sidebar_on,
					'article_container_width'       => $article_container_tablet_width,
					'article_container_width_units' => $article_container_tablet_width_units,
					'article_body_width'            => $article_body_tablet_width,
					'article_body_width_units'      => $article_body_tablet_width_units,
					'article_left_sidebar_width'    => $article_left_sidebar_tablet_width,
					'article_content_width'         => $article_content_tablet_width,
					'article_right_sidebar_width'   => $article_right_sidebar_tablet_width,
					'breakpoint'                    => $tablet_breakpoint,
					'type'                          => 'TABLET',
					'theme_class'                   => $theme_class
			));			?>


			/* SHARED */
			#eckb-article-page-container-v2<?php echo $theme_class; ?> #eckb-article-left-sidebar {
				padding: <?php echo $article_left_sidebar_padding.'px;'; ?>;
				background-color: <?php echo $article_left_sidebar_bgColor.';'; ?>
				margin-top: <?php echo $article_left_sidebar_starting_position.'px;'; ?>
			}
			#eckb-article-page-container-v2<?php echo $theme_class; ?> #eckb-article-content {
				padding: <?php echo $article_content_padding.'px;'; ?>;
				background-color: <?php echo $article_content_bgColor.';'; ?>
			}
			#eckb-article-page-container-v2<?php echo $theme_class; ?> #eckb-article-right-sidebar {
				padding: <?php echo $article_right_sidebar_padding.'px;'; ?>;
				background-color: <?php echo $article_right_sidebar_bgColor.';'; ?>
				margin-top: <?php echo $article_right_sidebar_starting_position.'px;'; ?>
			}

			/* MOBILE - Set all columns to full width. */
			@media only screen and ( max-width: <?php echo $mobile_breakpoint; ?>px ) {

				#eckb-article-page-container-v2<?php echo $theme_class; ?> {
					width:100%;
				}
				#eckb-article-page-container-v2<?php echo $theme_class; ?> #eckb-article-content {
					grid-column-start: 1;
					grid-column-end: 4;
				}
				#eckb-article-page-container-v2<?php echo $theme_class; ?> #eckb-article-left-sidebar {
					grid-column-start: 1;
					grid-column-end: 4;
				}
				#eckb-article-page-container-v2<?php echo $theme_class; ?> #eckb-article-right-sidebar {
					grid-column-start: 1;
					grid-column-end: 4;
				}
				#eckb-article-page-container-v2<?php echo $theme_class; ?> .eckb-article-toc {
					position: relative;
					float: left;
					width: 100%;
					height: auto;
					top: 0;
				}
			}

		</style>    <?php
	}

	public static function is_left_sidebar_on( $kb_config, $sidebar_priority ) {
		return count(self::get_hooks_functions_list('eckb-article-left-sidebar')) ||                  // if user defines their own hooks
		       self::is_elay_sidebar_on( $kb_config, $sidebar_priority ) ||                           // control Elegant Layout sidebar
		       $sidebar_priority['toc_left'] ||                                                       // TOC
		       $sidebar_priority['kb_sidebar_left'] ||
		       ( $kb_config['kb_main_page_layout'] == EPKB_KB_Config_Layout_Categories::LAYOUT_NAME && $sidebar_priority['categories_left'] ); // Categories Layout always on the left
	}

	public static function is_right_sidebar_on( $kb_config, $sidebar_priority ) {
		return count(self::get_hooks_functions_list('eckb-article-right-sidebar')) ||                  // if user defines their own hooks
		       $sidebar_priority['toc_right'] ||                                                       // TOC
               $sidebar_priority['kb_sidebar_right'] ||                                                // KB Sidebar
               ( $kb_config['kb_main_page_layout'] == EPKB_KB_Config_Layout_Categories::LAYOUT_NAME && $sidebar_priority['categories_right'] );              // Categories Layout always on the right
	}

	/**
	 * Output style for either desktop or tablet
	 * @param array $settings
	 */
	public static function article_media_structure( $settings = array() ) {

		$defaults = array(
			'is_left_sidebar_on'            => '',
			'is_right_sidebar_on'           => '',
			'article_container_width'       => '',
			'article_container_width_units' => '',
			'article_body_width'            => '',
			'article_body_width_units'      => '',
			'article_left_sidebar_width'    => '',
			'article_content_width'         => '',
			'article_right_sidebar_width'   => '',
			'breakpoint'                    => '',
			'type'                          => '',
			'theme_class'                   => ''
		);
		$args = array_merge( $defaults, $settings );


		$article_length = ' /* ' . $args[ 'type' ] . ' */ ' . PHP_EOL ;
		if( $args[ 'breakpoint' ]  != 'desktop' ) {
			$article_length .= '@media only screen and ( max-width: '.$args[ 'breakpoint' ].'px ) {';
		}

		$article_length .=
			'#eckb-article-page-container-v2' . $args[ 'theme_class' ] . ' {
				width: '.$args[ 'article_container_width' ] . $args[ 'article_container_width_units'].' }';
		$article_length .=
			'#eckb-article-page-container-v2' . $args[ 'theme_class' ] . ' #eckb-article-body {
				width: '.$args[ 'article_body_width' ] . $args[ 'article_body_width_units'].' }';

		/**
		 * If No Left Sidebar
		 *  - Expend the Article Content 1 - 3
		 *  - Make Layout 2 Columns only and use the Two remaining values
		 */
		if ( ! $args[ 'is_left_sidebar_on' ]  ) {
			$article_length .= '
		        /* NO LEFT SIDEBAR */
				#eckb-article-page-container-v2' . $args[ 'theme_class' ] . ' #eckb-article-body {
				      grid-template-columns:  0 ' . $args[ 'article_content_width' ] . '% '.$args[ 'article_right_sidebar_width' ].'%;
				}
				#eckb-article-page-container-v2' . $args[ 'theme_class' ] . ' #eckb-article-left-sidebar {
						display:none;
				}
				#eckb-article-page-container-v2' . $args[ 'theme_class' ] . ' #eckb-article-content {
						grid-column-start: 1;
						grid-column-end: 3;
					}
				';
		}

		/**
		 * If No Right Sidebar
		 *  - Expend the Article Content 2 - 4
		 *  - Make Layout 2 Columns only and use the Two remaining values
		 */
		if ( ! $args[ 'is_right_sidebar_on' ] ) {
			$article_length .= '
				/* NO RIGHT SIDEBAR */
				#eckb-article-page-container-v2' . $args[ 'theme_class' ] . ' #eckb-article-body {
				      grid-template-columns: '.$args[ 'article_left_sidebar_width' ].'% ' . $args[ 'article_content_width' ] . '% 0 ;
				}
				
				#eckb-article-page-container-v2' . $args[ 'theme_class' ] . ' #eckb-article-right-sidebar {
						display:none;
				}
				#eckb-article-page-container-v2' . $args[ 'theme_class' ] . ' #eckb-article-content {
						grid-column-start: 2;
						grid-column-end: 4;
					}
				';
		}

		// If No Sidebars Expand the Article Content 1 - 4
		if ( ! $args[ 'is_left_sidebar_on']  && ! $args[ 'is_right_sidebar_on' ] ) {
			$article_length .= '
				#eckb-article-page-container-v2' . $args[ 'theme_class' ] . ' #eckb-article-body {
				      grid-template-columns: 0 ' . $args[ 'article_content_width' ] . '% 0;
				}
				#eckb-article-page-container-v2' . $args[ 'theme_class' ] . ' #eckb-article-left-sidebar {
						display:none;
				}
				#eckb-article-page-container-v2' . $args[ 'theme_class' ] . ' #eckb-article-right-sidebar {
						display:none;
				}
				#eckb-article-page-container-v2' . $args[ 'theme_class' ] . ' #eckb-article-content {
						grid-column-start: 1;
						grid-column-end: 4;
					}
				';
		}

		/**
		 * If Both Sidebars are active
		 *  - Make Layout 3 Columns and divide their sizes according to the user settings
		 */
		if ( $args[ 'is_left_sidebar_on' ]  && $args[ 'is_right_sidebar_on' ] ) {
			$article_length .= '
					#eckb-article-page-container-v2' . $args[ 'theme_class' ] . ' #eckb-article-body {
					      grid-template-columns: ' . $args[ 'article_left_sidebar_width' ] . '% ' . $args[ 'article_content_width' ] . '% ' . $args[ 'article_right_sidebar_width' ] . '%;
					}
					';
		}

		if( $args[ 'breakpoint' ]  !== 'desktop' ) {
			$article_length .= '}';
		}

		echo $article_length;
	}

	/*
	 * Determine if we are using the new V2 structure for articles
	 */
	public static function is_article_structure_v2( $kb_config ) {
		return $kb_config['article-structure-version']  == 'version-2' || $kb_config['kb_main_page_layout'] == EPKB_KB_Config_Layout_Categories::LAYOUT_NAME;
	}

	/**
	 * This function will return array of the functions that are attached to the filter or action
	 * @param array|string $hook
	 * @return array[fucntion_name] => order, ... )
	 */
	public static function get_hooks_functions_list( $hook = '') {
		global $wp_filter;

		$epkb_hooks = array();

		/* TODO release next if ( isset($wp_filter[$hook]) && is_array($wp_filter[$hook]->callbacks) ) {
			foreach ( $wp_filter[$hook]->callbacks as $order => $callback_data ) {
				if ( is_array($callback_data) ) {
					foreach ( $callback_data as $name => $function_data ) {

						if ( empty($function_data['function']) ) {
							continue;
						}

						// simple function
						if ( is_string( $function_data['function'] ) ) {
							$epkb_hooks[ $function_data['function'] ] = $order;
						}

						// class function
						if ( is_array( $function_data['function'] ) && ! empty($function_data['function'][0]) && ! empty($function_data['function'][1]) ) {

							if ( is_string( $function_data['function'][0] ) ) {
								$epkb_hooks[ $function_data['function'][0] . '::' . $function_data['function'][1] ] = $order;
							}
							// public method
							else {
								$epkb_hooks[ get_class( $function_data['function'][0] ) . '->' . $function_data['function'][1] ] = $order;
							}
						}
					}
				}
			}
		} */

		return $epkb_hooks;
	}
}
