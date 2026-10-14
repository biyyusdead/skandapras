/* ============================================================
   PUBLIC LAYOUT — Custom Scripts
   ============================================================ */

// ============================================================
// Skeleton instant — fix bfcache + nav loading
// ============================================================
(function () {
    const hasSkeleton = !!document.getElementById('page-skeleton');
    const hasContent  = !!document.getElementById('page-content');

    if (!hasSkeleton || !hasContent) {
        document.documentElement.classList.remove('nav-loading');
        sessionStorage.removeItem('skandapras_nav');
        return;
    }

    if (document.documentElement.classList.contains('nav-loading')) {
        sessionStorage.removeItem('skandapras_nav');

        const finish = () => {
            document.documentElement.classList.remove('nav-loading');
            const content  = document.getElementById('page-content');
            const skeleton = document.getElementById('page-skeleton');
            if (content)  content.classList.remove('hidden');
            if (skeleton) skeleton.classList.add('hidden');
        };

        finish();
    }

    window.addEventListener('pageshow', () => {
        const skeleton = document.getElementById('page-skeleton');
        const content  = document.getElementById('page-content');
        const oldSpinner = document.getElementById('nav-spinner');

        if (skeleton && !skeleton.classList.contains('hidden')) {
            if (!document.documentElement.classList.contains('nav-loading')) {
                skeleton.classList.add('hidden');
                if (content) content.classList.remove('hidden');
            }
        }

        if (oldSpinner && oldSpinner.parentNode) {
            oldSpinner.parentNode.removeChild(oldSpinner);
        }
    });
})();

// ============================================================
// Navigasi skeleton — klik link
// ============================================================
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

    if (typeof LoadingState !== 'undefined') {
        LoadingState.start('nav', {
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

// ============================================================
// Navigasi skeleton — submit form GET
// ============================================================
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (form.tagName !== 'FORM') return;
    if (form.method.toUpperCase() !== 'GET') return;
    if (form.closest('[data-modal]') || form.querySelector('[name="_method"]')) return;
    sessionStorage.setItem('skandapras_nav', '1');
}, true);

// ============================================================
// Scroll progress bar
// ============================================================
window.addEventListener('scroll', () => {
    const h = document.documentElement.scrollHeight - window.innerHeight;
    const progress = h > 0 ? (window.scrollY / h) : 0;
    const bar = document.getElementById('scroll-progress');
    if (bar) bar.style.transform = `scaleX(${progress})`;
}, { passive: true });

// ============================================================
// Back to top button
// ============================================================
(function () {
    const btn = document.getElementById('back-to-top');
    if (!btn) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 500) {
            btn.classList.remove('opacity-0', 'translate-y-4', 'pointer-events-none');
        } else {
            btn.classList.add('opacity-0', 'translate-y-4', 'pointer-events-none');
        }
    }, { passive: true });

    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();

