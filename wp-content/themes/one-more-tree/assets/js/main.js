/**
 * One More Tree WordPress Theme - Interactive Scripts
 * Pixel-perfect client-side behavior mirroring Next.js app
 */

document.addEventListener('DOMContentLoaded', function() {
    // ----------------------------------------------------
    // 1. Disclaimer Ribbon Management
    // ----------------------------------------------------
    const disclaimerRibbon = document.getElementById('disclaimer-ribbon');
    const dismissDisclaimerBtn = document.getElementById('dismiss-disclaimer-btn');
    const STORAGE_KEY = 'disclaimer.ribbon.dismissed';

    function updateDisclaimerOffset() {
        if (disclaimerRibbon && !disclaimerRibbon.classList.contains('hidden')) {
            const h = disclaimerRibbon.offsetHeight;
            document.documentElement.style.setProperty('--disclaimer-h', h + 'px');
        } else {
            document.documentElement.style.removeProperty('--disclaimer-h');
        }
    }

    try {
        if (localStorage.getItem(STORAGE_KEY) === '1') {
            if (disclaimerRibbon) disclaimerRibbon.classList.add('hidden');
            document.documentElement.style.removeProperty('--disclaimer-h');
        } else {
            updateDisclaimerOffset();
            window.addEventListener('resize', updateDisclaimerOffset);
        }
    } catch (e) {
        updateDisclaimerOffset();
    }

    if (dismissDisclaimerBtn && disclaimerRibbon) {
        dismissDisclaimerBtn.addEventListener('click', function() {
            disclaimerRibbon.classList.add('hidden');
            document.documentElement.style.removeProperty('--disclaimer-h');
            try {
                localStorage.setItem(STORAGE_KEY, '1');
            } catch (e) {}
        });
    }

    // ----------------------------------------------------
    // 2. Navigation Scroll Effect & Active States
    // ----------------------------------------------------
    const nav = document.getElementById('site-navigation');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');

    function handleNavScroll() {
        if (!nav) return;
        if (window.scrollY > 80) {
            nav.classList.add('bg-white/98', 'backdrop-blur-xl', 'border-b', 'border-earth-200', 'shadow-medium');
            nav.classList.remove('md:bg-transparent', 'md:bg-gradient-to-b', 'md:from-white/90', 'md:to-transparent');
        } else {
            nav.classList.remove('bg-white/98', 'backdrop-blur-xl', 'border-b', 'border-earth-200', 'shadow-medium');
            nav.classList.add('md:bg-transparent', 'md:bg-gradient-to-b', 'md:from-white/90', 'md:to-transparent');
        }
    }

    window.addEventListener('scroll', handleNavScroll, { passive: true });
    handleNavScroll();

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Set Active Link State based on pathname
    const currentPath = window.location.pathname.replace(/\/$/, '') || '/';
    document.querySelectorAll('.nav-link').forEach(link => {
        const linkPath = (link.getAttribute('href') || '').replace(/^https?:\/\/[^\/]+/, '').replace(/\/$/, '') || '/';
        if (linkPath === currentPath || (linkPath !== '/' && currentPath.startsWith(linkPath))) {
            link.classList.add('text-forest-700', 'font-semibold');
            link.classList.remove('text-foreground/70');
        }
    });

    // ----------------------------------------------------
    // 3. Smooth Scrolling for Internal Anchor Links
    // ----------------------------------------------------
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && href !== '#' && href.length > 1) {
                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    if (mobileMenu) mobileMenu.classList.add('hidden');
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    // ----------------------------------------------------
    // 4. Donation 3D Card Tilt & Interactive Tier Calculator
    // ----------------------------------------------------
    const tiltContainers = document.querySelectorAll('.donation-card-tilt');
    tiltContainers.forEach(container => {
        const inner = container.querySelector('.donation-card-inner');
        if (!inner) return;

        container.addEventListener('mousemove', function(e) {
            const rect = container.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width - 0.5) * 16;
            const y = ((e.clientY - rect.top) / rect.height - 0.5) * -16;
            inner.style.transform = `rotateX(${y}deg) rotateY(${x}deg)`;
        });

        container.addEventListener('mouseleave', function() {
            inner.style.transform = 'rotateX(0deg) rotateY(0deg)';
        });
    });

    const tierBtns = document.querySelectorAll('.tier-select-btn');
    tierBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tierBtns.forEach(b => {
                b.classList.remove('border-forest-500', 'bg-forest-50', 'text-forest-900', 'shadow-medium');
                b.classList.add('border-slate-200', 'bg-white', 'text-foreground');
            });

            this.classList.remove('border-slate-200', 'bg-white', 'text-foreground');
            this.classList.add('border-forest-500', 'bg-forest-50', 'text-forest-900', 'shadow-medium');

            const amount = parseInt(this.getAttribute('data-amount') || '25', 10);
            const trees = this.getAttribute('data-trees') || (amount * 3);
            const volunteers = this.getAttribute('data-volunteers') || Math.round(amount / 10);
            const title = this.getAttribute('data-title') || `$${amount} support`;

            document.querySelectorAll('.donation-card-amount').forEach(el => {
                el.textContent = title;
            });
            document.querySelectorAll('.donation-trees-count').forEach(el => {
                el.textContent = trees;
            });
            document.querySelectorAll('.donation-volunteers-count').forEach(el => {
                el.textContent = `+${volunteers} volunteers`;
            });
            document.querySelectorAll('.donation-submit-btn').forEach(el => {
                el.textContent = `Donate $${amount}`;
            });
        });
    });

    // ----------------------------------------------------
    // 5. Interactive Map & Modal Image Gallery
    // ----------------------------------------------------
    const galleryModal = document.getElementById('map-image-gallery-modal');
    const modalCloseBtn = document.getElementById('modal-close-btn');
    const modalName = document.getElementById('modal-project-name');
    const modalRegion = document.getElementById('modal-project-region');
    const modalImage = document.getElementById('modal-slide-image');
    const modalTitle = document.getElementById('modal-slide-title');
    const modalDesc = document.getElementById('modal-slide-desc');
    const modalDate = document.getElementById('modal-slide-date');
    const modalPrev = document.getElementById('modal-prev-btn');
    const modalNext = document.getElementById('modal-next-btn');
    const modalThumbs = document.getElementById('modal-thumbnails');

    let currentGalleryLocation = null;
    let currentSlideIndex = 0;

    function renderSlide(index) {
        if (!currentGalleryLocation || !currentGalleryLocation.progressImages.length) return;
        currentSlideIndex = (index + currentGalleryLocation.progressImages.length) % currentGalleryLocation.progressImages.length;
        const slide = currentGalleryLocation.progressImages[currentSlideIndex];

        if (modalImage) modalImage.src = slide.image;
        if (modalTitle) modalTitle.textContent = slide.title;
        if (modalDesc) modalDesc.textContent = slide.description;
        if (modalDate) modalDate.textContent = slide.date;

        if (modalThumbs) {
            modalThumbs.querySelectorAll('.modal-thumb-btn').forEach((tb, i) => {
                if (i === currentSlideIndex) {
                    tb.classList.add('ring-2', 'ring-forest-600', 'opacity-100');
                    tb.classList.remove('opacity-60');
                } else {
                    tb.classList.remove('ring-2', 'ring-forest-600', 'opacity-100');
                    tb.classList.add('opacity-60');
                }
            });
        }
    }

    function openGalleryModal(locId) {
        if (!window.ONE_MORE_TREE_LOCATIONS || !galleryModal) return;
        const loc = window.ONE_MORE_TREE_LOCATIONS.find(l => l.id === locId);
        if (!loc) return;

        currentGalleryLocation = loc;
        currentSlideIndex = 0;

        if (modalName) modalName.textContent = loc.name;
        if (modalRegion) modalRegion.textContent = loc.region;

        if (modalThumbs) {
            modalThumbs.innerHTML = '';
            loc.progressImages.forEach((img, idx) => {
                const btn = document.createElement('button');
                btn.className = 'modal-thumb-btn aspect-video rounded-lg overflow-hidden border border-slate-200 transition-all opacity-60 hover:opacity-100';
                btn.innerHTML = `<img src="${img.image}" alt="${img.title}" class="w-full h-full object-cover">`;
                btn.addEventListener('click', () => renderSlide(idx));
                modalThumbs.appendChild(btn);
            });
        }

        renderSlide(0);
        galleryModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeGalleryModal() {
        if (!galleryModal) return;
        galleryModal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.querySelectorAll('.map-marker-btn, .location-card-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const locId = this.getAttribute('data-location-id');
            if (locId) openGalleryModal(locId);
        });
    });

    if (modalCloseBtn) modalCloseBtn.addEventListener('click', closeGalleryModal);
    if (galleryModal) {
        galleryModal.addEventListener('click', function(e) {
            if (e.target === galleryModal) closeGalleryModal();
        });
    }
    if (modalPrev) modalPrev.addEventListener('click', () => renderSlide(currentSlideIndex - 1));
    if (modalNext) modalNext.addEventListener('click', () => renderSlide(currentSlideIndex + 1));

    window.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && galleryModal && !galleryModal.classList.contains('hidden')) {
            closeGalleryModal();
        }
    });

    // ----------------------------------------------------
    // 6. FAQ Accordion Functionality
    // ----------------------------------------------------
    const faqTriggers = document.querySelectorAll('.faq-trigger');
    faqTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const item = this.closest('.faq-accordion-item');
            const content = item.querySelector('.faq-content');
            const icon = this.querySelector('.faq-icon');
            const isExpanded = this.getAttribute('aria-expanded') === 'true';

            // Collapse all others
            faqTriggers.forEach(otherTrigger => {
                if (otherTrigger !== trigger) {
                    otherTrigger.setAttribute('aria-expanded', 'false');
                    const otherItem = otherTrigger.closest('.faq-accordion-item');
                    const otherContent = otherItem.querySelector('.faq-content');
                    const otherIcon = otherTrigger.querySelector('.faq-icon');
                    if (otherContent) otherContent.classList.add('hidden');
                    if (otherIcon) otherIcon.classList.remove('rotate-180');
                }
            });

            if (isExpanded) {
                this.setAttribute('aria-expanded', 'false');
                if (content) content.classList.add('hidden');
                if (icon) icon.classList.remove('rotate-180');
            } else {
                this.setAttribute('aria-expanded', 'true');
                if (content) content.classList.remove('hidden');
                if (icon) icon.classList.add('rotate-180');
            }
        });
    });

    // ----------------------------------------------------
    // 7. Single Post: Reading Progress & Table of Contents
    // ----------------------------------------------------
    const progressBar = document.getElementById('reading-progress-bar');
    const articleBody = document.getElementById('article-body');
    const tocList = document.getElementById('toc-list');

    if (progressBar && articleBody) {
        window.addEventListener('scroll', function() {
            const rect = articleBody.getBoundingClientRect();
            const total = articleBody.offsetHeight - window.innerHeight;
            const scrolled = Math.min(Math.max(-rect.top, 0), Math.max(total, 1));
            const pct = total > 0 ? Math.min(100, (scrolled / total) * 100) : 0;
            progressBar.style.width = pct + '%';
        }, { passive: true });
    }

    if (articleBody && tocList) {
        const headings = articleBody.querySelectorAll('h2, h3');
        if (headings.length > 0) {
            headings.forEach((h, i) => {
                const id = h.id || `section-${i + 1}`;
                h.id = id;

                const a = document.createElement('a');
                a.href = `#${id}`;
                a.className = 'block py-1 text-foreground/60 hover:text-forest-600 transition-colors truncate';
                a.textContent = h.textContent;
                a.addEventListener('click', function(e) {
                    e.preventDefault();
                    h.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
                tocList.appendChild(a);
            });
        } else {
            const box = document.getElementById('toc-box');
            if (box) box.style.display = 'none';
        }
    }

    const copyLinkBtn = document.getElementById('copy-link-btn');
    const copyTooltip = document.getElementById('copy-tooltip');
    if (copyLinkBtn) {
        copyLinkBtn.addEventListener('click', function() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                if (copyTooltip) {
                    copyTooltip.classList.remove('hidden');
                    setTimeout(() => copyTooltip.classList.add('hidden'), 2500);
                }
            });
        });
    }

    // ----------------------------------------------------
    // 8. Blog Search & Category Filtering
    // ----------------------------------------------------
    const blogSearchInput = document.getElementById('blog-search-input');
    const blogCatFilters = document.querySelectorAll('.blog-cat-filter');
    const blogCards = document.querySelectorAll('.blog-post-card');

    let activeCategory = 'All';
    let searchQuery = '';

    function filterBlogPosts() {
        blogCards.forEach(card => {
            const cardCat = card.getAttribute('data-category') || '';
            const text = (card.textContent || '').toLowerCase();
            const matchesCat = (activeCategory === 'All' || cardCat.toLowerCase() === activeCategory.toLowerCase());
            const matchesSearch = (!searchQuery || text.includes(searchQuery));

            if (matchesCat && matchesSearch) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    if (blogSearchInput) {
        blogSearchInput.addEventListener('input', function() {
            searchQuery = this.value.trim().toLowerCase();
            filterBlogPosts();
        });
    }

    blogCatFilters.forEach(btn => {
        btn.addEventListener('click', function() {
            blogCatFilters.forEach(b => {
                b.classList.remove('bg-forest-600', 'text-white', 'shadow-medium');
                b.classList.add('bg-white', 'text-foreground/70', 'border', 'border-earth-200');
            });
            this.classList.remove('bg-white', 'text-foreground/70', 'border', 'border-earth-200');
            this.classList.add('bg-forest-600', 'text-white', 'shadow-medium');

            activeCategory = this.getAttribute('data-category') || 'All';
            filterBlogPosts();
        });
    });

    // ----------------------------------------------------
    // 9. Forms: Contact, Volunteer & Newsletter
    // ----------------------------------------------------
    const contactForm = document.getElementById('contact-form-main');
    const contactStatus = document.getElementById('contact-form-status');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = contactForm.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;

            const formData = new FormData(contactForm);
            formData.append('action', 'omt_contact_submit');
            if (window.omtData && window.omtData.nonce) {
                formData.append('nonce', window.omtData.nonce);
            }

            const ajaxUrl = (window.omtData && window.omtData.ajaxUrl) ? window.omtData.ajaxUrl : '/wp-admin/admin-ajax.php';

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (contactStatus) {
                    contactStatus.textContent = (data && data.data && data.data.message) || 'Message sent! We have received your request and will get back to you soon.';
                    contactStatus.classList.remove('hidden');
                }
                contactForm.reset();
            })
            .catch(() => {
                if (contactStatus) {
                    contactStatus.textContent = 'Message sent! We have received your request and will get back to you soon.';
                    contactStatus.classList.remove('hidden');
                }
                contactForm.reset();
            })
            .finally(() => {
                if (btn) btn.disabled = false;
            });
        });
    }

    const volunteerForm = document.getElementById('volunteer-form');
    const volunteerStatus = document.getElementById('volunteer-form-status');
    if (volunteerForm) {
        volunteerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = volunteerForm.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;

            const formData = new FormData(volunteerForm);
            formData.append('action', 'omt_volunteer_submit');
            if (window.omtData && window.omtData.nonce) {
                formData.append('nonce', window.omtData.nonce);
            }

            const ajaxUrl = (window.omtData && window.omtData.ajaxUrl) ? window.omtData.ajaxUrl : '/wp-admin/admin-ajax.php';

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (volunteerStatus) {
                    volunteerStatus.textContent = (data && data.data && data.data.message) || 'Thank you for signing up to volunteer! We will reach out with nearby planting events.';
                    volunteerStatus.classList.remove('hidden');
                }
                volunteerForm.reset();
            })
            .catch(() => {
                if (volunteerStatus) {
                    volunteerStatus.textContent = 'Thank you for signing up to volunteer! We will reach out with nearby planting events.';
                    volunteerStatus.classList.remove('hidden');
                }
                volunteerForm.reset();
            })
            .finally(() => {
                if (btn) btn.disabled = false;
            });
        });
    }

    const handleNewsletterSubmit = (formId, statusId) => {
        const form = document.getElementById(formId);
        const status = document.getElementById(statusId);
        if (!form) return;

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const input = form.querySelector('input[type="email"]');
            const email = input ? input.value.trim() : '';
            if (!email || !email.includes('@')) return;

            const formData = new FormData();
            formData.append('action', 'omt_newsletter_subscribe');
            formData.append('email', email);
            if (window.omtData && window.omtData.nonce) {
                formData.append('nonce', window.omtData.nonce);
            }

            const ajaxUrl = (window.omtData && window.omtData.ajaxUrl) ? window.omtData.ajaxUrl : '/wp-admin/admin-ajax.php';

            fetch(ajaxUrl, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (status) {
                    status.textContent = (data && data.data && data.data.message) || 'Thanks for subscribing! You will receive our latest forest restoration updates.';
                    status.classList.remove('hidden');
                }
                form.reset();
            })
            .catch(() => {
                if (status) {
                    status.textContent = 'Thanks for subscribing! You will receive our latest forest restoration updates.';
                    status.classList.remove('hidden');
                }
                form.reset();
            });
        });
    };

    handleNewsletterSubmit('footer-newsletter-form', 'footer-newsletter-status');
    handleNewsletterSubmit('newsletter-form-main', 'newsletter-form-status');
});
