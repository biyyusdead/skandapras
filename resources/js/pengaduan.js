// =============================================================
// pengaduan.js — Report Modal Alpine Component
// =============================================================

export function registerReportModal() {
    window.reportModal =  {
            isOpen: false,
            sent: false,
            roleChosen: false,
            step: 1,
            submitting: false,
            dragging: false,
            form: {
                reporter_type: 'siswa',
                nama: '',
                phone: '',
                identifier: '',
                student_class: '',
                student_major: '',
                room_id: '',
                item_room_id: '',
                deskripsi: ''
            },
            file: null,
            preview: null,
            errors: {},
            rooms: [],
            maxSize: 5 * 1024 * 1024,
            turnstileConfig: window.turnstileConfig || { configured: false, siteKey: '', action: 'pengaduan_sarpras' },
            turnstileConfigured: false,
            turnstileToken: '',
            turnstileWidgetId: null,
            turnstileError: '',

            init() {
                // Ambil data rooms dari window global (di-set dari Blade)
                this.rooms = window.pengaduanRoomsData || [];
                this.turnstileConfig = window.turnstileConfig || { configured: false, siteKey: '', action: 'pengaduan_sarpras' };
                this.turnstileConfigured = Boolean(this.turnstileConfig?.configured && this.turnstileConfig?.siteKey);

                this.$watch('isOpen', v => {
                    document.body.classList.toggle('overflow-hidden', v);
                    if (!v) {
                        this.resetTurnstile();
                    }
                });

                this.$watch('step', s => {
                    if (s === 3) {
                        this.$nextTick(() => this.initTurnstileWidget());
                    }
                });

                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                });
            },

            get reporterTypeLabel() {
                if (this.form.reporter_type === 'siswa') return 'Siswa';
                if (this.form.reporter_type === 'guru_tendik') return 'Guru / Tenaga Kependidikan';
                if (this.form.reporter_type === 'pengunjung') return 'Pengunjung Umum';
                return 'Pengunjung Umum';
            },

            get filteredItems() {
                const room = this.rooms.find(r => r.id == this.form.room_id);
                return room ? room.items : [];
            },

            get roomName() {
                const room = this.rooms.find(r => r.id == this.form.room_id);
                return room ? room.name : '';
            },

            get itemRoomName() {
                if (!this.form.item_room_id) return null;
                const room = this.rooms.find(r => r.id == this.form.room_id);
                if (!room) return null;
                const item = room.items.find(i => i.id == this.form.item_room_id);
                return item ? item.name : null;
            },

            circle(n) {
                return this.step >= n
                    ? 'bg-gradient-to-br from-[#8E71D2] to-[#5A3AA4] text-white'
                    : 'bg-slate-200 text-slate-400';
            },

            label(n) {
                return this.step >= n ? 'text-[#6C4AB6]' : 'text-slate-400';
            },

            selectRole(type) {
                this.form.reporter_type = type;
                this.roleChosen = true;
                this.step = 1;
                this.errors = {};
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                    if (type !== 'pengunjung' && this.$refs.form) {
                        const first = this.$refs.form.querySelector('input[name="reporter_name"]');
                        if (first) first.focus();
                    }
                });
            },

            resetRole() {
                this.roleChosen = false;
                this.step = 1;
                this.resetTurnstile();
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                });
            },

            open() {
                this.isOpen = true;
                this.roleChosen = false;
                this.step = 1;
                this.resetTurnstile();
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                });
            },

            close() {
                if (this.submitting) return;
                this.isOpen = false;
            },

            validateStep1() {
                this.errors = {};
                if (!this.form.reporter_type) this.errors.reporter_type = 'Status pelapor wajib dipilih.';
                if (!this.form.nama.trim()) this.errors.nama = 'Nama lengkap wajib diisi.';
                if (!this.form.phone.trim()) this.errors.phone = 'Nomor telepon/kontak wajib diisi.';

                if (this.form.reporter_type === 'siswa') {
                    if (!this.form.identifier.trim()) this.errors.identifier = 'NIS/NISN wajib diisi.';
                    if (!this.form.student_class.trim()) this.errors.student_class = 'Kelas wajib diisi.';
                    if (!this.form.student_major.trim()) this.errors.student_major = 'Jurusan wajib diisi.';
                } else if (this.form.reporter_type === 'guru_tendik') {
                    if (!this.form.identifier.trim()) this.errors.identifier = 'NIP / NUPTK wajib diisi.';
                }

                if (!this.form.room_id) this.errors.room_id = 'Ruangan wajib dipilih.';
                if (!this.form.deskripsi.trim()) {
                    this.errors.deskripsi = 'Deskripsi masalah wajib diisi.';
                } else if (this.form.deskripsi.trim().length < 10) {
                    this.errors.deskripsi = 'Deskripsi masalah minimal 10 karakter.';
                }

                return Object.keys(this.errors).length === 0;
            },

            next() {
                if (this.step === 1 && !this.validateStep1()) return;
                if (this.step < 3) {
                    this.step++;
                    this.$nextTick(() => {
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    });
                }
            },

            back() {
                this.errors = {};
                if (this.step > 1) {
                    this.step--;
                    this.$nextTick(() => {
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                    });
                }
            },

            // ============================================================
            // TURNSTILE WIDGET & LIFECYCLE
            // ============================================================
            initTurnstileWidget() {
                if (!this.turnstileConfigured) return;
                const container = document.getElementById('cf-turnstile-container');
                if (!container) return;

                if (this.turnstileWidgetId !== null) return;

                const renderFn = () => {
                    if (typeof window.turnstile === 'undefined') {
                        setTimeout(renderFn, 150);
                        return;
                    }

                    try {
                        this.turnstileWidgetId = window.turnstile.render(container, {
                            sitekey: this.turnstileConfig.siteKey,
                            action: this.turnstileConfig.action || 'pengaduan_sarpras',
                            theme: 'light',
                            callback: (token) => {
                                this.turnstileToken = token;
                                this.turnstileError = '';
                                if (this.errors['cf-turnstile-response']) {
                                    delete this.errors['cf-turnstile-response'];
                                }
                            },
                            'expired-callback': () => {
                                this.turnstileToken = '';
                                this.turnstileError = 'Token verifikasi telah kedaluwarsa. Silakan ulangi verifikasi CAPTCHA.';
                            },
                            'error-callback': () => {
                                this.turnstileToken = '';
                                this.turnstileError = 'Gagal memuat verifikasi keamanan. Silakan periksa koneksi internet atau muat ulang.';
                            },
                        });
                    } catch (e) {
                        console.error('[Turnstile] Render error:', e);
                    }
                };

                renderFn();
            },

            resetTurnstile() {
                this.turnstileToken = '';
                this.turnstileError = '';
                if (typeof window.turnstile !== 'undefined' && this.turnstileWidgetId !== null) {
                    try {
                        window.turnstile.reset(this.turnstileWidgetId);
                    } catch (e) {
                        console.warn('[Turnstile] Reset failed:', e);
                    }
                }
            },

            // ============================================================
            // SUBMIT — AJAX tanpa reload
            // ============================================================
            async submit() {
                if (!this.validateStep1()) {
                    this.step = 1;
                    return;
                }

                // Validasi Turnstile sebelum submit jika dikonfigurasi
                if (this.turnstileConfigured && !this.turnstileToken) {
                    this.errors['cf-turnstile-response'] = 'Silakan selesaikan verifikasi keamanan (CAPTCHA) terlebih dahulu.';
                    this.step = 3;
                    return;
                }

                if (this.submitting) return;
                this.submitting = true;
                this.errors = {};

                try {
                    const formData = new FormData(this.$refs.form);
                    const response = await fetch(this.$refs.form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });

                    if (response.ok) {
                        // Sukses — tampilkan notif sukses, tetap di halaman
                        this.sent = true;
                        this.resetTurnstile();
                        this.$nextTick(() => {
                            if (typeof lucide !== 'undefined') lucide.createIcons();
                        });
                    } else if (response.status === 422) {
                        // Validation error dari Laravel
                        const data = await response.json();
                        if (data.errors) {
                            if (data.errors.reporter_type)         this.errors.reporter_type = data.errors.reporter_type[0];
                            if (data.errors.reporter_name)         this.errors.nama = data.errors.reporter_name[0];
                            if (data.errors.reporter_phone)        this.errors.phone = data.errors.reporter_phone[0];
                            if (data.errors.reporter_identifier)   this.errors.identifier = data.errors.reporter_identifier[0];
                            if (data.errors.student_class)         this.errors.student_class = data.errors.student_class[0];
                            if (data.errors.student_major)         this.errors.student_major = data.errors.student_major[0];
                            if (data.errors.room_id)               this.errors.room_id = data.errors.room_id[0];
                            if (data.errors.description)           this.errors.deskripsi = data.errors.description[0];
                            if (data.errors.evidence_photo)        this.errors.foto = data.errors.evidence_photo[0];
                            if (data.errors['cf-turnstile-response']) {
                                this.errors['cf-turnstile-response'] = data.errors['cf-turnstile-response'][0];
                            }
                        }

                        // Jika ada error pada step 1, kembali ke step 1
                        if (data.errors && (data.errors.reporter_name || data.errors.reporter_phone || data.errors.reporter_identifier || data.errors.room_id || data.errors.description)) {
                            this.step = 1;
                        }

                        this.resetTurnstile();
                    } else if (response.status === 429) {
                        let msg = 'Terlalu banyak permintaan pengiriman pengaduan. Demi keamanan, silakan tunggu beberapa menit.';
                        try {
                            const data = await response.json();
                            if (data.message) msg = data.message;
                        } catch (_) {}
                        this.errors['cf-turnstile-response'] = msg;
                        this.resetTurnstile();
                    } else {
                        // Error lain (500, 419, dll)
                        let msg = 'Gagal mengirim laporan. Coba lagi.';
                        try {
                            const data = await response.json();
                            if (data.message) msg = data.message;
                        } catch (_) {}
                        alert(msg);
                        this.resetTurnstile();
                    }
                } catch (err) {
                    console.error('[Pengaduan] Submit error:', err);
                    alert('Terjadi kesalahan jaringan. Coba lagi.');
                    this.resetTurnstile();
                } finally {
                    this.submitting = false;
                }
            },

            // ============================================================
            // FILE HANDLING
            // ============================================================
            pickFile() {
                this.$refs.foto.click();
            },

            onSelect(e) {
                this.setFile(e.target.files[0], false);
            },

            onDrop(e) {
                this.dragging = false;
                const f = e.dataTransfer.files[0];
                if (f) this.setFile(f, true);
            },

            setFile(file, syncInput) {
                if (!file) return;
                if (!['image/jpeg', 'image/png'].includes(file.type)) {
                    alert('Format harus JPG atau PNG.');
                    return;
                }
                if (file.size > this.maxSize) {
                    alert('Maksimal 5MB.');
                    return;
                }
                if (this.preview) URL.revokeObjectURL(this.preview);
                this.file = file;
                this.preview = URL.createObjectURL(file);
                if (syncInput) {
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    this.$refs.foto.files = dt.files;
                }
            },

            removeFile() {
                if (this.preview) URL.revokeObjectURL(this.preview);
                this.file = null;
                this.preview = null;
                if (this.$refs.foto) this.$refs.foto.value = '';
            },

            // ============================================================
            // RESET setelah sukses
            // ============================================================
            closeAndReset() {
                this.isOpen = false;
                this.sent = false;
                this.roleChosen = false;
                this.step = 1;
                this.resetTurnstile();
                this.form = {
                    reporter_type: 'siswa',
                    nama: '',
                    phone: '',
                    identifier: '',
                    student_class: '',
                    student_major: '',
                    room_id: '',
                    item_room_id: '',
                    deskripsi: ''
                };
                this.file = null;
                if (this.preview) URL.revokeObjectURL(this.preview);
                this.preview = null;
                this.errors = {};
                if (this.$refs.foto) this.$refs.foto.value = '';
                if (this.$refs.form) this.$refs.form.reset();
            },
        };
}