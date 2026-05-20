<?php
add_shortcode('homepage_featured_products', function ( $atts ) {
  extract( shortcode_atts( [
    'prd_cat_id' => 0,
    'display_style' => 'carousel' // carousel || grid
  ], $atts ));
  $products = wc_get_products([
    'status' => 'publish',
    'limit' => $display_style === 'carousel' ? 6 : 8,
    'product_category_id' => [ $prd_cat_id ],
  ]);
  $classes = [ 'featured-trips' ];
  $classes[] = $display_style === 'carousel' ? 'featured-trips--carousel' : 'featured-trips--grid';
  ob_start();
  ?>
  <div class="<?= esc_attr( implode( ' ', $classes )) ?>">
    <?php if( $display_style === 'carousel' ) : ?>
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
    <?php else: ?>

      <?php foreach( $products as $product ) {
        unset($GLOBALS['product']);
        $GLOBALS['product'] = $product;
        get_template_part( 'gpw-templates/woocommerce/product-card' );
      } ?>

    <?php endif ?>
    
  </div><!-- Close featured-trips -->
  <?php
  return ob_get_clean();
});