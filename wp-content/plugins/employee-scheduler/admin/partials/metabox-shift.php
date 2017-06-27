<div class="shiftee-metabox" id="shiftinfo">

<table class="form-table">
	<tr>
		<th scope="row"><label><?php _e( 'Date', 'shiftee' ); ?></label></th>
		<td>
			<input id="thisdate"  type="text" class="shiftee-date-picker" size="10" name="<?php $metabox->the_name( 'date' ); ?>" value="<?php $metabox->the_value( 'date' ); ?>"/>
		</td>
	</tr>

	<tr>
		<th scope="row"><label><?php _e( 'Scheduled Time', 'shiftee' ); ?></label></th>
		<td>
			<span><?php _e( 'Use 24-hour time format.', 'shiftee' ); ?></span><br />
			<?php _e( 'From', 'shiftee' ); ?>
			<input id="starttime"  type="text" class="shiftee-time-picker" size="6" name="<?php $metabox->the_name( 'starttime' ); ?>" value="<?php $metabox->the_value( 'starttime' ); ?>"/>
			<?php _e('to', 'wpaesm'); ?>
			<input id="endtime"  type="text" class="shiftee-time-picker" size="6" name="<?php $metabox->the_name('endtime'); ?>" value="<?php $metabox->the_value('endtime'); ?>"/>
			<?php if( $metabox->get_the_value( 'starttime' ) && $metabox->get_the_value( 'starttime' ) !== '____-__-__' && $metabox->get_the_value( 'endtime' ) && $metabox->get_the_value( 'endtime' ) !== '____-__-__' ) {
				$startTime=date_create( date( "H:i",strtotime( $metabox->get_the_value( 'starttime' ) ) ) );
				$endTime=date_create( date( "H:i",strtotime( $metabox->get_the_value( 'endtime') ) ) );
				$timeInterval=date_diff($startTime, $endTime);
				$getHours=$timeInterval->format('%h');
				$getMinutes=$timeInterval->format('%I');
				$duration=$getHours.":".$getMinutes;
				_e( '<p>Scheduled duration: ' . $duration . '</p>', 'shiftee' );
			} ?>
		</td>
	</tr>

	<tr>
		<th scope="row"><label><?php _e( 'Actual Time Worked (reported by employee)', 'shiftee' ); ?></label></th>
		<td>
			<?php _e('From', 'shiftee'); ?>
			<input  id="clockin" type="text" class="shiftee-time-picker" size="6" name="<?php $metabox->the_name('clockin'); ?>" value="<?php $metabox->the_value('clockin'); ?>"/>
			<?php _e('to', 'shiftee'); ?>
			<input  id="clockout" type="text" size="6" class="shiftee-time-picker" name="<?php $metabox->the_name('clockout'); ?>" value="<?php $metabox->the_value('clockout'); ?>"/>
			<?php if( $metabox->get_the_value( 'clockin' ) && $metabox->get_the_value( 'clockin' ) !== '____-__-__' && $metabox->get_the_value( 'clockout' ) && $metabox->get_the_value( 'clockout' ) !== '____-__-__' ) {
				$startTime=date_create(date("H:i",strtotime($metabox->get_the_value('clockin'))));
				$endTime=date_create(date("H:i",strtotime($metabox->get_the_value('clockout'))));
				$timeInterval=date_diff($startTime, $endTime);
				$getHours=$timeInterval->format('%h');
				$getMinutes=$timeInterval->format('%I');
				$duration=$getHours.":".$getMinutes;
				_e('<p>Actual duration: ' . $duration . '</p>', 'shiftee');
			} ?>
		</td>
	</tr>

	<?php $options = get_option('wpaesm_options'); // display geolocation information if option is set
	if(isset($options['geolocation']) && $options['geolocation'] == 1) { ?>
		<tr>
			<th scope="row"><label><?php _e('Locations (approximate)', 'shiftee'); ?></label></th>
			<td>
				<?php _e('Clock-In Location:', 'shiftee'); ?>
				<input disabled id="location_in" type="text" size="40" name="<?php $metabox->the_name('location_in'); ?>" value="<?php $metabox->the_value('location_in'); ?>"/>
				<br /><?php _e('Clock-Out Location:', 'shiftee'); ?>
				<input disabled id="location_out" type="text" size="40" name="<?php $metabox->the_name('location_out'); ?>" value="<?php $metabox->the_value('location_out'); ?>"/>
			</td>
		</tr>

	<?php } ?>

	<tr>
		<th scope="row"><label><?php _e('Notification', 'shiftee'); ?></label></th>
		<td>
			<?php $metabox->the_field('notify'); ?>
			<span><?php _e('Notify Employee that Shift has been Created/Updated?', 'shiftee'); ?></span><br />
			<input type="checkbox" name="<?php $metabox->the_name(); ?>" value="1"<?php if ($metabox->get_the_value()) echo ' checked="checked"'; ?>/><?php _e(' Notify Employee', 'wpaesm'); ?>
		</td>
	</tr>

	<?php do_action( 'shiftee_extra_shift_fields', $metabox ); ?>
</table>


<!-- Internal notes -->
	<label><?php _e('Internal Notes (this will only be seen by site admins)', 'wpaeshifteesm'); ?></label>
	<p>
		<?php $metabox->the_field('shiftnotes'); ?>
		<textarea name="<?php $metabox->the_name(); ?>" rows="3"><?php $metabox->the_value(); ?></textarea>
	</p>


<!-- Employee notes -->
	<label><?php _e('Employee Notes', 'shiftee'); ?></label>
	<p class="explain"><?php _e('These notes are left by employees', 'shiftee'); ?></p>
	<!-- hidden field to keep track of the last time a note was left -->
	<input id="lastnote" type="hidden" name="<?php $metabox->the_name('lastnote'); ?>" value="<?php $metabox->the_value('lastnote'); ?>"/>

	<a href="#" class="dodelete-employeenote button"><?php _e('Remove All Notes', 'shiftee'); ?></a>
 
	<?php while($mb->have_fields_and_multi('employeenote')): ?>
	<?php $mb->the_group_open(); ?>
		<div class="employeenote">

			<?php $mb->the_field('notedate'); ?>
			<label><?php _e('Date', 'shiftee'); ?></label>
			<p><input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/></p>

			<label><?php _e('Note', 'shiftee'); ?></label>
			<p>
				<?php $metabox->the_field('notetext'); ?>
				<textarea name="<?php $metabox->the_name(); ?>" rows="5"><?php $metabox->the_value(); ?></textarea>
			</p>
 		</div>

	<?php $mb->the_group_close(); ?>
	<?php endwhile; ?>
	<p style="margin-bottom:15px; padding-top:5px;"><a href="#" class="docopy-employeenote button"><?php _e('Add Another Note', 'shiftee'); ?></a></p>

</div>

<div style="display:none;" id="confirm-availability"><?php _e( 'Checking employee\'s availability...', 'shiftee' ); ?></div>