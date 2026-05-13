<?php
add_shortcode('booking_form', function () {
  try {
    $current_date = date('Y-m-d');
    $current_cat_id = null;
    $current_object = get_queried_object();
    if(is_a($current_object, 'WP_Term') && $current_object->taxonomy === 'product_cat') {
      $current_cat_id = get_queried_object_id();
    }
    $tax_query = $current_cat_id ? [
      [
        'taxonomy' => 'product_cat',
        'field' => 'term_id',
        'terms' => $current_cat_id
      ]
     ] : [];
    // Trip data
    $trips = get_posts(
      [
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'tax_query' => $tax_query
      ]
    );

    // * Pickup data ( Child categories of trip )
    $pick_ups = get_terms(
      [
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => get_term_by('slug', 'trip', 'product_cat')->term_id
      ]
    );

    $trip_input_name = 'attribute_pa_type-of-car';

    ob_start();
    ?>
    <form id="booking-form" method="POST" action="<?= home_url('/checkout', 'https') ?>">
      <?= wp_nonce_field('handling_booking_nonce', 'my_form_nonce') ?>
      <header class="booking-form__header">
        <span class="booking-form__title">Booking Form</span>
      </header>
      <main class="booking-form__content">
        
        <?php if(!$current_cat_id) : ?>
        <div class="booking-form__control-wrap">
          <label for="pick-up"><span class="dashicons dashicons-location-alt"></span> Pick up:</label>
          <select id="pick-up" name="pick-up" class="book-form__pick-up" required>
            <option value="default">Select where to pick up</option>
            <?php if( !empty($pick_ups) ): foreach($pick_ups as $pick_up): ?>
              <option value="<?= esc_attr($pick_up->term_id) ?>"><?= esc_html($pick_up->name) ?></option>
            <?php endforeach; endif; ?>
          </select>
        </div>
        <?php endif ?>

        <div class="booking-form__control-wrap">
          <label for="services"><span class="dashicons dashicons-list-view"></span> Services:</label>
          <select id="services" name="services" class="book-form__services" required>
            <option value="default">Select trip</option>
            
            <?php if( $current_cat_id ) : foreach($trips as $trip): ?>
            
              <option value="<?= esc_attr($trip->ID) ?>"><?= esc_html($trip->post_title) ?></option>

            <?php endforeach; endif; ?>

          </select>
        </div>
        <div class="booking-form__control-wrap"> 
          <label for="booking-date"><span class="dashicons dashicons-calendar-alt"></span> Select a date:</label>
          <input type="date" name="booking-date" id="booking-date" class="booking-form__booking-date" min="<?= $current_date ?>" required>
        </div>
        <div class="booking-form__control-wrap">
          <label for="booking-time"><span class="dashicons dashicons-clock"></span> Time to departure:</label>
          <input type="time" name="booking-time" id="booking-time" class="booking-form__booking-time" required>
        </div>
        <div class="booking-form__control-wrap">
          <label for="booking-car-type"><span class="dashicons dashicons-car"></span> Type of car:</label>
          <select id="booking-car-type" name="<?= $trip_input_name ?>" class="booking-form__car-type" required>
            <option value="default">Select type of car</option>
          </select>
        </div>
        <div class="booking-form__control-wrap">
          <div class="booking-form__control-wrap-title"><strong>Add on:</strong></div>
          <div class="booking-form__add-ons"></div>
        </div>
        <table class="booking-form__summary">
          <tbody>
            <tr class="booking-form__car-price"><td>Price for car</td><td class="price-text"><span class="icon">$</span><span class="amount">0</span></td></tr>
            <tr class="booking-form__add-on-price"><td>Price addon</td><td class="price-text"><span class="icon">$</span><span class="amount">0</span></td></tr>
            <tr class="booking-form__total-price"><td>Price total</td><td class="price-text"><span class="icon">$</span><span class="amount" data-car-amount="0" data-add-on-amount="0">0</span></td></tr>
          </tbody>
        </table>
      </main>
      <footer class="booking-form__footer">
        <input type="submit" class="booking-form__submit-btn" value="Confirm booking">
        <input type="reset" class="booking-form__reset-btn" value="Reset form">
      </footer>
    </form>
    <?php
    return ob_get_clean();
  } catch (Exception $e) {
    return 'Error in booking form short code: ' . $e->getMessage();
  }
});