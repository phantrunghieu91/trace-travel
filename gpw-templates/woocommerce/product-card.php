<?php 
/**
 * @author Hieu "Jin" Phan Trung
 * * Template: Woocommerce - Product card
 */
global $product;
if( !$product ) {
  $product = wc_get_product( get_the_ID() );
}
if( !is_a( $product, 'WC_Product' )) {
  return;
}
$classes = ['product-card', "product-card-{$product->get_id()}"];
$url = $product->get_permalink();
$cat_ids = $product->get_category_ids();
$category = get_term( $cat_ids[0], 'product_cat' );
$shortDesc = wp_trim_words($product->get_short_description() ?? $product->get_description(), 30);
$priceHTML = $product->get_price_html();
?>
<article class="<?= esc_attr( implode( ' ', $classes )) ?>">
  <header class="product-card__header">
    <a href="<?= get_term_link( $category ) ?>" class="product-card__category"><?= $category->name ?></a>
    <a href="<?= esc_url( $url ) ?>" class="product-card__thumbnail">
      <?= $product->get_image( 'medium_large' ) ?>
    </a>
  </header>
  <main class="product-card__content">
    <h4 class="product-card__title line-clamp">
      <a href="<?= $url ?>"><?= $product->get_title(); ?></a>
    </h4>
    <?php if( !empty( $shortDesc )) : ?>
      <div class="product-card__description line-clamp"><?= wp_kses_post( $shortDesc ) ?></div>
    <?php endif ?>
    <div class="product-card__price-wrapper">
      <div class="product-card__price"><?= $priceHTML ?></div>
      <a href="<?= $url ?>" class="product-card__view-detail"><?= __('View detail', 'gpw') ?></a>
    </div>
  </main>
</article>