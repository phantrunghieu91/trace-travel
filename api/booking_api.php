<?php
// Return services (product) related to a pick up (category)
add_action('wp_ajax_get_services', 'get_services');
add_action('wp_ajax_nopriv_get_services', 'get_services');
function get_services(){
  // Check nonce
  $nonce = isset($_GET['nonce']) ? sanitize_text_field($_GET['nonce']) : '';
  if (!wp_verify_nonce($nonce, 'get_services')) {
    wp_send_json_error("Invalid nonce: $nonce", 401);
  }

  $pick_up_cat_id = isset($_GET['cat_id']) ? $_GET['cat_id'] : false;
  if (!$pick_up_cat_id) {
    wp_send_json_error('Invalid pick up category id!', 404);
  }

  $services = get_posts(
    [
      'post_type' => 'product',
      'posts_per_page' => -1,
      'post_status' => 'publish',
      'tax_query' => [
        [
          'taxonomy' => 'product_cat',
          'field' => 'term_id',
          'terms' => $pick_up_cat_id
        ]
      ]
    ]
  );

  if (!$services) {
    wp_send_json_error("No services found!, cat_id: $pick_up_cat_id");
  }

  $services_data = [];
  foreach ($services as $service) {
    $service_data = wc_get_product($service->ID);
    $services_data[] = [
      'id' => $service->ID,
      'name' => $service_data->name,
    ];
  }

  wp_send_json_success($services_data);
}

// RETURN TRIP DATA TO FRONT END
add_action('wp_ajax_get_trip_data', 'get_trip_data');
add_action('wp_ajax_nopriv_get_trip_data', 'get_trip_data');

function get_trip_data()
{
  $product_id = isset($_GET['product_id']) ? sanitize_text_field($_GET['product_id']) : false;
  if ($product_id) {
    $product = wc_get_product($product_id);
    if ($product) {
      $variations = [];
      $add_ons = [];
      foreach ($product->get_available_variations() as $variation) {
        $term = get_term_by('slug', reset($variation['attributes']), array_keys($product->get_attributes())[0]);

        $variations[] = array(
          'id' => $variation['variation_id'],
          'price' => $variation['display_price'],
          'slug' => reset($variation['attributes']),
          'name' => $term->name
        );
      }
      for ($i = 0; $i < $product->get_meta('add_on'); $i++) {
        if (empty($product->get_meta('add_on_' . $i . '_fixed_price'))) {
          $add_ons[] = [
            'multi_price' => true,
            'name' => $product->get_meta('add_on_' . $i . '_name'),
            'prices' => [
              'sedan' => $product->get_meta('add_on_' . $i . '_prices_sedan'),
              'suv' => $product->get_meta('add_on_' . $i . '_prices_suv'),
              'van' => $product->get_meta('add_on_' . $i . '_prices_van'),
            ]
          ];
        } else {
          $add_ons[] = [
            'multi_price' => false,
            'name' => $product->get_meta('add_on_' . $i . '_name'),
            'price' => $product->get_meta('add_on_' . $i . '_fixed_price'),
          ];
        }
      }
      $data_to_send = array(
        'attributes_slug' => array_keys($product->get_attributes())[0],
        'id' => $product->id,
        'name' => $product->name,
        'slug' => $product->slug,
        'description' => $product->description,
        'categories' => $product->categories,
        'price' => $product->price,
        'short_description' => $product->short_description,
        'tags' => $product->tags,
        'add_ons' => $add_ons,
        'variations' => $variations,
        // 'meta_data' => $product->get_meta_data(),
      );
      wp_send_json($data_to_send);
    } else {
      wp_send_json_error('Can\'t find product!', 505);
    }
  } else
    wp_send_json_error('Wrong product id!', 505);
  wp_die();
}

// HANDLE ADD TO CART
add_action('wp_ajax_handle_booking', 'handle_booking');
add_action('wp_ajax_nopriv_handle_booking', 'handle_booking');
function handle_booking()
{
  // Verify nonce (recommended for security)
  $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
  if (!wp_verify_nonce($nonce, 'handling_booking_nonce')) {
    wp_send_json_error('Invalid nonce: ' . $nonce, 401);
  }

  // Get JSON data sent from frontend
  $json_data = isset($_POST['bookingData']) ? ($_POST['bookingData']) : false;

  // Check if JSON data is valid
  if (!$json_data) {
    wp_send_json_error('Invalid JSON data: ' . $nonce . ' .Data: ' . $json_data, 404);
    wp_die();
  }

  // Process JSON data
  $product_id = isset($json_data['product_id']) ? sanitize_text_field($json_data['product_id']) : '';
  $variation_id = isset($json_data['variation_id']) ? sanitize_text_field($json_data['variation_id']) : '';
  $total = isset($json_data['total_price']) ? sanitize_text_field($json_data['total_price']) : 0;

  // Add product to cart
  if (!empty(WC()->cart->get_cart())) {
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
      if ((empty($cart_item['variation_id']) && $cart_item['product_id'] == $product_id) || $cart_item['variation_id'] == $variation_id) {
        wp_send_json_error('Already booking that trip!', 400);
        wp_die();
      }
    }
  }

  WC()->cart->add_to_cart($product_id, 1, $variation_id);
  // Adjust cart item price before calculate total

  foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
    $cart_item['data']->set_price($total);
  }

  WC()->cart->calculate_totals();
  wp_send_json('Booked!');
  wp_die();
}

