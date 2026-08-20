import Swiper from 'swiper';
import { Navigation, Autoplay } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import { initProjectImageSwipers } from './project-image-swiper';

// Don't auto-advance the carousel for visitors who prefer reduced motion —
// they can still swipe/drag through it manually.
const prefersReducedMotion = window.matchMedia
    && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const projectsCarousel = new Swiper('.projects-carousel', {
    modules: [Autoplay],
    loop: true,
    speed: 2000,
    pagination: false,
    lazyPreloadPrevNext: 2,
    autoplay: prefersReducedMotion ? false : {
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

// Pause auto-advance while a keyboard user is tabbing through the carousel's
// links, and resume when they leave (WCAG 2.2.2 — mouse users get pauseOnMouseEnter).
if (!prefersReducedMotion) {
    const carouselEl = document.querySelector('.projects-carousel');
    if (carouselEl) {
        carouselEl.addEventListener('focusin', () => projectsCarousel.autoplay?.stop());
        carouselEl.addEventListener('focusout', () => projectsCarousel.autoplay?.start());
    }
}