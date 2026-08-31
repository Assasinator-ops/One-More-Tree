/**
 * OneMoreTree Admin Authentication Module
 * Handles login, logout, and authentication state management
 */

class AdminAuth {
    constructor(config) {
        this.config = config;
        this.init();
    }

    init() {
        if (!this.config.isLoggedIn) {
            this.initLoginForm();
        } else {
            this.initLogout();
        }
    }

    initLoginForm() {
        const loginForm = document.getElementById('admin-login-form');
        const usernameInput = document.getElementById('login-username');
        const passwordInput = document.getElementById('login-password');
        const togglePasswordBtn = document.getElementById('toggle-password-btn');
        const eyeIcon = document.getElementById('eye-icon');
        const errorBox = document.getElementById('login-error-box');
        const errorMsg = document.getElementById('login-error-msg');
        const submitBtn = document.getElementById('login-submit-btn');
        const btnText = document.getElementById('login-btn-text');

        if (togglePasswordBtn && passwordInput) {
            this.initPasswordToggle(togglePasswordBtn, passwordInput, eyeIcon);
        }

        if (loginForm) {
            this.initLoginSubmit(loginForm, usernameInput, passwordInput, errorBox, errorMsg, submitBtn, btnText);
        }
    }

    initPasswordToggle(toggleBtn, passwordInput, eyeIcon) {
        let visible = false;
        toggleBtn.addEventListener('click', () => {
            visible = !visible;
            passwordInput.type = visible ? 'text' : 'password';
            if (eyeIcon) {
                eyeIcon.innerHTML = visible 
                    ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>`
                    : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542 7z"></path>`;
            }
        });
    }

    async initLoginSubmit(form, usernameInput, passwordInput, errorBox, errorMsg, submitBtn, btnText) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (errorBox) errorBox.classList.add('hidden');
            if (submitBtn) submitBtn.disabled = true;
            
            if (btnText) {
                btnText.innerHTML = `
                    <div class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
                    <span>Authenticating...</span>
                `;
            }

            try {
                const res = await fetch(`${this.config.restUrl}auth/login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': this.config.restNonce
                    },
                    body: JSON.stringify({
                        username: usernameInput.value.trim(),
                        password: passwordInput.value
                    })
                });

                const data = await res.json();
                if (!res.ok || !data.ok) {
                    this.showError(errorMsg, errorBox, data.message || 'Invalid administrator credentials.', submitBtn, btnText);
                    return;
                }

                this.showSuccess(btnText);
                window.location.reload();
            } catch (err) {
                this.showError(errorMsg, errorBox, 'Unable to sign in. Please try again.', submitBtn, btnText);
            }
        });
    }

    showError(errorMsg, errorBox, message, submitBtn, btnText) {
        if (errorMsg) errorMsg.textContent = message;
        if (errorBox) {
            errorBox.classList.remove('hidden');
            errorBox.classList.add('flex');
        }
        if (submitBtn) submitBtn.disabled = false;
        if (btnText) {
            btnText.innerHTML = `
                <span>Access Admin Console</span>
                <svg class="w-4 h-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                </svg>
            `;
        }
    }

    showSuccess(btnText) {
        if (btnText) {
            btnText.innerHTML = `
                <svg class="w-4 h-4 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Access Granted</span>
            `;
        }
    }

    initLogout() {
        const logoutBtn = document.getElementById('admin-logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async () => {
                try {
                    await fetch(`${this.config.restUrl}auth/logout`, {
                        method: 'POST',
                        headers: { 'X-WP-Nonce': this.config.restNonce }
                    });
                } finally {
                    window.location.reload();
                }
            });
        }
    }
}

// Register to global scope
window.AdminAuth = AdminAuth;