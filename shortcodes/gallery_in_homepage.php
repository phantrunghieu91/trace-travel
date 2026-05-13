<?php
add_shortcode('gallery_in_homepage', function () {
  try {
    $gallery = get_field('homepage_gallery', 'option');
    if (!empty ($gallery)) {
      $dialog_html = '<a class="dialog__close-btn" data-dialog="#homepage-gallery__dialog"><span class="dashicons dashicons-no-alt"></span></a><div class="swiper"><div class="swiper-wrapper">';
      ob_start();
      echo '<div class="homepage-gallery">';
      foreach ($gallery as $img):
        // dump($img);
        echo '<img src="' . $img['sizes']['large'] . '" alt="' . ($img['alt'] ? $img['alt'] : $img['title']) . '">';
        $dialog_html .= '<div class="swiper-slide"><img src="' . $img['url'] . '" alt="' . ($img['alt'] ? $img['alt'] : $img['title']) . '"></div>';
      endforeach;
      $dialog_html .= '</div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
      </div>';
      echo '<a class="homepage-gallery__open-dialog-btn" data-dialog="#homepage-gallery__dialog">Open gallery</a>
        <dialog id="homepage-gallery__dialog">
          ' . $dialog_html . '
        </dialog>
      </div>';  // Close homepage-gallery
      return ob_get_clean();
    } else {
      return 'There is no image in Gallery in homepage Option page!';
    }
  } catch (Exception $e) {
    return 'Error in gallery in home page short code: ' . $e->getMessage();
  }
});