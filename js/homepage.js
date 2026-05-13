document.addEventListener('DOMContentLoaded', function (docEv) {
  const swiper = new Swiper('.feedbacks.swiper', {
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
    self: document.querySelector('.featured-trips'),
    tabs: document.querySelectorAll('.featured-trips__tab-nav-item'),
    panes: document.querySelectorAll('.featured-trips__tab-pane'),

    init: function() {
      this.tabs.forEach(tab => {
        tab.addEventListener('click', this.tabClickHandler.bind(this));
      });
    },

    tabClickHandler: function(evt) {
      evt.preventDefault();
     
      const _self = evt.currentTarget;

      if(_self.classList.contains('.featured-trips__tab-nav-item--active')) {
        return;
      }

      this.removeActiveTabAndPane();
      _self.classList.add('featured-trips__tab-nav-item--active');

      const target = _self.dataset.target;
      
      this.self.querySelector(`#${target}`).classList.add('featured-trips__tab-pane--active');
    },
    removeActiveTabAndPane: function() {
      this.self.querySelector('.featured-trips__tab-nav-item--active').classList.remove('featured-trips__tab-nav-item--active');
      this.self.querySelector('.featured-trips__tab-pane--active').classList.remove('featured-trips__tab-pane--active');
    }
  }.init();

});
