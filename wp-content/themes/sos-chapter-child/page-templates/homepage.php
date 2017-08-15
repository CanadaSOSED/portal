<?php
/**
 * Template Name: Home Page - Chapter
 *
 * This template can be used to override the default template and sidebar setup
 *
 * @package sos-chapter
 */

get_header();
$container = get_theme_mod( 'understrap_container_type' );
?>

<div class="wrapper">
	<section id="home-hero">
		<div class="overlay"></div>
	     <div class="page-header page-header-small">
	         <div class="container">
	     		<div class="row">
	     			<div class="col-md-6">
	     				<div class="embed-container"><iframe src='http://www.youtube.com/embed/b3A9tkKw43A' frameborder='0' allowfullscreen></iframe></div>
	     			</div>
	     			<div class="col-md-6">
	         			<h1 class="title">Get Help With Your Courses</h1>
	         			<div class="lead">
	         				<p>Raise your marks with our student volunteer Exam-AID review sessions!</p>
	         			</div>
	         			<a class="btn btn-lg btn-info" href="#">FIND YOUR SCHOOL</a>
	     			</div>
	     		</div>
	         </div>
	     </div>
     </section>
     <section id="sos-mission">
     	<div class="container">
     	<div class="row">
     	                 <div class="col-md-8 offset-md-2 text-center">
     	                     <h2 class="title mb-5">Our Mission</h2>
     	                 </div>
     	             </div>
     		<div class="row">
     			<div class="col-xs-12 col-md-6">
     					<div class="card text-center">
     						<div class="card-block">
     							<h4 class="card-title">Raising Marks</h4>
     							<h6 class="card-subtitle mb-2 text-muted">Exam-AID Sessions</h6>
     							<p class="card-text">Volunteers manage SOS campus chapters as social enterprises,&nbsp;planning and running “Exam-AID” group review sessions. During our 3-4 hour Exam-AID sessions, volunteer leaders take their peers over all the material covered on an upcoming exam.<br></p>
     						</div>
     					</div>

     			</div>
     			<div class="col-xs-12 col-md-6">
     					<div class="card text-center">
     						<div class="card-block">
     							<h4 class="card-title">Raising Money</h4>
     							<h6 class="card-subtitle mb-2 text-muted">Exam-AID Donations</h6>
     							<p class="card-text">SOS asks for a $10-$20 donation to attend an Exam-AID session. The money generated from these sessions is used to fund education development projects in rural Latin&nbsp;America.<br></p>

     						</div>
     					</div>
     			</div>
     		</div>
     		<div class="row">
     		<div class="col-xs-12 col-md-12">
     					<div class="card text-center">
     						<div class="card-block">
     							<h4 class="card-title">Raising Roofs</h4>
     							<h6 class="card-subtitle mb-2 text-muted">Latin America Homes</h6>
     							<p class="card-text">These projects are not just funded by, but also built by volunteers on outreach trips. Each May &amp; August, volunteers spend two weeks building the project their SOS chapter funded over the year, exposing them to a whole new culture and to the impact of their time and efforts.</p>

     						</div>
     					</div>
     			</div>
     		</div>
     	</div>
     </section>
     <div class="section section-about-us">
         <div class="container">
             <div class="row">
                 <div class="col-md-8 offset-md-2 text-center">
                     <h2 class="title">SOS on Campus and Exam Aid Sessions</h2>
                     <h5 class="description">Exam Aids are three hour group review sessions that occur prior to a midterm or final test. Each session is taught by a student volunteer with exceptional communication skills, who has previously excelled in that particular course.</h5>
                 </div>
             </div>
             <div class="separator separator-primary"></div>
             <div class="section-story-overview">
                 <div class="row">
                     <div class="col-md-6">
                         <div class="image-container image-left">
                             <!-- First image on the left side -->
                             <img src="<?php bloginfo( 'stylesheet_directory' ); ?>/assets/img/now-ui/writing.jpg" alt="" class="rounded img-fluid img-raised">
                             <p class="blockquote blockquote-primary w-3">"Having participated in two outreach trips with SOS, I can attest to the positive difference the organization is making. I've established lasting and unique relationships and can genuinely say that the memories will last a lifetime."
                                 <br>
                                 <br>
                                 <small>-  <b>Joey Hutter, University of Calgary</b><br/>
											<i>Honduras 2013, Guatemala 2014</i>
								 </small>
                             </p>
                         </div>
                         <!-- Second image on the left side of the article -->
                         <div class="image-container">
                             <img src="<?php bloginfo( 'stylesheet_directory' ); ?>/assets/img/joel.jpg" alt="" class="img-fluid rounded img-raised">
                         </div>
                     </div>
                     <div class="col-md-5">
                         <!-- First image on the right side, above the article -->
                         <div class="image-container image-right">
                             <img src="<?php bloginfo( 'stylesheet_directory' ); ?>/assets/img/exam-aid-1.jpg" alt="" class="rounded img-fluid img-raised">
                         </div>
                         <h3>Get Involved. Make An Impact. Travel Abroad.</h3>
                         <p>Proceeds from Exam Aid sessions go towards sustainable development projects. Volunteers also have the opportunity to bring their campus-funded project to life through hands on involvement. Whether it’s a classroom, library, computer lab or school kitchen, our volunteers help build it from the ground up during two week trips to our partner communities in Central and South America. 
                         </p>
                         <p>
                             These trips foster communities of active and aware citizens through an immersive experience of cultural exchange.
                         </p>
                         <p>Trips cost under $2,500 (including accommodations, flights, in country transportation, insurance, meals, and support from SOS and our partnering organizations). We’ve coordinated over 1500 students to travel to partner communities in Central and South America. Join us this May or August to become part of our network of global citizens!
                         </p>
                     </div>
                 </div>
             </div>
         </div>
     </div>

