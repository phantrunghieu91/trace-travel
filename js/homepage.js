document.addEventListener('DOMContentLoaded', function (docEv) {
  const feedbackSwiper = new Swiper('.feedbacks.swiper', {
    loop: true,
    slidesPerView: 1,
    autoplay: {
      delay: 4000,
    },
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

  // Gallery dialog toggle
  document.querySelector('.homepage-gallery__open-dialog-btn')?.addEventListener('click', function (evt) {
    evt.preventDefault();
    document.querySelector(`${this.getAttribute('data-dialog')}`).showModal();
    document.documentElement.classList.add('homepage-gallery-showing');
  });

  const gallerySwiper = new Swiper('#homepage-gallery__dialog > .swiper', {
    slidesPerView: 1,
    loop: true,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });

  document.querySelector('#homepage-gallery__dialog > .dialog__close-btn')?.addEventListener('click', function (evt) {
    evt.preventDefault();
    document.querySelector(`${this.getAttribute('data-dialog')}`).close();
    document.documentElement.classList.remove('homepage-gallery-showing');
  });

  // * featured trips Tabs
  const featuredTrips = {
    init() {
      this.swiperEl = document.querySelector('.featured-trips .swiper');
      if( !this.swiperEl ) {
        console.warn( 'HOME PAGE: Can NOT find featured trips swiper element!' );
        return;
      }
      this.initObserver();
    },
    initObserver() {
      let swiper = null;
      const observer = new ResizeObserver( (entries) => {
        const entry = entries[0];
        const isLargeScreen = entry.contentRect.width > 850;
        if( isLargeScreen ) {
          if( !swiper ) {
            swiper = this.initSwiper();
          }
        } else {
          if( swiper ) {
            swiper.destroy();
            swiper = null;
          }
        }
      } );
      observer.observe( this.swiperEl );
    },
    initSwiper() {
      if( typeof Swiper === 'undefined' ) {
        console.warn( 'HOME PAGE: Swiper library is missing!' );
        return;
      }
      return new Swiper( this.swiperEl, {
        slidesPerView: 4,
        spaceBetween: 20,
        initialSlide: 2,
        centeredSlides: true,
        loop: true,
        navigation: {
          nextEl: '.featured-trips .swiper-button-next',
          prevEl: '.featured-trips .swiper-button-prev',
        }
      });
    }
  }.init();
});
