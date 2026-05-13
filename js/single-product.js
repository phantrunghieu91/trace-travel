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
  document.querySelectorAll('.faqs__item').forEach(item => {
    item.addEventListener('click', event => {
      document.querySelector('.faqs__item.current').classList.remove('current');
      item.classList.add('current');
    });
  });

  // Handle related trips carousel
  const relatedTrips = new Swiper('.related-trips__content.swiper', {
    slidesPerView: 1,
    loop: true,
    breakpoints: {
      550: {
        slidesPerView: 2,
        spaceBetween: 20,
      },
      850: {
        slidesPerView: 4,
        spaceBetween: 30,
      },
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });

  // handle scroll event for tab nav
  document.querySelectorAll('.nav-tabs__tab-control').forEach(control => {
    control.addEventListener('click', event => {
      event.preventDefault();
      document.querySelector('.nav-tabs__tab-control.current').classList.remove('current');
      control.classList.add('current');
      document.querySelector(`${control.getAttribute('href')}`).scrollIntoView({ behavior: 'smooth' });
    });
  });

  // Display field of form in order
  // let isDateTimeSelected = { time: false, date: false };
  // const bookingDate = document.querySelector('#booking-date');
  // const bookingTime = document.querySelector('#booking-time');
  // const bookingCarType = document.querySelector('.booking-form__control-wrap.car-types');

  // // When date, time choose show car type
  // const checkSelected = (condition, type) => {
  //   if (type == 'date') isDateTimeSelected.date = condition ? true : false;
  //   else isDateTimeSelected.time = condition ? true : false;
  //   if (isDateTimeSelected.date && isDateTimeSelected.time) {
  //     bookingCarType.classList.remove('hide');
  //   }
  // };
  // bookingDate.addEventListener('change', event => {
  //   checkSelected(bookingDate.value !== '', 'date');
  // });
  // bookingTime.addEventListener('change', event => {
  //   checkSelected(bookingTime.value !== '', 'time');
  // });

  // // When car type selected, summary and addon show
  // bookingCarType.addEventListener('change', event => {
  //   if (bookingCarType.value != 'default') {
  //     document.querySelector('.booking-form__summary').classList.remove('hide');
  //     document.querySelector('.booking-form__control-wrap.add-ons').classList.remove('hide');
  //     document.querySelector('.booking-form__submit-btn').classList.remove('hide');
  //   }
  // });
});
