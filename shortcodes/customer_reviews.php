<?php
add_shortcode('customer_reviews', function () {
  try {
    $feedbacks = get_field('feedbacks', 'option');
    if (!empty ($feedbacks)) {
      ob_start();
      $feedback_count = 1;
      echo '<div class="feedbacks swiper"><div class="swiper-wrapper">';
      foreach ($feedbacks as $feedback) {
        echo '<div class="swiper-slide feedback">
          <div class="feedback__rate-stars">';
        for ($i = 1; $i <= 5; $i++) {
          echo '<div class="feedback__rate-star' . ($i <= $feedback['rate'] ? ' feedback__rate-star--shiny' : '') . '">
            <svg height="24px" width="24px" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" 
          viewBox="0 0 47.94 47.94" xml:space="preserve">
              <path d="M26.285,2.486l5.407,10.956c0.376,0.762,1.103,1.29,1.944,1.412l12.091,1.757
            c2.118,0.308,2.963,2.91,1.431,4.403l-8.749,8.528c-0.608,0.593-0.886,1.448-0.742,2.285l2.065,12.042
            c0.362,2.109-1.852,3.717-3.746,2.722l-10.814-5.685c-0.752-0.395-1.651-0.395-2.403,0l-10.814,5.685
            c-1.894,0.996-4.108-0.613-3.746-2.722l2.065-12.042c0.144-0.837-0.134-1.692-0.742-2.285l-8.749-8.528
            c-1.532-1.494-0.687-4.096,1.431-4.403l12.091-1.757c0.841-0.122,1.568-0.65,1.944-1.412l5.407-10.956
            C22.602,0.567,25.338,0.567,26.285,2.486z"/>
            </svg>
          </div>';
        }
        echo '</div>';  // Close feedback__rate
        echo '<div class="feedback__message" data-feedback-id="' . $feedback_count . '">
          <div class="feedback__message-inner">' . $feedback['feedback'] . '</div>
          <a class="feedback__message-toggle-btn" data-feedback-id="' . $feedback_count++ . '">Read more</a>
        </div>';
        echo '<div class="feedback__meta">
          <div class="feedback__avatar">' . ($feedback['avatar'] ? wp_get_attachment_image($feedback['avatar']['id'], 'thumbnail', false, array ('alt' => $feedback['avatar']['name'])) : do_shortcode('[img_by_id img_id="55" alt="placeholder" size="thumbnail"]')) . '</div>
          <div class="feedback__name">' . $feedback['name'] . '</div>
          <div class="feedback__job">' . $feedback['job'] . '</div>
        </div>';

        echo '</div>';  // Close swiper slide
      }
      echo '</div>
      <div class="swiper-pagination"></div>
      <div class="swiper-button-prev"></div> 
      <div class="swiper-button-next"></div>
      </div>';  // Close feedbacks, swiper-wrapper
      return ob_get_clean();
    }
  } catch (Exception $e) {
    return 'Error in customer reviews shortcode : ' . $e->getMessage();
  }
});