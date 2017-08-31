<?php 
defined( 'ABSPATH' ) || exit;
return array (
  'homepage' => 'http://localhost/sos3/ubc',
  'cache_options' => 
  array (
    'breeze-active' => '1',
    'breeze-ttl' => 1440,
    'breeze-minify-html' => '1',
    'breeze-minify-css' => '1',
    'breeze-minify-js' => '1',
    'breeze-gzip-compression' => '1',
    'breeze-browser-cache' => '1',
    'breeze-desktop-cache' => 1,
    'breeze-mobile-cache' => 1,
    'breeze-disable-admin' => '1',
    'breeze-display-clean' => '1',
  ),
  'disable_per_adminuser' => '1',
  'exclude_url' => 
  array (
    0 => '/sos3/ubc/checkout/(.*)',
    1 => '/sos3/ubc/cart/',
    2 => '/sos3/ubc/my-account/(.*)',
  ),
); 
