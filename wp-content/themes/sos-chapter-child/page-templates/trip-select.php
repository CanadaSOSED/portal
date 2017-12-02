<?php
/**
 * Template Name: Trip Select
 *
 * Template for displaying a page with login form
 *
 * @package sos-chapter
 */

get_header(); ?>

<div class="container">
  <div class="row d-flex justify-content-center text-center p-3 p-md-3">
    <div class="col-12 my-6">
      <h1 class="h1 animated fadeInDown" >Join us on one of our Outreach Trips!</h1>
      <div class="lead animated fadeIn">
        <p>We focus our work in communities across 8 different countries in Latin America. There is a huge need for projects within the region which means we can make a genuine and lasting impact.</p>
      <!-- <p class="animated fadeIn"></p> -->
      <select id="chapters-list" class="custom-select" onchange="document.location.href=this.options[this.selectedIndex].value;">
      <option> Select Your Campus </option>
          <?php sos_chapters_list_option_box(); ?>
      </select>
      
      <a class="btn btn-primary btn-lg mx-auto my-3 d-block animated fadeIn" style="width: 200px;" href="<?php bloginfo('site_url'); ?>/trips" role="button">Find a Trip</a>
    </div>
    </div>
  </div>
</div>

<?php
get_footer();
?>