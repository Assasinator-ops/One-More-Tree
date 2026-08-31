/**
 * OneMoreTree Admin Dashboard Module
 * Handles module loading, rendering, and dashboard functionality
 */

class AdminDashboard {
    constructor(config, api) {
        this.config = config;
        this.api = api;
        this.moduleContent = document.getElementById('admin-module-content');
        // Defer initialization until API is set
    }

    init() {
        if (!this.api) return;
        this.loadModule('home-counters');
    }

    async loadModule(pageKey) {
        if (!this.moduleContent) return;
        
        this.showLoading();

        switch (pageKey) {
            case 'home-counters':
                await this.loadHomeCounters();
                break;
            case 'impact-kpis':
                await this.loadImpactKpis();
                break;
            case 'projects':
                await this.loadProjects();
                break;
            case 'stories':
                await this.loadStories();
                break;
            case 'live-map':
                await this.loadLiveMap();
                break;
            case 'faq':
                await this.loadFaq();
                break;
            case 'donations':
                await this.loadDonations();
                break;
            case 'contact':
                await this.loadContact();
                break;
            case 'newsletter':
                await this.loadNewsletter();
                break;
            case 'social-media':
                await this.loadSocialMedia();
                break;
            case 'team':
                await this.loadTeam();
                break;
            case 'disclaimer':
                await this.loadDisclaimer();
                break;
            case 'module-status':
                await this.loadModuleStatus();
                break;
            case 'blog':
                await this.loadBlog();
                break;
            case 'admin-users':
                await this.loadAdminUsers();
                break;
            case 'reporting':
            default:
                await this.loadReporting();
                break;
        }
    }

    showLoading() {
        if (!this.moduleContent) return;
        this.moduleContent.innerHTML = `
            <div class="py-16 text-center text-foreground/50">
                <div class="inline-block w-8 h-8 border-4 border-forest-600 border-t-transparent rounded-full animate-spin mb-3"></div>
                <p class="text-sm">Loading module data...</p>
            </div>
        `;
    }

    async loadHomeCounters() {
        const data = await this.api.fetchData('home_counters') || {
            treesTracked: 1000000,
            projectsActive: 50,
            volunteersEngaged: 10000,
            regionsCovered: 4
        };

        this.moduleContent.innerHTML = `
            <div class="space-y-6 animate-in fade-in">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-foreground">Home Counters</h2>
                    <p class="text-xs sm:text-sm text-foreground/60">Live metrics shown in the homepage hero and counter badges.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="p-4 bg-white rounded-2xl border border-earth-200 shadow-soft">
                        <label class="block text-xs font-semibold text-foreground/70 mb-1">Trees Tracked</label>
                        <input id="hc-trees" type="number" class="w-full px-4 py-2.5 rounded-xl border border-earth-200 text-sm focus:border-forest-500 outline-none" value="${data.treesTracked}">
                        <p class="text-[11px] text-foreground/40 mt-1">Total trees planted across all active restoration sites.</p>
                    </div>
                    <div class="p-4 bg-white rounded-2xl border border-earth-200 shadow-soft">
                        <label class="block text-xs font-semibold text-foreground/70 mb-1">Projects Active</label>
                        <input id="hc-projects" type="number" class="w-full px-4 py-2.5 rounded-xl border border-earth-200 text-sm focus:border-forest-500 outline-none" value="${data.projectsActive}">
                        <p class="text-[11px] text-foreground/40 mt-1">Number of active reforestation projects.</p>
                    </div>
                    <div class="p-4 bg-white rounded-2xl border border-earth-200 shadow-soft">
                        <label class="block text-xs font-semibold text-foreground/70 mb-1">Volunteers Engaged</label>
                        <input id="hc-volunteers" type="number" class="w-full px-4 py-2.5 rounded-xl border border-earth-200 text-sm focus:border-forest-500 outline-none" value="${data.volunteersEngaged}">
                        <p class="text-[11px] text-foreground/40 mt-1">Total volunteers participating in planting events.</p>
                    </div>
                    <div class="p-4 bg-white rounded-2xl border border-earth-200 shadow-soft">
                        <label class="block text-xs font-semibold text-foreground/70 mb-1">Regions Covered</label>
                        <input id="hc-regions" type="number" class="w-full px-4 py-2.5 rounded-xl border border-earth-200 text-sm focus:border-forest-500 outline-none" value="${data.regionsCovered}">
                        <p class="text-[11px] text-foreground/40 mt-1">Geographic regions with active projects.</p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button id="hc-save-btn" class="px-6 py-2.5 bg-forest-600 hover:bg-forest-700 text-white font-medium rounded-full transition-colors text-sm">
                        Save Changes
                    </button>
                </div>
            </div>
        `;

        this.initHomeCountersSave();
    }

