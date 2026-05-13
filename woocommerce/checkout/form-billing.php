<?php
/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined('ABSPATH') || exit;
?>
<?php if (wc_ship_to_billing_address_only() && WC()->cart->needs_shipping()): ?>

  <h3><?php esc_html_e('Billing &amp; Shipping', 'woocommerce'); ?></h3>

<?php else: ?>

  <h3><?php esc_html_e('Billing details', 'woocommerce'); ?></h3>

<?php endif; ?>
<div class="billing-fields">

  <?php do_action('woocommerce_before_checkout_billing_form', $checkout); ?>

  <?php
  $fields = $checkout->get_checkout_fields('billing');

  // Remove do NOT need fields
  $remove_fields = ['billing_country', 'billing_address_1', 'billing_postcode', 'billing_city', 'billing_state'];
  foreach ($remove_fields as $remove_field) {
    unset($fields[$remove_field]);
  }

  foreach ($fields as $key => $field): ?>
    <div class="billing-field__wrapper <?= $key ?>">
      <span
        class="billing-field__label"><?= $field['label'] ?><?= $field['required'] ? '<span class="required">*</span>' : '' ?></span>
      <input type="<?= isset($field['type']) ? esc_attr($field['type']) : 'text' ?>" name="<?= esc_attr($key) ?>"
        id="<?= esc_attr($key) ?>" <?= $field['required'] ? 'required' : '' ?>
        autocomplete="<?= isset($field['autocomplete']) ? esc_attr($field['autocomplete']) : '' ?>"
        value="<?= $checkout->get_value($key) ?>" <?= $key == 'billing_phone' ? 'pattern="[0-9]{10,12}"' : '' ?>>
    </div>
  <?php endforeach; ?>

  <?php do_action('woocommerce_after_checkout_billing_form', $checkout); ?>
</div>

<?php if (!is_user_logged_in() && $checkout->is_registration_enabled()): ?>
  <div class="woocommerce-account-fields">
    <?php if (!$checkout->is_registration_required()): ?>

      <p class="form-row form-row-wide create-account">
        <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
          <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" <?php checked((true === $checkout->get_value('createaccount') || (true === apply_filters('woocommerce_create_account_default_checked', false))), true); ?> type="checkbox"
            name="createaccount" value="1" /> <span><?php esc_html_e('Create an account?', 'woocommerce'); ?></span>
        </label>
      </p>

    <?php endif; ?>

    <?php do_action('woocommerce_before_checkout_registration_form', $checkout); ?>

    <?php if ($checkout->get_checkout_fields('account')): ?>

      <div class="create-account">
        <?php foreach ($checkout->get_checkout_fields('account') as $key => $field): ?>
          <?php woocommerce_form_field($key, $field, $checkout->get_value($key)); ?>
        <?php endforeach; ?>
        <div class="clear"></div>
      </div>

    <?php endif; ?>

    <?php do_action('woocommerce_after_checkout_registration_form', $checkout); ?>
  </div>
<?php endif; ?>