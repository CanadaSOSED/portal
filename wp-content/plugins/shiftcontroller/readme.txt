=== ShiftController - Employee Shift Scheduling ===

Contributors: HitCode
Tags: staff scheduling, shift scheduling, employee scheduling, rota shift scheduling
License: GPLv2 or later

Stable tag: trunk
Requires at least: 3.3
Tested up to: 4.7

Schedule staff and shifts anywhere at anytime online from your WordPress powered website.

== Description ==

ShiftController is a staff scheduling plugin for any business that needs to manage and schedule staff. 
It provides the ability for the administrators to assign staff members to the shifts.
ShiftController allows to manage timeoffs and holidays so you can assign only those people who are available for work. 
It helps overcome schedule conflicts as you can see and correct any conflicts due to overlapping shifts or timeoffs. 

The monthly shifts are listed by position (location) or by staff member, the plugin automatically calculates the working time and the number of shifts. The plugin automatically emails the schedule to every staff member and lets them know when they work.

###Pro Version Features###

* __Recurring shifts__ to quickly schedule shifts weeks ahead
* __Release shifts__ to let your staff release their shifts so other employees could pick them up
* __Comments for shifts and timeoffs__ to keep track of what is going on
* __Bulk edit and delete__  apply changes to multiple shifts and timeoffs at once
* __Staff availability__  configure preferred or unavailable times for staff members 

