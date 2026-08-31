/**
 * OneMoreTree Admin Settings Module
 * Handles settings management and configuration
 */

class AdminSettings {
    constructor(config, api) {
        this.config = config;
        this.api = api;
        // Defer initialization until API is set
    }

    init() {
        if (!this.api) return;
        this.initSettingsForms();
        this.initToggleSwitches();
        this.initColorPickers();
    }

    initSettingsForms() {
        const settingsForms = document.querySelectorAll('[data-settings-form]');
        settingsForms.forEach(form => {
            form.addEventListener('submit', (e) => this.handleSettingsSubmit(e, form));
        });
    }

    async handleSettingsSubmit(event, form) {
        event.preventDefault();
        const formData = new FormData(form);
        const settingsKey = form.dataset.settingsForm;
        const settingsData = {};

        formData.forEach((value, key) => {
            settingsData[key] = value;
        });

        const result = await this.api.saveData(settingsKey, settingsData);
        
        if (result.ok) {
            this.showSettingsSuccess(form);
        } else {
            this.showSettingsError(form, result.error || 'Failed to save settings');
        }
    }

    showSettingsSuccess(form) {
        const successMessage = form.querySelector('[data-settings-success]');
        if (successMessage) {
            successMessage.classList.remove('hidden');
            successMessage.classList.add('flex');
            setTimeout(() => {
                successMessage.classList.add('hidden');
                successMessage.classList.remove('flex');
            }, 3000);
        }
    }

    showSettingsError(form, message) {
        const errorMessage = form.querySelector('[data-settings-error]');
        if (errorMessage) {
            errorMessage.textContent = message;
            errorMessage.classList.remove('hidden');
            errorMessage.classList.add('flex');
            setTimeout(() => {
                errorMessage.classList.add('hidden');
                errorMessage.classList.remove('flex');
            }, 5000);
        }
    }

    initToggleSwitches() {
        const toggles = document.querySelectorAll('[data-toggle-setting]');
        toggles.forEach(toggle => {
            toggle.addEventListener('change', (e) => this.handleToggleChange(e, toggle));
        });
    }

    async handleToggleChange(event, toggle) {
        const settingKey = toggle.dataset.toggleSetting;
        const isEnabled = toggle.checked;

        const result = await this.api.saveData(settingKey, { enabled: isEnabled });
        
        if (!result.ok) {
            toggle.checked = !isEnabled; // Revert on failure
            this.showToggleError(toggle);
        }
    }

    showToggleError(toggle) {
        const parent = toggle.closest('[data-toggle-parent]');
        if (parent) {
            const errorEl = parent.querySelector('[data-toggle-error]');
            if (errorEl) {
                errorEl.classList.remove('hidden');
                setTimeout(() => {
                    errorEl.classList.add('hidden');
                }, 3000);
            }
        }
    }

    initColorPickers() {
        const colorPickers = document.querySelectorAll('[data-color-setting]');
        colorPickers.forEach(picker => {
            picker.addEventListener('change', (e) => this.handleColorChange(e, picker));
        });
    }

    async handleColorChange(event, picker) {
        const settingKey = picker.dataset.colorSetting;
        const colorValue = picker.value;

        const result = await this.api.saveData(settingKey, { color: colorValue });
        
        if (!result.ok) {
            this.showColorError(picker);
        } else {
            this.updateColorPreview(picker, colorValue);
        }
    }

    showColorError(picker) {
        const parent = picker.closest('[data-color-parent]');
        if (parent) {
            const errorEl = parent.querySelector('[data-color-error]');
            if (errorEl) {
                errorEl.classList.remove('hidden');
                setTimeout(() => {
                    errorEl.classList.add('hidden');
                }, 3000);
            }
        }
    }

    updateColorPreview(picker, color) {
        const preview = picker.closest('[data-color-parent]').querySelector('[data-color-preview]');
        if (preview) {
            preview.style.backgroundColor = color;
        }
    }

    async loadSettings(settingsKey) {
        return await this.api.fetchData(settingsKey);
    }

    async resetSettings(settingsKey) {
        if (confirm('Are you sure you want to reset these settings to default values?')) {
            const result = await this.api.saveData(settingsKey, null); // null signals reset
            return result.ok;
        }
        return false;
    }
}

// Register to global scope
window.AdminSettings = AdminSettings;