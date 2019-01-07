<?php
/**
 * The template for displaying Comments for KB Article.
 *
 * This template can be overridden by copying it to yourtheme/kb_templates/feature-comments.php.
 *
 * HOWEVER, on occasion Echo Plugins will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author 		Echo Plugins
 */
/** @var WP_Post $article */
/** @var EPKB_KB_Config_DB $kb_config */

if ( post_password_required() ) { ?>
	<p><?php _e( 'This page is password protected. Enter the password to view comments.', 'echo-knowledge-base' ); ?></p>	<?php
	return;
}

if ( comments_open() || get_comments_number() ) {

	echo '<div class="epkb-comments-container">';

	comments_template( '', true );

	echo '</div>';
}