// Add custom data into cart item
add_filter('woocommerce_add_cart_item_data', function ($cart_item_data, $product_id, $variation_id) {
  $booking_data = isset($_POST['bookingData']) ? ($_POST['bookingData']) : false;
  $cart_item_data['booking_additional_info'] = $booking_data;
  return $cart_item_data;
}, 10, 3);

// Change display in cart and checkout with additional data
add_filter('woocommerce_get_item_data', function ($item_data, $cart_item) {
  if (empty($cart_item['booking_additional_info']))
    return $item_data;

  foreach ($cart_item['booking_additional_info']['add_on_values'] as $field) {
    $item_data[] = [
      'key' => $field['name'],
      'value' => wc_price($field['price'])
    ];
  }
  $item_data[] = ['key' => 'Departure time', 'value' => $cart_item['booking_additional_info']['departure_time']];
  $item_data[] = ['key' => 'Departure date', 'value' => $cart_item['booking_additional_info']['departure_date']];

  return $item_data;
}, 10, 2);

// Add addon total display in cart and checkout
add_action('woocommerce_cart_calculate_fees', function ($cart) {
  // $timeRangeStart = DateTime::createFromFormat('H:i', '22:00');
  // $timeRangeEnd = DateTime::createFromFormat('H:i', '06:00')->modify('+1 day');
  $holidays = get_field('holidays', 'option');
  foreach ($cart->get_cart() as $cart_item) {
    $depart_time = DateTime::createFromFormat('H:i', $cart_item['booking_additional_info']['departure_time']);
    $depart_date = DateTime::createFromFormat('Y-m-d', $cart_item['booking_additional_info']['departure_date']);
    $depart_date->format('d/m/Y');
    $add_ons = $cart_item['booking_additional_info']['add_on_values'];
    if (!empty($add_ons)) {
      foreach ($add_ons as $add_on) {
        $cart->add_fee(__($add_on['name'], 'woocommerce'), $add_on['price'], true, '');
      }
    }
    // Calculate night time fee (After 10pm to 6am)
    // if ($depart_time >= $timeRangeStart || $depart_time <= $timeRangeEnd) {
    //   $fee = $cart->get_cart_contents_total() * .1;
    //   $cart->add_fee(__('Nighttime fee (10%)', 'Phí chạy đêm (10%)'), $fee, true, '');
    // }
    // Check if depart date is in holiday or not, calculate fee
    if (!empty($holidays)) {
      foreach ($holidays as $holiday) {
        $holiday_start = DateTime::createFromFormat('d/m/Y', $holiday['start_date']);
        $holiday_end = DateTime::createFromFormat('d/m/Y', $holiday['end_date']);
        $holiday_name = $holiday['name'];
        $holiday_fee = $holiday['fee'];
        // Increment end date by 1 day to include the end date in the range
        if ($holiday_end)
          $holiday_end->modify('+1 day');
        if (($holiday_end && $depart_date >= $holiday_start && $depart_date < $holiday_end) || (!$holiday_end && $depart_date == $holiday_start)) {
          $fee = $cart->get_cart_contents_total() * $holiday_fee / 100;
          $cart->add_fee(__('Holiday fee ' . ($holiday_name ? ' - ' . $holiday_name : '') . ' (' . $holiday_fee . '%)', 'gpw' ));
          break;
        }
      }
    }
  }
}, 10, 1);

// add addon and departure date, time to order, this hook excute after user place order and before send data to database
add_action('woocommerce_checkout_create_order_line_item', function ($item, $cart_item_key, $values, $order) {
  if (empty($values['booking_additional_info']))
    return;
  $booking_data = $values['booking_additional_info'];
  $item->add_meta_data('Departure date', $booking_data['departure_date']);
  $item->add_meta_data('Departure time', $booking_data['departure_time']);
}, 20, 4);

// Add departure date and time to order
add_action('woocommerce_order_item_meta_start', function ($item_id, $item, $order, $plain_text) {
  $depart_date = $item->get_meta('departure_date');
  $depart_time = $item->get_meta('departure_time');
  if (empty($depart_date) && empty($depart_time))
    return;

  echo '<div class="departure-date-time">Date: ' . $depart_date . ', Time: ' . $depart_time . '</div>';
}, 20, 4);