document.addEventListener('DOMContentLoaded', function() {
    // GSAP animation on load
    const nav = document.getElementById('site-navigation');
    if (nav && typeof gsap !== 'undefined') {
        gsap.fromTo(nav, 
            { y: -100, opacity: 0 },
            { y: 0, opacity: 1, duration: 1, delay: 0.5, ease: 'power3.out' }
        );
    }

    // Scroll detection
    let isScrolled = false;
    const handleScroll = () => {
        const wasScrolled = isScrolled;
        isScrolled = window.scrollY > 100;
        
        if (wasScrolled !== isScrolled && nav) {
            nav.classList.toggle('is-scrolled', isScrolled);
            
            // Apply conditional classes based on scroll state (matching Next.js)
            if (isScrolled) {
                nav.classList.remove('md:bg-transparent');
                nav.classList.add('bg-white/98', 'backdrop-blur-xl', 'border-b', 'border-earth-200', 'shadow-medium');
                nav.classList.remove('md:bg-gradient-to-b', 'md:from-white/90', 'md:to-transparent');
            } else {
                nav.classList.add('md:bg-transparent');
                nav.classList.remove('bg-white/98', 'backdrop-blur-xl', 'border-b', 'border-earth-200', 'shadow-medium');
                nav.classList.add('md:bg-gradient-to-b', 'md:from-white/90', 'md:to-transparent');
            }
        }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });

    // Active link detection for custom nav links
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(link => {
        const linkPath = link.getAttribute('data-path');
        if (currentPath === linkPath || (linkPath !== '/' && currentPath.startsWith(linkPath))) {
            link.classList.add('text-forest-700', 'font-semibold');
            link.classList.remove('text-foreground/70');
        }
    });

    // Active link detection for WordPress navigation blocks
    document.querySelectorAll('.wp-block-navigation-link__content').forEach(link => {
        const linkHref = link.getAttribute('href');
        if (linkHref) {
            const linkPath = new URL(linkHref, window.location.origin).pathname;
            if (currentPath === linkPath || (linkPath !== '/' && currentPath.startsWith(linkPath))) {
                link.classList.add('text-forest-700', 'font-semibold');
                link.classList.remove('text-foreground/70');
            }
        }
    });

    // Mobile menu toggle for block templates
    const menuBtn = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.wp-block-group.md\\:hidden.absolute');
    
    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            const isOpen = !mobileMenu.classList.contains('hidden');
            menuBtn.textContent = isOpen ? '✕' : '☰';
        });
    }

    // Mobile menu toggle for PHP templates
    const menuBtnPhp = document.getElementById('mobile-menu-btn');
    const mobileMenuPhp = document.getElementById('mobile-menu');
    const menuIconHamburger = document.querySelector('.menu-icon-hamburger');
    const menuIconX = document.querySelector('.menu-icon-x');

    if (menuBtnPhp && mobileMenuPhp && menuIconHamburger && menuIconX) {
        menuBtnPhp.addEventListener('click', () => {
            mobileMenuPhp.classList.toggle('hidden');
            menuIconHamburger.classList.toggle('hidden');
            menuIconX.classList.toggle('hidden');
        });

        // Close menu on link click
        mobileMenuPhp.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenuPhp.classList.add('hidden');
                menuIconHamburger.classList.remove('hidden');
                menuIconX.classList.add('hidden');
            });
        });
    }
});
