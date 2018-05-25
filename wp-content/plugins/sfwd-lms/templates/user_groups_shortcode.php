<?php
/**
 * Displays a user group.
 *
 * @since 2.1.0
 * 
 * @package LearnDash\Groups
 */
?>

<?php if ( $has_admin_groups ) : ?>	
	<b><?php esc_html_e( 'Group Leader in : ', 'learndash' ); ?></b>
    <br />
	<?php  foreach ( $admin_groups as $group_id ) : ?>
		<?php if ( ! empty( $group_id ) ) : ?>
			<?php $group = get_post( $group_id ); ?>
			<b><?php echo $group->post_title; ?></b>
			<p><?php echo trim( $group->post_content ); ?></p>
		<?php endif; ?>
	<?php endforeach; ?>
	<br />
    <br />
<?php endif; ?>

<?php if ( $has_user_groups ) : ?>
	<b><?php esc_html_e( 'Assigned Group(s) : ', 'learndash' ); ?></b>
    <br />
	<?php foreach( $user_groups as $group_id ) : ?>
		<?php if( ! empty( $group_id ) ) : ?>
			<?php $group = get_post( $group_id ); ?>
			<b><?php echo $group->post_title; ?></b>
			<p><?php echo trim( $group->post_content ); ?></p>
			<?php endif; ?>
	<?php endforeach; ?>
	<br />
    <br />
<?php endif; ?>