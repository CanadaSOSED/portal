<?php
/**
 * Add Meta box to Order Screen
 * @author WPGens
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPGENS_RAF_Admin_Order {

	/**
	 * Hook in order meta boxes and save order meta
	 *
	 * @since 2.0.0
	 */
	public function __construct() 
	{
		add_action( 'add_meta_boxes', array($this, 'raf_order_meta_box') );
		add_action( 'save_post', array($this, 'raf_order_meta_box_save_data') );
	}
	
	/**
	 * Add meta Box
	 *
	 * @since 2.0.0
	 */
	function raf_order_meta_box() {
		foreach ( wc_get_order_types( 'order-meta-boxes' ) as $type ) {
			$order_type_object = get_post_type_object( $type );
			add_meta_box( 'wpgens-raf-notes', sprintf( __( 'Refer a Friend Data', 'gens-raf' ), $order_type_object->labels->singular_name ), array($this, 'raf_order_meta_box_data'), $type, 'side', 'default' );
		}
	}

	/**
	 * Callback function for RAF Order Meta Box
	 *
	 * @since 2.0.0
	 */
	function raf_order_meta_box_data($post) {

		$referralID = get_post_meta( $post->ID, '_raf_id', true );
		$raf_meta = get_post_meta( $post->ID, '_raf_meta', true );

		if (!empty($referralID)) {
            
            if(filter_var($referralID, FILTER_VALIDATE_EMAIL)) {
                $guest_email = $referralID;
            } else {
                $args = array('meta_key' => "gens_referral_id", 'meta_value' => $referralID );
                $user = get_users($args);
            }
			?>
	        <h4 style="margin-bottom:0px;">
	            <?php _e( 'Referred by:', 'gens-raf' ); ?>
	        </h4>
	        <?php if(isset($user)) { ?>
	        <p style="margin-top:3px;"><a href="<?php echo get_edit_user_link($user[0]->ID); ?>"><?php echo $user[0]->first_name . ' '. $user[0]->last_name; ?> (<?php echo $user[0]->user_email; ?>)</a></p>
	        <?php } ?>
            <?php if(isset($guest_email)) { ?>
	        <p style="margin-top:3px;"><a href="mailto:<?php echo $guest_email; ?>" target="_blank">(<?php echo $guest_email; ?>)</a></p>
	        <?php } ?>
	        <?php if(isset($raf_meta) && is_array($raf_meta)) {  // need to be compatible with previous version or lots of notices
				if(isset($raf_meta['generate'])) {
					$generate = $raf_meta['generate'];
				} else {
					$generate = $raf_meta['publish']; // old fallback.
				}
			    wp_nonce_field( 'raf_order_meta', 'raf_order_meta_nonce' );
	        ?>
	        <h4 style="margin-bottom:0px;">
	            <?php _e( 'Generate a coupon on order complete:', 'gens-raf' ); ?>
	        </h4>
            <label> Yes <input type="radio" name="raf_generate_coupon" value="true" <?php echo $generate == "true" ? "checked" : ""; ?>/></label>
			<label> No <input type="radio" name="raf_generate_coupon" value="false" <?php echo $generate == "false" ? "checked" : ""; ?>/></label>
			<?php if(isset($raf_meta['info'])) { ?>
		        <h4 style="margin-bottom:0px;">
		            <?php _e( 'Referral notes:', 'gens-raf' ); ?>
		        </h4>
		 	        <p style="margin-top:3px;"><?php echo $raf_meta['info']; ?></p>
		        <?php
	        }
	        // link to coupon
	    	}
    	} else {
            wp_nonce_field( 'raf_order_meta', 'raf_order_meta_nonce' );
            ?>
                <h4 style="margin-bottom:5px;">
                    <?php _e( 'No referral data.', 'gens-raf' ); ?>
                </h4>
                <input type="text" name="raf_code_id" value="" style="width:100%" />
                <p><?php _e( 'Use input box above if you want to add referral code manually.', 'gens-raf' ); ?></p>
            <?php
    	}
	}

	/**
	 * Hook in order meta boxes and save order meta
	 *
	 * @since 2.0.0
	 */
	function raf_order_meta_box_save_data( $post_id ) {
        $nonce = isset($_POST['raf_order_meta_nonce']) ? $_POST['raf_order_meta_nonce'] : false;
        $post_type = get_post_type($post_id);

		// Load only on order page
		if ( !($nonce) || ( $post_type !== "shop_order" && $post_type !== 'shop_subscription' ) )
            return $post_id;
            
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'raf_order_meta' ) )
            return $post_id;
            
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return $post_id;
        
        // If its not referral order and code wasnt added manually: 
        if (isset($_POST['raf_code_id']) && $_POST['raf_code_id'] === '')
            return $post_id;

        // If admin is adding code manually: 
        if (isset($_POST['raf_code_id']) && $_POST['raf_code_id'] !== '') {    
            // Sanitize user input.
            $rafID = sanitize_text_field( $_POST['raf_code_id'] );
            $raf_meta = array("info" => "Referral was added manually. Coupon will be generated on the order complete.", "generate" => "true", "increase_referrals" => "true");

            $referral = get_users( array('meta_key' => 'gens_referral_id', 'meta_value' => $rafID, 'number' => 1, 'count_total' => false, 'fields' => 'ids') );

            if(empty($referral)) {
                return $post_id;
            }

            $order = wc_get_order( $post_id );
            $user_id = ( version_compare( WC_VERSION, '2.7', '<' ) ) ? get_post_meta($post_id, '_customer_user', true) : $order->get_user_id();

            do_action('new_raf_data', 'new_order', array_merge(array('user' => $user_id, 'referral' => $rafID, 'order' => $post_id), $raf_meta) );

            update_post_meta( $post_id, '_raf_id', esc_attr($rafID)); // will be depricated
            update_post_meta( $post_id, '_wpgens_raf_id', esc_attr($rafID));
        
        } else {

            // Sanitize user input.
            $new_data = sanitize_text_field( $_POST['raf_generate_coupon'] );
            // Update the meta field in the database.
            $raf_meta = get_post_meta( $post_id, '_raf_meta', true );
            $raf_meta['generate'] = $new_data;
            $raf_meta['increase_referrals'] = $new_data;
        }

		/* OK, its safe for us to save the data now. */
		update_post_meta( $post_id, '_raf_meta', $raf_meta );
		update_post_meta( $post_id, '_wpgens_raf_meta', $raf_meta);
	}



}

new WPGENS_RAF_Admin_Order();