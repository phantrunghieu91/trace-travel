document.addEventListener('DOMContentLoaded', function (docEv) {
  const swiper = new Swiper('.swiper', {
    loop: true,
    slidesPerView: 1,
    // autoplay: {
    //   delay: 4000,
    // },
    breakpoints: {
      550: {
        slidesPerView: 2,
        spaceBetween: 10,
      },
      850: {
        slidesPerView: 4,
        spaceBetween: 20,
      },
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
    pagination: {
      el: '.swiper-pagination',
      type: 'bullets',
    },
  });

  // read more toggle button
  document.querySelectorAll('.feedback__message-toggle-btn').forEach(btn => {
    btn.addEventListener('click', toggleEvent => {
      toggleEvent.preventDefault();
      const container = document.querySelector(`.feedback__message[data-feedback-id="${btn.dataset.feedbackId}"]`);
      if (container.classList.contains('feedback__message--show-more')) {
        container.classList.remove('feedback__message--show-more');
        btn.textContent = 'Read more';
      } else {
        container.classList.add('feedback__message--show-more');
        btn.textContent = 'Read less';
      }
    });
  });
});
