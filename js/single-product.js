document.addEventListener('DOMContentLoaded', domEvt => {
  const galleryDialog = document.querySelector('#trip-gallery-full');
  const gallerySwiper = new Swiper('#trip-gallery-full > .swiper', {
    slidesPerView: 1,
    loop: true,
    centeredSlides: true,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });

  // Function open gallery modal
  const openGalleryModal = () => {
    galleryDialog.showModal();
    document.documentElement.classList.add('gallery-show');
  };
  if (document.querySelector('.trip-gallery__toggle-btn'))
    document.querySelector('.trip-gallery__toggle-btn').addEventListener('click', toggleEvt => {
      toggleEvt.preventDefault();
      openGalleryModal();
    });

  // When click on any pics in gallery will open modal with that pic at full size
  document.querySelectorAll('.previews__item').forEach(img => {
    img.addEventListener('click', evt => {
      openGalleryModal();
      let index = document.querySelector(`.swiper-slide[data-img-id="${img.dataset.imgId}"]`).dataset.swiperSlideIndex;
      gallerySwiper.slideTo(index, 300, true);
    });
  });

  // Close gallery dialog
  const closeGalleryDialog = () => {
    galleryDialog.close();
    document.documentElement.classList.remove('gallery-show');
  };
  if (document.querySelector('.trip-gallery__dialog-close-btn'))
    document.querySelector('.trip-gallery__dialog-close-btn').addEventListener('click', event => {
      event.preventDefault();
      closeGalleryDialog();
    });
  // Handle gallery dialog close on when user press Esc key
  galleryDialog.addEventListener('keydown', event => {
    if (event.key === 'Escape') closeGalleryDialog();
  });

  // Handle accordion faqs
  [...document.querySelectorAll('.faqs__item')].forEach((item, idx, items) => {
    item.addEventListener('click', event => {
      items.find( accordion => accordion.classList.contains('current'))?.classList.remove('current');
      item.classList.add('current');
    });
  });

  // Handle related trips carousel
  const relatedTrips = new Swiper('.related-trips__content.swiper', {
    slidesPerView: 1,
    spaceBetween: 10,
    rewind: true,
    breakpoints: {
      550: {
        slidesPerView: 2,
        spaceBetween: 20,
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
  });

  // handle scroll event for tab nav
  const tabNavItems = {};
  const tabPanels = {};
  [...document.querySelectorAll('.nav-tabs__tab-control')].forEach((control) => {
    const panelId = control.getAttribute('href');
    const panel = document.querySelector( panelId );
    if( panel ) tabPanels[panelId.slice(1)] = panel;
    tabNavItems[panelId.slice(1)] = control;
    control.addEventListener('click', event => {
      event.preventDefault();
      changeCurrentTab( control );
      tabPanels[panelId.slice(1)].scrollIntoView({ behavior: 'smooth' });
    });
  });
  const changeCurrentTab = ( navItem ) => {
    Object.values(tabNavItems).find( c => c.classList.contains( 'current' ))?.classList.remove('current');
    navItem.classList.add('current');
  };
  const tabObserver = new IntersectionObserver( entries => {
    for( const entry of entries ) {
      if( entry.isIntersecting ) {
        changeCurrentTab( tabNavItems[entry.target.id] );
      }
    }
  }, {
    threshold: .5,
  });
  Object.values(tabPanels).forEach( panel => {
    tabObserver.observe( panel );
  });
});
