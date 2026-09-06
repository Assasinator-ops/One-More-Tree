document.addEventListener('DOMContentLoaded', function() {
    const STORAGE_KEY = 'disclaimer.ribbon.dismissed';
    const ribbon = document.getElementById('disclaimer-ribbon');
    const blockDisclaimer = document.querySelector('.wp-block-group.fixed.top-0.left-0.right-0.z-\\[60\\]');
    const dismissBtn = document.getElementById('dismiss-disclaimer-btn');
    const blockDismissBtn = document.querySelector('.dismiss-disclaimer-btn');
    
    const disclaimer = ribbon || blockDisclaimer;
    const dismissButton = dismissBtn || blockDismissBtn;
    
    // Check if dismissed
    try {
        if (localStorage.getItem(STORAGE_KEY) === '1' && disclaimer) {
            disclaimer.style.display = 'none';
            document.documentElement.style.removeProperty('--disclaimer-h');
        } else if (disclaimer && disclaimer.style.display !== 'none') {
            // Set CSS variable immediately if disclaimer is visible
            const setHeight = () => {
                document.documentElement.style.setProperty('--disclaimer-h', `${disclaimer.offsetHeight}px`);
            };
            setHeight();
            window.addEventListener('resize', setHeight);
        }
    } catch (e) {
        console.warn('Local storage not available');
    }
    
    // Dismiss handler
    if (dismissButton) {
        dismissButton.addEventListener('click', function() {
            try {
                localStorage.setItem(STORAGE_KEY, '1');
            } catch (e) {
                console.warn('Local storage not available');
            }
            
            if (disclaimer) {
                disclaimer.style.display = 'none';
                document.documentElement.style.removeProperty('--disclaimer-h');
            }
        });
    }
});
