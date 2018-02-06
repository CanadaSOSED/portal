<?php
/**
 * Show RAF User meta in profile edit screen
 * @author    WPGens
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WPGENS_RAF_User_Meta {

	/**
	 * Hook in profile tabs.
	 */
	public function __construct() {
		add_action( 'edit_user_profile', array($this, 'raf_user_profile_field') );
		add_action( 'show_user_profile', array($this, 'raf_user_profile_field') );
	}

	/**
	 * Add user referral code to backend user profile
	 *
	 * @since 2.0.0
	 */
	public function raf_user_profile_field($user) {
		?>
		<table class="form-table">
	        <tr>
	            <th>
	                <label for="code"><?php _e( 'Refer a friend Link','gens-raf' ); ?></label>
				</th>
				<td>
					<?php if(get_user_meta($user->ID, 'gens_referral_id', true ) != "") {
						echo get_home_url() .'/?raf='. esc_attr( get_user_meta($user->ID, 'gens_referral_id', true ) );
					} else {
						echo "Go to Woocommerce -> System Status -> Tools -> click on 'Create referrals' and then come back here";
					} ?>
				</td>
			</tr>
		</table>
	<?php
	}

}

new WPGENS_RAF_User_Meta();