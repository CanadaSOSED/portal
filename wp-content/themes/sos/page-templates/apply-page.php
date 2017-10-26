<?php
/**
 * Template Name: Apply Page - SOS Main
 *
 *
 * @package sos-primary
 */

get_header(); ?>

<section id="" class="bg bg-light pb-7">
    <div class="grad-overlay"></div>
    <div class="container">
        <div class="row d-flex justify-content-center text-center p-3 p-md-3">
            <div class="col-12 col-sm-8 my-6">
                <div class="card mt-3 p-3">
                    <div class="card-body">
                <h1 class="h1 animated fadeInDown">Apply To Be An SOS Student Leader!</h1>
                <div class="lead animated fadeIn">
                    <p>Gain valuable experience, create lasting friendships and improve your résumé!</p>
                </div>
                <p class="animated fadeIn">Check out the latest opportunities on your campus.</p>
                <select id="chapters-list" class="custom-select" onchange="document.location.href=this.options[this.selectedIndex].value;">
                <option> Select Your Campus </option>
                    <?php sos_chapters_list_apply_option_box(); ?>
                </select>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
