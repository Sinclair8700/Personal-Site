import Swiper from 'swiper';
import { Navigation, Autoplay } from 'swiper/modules';
import 'swiper/css';
import './project-image-swiper';

const swiper = new Swiper('.projects-carousel', {
    modules: [Navigation, Autoplay],
    direction: 'horizontal',
    loop: true,
    speed: 2000,
    effect: 'slide',
    slidesPerView: 1,
    pagination: false,
    autoplay: {
        delay: 2500,
        pauseOnMouseEnter: true,
        disableOnInteraction: false,
    },
    spaceBetween: 24,

    lazy: true,

    breakpoints: {
        640: {
            slidesPerView: 1,
        },
        768: {
            slidesPerView: 2,
        },
    },
    on: {
        init: function () {
            setTimeout(() => {
                this.slides.forEach(function (slide) {
                    slide.classList.remove('[&:not(.swiper-slide-active)]:hidden');
                    slide.classList.remove('md:[&:not(.swiper-slide-active,.swiper-slide-next)]:hidden');
                });
                this.update();
            }, 100);
        }
    }
});