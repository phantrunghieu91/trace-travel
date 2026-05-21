<?php
/**
 * @author Hieu "Jin" Phan Trung
 * * The template for displaying the footer.
 */
$current_obj_id = get_queried_object_id();

$site_logo_id = get_theme_mod('site_logo') ?: 203;
$socials = get_field( 'socials', 'option' );
$footer_menu_id = 2;
$menu_items = wp_get_nav_menu_items( $footer_menu_id );
$certificate_img_ids = [ 2108, 2109 ];
?>
</main>

<footer id="footer" class="footer">
  <section class="footer__main">
    <div class="section__inner">
      <div class="footer__about">
        <div class="footer__logo"><?= wp_get_attachment_image( $site_logo_id, 'medium' ) ?></div>
        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Ducimus id alias deleniti iste mollitia placeat corporis commodi blanditiis cupiditate repellat.</p>
        <p><?= __('Hotline (24/24)', 'gpw') ?>: <a href="tel:0338 023 344">0338 023 344</a></p>
        <p>Email: <a href="mailto:contact@motogo.vn">contact@motogo.vn</a></p>
        <p><?= __('Monday - Sunday') ?>: 7:00 - 22:00</p>
        <?php if( !empty( $socials )): ?>
        <ul class="footer__socials">
          <?php foreach( $socials as $social ): ?>
            <li class="footer__socials-item">
              <a href="<?= esc_url($social['url']) ?>" target="_blank" rel="noopener noreferrer">
                <?= wp_get_attachment_image( $social[ 'icon'], 'thumbnail', false, [ 'alt' => $social['name'] ]) ?>
              </a>
            </li>
          <?php endforeach ?>
        </ul>
        <?php endif ?>
      </div>
      <?php if( !empty( $menu_items ) ): ?>
      <div class="footer__menu">
        <h3 class="footer__title"><?= __('About us', 'gpw') ?></h3>
        <ul class="footer__menu-list">
          <?php foreach( $menu_items as $item ): 
            if( $item->menu_item_parent != 0 ) continue;
          ?>
            <li class="footer__menu-item<?= $item->object_id == get_queried_object_id() ? ' current' : '' ?>">
              <a href="<?= $item->url ?>"><?= $item->title ?></a>
            </li>
          <?php endforeach ?>
        </ul>
      </div>
      <?php endif ?>

      <div class="footer__locations">
        <h3 class="footer__title"><?= __('Location', 'gpw') ?></h3>
        <div class="footer__location">
          <span class="material-symbols-outlined">location_on</span>
          <a href="javascript:void(0);" class="footer__location-name"><strong>Thuê Xe Máy Hà Nội</strong></a>
          <div class="footer__location-address">
            <p>CS1: <a href="https://goo.gl/maps/S8GFVPvD49TTPtbJA">81 P. Nguyễn Khả Trạc, Mai Dịch, Cầu Giấy, Hà Nội</a></p>
            <p>CS2: <a href="https://goo.gl/maps/BAEHmNumAbzZPcvz7">Số 7 Ngõ 267 Hoàng Hoa Thám, Ngọc Hà, Hà Nội</a></p>
          </div>
        </div>
        <div class="footer__location">
          <span class="material-symbols-outlined">location_on</span>
          <a href="javascript:void(0);" class="footer__location-name"><strong>Thuê Xe Máy Đà Nẵng</strong></a>
          <div class="footer__location-address">
            <p>421/3 Đ. Lê Duẩn, Chính Gián, Thanh Khê, Đà Nẵng, Vietnam</p>
          </div>
        </div>
        <div class="footer__certificate">
          <?php foreach( $certificate_img_ids as $img_id ) {
            echo wp_get_attachment_image( $img_id, 'medium' );
          } ?>
        </div>
      </div>
    </div>
  </section>

	<section class="footer__bottom">
    <div class="section__inner">
      <p>Copyright 2026 © <?= get_bloginfo('name') ?> - All Right Reserved.</p>
      <p>Designed by <a href="https://giaiphapweb.vn">giaiphapweb.vn</a></p>
    </div>
  </section>

	<?php
	if (get_theme_mod('back_to_top', 1)) {
		get_template_part('template-parts/footer/back-to-top');
	}
	?>

</footer>

</div>

<?php wp_footer(); ?>

</body>
</html>