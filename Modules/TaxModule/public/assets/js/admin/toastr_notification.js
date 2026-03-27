'use strict';
function sent_notification(type, message) {
    if (typeof toastr === 'undefined') {
        console.warn('Toastr is not available.');
        return;
    }

    const methodTypes = {
        successMessage: 'success',
        infoMessage: 'info',
        warningMessage: 'warning',
        errorMessage: 'error'
    };

    const method = methodTypes[type] || 'info';

    toastr[method](message, null, {
        closeButton: true,
        progressBar: true
    });
}
