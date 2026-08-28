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

    // The archive topbar and the Project Detail topbar (nav.blade.php's
    // archiveChapters/showBack branches) both keep their own fixed,
    // deliberately compact padding (py-3.5, see nav.blade.php) -- toggling
    // py-4/py-5 on top of that would fight it and make their carefully-
    // sized height drift on scroll, which the brief explicitly doesn't
    // want ("do not dramatically animate the topbar layout"). Shadow/
    // background-opacity-on-scroll still applies there; only the padding
    // toggle is skipped.
    if (nav) {
        const isCompactTopbar = nav.hasAttribute('data-compact-topbar');
        const applyNavState = () => {
            const scrolled = window.scrollY > 50;
            nav.classList.toggle('shadow-sm', scrolled);
            nav.classList.toggle('bg-white/95', scrolled);
            nav.classList.toggle('bg-white/90', !scrolled);
            if (!isCompactTopbar) {
                nav.classList.toggle('py-4', scrolled);
                nav.classList.toggle('py-5', !scrolled);
            }
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

    // Contact form: Send Message stays a real, always-submittable button
    // (progressive enhancement -- no `disabled` in the raw HTML, so the
    // form still works with JS off) but visibly reflects whether the
    // required fields actually validate, so its enabled/disabled state is
    // never ambiguous once JS has loaded.
    const contactForm = document.getElementById('contact-form');
    const contactSubmit = document.getElementById('contact-submit');
    if (contactForm && contactSubmit) {
        const nameField = document.getElementById('contact-name');
        const emailField = document.getElementById('contact-email');
        const messageField = document.getElementById('contact-message');

        const updateSubmitState = () => {
            const ready = nameField.value.trim() !== ''
                && emailField.value.trim() !== '' && emailField.checkValidity()
                && messageField.value.trim() !== '';
            contactSubmit.disabled = !ready;
        };

        [nameField, emailField, messageField].forEach((field) => {
            field.addEventListener('input', updateSubmitState);
        });
        updateSubmitState();
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
