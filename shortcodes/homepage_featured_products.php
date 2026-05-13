<?php
add_shortcode('homepage_featured_products', function () {
  // * get all trip child categories
  $categories = get_terms(
    [
      'taxonomy' => 'product_cat',
      'hide_empty' => false,
      'parent' => get_term_by('slug', 'trip', 'product_cat')->term_id
    ]
  );
  ob_start();
  ?>
  <div class="featured-trips">
    <nav class="featured-trips__tab-nav">

      <?php foreach ($categories as $category): ?>

        <a href="javascript:void(0);" data-target="<?= esc_attr($category->slug) ?>"
          class="featured-trips__tab-nav-item<?= $category->slug === 'da-nang-car-rental' ? ' featured-trips__tab-nav-item--active' : '' ?>"><?= esc_html($category->name) ?></a>

        <?php endforeach; ?>

    </nav>
    <div class="featured-trips__tab-content">

      <?php foreach ($categories as $category): ?>

        <div class="featured-trips__tab-pane<?= $category->slug === 'da-nang-car-rental' ? esc_attr(' featured-trips__tab-pane--active') : '' ?>"
          id="<?= esc_attr($category->slug) ?>">

          <?php
          $trips_query = new WP_Query(
            [
              'post_type' => 'product',
              'posts_per_page' => 6,
              'fields' => 'ids',
              'post_status' => 'publish',
              'tax_query' => [
                [
                  'taxonomy' => 'product_cat',
                  'field' => 'term_id',
                  'terms' => $category->term_id
                ]
              ]
            ]
          );
          if ($trips_query->have_posts()) {
            foreach ($trips_query->posts as $trip_id):
              $trip = wc_get_product($trip_id);
              ?>
              <div class="featured-trip">
                <a href="<?php echo $trip->get_permalink(); ?>" class="featured-trip__img">
                  <?php echo get_the_post_thumbnail($trip_id, 'large'); ?>
                </a>
                <div class="featured-trip__content">
                  <a href="<?php echo $trip->get_permalink(); ?>" class="featured-trip__title">
                    <h3>
                      <?php echo $trip->get_title(); ?>
                    </h3>
                  </a>
                  <div class="featured-trip__icons">
                    <?php
                    $icons = ['sedan' => 384, 'suv' => 385, 'van' => 386, 'aircon' => 387];
                    foreach ($icons as $icon_name => $icon_id): ?>
                      <div class="featured-trip__icon">
                        <?php echo wp_get_attachment_image($icon_id, 'thumbnail', true, array('allt' => $icon_name)); ?>
                        <span>
                          <?php echo $icon_name; ?>
                        </span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                  <div class="featured-trip__price-wrapper">
                    <span>Price from</span>
                    <?php echo wc_price($trip->price); ?>
                    <span>per car</span>
                  </div>
                </div>
                <a href="<?php echo $trip->get_permalink(); ?>" class="featured-trip__book-btn">More Detail</a>
              </div>
              <?php
            endforeach;
          }
          ?>

        </div>

        <?php endforeach; ?>

    </div>
  </div><!-- Close featured-trips -->
  <?php
  return ob_get_clean();
});