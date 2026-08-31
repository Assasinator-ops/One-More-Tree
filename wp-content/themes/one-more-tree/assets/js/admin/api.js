/**
 * OneMoreTree Admin API Module
 * Handles API communication and data caching
 */

class AdminAPI {
    constructor(config) {
        this.config = config;
        this.dataCache = {};
    }

    async fetchData(apiKey, forceReload = false) {
        if (!forceReload && this.dataCache[apiKey] !== undefined) {
            return this.dataCache[apiKey];
        }

        try {
            const res = await fetch(`${this.config.restUrl}admin/data?key=${apiKey}`, {
                headers: { 'X-WP-Nonce': this.config.restNonce }
            });
            const data = await res.json();
            
            if (data.ok && data.value !== undefined) {
                this.dataCache[apiKey] = data.value;
                return data.value;
            }
        } catch (e) {
            console.error('Error fetching admin data:', e);
        }
        return null;
    }

    async saveData(apiKey, value) {
        this.dataCache[apiKey] = value;
        try {
            const res = await fetch(`${this.config.restUrl}admin/data`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': this.config.restNonce
                },
                body: JSON.stringify({ key: apiKey, value: value })
            });
            return await res.json();
        } catch (e) {
            return { ok: false, error: e.message };
        }
    }

    showToast(elementId, message, isSuccess = true) {
        const el = document.getElementById(elementId);
        if (!el) return;
        
        el.className = `text-xs font-semibold px-3 py-1.5 rounded-lg transition-all ${
            isSuccess 
                ? 'bg-forest-50 text-forest-700 border border-forest-200' 
                : 'bg-red-50 text-red-700 border border-red-200'
        }`;
        el.textContent = message;
        
        setTimeout(() => {
            if (el) el.textContent = '';
        }, 4000);
    }

    clearCache() {
        this.dataCache = {};
    }
}

// Register to global scope
window.AdminAPI = AdminAPI;