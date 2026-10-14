/**
 * Loading State Manager
 * - 0-1s   : tidak menampilkan apa-apa
 * - 1-3s   : skeleton
 * - >3s    : spinner
 */

const LoadingState = {
    THRESHOLD_SKELETON: 1000,
    THRESHOLD_SPINNER: 3000,
    _timers: {},

    start(key, callbacks) {
        callbacks = callbacks || {};
        this.cancel(key);

        this._timers[key] = {
            skeleton: setTimeout(function () {
                if (callbacks.onSkeleton) callbacks.onSkeleton();
            }, this.THRESHOLD_SKELETON),

            spinner: setTimeout(function () {
                if (callbacks.onSpinner) callbacks.onSpinner();
            }, this.THRESHOLD_SPINNER),
        };
    },

    done(key, onDone) {
        this.cancel(key);
        if (onDone) onDone();
    },

    cancel(key) {
        if (!this._timers[key]) return;
        clearTimeout(this._timers[key].skeleton);
        clearTimeout(this._timers[key].spinner);
        delete this._timers[key];
    },
};

window.LoadingState = LoadingState;

/* ============================================================
   AUTO: SPINNER DI TOMBOL SUBMIT
   ============================================================ */
document.addEventListener('form-submit', function () {
    const activeForm = document.activeElement
        ? document.activeElement.closest('form')
        : null;
    if (!activeForm) return;

    if (activeForm.dataset.loadingSubmitting === '1') return;
    activeForm.dataset.loadingSubmitting = '1';

    const btn = activeForm.querySelector('button[type="submit"], button:not([type])');
    if (!btn) return;

    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.classList.add('opacity-70', 'cursor-not-allowed');

    LoadingState.start('form-btn', {
        onSkeleton: function () {
            btn.innerHTML = '<span class="inline-flex items-center gap-2">' +
                '<span class="inline-block w-4 h-4 rounded-full border-2 border-white/40 border-t-white animate-spin"></span>' +
                '<span>Memproses...</span>' +
                '</span>';
        },
        onSpinner: function () {
            // Sudah spinner dari 1s, tidak perlu ubah
        },
    });

    setTimeout(function () {
        if (activeForm.dataset.loadingSubmitting === '1') {
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            btn.classList.remove('opacity-70', 'cursor-not-allowed');
            delete activeForm.dataset.loadingSubmitting;
        }
    }, 30000);
});