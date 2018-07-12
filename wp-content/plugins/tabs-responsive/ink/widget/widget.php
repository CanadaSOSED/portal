<?php
/**
 * Adds  widget.
 */
class Wpsm_Tabs_R_Widget extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    function __construct() {
        parent::__construct(
            'wpsm_tabs_r_widget_id', // Base ID
            'Tabs Responsive Widget', // Name
            array( 'description' => __( 'Display Your Tabs in widget area.', wpshopmart_tabs_r_text_domain ), ) // Args
        );
	}

    /**
     * Front-end display of widget.
     */
    public function widget( $args, $instance ) {
        $Title    	=   apply_filters( 'wpsm_tabs_r_widget_title', $instance['Title'] );
		echo $args['before_widget'];
		
		 $wpsm_tabs_r_id	=   apply_filters( 'wpsm_tabs_r_widget_shortcode', $instance['Shortcode'] ); 

		if(is_numeric($wpsm_tabs_r_id)) {
			if ( ! empty( $instance['Title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['Title'] ). $args['after_title'];
			}
			echo do_shortcode( '[TABS_R id='.$wpsm_tabs_r_id.']' );
		} else {
			echo "<p>Sorry! No Tabs Shortcode Found.</p>";
		}
		echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {

        if ( isset( $instance[ 'Title' ] ) ) {
            $Title = $instance[ 'Title' ];
        } else {
            $Title = "Tabs Responsive Shortcode";
        }

        if ( isset( $instance[ 'Shortcode' ] ) ) {
            $Shortcode = $instance[ 'Shortcode' ];
        } else {
            $Shortcode = "Select Any Tabs";
        }
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'Title' ); ?>"><?php _e( 'Widget Title' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'Title' ); ?>" name="<?php echo $this->get_field_name( 'Title' ); ?>" type="text" value="<?php echo esc_attr( $Title ); ?>">
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'Shortcode' ); ?>"><?php _e( 'Select Tabs' ); ?> (Required)</label>
			<?php
			/**
			 * Get All Tabs Shortcode Custom Post Type
			 */
			$wpsm_ac_cpt = "tabs_responsive";
			global $All_Wpsm_Acsh;
			$All_Wpsm_Acsh = array('post_type' => $wpsm_ac_cpt, 'orderby' => 'ASC', 'post_status' => 'publish');
			$All_Wpsm_Acsh = new WP_Query( $All_Wpsm_Acsh );		
			?>
			<select id="<?php echo $this->get_field_id( 'Shortcode' ); ?>" name="<?php echo $this->get_field_name( 'Shortcode' ); ?>" style="width: 100%;">
				<option value="Select Any Tabs" <?php if($Shortcode == "Select Any Tabs") echo 'selected="selected"'; ?>>Select Any Tabs</option>
				<?php
				if( $All_Wpsm_Acsh->have_posts() ) {	 ?>	
				<?php while ( $All_Wpsm_Acsh->have_posts() ) : $All_Wpsm_Acsh->the_post();	
					$PostId = get_the_ID(); 
					$PostTitle = get_the_title($PostId);
				?>
				<option value="<?php echo $PostId; ?>" <?php if($Shortcode == $PostId) echo 'selected="selected"'; ?>><?php if($PostTitle) echo $PostTitle; else _e("No Title", wpshopmart_tabs_r_text_domain); ?></option>
				<?php endwhile; ?>
				<?php
			}  else  { 
				echo "<option>Sorry! No Tabs Shortcode Found.</option>";
			}
			?>
			</select>
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['Title'] = ( ! empty( $new_instance['Title'] ) ) ? strip_tags( $new_instance['Title'] ) : '';
        $instance['Shortcode'] = ( ! empty( $new_instance['Shortcode'] ) ) ? strip_tags( $new_instance['Shortcode'] ) : 'Select Any Tab';
        
        return $instance;
    }
} // end of  Widget Class

// Register Widget
function Wpsm_Tabs_R_Widget() {
    register_widget( 'Wpsm_Tabs_R_Widget' );
}
add_action( 'widgets_init', 'Wpsm_Tabs_R_Widget' );
?>