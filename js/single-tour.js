document.addEventListener('DOMContentLoaded', domEvt => {
  const galleryDialog = document.querySelector('#tour-gallery-full');
  const gallerySwiper = new Swiper('#tour-gallery-full > .swiper', {
    slidesPerView: 1,
    loop: 1,
    centeredSlides: true,
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });

  // Function open modal
  const openModal = () => {
    galleryDialog.showModal();
    document.documentElement.classList.add('gallery-show');
  };
  document.querySelector('.tour-gallery__toggle-btn').addEventListener('click', toggleEvt => {
    toggleEvt.preventDefault();
    openModal();
  });

  // When click on any pics in gallery will open modal with that pic at full size
  document.querySelectorAll('.previews__item').forEach(img => {
    img.addEventListener('click', evt => {
      openModal();
      let index = document.querySelector(`.swiper-slide[data-img-id="${img.dataset.imgId}"]`).dataset.swiperSlideIndex;
      gallerySwiper.slideTo(index, 300, false);
    });
  });

  // Close dialog
  document.querySelector('.tour-gallery__dialog-close-btn').addEventListener('click', event => {
    event.preventDefault();
    galleryDialog.close();
    document.documentElement.classList.remove('gallery-show');
  });
});
