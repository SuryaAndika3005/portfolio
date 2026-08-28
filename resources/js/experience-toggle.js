// Experience section: expand/collapse a professional entry's short
// description in place. Only entries with a description render a toggle
// button at all (see index.blade.php), so this only ever wires up rows
// that actually have something to reveal.

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-experience-toggle]').forEach((button) => {
        const panel = document.getElementById(button.getAttribute('aria-controls'));
        const icon = button.querySelector('[data-toggle-icon]');
        if (!panel) return;

        button.addEventListener('click', () => {
            const expanded = button.getAttribute('aria-expanded') === 'true';
            button.setAttribute('aria-expanded', String(!expanded));
            panel.classList.toggle('is-expanded', !expanded);
            if (icon) icon.textContent = expanded ? '+' : '×';
        });
    });
});
