/**
 * OneMoreTree Admin Navigation Module
 * Handles navigation, dropdown menus, and page routing
 */

class AdminNavigation {
    constructor(config, onPageChange) {
        this.config = config;
        this.onPageChange = onPageChange;
        this.activePageKey = 'home-counters';
        this.openGroupDropdown = null;
        
        this.NAVIGATION_GROUPS = [
            {
                name: 'Homepage',
                icon: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>`,
                pages: [
                    { key: 'home-counters', label: 'Home Counters', apiKey: 'home_counters' },
                    { key: 'impact-kpis', label: 'Impact KPIs', apiKey: 'impact_kpis' }
                ]
            },
            {
                name: 'Projects',
                icon: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>`,
                pages: [
                    { key: 'projects', label: 'Projects', apiKey: 'projects' },
                    { key: 'stories', label: 'Stories', apiKey: 'stories' },
                    { key: 'live-map', label: 'Live Map', apiKey: 'live_map' }
                ]
            },
            {
                name: 'Communication',
                icon: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>`,
                pages: [
                    { key: 'blog', label: 'Blog', apiKey: 'blog' },
                    { key: 'newsletter', label: 'Newsletter', apiKey: 'newsletter_subscribers' },
                    { key: 'social-media', label: 'Social Media', apiKey: 'social' },
                    { key: 'contact', label: 'Contact Inquiries', apiKey: 'contact_submissions' }
                ]
            },
            {
                name: 'Organization',
                icon: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>`,
                pages: [
                    { key: 'team', label: 'Team', apiKey: 'team' },
                    { key: 'admin-users', label: 'Admin Users', apiKey: 'admin_users' }
                ]
            },
            {
                name: 'Settings',
                icon: `<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>`,
                pages: [
                    { key: 'faq', label: 'FAQ', apiKey: 'faq' },
                    { key: 'disclaimer', label: 'Disclaimer', apiKey: 'disclaimer' },
                    { key: 'module-status', label: 'Module Visibility', apiKey: 'modules' },
                    { key: 'donations', label: 'Donations', apiKey: 'donation_tiers' },
                    { key: 'reporting', label: 'Reporting', apiKey: 'reporting' }
                ]
            }
        ];

        this.init();
    }

    init() {
        const navContainer = document.getElementById('admin-nav-groups-container');
        if (navContainer) {
            this.renderNav();
            this.initClickOutside();
        }
    }

    renderNav() {
        const navContainer = document.getElementById('admin-nav-groups-container');
        if (!navContainer) return;
        
        navContainer.innerHTML = '';

        this.NAVIGATION_GROUPS.forEach(group => {
            const hasActive = group.pages.some(p => p.key === this.activePageKey);
            const isOpen = this.openGroupDropdown === group.name;

            const groupWrapper = document.createElement('div');
            groupWrapper.className = 'relative';

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs sm:text-sm font-medium transition-all ${
                isOpen || hasActive
                    ? 'bg-forest-600 text-white shadow-medium'
                    : 'bg-white/80 border border-earth-200 text-foreground/70 hover:text-foreground hover:bg-white'
            }`;
            btn.innerHTML = `
                ${group.icon}
                <span>${group.name}</span>
                <svg class="w-3.5 h-3.5 transition-transform duration-200 ${isOpen ? 'rotate-180' : ''}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            `;

            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.openGroupDropdown = (this.openGroupDropdown === group.name) ? null : group.name;
                this.renderNav();
            });

            groupWrapper.appendChild(btn);

            if (isOpen) {
                const dropdown = this.createDropdown(group);
                groupWrapper.appendChild(dropdown);
            }

            navContainer.appendChild(groupWrapper);
        });
    }

    createDropdown(group) {
        const dropdown = document.createElement('div');
        dropdown.className = 'absolute top-full left-0 mt-2 w-52 bg-white border border-earth-200 rounded-2xl shadow-elevated z-50 overflow-hidden py-1.5 animate-in fade-in slide-in-from-top-1';

        group.pages.forEach(p => {
            const itemBtn = document.createElement('button');
            itemBtn.type = 'button';
            itemBtn.className = `w-full text-left px-4 py-2.5 text-xs sm:text-sm transition-colors ${
                this.activePageKey === p.key
                    ? 'bg-forest-50 text-forest-700 font-semibold'
                    : 'text-foreground/70 hover:bg-earth-50 hover:text-foreground'
            }`;
            itemBtn.textContent = p.label;
            itemBtn.addEventListener('click', () => {
                this.activePageKey = p.key;
                this.openGroupDropdown = null;
                this.renderNav();
                if (this.onPageChange) {
                    this.onPageChange(p.key);
                }
            });
            dropdown.appendChild(itemBtn);
        });

        return dropdown;
    }

    initClickOutside() {
        document.addEventListener('click', () => {
            if (this.openGroupDropdown) {
                this.openGroupDropdown = null;
                this.renderNav();
            }
        });
    }

    setActivePage(pageKey) {
        this.activePageKey = pageKey;
        this.renderNav();
    }

    getActivePage() {
        return this.activePageKey;
    }

    setOnPageChange(callback) {
        this.onPageChange = callback;
    }
}

// Register to global scope
window.AdminNavigation = AdminNavigation;