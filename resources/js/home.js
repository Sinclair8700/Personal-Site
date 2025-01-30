import Swiper from 'swiper';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
// import Swiper styles
import 'swiper/css';


console.log('home.js');

const swiper = new Swiper('.swiper', {
    modules: [Navigation, Pagination, Autoplay],
    // Optional parameters
    direction: 'horizontal',
    loop: true,
    speed: 2000,
    autoplay: {
        
        delay: 2500,
        pauseOnMouseEnter: true,
    },
    spaceBetween: 24,

    pagination: {
        el: '.swiper-pagination',
      },
    
      // Navigation arrows
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },

    breakpoints: {
        640: {
            slidesPerView: 1,
        },
        768: {
            slidesPerView: 2,
        },
    },
});