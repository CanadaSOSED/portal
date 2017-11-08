<?php
/**
 * Template Name: My Trip Page Template
 *
 * Template for displaying a page just with the header and footer area and a "naked" content area in between.
 * Good for landingpages and other types of pages where you want to add a lot of custom markup.
 *
 * @package sos-primary
 */



if(is_user_logged_in()){

	get_header();

	$my_trip_application = get_posts(array(
		'posts_per_page'    =>  -1,
		'post_type'         =>  'trip_applications',
		'post_status'       =>  'publish',
		'meta_key'			=>  'ta_user_id',
		'meta_value'		=>  get_current_user_id()
	));

	$all_trips = get_posts(array(
		'posts_per_page'    =>  -1,
		'post_type'         =>  'trips',
		'post_status'       =>  'publish'
	));

	$application_states = get_field_object("field_59ef820057f5c");

	$main_blog_url = get_site_url();

	foreach($my_trip_application as $app){
		$app_id = $app->ID;
		$trip_id = get_field('ta_trip_select', $app->ID);
		$trip_status_id = get_field('ta_application_state', $app->ID);
		$interview_date = get_field('ta_interview_date', $app->ID);
		$trip_deposit_payed = get_field('ta_trip_deposit_received', $app->ID);
		$trip_flight_cost_payed = get_field('ta_flight_cost_received', $app->ID);
		$trip_participation_payed = get_field('ta_participation_fee_received', $app->ID);
		$trip_cancelled = get_field('ta_trip_cancelled', $app->ID);
		$volunteer_outreach_form = get_field('ta_medical_acknowledge_medical_conditions', $app->ID);
		$medical_fitness_form = get_field('ta_fitness_agree_to_terms_medical_fitness_form', $app->ID);
		$policies_agreed = get_field('ta_agree_to_policies_and_procedures', $app->ID);
		$waiver_uploaded = get_field('ta_waiver_uploaded', $app->ID);
		$webinar_registered = get_field('ta_webinar_registered', $app->ID);
	}

	foreach($all_trips as $trip){
		if($trip_id == $trip->ID){
			$trip_name = $trip->post_title;
			$trip_deposit_url = get_field('trip_deposit_installment', $trip->ID)->guid;
			$trip_flight_cost_url = get_field('trip_flight_cost_installment', $trip->ID)->guid;
			$trip_participation_url = get_field('trip_participation_fee_installment', $trip->ID)->guid;
			$trip_resources = get_field('trip_resources', $trip->ID);
		}
	}

	$options_trip_resources = get_field('options_resources', 'options');
	$waiver_download_url = get_field('trip_waiver', 'options');


	foreach($application_states['choices'] as $value => $state){
		if($trip_status_id == $value){
			$trip_state = $state;
		}
	}



	echo '<div class="wrapper">';
	echo '<div class="container">';
	echo '<div class="row">';
	echo '<div class="col-md-12 content-area" id="primary">';
	echo '<h1>My Trip</h1>';
	echo '<strong>Your Trip is:</strong> ' . $trip_name;
	echo '<br>';
	echo '<strong>Status:</strong> ' . $trip_state;
	echo '<br>';

	if($interview_date != Null){
		echo '<strong>Interview Date:</strong> ' . $interview_date;
	}else{
		echo '<strong>Interview date:</strong> Not Set';
	}

	/////// Checklist Area ///////
	echo '<br><br>';
	echo '<strong>To Do List:</strong>';
	echo '<br>';
	echo '<p>Please complete the following items at <u>least <strong>TWO</strong></u> months prior to the start date of your SOS Outreach Trip. <br>
		Failure to complete these items can result in the cancellation of your SOS Outreach Trip</p>';

	$volunteer_outreach_url = $main_blog_url . "/volunteer-outreach-form/?App=" . $app_id;

	if($volunteer_outreach_form == 1){
		echo '&#10004 Volunteer Outreach Participation Form Complete';
	}else{
		echo '<a href='. $volunteer_outreach_url . '>Click here to fill out the Volunteer Outreach Participation Form</a>';
	}

	echo '<br>';

	$policies_url = $main_blog_url . "/policies-and-procedures/?App=" . $app_id;

	if($policies_agreed == 1){
		echo '&#10004 Policies and Procedures Agreed';
	}else{
		echo '<a href='. $policies_url . '>Click here to read the Policies and Procedures</a>';
	}

	echo '<br>';

	$waiver_upload_url = $main_blog_url . "/trip-waiver-upload/?App=" . $app_id;

	if($waiver_uploaded == 1){
		echo '&#10004 Waiver Uploaded';
	}else{
		echo '<a target="_blank"
				 href='. $waiver_download_url . '
				 title="Please download, complete, sign and re-upload the following waiver. If you don&#39;t have access to a scanner, you can take a picture of all three pages and upload them below. Please note: when filling out the waiver, &#39;Chapter&#39; refers to the school or group that you are travelling with. Example: Ryerson University, Wilfrid Laurier University Alumni, etc.​​">Download the Trip Waiver</a>';
		echo '<br>';
		echo '<a href='. $waiver_upload_url . '>Click here to upload the Trip Waiver</a>';
	}

	echo '<br>';

	$medical_fitness_url = $main_blog_url . "/medical-fitness-form/?App=" . $app_id;

	if($medical_fitness_form == 1){
		echo '&#10004 Medical Fitness Form Complete';
	}else{
		echo '<a href='. $medical_fitness_url . '>Click here to fill out the Medical Fitness Form</a>';
	}

	echo '<br>';

	$webinar_url = $main_blog_url . "/pre-departure-webinar";

	if($webinar_registered == 1){
		echo '&#10004 Pre-Depature Webinar Registered';
	}else{
		echo '<a href='. $webinar_url . '>Click here to Register for a Pre-Departure Webinar</a>';
	}

	/////// Payment Area ///////
	echo '<br><br>';
	echo '<strong>Payment Status:</strong>';
	echo '<br>';

	if($trip_deposit_payed != 1){
		echo '<strong>Your Deposit:</strong> Not Paid';

		if($trip_status_id != 'application_confirmed'){

		}else{
			echo '<a href=' . $trip_deposit_url .'> Pay Now </a>';
		}

	}else{
		echo '<strong>Your Deposit:</strong> Paid';
	}

	echo '<br>';

	if($trip_flight_cost_payed != 1){
		echo '<strong>Your Flight Cost:</strong> Not Paid';

		if($trip_status_id != 'insurance_info_approved'){

		}else{
			echo '<a href=' . $trip_flight_cost_url .'> Pay Now </a>';
		}

	}else{
		echo '<strong>Your Flight Cost:</strong> Paid';
	}

	echo '<br>';

	if($trip_participation_payed != 1){
		echo '<strong>Your Participation Fee:</strong> Not Paid';

		if($trip_status_id != 'flight_cost_received'){

		}else{
			echo '<a href=' . $trip_participation_url .'> Pay Now </a>';
		}
	}else{
		echo '<strong>Your Participation Fee:</strong> Paid';
	}

	/////// Resources Area ///////
	echo '<br><br>';
	echo '<strong>Trip Resources:</strong>';
	echo '<br>';

	echo '<ul>';
	if($trip_resources){
		foreach($trip_resources as $resource){
			echo '<li><a target="_blank" href=' . $resource['trip_resource'] .'>' . $resource['resource_name'] . '</a></li>';
		}
	}
	if($options_trip_resources){
		foreach($options_trip_resources as $resource){
			echo '<li><a target="_blank" href=' . $resource['resource'] .'>' . $resource['resource_name'] . '</a></li>';
		}
	}

	echo '</ul>';

	echo '</div>';
	echo '</div>';
	echo '</div>';
	echo '</div>';


	get_footer();

}else{
	wp_redirect(home_url() . "/my-account");
    exit();
}
