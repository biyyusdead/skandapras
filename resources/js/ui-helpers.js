/**
 * UI Helpers — SKANDAPRAS
 * Shared utilities for backend and admin dashboards.
 */

// ============================================================
// 1. Image & Photo Previews
// ============================================================
export function previewImage(event, prefix = 'image') {
    const file = event?.target?.files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        const preview = document.getElementById(`${prefix}-preview`);
        const placeholder = document.getElementById(`${prefix}-placeholder`);
        if (preview) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        }
        if (placeholder) {
            placeholder.classList.add('hidden');
        }
    };
    reader.readAsDataURL(file);
}

export function previewPhoto(event) {
    previewImage(event, 'photo');
}

window.previewImage = previewImage;
window.previewPhoto = previewPhoto;

// ============================================================
// 2. Generic Delete Confirmation
// ============================================================
export function confirmDelete(id, formId = 'delete-form', targetAction = null) {
    const form = document.getElementById(formId);
    if (!form) return;

    if (targetAction) {
        form.action = targetAction.replace(':id', id);
    } else {
        // Default: replace last segment or append id
        const base = form.action.replace(/\/+$/, '');
        form.action = base.replace(/\/\d+$/, '') + `/${id}`;
    }

    window.dispatchEvent(new CustomEvent('open-confirm', {
        detail: { id: 'delete-item', action: form.action, method: 'DELETE' }
    }));
}

window.confirmDelete = confirmDelete;

// ============================================================
// 3. Bulk Action Bar Component & Helpers
// ============================================================
export function bulkAction() {
    return {
        selected: [],
        currentAction: '',

        submit(action, requireConfirm) {
            if (this.selected.length === 0) return;

            this.currentAction = action;
            const inputAction = this.$refs.form.querySelector('input[name="bulk_action"]');
            if (inputAction) inputAction.value = action;

            if (requireConfirm) {
                const label = action === 'delete' ? 'menghapus' : action;
                if (!confirm(`Yakin ingin ${label} ${this.selected.length} data terpilih?`)) {
                    return;
                }
            }

            this.$refs.form.submit();
        },

        clear() {
            this.selected = [];
            document.querySelectorAll('[data-bulk-checkbox]').forEach(cb => cb.checked = false);
            document.querySelectorAll('[data-bulk-select-all]').forEach(cb => cb.checked = false);
        },
    };
}

export function toggleAll(masterCheckbox) {
    const checkboxes = document.querySelectorAll('[data-bulk-checkbox]');
    checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);

    const selected = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);
    const bar = document.querySelector('[x-data*="bulkAction"]');
    if (bar && window.Alpine) {
        const data = Alpine.$data(bar);
        if (data) data.selected = selected;
    }
}

export function toggleOne(checkbox) {
    const all = document.querySelectorAll('[data-bulk-checkbox]');
    const selected = Array.from(all).filter(cb => cb.checked).map(cb => cb.value);

    const master = document.querySelector('[data-bulk-select-all]');
    if (master) {
        master.checked = all.length > 0 && selected.length === all.length;
        master.indeterminate = selected.length > 0 && selected.length < all.length;
    }

    const bar = document.querySelector('[x-data*="bulkAction"]');
    if (bar && window.Alpine) {
        const data = Alpine.$data(bar);
        if (data) data.selected = selected;
    }
}

window.bulkAction = bulkAction;
window.toggleAll = toggleAll;
window.toggleOne = toggleOne;

// ============================================================
// 4. Loading Bar Component
// ============================================================
export function loadingBar() {
    return {
        visible: false,
        progress: 0,
        _tick: null,
        _hideT: null,

        init() {
            if (sessionStorage.getItem('skandapras_loadingbar') === '1') {
                sessionStorage.removeItem('skandapras_loadingbar');
                this.visible = true;
                this.progress = 100;

                setTimeout(() => {
                    this.visible = false;
                    setTimeout(() => { this.progress = 0; }, 300);
                }, 200);
            }

            document.addEventListener('click', (e) => {
                const a = e.target.closest('a');
                if (!a) return;
                const href = a.getAttribute('href');
                if (!href) return;
                if (
                    a.target === '_blank' ||
                    a.hasAttribute('download') ||
                    href.startsWith('#') ||
                    href.startsWith('javascript:') ||
                    href.startsWith('mailto:') ||
                    href.startsWith('tel:') ||
                    a.hostname !== window.location.hostname
                ) return;
                if (a.pathname === window.location.pathname && a.search === window.location.search) return;

                this.start();
            }, true);

            window.addEventListener('form-submit', () => this.start());
        },

        start() {
            clearTimeout(this._hideT);
            this.visible = true;
            this.progress = 5;
            clearInterval(this._tick);

            sessionStorage.setItem('skandapras_loadingbar', '1');

            this._tick = setInterval(() => {
                const remaining = 90 - this.progress;
                this.progress += Math.max(remaining * 0.08, 0.5);
                if (this.progress >= 90) {
                    clearInterval(this._tick);
                }
            }, 200);

            this._hideT = setTimeout(() => {
                this.done();
            }, 10000);
        },

        done() {
            clearInterval(this._tick);
            this.progress = 100;
            sessionStorage.removeItem('skandapras_loadingbar');

            this._hideT = setTimeout(() => {
                this.visible = false;
                setTimeout(() => { this.progress = 0; }, 300);
            }, 250);
        }
    };
}

window.loadingBar = loadingBar;

