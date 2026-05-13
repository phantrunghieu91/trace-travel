<?php
add_action('wp_enqueue_scripts', function () {
  if (is_front_page()) {
    wp_enqueue_script('swiper', get_stylesheet_directory_uri() . '/js/swiper-bundle.min.js', array ('jquery'), '11.0.5', true);
    wp_enqueue_script('homepage', get_stylesheet_directory_uri() . '/js/homepage.js', array ('jquery'), '0.0.0', true);
  }
  if (is_product()) {
    wp_enqueue_script('swiper', get_stylesheet_directory_uri() . '/js/swiper-bundle.min.js', array ('jquery'), '11.0.5', true);
    wp_enqueue_script('single-product', get_stylesheet_directory_uri() . '/js/single-product.js', array ('jquery'), rand(111, 9999), true);
  }
  if (is_product() || is_front_page() || is_product_category() || is_shop() || is_product_tag() || is_archive() || is_single() || is_search() || is_home() || is_page('contact-us')) {
    wp_enqueue_script('booking-form', get_stylesheet_directory_uri() . '/js/booking-form.js', ['jquery'], '0.0.22', true);
    // Register for booking-form so that can use api_data call
    wp_localize_script( 'booking-form', 'api_data', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'get_services_nonce' => wp_create_nonce('get_services'),
      ]
    );
  }
  if (is_checkout()) {
    wp_enqueue_script('checkout-page', get_stylesheet_directory_uri() . '/js/checkout-page.js', array ('jquery'), rand(111, 9999), true);
  }
});