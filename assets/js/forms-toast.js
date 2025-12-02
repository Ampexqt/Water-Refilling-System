// Toast notification handler for forms page
document.addEventListener('DOMContentLoaded', function () {
    // Check if there's an alert div on the page
    const alertDiv = document.querySelector('.alert');

    if (alertDiv) {
        // Get the message and type
        const message = alertDiv.textContent.trim();
        const isSuccess = alertDiv.classList.contains('alert-success');
        const isError = alertDiv.classList.contains('alert-error');
        const type = isSuccess ? 'success' : (isError ? 'error' : 'info');

        // Create a unique key for this message based on timestamp to allow same message multiple times
        const timestamp = new Date().getTime();
        const messageKey = 'toast_' + message + '_' + type + '_' + timestamp;

        // Hide the alert div immediately
        alertDiv.style.display = 'none';

        // Show toast notification
        if (typeof showToast === 'function') {
            // Small delay to ensure smooth transition
            setTimeout(function () {
                showToast(message, type);
            }, 100);
        } else {
            console.warn('showToast function not found');
        }
    }
});
