<?php
// Create the Responsive Tabs shortcode
function rtbs_sc($atts) {
	extract(shortcode_atts(array(
		"name" => ''
	), $atts));

    global $post;

    $args = array('post_type' => 'rtbs_tabs', 'name' => $name);
    $custom_posts = get_posts($args);
    $output = '';
    
    foreach($custom_posts as $post) : setup_postdata($post);

	$entries = get_post_meta( $post->ID, '_rtbs_tabs_head', true );
    $options = get_post_meta( $post->ID, '_rtbs_settings_head', true );
    
    (get_post_meta( $post->ID, '_rtbs_tbg', true )) ? $rtbs_tbg = get_post_meta( $post->ID, '_rtbs_tbg', true ) : $rtbs_tbg = 'transparent';

    /* Checks if forcing original fonts. */
    $original_font = get_post_meta( $post->ID, '_rtbs_original_font', true );
    ($original_font && $original_font != 'no' ? $ori_f = 'rtbs_tab_ori' : $ori_f = '');

    $rtbs_breakpoint = get_post_meta( $post->ID, '_rtbs_breakpoint', true );
    $rtbs_color = get_post_meta( $post->ID, '_rtbs_tabs_bg_color', true );

    

    /* Outputing the options in invisible divs */
    $output = '<div class="rtbs '.$ori_f.' rtbs_'.$name.'">';
    $output .= '<div class="rtbs_slug" style="display:none">'.$name.'</div>';
    $output .= '<div class="rtbs_inactive_tab_background" style="display:none">'.$rtbs_tbg.'</div>';
    $output .= '<div class="rtbs_breakpoint" style="display:none">'.$rtbs_breakpoint.'</div>';
    $output .= '<div class="rtbs_color" style="display:none">'.$rtbs_color.'</div>';

    $output .= '
        <div class="rtbs_menu">
            <ul>
                <li class="mobile_toggle">&zwnj;</li>';
                foreach ($entries as $key => $tabs) {
                    if ($key == 0){
                    $output .= '<li class="current">';
                    $output .= '<a style="background:'.$rtbs_color.'" class="active '.$name.'-tab-link-'.$key.'" href="#" data-tab="#'.$name.'-tab-'.$key.'">';

                    (!empty($tabs['_rtbs_title'])) ?
                            $output .= $tabs['_rtbs_title'] :
                                $output .= '&nbsp;';

                    $output .= '</a>';
                    $output .= '</li>';
                    } else {
                    $output .= '<li>';
                    $output .= '<a href="#" data-tab="#'.$name.'-tab-'.$key.'" class="'.$name.'-tab-link-'.$key.'">';
                    (!empty($tabs['_rtbs_title'])) ?
                            $output .= $tabs['_rtbs_title'] :
                                $output .= '&nbsp;';

                    $output .= '</a>';
                    $output .= '</li>';
                    }
                }
    $output .= '
            </ul>
        </div>';

    foreach ($entries as $key => $tabs) {
        if ($key == 0){
            $output .= '<div style="border-top:7px solid '.$rtbs_color.';" id="'.$name.'-tab-'.$key.'" class="rtbs_content active">';
                $output .= do_shortcode(wpautop($tabs['_rtbs_content']));
            $output .= '<div style="margin-top:30px; clear:both;"></div></div>';
        } else {
            $output .= '<div style="border-top:7px solid '.$rtbs_color.';" id="'.$name.'-tab-'.$key.'" class="rtbs_content">';
                $output .= do_shortcode(wpautop($tabs['_rtbs_content']));
            $output .= '<div style="margin-top:30px; clear:both;"></div></div>';
        }
    }
    $output .= '
    </div>
    ';

  endforeach; wp_reset_postdata();
  return $output;

}

add_shortcode("rtbs", "rtbs_sc");

?>