    initHomeCountersSave() {
        const saveBtn = document.getElementById('hc-save-btn');
        if (saveBtn) {
            saveBtn.addEventListener('click', async () => {
                const trees = document.getElementById('hc-trees').value;
                const projects = document.getElementById('hc-projects').value;
                const volunteers = document.getElementById('hc-volunteers').value;
                const regions = document.getElementById('hc-regions').value;

                const result = await this.api.saveData('home_counters', {
                    treesTracked: parseInt(trees),
                    projectsActive: parseInt(projects),
                    volunteersEngaged: parseInt(volunteers),
                    regionsCovered: parseInt(regions)
                });

                this.api.showToast('hc-toast', result.ok ? 'Saved successfully!' : 'Failed to save.', result.ok);
            });
        }
    }

    async loadImpactKpis() {
        const data = await this.api.fetchData('impact_kpis') || {
            carbonSequestered: 50000,
            biodiversityIndex: 85,
            communityBenefits: 1200
        };

        this.moduleContent.innerHTML = `
            <div class="space-y-6 animate-in fade-in">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-foreground">Impact KPIs</h2>
                    <p class="text-xs sm:text-sm text-foreground/60">Key performance indicators for environmental impact.</p>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="p-4 bg-white rounded-2xl border border-earth-200 shadow-soft">
                        <label class="block text-xs font-semibold text-foreground/70 mb-1">Carbon Sequestered (tons)</label>
                        <input id="ik-carbon" type="number" class="w-full px-4 py-2.5 rounded-xl border border-earth-200 text-sm focus:border-forest-500 outline-none" value="${data.carbonSequestered}">
                    </div>
                    <div class="p-4 bg-white rounded-2xl border border-earth-200 shadow-soft">
                        <label class="block text-xs font-semibold text-foreground/70 mb-1">Biodiversity Index</label>
                        <input id="ik-biodiversity" type="number" class="w-full px-4 py-2.5 rounded-xl border border-earth-200 text-sm focus:border-forest-500 outline-none" value="${data.biodiversityIndex}">
                    </div>
                    <div class="p-4 bg-white rounded-2xl border border-earth-200 shadow-soft">
                        <label class="block text-xs font-semibold text-foreground/70 mb-1">Community Benefits</label>
                        <input id="ik-community" type="number" class="w-full px-4 py-2.5 rounded-xl border border-earth-200 text-sm focus:border-forest-500 outline-none" value="${data.communityBenefits}">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button id="ik-save-btn" class="px-6 py-2.5 bg-forest-600 hover:bg-forest-700 text-white font-medium rounded-full transition-colors text-sm">
                        Save Changes
                    </button>
                </div>
                <div id="ik-toast"></div>
            </div>
        `;

        this.initImpactKpisSave();
    }

    initImpactKpisSave() {
        const saveBtn = document.getElementById('ik-save-btn');
        if (saveBtn) {
            saveBtn.addEventListener('click', async () => {
                const carbon = document.getElementById('ik-carbon').value;
                const biodiversity = document.getElementById('ik-biodiversity').value;
                const community = document.getElementById('ik-community').value;

                const result = await this.api.saveData('impact_kpis', {
                    carbonSequestered: parseInt(carbon),
                    biodiversityIndex: parseInt(biodiversity),
                    communityBenefits: parseInt(community)
                });

                this.api.showToast('ik-toast', result.ok ? 'Saved successfully!' : 'Failed to save.', result.ok);
            });
        }
    }

    async loadProjects() {
        this.moduleContent.innerHTML = `
            <div class="space-y-6 animate-in fade-in">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-foreground">Projects</h2>
                    <p class="text-xs sm:text-sm text-foreground/60">Manage reforestation projects and their details.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border border-earth-200 shadow-soft text-center">
                    <p class="text-foreground/60">Projects module - Coming soon</p>
                </div>
            </div>
        `;
    }

    async loadStories() {
        this.moduleContent.innerHTML = `
            <div class="space-y-6 animate-in fade-in">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-foreground">Stories</h2>
                    <p class="text-xs sm:text-sm text-foreground/60">Manage success stories and testimonials.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border border-earth-200 shadow-soft text-center">
                    <p class="text-foreground/60">Stories module - Coming soon</p>
                </div>
            </div>
        `;
    }

    async loadLiveMap() {
        this.moduleContent.innerHTML = `
            <div class="space-y-6 animate-in fade-in">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-foreground">Live Map</h2>
                    <p class="text-xs sm:text-sm text-foreground/60">Configure live map settings and project locations.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border border-earth-200 shadow-soft text-center">
                    <p class="text-foreground/60">Live Map module - Coming soon</p>
                </div>
            </div>
        `;
    }

