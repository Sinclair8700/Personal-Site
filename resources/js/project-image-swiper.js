import Swiper from 'swiper';
import { Autoplay } from 'swiper/modules';
import 'swiper/css';

function initProjectImageSwipers() {
    document.querySelectorAll('.project-image-swiper').forEach((el) => {
        const slideCount = el.querySelectorAll('.swiper-slide').length;
        new Swiper(el, {
            modules: [Autoplay],
            direction: 'horizontal',
            loop: slideCount > 1,
            speed: 600,
            slidesPerView: 1,
            spaceBetween: 0,
            autoplay: slideCount > 1 ? {
                delay: 3000,
                pauseOnMouseEnter: true,
                disableOnInteraction: false,
            } : false,
            nested: true,
        });
    });
}

// Delay 150ms so we run after the main home swiper removes hidden classes from slides (100ms)
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(initProjectImageSwipers, 150);
});