// ============================================================
// 5. Notification Dropdown Component
// ============================================================
export function notificationDropdown() {
    return {
        open: false,
        loading: false,
        notifications: [],
        unreadCount: 0,

        init() {
            const initial = parseInt(this.$el.dataset.unreadCount || '0', 10);
            this.unreadCount = Number.isFinite(initial) ? initial : 0;
        },

        toggle() {
            this.open = !this.open;
            if (this.open) this.fetchNotifications();
        },

        close() {
            this.open = false;
        },

        async fetchNotifications() {
            this.loading = true;
            try {
                const url = this.$el.dataset.dropdownUrl || '/notifications/dropdown';
                const res = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                const data = await res.json();
                this.notifications = data.notifications || [];
                this.unreadCount = data.unread_count ?? 0;
                this.$nextTick(() => window.refreshLucide?.());
            } catch (e) {
                console.error('[Notifications] Fetch error:', e);
            } finally {
                this.loading = false;
            }
        },

        async markAllRead() {
            try {
                const url = this.$el.dataset.markAllUrl || '/notifications/mark-all';
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                this.unreadCount = 0;
                this.notifications.forEach(n => n.read = true);
            } catch (e) {
                console.error('[Notifications] Mark all read error:', e);
            }
        }
    };
}

window.notificationDropdown = notificationDropdown;

// ============================================================
// 6. Form Submit Dispatcher
// ============================================================
document.addEventListener('submit', (e) => {
    if (e.target.tagName === 'FORM' && e.target.checkValidity?.()) {
        setTimeout(() => window.dispatchEvent(new CustomEvent('form-submit')), 0);
    }
}, true);

// ============================================================
// 7. Backend BFCACHE & Skeleton Instant Navigation
// ============================================================
window.addEventListener('pageshow', function (event) {
    if (event.persisted) {
        const content  = document.getElementById('page-content');
        const skeleton = document.getElementById('page-skeleton');

        if (content) content.classList.remove('hidden');
        if (skeleton) skeleton.classList.add('hidden');

        document.documentElement.classList.remove('nav-loading');

        const sp = document.getElementById('nav-spinner');
        if (sp && sp.parentNode) sp.parentNode.removeChild(sp);

        if (typeof window.LoadingState !== 'undefined' && window.LoadingState.cancel) {
            window.LoadingState.cancel('nav');
            window.LoadingState.cancel('form-btn');
        }

        sessionStorage.removeItem('skandapras_nav');
    }
});

(function () {
    if (sessionStorage.getItem('skandapras_nav') !== '1') return;

    sessionStorage.removeItem('skandapras_nav');

    const hasContent  = !!document.getElementById('page-content');
    const hasSkeleton = !!document.getElementById('page-skeleton');

    if (!hasContent || !hasSkeleton) {
        document.documentElement.classList.remove('nav-loading');
        return;
    }

    let finished = false;

    const activate = () => {
        if (finished) return;
        finished = true;

        const content  = document.getElementById('page-content');
        const skeleton = document.getElementById('page-skeleton');

        if (content && skeleton) {
            content.classList.add('hidden');
            skeleton.classList.remove('hidden');
        }

        document.documentElement.classList.remove('nav-loading');

        setTimeout(() => {
            const c = document.getElementById('page-content');
            const s = document.getElementById('page-skeleton');
            if (c && s) {
                c.classList.remove('hidden');
                s.classList.add('hidden');
            }

            const sp = document.getElementById('nav-spinner');
            if (sp && sp.parentNode) sp.parentNode.removeChild(sp);
        }, 400);
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', activate);
    } else {
        activate();
    }

    setTimeout(() => {
        if (!finished) {
            finished = true;
            document.documentElement.classList.remove('nav-loading');
            const c = document.getElementById('page-content');
            const s = document.getElementById('page-skeleton');
            if (c) c.classList.remove('hidden');
            if (s) s.classList.add('hidden');
        }
    }, 3000);
})();

document.addEventListener('click', (e) => {
    const a = e.target.closest('a');
    if (!a) return;
    const href = a.getAttribute('href');
    if (!href) return;
    if (
        a.target === '_blank' ||
        a.hasAttribute('download') ||
        href.startsWith('#') ||
        href.startsWith('javascript:') ||
        href.startsWith('mailto:') ||
        href.startsWith('tel:') ||
        a.hostname !== window.location.hostname
    ) return;
    if (a.pathname === window.location.pathname && a.search === window.location.search) return;

    sessionStorage.setItem('skandapras_nav', '1');

    const content  = document.getElementById('page-content');
    const skeleton = document.getElementById('page-skeleton');
    if (!content || !skeleton) return;

    if (typeof window.LoadingState !== 'undefined') {
        window.LoadingState.start('nav', {
            onSkeleton: () => {
                content.classList.add('hidden');
                skeleton.classList.remove('hidden');
            },
            onSpinner: () => {
                if (!document.getElementById('nav-spinner')) {
                    const sp = document.createElement('div');
                    sp.id = 'nav-spinner';
                    sp.className = 'flex flex-col items-center justify-center py-8 gap-3';
                    sp.innerHTML = `
                        <span class="inline-block w-10 h-10 rounded-full border-4 border-primary-500 border-t-transparent animate-spin"></span>
                        <p class="text-sm text-surface-500">Masih memuat, mohon tunggu...</p>
                    `;
                    skeleton.prepend(sp);
                }
            },
        });
    }
}, true);
