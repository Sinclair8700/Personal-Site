import Swiper from 'swiper';
import { Autoplay } from 'swiper/modules';
import 'swiper/css';

function initProjectImageSwipers() {
    document.querySelectorAll('.project-image-swiper').forEach((el) => {
        const slideCount = el.querySelectorAll('.swiper-slide').length;
        const swiper = new Swiper(el, {
            modules: [Autoplay],
            direction: 'horizontal',
            loop: slideCount > 1,
            speed: 600,
            slidesPerView: 1,
            spaceBetween: 0,
            autoplay: slideCount > 1 ? {
                delay: 3000,
                disableOnInteraction: false,
            } : false,
            nested: true,
        });

        if (slideCount > 1) {
            swiper.autoplay.stop();
            el.addEventListener('mouseenter', () => swiper.autoplay.start());
            el.addEventListener('mouseleave', () => swiper.autoplay.stop());
        }
    });
}

// Delay 150ms so we run after the main home swiper removes hidden classes from slides (100ms)
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(initProjectImageSwipers, 150);
});
