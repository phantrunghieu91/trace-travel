<?php
add_shortcode('homepage_featured_products', function () {
  $products = wc_get_products([
    'status' => 'publish',
    'limit' => 6,
    'product_category_id' => [ 18 ],
  ]);
  ob_start();
  ?>
  <div class="featured-trips">
    <div class="swiper">
      <div class="swiper-wrapper">
        <?php foreach( $products as $product ) {
          unset($GLOBALS['product']);
          $GLOBALS['product'] = $product;
          echo '<div class="swiper-slide">';
          get_template_part( 'gpw-templates/woocommerce/product-card' );
          echo '</div>';
        } ?>
      </div>
      <a href="javascript:void(0);" role="button" class="swiper-button-prev" aria-label="Previous Product">
      </a>
      <a href="javascript:void(0);" role="button" class="swiper-button-next" aria-label="Next Product">
      </a>
    </div>
    
  </div><!-- Close featured-trips -->
  <?php
  return ob_get_clean();
});