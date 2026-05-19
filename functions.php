<?php
// Turn off auto gen <p> of contact form 7
add_filter('wpcf7_autop_or_not', '__return_false');

// add dashicons to normal page too
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style('dashicons');
});

// Include other php
include get_theme_file_path('shortcodes/register.php');
include get_theme_file_path('js/register.php');
include get_theme_file_path('css/register.php');
include get_theme_file_path('api/register.php');

// * Add socials media to footer
add_action('wp_footer', function () {
  echo do_shortcode('[socials_media]');
});

// ! WOOCOMMERCE SECTION
// remove validate of billing country and billing town
add_filter('woocommerce_default_address_fields', function ($address_fields) {
  // dd($address_fields);
  $address_fields['country']['required'] = false;
  $address_fields['city']['required'] = false;
  return $address_fields;
});

// Custom plugin to modify formatted billing address
add_filter('woocommerce_order_get_formatted_billing_address', function ($address, $order) {
  $formatted_address = '';
  $formatted_address .= 'Full name: ' . $order['first_name'] . ' ' . $order['last_name'] . '<br>';
  $formatted_address .= 'Pickup location: ' . $order['address_1'];
  return $formatted_address;
}, 10, 2);

// Replace empty price text
function gpw_change_empty_price($price, $product)
{
  if ($price == '') {
    return __('Contact', 'gpw');
  }
  return $price;
}
add_filter('woocommerce_get_price_html', 'gpw_change_empty_price', 10, 2);