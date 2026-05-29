document.addEventListener('DOMContentLoaded', domEvt => {
  // Gallery
  if( typeof Fancybox !== 'undefined' ) {
    const options = {
      Thumbs: { type: 'classic' },
    };
    Fancybox.bind('[data-fancybox="gallery"]', options);
    document.querySelector('.trip-gallery__toggle-btn')?.addEventListener('click', event => {
      Fancybox.fromSelector('[data-fancybox="gallery"]', 0, options);
    });
  } else {
    console.warn( 'SINGLE PRODUCT PAGE: Fancybox library is missing!' );
  }

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
