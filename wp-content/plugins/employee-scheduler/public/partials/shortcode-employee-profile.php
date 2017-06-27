<?php
/**
 * Profile Form
 *
 * HTML template for the form to edit user profile fields
 *
 * @package Shiftee Basic
 * @subpackage Shiftee Basic/public/partials
 * @since 1.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; ?>
<p class='error'><?php implode('<br />', $error); ?></p>
<form method="post" id="adduser" action="<?php the_permalink(); ?>">
	<table class="form-table">
		<tr>
			<th><label for="first-name"><?php _e('First Name', 'employee-scheduler'); ?></label></th>
			<td>
				<input type="text" name="first-name" id="first-name" value="<?php echo get_the_author_meta( 'first_name', $current_user->ID ); ?>" class="regular-text" /><br />
			</td>
		</tr>
		<tr>
			<th><label for="last-name"><?php _e('Last Name', 'employee-scheduler'); ?></label></th>
			<td>
				<input type="text" name="last-name" id="last-name" value="<?php echo get_the_author_meta( 'last_name', $current_user->ID ); ?>" class="regular-text" /><br />
			</td>
		</tr>
		<tr>
			<th><label for="last-name"><?php _e('E-mail', 'employee-scheduler'); ?></label></th>
			<td>
				<input type="text" name="email" id="email" value="<?php echo get_the_author_meta( 'user_email', $current_user->ID ); ?>" class="regular-text" /><br />
			</td>
		</tr>
		<tr>
			<th><label for="pass1"><?php _e('Password', 'employee-scheduler'); ?></label></th>
			<td>
				<input type="password" name="pass1" id="pass1" class="regular-text" /><br />
			</td>
		</tr>
		<tr>
			<th><label for="pass2"><?php _e('Repeat Password', 'employee-scheduler'); ?></label></th>
			<td>
				<input type="password" name="pass2" id="pass2" class="regular-text" /><br />
			</td>
		</tr>

		<?php do_action( 'shiftee_additional_user_profile_fields', $current_user ); ?>
	</table>

	<?php do_action( 'edit_user_profile', $current_user );  ?>
	<p class='form-submit'>
		<input name='updateuser' type='submit' id='updateuser' class='submit button' value='<?php _e('Update', 'employee-scheduler'); ?>' />
		<?php wp_nonce_field( 'update-user' ); ?>
		<input name='action' type='hidden' id='action' value='update-user' />
	</p><!-- .form-submit -->
</form><!-- #adduser -->
