// Category filter for the project archive page.
// This is now the ONLY copy of this logic in the project — previously
// duplicated (one live, one dead) across index.blade.php and this page.

document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.filter-btn');
    const items = document.querySelectorAll('.project-item');
    const emptyState = document.getElementById('filter-empty-state');

    if (!buttons.length || !items.length) return;

    buttons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const slug = btn.dataset.filter;
            let visibleCount = 0;

            buttons.forEach((b) => {
                const isActive = b === btn;
                b.classList.toggle('active-filter', isActive);
                b.classList.toggle('text-slate-500', !isActive);
                b.setAttribute('aria-pressed', String(isActive));
            });

            items.forEach((item) => {
                const matches = slug === 'all' || item.dataset.category === slug;
                if (matches) visibleCount++;

                item.style.display = matches ? 'block' : 'none';
                item.style.opacity = matches ? '1' : '0';
                item.style.transform = matches ? 'scale(1)' : 'scale(0.95)';
            });

            emptyState?.classList.toggle('hidden', visibleCount > 0);
        });
    });
});
