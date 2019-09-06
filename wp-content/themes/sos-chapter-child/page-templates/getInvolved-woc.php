<?php
/**
 * Template Name: WOC - Get Involved
 *
 * This template can be used to override the default template and sidebar setup
 *
 * @package sos-chapter
 */

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>

<section id="bcg_banner" style="background-image: linear-gradient(0deg, rgba(128,128,128,0.4) 0%, rgba(128,128,128,0.3) 100%),url('https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/sites/34/2019/03/28134344/bck_banner1.jpg');">
	<div class="row">
		<div class="col-12 col-md-8 ml-md-10 align-middle py-5 px-4 py-lg-5 px-lg-8">
			<div style="font-size: 5.0em;font-weight: 500; color: #fff; text-align:center;"><strog>GET INVOLVED</strong></div>
		</div>
	</div>
</section>

<section id="causeSection">
   <div class="row no-gutters">
      <div class="col-12 py-6 px-4 py-lg-5 px-lg-6 order-md-2" id="service-trip">
         <div style="font-size: 2.5em; color: #0f425c; text-align:center; margin-bottom:20px;"><strong>Join a Service Trip</strong></div>

         <div style="position:relative; float: left; width: 60%; padding-top:15px;  padding-right:20px;">

            <div style="font-size:1.3em; text-align:left;">
               <p>Each year, the Winds of Change community participates in annual one-week service trips each November and May to work alongside leaders from our partner community in Latin America as well as youth leaders from the SOS network. These cross-cultural, intergenerational experiences are a transformative experience for all involved.</p>
               <ul>
                  <li>Learn all about what is a <a href="https://studentsofferingsupport.ca/trip-overview/" target="_blank"><strong>Service Trip</strong></a></li>
  	          <li>Learn about this year's <a href="https://studentsofferingsupport.ca/upcoming-trips/" target="_blank"><strong>Upcoming Trips</strong></a></li>
	          <li>Learn more how the service trips fit with our <a href="https://studentsofferingsupport.ca/our-development-model/" target="_blank"><strong>Community Development Model</strong></a></li>
               </ul>
	       <p>SOS and Winds of Change can also facilitate corporate service trips, tailored to your company. Learn more bellow.</p>
            </div>

         </div>
         <div style="position:relative; float: left; width: 40%; padding-top:5px;">
	    <img src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/sites/34/2019/04/09165744/IMG_5484.jpg"/>
	 </div>

      </div>
   </div>
</section>



<section id="bcg_banner" style="background-image: linear-gradient(0deg, rgba(3,21,35,0.6) 0%, rgba(3, 21, 34, 0.5) 100%),url('https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/sites/34/2019/03/28154702/bck_banner3.jpg');">
	<div class="row">
		<div class="col-12 col-md-8 ml-md-10 align-middle py-5 px-4 py-lg-5 px-lg-8"  id="company-involved">
      <div style="font-size: 2.5em;font-weight: 500; color: #fff; text-align:center;">Get your Company Involved</div>
		</div>
	</div>
</section>


<section class="progBoxes">
	<div class="row">
		<div class="col-12 py-6 px-4 py-lg-3 px-lg-5 order-md-2">
  		<div style="position:relative; float: left; width: 40%; padding-top:5px;  padding-right:20px;">
	  		 <img src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/sites/34/2019/03/28173350/IMG_5738.jpg"/>
		  </div>
      <div style="position:relative; float: left; width: 60%; padding-top:15px;">
        <div style="font-size: 1.5em; color:#0f425c;"></div>
				<p style="font-size:1.3em;">
					Are you looking for innovative ways to strengthen your organizational culture, ignite employee engagement, attract top talent, and create a high-impact Corporate Social Responsibility program?</br></br>
					Winds of Change and SOS have created Catapult to build upon past collaborations with fast-growing companies like <a href="https://www.prophix.com/" target="_blank"><strong>Prophix</strong></a> and <a href="https://www.quarry.com/" target="_blank"><strong>Quarry Communications</strong></a>. By becoming a Catapult corporate participant, you can help improve global access to education, while facilitating an incredible experience of leadership development and culture-building for your employees. </br></br>
   			        <i>Learn more by <a style="color: #54c9ff;" href="<?php echo get_site_url(); ?>/contact-us" target="_top">Contacting us</a>.</i> </p>
		  </div>
	  </div>
	</div>
</section>


<section class="progBoxes" style="background-color: #efefef;" id="donate">
	<div class="row" >
		<div class="col-12 py-2 px-2 py-lg-5 px-lg-6 order-md-2">
			<p style="font-size: 2.5em; color: #0f425c; text-align:center;">Donate</p>
      <div style="position:relative; float: left; width: 60%; padding-top:15px;  padding-right:20px;">
        <div style="font-size: 2em; color: #0f425c;"><p>CURRENT PROJECT: A school and a library in Nicaragua need your help...</p></div>
				<p style="font-size:1.3em;"> Political unrest in Nicaragua has caused many charities and other Non Governmental Organizations (NGOs) to stop hosting service trips to the country. Revenue from these trips is the main source of funding for many small-scale development projects in Nicaragua so the cancellation of trips puts many projects in difficult financial position. This means that badly needed schools and library facilities are left unfinished leaving kids in crowded, often leaky classrooms. Not only do these projects provide badly needed infrastructure they bring communities together in a constructive and meaningful way - at a time when communities are suffering tension and hardship from the ongoing  unrest.</p>
				<p style="font-size:1.3em;"> <a href=" https://www.canadahelps.org/en/pages/winds-of-change-2019-fundraiser-schools-in-guatema/" target="_blank"><strong>Click here to donate.</strong> </a> </p>
		  </div>
			<div style="position:relative; float: left; width: 40%; padding-top:5px;">
	  		 <img src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/sites/34/2019/03/28174356/donate.jpg"/>
		  </div>
	  </div>
	</div>
</section>


<?php get_footer(); ?>
