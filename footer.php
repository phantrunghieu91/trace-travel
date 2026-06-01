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

$booking_data = get_field( 'booking', 'gpw_settings' );
?>

<?php if (!empty( $booking_data['booking_with'] ) ) : ?>
  <section class="booking">
    <div class="section__inner">
      <ul class="booking__with">
        <?php foreach( $booking_data['booking_with'] as $with ): ?>
          <li class="booking__with-item">
            <a href="<?= esc_url( $with['link']) ?>" target="_blank" rel="noopener noreferrer">
              <span><?= esc_html( $with['label']) ?></span>
              <span class="material-symbols-outlined">chevron_right</span>
            </a>
          </li>
        <?php endforeach ?>
      </ul>
      <?php if( !empty( $booking_data['map'] )): ?>
      <div class="booking__map">
        <?= $booking_data['map'] ?>
      </div>
      <?php endif ?>
    </div>
  </section>
<?php endif ?>

</main>

<footer id="footer" class="footer">
  <section class="footer__main">
    <div class="section__inner">
      <div class="footer__about">
        <div class="footer__logo"><?= wp_get_attachment_image( $site_logo_id, 'medium' ) ?></div>
        <p>Trace Travel was born out of our love for travel in Vietnam. With ten years of experience in the travel industry, we have extensive knowledge to share<br>
Our focus is on providing authentic and unforgettable experiences for our clients, tracing the path to the best journeys throughout Vietnam</p>
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

      <div class="footer__info">
        <h3 class="footer__title"><?= __('Get in touch', 'gpw') ?></h3>
        <p><?= __('Hotline (24/24)', 'gpw') ?>: <a href="tel:+84389080608">+84 389 08 06 08</a></p>
        <p>Email: <a href="mailto:contact@motogo.vn">tracetravelvietnam@gmail.com</a></p>
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