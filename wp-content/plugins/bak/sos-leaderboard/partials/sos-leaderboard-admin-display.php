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
	<h2>Leaderboard</h2>
	
	<?php
	if( isset( $_GET[ 'tab' ] ) ) {
		$active_tab = $_GET[ 'tab' ];
	 } // end if

	 $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'Orders';
	 ?>
	 
	 <!-- Tabs --> 
	 <nav class="nav-tab-wrapper woo-nav-tab-wrapper">
		<a href="?page=sos-leaderboard&tab=Orders" class="nav-tab <?php echo $active_tab == 'Orders' ? 'nav-tab-active' : ''; ?>">Orders</a>
		<a href="?page=sos-leaderboard&tab=Revenue" class="nav-tab <?php echo $active_tab == 'Revenue' ? 'nav-tab-active' : ''; ?>">Revenue</a>
	 </nav>
	 <ul class="subsubsub">
	 	<li><a href="<?php echo '?page=sos-leaderboard&tab=' . $active_tab . '&amp;report=digital-exam-aid-orders'; ?>" class="">Digital Exam Aids</a></li>
	</ul>
	<br class="clear" />

	 <div id="poststuff" class="woocommerce-reports-wide">
	 	<div class="postbox">

		<div class="stats_range">
			<ul>
				<?php
				foreach ( $ranges as $range => $name ) {
					echo '<li class="' . ( $current_range == $range ? 'active' : '' ) . '"><a href="' . esc_url( remove_query_arg( array( 'start_date', 'end_date' ), add_query_arg( 'range', $range ) ) ) . '">' . $name . '</a></li>';
				}
				?>
				<li class="custom <?php echo ( 'custom' === $current_range ) ? 'active' : ''; ?>">
					<?php _e( 'Custom:', 'woocommerce' ); ?>
					<form method="GET">
						<div>
							<?php
								// Maintain query string
							foreach ( $_GET as $key => $value ) {
								if ( is_array( $value ) ) {
									foreach ( $value as $v ) {
										echo '<input type="hidden" name="' . esc_attr( sanitize_text_field( $key ) ) . '[]" value="' . esc_attr( sanitize_text_field( $v ) ) . '" />';
									}
								} else {
									echo '<input type="hidden" name="' . esc_attr( sanitize_text_field( $key ) ) . '" value="' . esc_attr( sanitize_text_field( $value ) ) . '" />';
								}
							}
							?>
							<input type="hidden" name="range" value="custom" />
							<input type="text" size="11" placeholder="yyyy-mm-dd" value="<?php echo ( ! empty( $_GET['start_date'] ) ) ? esc_attr( $_GET['start_date'] ) : ''; ?>" name="start_date" class="range_datepicker from" />
							<span>&ndash;</span>
							<input type="text" size="11" placeholder="yyyy-mm-dd" value="<?php echo ( ! empty( $_GET['end_date'] ) ) ? esc_attr( $_GET['end_date'] ) : ''; ?>" name="end_date" class="range_datepicker to" />
							<input type="submit" class="button" value="<?php esc_attr_e( 'Go', 'woocommerce' ); ?>" />
							<?php wp_nonce_field( 'custom_range', 'wc_reports_nonce', false ); ?>
						</div>
					</form>
				</li>
			</ul>
		</div>

		<div class="main">
			<?php $this->get_main_chart(); ?>
		</div>

	 </div>
	</div>

 </div>