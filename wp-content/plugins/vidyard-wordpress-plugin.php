<?php
   /*
   Plugin Name: Vidyard Wordpress Plugin
   Plugin URI: http://vidyard.com
   Description: Makes it easy to embed your Vidyard videos into WordPress.
   Version: 1.0
   Author: Vidyard
   Author URI: http://vidyard.com
   License: MIT
   */

  wp_oembed_add_provider(
    '#https?://embed\.vidyard\.com/share/.*#i',
    'https://api.vidyard.com/dashboard/v1/oembed',
    true
  );

  wp_oembed_add_provider(
    '#https?://play\.vidyard\.com/.*#i',
    'https://api.vidyard.com/dashboard/v1/oembed',
    true
  );

  wp_oembed_add_provider(
    '#https?://.*\.hubs\.vidyard\.com/.*#i',
    'https://api.vidyard.com/dashboard/v1/oembed',
    true
  );

?>
