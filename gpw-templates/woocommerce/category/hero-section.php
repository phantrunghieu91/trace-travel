<?php 
/**
 * @author Hieu "Jin" Phan Trung
 * * Template: Woocommerce category - Hero section
 */
$banner_id = isset( $args['banner_id'] ) ? $args['banner_id'] : 0;
if( empty( $banner_id )) {
  return;
}
?>
<section class="hero">
  <div class="hero__bg"><?= wp_get_attachment_image( $banner_id, 'full' ) ?></div>
  <div class="section-inner"></div>
</section>