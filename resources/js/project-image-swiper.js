import Swiper from 'swiper';
import { Autoplay } from 'swiper/modules';
import 'swiper/css';

function initProjectImageSwipers() {
    document.querySelectorAll('.project-image-swiper').forEach((el) => {
        const slideCount = el.querySelectorAll('.swiper-slide').length;
        // Loop needs 3+ slides; with 2, Swiper emits a warning and disables loop anyway
        const canLoop = slideCount >= 3;
        const swiper = new Swiper(el, {
            modules: [Autoplay],
            direction: 'horizontal',
            loop: canLoop,
            speed: 600,
            slidesPerView: 1,
            spaceBetween: 0,
            autoplay: slideCount > 1 ? {
                delay: 3000,
                disableOnInteraction: false,
            } : false,
            nested: true,
            on: {
                init() {
                    // Stop autoplay immediately on init so it only runs on hover
                    if (slideCount > 1 && this.autoplay) {
                        this.autoplay.stop();
                    }
                },
            },
        });

        if (slideCount > 1) {
            // Use the image area (not the whole card) so leaving the image stops autoplay
            const imageArea = el.closest('.the-image') || el;
            imageArea.addEventListener('mouseenter', () => swiper.autoplay.start());
            imageArea.addEventListener('mouseleave', () => swiper.autoplay.stop());
        }
    });
}

// Delay 150ms so we run after the main home swiper removes hidden classes from slides (100ms)
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(initProjectImageSwipers, 150);
});
