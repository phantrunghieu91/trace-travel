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
          
          <div class="rental-detail">
            <h2 class="rental-detail__title"><?= __('Included in rental price', 'gpw') ?>:</h2>
            <div class="rental-detail__grid">
              <?php 
              $rental_items = [
                [ 'img_id' => 2100, 'label' => 'Helmet' ],
                [ 'img_id' => 2101, 'label' => 'Phone holder' ],
                [ 'img_id' => 2105, 'label' => '1 liter of fuel' ],
                [ 'img_id' => 2102, 'label' => 'Bungee cords' ],
                [ 'img_id' => 2103, 'label' => 'Luggage Transportation (1 piece of luggage per motorbike)' ],
                [ 'img_id' => 2104, 'label' => 'Google Maps detailed route map' ],
              ];
              foreach( $rental_items as $item ) {
                echo sprintf('<div class="rental-detail__item">%s<span class="rental-detail__item-label">%s</span></div>',
                  wp_get_attachment_image( $item['img_id'], 'thumbnail', false, [ 'class' => 'rental-detail__item-icon' ]),
                  $item['label'],
                );
              }
              ?>
            </div>
            <a class="rental-detail__check-price gpw-button" href="#car-type-price"><?= __('Check price', 'gpw') ?></a>
          </div>
          
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

        <?php 
          $tab_nav = [ 'detail' => __('Detail', 'gpw'), 'promotion' => __('Promotion', 'gpw'), 'faqs' => __('FAQs', 'gpw'), 'reviews' => __('Reviews', 'gpw'), 'contact-us' => __('Contact us', 'gpw') ];
        ?>

        <div class="section-inner">
          <div class="additional-info__nav-tabs">
            <div class="nav-tabs__inner">
              <?php $idx = 0; foreach( $tab_nav as $id => $label ) {
                echo sprintf( '<a class="nav-tabs__tab-control%s" href="#%s">%s</a>',
                  $idx === 0 ? ' current' : '',
                  esc_attr( $id ),
                  esc_html( $label ),
                );
                $idx++;
              } 
              unset($idx); ?>
            </div>
          </div>
          <div class="additional-info__tab-content">
            <div class="additional-info__tab-pane" id="detail">
              <h3 class="tab-pane__title"><?= $tab_nav['detail'] ?></h3>
              <div class="tab-pane__content">
                <?php the_content(); ?>
                <div class="detail__car-price" id="car-type-price">
                  <strong class="car-price__title"><?= __('Price per car from', 'gpw') ?> <?= get_the_title(); ?></strong>
                  <div class="car-price__table">
                    <div class="table__title">
                      <div class="car-price__option logo">
                        <?= wp_get_attachment_image(get_theme_mod('site_logo'), 'thumbnail', false, array('alt' => 'logo')) ?>
                      </div>
                      <div class="car-price__option car-type"><?= __('Motorbike type', 'gpw') ?></div>
                      <div class="car-price__option price"><?= __('Price', 'gpw') ?></div>
                    </div>
                    <?php
                    foreach ($variations as $idx => $variation) :
                      $motorbike_term = get_term_by( 'slug', $variation['attributes']['attribute_pa_motorbike-type'], 'pa_motorbike-type' );
                    ?>
                      <div class="table__<?= esc_attr( $idx ) ?>">
                        <div class="car-price__option image">
                          <?= wp_get_attachment_image( $variation['image_id'], 'medium', false, [ 'alt' => $variation['image']['alt'] ]) ?>
                        </div>
                        <div class="car-price__option car-type">
                          <span>
                            <?= $motorbike_term->name ?>
                          </span>
                        </div>
                        <div class="car-price__option price">
                          <?= $variation['price_html'] ?>
                          <?php if( !empty($variation['variation_description']) ) 
                            echo $variation['variation_description'];
                          ?>
                        </div>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="additional-info__tab-pane" id="promotion">
              <div class="tab-pane__title"><?= $tab_nav['promotion'] ?></div>
              <div class="tab-pane__content">
                <?php the_field('promotion'); ?>
              </div>
            </div>
            <div class="additional-info__tab-pane" id="faqs">
              <?php 
              $faqs = [
                ['question' => __('Does your company have limited time to visit?', 'gpw'), 'answer' => __('You can choose your departure time and your favorite destinations and we want to make sure you have enough time to visit at your chosen locations.', 'gpw')],
                ['question' => __('This price is the price per person or per the private car?', 'gpw'), 'answer' => __('That price per car not person which included an English speaking driver; 24/7 chat, email or call support; toll and airport fees and charges, door to door service; free Wi-Fi on board and bottle of water.', 'gpw')],
                ['question' => __('What happen if unfortunately we canceled the transfer?', 'gpw'), 'answer' => __('You can cancel your booking without any fees charge with but only for one day before your trip starts. After that time, you’ll pay for the fee charges.', 'gpw')],
                [ 'question' => __('Does your driver can speak English if we want to know something during our trip?','gpw'), 'answer' => __('Yes, they can. We are always so proud of our drivers who can speak Basic English communication or better but they are not tour guides so they have limitation. You will stop at Dragon Bridge and our drivers also can give you some information about each place. We will be continue delivering excellent services for you.', 'gpw')],
                [ 'question' => __('How will we pay for the trip?','gpw'), 'answer' => __('We accept the payment method as bellow:
                    <ul><li>Paypal (pay extra 4% for transaction fee)</li><li>Credit card at our office (pay extra 4% for banking fee): we have two offices in Hue or Hoi An so you can pay at where you feel the most convenient.</li><li>Cash at our office or to driver.</li></ul>', 'gpw')],
              ];
              $faqs_specific_for_prd = get_field('faqs');
              if( !empty( $faqs_specific_for_prd )) {
                foreach( $faqs_specific_for_prd as $faq ) {
                  $faqs[] = [
                    'question' => $faq['question'],
                    'answer' => $faq['answer'],
                  ];
                }
              }
              ?>
              <div class="tab-pane__title"><?= $tab_nav['faqs'] ?></div>
              <div class="tab-pane__content">
                <div class="faqs-accordion">
                  <?php foreach ($faqs as $idx => $faq ) : ?>
                    <div class="faqs__item<?= $idx == 0 ? ' current' : '' ?>">
                      <div class="faqs__question"><?= $faq['question'] ?></div>
                      <div class="faqs__answer"><?= wp_kses_post( $faq['answer']) ?></div>
                    </div>
                  <?php endforeach ?>
                </div>
              </div>
            </div>
            <div class="additional-info__tab-pane" id="reviews">
              <div class="tab-pane__title"><?= $tab_nav['reviews'] ?></div>
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
                      <option value="default"><?= __('Choose motorbike', 'gpw') ?></option>
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
      } ?>
      <?php if( !empty( $related_trip_ids )) : ?>
      <section class="related-trips">
        <div class="section-inner">
          <h2 class="related-trips__title"><?= __('Related Tours', 'gpw') ?></h2>
          <div class="related-trips__content swiper">
            <div class="swiper-wrapper">
              <?php foreach ($related_trip_ids as $related_trip_id) :
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
              <?php endforeach ?>
            </div>
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
          </div>
        </div>
      </section>
      <?php endif ?>
    </div>
  <?php endwhile; endif;
wp_reset_postdata(); ?>
<?php get_footer(); ?>