<?php
add_action('wp_ajax_get_trip_data', 'get_trip_data');
add_action('wp_ajax_nopriv_get_trip_data', 'get_trip_data');

function get_trip_data()
{
  $product_id = isset($_GET['product_id']) ? sanitize_text_field($_GET['product_id']) : false;
  if ($product_id) {
    $product = wc_get_product($product_id);
    if ($product) {
      $variations = array();
      $add_ons = array();
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
        $name = $product->get_meta('add_on_' . $i . '_name');
        $price = $product->get_meta('add_on_' . $i . '_fixed_price');
        $add_ons[] = ['name' => $name, 'price' => $price, 'slug' => 'add_on_' . $i];
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
        'meta_data' => $product->get_meta_data(),
      );
      wp_send_json($data_to_send);
    } else {
      wp_send_json_error('Can\'t find product!', 505);
    }
  } else
    wp_send_json_error('Wrong product id!', 505);
  wp_die();
}