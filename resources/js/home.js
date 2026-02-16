import Swiper from 'swiper';
import { Navigation, Autoplay } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import { initProjectImageSwipers } from './project-image-swiper';

new Swiper('.projects-carousel', {
    modules: [Autoplay],
    loop: true,
    speed: 2000,
    pagination: false,
    lazyPreloadPrevNext: 2,
    autoplay: {
        delay: 2500,
        pauseOnMouseEnter: true,
        disableOnInteraction: false,
    },
    spaceBetween: 24,
    breakpoints: {
        640: { slidesPerView: 1 },
        768: { slidesPerView: 2 },
    },
    on: {
        init() {
            setTimeout(() => {
                this.slides.forEach((slide) => {
                    slide.classList.remove('[&:not(.swiper-slide-active)]:hidden');
                    slide.classList.remove('md:[&:not(.swiper-slide-active,.swiper-slide-next)]:hidden');
                });
                this.update();
                initProjectImageSwipers();
            }, 100);
        },
    },
});