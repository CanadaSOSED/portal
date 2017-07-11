<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
add_thickbox();
$settings = $WC_Order_Export->get_export_settings( $mode, $id );
$order_custom_meta_fields = WC_Order_Export_Data_Extractor::get_all_order_custom_meta_fields();

//var_dump( $WC_Order_Export->get_value( $settings, '[schedule][type]' ) );
?>

<script>
	var mode = '<?php echo $mode ?>';
	var job_id = '<?php echo $id ?>';
	var output_format = '<?php echo $settings[ 'format' ] ?>';
	var order_fields = <?php echo json_encode( $settings[ 'order_fields' ] ) ?>;
	var order_products_fields = <?php echo json_encode( $settings[ 'order_product_fields' ] ) ?>;
	var order_coupons_fields = <?php echo json_encode( $settings[ 'order_coupon_fields' ] ) ?>;
	var order_custom_meta_fields = <?php echo json_encode( $order_custom_meta_fields ) ?>;
	var order_products_custom_meta_fields = <?php echo json_encode( WC_Order_Export_Data_Extractor::get_all_product_custom_meta_fields() ) ?>;
	var order_coupons_custom_meta_fields = <?php echo json_encode( WC_Order_Export_Data_Extractor::get_all_coupon_custom_meta_fields() ) ?>;
</script>


