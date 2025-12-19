/**
 * Real-time form validation for Bussin' Foodie Dashboard
 */

class FormValidator {
    constructor(formId) {
        this.form = document.getElementById(formId);
        if (!this.form) return;
        
        this.init();
    }
    
    init() {
        // Add real-time validation to inputs
        const inputs = this.form.querySelectorAll('input[required], textarea[required], select[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearError(input));
        });
        
        // Add submit validation
        this.form.addEventListener('submit', (e) => this.validateForm(e));
    }
    
    validateField(field) {
        const value = field.value.trim();
        const errorId = field.id + 'Error';
        const errorElement = document.getElementById(errorId);
        
        let error = '';
        
        if (field.hasAttribute('required') && !value) {
            error = 'This field is required';
        } else if (field.type === 'email' && value && !this.isValidEmail(value)) {
            error = 'Please enter a valid email address';
        } else if (field.type === 'url' && value && !this.isValidUrl(value)) {
            error = 'Please enter a valid URL';
        } else if (field.type === 'number') {
            const min = field.getAttribute('min');
            const max = field.getAttribute('max');
            
            if (min && parseFloat(value) < parseFloat(min)) {
                error = `Minimum value is ${min}`;
            } else if (max && parseFloat(value) > parseFloat(max)) {
                error = `Maximum value is ${max}`;
            }
        } else if (field.id === 'name' && value.length < 3) {
            error = 'Name must be at least 3 characters';
        }
        
        if (error && errorElement) {
            errorElement.textContent = error;
            errorElement.classList.remove('hidden');
            field.classList.add('border-red-500');
            field.classList.remove('border-green-500');
        } else if (errorElement) {
            this.clearError(field);
        }
    }
    
    clearError(field) {
        const errorId = field.id + 'Error';
        const errorElement = document.getElementById(errorId);
        
        if (errorElement) {
            errorElement.classList.add('hidden');
            field.classList.remove('border-red-500');
            
            // Add green border for valid fields
            if (field.value.trim()) {
                field.classList.add('border-green-500');
            }
        }
    }
    
    validateForm(e) {
        let isValid = true;
        const inputs = this.form.querySelectorAll('input[required], textarea[required], select[required]');
        
        inputs.forEach(input => {
            const value = input.value.trim();
            
            if (!value) {
                this.validateField(input);
                isValid = false;
            }
        });
        
        // Price validation
        const priceInput = this.form.querySelector('#price');
        if (priceInput && parseFloat(priceInput.value) <= 0) {
            isValid = false;
            if (!document.getElementById('priceError')) {
                const errorDiv = document.createElement('div');
                errorDiv.id = 'priceError';
                errorDiv.className = 'mt-1 text-sm text-red-600';
                errorDiv.textContent = 'Price must be greater than 0';
                priceInput.parentNode.appendChild(errorDiv);
            }
        }
        
        if (!isValid) {
            e.preventDefault();
            
            // Scroll to first error
            const firstError = this.form.querySelector('.border-red-500');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    }
    
    isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }
    
    isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }
}

// Initialize validators when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize quick product form validator
    if (document.getElementById('quickProductForm')) {
        new FormValidator('quickProductForm');
    }
});