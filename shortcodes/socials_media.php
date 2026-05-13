<?php
add_shortcode('socials_media', function () {
  $socials = get_field('socials', 'option');
  if (empty($socials))
    return;

  ob_start();
  ?>

  <div class="fixed-social-icons">
    <?php foreach ($socials as $social):
      if (!empty($social['icon'])): ?>
        <a class="fixed-social-icons__item <?= $social['name'] ?>"
          href="<?= $social['url'] ? esc_url($social['url']) : 'javascript:void(0);'; ?>"
          target="_blank"><?= wp_get_attachment_image($social['icon'], 'thumbnail', true, ['alt' => $social['name']]); ?></a>
      <?php endif; endforeach; ?>
  </div> <!-- Close fixed-social-icons -->

  <?php
  return ob_get_clean();
});