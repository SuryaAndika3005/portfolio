// Sticky nav shadow-on-scroll + mobile menu toggle.
// Shared across all pages via the layout — guarded so it no-ops
// safely on pages that don't render the full nav (e.g. show/back-nav variant).

document.addEventListener('DOMContentLoaded', () => {
    const nav = document.getElementById('main-nav');
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');

    if (nav) {
        window.addEventListener('scroll', () => {
            const scrolled = window.scrollY > 50;
            nav.classList.toggle('bg-white/80', scrolled);
            nav.classList.toggle('shadow-sm', scrolled);
            nav.classList.toggle('py-4', scrolled);
            nav.classList.toggle('py-6', !scrolled);
        });
    }

    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.contains('flex');
            mobileMenu.classList.toggle('hidden', isOpen);
            mobileMenu.classList.toggle('flex', !isOpen);
            menuBtn.setAttribute('aria-expanded', String(!isOpen));
            menuIcon?.classList.toggle('rotate-90');
            document.body.style.overflow = isOpen ? 'auto' : 'hidden';
        });
    }

    // Scroll-reveal: elements with class="reveal" fade/slide in once they
    // enter the viewport. Falls back to showing everything immediately if
    // IntersectionObserver isn't available.
    const revealEls = document.querySelectorAll('.reveal');
    if (revealEls.length) {
        if (!('IntersectionObserver' in window)) {
            revealEls.forEach((el) => el.classList.add('is-visible'));
        } else {
            const observer = new IntersectionObserver(
                (entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            observer.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.15, rootMargin: '0px 0px -60px 0px' }
            );

            revealEls.forEach((el) => observer.observe(el));
        }
    }
});
