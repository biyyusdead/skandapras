<div x-data="loadingBar()" x-init="init()" class="fixed top-0 left-0 right-0 z-[9999] h-0.5 pointer-events-none">
    <div x-show="visible" x-transition:enter="transition-opacity duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity duration-300"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="h-full bg-gradient-to-r from-primary-500 via-primary-400 to-primary-600 shadow-[0_0_10px_rgba(108,74,182,0.6)] transition-[width] duration-300 ease-out"
        :style="`width: ${progress}%`"></div>
</div>

@once
    @push('scripts')
        <script>
            function loadingBar() {
                return {
                    visible: false,
                    progress: 0,
                    _tick: null,
                    _hideT: null,

                    init() {
                        // ═══ CEK: kalau sebelumnya loading bar muncul,
                        // berarti kita baru selesai pindah halaman.
                        // Langsung selesaikan (progress ke 100% & hilang).
                        if (sessionStorage.getItem('skandapras_loadingbar') === '1') {
                            sessionStorage.removeItem('skandapras_loadingbar');

                            // Set visible + progress 100%, lalu hide
                            this.visible = true;
                            this.progress = 100;

                            setTimeout(() => {
                                this.visible = false;
                                setTimeout(() => { this.progress = 0; }, 300);
                            }, 200);
                        }

                        // ═══ Show saat link diklik ═══
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

                        // ═══ Show saat form submit ═══
                        window.addEventListener('form-submit', () => this.start());
                    },

                    start() {
                        clearTimeout(this._hideT);
                        this.visible = true;
                        this.progress = 5;
                        clearInterval(this._tick);

                        // ═══ Tandai di sessionStorage bahwa loading bar aktif ═══
                        // Supaya halaman baru tahu & panggil done()
                        sessionStorage.setItem('skandapras_loadingbar', '1');

                        // ═══ Progress naik perlahan ke 90% ═══
                        this._tick = setInterval(() => {
                            const remaining = 90 - this.progress;
                            this.progress += Math.max(remaining * 0.08, 0.5);
                            if (this.progress >= 90) {
                                clearInterval(this._tick);
                            }
                        }, 200);

                        // ═══ Safety: kalau 10 detik belum selesai, paksa done ═══
                        this._hideT = setTimeout(() => {
                            this.done();
                        }, 10000);
                    },

                    done() {
                        clearInterval(this._tick);
                        this.progress = 100;

                        // Hapus flag
                        sessionStorage.removeItem('skandapras_loadingbar');

                        this._hideT = setTimeout(() => {
                            this.visible = false;
                            setTimeout(() => { this.progress = 0; }, 300);
                        }, 250);
                    }
                }
            }
        </script>
    @endpush
@endonce