<?php
echo HC_Html::page_header(
	HC_Html_Factory::element('h1')
		->add_child(
			'Shortcode'
		)
	)
?>
<p>
With the following shoftcode you can insert your <strong class="hc-bold">Everyone's Schedule</strong> view into a post or a page. 
</p>

<p>
<code class="hc-p2 hc-mt1 hc-mb1 hc-border hc-block hc-maroon">
[<?php echo $shortcode; ?>]
</code>
</p>

<p>
By default, this view will display the current week shifts calendar. 
If need to, you can adjust it by supplying additional parameters to control the display:
</p>

<ul>
	<li class="hc-mb2 hc-mt1 hc-ml3">
		<strong class="hc-bold">date</strong>: <em class="hc-italic">yyyymmdd</em>, for example <em class="hc-italic">20140901</em>. If not supplied, it will start from the current date.
	</li>

	<li class="hc-mb2 hc-mt1 hc-ml3">
		<strong class="hc-bold">range</strong>
		<ul class="hc-ml3">
			<li class="hc-mb1 hc-mt1">
				<em class="hc-italic">week</em>
				<p class="hc-ml3">
				It will display the week calendar with shifts starting from Sunday (or Monday) of the current week regardless of the current week day.
				</p>
			</li>
			<li class="hc-mb1 hc-mt1">
				<em class="hc-italic">month</em>
				<p class="hc-ml3">
				It will display the month calendar with shifts starting from the 1st of the current month regardless of the current date.
				</p>
			</li>
			<li class="hc-mb1 hc-mt1">
				Time range, for example <em class="hc-italic">5 days</em>, <em class="hc-italic">2 weeks</em>
				<p class="hc-ml3">
				It will display the list of shifts starting from the date specified in the <strong class="hc-bold">date</strong> parameter and within the range given. If no <strong class="hc-bold">date</strong> is giving, it will start from today.
				</p>
			</li>
		</ul>
	</li>

	<li class="hc-mb2 hc-mt1 hc-ml3">
		<strong class="hc-bold">location</strong>: <em class="hc-italic">location id</em>, for example <em class="hc-italic">2</em>. You can find out the id of a location in <em class="hc-italic">Configuration &gt; Locations</em>. If not supplied, it will display shifts of all locations.
		You can also supply several ids separated by comma. 
	</li>

	<li class="hc-mb2 hc-mt1 hc-ml3">
		<strong class="hc-bold">staff</strong>: <em class="hc-italic">staff id</em>, for example <em class="hc-italic">3</em>. You can find out the id of an employee in <strong class="hc-bold">Users</strong>. If not supplied, it will display shifts of all employees.
		You can also supply several ids separated by comma. 
	</li>

	<li class="hc-mb2 hc-mt1 hc-ml3">
		<strong class="hc-bold">route</strong>
		<p>
		This parameter defines the default area where the visitor gets to by going to the page with ShiftController shortcode.
		</p>

		<ul class="hc-ml3">
			<li class="hc-mb1 hc-mt1">
				<em class="hc-italic">list</em>
				<p>
				The only option available for not logged in visitors. 
				It will display everyone's shifts (the <strong class="hc-bold">Full Schedule</strong> page).
				</p>
			</li>
			<li class="hc-mb1 hc-mt1">
				<em class="hc-italic">listme</em>
				<p>
				The default option for logged in employees. 
				It will display the shifts of the currently logged in user (the <strong class="hc-bold">My Schedule</strong> page).
				</p>
			</li>
			<li class="hc-mb1 hc-mt1">
				<em class="hc-italic">list-toff</em>
				<p>
				Available for logged in employees only. It will display the list of the employee's timeoff requests (the <strong class="hc-bold">Timeoff Requests</strong> page).
				</p>
			</li>
		</ul>
	</li>

	<li class="hc-mb2 hc-mt1 hc-ml3">
		<strong class="hc-bold">hide-ui</strong>
		<p>
		Optionally you can hide certain user interface elements on the front end page. Separate several options by comma. Possible options include:
		</p>

		<ul class="hc-ml3">
			<li class="hc-mb1 hc-mt1">
				<em class="hc-italic">login, filter-staff, filter-location, print, download, view-type, group-by, date-navigation</em>
			</li>
		</ul>
	</li>
</ul>

<p>
<h3>Examples</h3>
</p>

<p>
Month calendar for September in location #2:
</p>

<p>
<code class="hc-p2 hc-mt1 hc-mb1 hc-border hc-block hc-maroon">
[<?php echo $shortcode; ?> date="20150901" range="month" location="2"]
</code>
</p>

<p>
Week calendar for the current week:
</p>

<p>
<code class="hc-p2 hc-mt1 hc-mb1 hc-border hc-block hc-maroon">
[<?php echo $shortcode; ?> range="week"]
</code>
</p>

<p>
List shifts in the next 3 days:
</p>

<p>
<code class="hc-p2 hc-mt1 hc-mb1 hc-border hc-block hc-maroon">
[<?php echo $shortcode; ?> range="3 days"]
</code>
</p>

<p>
Make the Full Schedule page a default view for a logged in employee (instead of the My Schedule page):
</p>

<p>
<code class="hc-p2 hc-mt1 hc-mb1 hc-border hc-block hc-maroon">
[<?php echo $shortcode; ?> route="list"]
</code>
</p>

<p>
Do not show the login link and the download button:
</p>

<p>
<code class="hc-p2 hc-mt1 hc-mb1 hc-border hc-block hc-maroon">
[<?php echo $shortcode; ?> hide-ui="login,download"]
</code>
</p>
