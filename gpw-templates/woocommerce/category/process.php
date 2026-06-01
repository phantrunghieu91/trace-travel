<?php 
/**
 * @author Hieu "Jin" Phan Trung
 * * Template: woocommerce category - Process
 */
$process = get_field( 'process', 'gpw_settings' );
if( empty( $process ) ) {
  return;
}
?>
<section class="process">
  <div class="section-inner">
    <h2 class="title-of-section"><?= __('Rental process', 'gpw') ?></h2>
    <div class="process__content"><?= wp_kses_post( $process ) ?></div>
  </div>
</section>