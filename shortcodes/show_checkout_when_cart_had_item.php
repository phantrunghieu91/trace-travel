<?php
add_shortcode('show_checkout_when_cart_had_item', function () {
  try {
    if (!WC()->cart->is_empty()) {
      return do_shortcode('[link_to slug="checkout"]Checkout[/link_to]');
    }
  } catch (Exception $e) {
    return 'Error in show check out when cart had item shortcode: ' . $e->getMessage();
  }
});