// Sticky nav shadow-on-scroll + mobile menu toggle.
// Shared across all pages via the layout. Guarded so it no-ops
// safely on pages that don't render the full nav (e.g. show/back-nav variant).

// Top loading bar starts filling immediately, doesn't wait for DOMContentLoaded.
const loadingBar = document.getElementById('page-loading-bar');
if (loadingBar) {
    requestAnimationFrame(() => {
        loadingBar.style.width = '75%';
    });
    window.addEventListener('load', () => {
        loadingBar.style.width = '100%';
        setTimeout(() => loadingBar.classList.add('is-done'), 200);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Fade images in once they've actually loaded rather than popping in.
    document.querySelectorAll('img.lazy-fade').forEach((img) => {
        if (img.complete) {
            img.classList.add('is-loaded');
        } else {
            img.addEventListener('load', () => img.classList.add('is-loaded'), { once: true });
        }
    });

    const nav = document.getElementById('main-nav');
    const menuBtn = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');

    if (nav) {
        const applyNavState = () => {
            const scrolled = window.scrollY > 50;
            nav.classList.toggle('shadow-sm', scrolled);
            nav.classList.toggle('bg-white/95', scrolled);
            nav.classList.toggle('bg-white/90', !scrolled);
            nav.classList.toggle('py-4', scrolled);
            nav.classList.toggle('py-5', !scrolled);
        };

        applyNavState();
        window.addEventListener('scroll', applyNavState, { passive: true });
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

    // Hero role rotator: cycles the text of #role-rotator through the
    // comma-separated roles in its data-roles attribute. No-ops on pages
    // without the element.
    const roleEl = document.getElementById('role-rotator');
    if (roleEl) {
        const roles = (roleEl.dataset.roles || '')
            .split(',')
            .map((role) => role.trim())
            .filter(Boolean);

        if (roles.length > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            let index = 0;
            setInterval(() => {
                index = (index + 1) % roles.length;
                roleEl.style.opacity = '0';
                setTimeout(() => {
                    roleEl.textContent = roles[index];
                    roleEl.style.opacity = '1';
                }, 300);
            }, 2600);
        }
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
