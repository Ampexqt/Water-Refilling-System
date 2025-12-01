// Force modal styles to override cached CSS
document.addEventListener('DOMContentLoaded', function () {
    console.log('Modal fix script loaded');

    const modal = document.querySelector('#addOrderModal .modal');
    const modalBody = document.querySelector('#addOrderModal .modal-body');
    const modalFooter = document.querySelector('#addOrderModal .modal-footer');

    if (modal) {
        console.log('Applying modal styles');
        modal.style.setProperty('overflow', 'hidden', 'important');
        modal.style.setProperty('max-height', '70vh', 'important');
        modal.style.setProperty('display', 'flex', 'important');
        modal.style.setProperty('flex-direction', 'column', 'important');
    }

    if (modalBody) {
        console.log('Applying modal-body styles');
        modalBody.style.setProperty('flex', '1 1 auto', 'important');
        modalBody.style.setProperty('overflow-y', 'auto', 'important');
        modalBody.style.setProperty('overflow-x', 'hidden', 'important');
        modalBody.style.setProperty('min-height', '0', 'important');
        modalBody.style.setProperty('max-height', 'calc(70vh - 180px)', 'important');
    }

    if (modalFooter) {
        console.log('Applying modal-footer styles');
        modalFooter.style.setProperty('flex-shrink', '0', 'important');
        modalFooter.style.setProperty('flex-grow', '0', 'important');
        modalFooter.style.setProperty('background', 'white', 'important');
        modalFooter.style.setProperty('padding', '1.5rem', 'important');
        modalFooter.style.setProperty('border-top', '1px solid #B2DFDB', 'important');
        modalFooter.style.setProperty('display', 'flex', 'important');
        modalFooter.style.setProperty('justify-content', 'flex-end', 'important');
        modalFooter.style.setProperty('gap', '0.75rem', 'important');
    }

    console.log('Modal fix applied');
});
