document.addEventListener('DOMContentLoaded', function (docEv) {
  // * Feedback section
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

  // * featured trips Tabs
  const featuredTrips = {
    init() {
      this.swiperEls = [...document.querySelectorAll('.featured-trips .swiper')];
      if( !this.swiperEls ) {
        console.warn( 'HOME PAGE: Can NOT find featured trips swiper element!' );
        return;
      }
      this.swipers = this.initSwiper();
    },
    initObserver() {
      let swipers = [];
      const observer = new ResizeObserver( (entries) => {
        const entry = entries[0];
        const isLargeScreen = entry.contentRect.width > 850;
        if( isLargeScreen ) {
          if( swipers.length == 0 ) {
            swipers = this.initSwiper();
          }
        } else {
          if( swipers.length > 0 ) {
            swipers.forEach( instance => {
              instance.destroy();
            });
            swipers = [];
          }
        }
      } );
      observer.observe( this.swiperEls[0] );
    },
    initSwiper() {
      if( typeof Swiper === 'undefined' ) {
        console.warn( 'HOME PAGE: Swiper library is missing!' );
        return;
      }
      return this.swiperEls.map( swiperEl => {
        return new Swiper( swiperEl, {
          slidesPerView: 1,
          spaceBetween: 20,
          initialSlide: 2,
          centeredSlides: true,
          loop: true,
          navigation: {
            nextEl: '.featured-trips .swiper-button-next',
            prevEl: '.featured-trips .swiper-button-prev',
          },
          breakpoints: {
            550: {
              slidesPerView: 2,
            },
            850: {
              slidesPerView: 3,
            }
          }
        }) 
      });
    }
  }.init();
  // * Gallery
  const gallerySection = {
    init() {
      if( typeof Fancybox === 'undefined' ) {
        console.warn( 'HOME PAGE: Fancy is NOT found!');
        return;
      }
      Fancybox.bind( '[data-fancybox="imggal"]', {
        Thumbs: {
          type: 'classic',
        },
      } );
    }
  }.init();  
});
