/**
 * OneMoreTree Admin Forms Module
 * Handles form validation, submission, and UI feedback
 */

class AdminForms {
    constructor() {
        this.init();
    }

    init() {
        this.initFormValidation();
        this.initFormFeedback();
    }

    initFormValidation() {
        // Add client-side validation to forms
        const forms = document.querySelectorAll('form[data-validate]');
        forms.forEach(form => {
            form.addEventListener('submit', (e) => this.handleFormSubmit(e, form));
        });
    }

    handleFormSubmit(event, form) {
        const isValid = this.validateForm(form);
        if (!isValid) {
            event.preventDefault();
            this.showFormErrors(form);
        }
    }

    validateForm(form) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-500');
            } else {
                field.classList.remove('border-red-500');
            }
        });

        return isValid;
    }

    showFormErrors(form) {
        const errorContainer = form.querySelector('[data-form-errors]');
        if (errorContainer) {
            errorContainer.classList.remove('hidden');
            errorContainer.innerHTML = '<p class="text-red-600 text-sm">Please fill in all required fields.</p>';
        }
    }

    initFormFeedback() {
        // Add loading states and success feedback to forms
        const submitButtons = document.querySelectorAll('button[type="submit"]');
        submitButtons.forEach(button => {
            button.addEventListener('click', () => this.showButtonLoading(button));
        });
    }

    showButtonLoading(button) {
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = `
            <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
            <span>Saving...</span>
        `;

        // Reset after 3 seconds (in case of no response)
        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = originalText;
        }, 3000);
    }

    showButtonSuccess(button, message = 'Saved!') {
        button.disabled = false;
        button.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span>${message}</span>
        `;

        setTimeout(() => {
            button.innerHTML = 'Save Changes';
        }, 2000);
    }

    showButtonError(button, message = 'Error') {
        button.disabled = false;
        button.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <span>${message}</span>
        `;

        setTimeout(() => {
            button.innerHTML = 'Save Changes';
        }, 2000);
    }
}

// Register to global scope
window.AdminForms = AdminForms;