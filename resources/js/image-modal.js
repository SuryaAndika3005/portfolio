// Fullscreen image preview modal for the project detail page.
// Delegated click handling so it works for any number of gallery triggers
// without attaching a listener per-image.

document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImg');
    const closeBtn = document.getElementById('modalClose');

    if (!modal || !modalImg) return;

    const openModal = (src) => {
        modalImg.src = src;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    };

    const closeModal = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    };

    document.querySelectorAll('[data-modal-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', () => openModal(trigger.dataset.imageSrc));
    });

    closeBtn?.addEventListener('click', closeModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeModal();
    });
});
