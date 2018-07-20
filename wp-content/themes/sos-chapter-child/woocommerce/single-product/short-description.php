<?php
/**
 * Single product short description
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/short-description.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

if ( ! $post->post_excerpt ) {
	return;
}

?>
<div class="row">
	<div class="col-12 col-md-8" "woocommerce-product-details__short-description">
			<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>
	</div>


	<div class="col-12 col-md-4">
		<p class="price">
			<?php global $product;
      echo "<strong>Price:</strong> " . $product->get_price_html(); ?>
	  </p>
	  <?php
		echo "<strong>Location:</strong> " . get_field('session_location', get_the_ID());
		echo '<br>';
		echo "<strong>Date:</strong> " . get_field('session_date', get_the_ID());
		echo '<br>';
		echo "<strong>Time:</strong> " . get_field('session_time', get_the_ID());
		echo '<br>';
		global $session_fb;
		$session_fb = get_field('session_fb_event', get_the_ID());
		echo "<strong>Facebook event:</strong> <br>" ;
		?>
		<a href="<?php echo $session_fb; ?>" target="_blank"><?php echo $session_fb; ?> </a>
		<?php
		echo '<br><br><br>';
		?>

	</div>
</div>




	<!--<div class="woocommerce-product-details__short-description">
	    // <?php
			//echo apply_filters( 'woocommerce_short_description', $post->post_excerpt );
			//?>
	</div>
	-->