    async loadTeam() {
        this.moduleContent.innerHTML = `
            <div class="space-y-6 animate-in fade-in">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-foreground">Team</h2>
                    <p class="text-xs sm:text-sm text-foreground/60">Manage team members and roles.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border border-earth-200 shadow-soft text-center">
                    <p class="text-foreground/60">Team module - Coming soon</p>
                </div>
            </div>
        `;
    }

    async loadFaq() {
        this.moduleContent.innerHTML = `
            <div class="space-y-6 animate-in fade-in">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-foreground">FAQ</h2>
                    <p class="text-xs sm:text-sm text-foreground/60">Manage frequently asked questions.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border border-earth-200 shadow-soft text-center">
                    <p class="text-foreground/60">FAQ module - Coming soon</p>
                </div>
            </div>
        `;
    }

    async loadDisclaimer() {
        this.moduleContent.innerHTML = `
            <div class="space-y-6 animate-in fade-in">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-foreground">Disclaimer</h2>
                    <p class="text-xs sm:text-sm text-foreground/60">Manage site disclaimer ribbon content.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border border-earth-200 shadow-soft text-center">
                    <p class="text-foreground/60">Disclaimer module - Coming soon</p>
                </div>
            </div>
        `;
    }

    async loadDonations() {
        this.moduleContent.innerHTML = `
            <div class="space-y-6 animate-in fade-in">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-foreground">Donations</h2>
                    <p class="text-xs sm:text-sm text-foreground/60">Manage donation tiers and settings.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border border-earth-200 shadow-soft text-center">
                    <p class="text-foreground/60">Donations module - Coming soon</p>
                </div>
            </div>
        `;
    }

    async loadSocialMedia() {
        this.moduleContent.innerHTML = `
            <div class="space-y-6 animate-in fade-in">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-foreground">Social Media</h2>
                    <p class="text-xs sm:text-sm text-foreground/60">Manage social media links and content.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border border-earth-200 shadow-soft text-center">
                    <p class="text-foreground/60">Social Media module - Coming soon</p>
                </div>
            </div>
        `;
    }

    async loadContact() {
        this.moduleContent.innerHTML = `
            <div class="space-y-6 animate-in fade-in">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-foreground">Contact Inquiries</h2>
                    <p class="text-xs sm:text-sm text-foreground/60">View and manage contact form submissions.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border border-earth-200 shadow-soft text-center">
                    <p class="text-foreground/60">Contact module - Coming soon</p>
                </div>
            </div>
        `;
    }

    async loadNewsletter() {
        this.moduleContent.innerHTML = `
            <div class="space-y-6 animate-in fade-in">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-foreground">Newsletter</h2>
                    <p class="text-xs sm:text-sm text-foreground/60">Manage newsletter subscribers.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border border-earth-200 shadow-soft text-center">
                    <p class="text-foreground/60">Newsletter module - Coming soon</p>
                </div>
            </div>
        `;
    }

    async loadModuleStatus() {
        this.moduleContent.innerHTML = `
            <div class="space-y-6 animate-in fade-in">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-foreground">Module Visibility</h2>
                    <p class="text-xs sm:text-sm text-foreground/60">Control which modules are visible on the website.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border border-earth-200 shadow-soft text-center">
                    <p class="text-foreground/60">Module Status module - Coming soon</p>
                </div>
            </div>
        `;
    }

    async loadBlog() {
        this.moduleContent.innerHTML = `
            <div class="space-y-6 animate-in fade-in">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-foreground">Blog</h2>
                    <p class="text-xs sm:text-sm text-foreground/60">Manage blog posts and content.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border border-earth-200 shadow-soft text-center">
                    <p class="text-foreground/60">Blog module - Coming soon</p>
                </div>
            </div>
        `;
    }

    async loadAdminUsers() {
        this.moduleContent.innerHTML = `
            <div class="space-y-6 animate-in fade-in">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-foreground">Admin Users</h2>
                    <p class="text-xs sm:text-sm text-foreground/60">Manage administrator access and permissions.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border border-earth-200 shadow-soft text-center">
                    <p class="text-foreground/60">Admin Users module - Coming soon</p>
                </div>
            </div>
        `;
    }

    async loadReporting() {
        this.moduleContent.innerHTML = `
            <div class="space-y-6 animate-in fade-in">
                <div>
                    <h2 class="font-serif text-2xl font-bold text-foreground">Reporting</h2>
                    <p class="text-xs sm:text-sm text-foreground/60">View reports and analytics.</p>
                </div>
                <div class="p-8 bg-white rounded-2xl border border-earth-200 shadow-soft text-center">
                    <p class="text-foreground/60">Reporting module - Coming soon</p>
                </div>
            </div>
        `;
    }
}

// Register to global scope
window.AdminDashboard = AdminDashboard;