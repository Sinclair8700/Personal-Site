import Swiper from 'swiper';
import { Autoplay } from 'swiper/modules';
import 'swiper/css';

const INIT_DELAY_MS = 200;

function getCard(el) {
    return el.closest('a') || el.closest('.the-image') || el;
}

function updatePagination(swiperEl) {
    const swiper = swiperEl.swiper;
    if (!swiper) return;
    const activeIndex = swiper.realIndex ?? swiper.activeIndex;
    const card = getCard(swiperEl);
    const bullets = card.querySelectorAll('.project-swiper-pagination-bullet');
    bullets.forEach((bullet, i) => {
        bullet.classList.toggle('project-swiper-pagination-bullet-active', i === activeIndex);
    });
}

export function initProjectImageSwipers() {
    document.querySelectorAll('.project-image-swiper').forEach((el) => {
        if (el.swiper) return;

        const slideCount = el.querySelectorAll('.swiper-slide').length;
        const canLoop = slideCount >= 3; // Loop needs 3+; with 2, Swiper disables it anyway
        const card = getCard(el);

        const swiper = new Swiper(el, {
            modules: [Autoplay],
            loop: canLoop,
            speed: 600,
            spaceBetween: 0,
            pagination: false,
            lazyPreloadPrevNext: 1,
            autoplay: slideCount > 1 ? { delay: 3000, disableOnInteraction: false } : false,
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

document.addEventListener('DOMContentLoaded', () => {
    setTimeout(initProjectImageSwipers, INIT_DELAY_MS);
});
