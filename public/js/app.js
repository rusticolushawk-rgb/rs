/**
 * Gyr Falcon ERP - Core JavaScript
 * AJAX-driven, Odoo-style dynamic UI
 */
const GyrFalcon = {
    locale: document.body.dataset.locale || 'en',
    translations: {},
    sidebarCollapsed: false,

    init() {
        this.loadSidebar();
        this.loadNotifications();
        this.loadAppLauncher();
        this.highlightActiveSidebar();
        this.initAlertDismiss();
        this.initCharts();

        // Close dropdowns on outside click
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.nav-dropdown')) {
                document.querySelectorAll('.dropdown-menu').forEach(d => d.style.display = 'none');
            }
            if (!e.target.closest('.app-launcher-modal') && !e.target.closest('[onclick*="toggleAppLauncher"]')) {
                document.getElementById('appLauncherModal').style.display = 'none';
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.getElementById('appLauncherModal').style.display = 'none';
                document.querySelectorAll('.dropdown-menu').forEach(d => d.style.display = 'none');
            }
        });
    },

    // ==========================================
    // SIDEBAR
    // ==========================================
    async loadSidebar() {
        try {
            const resp = await fetch('/api/sidebar', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await resp.json();
            this.renderSidebar(data.menu || []);
        } catch (e) {
            console.error('Failed to load sidebar:', e);
        }
    },

    renderSidebar(menu) {
        const container = document.getElementById('sidebarMenu');
        if (!container) return;

        let html = '';
        menu.forEach(item => {
            const hasChildren = item.children && item.children.length > 0;
            const isActive = window.location.pathname.startsWith(item.url);

            html += `<div class="sidebar-item-wrapper">`;
            html += `<a href="${hasChildren ? 'javascript:void(0)' : item.url}" 
                        class="sidebar-item ${isActive ? 'active' : ''}" 
                        ${hasChildren ? `onclick="GyrFalcon.toggleSubmenu(this)"` : ''}
                        data-id="${item.id || ''}">
                        <i class="${item.icon || 'fas fa-circle'}"></i>
                        <span class="sidebar-item-text">${item.label}</span>
                        ${hasChildren ? '<i class="fas fa-chevron-right sidebar-expand-icon"></i>' : ''}
                     </a>`;

            if (hasChildren) {
                html += `<div class="sidebar-submenu ${isActive ? 'show' : ''}">`;
                item.children.forEach(child => {
                    const childActive = window.location.pathname === child.url;
                    html += `<a href="${child.url}" class="sidebar-item ${childActive ? 'active' : ''}">
                                <span class="sidebar-item-text">${child.label}</span>
                             </a>`;
                });
                html += '</div>';
            }
            html += '</div>';
        });

        container.innerHTML = html;
    },

    toggleSubmenu(el) {
        const submenu = el.nextElementSibling;
        const icon = el.querySelector('.sidebar-expand-icon');
        if (submenu) {
            submenu.classList.toggle('show');
            if (icon) icon.classList.toggle('expanded');
        }
    },

    highlightActiveSidebar() {
        const path = window.location.pathname;
        document.querySelectorAll('.sidebar-item').forEach(item => {
            if (item.getAttribute('href') === path) {
                item.classList.add('active');
            }
        });
    },

    // ==========================================
    // NOTIFICATIONS
    // ==========================================
    async loadNotifications() {
        try {
            const resp = await fetch('/api/notifications', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await resp.json();
            this.renderNotifications(data);
        } catch (e) {}
    },

    renderNotifications(data) {
        const badge = document.getElementById('notifBadge');
        const list = document.getElementById('notifList');
        if (!badge || !list) return;

        if (data.unread_count > 0) {
            badge.textContent = data.unread_count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }

        if (!data.notifications || data.notifications.length === 0) {
            list.innerHTML = '<div class="empty-state-small">' + (this.translations.no_notifications || 'No new notifications') + '</div>';
            return;
        }

        list.innerHTML = data.notifications.map(n => `
            <div class="notification-item ${n.is_read ? '' : 'unread'}" onclick="GyrFalcon.openNotification(${n.id}, '${n.link || ''}')">
                <div class="notif-icon" style="background:${n.type === 'success' ? '#D4EDDA' : n.type === 'warning' ? '#FFF3CD' : '#D1ECF1'}; color:${n.type === 'success' ? '#155724' : n.type === 'warning' ? '#856404' : '#0C5460'}">
                    <i class="${n.icon || 'fas fa-bell'}"></i>
                </div>
                <div>
                    <div class="notif-title">${n.title}</div>
                    <div class="notif-time">${n.created_at}</div>
                </div>
            </div>
        `).join('');
    },

    async openNotification(id, link) {
        await fetch('/api/notifications/read', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ id })
        });
        this.loadNotifications();
        if (link) window.location.href = link;
    },

    // ==========================================
    // APP LAUNCHER
    // ==========================================
    appLauncherData: [],

    async loadAppLauncher() {
        try {
            const resp = await fetch('/api/app-launcher', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await resp.json();
            this.appLauncherData = data.apps || [];
            this.renderAppLauncher(this.appLauncherData);
        } catch (e) {}
    },

    renderAppLauncher(apps) {
        const grid = document.getElementById('appLauncherGrid');
        if (!grid) return;

        grid.innerHTML = apps.map(app => `
            <a href="${app.url}" class="app-tile" data-name="${app.name.toLowerCase()}">
                <div class="app-tile-icon" style="background:${app.color}">
                    <i class="${app.icon}"></i>
                </div>
                <span class="app-tile-name">${app.name}</span>
            </a>
        `).join('');
    },

    // ==========================================
    // LANGUAGE SWITCHING
    // ==========================================
    async switchLang() {
        const newLocale = this.locale === 'en' ? 'ar' : 'en';
        try {
            const resp = await fetch('/api/lang/switch', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ locale: newLocale })
            });
            const data = await resp.json();
            if (data.success) {
                window.location.reload();
            }
        } catch (e) {
            console.error('Language switch failed:', e);
        }
    },

    // ==========================================
    // MODULE MANAGEMENT
    // ==========================================
    async installModule(moduleName) {
        if (!confirm('Install module: ' + moduleName + '?')) return;
        try {
            const resp = await fetch('/api/modules/install', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ module: moduleName })
            });
            const data = await resp.json();
            this.showToast(data.message || data.error, data.success ? 'success' : 'danger');
            if (data.success) {
                setTimeout(() => window.location.reload(), 1000);
            }
        } catch (e) {
            this.showToast('Installation failed', 'danger');
        }
    },

    async uninstallModule(moduleName) {
        if (!confirm('Uninstall module: ' + moduleName + '? This will remove all module data.')) return;
        try {
            const resp = await fetch('/api/modules/uninstall', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ module: moduleName })
            });
            const data = await resp.json();
            this.showToast(data.message || data.error, data.success ? 'success' : 'danger');
            if (data.success) {
                setTimeout(() => window.location.reload(), 1000);
            }
        } catch (e) {
            this.showToast('Uninstallation failed', 'danger');
        }
    },

    // ==========================================
    // VIEW SWITCHING (Kanban/List/Form)
    // ==========================================
    currentView: 'list',

    switchView(view) {
        this.currentView = view;
        document.querySelectorAll('.view-switcher-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === view);
        });
        document.querySelectorAll('.view-panel').forEach(panel => {
            panel.style.display = panel.dataset.view === view ? 'block' : 'none';
        });
    },

    // ==========================================
    // CHARTS
    // ==========================================
    async initCharts() {
        const revenueEl = document.getElementById('revenueChart');
        const ordersEl = document.getElementById('ordersChart');
        if (!revenueEl && !ordersEl) return;

        try {
            const resp = await fetch('/api/dashboard/charts', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await resp.json();

            if (revenueEl && data.revenue_chart) {
                new Chart(revenueEl.getContext('2d'), {
                    type: 'line',
                    data: data.revenue_chart,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: true, position: 'top' } },
                        scales: {
                            y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString() } }
                        }
                    }
                });
            }

            if (ordersEl && data.orders_chart) {
                new Chart(ordersEl.getContext('2d'), {
                    type: 'bar',
                    data: data.orders_chart,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: true, position: 'top' } }
                    }
                });
            }
        } catch (e) {
            console.error('Charts failed:', e);
        }
    },

    // ==========================================
    // TOAST / ALERTS
    // ==========================================
    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type}`;
        toast.style.cssText = 'position:fixed;top:56px;right:20px;z-index:9999;min-width:300px;max-width:500px;animation:slideDown 0.3s ease;';
        toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'danger' ? 'exclamation-circle' : 'info-circle'}"></i> ${message}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    },

    initAlertDismiss() {
        setTimeout(() => {
            document.querySelectorAll('.main-content > .alert').forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transition = 'opacity 0.3s';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    },

    // ==========================================
    // FORM HANDLERS
    // ==========================================
    async submitForm(formId, url, method = 'POST') {
        const form = document.getElementById(formId);
        if (!form) return;

        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        try {
            const resp = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(data)
            });
            const result = await resp.json();
            this.showToast(result.message || result.error, result.success ? 'success' : 'danger');
            if (result.success && result.redirect) {
                setTimeout(() => window.location.href = result.redirect, 800);
            } else if (result.success) {
                setTimeout(() => window.location.reload(), 800);
            }
        } catch (e) {
            this.showToast('Request failed', 'danger');
        }
    },

    // ==========================================
    // CURRENCY CONVERTER
    // ==========================================
    async convertCurrency() {
        const amount = document.getElementById('convertAmount')?.value;
        const from = document.getElementById('convertFrom')?.value;
        const to = document.getElementById('convertTo')?.value;

        if (!amount) return;

        try {
            const resp = await fetch('/api/currencies/convert', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ amount, from, to })
            });
            const data = await resp.json();
            const resultEl = document.getElementById('conversionResult');
            if (resultEl && data.success) {
                resultEl.innerHTML = `<strong>${amount} ${from}</strong> = <strong>${data.formatted}</strong> (Rate: ${data.rate})`;
                resultEl.style.display = 'block';
            }
        } catch (e) {}
    },

    // ==========================================
    // DATA TABLE OPERATIONS
    // ==========================================
    async loadData(url, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        container.innerHTML = '<div class="loading-spinner"><div class="spinner"></div></div>';

        try {
            const resp = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            return await resp.json();
        } catch (e) {
            container.innerHTML = '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><h3>Failed to load data</h3></div>';
            return null;
        }
    },

    // ==========================================
    // TABS
    // ==========================================
    switchTab(tabId) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.tab === tabId);
        });
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.toggle('active', content.id === tabId);
        });
    },

    // ==========================================
    // SEARCH & FILTER
    // ==========================================
    debounceTimer: null,
    
    debounceSearch(callback, delay = 300) {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(callback, delay);
    },

    filterTable(searchValue, tableId) {
        const table = document.getElementById(tableId);
        if (!table) return;
        const rows = table.querySelectorAll('tbody tr');
        const search = searchValue.toLowerCase();

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(search) ? '' : 'none';
        });
    },

    // ==========================================
    // MODAL
    // ==========================================
    openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
    },

    closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    },

    // ==========================================
    // FILE UPLOAD PREVIEW
    // ==========================================
    previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (!preview || !input.files || !input.files[0]) return;

        const reader = new FileReader();
        reader.onload = (e) => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
};

// ==========================================
// GLOBAL FUNCTIONS
// ==========================================
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('collapsed');
    sidebar.classList.toggle('show');
    GyrFalcon.sidebarCollapsed = sidebar.classList.contains('collapsed');
}

function toggleAppLauncher() {
    const modal = document.getElementById('appLauncherModal');
    modal.style.display = modal.style.display === 'none' ? 'flex' : 'none';
    if (modal.style.display === 'flex') {
        document.getElementById('appSearchInput')?.focus();
    }
}

function filterApps(search) {
    const tiles = document.querySelectorAll('.app-tile');
    const s = search.toLowerCase();
    tiles.forEach(tile => {
        tile.style.display = tile.dataset.name.includes(s) ? '' : 'none';
    });
}

function toggleDropdown(id) {
    const dropdown = document.getElementById(id);
    const isVisible = dropdown.style.display !== 'none';
    // Close all dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(d => d.style.display = 'none');
    dropdown.style.display = isVisible ? 'none' : 'block';
}

function switchLanguage() {
    GyrFalcon.switchLang();
}

function markAllRead() {
    fetch('/api/notifications/read', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        body: JSON.stringify({})
    }).then(() => GyrFalcon.loadNotifications());
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => GyrFalcon.init());
