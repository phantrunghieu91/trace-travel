<?php
/**
 * Single page for product
 */
get_header();
if (have_posts()):
  while (have_posts()):
    the_post();
    $product = wc_get_product(get_the_ID());
    $variations = $product->get_available_variations();
    // Sort variations by price
    usort($variations, function ($a, $b) {
      return $a['display_price'] - $b['display_price'];
    });
    // Get add on list
    $add_ons = [];
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
    ?>
    <div id="content" class="tour-entry tour-<?php the_ID(); ?>">
      <section class="hero-banner">
        <?php the_post_thumbnail('full', array('alt' => 'hero banner')) ?>
        <div class="breadcrumb">
          <?php if (function_exists('rank_math_the_breadcrumbs'))
            rank_math_the_breadcrumbs(); ?>
        </div>
      </section>
      <section class="title-gallery">
        <div class="section-inner">
          <h1 class="page-title">
            <?php the_title(); ?>
          </h1>
          <?php
          $trip_info = get_field('trip_info');
          if (!empty($trip_info)): ?>
            <div class="trip-info">
              <div class="trip-info__duration">
                <div class="trip-info__icon">
                  <?php echo wp_get_attachment_image(320, 'thumbnail', true, array('alt' => 'trip info icons')); ?>
                </div>
                <div class="trip-info__title"><span>Duration:</span></div>
                <div class="trip-info__content">
                  <?php echo $trip_info['duration']; ?>
                </div>
              </div>
              <div class="trip-info__transport">
                <div class="trip-info__icon">
                  <?php echo wp_get_attachment_image(321, 'thumbnail', true, array('alt' => 'trip info icons')); ?>
                </div>
                <div class="trip-info__title"><span>Transport:</span>
                </div>
                <div class="trip-info__content">
                  <?php echo $trip_info['transport']; ?>
                </div>
              </div>
              <div class="trip-info__route">
                <div class="trip-info__icon">
                  <?php echo wp_get_attachment_image(322, 'thumbnail', true, array('alt' => 'trip info icons')); ?>
                </div>
                <div class="trip-info__title"><span>Route:</span>
                </div>
                <div class="trip-info__content">
                  <?php echo $trip_info['route']; ?>
                </div>
              </div>
              <a class="trip-info__check-price" href="#car-type-price">Check Price</a>
            </div>
          <?php endif; ?>
          <div class="trip-gallery">
            <?php
            $gallery = $product->get_gallery_image_ids();
            $previews = '';
            $dialog = '';
            $count = 0;
            if (!empty($gallery)):
              foreach ($gallery as $img_id) {
                if ($count++ < 5)
                  $previews .= '<div class="previews__item" data-img-id="' . $img_id . '">' . wp_get_attachment_image($img_id, 'large', false, array('alt' => $product->get_title() . $img_id)) . '</div>';
                $dialog .= '<div class="swiper-slide" data-img-id="' . $img_id . '">' . wp_get_attachment_image($img_id, 'full', false, array('alt' => $product->get_title() . $img_id)) . '</div>';
              }
              ?>
              <div class="trip-gallery__previews">
                <a href="#trip-gallery-full" class="trip-gallery__toggle-btn">
                  <span class="dashicons dashicons-images-alt2"></span>Gallery</a>
                <?php echo $previews; ?>
              </div>
              <dialog class="trip-gallery__dialog" id="trip-gallery-full">
                <a class="trip-gallery__dialog-close-btn"><span class="dashicons dashicons-no-alt"></span></a>
                <div class="swiper">
                  <div class="swiper-wrapper">
                    <?php echo $dialog; ?>
                  </div>
                  <div class="swiper-button-prev"></div>
                  <div class="swiper-button-next"></div>
                </div>
              </dialog>
            <?php endif; ?>
          </div>
        </div>
      </section>
      <section class="additional-info">
        <div class="section-inner">
          <div class="additional-info__nav-tabs">
            <div class="nav-tabs__inner">
              <a class="nav-tabs__tab-control current" href="#detail">Detail</a>
              <a class="nav-tabs__tab-control" href="#video">Video</a>
              <a class="nav-tabs__tab-control" href="#faqs">FAQs</a>
              <a class="nav-tabs__tab-control" href="#reviews">Reviews</a>
              <a class="nav-tabs__tab-control" href="#contact-us">Contact Us</a>
            </div>
          </div>
          <div class="additional-info__tab-content">
            <div class="additional-info__tab-pane" id="detail">
              <div class="tab-pane__title">Detail</div>
              <div class="tab-pane__content">
                <?php the_content(); ?>
                <div class="detail__car-price" id="car-type-price">
                  <div class="car-price__title">Price per car from
                    <?php the_title(); ?>
                  </div>
                  <div class="car-price__table">
                    <div class="table__title">
                      <div class="car-price__option logo">
                        <?php echo wp_get_attachment_image(7, 'thumbnail', false, array('alt' => 'logo')) ?>
                      </div>
                      <div class="car-price__option car-type">Car type</div>
                      <div class="car-price__option brand">Brand</div>
                      <div class="car-price__option max-passengers">Max Passengers</div>
                      <div class="car-price__option english-skill">English Skill</div>
                      <div class="car-price__option price">Price</div>
                    </div>
                    <?php
                    $table_content = [];
                    $render_conditions = function ($condition_1, $condition_2, $result_1, $result_2, $result_3) {
                      if ($condition_1)
                        return $result_1;
                      elseif ($condition_2)
                        return $result_2;
                      else
                        return $result_3;
                    };
                    foreach ($variations as $variation) {
                      // dd($variation);
                      $table_col = [];
                      foreach ($variation['attributes'] as $key => $val) {
                        $table_col['col_name'] = $render_conditions(str_contains($val, 'max-3'), str_contains($val, 'max-5'), 'sedan', 'suv', 'van');
                        $table_col['seat'] = $render_conditions(str_contains($val, 'max-3'), str_contains($val, 'max-5'), '(04 seats)', '(07 seats)', '(16 seats)');
                        $table_col['max_pax'] = $render_conditions(str_contains($val, 'max-3'), str_contains($val, 'max-5'), 'Max 3 Passengers', 'Max 5 Passengers', 'Max 12 Passengers');
                        $table_col['img_id'] = $render_conditions(str_contains($val, 'max-3'), str_contains($val, 'max-5'), 315, 316, 317);
                        $table_col['brand'] = $render_conditions(str_contains($val, 'max-3'), str_contains($val, 'max-5'), 'Chevrolet Cruze, Toyota Vios, Hyundai Accent', 'Toyota Innova, Toyota Fortuner', 'Ford Transit, Mercedes Benz Sprinter');
                        $table_col['price'] = $variation['display_price'];
                      }
                      $table_content[] = $table_col;
                    }
                    foreach ($table_content as $col): ?>
                      <div class="table__<?php echo $col['col_name']; ?>">
                        <div class="car-price__option image">
                          <?php echo wp_get_attachment_image($col['img_id'], 'full', false, array('alt' => $col['col_name'])) ?>
                        </div>
                        <div class="car-price__option car-type">
                          <span>
                            <?php echo $col['col_name']; ?>
                          </span>
                          <span>
                            <?php echo $col['seat']; ?>
                          </span>
                        </div>
                        <div class="car-price__option brand">
                          <?php echo $col['brand']; ?>
                        </div>
                        <div class="car-price__option max-passengers">
                          <?php echo $col['max_pax']; ?>
                        </div>
                        <div class="car-price__option english-skill">Conversational English
                        </div>
                        <div class="car-price__option price">
                          <?php echo wc_price($col['price']); ?>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="additional-info__tab-pane" id="video">
              <div class="tab-pane__title">Video</div>
              <div class="tab-pane__content">
                <?php the_field('video'); ?>
              </div>
            </div>
            <div class="additional-info__tab-pane" id="faqs">
              <div class="tab-pane__title">FAQs</div>
              <div class="tab-pane__content">
                <div class="faqs-accordion">
                  <div class="faqs__item current">
                    <div class="faqs__question">Does your company have limited time to visit?</div>
                    <div class="faqs__answer">
                      You can choose your departure time and your favorite destinations and we want
                      to make sure you have enough time to visit at your chosen locations.
                    </div>
                  </div>
                  <div class="faqs__item">
                    <div class="faqs__question">This price is the price per person or per the private car?</div>
                    <div class="faqs__answer">
                      That price per car not person which included an English speaking driver; 24/7
                      chat, email or call support; toll and airport fees and charges, door to door service; free Wi-Fi on
                      board and bottle of water.
                    </div>
                  </div>
                  <div class="faqs__item">
                    <div class="faqs__question">What happen if unfortunately we canceled the transfer?</div>
                    <div class="faqs__answer">
                      You can cancel your booking without any fees charge with but only for one day before your trip starts.
                      After that time, you’ll pay for the fee charges.
                    </div>
                  </div>
                  <?php
                  $accordion = get_field('faqs');
                  if (!empty($accordion)) {
                    for ($i = 0; $i < sizeof($accordion); $i++) {
                      ?>
                      <div class="faqs__item">
                        <div class="faqs__question">
                          <?php echo $accordion[$i]['question']; ?>
                        </div>
                        <div class="faqs__answer">
                          <?php echo $accordion[$i]['answer']; ?>
                        </div>
                      </div>
                      <?php
                    }
                  }
                  ?>
                  <div class="faqs__item">
                    <div class="faqs__question">Does your driver can speak English if we want to know something during our
                      trip?</div>
                    <div class="faqs__answer">
                      Yes, they can. We are always so proud of our drivers who can speak Basic English communication or
                      better but they are not tour guides so they have limitation. You will stop at Dragon Bridge and our
                      drivers also can give you some information about each place. We will be continue delivering excellent
                      services for you.
                    </div>
                  </div>
                  <div class="faqs__item">
                    <div class="faqs__question">How will we pay for the trip?</div>
                    <div class="faqs__answer">
                      We accept the payment method as bellow:

                      – Paypal (pay extra 4% for transaction fee)
                      – Credit card at our office (pay extra 4% for banking fee): we have two offices in Hue or Hoi An so
                      you can pay at where you feel the most convenient.
                      – Cash at our office or to driver.
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="additional-info__tab-pane" id="reviews">
              <div class="tab-pane__title">Reviews</div>
              <div class="tab-pane__content"></div>
            </div>
          </div>
          <aside class="additional-info__sidebar">
            <div class="sidebar__inner--sticky">
              <div class="sidebar__item booking-form">
                <span class="item__title">Booking</span>
                <div class="item__content price-wrapper">
                  <span class="dashicons dashicons-tag"></span>
                  <span>From </span>
                  <span class="item__price">
                    <?php echo wc_price($product->get_price()); ?>
                  </span>
                </div>
                <form id="booking-form" action="<?php echo home_url('/checkout', 'https'); ?>">
                  <?php wp_nonce_field('handling_booking_nonce', 'my_form_nonce') ?>
                  <input type="hidden" name="product-id" value="<?php echo $product->get_id(); ?>">
                  <div class="booking-form__control-wrap">
                    <label for="booking-date"><span class="dashicons dashicons-calendar-alt"></span></label>
                    <input type="date" name="booking-date" id="booking-date" class="booking-form__booking-date"
                      min="<?php echo date('Y-m-d'); ?>">
                  </div>
                  <div class="booking-form__control-wrap">
                    <label for="booking-time"><span class="dashicons dashicons-clock"></span></label>
                    <input type="time" name="booking-time" id="booking-time" class="booking-form__booking-time">
                  </div>
                  <div class="booking-form__control-wrap car-types">
                    <label for="booking-car-type"><span class="dashicons dashicons-car"></span></label>
                    <select id="booking-car-type" name="booking-car-type" class="booking-form__car-type">
                      <option value="default">Select type of car</option>
                      <?php
                      foreach ($variations as $variation) {
                        $term = get_term_by('slug', reset($variation['attributes']), array_keys($product->get_attributes())[0]);
                        ?>
                        <option value="<?php echo reset($variation['attributes']); ?>"
                          data-price="<?php echo $variation['display_price']; ?>"
                          data-prd-id="<?php echo $variation['variation_id']; ?>">
                          <?php echo $term->name; ?>
                        </option>
                        <?php
                      }
                      ?>
                    </select>
                  </div>
                  <div class="booking-form__control-wrap add-ons">
                    <div class="booking-form__control-wrap-title">Add on:</div>
                    <div class="booking-form__add-ons"
                      data-multi-prices="<?= $add_ons[0]['multi_price'] ? "true" : "false" ?>">
                      <?php
                      foreach ($add_ons as $add_on):
                        if (!$add_on['multi_price']):
                          ?>
                          <label><input type="checkbox" name="add-on" value="<?php echo $add_on['name']; ?>"
                              data-price="<?php echo $add_on['price']; ?>">
                            <span>
                              <?php echo $add_on['name']; ?>
                            </span>
                            <span>
                              <?= wc_price($add_on['price']); ?>
                            </span>
                          </label>
                        <?php else: ?>
                          <label><input type="checkbox" name="add-on" value="<?php echo $add_on['name']; ?>" data-price="<?= $add_on['prices']['sedan'] ?>"
                              data-sedan-price="<?= $add_on['prices']['sedan'] ?>"
                              data-suv-price="<?= $add_on['prices']['suv'] ?>" data-van-price="<?= $add_on['prices']['van'] ?>">
                            <span>
                              <?php echo $add_on['name']; ?>
                            </span>
                            <div class="add-on__prices">
                              <span class="sedan-price"><?= wc_price($add_on['prices']['sedan']) ?></span>
                              <span class="suv-price hide"><?= wc_price($add_on['prices']['suv']) ?></span>
                              <span class="van-price hide"><?= wc_price($add_on['prices']['van']) ?></span>
                            </div>
                          </label>
                          <?php
                        endif;
                      endforeach;
                      ?>
                    </div>
                  </div>
                  <table class="booking-form__summary">
                    <tbody>
                      <tr class="booking-form__car-price">
                        <td>Price for car</td>
                        <td class="price-text"><span class="icon">$</span><span class="amount">0</span></td>
                      </tr>
                      <tr class="booking-form__add-on-price">
                        <td>Price addon</td>
                        <td class="price-text"><span class="icon">$</span><span class="amount">0</span></td>
                      </tr>
                      <tr class="booking-form__total-price">
                        <td>Price total</td>
                        <td class="price-text">
                          <span class="icon">$</span><span class="amount" data-car-amount="0"
                            data-add-on-amount="0">0</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <input type="submit" class="booking-form__submit-btn" value="Confirm booking">
                </form>
              </div>
              <div class="sidebar__item need-help">
                <div class="item__title">Need help?</div>
                <div class="item__content">
                  <div class="need-help__phone-number"><span class="dashicons dashicons-phone"></span><a
                      href="tel:84916055666">+84 916 055 666</a></div>
                  <div class="need-help__email"><span class="dashicons dashicons-email-alt"></span><a
                      href="mailto:info@example.com">info@example.com</a></div>
                </div>
              </div>
            </div>
          </aside>
          <div class="additional-info__tab-pane" id="contact-us">
            <div class="tab-pane__title">Contact Us in Da Nang</div>
            <div class="tab-pane__content">
              <div class="contact-us__item address">
                <span class="contact-us__title">Address:</span>
                <div class="contact-us__content">117 Nguyễn Tri Phương - P. Vĩnh Trung - Q. Thanh Khê - Tp. Đà nẵng</div>
              </div>
              <div class="contact-us__item email">
                <span class="contact-us__title">Email:</span>
                <div class="contact-us__content"><a href="mailto:example@email.com">example@email.com</a></div>
              </div>
              <div class="contact-us__item phone">
                <span class="contact-us__title">Phone:</span>
                <div class="contact-us__content">
                  <a href="tel:84916055666">+84 916 055 666</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <section class="related-trips">
        <div class="section-inner">
          <h2 class="related-trips__title">Related Trip</h2>
          <div class="related-trips__content swiper">
            <div class="swiper-wrapper">
              <?php
              $related_trip_ids = [];

              $related_trip_query = new WP_Query(
                array(
                  'post_type' => 'product',
                  'posts_per_page' => 10,
                  'tax_query' => array(
                    array(
                      'taxonomy' => 'product_tag',  // product_tag for tag, product_cat for category
                      'field' => 'term_id',
                      'terms' => $product->get_tag_ids(),
                      'operator' => 'IN',
                    ),
                  ),
                  'post__not_in' => array(get_the_ID()),
                )
              );
              if ($related_trip_query->have_posts()) {
                foreach ($related_trip_query->posts as $related_prd) {
                  if (empty($related_trip_ids)) {
                    $related_trip_ids[] = $related_prd;
                  } else {
                    $is_duplicate = false;
                    foreach ($related_trip_ids as $related_trip_dup) {
                      if ($related_trip_dup->ID == $related_prd->ID) {
                        $is_duplicate = true;
                        break;
                      } else
                        continue;
                    }
                    if (!$is_duplicate)
                      $related_trip_ids[] = $related_prd->ID;
                  }
                }
              }
              foreach ($related_trip_ids as $related_trip_id) {
                $related_trip = wc_get_product($related_trip_id);
                ?>
                <div class="swiper-slide related-trip">
                  <a href="<?php echo $related_trip->get_permalink(); ?>" class="related-trip__featured-img">
                    <?php echo get_the_post_thumbnail($related_trip_id, 'medium'); ?>
                  </a>
                  <div class="related-trip__content">
                    <a href="<?php echo $related_trip->get_permalink(); ?>" class="related-trip__title">
                      <?php echo $related_trip->get_title(); ?>
                    </a>
                    <div class="related-trip__price-wrapper">From
                      <?php echo wc_price($related_trip->price); ?>
                    </div>
                  </div>
                  <a href="<?php echo $related_trip->get_permalink(); ?>" class="related-trip__book-btn">More Detail</a>
                </div>
                <?php
              }

              ?>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
          </div>
        </div>
      </section>
    </div>
  <?php endwhile; endif;
wp_reset_postdata(); ?>
<?php get_footer(); ?>
