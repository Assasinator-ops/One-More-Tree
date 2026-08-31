document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('footer-newsletter-form');
    const statusDiv = document.getElementById('footer-newsletter-status');
    
    if (form && statusDiv && typeof omtData !== 'undefined') {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const emailInput = form.querySelector('input[name="email"]');
            const email = emailInput ? emailInput.value.trim() : '';
            
            // Validation - exact match to Next.js
            if (!email || !email.includes('@')) {
                statusDiv.textContent = 'Please enter a valid email address.';
                statusDiv.classList.remove('hidden');
                return;
            }
            
            // AJAX submission
            const formData = new FormData();
            formData.append('action', 'omt_newsletter_subscribe');
            formData.append('email', email);
            formData.append('nonce', omtData.nonce);
            
            try {
                const response = await fetch(omtData.ajaxUrl, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    statusDiv.textContent = 'Thanks for subscribing!';
                    if (emailInput) {
                        emailInput.value = '';
                    }
                } else {
                    statusDiv.textContent = result.data.message || 'Unable to subscribe. Please try again later.';
                }
            } catch (error) {
                statusDiv.textContent = 'Unable to subscribe. Please try again later.';
            }
            
            statusDiv.classList.remove('hidden');
        });
    }
});
