=== Shiftee Basic - Employee and Staff Scheduling ===
Contributors: range, gwendydd, jpkay, saracannon, scubakyle
Tags: employee, staff, schedule, clock in, clock out, payroll, work schedule, timesheet, volunteer schedule, volunteer, human resources
Requires at least: 4.0
Tested up to: 4.7.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Complete staff schedule management system: create and display schedule, let staff clock in and out, report expenses.

== Description ==

Shiftee Basic does everything you need to keep track of your staff schedules!  Whether you have paid employees or volunteers, Shiftee can track their schedule, their worked hours, and their expenses.

* Create a work schedule for staff
* Send email notifications to staff when their shifts are created or updated
* Display the schedule on your website - only logged-in users will see it
* Staff can clock in and clock out
* Staff can report expenses and mileage

[youtube http://youtu.be/4lqZg77B9Ro]

Learn more at [shiftee.co](https://shiftee.co/)

Upgrade to [Shiftee](https://shiftee.co/downloads/shiftee/) for even more features!

* Bulk create shifts
* Bulk edit shifts
* Staff can claim unassigned shifts
* Staff can drop shifts
* Manager user role
* Create payroll reports
* Easily filter shifts and expenses on several criteria
* View report comparing staffs' scheduled hours to hours actually worked
* Personal, priority support

== Installation ==

The Plugin can be installed directly from the main WordPress Plugin page.

1. Go to the Plugins => Add New page.
2. Enter 'Shiftee Basic' (without quotes) in the textbox and click the 'Search Plugins' button.
3. In the list of relevant Plugins click the 'Install' link.
4. Click the 'Install Now' button on the popup page.
5. Click 'Activate Plugin' to finish installation.
6. That's it!

After installation, you can go to Shiftee Basic --> Instructions in your WordPress dashboard to learn how to configure the plugin.


== Frequently Asked Questions ==

For complete documentation, visit the [Shiftee Basic Documentation](https://shiftee.co/docs/category/shiftee-basic/)

== Screenshots ==

1. Plugin settings page
2. Creating a shift
3. Master schedule
4. Your schedule - the employee who is logged in sees only their own shifts
5. Single shift view - if the shift is assigned to the logged-in user who is viewing the shift on the day the shift is scheduled, a "clock in" button appears.  If they have already clocked in, they will see a "clock out" button.
6. Expense report form
7. Extra work form, for reporting work that is not a part of a scheduled shift
8. Shift overview in dashboard

== Changelog ==
= 2.1.0 =
* improvement: switch from WP Alchemy to CMB2 for meta boxes
* new feature: overnight/multi-day shifts
* improvement: change "employee" to "staff" globally
* improvement: front-end displays follow user settings for date and time display formats
* improvement: on job page in admin, do not show connected shifts and expenses
* improvement: break settings page into sensible sections
* improvement: master schedule fits better in smaller spaces and smaller screens

= 2.0.4 =
* improvement: better error reporting if user has turned off geolocation data
* bug fix: fix the syntax error that prevented expense receipts from saving
* improvement: add filter to the 'CC:' on the notification email sent to employees when a shift is created and assigned to them

= 2.0.3 =
* improvement: add filter to today shortcode to make it customizable
* improvement: remove "shift" archive page (some users will need to save permalinks to see this change)

= 2.0.2 =
* bug fix: make master_schedule shortcode work with parameters
* bug fix: make your_schedule shortcode display jobs
* improvement: make email character set translatable

= 2.0.1 =
* bug fix: rename wpaesm_filter_your_schedule to shiftee_filter_your_schedule
* bug fix: make managers appear on the Bulk Shift Creator
* bug fix: don't load scripts on employee_profile page in dashboard
* bux fix: error message in sidebar on instructions page

= 2.0.0 =
* Rebranding: change name to Shiftee Basic
* complete code overhaul
* add "location" to master schedule view

= 1.9.0 =
* added lots of CSS classes to schedule to make it easy to customize
* compatibility with Shiftee On Demand
* compatibility with Shiftee Text Notifications
* security improvements
* added Location to extra work form
* added receipt image upload field to expense report form
* added optional parameter to master shortcode to make it publicly viewable
* fixed bug in dashboard view of master schedule
* add option for currency
* improvements to the "add a note" form on single shift view
* support for WordPress language packs
* on the master schedule, multiple assigned shifts are hidden with a toggle to view them all

= 1.8.6 =
* fixed bug that prevented job title from displaying in today shortcode

= 1.8.5 =
* fixed bug that prevented multiple shifts per day from showing on your schedule shortcode

= 1.8.4 =
* fixed bug in expense report form

= 1.8.3 =
* added actions so that On Demand add-on can add more fields to employee profile
* user must be logged in as administrator or employee to see employee profile shortcode
* changes to CSS and JS to accommodate On Demand
* fixed bug in extra work shortcode

= 1.8.2 =
* removed the "change shift status" form from the single shift view - employees can no longer change shift status
* actually fixed the master schedule bug that 1.8.1 claimed to fix

= 1.8.1 =
* when creating shifts, you can connect to users with any user role (limiting to employee created more problems than it solved)
* fixed bug that prevented some shifts from appearing in the master schedule

= 1.8.0 =
* added hooks and filters throughout to make customization/extension easier.  See [documentation](https://employee-scheduler.co/documentation/)
* "your schedule" shortcode shows the same shift status background colors as the "master schedule" shortcode
* fixed CSS on master schedule so that schedule navigation looks better
* added "type", "status", and "location" parameters to master_schedule and your_schedule shortcodes
* added "former employee" user role
* when creating shifts, you can only connect to users with "employee" user role

= 1.7.1 =
* fixed bug that prevented bulk shift updater in Shiftee Pro from working

= 1.7.0 =
* added option to send an email to site admin whenever an employee clocks out
* added option to require admin approval before counting extra shifts as worked

= 1.6.0 =
* error messages will display if clock in and clock out times are not recorded
* added new admin page to view schedules
* added location taxonomy to shifts
* updated code documentation

= 1.5.0 =
* when employee fills out "extra work" form, the scheduled time fields are filled in
* make shifts show up on schedule when no job is assigned
* make shifts show up on schedule when no employee is assigned
* add HTML to emails
* change h2 to h1 on admin screens to conform to new accessibility standards
* improved options validation
* added filters to accommodate new features in Shiftee Pro (ability to drop and claim shifts)
* fixed compatibility problem with wpp2p and WP 4.3

= 1.4.2 =
* fix compatibility issues with other plugins using WPP2P

= 1.4.1 =
* improved geolocation error reporting
* minor changes to work with schedule conflict checking in Shiftee Pro

= 1.4 =
* updated datetimepicker js library
* fixed bugs in [employee_profile] shortcode

= 1.3 =
* fixed header on "your schedule" shortcode to show the correct dates
* when clock out button is visible, clock in time is displayed
* fixed formatting issues on [employee_profile] shortcode

= 1.2 =
* made shift email notifications more reliable
* improved shift status change notification email

= 1.1 =
* removed conflict with other plugins that use WP Alchemy for metaboxes
* removed extraneous files
* minor fixes to be compatible with Shiftee Pro

= 1.0 =
* Initial release