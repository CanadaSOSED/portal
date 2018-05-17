<?php
/**
 * Template Name: ProPanel Full Page 
 *
 * This template can be used to display all the LearnDash ProPanel widgets on a single front-end page. This template will only load the needed
 * styles and scripts used for the widget output and will not conflict with theme or other plugins. 
 * 
 * Usage:
 * By default this template is available by URL. For example http://www.your-site.com/?ld_propanel
 *
 * @package LearnDash ProPanel
 * @since 2.1
 */
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />

		<title><?php _e('LearnDash ProPanel', 'ld_propanel' ); ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<?php 
		wp_print_styles();
		wp_print_scripts();
		?>
  </head>
  
  <body class="ld-propanel-full-page">
    
  	<div class="columnsContainer">

	  	<div class="leftColumn">
			<?php if ( ( is_user_logged_in() ) && ( ( learndash_is_group_leader_user() ) || ( learndash_is_admin_user() ) || ( current_user_can( 'propanel_widgets' ) ) ) 
			 && ( apply_filters('ld_propanel_shortcode_show', 'reporting', get_current_user_id() ) ) ) { ?>
				 <h2><?php _e('ProPanel Reporting', 'ld_propanel'); ?></h2>
				 <?php echo do_shortcode('[ld_propanel widget="reporting"]'); ?>
			<?php } ?>
			
			<?php if ( ( is_user_logged_in() ) && ( ( learndash_is_group_leader_user() ) || ( learndash_is_admin_user() ) || ( current_user_can( 'propanel_widgets' ) ) ) 
			 && ( apply_filters('ld_propanel_shortcode_show', 'activity', get_current_user_id() ) ) ) { ?>
				 <h2><?php _e('ProPanel Progress Activity', 'ld_propanel'); ?></h2>
				 <?php echo do_shortcode('[ld_propanel widget="activity"]'); ?>
			<?php } ?>
	  	</div>

	  	<div class="rightColumn">
			<?php if ( ( is_user_logged_in() ) && ( ( learndash_is_group_leader_user() ) || ( learndash_is_admin_user() ) ) ) { ?>
				<h2><?php _e('ProPanel Overview', 'ld_propanel'); ?></h2>
				<?php echo do_shortcode('[ld_propanel widget="overview"]'); ?>
			<?php } ?>

			<?php if ( ( is_user_logged_in() ) && ( ( learndash_is_group_leader_user() ) || ( learndash_is_admin_user() ) || ( current_user_can( 'propanel_widgets' ) ) ) 
			 && ( apply_filters('ld_propanel_shortcode_show', 'filtering', get_current_user_id() ) ) ) { ?>
				 <h2><?php _e('ProPanel Filtering', 'ld_propanel'); ?></h2>
				 <?php echo do_shortcode('[ld_propanel widget="filtering"]'); ?>
			<?php } ?>
				 			
			<?php if ( ( is_user_logged_in() ) && ( ( learndash_is_group_leader_user() ) || ( learndash_is_admin_user() ) || ( current_user_can( 'propanel_widgets' ) ) ) 
			 && ( apply_filters('ld_propanel_shortcode_show', 'progress_chart', get_current_user_id() ) ) ) { ?>
				 <h2><?php _e('ProPanel Progress Chart', 'ld_propanel'); ?></h2>
				<?php echo do_shortcode('[ld_propanel widget="progress_chart"]'); ?>
			<?php } ?>
	  	</div>
  	</div>
    
	<?php /* ?>
    <footer>
  	</footer>
	<?php */ ?>
	<?php 
		wp_print_footer_scripts();
	?>
  </body>
</html>
<?php ref: https://codepen.io/johnstonian/pen/guhid ?>