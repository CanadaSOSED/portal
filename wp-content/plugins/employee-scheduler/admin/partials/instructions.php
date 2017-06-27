<?php

/**
 * Instructions
 *
 * Instructions page for how to use the plugin.
 *
 * @link       http://ran.ge
 * @since      2.0.0
 *
 * @package    Shiftee Basic
 * @subpackage Shiftee Basic/admin/partials
 */
?>

<div class="wrap instructions">

	<!-- Display Plugin Icon, Header, and Description -->
	<div class="icon32" id="icon-options-general"><br></div>
	<h1><?php printf( __('Instructions for using %s', 'employee-scheduler' ), apply_filters( 'shiftee_name', __( 'Shiftee Basic', 'employee-scheduler' ) ) ); ?></h1>

	<?php
	$admin = new Shiftee_Basic_Admin( 'Shiftee Basic', '2.0' );
	$admin->show_sidebar(); ?>

	<h3><?php _e('Initial Set Up', 'employee-scheduler'); ?></h3>

	<h4><?php _e('Plugin Settings', 'employee-scheduler'); ?></h4>

	<p><?php printf( __('The plugin has a few settings that you might want to adjust.  In the WordPress dashboard menu, click on <a href="%s">%s.</a>', 'employee-scheduler'), admin_url( 'admin.php?page=shiftee' ), apply_filters( 'shiftee_name', __( 'Shiftee Basic', 'employee-scheduler' ) ) ); ?></p>
	<p><?php _e('The first several settings relate to the email notifications sent to staff. You can change the sender name, sender email, and message subject.', 'employee-scheduler'); ?></p>
	<p><?php _e('The "Admin Notifications" settings let you decide whether or not to receive a notification when a staff member leaves a note on a shift, or when a shift changes status.  You can change the email address that receives these notifications.', 'employee-scheduler'); ?></p>
	<p><?php _e('You can select what day of the week your work-week starts on and whether or not you want to record staff members\' location when they clock in and out (note: location is accurate to within a city block).', 'employee-scheduler'); ?></p>

	<h4><?php _e('Set up shift types', 'employee-scheduler'); ?></h4>
	<p><?php _e('Shifts can be organized into shift types if you want.  You might want to categorize shifts based on where they are worked or the type of work involved.', 'employee-scheduler'); ?></p>
	<p><?php _e('To create your shift types, go to <a href="' . admin_url( 'edit-tags.php?taxonomy=shift_type&post_type=shift') . '">Shifts &rightarrow; Shift Types</a>.  Two shift types have already been created for you:', 'employee-scheduler'); ?>
	<ul>
		<li>
			<?php _e('Extra: automatically assigned to shifts that staff create themselves using the Extra Work form, if they do work outside of their scheduled shifts.', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('Paid Time Off: If you want a staff member to get paid time off, you can create a shift in this category, with a duration of the number of hours you want them to be paid for.', 'employee-scheduler'); ?>
		</li>
	</ul>
	</p>
	<p><?php _e('To create more shift types, fill in the "Add New Shift Type" form on the left side of the page.  You only need to fill in the name - you can ignore the "slug" field, and the "description" field is optional.', 'employee-scheduler'); ?></p>
	<p><?php _e( 'Examples of shift types you might want to create are: "onsite", "offsite", "flex", etc.', 'employee-scheduler' ); ?></p>
    <p><?php _e('If you want to keep track of different kinds of "Extra" work, you can make shift types with "Extra" as their parent.  If you do this, the "Record Extra Work" form will let staff select what kind of extra work they are recording.', 'employee-scheduler'); ?></p>

	<h4><?php _e('Set up shift statuses', 'employee-scheduler'); ?></h4>
	<p><?php _e('Shifts are also organized into statuses, such as "tentative," "assigned," and "worked."', 'employee-scheduler'); ?></p>
	<p><?php _e('Several shift statuses have already been created for you:', 'employee-scheduler'); ?>
	<ul>
		<li>
			<?php _e('Assigned: Default status for a shift, indicates that this shift has been assigned to a staff member', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('Tentative: Shift has been assigned, but not cast in stone', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('Unassigned: No one has been assigned to work this shift', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('Worked: Staff member has worked this shift', 'employee-scheduler'); ?>
		</li>
	</ul>
	</p>
	<p><?php _e('If you need additional shift statuses, you can create them by going to go to <a href="' . admin_url( 'edit-tags.php?taxonomy=shift_status&post_type=shift') . '">Shifts &rightarrow; Shift Statuses</a> and using the form on the left side of the page.', 'employee-scheduler'); ?></p>
	<p><?php _e('Shift statuses have an extra field for color.  If you assign a color to a shift status, then that color will be used as the border for shifts with that status on the schedule.  So you might want to give the "Tentative" status a light grey color, so that staff can easily see that the shift isn\'t cast in stone.  You will probably also want to assign a color to shifts that have been worked, so that you can easily see if someone missed a shift.  These colors are entirely optional.', 'employee-scheduler'); ?></p>
	<p><?php _e('To assign a color to an existing shift, click on the status name in the list of shift statuses.  Click in the "color" field, and a color wheel will appear.  When you like the color, click "Update."', 'employee-scheduler'); ?></p>

	<h4><?php _e('Create staff', 'employee-scheduler'); ?></h4>
	<p><?php _e('Next you need to set up your staff.  To do this, you will create User accounts for them.', 'employee-scheduler'); ?></p>
	<p><?php _e('Go to <a href="' . admin_url( 'user-new.php') . '">Users &rightarrow; Add New.</a>', 'employee-scheduler'); ?></p>
	<p><?php _e('Enter the information about a staff member: username, name, email, password (the staff member will be able to change the password).  In the dropdown menu labeled "Role," make sure you select "Shiftee Staff."', 'employee-scheduler'); ?></p>
	<p><?php _e('Click "Add New User."', 'employee-scheduler'); ?></p>
	<p><?php _e('Once you have created a user, there are some more fields you can fill in.  Click on <a href="' . admin_url( 'users.php') . '">Users &rightarrow; All Users</a>.  Click on a user to edit them.  Scroll down to the section labeled "Staff Information."  There you can enter their address and phone number.  If you prefer to let your staff fill this out themselves, they can do so with the Staff Profile shortcode.', 'employee-scheduler'); ?></p>

	<h4><?php _e('Create jobs', 'employee-scheduler'); ?></h4>
	<p><?php _e('Next you can to set up your jobs.  Jobs are optional, but if your staff perform different tasks, or if you want to track how many hours are spent at certain tasks, then jobs can be useful.', 'employee-scheduler'); ?></p>
	<p><?php _e('You can create categories for your jobs if you want - it is just like creating shift types or shift statuses.  Some example job categories could be: "requires food handler\'s license", "administrative", "customer-facing", "experienced staff only", etc.', 'employee-scheduler'); ?></p>
	<p><?php _e('To create a job, go to <a href="' . admin_url( 'post-new.php?post_type=job') . '">Jobs &rightarrow; Add New</a>.  For the title, enter the job\'s name.  You can enter a description of the job if you want.  ', 'employee-scheduler'); ?></p>
	<p><?php _e('In the right-hand sidebar, you will see several boxes.  Most of these boxes will be auto-populated as you create shifts and expenses.  However, you might want to fill in some of these boxes.  Here are descriptions of these boxes:', 'employee-scheduler'); ?>
	<ul>
		<li>
			<?php _e('Job Category: If you are grouping your jobs into categories, here is where you select what category or categories this job belongs to.', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('Connected Shifts: This box will auto-populate, so you do not need to enter anything into it.  This box will show you all of the shifts associated with a job.  This box is likely to get very long, so you might want to click the little triangle next to the box title to collapse the box.', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('Connected Expenses: This box will auto-populate, so you do not need to enter anything into it.  When staff enter expenses, they can enter the job associated with that expense: those expenses will show up here.', 'employee-scheduler'); ?>
		</li>
	</ul>

	</p>

	<h4><?php _e('Shortcodes', 'employee-scheduler'); ?></h4>
	<p><?php _e('Here is a list the shortcodes to display your schedule and other information on the site.  To use these shortcodes, simply type the shortcode into the content area of a page on your site.  All shortcodes require users to be logged in to view the content: if a user is not logged in, the shortcode will display a login form instead.', 'employee-scheduler'); ?>
	<ul>
		<li>
			<?php _e('Master Schedule.  Displays the full work schedule for all staff: <br /><code>[master_schedule]</code>', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('Your Schedule.  Displays the schedule for the staff member who is viewing the page (so if John Smith is viewing the site, this page will show him his schedule): <br /><code>[your_schedule]</code>', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('Staff Profile.  Displays the staff member\'s user profile lets them edit their password and contact information:<br /><code>[employee_profile]</code>', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('Today. Displays today\'s shifts to the staff member who is viewing the page:<br /><code>[today]</code>', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('Extra Work.  Displays a form where staff can enter the date, start time, end time, and description of extra work they do that is not a part of a scheduled shift: <br /><code>[extra_work]</code>', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('Record Expense. Displays a form where staff can enter mileage and other expenses:<br /><code>[record_expense]</code>', 'employee-scheduler'); ?>
		</li>
	</ul>
	</p>

	<h3><?php _e('Creating the Schedule', 'employee-scheduler'); ?></h3>
	<p><?php _e('To create a single shift:', 'employee-scheduler'); ?>
	<ul>
		<li>
			<?php _e('Go to <a href="' . admin_url( 'post-new.php?post_type=shift') . '">Shifts &rightarrow; Add New</a>.', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('Enter a title.', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('Enter a description if you want - staff will see this description when they view the shift detail.', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('In the shift details box, enter the date and times of the shift.', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('If you check the box next to "notify staff", the staff member will receive an email telling them this shift has been created.', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('In the right sidebar, look for the box labeled "Connected Jobs."  Click "Choose connected job" to choose a job.', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('In the right sidebar, look for the box labeled "Assigned Staff."  Click "Choose staff member" to choose the person who will work this shift.', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('You can choose a Shift Status in the right sidebar.  If you do not choose a status, the shift will default to "Tentative."', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('You can also choose a Shift Type in the right sidebar.', 'employee-scheduler'); ?>
		</li>
		<li>
			<?php _e('When you are happy with all of these details, click "Publish."', 'employee-scheduler'); ?>
		</li>
	</ul>
	</p>
	<p><?php _e( 'Once you have created some shifts, they will show up on the page where you have put the <code>[master_schedule]</code> shortcode.  Staff can then view the shifts.  If an staff member is viewing a shift that is assigned to them on the day the shift is scheduled, they will see a "clock in" button on the shift.', 'employee-scheduler' ); ?></p>

	<?php do_action( 'shiftee_more_instructions' ) ;?>

</div>