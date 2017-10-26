<?php
/**
 * Plugin Name: SOS Multisite Update Options Table
 * Plugin URI: 
 * Description: Use with caution!!! This plugin will change the selected wp_option value for ALL sites on the network.
 * Version: 1.0 
 * Author: SOS Development Team <briancaicco@gmail.com>
 * Author URI: 
 * License: GPL2
 *
 * Text Domain: wordpress
 * Domain Path: /languages/
 *
 */


add_action('network_admin_menu', 'sos_add_admin_menu');


function sos_add_admin_menu(  ) { 

	//add_submenu_page( 'tools.php', 'SOS Update Option', 'SOS Update Option', 'manage_options', 'sos_update_option', 'sos_options_page' );
	
	add_options_page('SOS Update Option', 'SOS Update Option', 'manage_network', 'sos_update_option', 'sos_options_page_form');

	add_action('init', 'sos_options_page');

}


function sos_options_page_form( ) { 

	?>
	<style type="text/css">
		.form-field input[type=text]{
			width: 300px !important;
		}
	</style>
	<div class="wrap">
		<h1>SOS Multisite Update Option (wp_options table)</h1>

		<p>Use with caution!!! This plugin will change the selected wp_option value for <b>ALL sites on the network.</b> 

			<form method='POST'>
				<table class="form-table">
					<tbody>
						<tr class="form-field">
							<th scope="row"><label for="blog_registered">Option</label></th>
							<td><input type="text" name="option"></td>
						</tr>
						<tr class="form-field">
							<th scope="row"><label for="blog_last_updated">Value</label></th>
							<td><input type="text" name="value"></td>
						</tr>

					</tbody>
				</table>
				<p class="submit"><input type="submit" name="update" id="submit" class="button button-primary" value="Update Option"></p>
			</form>

		</div>
		<?php }


		function sos_options_page( ) { 

			if(isset($_POST['update'])){
				$option = $_POST['option'];
				$value = $_POST['value'];
				$sites = get_sites();

				ini_set( 'display_errors', 1 );

				foreach ( $sites as $site ) {

					if(get_option($option)){

						switch_to_blog( $site->blog_id );

						echo 'Updating ' . $option . ' for site: ', $site->path, '<br>';

						update_option( $option, $value );

						restore_current_blog();

					}

					else {


						echo $option . ' does not exists for: ', $site->path, '<br>';


				// switch_to_blog( $site->blog_id );

				// echo 'Adding ' . $option . ' for site: ', $site->path, '<br>';

				// update_option( $option, $value );

				// restore_current_blog();

					}

				}
				echo '<h2>Misson accomplished. </h2>';
			}

		}