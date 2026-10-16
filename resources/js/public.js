/* ============================================================
   PUBLIC LAYOUT — Custom Scripts
   ============================================================ */
import Alpine from 'alpinejs';
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

    window.addEventListener('pageshow', (event) => {
        const isBackForward = event.persisted ||
            (window.performance &&
             window.performance.navigation &&
             window.performance.navigation.type === 2);
        const isStuck = document.documentElement.classList.contains('nav-loading') ||
                        (document.getElementById('page-skeleton') &&
                         !document.getElementById('page-skeleton').classList.contains('hidden'));

        if (isBackForward || isStuck) {
            const content  = document.getElementById('page-content');
            const skeleton = document.getElementById('page-skeleton');
            if (content) content.classList.remove('hidden');
            if (skeleton) skeleton.classList.add('hidden');

            document.documentElement.classList.remove('nav-loading');

            const oldSpinner = document.getElementById('nav-spinner');
            if (oldSpinner && oldSpinner.parentNode) {
                oldSpinner.parentNode.removeChild(oldSpinner);
            }

            if (typeof LoadingState !== 'undefined' && LoadingState.cancel) {
                LoadingState.cancel('nav');
            }

            sessionStorage.removeItem('skandapras_nav');

            if (typeof window.refreshLucide === 'function') {
                setTimeout(() => window.refreshLucide(), 100);
            }
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

// ============================================================
// Scroll Reveal Observer (Shared)
// ============================================================
(function () {
    const initScrollReveal = () => {
        const elements = document.querySelectorAll('.scroll-reveal:not(.show)');
        if (!elements.length) return;

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            elements.forEach(el => el.classList.add('show'));
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        elements.forEach(el => observer.observe(el));
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initScrollReveal);
    } else {
        initScrollReveal();
    }
})();

// ============================================================
// Preloader logic
// ============================================================
(function () {
    const el = document.getElementById('skandapras-preloader');
    if (!el) return;

    if (sessionStorage.getItem('skandapras_preloader_shown') === '1' ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        el.classList.add('is-hidden');
        return;
    }

    let closed = false;
    function dismissPreloader() {
        if (closed) return;
        closed = true;
        el.classList.add('is-opening');
        setTimeout(function () {
            el.classList.add('is-hidden');
            sessionStorage.setItem('skandapras_preloader_shown', '1');
        }, 300);
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        setTimeout(dismissPreloader, 150);
    } else {
        window.addEventListener('DOMContentLoaded', function () {
            setTimeout(dismissPreloader, 200);
        }, { once: true });
    }

    setTimeout(dismissPreloader, 1000);
})();

// ============================================================
// Chatbot component
// ============================================================
export function registerChatbot() {
    Alpine.data('chatbot', () => {
        const greeting = {
            role: 'bot',
            title: 'Halo!',
            text: 'Ada yang bisa saya bantu tentang SMK Negeri 2 Karanganyar?',
        };

        return {
            isOpen: false,
            typing: false,
            draft: '',
            suggestions: ['Berita terbaru?', 'Jurusan?', 'Sarpras?'],
            messages: [greeting],

        toggle() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.$nextTick(() => {
                    this.scrollDown();
                    this.$refs.input && this.$refs.input.focus();
                });
            }
        },

        scrollDown() {
            this.$nextTick(() => {
                const el = this.$refs.scroller;
                if (el) el.scrollTop = el.scrollHeight;
            });
        },

        clearChat() {
            this.messages = [greeting];
            this.typing = false;
            this.draft = '';
        },

        async send(text) {
            const msg = (text || this.draft).trim();
            if (!msg || this.typing) return;

            const history = this.buildHistory();

            this.messages.push({ role: 'user', text: msg });
            this.draft = '';
            this.typing = true;
            this.scrollDown();

            const result = await this.getReply(msg, history);

            this.typing = false;
            this.messages.push({ role: 'bot', text: result.text, error: result.error });
            this.scrollDown();
        },

        buildHistory() {
            return this.messages
                .slice(1)
                .filter(m => !m.error)
                .slice(-20)
                .map(m => ({
                    role: m.role === 'user' ? 'user' : 'assistant',
                    text: m.text,
                }));
        },

        async getReply(msg, history = []) {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    || document.querySelector('input[name="_token"]')?.value || '';
                const chatUrl = this.$el?.dataset?.chatUrl || '/chatbot/chat';

                const response = await fetch(chatUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ message: msg, history }),
                });

                if (response.status === 429) {
                    let rateMsg = 'Batas penggunaan chatbot tercapai. Silakan coba kembali nanti.';
                    try {
                        const rateData = await response.json();
                        if (rateData.reply) rateMsg = rateData.reply;
                    } catch (_) {}
                    return { text: rateMsg, error: true };
                }

                const data = await response.json();

                if (!response.ok) {
                    let errMsg = data.reply || data.message || 'Maaf, terjadi kendala saat memproses jawaban.';
                    if (data.errors && data.errors.message) {
                        errMsg = data.errors.message[0];
                    }
                    return { text: errMsg, error: true };
                }

                return { text: data.reply || 'Maaf, saya belum mendapatkan jawaban.', error: !data.reply };
            } catch (error) {
                console.error('[Chatbot] Request error:', error);
                return { text: 'Maaf, terjadi kendala teknis atau koneksi internet. Silakan coba kembali sebentar lagi.', error: true };
            }
        },
    };
    });
}