<?php include 'modal-controls.php'; ?>
<form method="post" id="export_job_settings">
	<?php if ( $mode !== $WC_Order_Export::EXPORT_NOW ): ?>
		<div style="width: 100%;">&nbsp;</div>
	<?php endif; ?>
	
	<div id="my-left" style="float: left; width: 49%; max-width: 500px;">
		<?php if ( $mode === $WC_Order_Export::EXPORT_PROFILE ): ?>
			<div class="my-block">
				<div style="display: inline;">
					<span class="wc-oe-header"><?php _e( 'Title', 'woocommerce-order-export' ) ?></span>
					<input type=text  style="width: 90%;" name="settings[title]" value='<?php echo ( isset( $settings[ 'title' ] ) ? $settings[ 'title' ] : '' ) ?>'>
				</div>
				<div>
					<label>
						<input type="checkbox" name="settings[use_as_bulk]" class="width-100" <?php echo isset( $settings[ 'use_as_bulk' ] ) ? 'checked' : '' ?>>
						<?php _e( 'Use as bulk action', 'woocommerce-order-export' ) ?>
					</label>
				</div>
			</div>
			<br>
		<?php elseif ( $mode === $WC_Order_Export::EXPORT_ORDER_ACTION ): ?>
			<div class="my-block">
				<div style="display: inline;">
					<span class="wc-oe-header"><?php _e( 'Title', 'woocommerce-order-export' ) ?></span>
					<input type=text  style="width: 90%;" name="settings[title]" value='<?php echo ( isset( $settings[ 'title' ] ) ? $settings[ 'title' ] : '' ) ?>'>
				</div>
			</div>
			<br>
			<div class="my-block">
				<div>
					<span class="wc-oe-header"><?php _e( 'From status', 'woocommerce-order-export' ) ?></span>
					<select id="from_status" name="settings[from_status][]" multiple="multiple" style="width: 100%; max-width: 25%;">
						<?php foreach ( wc_get_order_statuses() as $i => $status ) { ?>
							<option value="<?php echo $i ?>" <?php if ( in_array( $i, $settings[ 'from_status' ] ) ) echo 'selected'; ?>><?php echo $status ?></option>
						<?php } ?>
					</select>
				</div>
				<div>
					<span class="wc-oe-header"><?php _e( 'To status', 'woocommerce-order-export' ) ?></span>
					<select id="to_status" name="settings[to_status][]" multiple="multiple" style="width: 100%; max-width: 25%;">
						<?php foreach ( wc_get_order_statuses() as $i => $status ) { ?>
							<option value="<?php echo $i ?>" <?php if ( in_array( $i, $settings[ 'to_status' ] ) ) echo 'selected'; ?>><?php echo $status ?></option>
						<?php } ?>
					</select>
				</div>
				<?php if( function_exists( "wc_get_logger" ) ) : ?>
				<div>
					<label>
						<input type="checkbox" name="settings[log_results]" <?php echo isset( $settings[ 'log_results' ] ) ? 'checked' : '' ?>>
						<?php _e( 'Log results', 'woocommerce-order-export' ) ?>&nbsp;<a href="admin.php?page=wc-status&tab=logs&source=woocommerce-order-export" target=_blank><?php _e( 'View logs', 'woocommerce-order-export' ) ?></a>
					</label>
				</div>
				<?php endif; ?>
			</div>
			<hr>
		<?php elseif ( $mode === $WC_Order_Export::EXPORT_SCHEDULE ): ?>
			<div class="my-block">
				<div style="display: inline;">
					<span class="wc-oe-header"><?php _e( 'Title', 'woocommerce-order-export' ) ?></span>
					<input type=text  style="width: 90%;" name="settings[title]" value='<?php echo ( isset( $settings[ 'title' ] ) ? $settings[ 'title' ] : '' ) ?>'>
				</div>
			</div>
			<div id="my-shedule-days" class="my-block">
				<div class="wc-oe-header"><?php _e( 'Schedule', 'woocommerce-order-export' ) ?></div>
				<div id="d-schedule-1">
					<input type="radio" name="settings[schedule][type]" value="schedule-1" id="schedule-1" class="wc-oe-schedule-type" <?php echo ((isset( $settings[ 'schedule' ] ) and $settings[ 'schedule' ][ 'type' ] == 'schedule-1') or ! isset( $settings[ 'schedule' ] )) ? 'checked' : '' ?>>
					<div class="weekday">

						<label>
							<?php _e( 'Sun', 'woocommerce-order-export' ) ?>
							<input type="checkbox" name="settings[schedule][weekday][Sun]" <?php echo isset( $settings[ 'schedule' ][ 'weekday' ][ 'Sun' ] ) ? 'checked' : '' ?>>
						</label>
						<label>
							<?php _e( 'Mon', 'woocommerce-order-export' ) ?>
							<input  type="checkbox" name="settings[schedule][weekday][Mon]" <?php echo isset( $settings[ 'schedule' ][ 'weekday' ][ 'Mon' ] ) ? 'checked' : '' ?>>
						</label>
						<label>
							<?php _e( 'Tue', 'woocommerce-order-export' ) ?>
							<input  type="checkbox" name="settings[schedule][weekday][Tue]" <?php echo isset( $settings[ 'schedule' ][ 'weekday' ][ 'Tue' ] ) ? 'checked' : '' ?>>
						</label>
						<label>
							<?php _e( 'Wed', 'woocommerce-order-export' ) ?>
							<input  type="checkbox" name="settings[schedule][weekday][Wed]" <?php echo isset( $settings[ 'schedule' ][ 'weekday' ][ 'Wed' ] ) ? 'checked' : '' ?>>
						</label>
						<label>
							<?php _e( 'Thu', 'woocommerce-order-export' ) ?>
							<input  type="checkbox" name="settings[schedule][weekday][Thu]" <?php echo isset( $settings[ 'schedule' ][ 'weekday' ][ 'Thu' ] ) ? 'checked' : '' ?>>
						</label>
						<label>
							<?php _e( 'Fri', 'woocommerce-order-export' ) ?>
							<input  type="checkbox" name="settings[schedule][weekday][Fri]" <?php echo isset( $settings[ 'schedule' ][ 'weekday' ][ 'Fri' ] ) ? 'checked' : '' ?>>
						</label>
						<label>
							<?php _e( 'Sat', 'woocommerce-order-export' ) ?>
							<input  type="checkbox" name="settings[schedule][weekday][Sat]" <?php echo isset( $settings[ 'schedule' ][ 'weekday' ][ 'Sat' ] ) ? 'checked' : '' ?>>
						</label>
					</div>
					<div class="">
						<label style="margin-left: 10px;"><?php _e( 'Run at', 'woocommerce-order-export' ) ?>:
							<select name="settings[schedule][run_at]" style="width: 80px">
								<?php
								for ( $i = 0; $i <= 23; $i++ ) :
									$h = ($i < 10) ? '0'.$i : $i;
									
									for($m =0; $m<60;$m+=5) :
										$time	 = "$h:" . ( $m<10 ? "0".$m : $m );
									?>
									<option <?php echo (isset( $settings[ 'schedule' ][ 'run_at' ] ) and $time == $settings[ 'schedule' ][ 'run_at' ]) ? 'selected' : '' ?>>
										<?php
										echo $time;
										?>
									</option>
									<?php endfor; ?>
								<?php endfor; ?>
							</select>
						</label>
					</div>
				</div>
				<div class="clearfix"></div>

				<div id="d-schedule-2" class="padding-bottom-10">
					<input type="radio" name="settings[schedule][type]" value="schedule-2" id="schedule-2" class="wc-oe-schedule-type" <?php echo (isset( $settings[ 'schedule' ] ) and $settings[ 'schedule' ][ 'type' ] == 'schedule-2') ? 'checked' : '' ?>>
					<select class="wc_oe-select-interval" name="settings[schedule][interval]">
						<option value="-1"><?php _e( 'Choose', 'woocommerce-order-export' ) ?></option>
						<option value="custom"  <?php echo (isset( $settings[ 'schedule' ][ 'interval' ] ) AND $settings[ 'schedule' ][ 'interval' ] == 'custom') ? 'selected' : '' ?>><?php _e( 'Custom', 'woocommerce-order-export' ) ?></option>
						<?php
						$schedules = wp_get_schedules();
						foreach ( $schedules as $name => $schedule ) :
							?>
							<option value="<?php echo $name ?>" <?php echo (isset( $settings[ 'schedule' ][ 'interval' ] ) AND $settings[ 'schedule' ][ 'interval' ] == $name ) ? 'selected' : '' ?>>
								<?php echo $schedule[ 'display' ] ?>
							</option>
						<?php endforeach; ?>
						<option value="first_day_month"  <?php echo (isset( $settings[ 'schedule' ][ 'interval' ] ) AND $settings[ 'schedule' ][ 'interval' ] == 'first_day_month') ? 'selected' : '' ?>><?php _e( 'On the 1st day of the month', 'woocommerce-order-export' ) ?></option>
						<option value="first_day_quarter"  <?php echo (isset( $settings[ 'schedule' ][ 'interval' ] ) AND $settings[ 'schedule' ][ 'interval' ] == 'first_day_quarter') ? 'selected' : '' ?>><?php _e( 'On the 1st day of the quarter', 'woocommerce-order-export' ) ?></option>
					</select>
					<label id="custom_interval">
						<?php _e( 'interval (min)', 'woocommerce-order-export' ) ?>:
						<input name="settings[schedule][custom_interval]" value="<?php echo isset( $settings[ 'schedule' ][ 'custom_interval' ] ) ? $settings[ 'schedule' ][ 'custom_interval' ] : '' ?>" >
					</label>
				</div>
				<?php if( function_exists( "wc_get_logger" ) ) : ?>
				<div id="d-schedule-3" class="padding-bottom-10">
					<label>
						<input type="checkbox" name="settings[log_results]" <?php echo isset( $settings[ 'log_results' ] ) ? 'checked' : '' ?>>
						<?php _e( 'Log results', 'woocommerce-order-export' ) ?>&nbsp;<a href="admin.php?page=wc-status&tab=logs&source=woocommerce-order-export" target=_blank><?php _e( 'View logs', 'woocommerce-order-export' ) ?></a>
					</label>
				</div>
				<?php endif; ?>
			</div>
			<br>
			<div id="my-export-options" class="my-block">
				<div class="wc-oe-header">
					<?php _e( 'Export options', 'woocommerce-order-export' ) ?>:
				</div>
				<label>
					<input type="radio" name="settings[export_rule]" class="width-100" <?php echo (!isset( $settings[ 'export_rule' ] ) || ($settings[ 'export_rule' ] == 'last_run')) ? 'checked' : '' ?> value="last_run" >				
					<?php _e( 'Since last Run', 'woocommerce-order-export' ) ?>
				</label>
				<br>
				<label>
					<input type="radio" name="settings[export_rule]" class="width-100" <?php echo (isset( $settings[ 'export_rule' ] ) && ($settings[ 'export_rule' ] == 'today')) ? 'checked' : '' ?> value="today" >					
					<?php _e( 'Today', 'woocommerce-order-export' ) ?>
				</label>
				<br>
				<label>
					<input type="radio" name="settings[export_rule]" class="width-100" <?php echo (isset( $settings[ 'export_rule' ] ) && ($settings[ 'export_rule' ] == 'last_day')) ? 'checked' : '' ?> value="last_day" >					
					<?php _e( 'Yesterday', 'woocommerce-order-export' ) ?>
				</label>
				<br>
				<label>
					<input type="radio" name="settings[export_rule]" class="width-100" <?php echo (isset( $settings[ 'export_rule' ] ) && ($settings[ 'export_rule' ] == 'last_week')) ? 'checked' : '' ?> value="last_week" >
					<?php _e( 'Last week', 'woocommerce-order-export' ) ?>		
				</label>
				<br>
				<label>
					<input type="radio" name="settings[export_rule]" class="width-100" <?php echo (isset( $settings[ 'export_rule' ] ) && ($settings[ 'export_rule' ] == 'last_month')) ? 'checked' : '' ?> value="last_month" >
					<?php _e( 'Last month', 'woocommerce-order-export' ) ?>		
				</label>
				<br>
				<label>
					<input type="radio" name="settings[export_rule]" class="width-100" <?php echo (isset( $settings[ 'export_rule' ] ) && ($settings[ 'export_rule' ] == 'last_quarter')) ? 'checked' : '' ?> value="last_quarter" >
					<?php _e( 'Last quarter', 'woocommerce-order-export' ) ?>		
				</label>
				<br>				
				<label>
					<input type="radio" name="settings[export_rule]" class="width-100" <?php echo (isset( $settings[ 'export_rule' ] ) && ($settings[ 'export_rule' ] == 'this_year')) ? 'checked' : '' ?> value="this_year" >
					<?php _e( 'This year', 'woocommerce-order-export' ) ?>		
				</label>
				<br>				
				<label>
					<input type="radio" name="settings[export_rule]" class="width-100" <?php echo (isset( $settings[ 'export_rule' ] ) && ($settings[ 'export_rule' ] == 'custom')) ? 'checked' : '' ?> value="custom" >
					<?php _e( 'Last', 'woocommerce-order-export' ) ?> <input class="width-15" name="settings[export_rule_custom]" value="<?php echo isset( $settings[ 'export_rule_custom' ] ) ? $settings[ 'export_rule_custom' ] : '3' ?>" > <?php _e( 'days', 'woocommerce-order-export' ) ?>			
				</label>
				<div class="wc-oe-header">
					<?php _e( 'Filter orders by', 'woocommerce-order-export' ) ?>:
				</div>
				<label>
					<input type="radio" name="settings[export_rule_field]" class="width-100" <?php echo (!isset( $settings[ 'export_rule' ] ) || ($settings[ 'export_rule_field' ] == 'date')) ? 'checked' : '' ?> value="date" >					
					<?php _e( 'date created', 'woocommerce-order-export' ) ?>
				</label>
				&#09;&#09;
				<label>
					<input type="radio" name="settings[export_rule_field]" class="width-100" <?php echo (isset( $settings[ 'export_rule' ] ) && ($settings[ 'export_rule_field' ] == 'modified')) ? 'checked' : '' ?> value="modified" >
					<?php _e( 'date modified', 'woocommerce-order-export' ) ?>
				</label>
			</div>
			<br>
		<?php endif; ?>
		
		<?php if ( $show[ 'date_filter' ] ) : ?>
			<div id="my-main" class="my-block">
				<div style="display: inline;">
					<span class="wc-oe-header"><?php _e( 'Date range', 'woocommerce-order-export' ) ?></span>
					<input type=text class='date' name="settings[from_date]" id="from_date" value='<?php echo $settings[ 'from_date' ] ?>'>
					<?php _e( 'to', 'woocommerce-order-export' ) ?>
					<input type=text class='date' name="settings[to_date]" id="to_date" value='<?php echo $settings[ 'to_date' ] ?>'>
				</div>

				<button id="my-quick-export-btn" class="button-primary"><?php _e( 'Express Export', 'woocommerce-order-export' ) ?></button>
			</div>
			<br>
		<?php endif; ?>
		
			<div id="my-export-file" class="my-block">
				<div class="wc-oe-header">
					<?php _e( 'Export filename', 'woocommerce-order-export' ) ?>:
				</div>
				<label id="export_filename" class="width-100">
					<input type="text" name="settings[export_filename]" class="width-100" value="<?php echo isset( $settings[ 'export_filename' ] ) ? $settings[ 'export_filename' ] : 'orders-%y-%m-%d-%h-%i-%s.xlsx' ?>" >
				</label>
			</div>
			<br>


		<div id="my-format" class="my-block">
			<span class="wc-oe-header"><?php _e( 'Format', 'woocommerce-order-export' ) ?></span><br>
			<p>
				<?php foreach ( WC_Order_Export_Admin::$formats as $format ) { ?>
					<label class="button-secondary">
						<input type=radio name="settings[format]" class="output_format" value="<?php echo $format ?>"
							   <?php if ( $format == $settings[ 'format' ] ) echo 'checked'; ?> ><?php echo $format ?>
						<span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span>
					</label>
				<?php } ?>
			</p>

			<div id='XLS_options' style='display:none'><strong><?php _e( 'XLS options', 'woocommerce-order-export' ) ?></strong><br>
				<input type=hidden name="settings[format_xls_use_xls_format]" value=0>
				<input type=hidden name="settings[format_xls_display_column_names]" value=0>
				<input type=hidden name="settings[format_xls_auto_width]" value=0>
				<input type=hidden name="settings[format_xls_populate_other_columns_product_rows]" value=0>
				<input type=hidden name="settings[format_xls_direction_rtl]" value=0>
				<input type=checkbox name="settings[format_xls_use_xls_format]" value=1 <?php if ( @$settings[ 'format_xls_use_xls_format' ] ) echo 'checked'; ?>  id="format_xls_use_xls_format">  <?php _e( 'Export as .xls (Binary File Format)', 'woocommerce-order-export' ) ?><br>
				<input type=checkbox name="settings[format_xls_display_column_names]" value=1 <?php if ( @$settings[ 'format_xls_display_column_names' ] ) echo 'checked'; ?>  >  <?php _e( 'Output column titles as first line', 'woocommerce-order-export' ) ?><br>
				<input type=checkbox name="settings[format_xls_auto_width]" value=1 <?php if ( @$settings[ 'format_xls_auto_width' ] ) echo 'checked'; ?>  >  <?php _e( 'Auto column width ', 'woocommerce-order-export' ) ?><br>
				<input type=checkbox name="settings[format_xls_populate_other_columns_product_rows]" value=1 <?php if ( @$settings[ 'format_xls_populate_other_columns_product_rows' ] ) echo 'checked'; ?>  >  <?php _e( 'Populate other columns if products exported as rows', 'woocommerce-order-export' ) ?><br>
				<input type=checkbox name="settings[format_xls_direction_rtl]" value=1 <?php if ( @$settings[ 'format_xls_direction_rtl' ] ) echo 'checked'; ?>  >  <?php _e( 'Right-to-Left direction', 'woocommerce-order-export' ) ?><br>
			</div>
			<div id='CSV_options' style='display:none'><strong><?php _e( 'CSV options', 'woocommerce-order-export' ) ?></strong><br>
				<input type=hidden name="settings[format_csv_add_utf8_bom]" value=0>
				<input type=hidden name="settings[format_csv_display_column_names]" value=0>
				<input type=hidden name="settings[format_csv_populate_other_columns_product_rows]" value=0>
				<input type=checkbox name="settings[format_csv_add_utf8_bom]" value=1 <?php if ( @$settings[ 'format_csv_add_utf8_bom' ] ) echo 'checked'; ?>  > <?php _e( 'Output utf-8 BOM', 'woocommerce-order-export' ) ?><br>
				<input type=checkbox name="settings[format_csv_display_column_names]" value=1 <?php if ( @$settings[ 'format_csv_display_column_names' ] ) echo 'checked'; ?>  >  <?php _e( 'Output column titles as first line', 'woocommerce-order-export' ) ?><br>
				<input type=checkbox name="settings[format_csv_populate_other_columns_product_rows]" value=1 <?php if ( @$settings[ 'format_csv_populate_other_columns_product_rows' ] ) echo 'checked'; ?>  >  <?php _e( 'Populate other columns if products exported as rows', 'woocommerce-order-export' ) ?><br>
				<?php _e( 'Enclosure', 'woocommerce-order-export' ) ?> <input type=text name="settings[format_csv_enclosure]" value='<?php echo $settings[ 'format_csv_enclosure' ] ?>' size=1>
				<?php _e( 'Field Delimiter', 'woocommerce-order-export' ) ?> <input type=text name="settings[format_csv_delimiter]" value='<?php echo $settings[ 'format_csv_delimiter' ] ?>' size=1>
				<?php _e( 'Line Break', 'woocommerce-order-export' ) ?><input type=text name="settings[format_csv_linebreak]" value='<?php echo $settings[ 'format_csv_linebreak' ] ?>' size=4><br>
				<?php if ( function_exists( 'iconv' ) ): ?>
					<?php _e( 'Code Page', 'woocommerce-order-export' ) ?><input type=text name="settings[format_csv_encoding]" value="<?php echo isset( $settings[ 'format_csv_encoding' ] ) ? $settings[ 'format_csv_encoding' ] : 'UTF-8' ?>"><br>
				<?php endif ?>
			</div>
			<div id='XML_options' style='display:none'><strong><?php _e( 'XML options', 'woocommerce-order-export' ) ?></strong><br>
				<span class="xml-title"><?php _e( 'Prepend XML', 'woocommerce-order-export' ) ?></span><input type=text name="settings[format_xml_prepend_raw_xml]" value='<?php echo $settings[ 'format_xml_prepend_raw_xml' ] ?>'><br>
				<span class="xml-title"><?php _e( 'Root tag', 'woocommerce-order-export' ) ?></span><input type=text name="settings[format_xml_root_tag]" value='<?php echo $settings[ 'format_xml_root_tag' ] ?>'><br>
				<span class="xml-title"><?php _e( 'Order tag', 'woocommerce-order-export' ) ?></span><input type=text name="settings[format_xml_order_tag]" value='<?php echo $settings[ 'format_xml_order_tag' ] ?>'><br>
				<span class="xml-title"><?php _e( 'Product tag', 'woocommerce-order-export' ) ?></span><input type=text name="settings[format_xml_product_tag]" value='<?php echo $settings[ 'format_xml_product_tag' ] ?>'><br>
				<span class="xml-title"><?php _e( 'Coupon tag', 'woocommerce-order-export' ) ?></span><input type=text name="settings[format_xml_coupon_tag]" value='<?php echo $settings[ 'format_xml_coupon_tag' ] ?>'><br>
				<span class="xml-title"><?php _e( 'Append XML', 'woocommerce-order-export' ) ?></span><input type=text name="settings[format_xml_append_raw_xml]" value='<?php echo $settings[ 'format_xml_append_raw_xml' ] ?>'><br>
			</div>

			<div id='JSON_options' style='display:none'></div>

			<br>
			<div id="my-date-time-format" class="">
				<div id="date_format_block">
					<span class="wc-oe-header"><?php _e( 'Date', 'woocommerce-order-export' ) ?></span>
					<?php
					$date_format = array(
							'',
							'F j, Y',
							'Y-m-d',
							'm/d/Y',
							'd/m/Y',
					);
					$date_format = apply_filters( 'woe_date_format', $date_format );
					?>
					<select name="settings[date_format]">
						<?php foreach( $date_format as $format ):  ?>
							<option value="<?php echo $format ?>" <?php echo selected( @$settings[ 'date_format' ], $format ) ?> ><?php echo !empty( $format ) ? current_time( $format ) : __( '-', 'woocommerce-order-export' ) ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div id="time_format_block">
					<span class="wc-oe-header"><?php _e( 'Time', 'woocommerce-order-export' ) ?></span>
					<?php
					$time_format = array(
							'',
							'g:i a',
							'g:i A',
							'H:i',
					);
					$time_format = apply_filters( 'woe_time_format', $time_format );
					?>
					<select name="settings[time_format]">
						<?php foreach( $time_format as $format ):  ?>
							<option value="<?php echo $format ?>" <?php echo selected( @$settings[ 'time_format' ], $format ) ?> ><?php echo !empty( $format ) ? current_time( $format ) : __( '-', 'woocommerce-order-export' ) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>
		<br/>
		<div id="my-sort" class="my-block">
		<?php _e( 'Sort orders by "Order Id" in  ', 'woocommerce-order-export' ) ?>
		<select name="settings[sort_direction]">
			<option value='DESC' <?php echo selected( @$settings[ 'sort_direction' ], 'DESC') ?> ><?php _e( 'Descending', 'woocommerce-order-export' ) ?></option>
			<option value='ASC'  <?php echo selected( @$settings[ 'sort_direction' ], 'ASC') ?> ><?php _e( 'Ascending', 'woocommerce-order-export' ) ?></option>
		</select><?php _e( ' order', 'woocommerce-order-export' ) ?>
		</div>
	</div>

	<div id="my-right" style="float: left; width: 48%; margin: 0px 10px; max-width: 500px;">
		<?php if ( in_array( $mode, array( $WC_Order_Export::EXPORT_SCHEDULE, $WC_Order_Export::EXPORT_ORDER_ACTION ) ) ): ?>
			<div id="my-shedule-destination" class="my-block">
				<div class="wc-oe-header"><?php _e( 'Destination', 'woocommerce-order-export' ) ?></div>

				<label class="button-secondary"><input type=radio name="settings[destination][type]" class="output_destination" value="email"
					<?php if ( isset( $settings[ 'destination' ][ 'type' ] ) AND $settings[ 'destination' ][ 'type' ] == 'email' ) echo 'checked'; ?>
													   > <?php _e( 'EMAIL', 'woocommerce-order-export' ) ?>
					<span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span>
				</label>

				<label class="button-secondary"><input type=radio name="settings[destination][type]" class="output_destination" value="ftp"
					<?php if ( isset( $settings[ 'destination' ][ 'type' ] ) AND $settings[ 'destination' ][ 'type' ] == 'ftp' ) echo 'checked'; ?>
													   > <?php _e( 'FTP', 'woocommerce-order-export' ) ?>
					<span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span>
				</label>

				<label class="button-secondary"><input type=radio name="settings[destination][type]" class="output_destination" value="http"
					<?php if ( isset( $settings[ 'destination' ][ 'type' ] ) AND $settings[ 'destination' ][ 'type' ] == 'http' ) echo 'checked'; ?>
													   > <?php _e( 'HTTP POST', 'woocommerce-order-export' ) ?>
					<span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span>
				</label>

				<label class="button-secondary"><input type=radio name="settings[destination][type]" class="output_destination" value="folder"
					<?php if ( isset( $settings[ 'destination' ][ 'type' ] ) AND $settings[ 'destination' ][ 'type' ] == 'folder' ) echo 'checked'; ?>
													   > <?php _e( 'Folder', 'woocommerce-order-export' ) ?>
					<span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span>
				</label>

				<div class="padding-bottom-10 set-destination my-block" id="email" style="display: none;" >
					<div class="wc-oe-header"><?php _e( 'Email Settings', 'woocommerce-order-export' ) ?></div>
					<div class="wc_oe-row">
						<div class="col-100pr">
							<label><div><?php _e( 'From Email', 'woocommerce-order-export' ) ?></div>
								<input type="text" name="settings[destination][email_from]" class="width-100" value="<?php echo $WC_Order_Export->get_value( $settings, "[destination][email_from]" ); ?>">
							</label>
						</div>
					</div>
					<div class="wc_oe-row">
						<div class="col-100pr">
							<label><div><?php _e( 'From Name', 'woocommerce-order-export' ) ?></div>
								<input type="text" name="settings[destination][email_from_name]" class="width-100" value="<?php echo $WC_Order_Export->get_value( $settings, "[destination][email_from_name]" ); ?>">
							</label>
						</div>
					</div>
					<div class="wc_oe-row">
						<div class="col-100pr">
							<label><div><?php _e( 'Recipient(s)', 'woocommerce-order-export' ) ?></div>
								<textarea name="settings[destination][email_recipients]" class="width-100"><?php echo $WC_Order_Export->get_value( $settings, "[destination][email_recipients]" ); ?></textarea>
							</label>
						</div>
					</div>
					<div class="wc_oe-row">
						<div class="col-100pr">
							<label><div><?php _e( 'Email Subject', 'woocommerce-order-export' ) ?></div>
								<input type="text" name="settings[destination][email_subject]" class="width-100" value="<?php echo $WC_Order_Export->get_value( $settings, "[destination][email_subject]" ); ?>">
							</label>
						</div>
					</div>

					<div class="wc_oe-row">
						<div class="col-100pr">
							<label><div><?php _e( 'Email Body', 'woocommerce-order-export' ) ?></div>
								<a href="#TB_inline?width=400&height=400&inlineId=modal-email-body" class="thickbox"><?php _e( 'Edit Email Body', 'woocommerce-order-export' ) ?></a>
							</label>
						</div>
					</div>


					<div id="modal-email-body" >
							<label><div><?php _e( 'Email Body', 'woocommerce-order-export' ) ?></div>						
								<textarea name="settings[destination][email_body]" class="email_body_textarea" ><?php echo $WC_Order_Export->get_value( $settings, "[destination][email_body]" ); ?></textarea>
							</label>							
					</div>


					<div class="wc_oe-row">
						<div class="col-100pr">
							<label>
								<div class="wrap"><input name="" class="wc_oe_test my-test-button add-new-h2" data-test="email" type="button" value="<?php _e( 'Test', 'woocommerce-order-export' ) ?>"></div>					
							</label>
						</div>
					</div>
				</div>

				<div class="padding-bottom set-destination my-block" id="ftp" style="display: none;">
					<div class="wc-oe-header"><?php _e( 'FTP Settings', 'woocommerce-order-export' ) ?></div>
					<div class="wc_oe-row">
						<div class="col-50pr">
							<label><div><?php _e( 'Server Name', 'woocommerce-order-export' ) ?></div>
								<input type="text" name="settings[destination][ftp_server]" value="<?php echo $WC_Order_Export->get_value( $settings, "[destination][ftp_server]" ); ?>">
							</label>
						</div>
						<div class="col-50pr">
							<label><div><?php _e( 'Port', 'woocommerce-order-export' ) ?></div>
								<input type="text" name="settings[destination][ftp_port]" value="<?php echo $WC_Order_Export->get_value( $settings, "[destination][ftp_port]" ); ?>">
							</label>
						</div>
					</div>
					<div class="wc_oe-row">

						<div class="col-50pr">
							<label><div><?php _e( 'Username', 'woocommerce-order-export' ) ?></div>
								<input type="text" name="settings[destination][ftp_user]" value="<?php echo $WC_Order_Export->get_value( $settings, "[destination][ftp_user]" ); ?>">
							</label>
						</div>
						<div class="col-50pr">
							<label><div><?php _e( 'Password', 'woocommerce-order-export' ) ?></div>
								<input type="text" name="settings[destination][ftp_pass]" value="<?php echo $WC_Order_Export->get_value( $settings, "[destination][ftp_pass]" ); ?>">
							</label>
						</div>
					</div>
					<div class="wc_oe-row">
						<div class="col-100pr">
							<label><div><?php _e( 'Initial path', 'woocommerce-order-export' ) ?></div>
								<input type="text" class="width-100" name="settings[destination][ftp_path]" value="<?php echo $WC_Order_Export->get_value( $settings, "[destination][ftp_path]" ); ?>">
							</label>
						</div>
					</div>
					<div class="wc_oe-row">
						<div class="col-100pr">
							<label>
								<div class=""><input name="settings[destination][ftp_passive_mode]" type="checkbox" <?php echo $WC_Order_Export->get_value( $settings, "[destination][ftp_passive_mode]" ) ? 'checked' : ''; ?>><?php _e( 'Passive mode', 'woocommerce-order-export' ) ?></div>
							</label>
						</div>
						<div class="col-100pr">
							<label>
								<div class=""><input name="settings[destination][ftp_append_existing]" type="checkbox" <?php echo $WC_Order_Export->get_value( $settings, "[destination][ftp_append_existing]" ) ? 'checked' : ''; ?>><?php _e( 'Append to existing file(CURL is required!)', 'woocommerce-order-export' ) ?></div>
							</label>
						</div>
					</div>
					<div class="wc_oe-row">
						<div class="col-100pr">
							<label>
								<div class="wrap"><input name="" class="wc_oe_test my-test-button add-new-h2" data-test="ftp" type="button" value="<?php _e( 'Test', 'woocommerce-order-export' ) ?>"></div>
							</label>
						</div>
					</div>
				</div>

				<div class="padding-bottom-10 set-destination my-block" id="http" style="display: none;" >
					<div class="wc-oe-header"><?php _e( 'HTTP POST Settings', 'woocommerce-order-export' ) ?></div>
					<div class="wc_oe-row">
						<div class="col-100pr">
							<label>
								<div><?php _e( 'URL', 'woocommerce-order-export' ) ?></div>
								<input type="text" name="settings[destination][http_post_url]" class="width-100" value="<?php echo $WC_Order_Export->get_value( $settings, "[destination][http_post_url]" ); ?>">
							</label>
						</div>
					</div>
					<div class="wc_oe-row">
						<div class="col-100pr">
							<label>
								<div class="wrap"><input name="" class="wc_oe_test my-test-button add-new-h2" data-test="http" type="button" value="<?php _e( 'Test', 'woocommerce-order-export' ) ?>"></div>
							</label>
						</div>
					</div>
				</div>

				<div class="padding-bottom-10 set-destination my-block" id="folder" style="display: none;" >
					<div class="wc-oe-header"><?php _e( 'To Folder Settings', 'woocommerce-order-export' ) ?></div>
					<div class="wc_oe-row">
						<div class="col-100pr">
							<label>
								<div><?php _e( 'Path', 'woocommerce-order-export' ) ?></div>
								<input type="text" name="settings[destination][path]" class="width-100" value="<?php echo $WC_Order_Export->get_value( $settings, "[destination][path]" ) ? $WC_Order_Export->get_value( $settings, "[destination][path]" ) : ABSPATH; ?>">
							</label>
						</div>
					</div>
					<div class="wc_oe-row">
						<div class="col-100pr">
							<label>
								<div class="wrap"><input name="" class="wc_oe_test my-test-button add-new-h2" data-test="folder" type="button" value="<?php _e( 'Test', 'woocommerce-order-export' ) ?>"></div>
							</label>
						</div>
					</div>
				</div>			

				<div id='test_reply_div'>
					<b><?php _e( 'Test Results', 'woocommerce-order-export' ) ?></b><br>
					<textarea rows=5 id='test_reply' style="overflow: auto; width:100%" wrap='off'></textarea>
				</div>

				<div class="clear"></div>
				<br/>
				<div id="extend_desstination">
					<?php if ( $mode !== $WC_Order_Export::EXPORT_ORDER_ACTION ): ?>
						<div>
							<label>
								<input name="settings[destination][separate_files]" type="checkbox" value="1" <?php echo $WC_Order_Export->get_value( $settings, "[destination][separate_files]" ) ? 'checked' : ''; ?>><?php _e( 'Make separate file for each order', 'woocommerce-order-export' ) ?>
							</label>
						</div>
					<?php endif; ?>
				</div>
			</div>
			<br>
		<?php endif; ?>

		<div class="my-block">
			<span class="my-hide-next "><?php _e( 'Filter by order', 'woocommerce-order-export' ) ?>
				<span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span></span>
			<div id="my-order" hidden="hidden">
				<div><input type="hidden" name="settings[skip_suborders]" value="0"/><label><input type="checkbox" name="settings[skip_suborders]" value="1" <?php checked($settings[ 'skip_suborders' ]) ?> /> <?php _e( "Don't export child orders", 'woocommerce-order-export' ) ?></label></div>
				<div><input type="hidden" name="settings[export_refunds]" value="0"/><label><input type="checkbox" name="settings[export_refunds]" value="1" <?php checked($settings[ 'export_refunds' ]) ?> /> <?php _e( "Export refunds", 'woocommerce-order-export' ) ?></label></div>
				<span class="wc-oe-header"><?php _e( 'Order Statuses', 'woocommerce-order-export' ) ?></span>
				<select id="statuses" name="settings[statuses][]" multiple="multiple" style="width: 100%; max-width: 25%;">
					<?php foreach ( wc_get_order_statuses() as $i => $status ) { ?>
						<option value="<?php echo $i ?>" <?php if ( in_array( $i, $settings[ 'statuses' ] ) ) echo 'selected'; ?>><?php echo $status ?></option>
					<?php } ?>
				</select>
				
				<span class="wc-oe-header"><?php _e( 'Custom fields', 'woocommerce-order-export' ) ?></span>
				<br>
				<select id="custom_fields" style="width: auto;">
					<?php foreach ( WC_Order_Export_Data_Extractor::get_order_custom_fields() as $cf_name ) { ?>
						<option><?php echo $cf_name; ?></option>
					<?php } ?>
				</select>

				<select id="custom_fields_compare" class="select_compare">
					<option>=</option>
					<option>&lt;&gt;</option>
					<option>LIKE</option>
				</select>

				<input type="text" id="text_custom_fields" disabled style="display: none;">

				<button id="add_custom_fields" class="button-secondary"><span class="dashicons dashicons-plus-alt"></span></button>
				<br>
				<select id="custom_fields_check" multiple name="settings[order_custom_fields][]" style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings[ 'order_custom_fields' ] )
						foreach ( $settings[ 'order_custom_fields' ] as $prod ) {
							?>
							<option selected value="<?php echo $prod; ?>"> <?php echo $prod; ?></option>
						<?php } ?>
				</select>
				
			</div>
		</div>

		<br>
		<br>

		<div class="my-block">
			<div id=select2_warning style='display:none;color:red;font-size: 120%;'><?php _e( "The filters won't work correctly.<br>Another plugin(or theme) has loaded outdated Select2.js", 'woocommerce-order-export' ) ?></div>
			<span class="my-hide-next "><?php _e( 'Filter by product', 'woocommerce-order-export' ) ?>
				<span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span></span>
			<div id="my-products" hidden="hidden">
				<div><input type="hidden" name="settings[all_products_from_order]" value="0"/><label><input type="checkbox" name="settings[all_products_from_order]" value="1" <?php checked($settings[ 'all_products_from_order' ]) ?> /> <?php _e( 'Export all products from a order', 'woocommerce-order-export' ) ?></label></div>
				<span class="wc-oe-header"><?php _e( 'Product categories', 'woocommerce-order-export' ) ?></span>
				<select id="product_categories" name="settings[product_categories][]" multiple="multiple" style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings[ 'product_categories' ] )
						foreach ( $settings[ 'product_categories' ] as $cat ) {
							$cat_term = get_term( $cat, 'product_cat' );
							?>
							<option selected value="<?php echo $cat_term->term_id ?>"> <?php echo $cat_term->name; ?></option>
						<?php } ?>
				</select>
				<span class="wc-oe-header"><?php _e( 'Vendor/creator', 'woocommerce-order-export' ) ?></span>
				<select id="product_vendors" name="settings[product_vendors][]" multiple="multiple" style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings[ 'product_vendors' ] )
						foreach ( $settings[ 'product_vendors' ] as $user_id ) {
							$user = get_user_by( 'id', $user_id );
							?>
							<option selected value="<?php echo $user_id ?>"> <?php echo $user->display_name; ?></option>
						<?php } ?>
				</select>
				
                <?php do_action("woe_settings_filter_by_product_after_vendors", $settings); ?>

				<span class="wc-oe-header"><?php _e( 'Product', 'woocommerce-order-export' ) ?></span>

				<select id="products" name="settings[products][]" multiple="multiple" style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings[ 'products' ] )
						foreach ( $settings[ 'products' ] as $prod ) {
							$p = get_the_title( $prod );
							?>
							<option selected value="<?php echo $prod ?>"> <?php echo $p; ?></option>
						<?php } ?>
				</select>
           
				<span class="wc-oe-header"><?php _e( 'Product Taxonomies', 'woocommerce-order-export' ) ?></span>
				<br>
				<select id="taxonomies" style="width: auto;">
					<?php foreach ( WC_Order_Export_Data_Extractor::get_product_taxonomies() as $attr_id => $attr_name ) { ?>
						<option><?php echo $attr_name; ?></option>
					<?php } ?>
				</select>
				=
				<input type=text id="text_taxonomies" value=''> <button id="add_taxonomies" class="button-secondary"><span class="dashicons dashicons-plus-alt"></span></button>
				<br>
				<select id="taxonomies_check" multiple name="settings[product_taxonomies][]" style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings[ 'product_taxonomies' ] )
						foreach ( $settings[ 'product_taxonomies' ] as $prod ) {
							?>
							<option selected value="<?php echo $prod; ?>"> <?php echo $prod; ?></option>
						<?php } ?>
				</select>
				
				<span class="wc-oe-header"><?php _e( 'Product custom fields', 'woocommerce-order-export' ) ?></span>
				<br>
				<select id="product_custom_fields" style="width: auto;">
					<?php foreach ( WC_Order_Export_Data_Extractor::get_product_custom_fields() as $cf_name ) { ?>
						<option><?php echo $cf_name; ?></option>
					<?php } ?>
				</select>

				<select id="product_custom_fields_compare" class="select_compare">
					<option>=</option>
					<option>&lt;&gt;</option>
					<option>LIKE</option>
				</select>

				<input type="text" id="text_product_custom_fields" disabled style="display: none;">

				<button id="add_product_custom_fields" class="button-secondary"><span class="dashicons dashicons-plus-alt"></span></button>
				<br>
				<select id="product_custom_fields_check" multiple name="settings[product_custom_fields][]" style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings[ 'product_custom_fields' ] )
						foreach ( $settings[ 'product_custom_fields' ] as $prod ) {
							?>
							<option selected value="<?php echo $prod; ?>"> <?php echo $prod; ?></option>
						<?php } ?>
				</select>

				<span class="wc-oe-header"><?php _e( 'Variable Product Attributes', 'woocommerce-order-export' ) ?></span>
				<br>
				<select id="attributes" style="width: auto;">
					<?php foreach ( WC_Order_Export_Data_Extractor::get_product_attributes() as $attr_id => $attr_name ) { ?>
						<option><?php echo $attr_name; ?></option>
					<?php } ?>
				</select>

				<select id="attributes_compare" class="select_compare">
					<option>=</option>
					<option>&lt;&gt;</option>
					<option>LIKE</option>
				</select>

				<input type="text" id="text_attributes" disabled style="display: none;">

				<button id="add_attributes" class="button-secondary"><span class="dashicons dashicons-plus-alt"></span></button>
				<br>
				<select id="attributes_check" multiple name="settings[product_attributes][]" style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings[ 'product_attributes' ] )
						foreach ( $settings[ 'product_attributes' ] as $prod ) {
							?>
							<option selected value="<?php echo $prod; ?>"> <?php echo $prod; ?></option>
						<?php } ?>
				</select>
                
                <span class="wc-oe-header"><?php _e( 'Item Meta Data', 'woocommerce-order-export' ) ?></span>
				<br>
				<select id="itemmeta" style="width: auto;">
					<?php foreach ( WC_Order_Export_Data_Extractor::get_product_itemmeta() as $attr_name ) { ?>
						<option><?php echo $attr_name; ?></option>
					<?php } ?>
				</select>

				<select id="itemmeta_compare" class="select_compare">
					<option>=</option>
					<option>&lt;&gt;</option>
					<option>LIKE</option>
				</select>

				<input type="text" id="text_itemmeta" disabled style="display: none;">

				<button id="add_itemmeta" class="button-secondary"><span class="dashicons dashicons-plus-alt"></span></button>
				<br>
				<select id="itemmeta_check" multiple name="settings[product_itemmeta][]" style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings[ 'product_itemmeta' ] )
						foreach ( $settings[ 'product_itemmeta' ] as $prod ) {
							?>
							<option selected value="<?php echo $prod; ?>"> <?php echo $prod; ?></option>
						<?php } ?>
				</select>

			</div>
		</div>

		<br>
		<br>

		<div class="my-block">
			<span class="my-hide-next "><?php _e( 'Filter by customers', 'woocommerce-order-export' ) ?>
				<span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span></span>
			<div id="my-users" hidden="hidden">
				<span class="wc-oe-header"><?php _e( 'User roles', 'woocommerce-order-export' ) ?></span>
				<select id="user_roles" name="settings[user_roles][]" multiple="multiple" style="width: 100%; max-width: 25%;">
					<?php
					global $wp_roles;
					foreach ( $wp_roles->role_names as $k => $v ) { ?>
						<option value="<?php echo $k ?>" <?php echo ( in_array($k, $settings[ 'user_roles' ] ) ? selected(true) : '') ?>> <?php echo $v ?></option>
					<?php } ?>
				</select>

				<span class="wc-oe-header"><?php _e( 'User names', 'woocommerce-order-export' ) ?></span>
				<select id="user_names" name="settings[user_names][]" multiple="multiple" style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings[ 'user_names' ] )
						foreach ( $settings[ 'user_names' ] as $user_id ) {
							$user = get_user_by( 'id', $user_id );
							?>
							<option selected value="<?php echo $user_id ?>"> <?php echo $user->display_name; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>

		<br>
		<br>

		<div class="my-block">
			<span class="my-hide-next "><?php _e( 'Filter by coupons', 'woocommerce-order-export' ) ?>
				<span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span></span>
			<div id="my-coupons" hidden="hidden">
				<span class="wc-oe-header"><?php _e( 'Coupons', 'woocommerce-order-export' ) ?></span>
				<select id="coupons" name="settings[coupons][]" multiple="multiple" style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings['coupons'] )
						foreach ( $settings['coupons'] as $coupon ) {
							?>
							<option selected value="<?php echo $coupon; ?>"> <?php echo $coupon; ?></option>
						<?php } ?>
				</select>
			</div>
		</div>

		<br>
		<br>

		<div class="my-block">
			<span class="my-hide-next "><?php _e( 'Filter by payment', 'woocommerce-order-export' ) ?>
				<span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span></span>
			<div id="my-payments" hidden="hidden">
				<span class="wc-oe-header"><?php _e( 'Payment Methods', 'woocommerce-order-export' ) ?></span>
				<select id="payment_methods" name="settings[payment_methods][]" multiple="multiple" style="width: 100%; max-width: 25%;">
					<?php foreach ( WC()->payment_gateways->payment_gateways() as $gateway ) { ?>
						<option value="<?php echo $gateway->id ?>" <?php if ( in_array( $gateway->id, $settings[ 'payment_methods' ] ) ) echo 'selected'; ?>><?php echo $gateway->get_title() ?></option>
					<?php } ?>
				</select>
			</div>
		</div>

		<br>
		<br>

		<div class="my-block">
			<span class="my-hide-next "><?php _e( 'Filter by shipping', 'woocommerce-order-export' ) ?>
				<span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span></span>
			<div id="my-shipping" hidden="hidden">
				<span class="wc-oe-header"><?php _e( 'Shipping locations', 'woocommerce-order-export' ) ?></span>
				<br>
				<select id="shipping_locations">
					<option>City</option>
					<option>State</option>
					<option>Postcode</option>
					<option>Country</option>
				</select>
				<select id="shipping_compare" class="select_compare">
					<option>=</option>
					<option>&lt;&gt;</option>
				</select>

				<button id="add_locations" class="button-secondary"><span class="dashicons dashicons-plus-alt"></span></button>
				<br>
				<select id="locations_check" multiple name="settings[shipping_locations][]" style="width: 100%; max-width: 25%;">
					<?php
					if ( $settings[ 'shipping_locations' ] )
						foreach ( $settings[ 'shipping_locations' ] as $location ) {
							?>
							<option selected value="<?php echo $location; ?>"> <?php echo $location; ?></option>
						<?php } ?>
				</select>

				<span class="wc-oe-header"><?php _e( 'Shipping methods', 'woocommerce-order-export' ) ?></span>
				<select id="shipping_methods" name="settings[shipping_methods][]" multiple="multiple" style="width: 100%; max-width: 25%;">
					<?php foreach ( WC_Order_Export_Data_Extractor::get_shipping_methods() as $i => $title ) { ?>
						<option value="<?php echo $i ?>" <?php if ( in_array( $i, $settings[ 'shipping_methods' ] ) ) echo 'selected'; ?>><?php echo $title ?></option>
					<?php } ?>
				</select>
			</div>
		</div>

	</div>

	<div class="clearfix"></div>
	<br><br>
	<div class="my-block">
		<span id='adjust-fields-btn' class="my-hide-next "><?php _e( 'Set up fields to export ', 'woocommerce-order-export' ) ?>
			<span class="ui-icon ui-icon-triangle-1-s my-icon-triangle"></span></span>
		<div id="manage_fields" style="display: none;">
			<br>
			<div id='fields_control' style='display:none'>
				<div class='div_meta' style='display:none'>
					<label style="width: 40%;"><?php _e( 'Meta key', 'woocommerce-order-export' ) ?>:
					<select id='select_custom_meta_order'>
							<?php
							foreach ( $order_custom_meta_fields as $meta_id => $meta_name ) {
								echo "<option value='$meta_name' >$meta_name</option>";
							};
							?>
						</select></label>
					<label style="width: 40%;"><?php _e( 'Column Name', 'woocommerce-order-export' ) ?>:<input type='text' id='colname_custom_meta'/></label>

					<div id="custom_meta_order_mode">
						<label style="width: 40%;"><input style="width: 80%;" type='text' id='text_custom_meta_order' placeholder="or type meta key here"/><br></label>
						<label><input id="custom_meta_order_mode_used" type="checkbox" name="custom_meta_order_mode" value="used"> <?php _e('Hide unused', 'woocommerce-order-export') ?></label>
					</div>
					<div style="text-align: right;">
						<button  id='button_custom_meta' class='button-secondary'>Confirm</button>
						<button  class='button-secondary button_cancel'>Cancel</button>
					</div>
				</div>
				<div class='div_custom' style='display:none;'>
					<label style="width: 40%;"><?php _e( 'Column Name', 'woocommerce-order-export' ) ?>:<input type='text' id='colname_custom_field'/></label>
					<label style="width: 40%;"><?php _e( 'Value', 'woocommerce-order-export' ) ?>:<input type='text' id='value_custom_field'/></label>
					<div style="text-align: right;">
						<button  id='button_custom_field' class='button-secondary'><?php _e( 'Confirm', 'woocommerce-order-export' ) ?></button>
						<button   class='button-secondary button_cancel'><?php _e( 'Cancel', 'woocommerce-order-export' ) ?></button>
					</div>
				</div>
				<div class='div1'><span><strong><?php _e( 'Use sections', 'woocommerce-order-export' ) ?>:</strong></span> <?php
					foreach ( WC_Order_Export_Data_Extractor::get_order_segments() as $section_id => $section_name ) {
						echo "<label ><input type=checkbox value=$section_id checked class='field_section'>$section_name &nbsp;</label>";
					}
					?>
				</div>
				<div class='div2'>
					<span><strong><?php _e( 'Actions', 'woocommerce-order-export' ) ?>:</strong></span>
					<button  id='orders_add_custom_meta' class='button-secondary'><?php _e( 'Add Field', 'woocommerce-order-export' ) ?></button>
					<br><br>
					<button  id='orders_add_custom_field' class='button-secondary'><?php _e( 'Add Static Field', 'woocommerce-order-export' ) ?></button>
                    <br></br>
                    <button  id='hide_unchecked' class='button-secondary'>
                        <span  style="padding:0px;"><?php _e( 'Hide Unused', 'woocommerce-order-export' ) ?></span>
                        <span style="padding:0px;display:none"><?php _e( 'Show Unused', 'woocommerce-order-export' ) ?></span>
                    </button>
				</div>
			</div>
			<div id='fields' style='display:none;'>
				<br>
				<div class="mapping_col_2">
					<label style="margin-left: 3px;">
						<input type="checkbox" name="orders_all" value="1"> <?php _e( 'Select All', 'woocommerce-order-export' ) ?></label>
				</div>
				<label class="mapping_col_3" style="color: red; font-size: medium;">
					<?php _e( 'Drag rows to reorder exported fields', 'woocommerce-order-export' ) ?>
				</label>
				<br>
				<ul id="order_fields"></ul>

			</div>
			<div id="modal_content" style="display: none;"></div>
		</div>

	</div>
     <?php do_action("woe_settings_above_buttons", $settings); ?>
	<div id=JS_error_onload style='color:red;font-size: 120%;'><?php echo sprintf(__( "If you see this message, user interface won't work correctly!<br>There is a JS error (<a target=blank href='%s'>read here</a> how to view it). Probably, it's a conflict with another plugin or active theme.", 'woocommerce-order-export' ) , "https://codex.wordpress.org/Using_Your_Browser_to_Diagnose_JavaScript_Errors#Step_3:_Diagnosis"); ?></div>
	<p class="submit">
		<input type="submit" id='preview-btn' class="button-secondary preview-btn"  data-limit="<?php echo ($mode === $WC_Order_Export::EXPORT_ORDER_ACTION ? 1 : 5); ?>" value="<?php _e( 'Preview', 'woocommerce-order-export' ) ?>" />
		<input type="submit" id='save-btn' class="button-primary" value="<?php _e( 'Save Settings', 'woocommerce-order-export' ) ?>" />
		<?php if ( $show[ 'export_button' ] ) { ?>
			<input type="submit" id='export-btn' class="button-secondary" value="<?php _e( 'Export', 'woocommerce-order-export' ) ?>" />
			<input type="submit" id='export-wo-pb-btn' class="button-secondary" value="<?php _e( 'Export [w/o progressbar]', 'woocommerce-order-export' ) ?>" />
		<?php } ?>
		<span id="preview_actions" class="hide">
			<strong id="output_preview_total"><?php _e( 'Export total: ', 'woocommerce-order-export' ); ?><span></span><?php _e( ' orders', 'woocommerce-order-export' ) ?></strong>
			<?php _e( 'Export view', 'woocommerce-order-export' ); ?>
			<?php foreach( array( 5, 10, 25, 50 ) as $n ): ?>
				<button class="button-secondary preview-btn" data-limit="<?php echo $n; ?>"><?php echo $n; ?></button>
			<?php endforeach ?>
		</span>
	</p>
	<?php if ( $show[ 'export_button' ] ) { ?>
		<div id="progress_div" style="display: none;">
			<h1><?php _e( "Press 'Esc' to cancel the export", 'woocommerce-order-export' ) ?></h1>
			<div id="progressBar"><div></div></div>
		</div>
		<div id="background"></div>
	<?php } ?>

