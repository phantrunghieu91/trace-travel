<?php 
/**
 * @author Hieu "Jin" Phan Trung
 * * Template: Single product page - Include block
 */
$include_data = get_field( 'included' );
if( empty( $include_data )) {
  return;
}
?>
<div class="included">
  <h2 class="included__title">
    <?= __('Included in price', 'gpw'); ?>
  </h2>
  <div class="included__grid">
    <?php
    foreach ($include_data as $item) {
      echo sprintf(
        '<div class="included__item">%s<span class="included__item-label">%s</span></div>',
        wp_get_attachment_image($item['icon'], 'thumbnail', false, ['class' => 'included__item-icon']),
        $item['label'],
      );
    }
    ?>
  </div>
</div>