// Toast notification handler for forms page
document.addEventListener('DOMContentLoaded', function () {
    // Check if there's an alert div on the page
    const alertDiv = document.querySelector('.alert');

    if (alertDiv) {
        // Get the message and type
        const message = alertDiv.textContent.trim();
        const type = alertDiv.classList.contains('alert-success') ? 'success' : 'error';

        // Create a unique key for this message
        const messageKey = 'toast_' + message + '_' + type;

        // Check if we've already shown this toast
        const alreadyShown = sessionStorage.getItem(messageKey);

        // Hide the alert div
        alertDiv.style.display = 'none';

        // Only show toast if we haven't shown it yet
        if (!alreadyShown && typeof showToast === 'function') {
            showToast(message, type);
            // Mark as shown
            sessionStorage.setItem(messageKey, 'true');

            // Clear the flag after a short delay to allow showing again for new actions
            setTimeout(function () {
                sessionStorage.removeItem(messageKey);
            }, 1000);
        }
    }
});
