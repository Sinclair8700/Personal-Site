import Swiper from 'swiper';
import { Autoplay } from 'swiper/modules';
import 'swiper/css';

function initProjectImageSwipers() {
    document.querySelectorAll('.project-image-swiper').forEach((el) => {
        if (el.swiper) return;
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
            pagination: false,
            autoplay: slideCount > 1 ? {
                delay: 3000,
                disableOnInteraction: false,
            } : false,
            nested: true,
            on: {
                init() {
                    if (slideCount > 1 && this.autoplay) this.autoplay.stop();
                    if (slideCount > 1) updatePagination(el);
                },
                slideChange() {
                    updatePagination(el);
                },
            },
        });

        if (slideCount > 1) {
            const card = el.closest('a') || el.closest('.the-image') || el;
            const pagination = card.querySelector('.project-swiper-pagination');
            if (pagination) {
                pagination.querySelectorAll('.project-swiper-pagination-bullet').forEach((bullet) => {
                    bullet.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        const index = parseInt(bullet.dataset.index, 10);
                        if (!Number.isNaN(index)) {
                            swiper.slideToLoop?.(index) ?? swiper.slideTo(index);
                        }
                    });
                });
            }

            const imageArea = el.closest('.the-image') || el;
            imageArea.addEventListener('mouseenter', () => swiper.autoplay.start());
            imageArea.addEventListener('mouseleave', () => swiper.autoplay.stop());
        }
    });
}

function updatePagination(swiperEl) {
    const swiper = swiperEl.swiper;
    if (!swiper) return;
    const activeIndex = swiper.realIndex ?? swiper.activeIndex;
    const card = swiperEl.closest('a') || swiperEl.closest('.the-image') || swiperEl;
    const bullets = card.querySelectorAll('.project-swiper-pagination-bullet');
    bullets.forEach((bullet, i) => {
        bullet.classList.toggle('project-swiper-pagination-bullet-active', i === activeIndex);
    });
}

// Delay 150ms so we run after the main home swiper removes hidden classes from slides (100ms)
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(initProjectImageSwipers, 150);
});
