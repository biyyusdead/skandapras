import Swiper from 'swiper';
import { Pagination, Autoplay } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/pagination';

/* ============================================================
   HOME PAGE — Custom Scripts
   ============================================================ */

document.addEventListener('DOMContentLoaded', function () {

    // ============================================================
    // Scroll reveal
    // ============================================================
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('show');
                revealObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px'
    });

    document.querySelectorAll('.scroll-reveal').forEach(el => revealObserver.observe(el));


    // ============================================================
    // Counter animation
    // ============================================================
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;

            const counter   = entry.target;
            const target    = parseInt(counter.dataset.target) || 0;
            const duration  = 1800;
            const startTime = performance.now();

            function animate(currentTime) {
                const progress = Math.min((currentTime - startTime) / duration, 1);
                const eased    = 1 - Math.pow(1 - progress, 4);
                const value    = Math.floor(eased * target);
                counter.textContent = value.toLocaleString('id-ID') + '+';

                if (progress < 1) {
                    requestAnimationFrame(animate);
                } else {
                    counter.textContent = target.toLocaleString('id-ID') + '+';
                }
            }

            counter.textContent = '0+';
            requestAnimationFrame(animate);
            counterObserver.unobserve(counter);
        });
    }, {
        threshold: 0.5
    });

    document.querySelectorAll('.counter').forEach(el => counterObserver.observe(el));


    // ============================================================
    // ALUMNI SWIPER — MOBILE ONLY
    // ============================================================
    if (document.querySelector('.alumni-swiper')) {
        new Swiper('.alumni-swiper', {
            modules: [Pagination, Autoplay],
            slidesPerView: 1.15,
            spaceBetween: 16,
            centeredSlides: false,
            grabCursor: true,
            pagination: {
                el: '.alumni-pagination',
                clickable: true,
                bulletClass: 'swiper-pagination-bullet !bg-primary-600',
                bulletActiveClass: 'swiper-pagination-bullet-active !w-6 !rounded-full',
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            breakpoints: {
                480: { slidesPerView: 1.4, spaceBetween: 16 },
                640: { slidesPerView: 1.8, spaceBetween: 16 },
            },
        });
    }


    // ============================================================
    // NEWS SWIPER — MOBILE ONLY
    // ============================================================
    if (document.querySelector('.news-swiper')) {
        new Swiper('.news-swiper', {
            modules: [Pagination, Autoplay],
            slidesPerView: 1.15,
            spaceBetween: 16,
            grabCursor: true,
            pagination: {
                el: '.news-pagination',
                clickable: true,
                bulletClass: 'swiper-pagination-bullet !bg-primary-600',
                bulletActiveClass: 'swiper-pagination-bullet-active !w-6 !rounded-full',
            },
            autoplay: {
                delay: 6000,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            breakpoints: {
                480: { slidesPerView: 1.4, spaceBetween: 16 },
                640: { slidesPerView: 1.8, spaceBetween: 16 },
            },
        });
    }

    // ============================================================
// SWIPER — REASONS (Kenapa Harus Sekolah di Sini?)
// ============================================================
if (document.querySelector('.reasons-swiper')) {
    new Swiper('.reasons-swiper', {
        slidesPerView: 1.15,
        spaceBetween: 16,
        grabCursor: true,
        centeredSlides: false,
        pagination: {
            el: '.reasons-pagination',
            clickable: true,
            dynamicBullets: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 1.5,
                spaceBetween: 20,
            },
            768: {
                slidesPerView: 2.2,
                spaceBetween: 24,
                pagination: false,
            },
            1024: {
                slidesPerView: 3,
                spaceBetween: 24,
                pagination: false,
            },
        },
    });
}

// ============================================================
// SWIPER — DEPARTMENTS (Kompetensi Keahlian)
// ============================================================
if (document.querySelector('.departments-swiper')) {
    new Swiper('.departments-swiper', {
        slidesPerView: 1.15,
        spaceBetween: 16,
        grabCursor: true,
        centeredSlides: false,
        pagination: {
            el: '.departments-pagination',
            clickable: true,
            dynamicBullets: true,
        },
        breakpoints: {
            640: {
                slidesPerView: 1.5,
                spaceBetween: 20,
            },
            768: {
                slidesPerView: 2.2,
                spaceBetween: 24,
                pagination: false,
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 24,
                pagination: false,
            },
        },
    });
}

    // ============================================================
    // FAQ ACCORDION
    // ============================================================
    (function () {
        const items = document.querySelectorAll('.faq-item');
        if (!items.length) return;

        items.forEach(item => {
            const trigger = item.querySelector('.faq-trigger');
            const panel = item.querySelector('.faq-panel');
            const icon = item.querySelector('.faq-icon svg');
            if (!trigger || !panel) return;

            trigger.addEventListener('click', () => {
                const isOpen = trigger.getAttribute('aria-expanded') === 'true';

                // Tutup yang lain (accordion mode)
                items.forEach(other => {
                    if (other === item) return;
                    const otherTrigger = other.querySelector('.faq-trigger');
                    const otherPanel = other.querySelector('.faq-panel');
                    const otherIcon = other.querySelector('.faq-icon svg');
                    if (!otherTrigger || !otherPanel) return;
                    otherTrigger.setAttribute('aria-expanded', 'false');
                    otherPanel.style.maxHeight = '0px';
                    otherPanel.style.opacity = '0';
                    other.classList.remove('ring-2', 'ring-primary-200', 'shadow-lg');
                    if (otherIcon) otherIcon.style.transform = 'rotate(0deg)';
                });

                // Toggle current
                if (isOpen) {
                    trigger.setAttribute('aria-expanded', 'false');
                    panel.style.maxHeight = '0px';
                    panel.style.opacity = '0';
                    item.classList.remove('ring-2', 'ring-primary-200', 'shadow-lg');
                    if (icon) icon.style.transform = 'rotate(0deg)';
                } else {
                    trigger.setAttribute('aria-expanded', 'true');
                    panel.style.maxHeight = panel.scrollHeight + 'px';
                    panel.style.opacity = '1';
                    item.classList.add('ring-2', 'ring-primary-200', 'shadow-lg');
                    if (icon) icon.style.transform = 'rotate(180deg)';
                }
            });
        });

        // Set transition untuk icon
        document.querySelectorAll('.faq-icon svg').forEach(icon => {
            icon.style.transition = 'transform 0.3s cubic-bezier(0.16, 1, 0.3, 1)';
        });

        // Handle resize — recalc maxHeight untuk panel yang terbuka
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                document.querySelectorAll('.faq-trigger[aria-expanded="true"]').forEach(trigger => {
                    const panel = trigger.closest('.faq-item')?.querySelector('.faq-panel');
                    if (panel) panel.style.maxHeight = panel.scrollHeight + 'px';
                });
            }, 150);
        });
    })();
});


// ============================================================
// Lucide init — dipanggil setelah Alpine ready
// ============================================================
document.addEventListener('alpine:initialized', () => {
    if (typeof window.initLucide === 'function') {
        window.initLucide();
    }
});