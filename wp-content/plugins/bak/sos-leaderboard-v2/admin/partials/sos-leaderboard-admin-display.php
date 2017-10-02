<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       -
 * @since      1.0.0
 *
 * @package    Sos_Leaderboard
 * @subpackage Sos_Leaderboard/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
	<h1 class="wp-heading-inline">Leaderboard</h1>
	<hr class="wp-header-end">
	<?php

	if ( function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {

		$sites = get_sites();

			// Set Counter
		$i = 0;

			// Set Time Vars
		$current_year = date('Y');
		$current_month = date('m');
		$last_month = date('m', strtotime('-1 month', time()));
		$last_year = date('Y', strtotime('-1 year', time()));
		$sitesArray = array(); 
		?>

		<table class="wp-list-table widefat fixed striped posts">
			<thead>
				<tr>
					<th class="manage-column column-author">Rank</th>
					<th class="manage-column column-primary">School</th>
					<th class="manage-column column-date">Orders</th>
				</tr>
			</thead>
			<tbody>
				<?php

				foreach ( $sites as $site ) {

					switch_to_blog( $site->blog_id );


				// // Set Vars
					$chapter_name = get_option( 'blogname' );					

				// Current Month Order Query
					$current_month_args = array('post_type' => 'shop_order', 'post_status' => 'complete', 'date_query' => array( array( 'month' => $current_month)));
					$current_month_orders = new WP_Query($current_month_args);
					$current_month_order_count = $current_month_orders->found_posts;

				// Create Array
					$sitesArray[ $current_month_order_count ] = $chapter_name;

					restore_current_blog();
				}
				
			// Sort Array Descending
				krsort($sitesArray, SORT_NUMERIC);

			// Loop over Array items displaying values
				foreach ($sitesArray as $orders => $chapter) { 

			// Start Counter
					$i++;

					?>
					
					<tr>
						<th scope="row"><?php echo $i; ?></th>
						<td><?php echo $chapter; ?></td>
						<td><?php echo $orders; ?></td>
					</tr>
					<?php 

				} ?>
				
			</tbody>
		</table>

		<?php return;
	}


	?>
</div>