Please visit [our website](http://www.shiftcontroller.com "WordPress Employee Scheduling") for more info and [get the Premium version now!](http://www.shiftcontroller.com/order/).

ShiftController users database is automatically synchronized with WordPress - you can define which WordPress user roles will be administrators and staff members in ShiftController. 

== Support ==
Please contact us at http://www.shiftcontroller.com/contact/

Author: HitCode
Author URI: http://www.shiftcontroller.com

== Installation ==

1. After unzipping, upload everything in the `shiftcontroller` folder to your `/wp-content/plugins/` directory (preserving directory structure).

2. Activate the plugin through the 'Plugins' menu in WordPress.

3. When you first open ShiftController in your WordPress admin panel, it will ask which existing WordPress user accounts would you like to import into ShiftController..

== Screenshots ==
1. Month Schedule Overview

== Upgrade Notice ==
The upgrade is simply - upload everything up again to your `/wp-content/plugins/` directory, then go to the ShiftController menu item in the admin panel. It will automatically start the upgrade process if any needed.

== Changelog ==

= 3.2.4 =
* Fixed the ical feed that might have failed with certain timezones.
* Minor code updates and fixes.

= 3.2.3 =
* Fixed the non working Shift Templates button in the shift edit form.
* Minor code updates and fixes.

= 3.2.2 =
* Removed potentially vulnerable own copy of PHPMailer library.

= 3.2.1 =
* In the admin view added filter options: filter by status and by type (shift/timeoff).

= 3.2.0 =
* BUG: iCal sync link was not working for some devices.
* BUG: timeoff list didn't show if grouping by location was set as a default view.
* BUG: setup failed if one of WordPress roles contained spaces.
* Added a configuration setting if to send notification when a released shift is picked up.

= 3.1.9 =
* In the "Shift available" notification after shift release removed the old employee name that was confusing.
* Added a global BCC field to send copies of all automatic notifications.

= 3.1.8 =
* User can now save the current calendar view configuration as default.
* Pro: shift comments are added to the iCal export.
* Pro: when a shift is realeased, notification can be sent to all staff members.
* Added French language.
* Minor code updates.

= 3.1.7 =
* Added Danish, German and Dutch languages.
* Minor code updates.

= 3.1.6 =
* BUG: certain actions were giving a 404 error if the admin panel was used in the front end with a shortcode.

= 3.1.5 =
* BUG: If ShiftController was activated but not yet setup, editing user accounts in WordPress gave error.
* Minor code updates.

= 3.1.4 =
* BUG: For iCal output if a shift had a break it was giving a wrong end time.
* BUG: Employees can not release shifts from their control panel.
* Minor PHP compatibility fixes

= 3.1.3 =
* Making "add" links appear constantly rather than on mouse over that caused issues on several platforms.
* BUG: Preferred availability setting could give a fault conflict alert.

= 3.1.2 =
* BUG: fixed the "range" shortcode parameters like "2 weeks" or "8 days" after they stopped working properly in 3.1.0.
* BUG: the shifts copy function didn't make use of the selected date, copying just to the next week.
* Not grouped month calendar is displayed in detailed view.

= 3.1.1 =
* BUG: it was not possible to click on an open shift in the "group by location" view.

= 3.1.0 =
* Added a configurable option for employees to view draft shifts, create and edit shifts.
* Added an option to copy shifts from a certain week (or month) to another week, so this feature can be used as a sort of schedule templates.
* Added an option to disable certain days of the week. So for example if you don't work on Saturdays and Sundays, it will not show them in the calendar leaving more screen space for work days.
* Colors for locations can now be manually picked rather than assigned automatically.
* Added the day view with timebar for a better visual overview.
* Changed CSS and font icons libraries to greatly reduce CSS and icon files sizes - faster speed and smaller distrib size.
* Employees can edit their own availability preferences if allowed by the admin.
* BUG: If the shortcode is set to "by=staff" or "by=location" then the front end can not change to the view without grouping by.

= 3.0.9 =
* BUG: The "Disable Email" setting was not taking effect.
* BUG: Ajax actions didn't work in the admin panel for https websites.

= 3.0.8 =
* BUG: Appeared after 3.0.7 after adding the shift break option. When entering a shift and the shift end time is the next day, error "The break should not be longer than the shift itself" was returned.

= 3.0.7 =
* Added shift breaks option, the duration of a break is not counted toward the total hours worked.
* Following introduction of shift breaks, timeoff icon changed: coffee is for lunch breaks now, timeoff is marked with the away icon.
* Added reports page to display number of shifts and time worked.
* BUG: Print view was corrupted when clicking the printer icon button.
* Sync users from WordPress with their display name rather than first/last name
* Replaced JavaScript timepicker by a regular dropdown because it was causing too many compatibility issues.
* Added location change option in the bulk edit form [Pro].
* Redesigned Ajax calls that should greatly improve the load speed for many actions.

= 3.0.6 =
* BUG: The start and end time inputs were not working when opening the bulk edit form in the Shift Series tab.
* BUG: The delete action didn't work in the bulk edit form in the Shift Series tab.
* BUG: After the delete action in the With All bulk edit form the calendar view was not properly refreshed.
* BUG: Error if filter the shifts by location in the calendar.

= 3.0.5 =
* Added the iCal export option (to Google Calendar or any other application capable of receiving iCal feed).
* Moved the Users menu under Configuration.
* BUG: Fixed the print view in Chrome.

= 3.0.4 =
* BUG: The link to the WP page to edit a user account was not working.
* BUG: When in the shortcode by="location" is used the logged in user cannot see their own shifts.
* BUG: Setup on new installs might fail under certain configurations.
* Now comments are added to notifications emails too [Premium].
* BUG: In the bulk form when opening and closing the subforms several times their inputs became disabled.
* Added a setting if to show the shift end time.
* Made the shift view text a bit larger.
* New shortcode parameter to hide certain user interface elements.

= 3.0.3 =
* BUG: Shift Release and Shift Pickup settting menu items were not localized.
* BUG: Fatal error when trying to delete a user account.
* BUG: "User deleted" message was not localized.
* Remember the last choice of the "Comment Visible To" option.
* Modified the timepicker library to avoid possible conflicts with other libraries from other plugins.

= 3.0.2 =
* BUG: Fatal error on new setup caused by a change in ver. 3.0.1
* Fixed the help page on shortcode parameter within the admin panel.
* Added a few more params options for the shortcode.

= 3.0.1 =
* BUG: There was an empty, unlabelled drop down box in the admin area in Configuration > Settings
* Modified the datepicker library to avoid possible conflicts with other libraries from other plugins.
* The add links in calendar now lead directly to the shift creation, without the shift/timeoff selection. Timeoffs are now created in the Timeoff Requests area.
* In the shift create form the location and the time are now on the same page to make the process quicker.
* Fixed success message after timeoff creation (was saying "shift added" rather than "timeoff added").
* Remember the last choice of several options in the shift creation form: status, skip notification email.
* Re-added shift templates.
* Added draft shifts for the admin todo page.
* Added the "With All" shift group action to perform bulk actions on all displayed shifts.
* Added an option to assign multiple staff members at once when creating a shift.
* Added a red triangle for open shifts for easier notice.

= 3.0.0 =
* A new major ShiftController update, almost completely reworked! 

= 2.4.1 =
* A small fix in code that might break redirects with WP high error reporting level.

= 2.4.0 =
* A fix for multiple staff ids in the shortcode param. 

= 2.3.9 =
* A slight optimization on login/logout internal process.

= 2.3.7 =
* BUG: On plugin complete uninstall might delete all WordPress tables.

= 2.3.6 =
* BUG: (Pro Versions) multiple shifts could be deleted when deleting a single shift created as non recurring from shift edit form in the Delete tab. 
* An option to color code shifts in the calendar according to the location
* Added the "within" parameter option for the shortcode to display shifts that are on now and within the specified tim

= 2.3.5 =
* Configuration option to set min and max values for time selection dropdowns, that will speed up time selection.
* Drop our database tables on plugin uninstall (delete) from WordPress admin. Also release the license code for the Pro version so it can be reused in another installation.
* Backend appearance restyled for a closer match to the latest WordPress version.
* Cleaned and optimized some files thus reducing the package size.

= 2.3.4 =
* Shift pickup links didn't work for staff members on the everyone schedule page (shortcode page).

= 2.3.3 =
* JavaScript error when staff picking up free shifts from everyone schedule page (shortcode page).

= 2.3.2 =
* A fix in session handling function that lead to an error on first user access of the system.

= 2.3.1 =
* Archived staff members are now not showing in the stats display if they have no shifts during the requested period.
* In the shortcode if you need to filter more than one location or employee, now you can supply a comma separated list of ids, for example [shiftcontroller staff="1,8"].
* Also if you do not want to show the list of locations in the shortcode page, you can supply the location parameter as 0 so it will list shifts for all locations [shiftcontroller location="0"]

= 2.3.0 =
* Added more options for shortcode to filter by location or by staff, as well as specify the start and end date and how many days to show.
* Extended options for the shift notes premium module, now one can define who can see the shift note - everyone, staff members, this shift staff or admin only.

= 2.2.9 =
* If more than one locations are available in the "Everyone Schedule" then it first asks to choose a location first.
* Added the description field for locations. If specified, it will be given in the "Everyone Schedule" and "Shift Pick Up" parts if more than one location available.
* Redesigned the "Everyone Schedule" (wall) page view so that lists all upcoming shifts in a simple list. It is supposed to eliminate all the compatibility issues for the shortcode page display as the calendar output would look cumbersome under certain themes.

= 2.2.8 =
* If there are open shifts in more than one location, an employee is asked to choose a location first, then the available shifts in this location ara displayed.
* Minor fixes and code updates

= 2.2.7 =
* Added an option to supply parameters to the shortcode to define the range (week or month) and the starting date, please check out the Configuration > Shortcode page
* Minor fixes and code updates

= 2.2.6 =
* Minor fixes and code updates

= 2.2.5 =
* BUG: In the schedule list view, if you choose filtering by location, the shifts for all locations were still displayed as if there were no filter applied. 
* BUG: When creating a new shift, if you selected one or several employees to assign right now, but there was a validation error (no location selected, or the start and end times were incorrect), it showed a database error. 

= 2.2.4 =
* Fixed an issue with shortcode that might be moving into infinite loop for admin and staff users
* An option to color code shifts in the calendar according to the employee
* An option to hide the shift end time for the employees 
* An option to disable shift email notifications
* Minor fixes and code updates

= 2.2.3 =
* Reworked the calendar view controls - now the list and stats display can also be filtered by location and by employee. 
* Fix with the timezone assignment
* Locations are sorted properly in the form dropdown
* Wrong employee name when a time off was requested by an employee
* when synchronizing users from WordPress you can append the original WP role name to the staff name

= 2.2.2 =
* Configure which user levels can take shifts
* Assign employees to shifts from the calendar view
* Fixed a problem with irrelevant email notifications
* Select multiple staff members or define the required number of employees when creating a shift

= 2.2.1 =
* Fixed problem when shortcode was not working properly

= 2.2.0 =
* Shift history module
* More convenient schedule views (show calendar by location and by staff member, week or month view)
* Updated view framework (Bootstrap 3)
* Minor code optimizations and bug fixes

= 2.1.1 =
* Login log module
* BUG: Select All in Timoffs and Shift Trades admin views were not working
* BUG: Repeating options were not active in the Premium version
* Minor code optimizations and bug fixes

= 2.1.0 =
* Fixed bug when email notification was not sent after publishing just one shift
* Remove location label if just one location is configured
* Shift notes view in the calendar
* Archived users do not appear in the dropdown list when creating or editing shifts


= 2.0.6 =
* Shifts month calendar

= 2.0.5 =
* Shifts list in a table view and CSV/Excel export

= 2.0.4 =
* Custom weekdays for recurring shifts

= 2.0.3 =
* Display shifts grouped by locations

= 2.0.2 =
* Public employee schedule calendar and minor bug fixes

= 2.0.1 =
* Bug fix: error when creating a new user in the free version.

= 2.0.0 =
* Completely reworked calendar view and the premium version.

= 1.0.2 =
* Bug fixes: time display, forgotten password and password change, email notification on a new timeoff.

= 1.0.1 =
* Bug fixes after not complete form in setup and error after timeoff delete.

= 1.0.0 =
* Initial release



Thank You.

 
