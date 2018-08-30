<?php
/**
 * The template for displaying all single posts.
 *
 * @package sos-primary
 */

get_header();
?>

<?php
   $container   = get_theme_mod( 'understrap_container_type' );
   $sidebar_pos = get_theme_mod( 'understrap_sidebar_position' );
?>

<div class="hero">
   <?php the_title( sprintf( '<h2 class="archive-entry-title"><a href="%s" rel="bookmark">', esc_url( $application_url ) ),
   '</a></h2>' ); ?>
</div>

<?php
   $argument= $_GET['argument1'];

   $currentblog = get_current_blog_id();
   switch_to_blog(1);

   if ($argument) {
      $args = (array(
         'post_type'       => 'trips',
         'post__in' => array($argument),
         'meta_query' => array(
            'relation' => "AND",
            array(
               'key' => 'trip_schools',
               'value' => '"'.$currentblog.'"',
               'compare' => 'LIKE'
            ),
            array(
               'key' => 'trip_close_toggle',
               'value' => '1',
               'compare' => '!='
            )
         )
      ));
   } else {
      $args = (array(
         'post_type'       => 'trips',
         'meta_query' => array(
            'relation' => "AND",
            array(
               'key' => 'trip_schools',
               'value' => '"'.$currentblog.'"',
               'compare' => 'LIKE'
            ),
            array(
               'key' => 'trip_close_toggle',
               'value' => '1',
               'compare' => '!='
            )
         )
      ));
   }
   $trips = new WP_Query($args);
?>

