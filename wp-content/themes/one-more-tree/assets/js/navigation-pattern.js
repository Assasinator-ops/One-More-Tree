/**
 * Navigation Pattern - Mobile Menu Functionality
 * Handles mobile menu toggle and scroll behavior for Gutenberg navigation pattern
 */

(function() {
    'use strict';

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        const navElement = document.querySelector('.omt-navigation-ribbon');
        if (!navElement) return;

        // Mobile menu toggle
        const mobileMenuButton = navElement.querySelector('.mobile-menu-toggle button');
        const mobileMenu = navElement.querySelector('.hidden.md\\:hidden.absolute');

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Toggle menu visibility
                if (mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.remove('hidden');
                    mobileMenu.classList.add('flex');
                    mobileMenuButton.setAttribute('aria-expanded', 'true');
                } else {
                    mobileMenu.classList.add('hidden');
                    mobileMenu.classList.remove('flex');
                    mobileMenuButton.setAttribute('aria-expanded', 'false');
                }
            });
        }

        // Scroll behavior
        let lastScroll = 0;
        const scrollThreshold = 100;

        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            
            if (currentScroll > scrollThreshold) {
                navElement.classList.add('scrolled');
            } else {
                navElement.classList.remove('scrolled');
            }
            
            lastScroll = currentScroll;
        }, { passive: true });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                if (!navElement.contains(e.target)) {
                    mobileMenu.classList.add('hidden');
                    mobileMenu.classList.remove('flex');
                    if (mobileMenuButton) {
                        mobileMenuButton.setAttribute('aria-expanded', 'false');
                    }
                }
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileMenu && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                mobileMenu.classList.remove('flex');
                if (mobileMenuButton) {
                    mobileMenuButton.setAttribute('aria-expanded', 'false');
                    mobileMenuButton.focus();
                }
            }
        });

        // Close menu when clicking a link
        if (mobileMenu) {
            const menuLinks = mobileMenu.querySelectorAll('a');
            menuLinks.forEach(function(link) {
                link.addEventListener('click', function() {
                    mobileMenu.classList.add('hidden');
                    mobileMenu.classList.remove('flex');
                    if (mobileMenuButton) {
                        mobileMenuButton.setAttribute('aria-expanded', 'false');
                    }
                });
            });
        }
    });
})();