<!--      <div class="section section-team text-center ">
         <div class="container">
             <h2 class="title">Here is our team</h2>
             <div class="team">
                 <div class="row">
                     <div class="col-md-4">
                         <div class="team-player">
                             <img src="<?php bloginfo( 'stylesheet_directory' ); ?>/assets/img/now-ui/avatar.jpg" alt="Thumbnail Image" class="rounded-circle img-fluid img-raised">
                             <h4 class="title">Romina Hadid</h4>
                             <p class="category text-primary">Model</p>
                             <p class="description">You can write here details about one of your team members. You can give more details about what they do. Feel free to add some
                                 <a href="#">links</a> for people to be able to follow them outside the site.</p>
                             <a href="#pablo" class="btn btn-primary btn-icon btn-icon-mini"><i class="fa fa-twitter"></i></a>
                             <a href="#pablo" class="btn btn-primary btn-icon btn-icon-mini"><i class="fa fa-instagram"></i></a>
                             <a href="#pablo" class="btn btn-primary btn-icon btn-icon-mini"><i class="fa fa-facebook-square"></i></a>
                         </div>
                     </div>
                     <div class="col-md-4">
                         <div class="team-player">
                             <img src="<?php bloginfo( 'stylesheet_directory' ); ?>/assets/img/now-ui/ryan.jpg" alt="Thumbnail Image" class="rounded-circle img-fluid img-raised">
                             <h4 class="title">Ryan Tompson</h4>
                             <p class="category text-primary">Designer</p>
                             <p class="description">You can write here details about one of your team members. You can give more details about what they do. Feel free to add some
                                 <a href="#">links</a> for people to be able to follow them outside the site.</p>
                             <a href="#pablo" class="btn btn-primary btn-icon btn-icon-mini"><i class="fa fa-twitter"></i></a>
                             <a href="#pablo" class="btn btn-primary btn-icon btn-icon-mini"><i class="fa fa-linkedin"></i></a>
                         </div>
                     </div>
                     <div class="col-md-4">
                         <div class="team-player">
                             <img src="<?php bloginfo( 'stylesheet_directory' ); ?>/assets/img/now-ui/eva.jpg" alt="Thumbnail Image" class="rounded-circle img-fluid img-raised">
                             <h4 class="title">Eva Jenner</h4>
                             <p class="category text-primary">Fashion</p>
                             <p class="description">You can write here details about one of your team members. You can give more details about what they do. Feel free to add some
                                 <a href="#">links</a> for people to be able to follow them outside the site.</p>
                             <a href="#pablo" class="btn btn-primary btn-icon btn-icon-mini"><i class="fa fa-google-plus"></i></a>
                             <a href="#pablo" class="btn btn-primary btn-icon btn-icon-mini"><i class="fa fa-youtube-play"></i></a>
                             <a href="#pablo" class="btn btn-primary btn-icon btn-icon-mini"><i class="fa fa-twitter"></i></a>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
     <div class="section section-contact-us text-center">
         <div class="container">
             <h2 class="title">Want to work with us?</h2>
             <p class="description">Your project is very important to us.</p>
             <div class="row">
                 <div class="col-lg-6 text-center offset-lg-3 col-md-8 offset-md-2">
                     <div class="input-group form-group-no-border input-lg">
                         <span class="input-group-addon">
                             <i class="now-ui-icons users_circle-08"></i>
                         </span>
                         <input type="text" class="form-control" placeholder="First Name...">
                     </div>
                     <div class="input-group form-group-no-border input-lg">
                         <span class="input-group-addon">
                             <i class="now-ui-icons ui-1_email-85"></i>
                         </span>
                         <input type="text" class="form-control" placeholder="Email...">
                     </div>
                     <div class="textarea-container">
                         <textarea class="form-control" name="name" rows="4" cols="80" placeholder="Type a message..."></textarea>
                     </div>
                     <div class="send-button">
                         <a href="#pablo" class="btn btn-info btn-round btn-block btn-lg">Send Message</a>
                     </div>
                 </div>
             </div>
         </div>
     </div> -->
 </div>

 <section class="pre-footer-cta">
 	<div class="container">
 		<div class="row">
 			<div class="col-md-4">
 				<div class="text-xs-center"><a class="btn btn-lg btn-info" href="#">FIND YOUR SCHOOL</a></div>
 			</div>
 			<div class="col-md-8 text-xs-center text-md-left">
 				<h1 class="title">GET STARTED TODAY</h3>
 				<p class="lead">You can be awesome too! Just do some cool SOS stuff like take an Exam Aid and you'll be totally Kick ASS!!!</h2>
 			</div>
 		</div>
 	</div>
 </section>

<?php get_footer(); ?>
