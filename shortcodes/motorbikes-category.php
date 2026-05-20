<?php 
/**
 * @author Hieu "Jin" Phan Trung
 * * Motorbikes category shortcode
 */
add_shortcode( 'motorbike_category', function( $atts ) {
  if( !is_front_page() ) {
    return '';
  }
  $data = get_field( 'motorbikes_category', get_the_ID() );
  if( empty( $data['motorbike'] ) ) {
    return '';
  }
  $phoneNumber = $data['phone_number'] ?: '';
  $features = $data['features'] ?: '';
  $currencyCode = get_woocommerce_currency();
  ob_start();
  ?>
  <div class="motorbikes-list">
    <?php foreach( $data[ 'motorbike' ] as $motorbike ): 
      $isBestChoice = $motorbike['best_choice'];
    ?>
      <article class="motorbike<?= $isBestChoice ? ' best-choice' : '' ?>">
        <div class="motorbike__overlay"></div>
        <div class="motorbike__content">
          <?php if( $isBestChoice ): ?>
            <div class="motorbike__best-choice-tag">
              <span class="material-symbols-outlined">star</span>
              <span><?= __('The best choice', 'gpw') ?></span>
            </div>
          <?php endif ?>
          <h3 class="motorbike__name"><?= esc_html( $motorbike['name'] ) ?></h3>
          <div class="motorbike__thumbnail">
            <?= wp_get_attachment_image( $motorbike['thumbnail'], 'medium_large') ?>
          </div>
          <?php if( !empty( $features ) ) : ?>
            <div class="motorbike__features"><?= wp_kses_post( $features ) ?></div>
          <?php endif ?>
          <div class="motorbike__price-wrapper">
            <strong class="motorbike__price"><?= esc_html( $motorbike['price']) ?></strong>
            <span class="motorbike__currency"><?= $currencyCode ?> / <?= __('day', 'gpw') ?></span>
          </div>
          <div class="motorbike__cta-buttons">
            <?php if( !empty( $phoneNumber )) : ?>
              <a href="tel:<?= esc_attr( $phoneNumber ) ?>" class="motorbike__phone-call">
                <span class="material-symbols-outlined">call</span>
                <span><?= __('Book now', 'gpw') ?></span>
              </a>
            <?php endif ?>
          </div>
        </div>
      </article>
    <?php endforeach ?>
  </div>
  <a class="motorbikes-category__price-table-cta gpw-button gpw-button--center" href="javascript:void(0);"><?= __( 'Price table', 'gpw' ) ?></a>
  <?php 
  return ob_get_clean();
} );