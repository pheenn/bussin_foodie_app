/**
 * SweetAlert2 configuration and helpers
 */

// Set default SweetAlert2 configuration
const SwalConfig = {
    confirmButtonColor: '#f97316', // Orange-500
    cancelButtonColor: '#6b7280',   // Gray-500
    buttonsStyling: true,
    reverseButtons: true,
    showClass: {
        popup: 'animate__animated animate__fadeInDown animate__faster'
    },
    hideClass: {
        popup: 'animate__animated animate__fadeOutUp animate__faster'
    }
};

// Apply configuration
if (typeof Swal !== 'undefined') {
    Object.assign(Swal, SwalConfig);
}

// Custom alert functions
const SweetAlert = {
    success: function(title, text, timer = 3000) {
        if (typeof Swal === 'undefined') {
            console.log('Success:', title, text);
            return;
        }
        return Swal.fire({
            icon: 'success',
            title: title,
            text: text,
            timer: timer,
            showConfirmButton: false
        });
    },
    
    error: function(title, text) {
        if (typeof Swal === 'undefined') {
            console.log('Error:', title, text);
            return;
        }
        return Swal.fire({
            icon: 'error',
            title: title,
            text: text
        });
    },
    
    warning: function(title, text, confirmText = 'OK') {
        if (typeof Swal === 'undefined') {
            console.log('Warning:', title, text);
            return;
        }
        return Swal.fire({
            icon: 'warning',
            title: title,
            text: text,
            confirmButtonText: confirmText
        });
    },
    
    confirm: function(title, text, confirmText = 'Yes', cancelText = 'Cancel') {
        if (typeof Swal === 'undefined') {
            console.log('Confirm:', title, text);
            return Promise.resolve({ isConfirmed: true });
        }
        return Swal.fire({
            icon: 'question',
            title: title,
            text: text,
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: cancelText,
            reverseButtons: true
        });
    },
    
    close: function() {
        if (typeof Swal !== 'undefined') {
            Swal.close();
        }
    }
};

// Make SweetAlert helpers globally available
window.SweetAlert = SweetAlert;