<?php 
/**
 * @author Hieu "Jin" Phan Trung
 * * Template: Woocommere category - Included
 */
$included = get_field( 'included', 'gpw_settings' );
$about = get_field( 'about', 'gpw_settings' );
if( empty( $included ) && empty( $about ) ) {
  return;
}
?>
<section class="included-about">
  <div class="section-inner">
    <?php if( !empty( $included )) : ?>
      <div class="included-about__included">
        <h2 class="title-of-section"><?= __('Included in rental price:', 'gpw') ?></h2>
        <div class="included-about__included-grid">
          <?php foreach( $included as $item ) : ?>
            <div class="included-about__included-item">
              <?= wp_get_attachment_image( $item['icon'], 'thumbnail', false, [ 'class' => 'included-about__included-item-icon' ]) ?>
              <span class="included-about__included-item-label"><?= esc_html($item['label']) ?></span>
            </div>
          <?php endforeach ?>
        </div>
      </div>
    <?php endif ?>
    <?php if( !empty( $about )) : ?>
      <div class="included-about__about">
        <h2 class="title-of-section"><?= __('About this rental:', 'gpw') ?></h2>
        <div class="included-about__about-content"><?= wp_kses_post( $about ) ?></div>
      </div>
    <?php endif ?>
  </div>
</section>