<?php if ( $trips->have_posts() ) : ?>
   <?php $trips->the_post(); ?>
   <article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
      <div class="wrapper" style="margin: 0px 50px 0px 50px;" id="single-wrapper">
	       <div class="row"> <!--principal-->
            <div class="col-12 col-md-10" id="primary"> <!-- trip colunm -->
				       <header class="archive-entry-header" style="text-align: center;">
					        <?php the_title( sprintf( '<h2 class="archive-entry-title"><a href="%s" rel="bookmark">', esc_url( $application_url ) ),
                  '</a></h2>' ); ?>
						      <p><a class="btn btn-primary" href="<?php echo $application_url ?>" >Apply</a></p>
					     </header><!-- .archive-entry-header -->

               <?php the_field('trip_partners');?>
               <div class="row"> <!-- trip information -->
						      <div class="col-12 col-md-3"> <!-- trip details -->
						         <div class="tripInfo">
					              <?php
					                 echo "<strong>Cost:</strong> $" . get_field('trip_total_cost', get_the_ID());
					                 echo '<br>';
					                 echo "<strong>Departure City:</strong> " . get_field('trip_departure_city', get_the_ID());
					                 echo '<br>';
					                 echo "<strong>Departure Date:</strong><br> " . get_field('trip_departure_date', get_the_ID());
					                 echo '<br>';
					                 echo "<strong>Return Date:</strong><br> " . get_field('trip_return_date', get_the_ID());
					              ?>
					           </div> <!-- tripInfo -->
							       <div class="tripMap ml-5 ml-md-0">
	    				          <?php
	    					           $image = get_field('trip_map');
	    					           if( $image ) {
	    					           echo wp_get_attachment_image( $image ); }
	 				              ?>
	    		           </div> <!-- tripmap -->
						      </div> <!-- trip details -->
						      <div class="col-12 col-md-9"> <!-- trip content -->
							       <?php
							          $content = get_post_field('post_content', $p->ID);
							          echo $content;
							       ?>
						      </div> <!-- trip content -->
               </div> <!-- trip information -->
               <div class="col-12"> <!-- trip recursivetab -->
	                <?php the_field('trips_tab'); ?>
               </div> <!-- trip recursivetab -->
    	         <div class="icons"> <!-- trip whats included -->
						      <h4 style="text-align: center; margin-top:40px;"><strong>What's Included?</strong></h4>
							    <div class="row"> <!-- trip icons -->
							       <div class="col-6 col-md-6"><img class="ml-3 ml-md-10" src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/2018/07/09132716/home.png"><p>SOS volunteers sleep in available community structures (classrooms, community centers, churches), and live as close to the conditions of the community as possible. </p></div>
							  	   <div class="col-6 col-md-6"><img class="ml-3 ml-md-10" src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/2018/07/09132717/transportation.png"><p>SOS works directly with Flight Centre to process all volunteers' Outreach Trip logistics from your travel insurance to your in country needs like clean drinking water and accommodations!</p></div>
							  	   <div class="col-6 col-md-6"><img class="ml-3 ml-md-10" src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/2018/07/09132715/food.png"><p>Three meals a day plus clean drinking water and snacks! Meals will consist of rice, beans, tortillas, and fruit/vegetables. Soups, stews, and pasta dishes are common as well. Most dietary restrictions and allergies can accommodated. </p></div>
							  	   <div class="col-6 col-md-6"><img class="ml-3 ml-md-10" src="https://s3-ca-central-1.amazonaws.com/sos.uploads/wp-content/uploads/2018/07/09132716/support.png"><p>SOS works exclusively with registered non governmental organizations in every community to ensure our volunteers have the best experience possible.</p></div>
							    </div> <!-- trip icons -->
							    <div style="text-align: center;">
							       <p><a class="btn btn-primary" href="<?php echo $application_url ?>">Apply</a></p>
							    </div>
						   </div> <!-- trip whats included -->
				    </div> <!-- trip colunm -->
				    <div class="col-12 col-md-2" id="primary"> <!-- all trips left sidebar-->
				       <div id="left-sidebar">
                  <?php
                     restore_current_blog();
                     wp_reset_postdata();
                  ?>
                  <h5 class="widget-title" style="text-align: center;">All <?php echo bloginfo('name') ?> SOS Outreach Trips</h5>
                  <?php
                     $currentblog = get_current_blog_id();
                     switch_to_blog(1);
                     $args = (array(
                        'post_type'       => 'trips',
                        'meta_query' => array(
                           'relation' => "AND",
                           array(
                              'key' => 'trip_schools',
                              'value' => '"'.$currentblog.'"',
                              'compare' => 'LIKE'
                           ),
                           array(
                              'key' => 'trip_close_toggle',
                              'value' => '1',
                              'compare' => '!='
                           )
                        )
                     ));
                  ?>
                  <?php
                     $trips = new WP_Query($args);
                     restore_current_blog();
                  ?>
                  <?php
	 					         while ( $trips->have_posts() ) : $trips->the_post();
							    ?>
	 					         <a href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
	 					            <div class="card mb-2">
	 					               <div class="card-body">
	 					  	              <p class="card-title h6"><?php the_title(); ?></p>
                              <p><a class="btn btn-primary" href="<?php echo get_site_url() .'/trips?argument1=' ?><?php echo get_the_ID(); ?>">More Info</a></p>
	 					               </div>
	 					            </div>
	 					         </a>
	 					        <?php endwhile; ?>
	 				     </div> <!-- left sidebar -->
	          </div> <!-- all trips left sidebar -->
            <?php wp_reset_postdata(); ?>

            <?php if ( 'right' === $sidebar_pos || 'both' === $sidebar_pos ) : ?>
       		  <?php get_sidebar( 'right' ); ?>
        	  <?php endif; ?>

         </div> <!--row principal-->
      </div><!-- Container end -->
   </article>

<?php else : ?>
   <div class="container">
      </br>
      </br>
      <header class="archive-entry-header" style="text-align: center;">
			   <h2 class="archive-entry-title"> All our Outreach Trips for your Chapter are now closed. </h2>
			</header><!-- .archive-entry-header -->
      </br>
      </br>
   </div>
<?php endif; ?>



<div class="hero-footer">
   <h4>Supporting a Great Cause</h4>
   <p>Since 2004, we have been working to support the universal right to education through funding of sustainable international projects.</p>
	 <p><a class="btn btn-primary" href="http://sosvolunteertrips.org/">Learn More</a></p>
</div>

<?php get_footer(); ?>
