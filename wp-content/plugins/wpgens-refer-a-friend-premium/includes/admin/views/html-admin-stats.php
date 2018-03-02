<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.wpgens.com
 * @since      1.0.0
 *
 * @package    Fav_Gens
 * @subpackage Fav_Gens/admin/partials
 */

?>
<div style="background-color:#985DC4;min-height:120px;margin-left:-20px;">
    <h1 style="color:#fff;margin:0;line-height:120px;margin-left:25px;"><?php _e('Refer a Friend by WPGens','gens-raf'); ?></h1>
</div>
<div style="background-color:#fff;min-height:60px;margin-left:-20px;">
    <h2 style="color:#222;margin:0;line-height:60px;margin-left:25px;"><?php _e('Orders made through referral link:','gens-raf'); ?></h2>
</div>
<?php if(isset($_GET['message'])) { ?>
	<div id="message" class="error notice is-dismissible">
		<p><?php echo $_GET['message']; ?></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button>
	</div>
<?php } ?>
<div class="wrap">
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="postbox-container-2" class="postbox-container">
            <?php
                $tables = new RAF_List_Table();
                $tables->prepare_items();
                $tables->display();
            ?>
            </div>
            <div id="postbox-container-1" class="postbox-container" style="margin-top:40px;">
                <div id="priority_side-sortables" class="meta-box-sortables ui-sortable">
                    <div class="postbox ">
                        <h3 class="hndle"><span><?php _e('Number of referrals per user:','gens-raf'); ?></span></h3>
                        <div class="inside">
                            <ul>
                                <?php
                                    $args = array('meta_key' => "gens_num_friends",'orderby' => 'meta_value_num','order' => 'DESC');
                                    $users = get_users($args);
                                    foreach ($users as $user) {
                                        $num_friends_refered = get_user_meta($user->ID, "gens_num_friends", true);
                                        echo "<li><a href='".get_edit_user_link( $user->ID )."'>".$user->user_email."</a><span style='float:right;'>".$num_friends_refered."</span></li>";
                                    }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="priority_side-sortables" class="meta-box-sortables ui-sortable">
                    <div class="postbox ">
                        <h3 class="hndle"><span><?php _e('Licence Key:','gens-raf'); ?></span></h3>
                        <div class="inside">

	                        <form method="post" action="options.php">
		                        <?php settings_fields('gens_raf_license'); ?>
		                        <input id="gens_raf_license_key" name="gens_raf_license_key" type="text" style="width:100%" value="<?php esc_attr_e( $license ); ?>" />
		                        <?php if( $status == false || $status != 'valid' ) { ?>
		                        <label class="description" for="gens_raf_license_key"><?php _e('Enter your valid license key to receive automatic updates, save and then click on activate license.','gens-raf'); ?></label>
		                        <?php } ?>
		                        <div>
			                        <?php if( false !== $license ) { ?>
				                        <?php if( $status !== false && $status == 'valid' ) { ?>
					                        <div style="color:green;"><?php _e('Licence is active.'); ?></div>

					                        <?php wp_nonce_field( 'gens_raf_nonce', 'gens_raf_nonce' ); ?>
					                        <input type="submit" class="button-secondary" name="gens_raf_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
				                        <?php } else {
					                        wp_nonce_field( 'gens_raf_nonce', 'gens_raf_nonce' ); ?>
					                        <input type="submit" class="button-secondary" name="gens_raf_license_activate" value="<?php _e('Activate License'); ?>"/>
				                        <?php } ?>
			                        <?php } ?>
		                        </div>
		                        <?php submit_button('Save new key'); ?>
	                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
