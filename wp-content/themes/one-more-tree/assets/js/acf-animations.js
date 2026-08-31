document.addEventListener('DOMContentLoaded', function() {
    // GSAP Hero Animation for ACF pages
    const heroSection = document.getElementById('hero-section');
    const heroImage = document.getElementById('hero-image');
    const heroContent = document.getElementById('hero-content');

    if (heroSection && heroImage && heroContent && typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);

        // Parallax effect on hero image
        gsap.to(heroImage, {
            yPercent: 30,
            ease: 'none',
            scrollTrigger: {
                trigger: heroSection,
                start: 'top top',
                end: 'bottom top',
                scrub: true
            }
        });

        // Fade out content on scroll
        gsap.to(heroContent, {
            opacity: 0,
            y: -50,
            ease: 'none',
            scrollTrigger: {
                trigger: heroSection,
                start: 'top top',
                end: '50% top',
                scrub: true
            }
        });
    }

    // 3D Donation Card Interaction
    const donationCard = document.getElementById('donation-3d-card');
    const donationInner = document.getElementById('donation-3d-inner');

    if (donationCard && donationInner) {
        donationCard.addEventListener('pointermove', function(e) {
            const rect = donationCard.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width - 0.5) * 16;
            const y = ((e.clientY - rect.top) / rect.height - 0.5) * -16;
            donationInner.style.transform = `rotateX(${y}deg) rotateY(${x}deg)`;
        });

        donationCard.addEventListener('pointerleave', function() {
            donationInner.style.transform = 'rotateX(0deg) rotateY(0deg)';
        });
    }

    // About Page Hero Animation
    const aboutHeroSection = document.querySelector('.page-header-texture');
    const aboutImageRef = document.querySelector('.page-header-texture');

    if (aboutHeroSection && aboutImageRef && typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.fromTo(
            aboutImageRef,
            { opacity: 0, y: 50 },
            { 
                opacity: 1, 
                y: 0, 
                duration: 1, 
                scrollTrigger: { 
                    trigger: aboutHeroSection, 
                    start: 'top 80%' 
                } 
            }
        );
    }

    // Donation Tier Selection
    window.selectDonationTier = function(tierNum) {
        const tierBtns = document.querySelectorAll('.donation-tier-btn');
        const tierAmounts = [
            document.querySelector('.donation-tier-1')?.getAttribute('data-amount') || '$25',
            document.querySelector('.donation-tier-2')?.getAttribute('data-amount') || '$50',
            document.querySelector('.donation-tier-3')?.getAttribute('data-amount') || '$100'
        ];
        
        const selectedAmount = tierAmounts[tierNum - 1];
        const numericAmount = parseInt(selectedAmount.replace(/[$,€]/g, ''), 10);
        
        // Update button styles
        tierBtns.forEach((btn, index) => {
            if (index === tierNum - 1) {
                btn.classList.remove('border-slate-200', 'bg-white', 'text-foreground');
                btn.classList.add('border-forest-500', 'bg-forest-50', 'text-forest-900', 'shadow-medium');
            } else {
                btn.classList.add('border-slate-200', 'bg-white', 'text-foreground');
                btn.classList.remove('border-forest-500', 'bg-forest-50', 'text-forest-900', 'shadow-medium');
            }
        });
        
        // Update 3D card content
        const donationAmount = document.getElementById('donation-amount');
        const treesPlanted = document.getElementById('trees-planted');
        const volunteers = document.getElementById('volunteers');
        const donateButton = document.getElementById('donate-button');
        
        if (donationAmount) {
            donationAmount.textContent = `${selectedAmount} support`;
        }
        if (treesPlanted) {
            treesPlanted.textContent = numericAmount * 3;
        }
        if (volunteers) {
            volunteers.textContent = `+${Math.round(numericAmount / 10)} volunteers`;
        }
        if (donateButton) {
            donateButton.textContent = `Donate ${selectedAmount}`;
        }
    };

    // Contact Form Handling
    const contactForm = document.getElementById('contact-form-main');
    const contactStatus = document.getElementById('contact-form-status');

    if (contactForm && contactStatus && typeof omtData !== 'undefined') {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(contactForm);
            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;

            submitBtn.textContent = 'Sending…';
            submitBtn.disabled = true;
            contactStatus.classList.add('hidden');

            formData.append('action', 'omt_contact_submit');
            formData.append('nonce', omtData.nonce);

            try {
                const response = await fetch(omtData.ajaxUrl, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    contactStatus.textContent = 'Message sent! We saved it to the admin inbox.';
                    contactStatus.classList.remove('hidden');
                    contactForm.reset();
                } else {
                    contactStatus.textContent = result.data.message || 'Unable to send message. Please try again later.';
                    contactStatus.classList.remove('hidden');
                }
            } catch (error) {
                contactStatus.textContent = 'Unable to send message. Please try again later.';
                contactStatus.classList.remove('hidden');
            } finally {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    }
});
