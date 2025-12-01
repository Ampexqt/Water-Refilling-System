// Modal functionality - Make globally available
console.log('Modal.js loaded');

window.openModal = function (modalId) {
    console.log('openModal called with:', modalId);
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Focus first input in modal
        const firstInput = modal.querySelector('input, textarea, select');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    } else {
        console.error('Modal not found:', modalId);
    }
}

window.closeModal = function (modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = 'auto';

        // Reset form if exists
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
            // Clear any error messages
            const errors = form.querySelectorAll('.form-error');
            errors.forEach(error => error.textContent = '');
        }
    }
}

// Close modal on overlay click
document.addEventListener('click', function (e) {
    if (e.target.classList.contains('modal-overlay')) {
        closeModal(e.target.id);
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        const activeModals = document.querySelectorAll('.modal-overlay.active');
        activeModals.forEach(modal => {
            closeModal(modal.id);
        });
    }
});

// Setup modal close buttons
document.addEventListener('DOMContentLoaded', function () {
    const closeButtons = document.querySelectorAll('[data-close-modal]');
    closeButtons.forEach(button => {
        button.addEventListener('click', function () {
            const modalId = this.getAttribute('data-close-modal');
            closeModal(modalId);
        });
    });

    // Setup modal open buttons
    const openButtons = document.querySelectorAll('[data-open-modal]');
    openButtons.forEach(button => {
        button.addEventListener('click', function () {
            const modalId = this.getAttribute('data-open-modal');
            openModal(modalId);
        });
    });
});

