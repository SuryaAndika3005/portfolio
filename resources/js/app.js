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
});
