<?php
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style('header-footer', get_stylesheet_directory_uri() . '/css/header-footer.css', [], '1.0.0', 'all');
  wp_enqueue_style('global', get_stylesheet_directory_uri() . '/css/global.css', [], time(), 'all');
  wp_enqueue_style( 'material-symbols-outlined', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined', [], null, 'all');
  if (is_front_page()) {
    wp_enqueue_style('swiper', get_stylesheet_directory_uri() . '/css/swiper-bundle.min.css', [], '11.0.5', 'all');
    wp_enqueue_style('feedback', get_stylesheet_directory_uri() . '/css/feedback.css', [], '1.0.0', 'all');
    wp_enqueue_style('homepage', get_stylesheet_directory_uri() . '/css/homepage.css', [], time(), 'all'); // 0.0.5
    wp_enqueue_style('booking-form', get_stylesheet_directory_uri() . '/css/booking-form.css', [], '0.0.1', 'all');
  }
  if (is_product()) {
    wp_enqueue_style('swiper', get_stylesheet_directory_uri() . '/css/swiper-bundle.min.css', [], '11.0.5', 'all');
    wp_enqueue_style('single-product', get_stylesheet_directory_uri() . '/css/single-product.css', [], '1.0.0', 'all');
  }
  if (is_page('contact-us')) {
    wp_enqueue_style('contactpage', get_stylesheet_directory_uri() . '/css/contactpage.css', [], '1.0.0', 'all');
  }
  if (is_checkout() || is_cart()) {
    wp_enqueue_style('cart-checkout', get_stylesheet_directory_uri() . '/css/cart-checkout.css', [], '1.0.0', 'all');
  }
  if (is_single() && !is_product()) {
    wp_enqueue_style('singlepost', get_stylesheet_directory_uri() . '/css/singlepost.css', [], '1.0.0', 'all');
  }
  if(is_product_category() || is_shop() || is_product_tag() || is_archive() || is_single() || is_search() || is_home() || is_page('contact-us')) {
    wp_enqueue_style('archive-product-page', get_stylesheet_directory_uri() . '/css/archive-product-page.css', [], time(), 'all');
  }
});