</form>
<textarea rows=10 id='output_preview' style="overflow: auto;" wrap='off'></textarea> 
<div id='output_preview_csv' style="overflow: auto;width:100%"></div>

<form id='export_new_window_form' method=POST target=_blank></form>
<iframe id='export_new_window_frame' width=0 height=0 style='display:none'></iframe>

<form id='export_wo_pb_form' method='post' target='export_wo_pb_frame'>
	<input name="action" type="hidden" value="order_exporter">
	<input name="method" type="hidden" value="plain_export">
	<input name="mode" type="hidden" value="<?php echo $mode ?>">
	<input name="id" type="hidden" value="<?php echo $id ?>">
	<input name="settings" type="hidden">
</form>
<iframe id='export_wo_pb_frame' width=0 height=0 style='display:none'></iframe>


<script>
	jQuery( document ).ready( function( $ ) {

		$( '#schedule-1,#schedule-2' ).change( function() {
			if ( $( '#schedule-1' ).is( ':checked' ) && $( '#schedule-1' ).val() == 'schedule-1' ) {
				$( '#d-schedule-2 input:not(input[type=radio])' ).attr( 'disabled', true )
				$( '#d-schedule-2 select' ).attr( 'disabled', true )
				$( '#d-schedule-1 input:not(input[type=radio])' ).attr( 'disabled', false )
				$( '#d-schedule-1 select' ).attr( 'disabled', false )
			} else {
				$( '#d-schedule-1 input:not(input[type=radio])' ).attr( 'disabled', true )
				$( '#d-schedule-1 select' ).attr( 'disabled', true )
				$( '#d-schedule-2 select' ).attr( 'disabled', false )
				$( '#d-schedule-2 input:not(input[type=radio]) ' ).attr( 'disabled', false )
			}
		} );
		$( '#schedule-1' ).change()
		$( '.wc_oe-select-interval' ).change( function() {
			var interval = $( this ).val()
			if ( interval == 'custom' ) {
				$( '#custom_interval' ).show()
			} else {
				$( '#custom_interval' ).hide()
			}
		} );
		$( '.wc_oe-select-interval' ).change()

		$( '.output_destination' ).click( function() {
			var target = $( this ).val();
			$( '.set-destination:not(#' + target + ')' ).hide();
			$( '.my-icon-triangle' ).removeClass( 'ui-icon-triangle-1-n' );
			$( '.my-icon-triangle' ).addClass( 'ui-icon-triangle-1-s' );
			if ( !jQuery( '#' + target ).is( ':hidden' ) ) {
				jQuery( '#' + target ).hide();
			}
			else {
				if ( jQuery( '#' + target ).is( ':hidden' ) ) {
					jQuery( '#' + target ).show();
					$( '#test_reply_div' ).hide();
					$( this ).next().removeClass( 'ui-icon-triangle-1-s' );
					$( this ).next().addClass( 'ui-icon-triangle-1-n' );
				}
			}
		} );
        var is_unchecked_shown = true;
        $('#hide_unchecked').on('click', function(e) {
            e.preventDefault();
            is_unchecked_shown = !is_unchecked_shown;
            $("#order_fields li input:checkbox:not(:checked)").closest('.mapping_row').toggle(is_unchecked_shown);
            $('#hide_unchecked span').toggle();
        });
        
		function my_hide( item ) {
			if ( $( item ).is( ':hidden' ) ) {
				$( item ).show();
				return false;
			}
			else {
				$( item ).hide();
				return true;
			}
		}

		$( '.my-hide-parent' ).click( function() {
			my_hide( $( this ).parent() );
		} );

		$( '.my-hide-next' ).click( function() {
			var f = my_hide( $( this ).next() );
			if ( f ) {
				$( this ).find( 'span' ).removeClass( 'ui-icon-triangle-1-n' );
				$( this ).find( 'span' ).addClass( 'ui-icon-triangle-1-s' );
			}
			else {
				$( this ).find( 'span' ).removeClass( 'ui-icon-triangle-1-s' );
				$( this ).find( 'span' ).addClass( 'ui-icon-triangle-1-n' );
			}
			return false;
		} );


		$( '.wc_oe_test' ).click( function() {
			var test = $( this ).attr( 'data-test' );
			var data = $( '#export_job_settings' ).serialize()
			data = data + "&action=order_exporter&method=test_destination&mode=" + mode + "&id=" + job_id + "&format=" + test;
			$( '#test_reply_div' ).hide();
			$.post( ajaxurl, data, function( data ) {
				$( '#test_reply' ).val( data );
				$( '#test_reply_div' ).show();
			} )
		} )
	} )

	function remove_custom_field( item ) {
		jQuery( item ).parent().parent().remove();
		return false;
	}

	function create_fields( format , format_changed) {
		jQuery( '#export_job_settings' ).prepend( jQuery( "#fields_control_products" ) );
		jQuery( '#export_job_settings' ).prepend( jQuery( "#fields_control_coupons" ) );
		jQuery( "#order_fields" ).html();
		jQuery( "#modal_content" ).html( "" );

		var html = '';
		jQuery.each( window['order_fields'], function( index, value ) {
			var checked = ( value.checked == 1 ) ? 'checked' : '';
			var colname = value.colname;
                        
                        colname     = escapeStr(colname);    
                        value.label = escapeStr(value.label);    
                        index       = escapeStr(index);  
                        value.value = escapeStr(value.value); 
			
//                         console.log(index);
//                         console.log(value);
                        
                        if(format_changed) {
				if( format == 'XLS' || format == 'CSV' )
					colname = value.label;
				else if ( format == 'XML' )
					colname = to_xml_tags( index );
				else
					colname = index;;
			}
                        
			
			if ( index == 'products' || index == 'coupons' ) {
				var sel_rows = ( value.repeat == 'rows' ) ? 'checked' : '';
				var sel_cols = ( value.repeat == 'columns' ) ? 'checked' : '';
				var max_cols = ( typeof(value.max_cols) !== 'undefined' ) ? value.max_cols : "10";
				var modal = '<div id="modal-manage-' + index + '" style="display:none;"><p>';
				modal += create_modal_fields( format, index, format_changed);
				modal += '</p></div>';
				jQuery( "#modal_content" ).append( modal );
				var row = '<li class="mapping_row segment_' + value.segment + '">\
                                                        <div class="mapping_col_1">\
                                                                <input type=hidden name="orders[segment][' + index + ']"  value="' + value.segment + '">\
                                                                <input type=hidden name="orders[label][' + index + ']"  value="' + value.label + '">\
                                                                <input type=hidden name="orders[exported][' + index + ']"  value="0">\
                                                                <input type=checkbox name="orders[exported][' + index + ']"  ' + checked + ' value="1">\
                                                        </div>\
                                                        <div class="mapping_col_2">' + value.label + '</div>\
                                                        <div class="mapping_col_3">';
				if ( format == 'XLS' || format == 'CSV' )
					
					row += 'Add <input type=radio name="orders[repeat][' + index + ']" value="columns" ' + sel_cols + ' >as '+
						'<input type=text size=2 name="orders[max_cols][' + index + ']" value="'+max_cols+'"> columns' +
						'<input type=radio name="orders[repeat][' + index + ']" value="rows" ' + sel_rows + ' >as rows'
				row += '<input class="mapping_fieldname" type=input name="orders[colname][' + index + ']" value="' + colname + '">\
                                                        <input type="button" class="button-primary" id="btn_modal_manage_' + index + '" value="Set up fields to export" /><a href="#TB_inline?width=600&height=550&inlineId=modal-manage-' + index + '" class="thickbox " id="link_modal_manage_' + index + '"> </a></div>\
                                                </li>\
                        ';
			}
			else {
				var value_part = ''
				var label_part = '';
				if ( index.indexOf( 'custom_field' ) >= 0 ) {
					value_part = '<div class="mapping_col_3"><input class="mapping_fieldname" type=input name="orders[value][' + index + ']" value="' + value.value + '"></div>';
					label_part = '<a href="#" onclick="return remove_custom_field(this);" style="float: right;"><span class="ui-icon ui-icon-trash"></span></a>';
				}
				else if ( index.charAt( 0 ) == '_'  || !value.default) {
					label_part = '<a href="#" onclick="return remove_custom_field(this);" style="float: right;"><span class="ui-icon ui-icon-trash"></span></a>';
				}

                             var row = '<li class="mapping_row segment_' + value.segment + '">\
                                                        <div class="mapping_col_1">\
                                                                <input type=hidden name="orders[segment][' + index + ']"  value="' + value.segment + '">\
                                                                <input type=hidden name="orders[label][' + index + ']"  value="' + value.label + '">\
                                                                <input type=hidden name="orders[exported][' + index + ']"  value="0">\
                                                                <input type=checkbox name="orders[exported][' + index + ']"  ' + checked + ' value="1">\
                                                        </div>\
                                                        <div class="mapping_col_2">' + value.label + label_part + '</div>\
                                                        <div class="mapping_col_3"><input class="mapping_fieldname" type=input name="orders[colname][' + index + ']" value="' + colname + '"></div> ' + value_part + '\
                                                </li>\
                        ';
			}
			html += row;
		} );

		jQuery( "#order_fields" ).html( html );
		jQuery( '#modal-manage-products' ).prepend( jQuery( "#fields_control_products" ) );
		jQuery( '#modal-manage-coupons' ).prepend( jQuery( "#fields_control_coupons" ) );
		jQuery( "#fields_control_products" ).css( 'display', 'inline-block' );
		jQuery( "#fields_control_coupons" ).css( 'display', 'inline-block' );
		add_bind_for_custom_fields( 'products', output_format, jQuery( "#sort_products" ) );
		add_bind_for_custom_fields( 'coupons', output_format, jQuery( "#sort_coupons" ) );

	}



	function create_modal_fields( format, index_p, format_changed ) {
		//console.log( 'order_' + index_p + '_fields', window['order_' + index_p + '_fields'] );

		var modal = "<div id='sort_" + index_p + "'>";
		jQuery.each( window['order_' + index_p + '_fields'], function( index, value ) {
			var checked = ( value.checked == 1 ) ? 'checked' : '';
			var colname = value.colname;
                        
//                         console.log(index);
//                         console.log(value);
                        
                        
                        colname     = escapeStr(colname);    
                        value.label = escapeStr(value.label);    
                        index       = escapeStr(index); 
                        value.value = escapeStr(value.value); 
                        
			if(format_changed) {
				if( format == 'XLS' || format == 'CSV' )
					colname = value.label;
				else if ( format == 'XML' )
					colname = to_xml_tags( index );
				else
					colname = index;;
			}

			var value_part = ''
			var label_part = '';
			if ( index.indexOf( 'custom_field' ) >= 0 ) {
				value_part = '<div class="mapping_col_3"><input class="mapping_fieldname" type=input name="' + index_p + '[value][' + index + ']" value="' + value.value + '"></div>';
				label_part = '<a href="#" onclick="return remove_custom_field(this);" style="float: right;"><span class="ui-icon ui-icon-trash"></span></a>';
			}
			else if ( index.charAt( 0 ) == '_'  || index.substr( 0,3 ) == 'pa_' || !value.default) {
				label_part = '<a href="#" onclick="return remove_custom_field(this);" style="float: right;"><span class="ui-icon ui-icon-trash"></span></a>';
			}

			var row = '<li class="mapping_row segment_modal_' + index + '">\
                                                        <div class="mapping_col_1">\
                                                                <input type=hidden name="' + index_p + '[label][' + index + ']"  value="' + value.label + '">\
                                                                <input type=hidden name="' + index_p + '[exported][' + index + ']"  value="0">\
                                                                <input type=checkbox name="' + index_p + '[exported][' + index + ']"  ' + checked + ' value="1">\
                                                        </div>\
                                                        <div class="mapping_col_2">' + value.label + label_part + '</div>\
                                                        <div class="mapping_col_3"><input class="mapping_fieldname" type=input name="' + index_p + '[colname][' + index + ']" value="' + colname + '"></div>' + value_part + '\
                                                </li>\
                        ';
			modal += row;
		} );
		modal += "</div>";
		return modal;
	}

	//for XML labels
	function to_xml_tags( str ) {
		var arr = str.split( /_/ );
		for ( var i = 0, l = arr.length; i < l; i++ ) {
			arr[i] = arr[i].substr( 0, 1 ).toUpperCase() + ( arr[i].length > 1 ? arr[i].substr( 1 ).toLowerCase() : "" );
		}
		return arr.join( "_" );
	}


	function change_filename_ext() {
		if ( jQuery( '#export_filename' ).size() ) {
			var filename = jQuery( '#export_filename input' ).val();
			var ext = output_format.toLowerCase();
			if( ext=='xls'  && !jQuery( '#format_xls_use_xls_format' ).prop('checked') ) //fix for XLSX
				ext = 'xlsx';
			var file = filename.replace( /^(.*)\..+$/, "$1." + ext );
			jQuery( '#export_filename input' ).val( file );
		}
	}
        
        function modal_buttons() 
        {
            jQuery('body').on('click', '#btn_modal_manage_products', function() {
                
                jQuery('#custom_meta_products_mode_all').attr('checked', 'checked');
                jQuery('#custom_meta_products_mode_all').change();
                jQuery('#link_modal_manage_products').click();
                
                return false;
            });

            jQuery('body').on('click', '#btn_modal_manage_coupons', function() {
                
                jQuery('#custom_meta_coupons_mode_all').attr('checked', 'checked');
                jQuery('#custom_meta_coupons_mode_all').change();
                jQuery('#link_modal_manage_coupons').click();
                
                return false;
            });
            
        }

	jQuery( document ).ready( function( $ ) {

		try {
			select2_inits();
		}
		catch ( err ) {
			console.log( err.message );
			jQuery( '#select2_warning' ).show();
		}

		bind_events();
		jQuery( '#attributes' ).change();
		jQuery( '#itemmeta' ).change();
		jQuery( '#custom_fields' ).change();
		jQuery( '#product_custom_fields' ).change();
		jQuery( '#shipping_locations' ).change();
//		jQuery( '#' + output_format + '_options' ).show();

		//jQuery('#fields').toggle(); //debug 
		create_fields( output_format, false );
		$( '#test_reply_div' ).hide();
//		jQuery( '#' + output_format + '_options' ).hide();

		jQuery( "#sort_products" ).sortable()/*.disableSelection()*/;
		jQuery( "#sort_coupons" ).sortable()/*.disableSelection()*/;
		jQuery( "#order_fields" ).sortable({ scroll: true, scrollSensitivity: 100, scrollSpeed: 100 });/*.disableSelection()*/;


		/*jQuery('#btn_modal_manage_products').click(function(){
			jQuery('#custom_meta_products_mode_all').attr('checked', 'checked');
			jQuery('#custom_meta_products_mode_all').change();
			jQuery('#link_modal_manage_products').click();
			return false;
		});

		jQuery('#btn_modal_manage_coupons').click(function(){
			jQuery('#custom_meta_coupons_mode_all').attr('checked', 'checked');
			jQuery('#custom_meta_coupons_mode_all').change();
			jQuery('#link_modal_manage_coupons').click();
			return false;
		});*/
        
                modal_buttons();

		jQuery( '.date' ).datepicker( {
			dateFormat: 'yy-mm-dd'
		} );

		jQuery( '#adjust-fields-btn' ).click( function() {
			jQuery( '#fields' ).toggle();
			jQuery( '#fields_control' ).toggle();
			return false;
		} );

		jQuery( '.field_section' ).click( function() {
			var section = jQuery( this ).val();
			var checked = jQuery( this ).is( ':checked' );

			jQuery( '.segment_' + section ).each( function( index ) {
				if ( checked ) {
					jQuery( this ).show();
					//jQuery(this).find('input:checkbox:first').attr('checked', true);
				}
				else {
					jQuery( this ).hide();
					jQuery( this ).find( 'input:checkbox:first' ).attr( 'checked', false );
				}
			} );
		} );

		jQuery( '.output_format' ).click( function() {
			var new_format = jQuery( this ).val();
			jQuery( '#my-format .my-icon-triangle' ).removeClass( 'ui-icon-triangle-1-n' );
			jQuery( '#my-format .my-icon-triangle' ).addClass( 'ui-icon-triangle-1-s' );

			if ( new_format != output_format ) {
				jQuery( this ).next().removeClass( 'ui-icon-triangle-1-s' );
				jQuery( this ).next().addClass( 'ui-icon-triangle-1-n' );
				jQuery( '#' + output_format + '_options' ).hide();
				jQuery( '#' + new_format + '_options' ).show();
				output_format = new_format;
				create_fields( output_format, true )
				jQuery( '#output_preview, #output_preview_csv' ).hide();
//				jQuery( '#fields' ).hide();
//				jQuery( '#fields_control' ).hide();
				change_filename_ext();
			}
			else {
				if ( !jQuery( '#' + new_format + '_options' ).is( ':hidden' ) ) {
					jQuery( '#' + new_format + '_options' ).hide();
				}
				else {
					if ( jQuery( '#' + new_format + '_options' ).is( ':hidden' ) ) {
						jQuery( '#' + new_format + '_options' ).show();
						jQuery( this ).next().removeClass( 'ui-icon-triangle-1-s' );
						jQuery( this ).next().addClass( 'ui-icon-triangle-1-n' );
					}
				}
			}

		} );

		$( '#order_fields input[type=checkbox]' ).change( function() {
			if ( $( '#order_fields input[type=checkbox]:not(:checked)' ).size() ) {
				$( 'input[name=orders_all]' ).attr( 'checked', false );
			}
			else {
				$( 'input[name=orders_all]' ).attr( 'checked', true );
			}
		} );

		$( 'input[name=orders_all]' ).change( function() {
			if ( $( 'input[name=orders_all]' ).is( ':checked' ) ) {
				$( '#order_fields input[type=checkbox]' ).attr( 'checked', true );
			}
			else {
				$( '#order_fields input[type=checkbox]' ).attr( 'checked', false );
			}
		} );

		if ( $( '#order_fields input[type=checkbox]' ).size() ) {
			$( '#order_fields input[type=checkbox]:first' ).change();
		}




		$( ".preview-btn" ).click( function() {
			preview(jQuery(this).attr('data-limit'));
			return false;
		} );

		function preview(size) {
			jQuery( '#output_preview, #output_preview_csv' ).hide();
			var data = $( '#export_job_settings' ).serialize();
			var estimate_data = data + "&action=order_exporter&method=estimate&mode=" + mode + "&id=" + job_id;
			$.post( ajaxurl, estimate_data, function( response ) {
						if ( response.total !== undefined ) {
							jQuery( '#output_preview_total' ).find( 'span' ).html( response.total );
							jQuery( '#preview_actions' ).removeClass( 'hide' );
						}
					}, "json"
			);

			data = data + "&action=order_exporter&method=preview&limit="+size+"&mode=" + mode + "&id=" + job_id;
			$.post( ajaxurl, data, function( response ) {
						var id = 'output_preview';
						if ( output_format == 'XLS' || output_format == 'CSV' )
							id = 'output_preview_csv';
						if ( output_format == 'JSON' || output_format == 'XML' ) {
							jQuery( '#' + id ).text( response );
						}
						else {
							jQuery( '#' + id ).html( response );
						}
						jQuery( '#' + id ).show();
						window.scrollTo( 0, document.body.scrollHeight );
					}
					, "html"
			);
		}
// EXPORT FUNCTIONS
		function get_data() {
			var data = $( '#export_job_settings' ).serializeArray()
			data.push( { name: 'action', value: 'order_exporter' } );
			data.push( { name: 'mode', value: mode } );
			data.push( { name: 'id', value: job_id } );
			return data;
		}

		function progress( percent, $element ) {

			if ( percent == 0 ) {
				$element.find( 'div' ).html( percent + "%&nbsp;" ).animate( { width: 0 }, 0 );
				waitingDialog();
				jQuery( '#progress_div' ).show();
			}
			else {
				var progressBarWidth = percent * $element.width() / 100;
				$element.find( 'div' ).html( percent + "%&nbsp;" ).animate( { width: progressBarWidth }, 200 );

				if ( percent >= 100 ) {
					jQuery( '#progress_div' ).hide();
					closeWaitingDialog();
				}
			}
		}

		function get_all( start, percent, method ) {
			if (window.cancelling) {
				return;
			}

			progress( parseInt( percent, 10 ), jQuery( '#progressBar' ) );

			if ( percent < 100 ) {
				data = get_data();
				data.push( { name: 'method', value: method } );
				data.push( { name: 'start', value: start } );
				data.push( { name: 'file_id', value: window.file_id } );

				jQuery.ajax( {
					type: "post",
					data: data,
					cache: false,
					url: ajaxurl,
					dataType: "json",
					error: function( xhr, status, error ) {
						alert( xhr.responseText );
						progress( 100, jQuery( '#progressBar' ) );
					},
					success: function( response ) {
						get_all( response.start, ( response.start / window.count ) * 100, method )
					}
				} );
			}
			else {
				data = get_data();
				data.push( { name: 'method', value: 'export_finish' } );
				data.push( { name: 'file_id', value: window.file_id } );
				jQuery.ajax( {
					type: "post",
					data: data,
					cache: false,
					url: ajaxurl,
					dataType: "json",
					error: function( xhr, status, error ) {
						alert( xhr.responseText );
					},
					success: function( response ) {
						var download_format = output_format;
						if( output_format=='XLS' && !jQuery( '#format_xls_use_xls_format' ).prop('checked') ) 
							download_format =  'XLSX';
						$( '#export_new_window_frame' ).attr( "src", ajaxurl + (ajaxurl.indexOf('?') === -1? '?':'&')+'action=order_exporter&method=export_download&format=' + download_format + '&file_id=' + window.file_id );
					}
				} );

//			$('#export_new_window_form').find("input").replaceWith( "" );
//			$('#export_new_window_form').attr("action",  ajaxurl);
//			$.each(data, function( index, obj ) {
//				if(obj.value != '') {
//					$('<input>').attr({
//						type: 'hidden',
//						name: obj.name,
//						value: obj.value
//					}).appendTo('#export_new_window_form');
//				}
//			});
//			$('#export_new_window_form').submit();
			}
		}

		function waitingDialog() {
			jQuery( "#background" ).addClass( "loading" );
			jQuery( '#wpbody-content' ).keydown(function(event) {
				if ( event.keyCode == 27 ) {
					if (!window.cancelling) {
						event.preventDefault();
						window.cancelling = true;

						jQuery.ajax( {
							type: "post",
							data: {
								action: 'order_exporter',
								method: 'cancel_export',
								file_id: window.file_id,
							},
							cache: false,
							url: ajaxurl,
							dataType: "json",
							error: function( xhr, status, error ) {
								alert( xhr.responseText );
								progress( 100, jQuery( '#progressBar' ) );
							},
							success: function( response ) {
								progress( 100, jQuery( '#progressBar' ) );
							}
						} );

						window.count = 0;
						window.file_id = '';
						jQuery( '#wpbody-content' ).off('keydown');
					}
					return false;
				}
			});
		}
		function closeWaitingDialog() {
			jQuery( "#background" ).removeClass( "loading" );
		}
		
		function openFilter(object_id) {
			var f = false;
			$( '#'+object_id+' ul' ).each( function( index ) {
				if ( $( this ).find( 'li:not(:first)' ).size() ) {
					f = true;
				}
			} );
			if ( f ) {
				$( '#'+object_id ).prev().click();
			}
		}
// EXPORT FUNCTIONS END
		$( "#export-wo-pb-btn" ).click( function() {
			$( '#export_wo_pb_frame' ).attr( "src", ajaxurl );
			$( '#export_wo_pb_form' ).attr( "action", ajaxurl );
			var $obj = $( '[name=settings\\[format_csv_linebreak\\]]');
			var val  = $obj.val();
			$obj.val(val.replace(/\\/g, '\\\\'));
			$( '#export_wo_pb_form' ).find( '[name=settings]' ).val( $( '#export_job_settings' ).serialize() );
			$obj.val(val);
			$( '#export_wo_pb_form' ).submit();

			return false;
		} );

		$( "#export-btn, #my-quick-export-btn" ).click( function() {
			window.cancelling = false;

			data = get_data();

			data.push( { name: 'method', value: 'export_start' } );
			if ( ( $( "#from_date" ).val() ) && ( $( "#to_date" ).val() ) ) {
				var d1 = new Date( $( "#from_date" ).val() );
				var d2 = new Date( $( "#to_date" ).val() );
				if ( d1.getTime() > d2.getTime() ) {
					alert( "Date From is greater than Date To" );
					return false;
				}
			}

			if ( $( '#order_fields input[type=checkbox]:checked' ).size() == 0 )
			{
				alert( "Please, set up fields to export" );
				return false;
			}

			jQuery.ajax( {
				type: "post",
				data: data,
				cache: false,
				url: ajaxurl,
				dataType: "json",
				error: function( xhr, status, error ) {
					alert( xhr.responseText );
				},
				success: function( response ) {
					window.count = response['total'];
					window.file_id = response['file_id'];
					console.log( window.count );
					if ( window.count > 0 )
						get_all( 0, 0, 'export_part' );
					else
						alert( "Nothing to export. Please, adjust your filters" );
				}
			} );

			return false;
		} );
		$( "#save-btn" ).click( function() {
			if ( ( mode == '<?php echo $WC_Order_Export::EXPORT_PROFILE; ?>' ) && ( !$( "[name='settings[title]']" ).val() ) ) {
				alert( "Title is empty!" );
				$( "[name='settings[title]']" ).focus();
				return false;
			}

			if ( ( $( "#from_date" ).val() ) && ( $( "#to_date" ).val() ) ) {
				var d1 = new Date( $( "#from_date" ).val() );
				var d2 = new Date( $( "#to_date" ).val() );
				if ( d1.getTime() > d2.getTime() ) {
					alert( "Date From is greater than Date To" );
					return false;
				}
			}
			if ( $( '#order_fields input[type=checkbox]:checked' ).size() == 0 )
			{
				alert( "Please, set up fields to export" );
				return false;
			}

			var data = $( '#export_job_settings' ).serialize()
			data = data + "&action=order_exporter&method=save_settings&mode=" + mode + "&id=" + job_id;
			$.post( ajaxurl, data, function( response ) {
				if ( mode == '<?php echo $WC_Order_Export::EXPORT_SCHEDULE; ?>' ) {
					document.location = '<?php echo admin_url( 'admin.php?page=wc-order-export&tab=schedules&save=y' ) ?>';
				} else if ( mode == '<?php echo $WC_Order_Export::EXPORT_PROFILE; ?>' ) {
					document.location = '<?php echo admin_url( 'admin.php?page=wc-order-export&tab=profiles&save=y' ) ?>';
				} else if ( mode == '<?php echo $WC_Order_Export::EXPORT_ORDER_ACTION; ?>' ) {
					document.location = '<?php echo admin_url( 'admin.php?page=wc-order-export&tab=order_actions&save=y' ) ?>';
				} else {
					document.location = '<?php echo admin_url( 'admin.php?page=wc-order-export&tab=export&save=y' ) ?>';
				}
			}, "json" );
			return false;
		} );
		
		openFilter('my-order');
		
		openFilter('my-products');
		
		openFilter('my-shipping');
		
		openFilter('my-users');

		openFilter('my-coupons');
		
		openFilter('my-payments');

		//for XLSX
		$('#format_xls_use_xls_format').click(function() {
			change_filename_ext();
		});
		
		// this line must be last , we don't have any errors
		jQuery('#JS_error_onload').hide();
	} );
</script>
