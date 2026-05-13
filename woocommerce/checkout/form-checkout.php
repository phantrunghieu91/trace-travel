<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if (!defined('ABSPATH')) {
  exit;
}

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
  echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
  return;
}

?>

<form name="checkout" method="post" class="form-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>"
  enctype="multipart/form-data">

  <?php if ($checkout->get_checkout_fields()): ?>

    <?php do_action('woocommerce_checkout_before_customer_details'); ?>

    <div class="customer-details" id="customer_details">
      <div class="customer-details__pickup">
        <h3><?= esc_html_e('Pickup location', 'Địa chỉ đón'); ?></h3>
        <?php $address_field = $checkout->get_checkout_fields()['billing']['billing_address_1']; ?>
        <input type="text" name="billing_address_1" id="billing_address_1" class="pickup__address"
          <?= $address_field['required'] ? 'required' : '' ?> autocomplete="<?= $address_field['autocomplete'] ?>"
          placeholder="Please fill in your pick up location. Ex. Sunsilk Hotel - Hoi An"
          value="<?= $checkout->get_value('billing_address_1') ?>">
      </div>
      <div class="customer-details__billing">
        <?php do_action('woocommerce_checkout_billing'); ?>
      </div>

      <div class="customer-details__shipping">
        <?php do_action('woocommerce_checkout_shipping'); ?>
      </div>
    </div>
    <div class="checkout__hidden-input" style="display: none;">
      <?php do_action('woocommerce_checkout_after_customer_details'); ?>
    </div>

  <?php endif; ?>

  <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>

  <div class="order-review">
    <h3 class="order-review__title"><?php esc_html_e('Your order', 'woocommerce'); ?></h3>
    <?php do_action('woocommerce_checkout_before_order_review'); ?>
    <div id="order_review" class="order-review__content">
      <?php do_action('woocommerce_checkout_order_review'); ?>
    </div>
    <?php do_action('woocommerce_checkout_after_order_review'); ?>
  </div>

</form>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>