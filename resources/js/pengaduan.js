// =============================================================
// pengaduan.js — Report Modal Alpine Component
// =============================================================

export function registerReportModal() {
    window.reportModal =  {
            isOpen: false,
            sent: false,
            step: 1,
            submitting: false,
            dragging: false,
            form: { nama: '', room_id: '', item_room_id: '', deskripsi: '' },
            file: null,
            preview: null,
            errors: {},
            rooms: [],
            maxSize: 5 * 1024 * 1024,

            init() {
                // Ambil data rooms dari window global (di-set dari Blade)
                this.rooms = window.pengaduanRoomsData || [];

                this.$watch('isOpen', v => document.body.classList.toggle('overflow-hidden', v));
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                });
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

            open() {
                this.isOpen = true;
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                    if (this.$refs.form) {
                        const first = this.$refs.form.querySelector('input[name="reporter_name"]');
                        if (first) first.focus();
                    }
                });
            },

            close() {
                if (this.submitting) return;
                this.isOpen = false;
            },

            validateStep1() {
                this.errors = {};
                if (!this.form.nama.trim()) this.errors.nama = 'Nama wajib diisi.';
                if (!this.form.room_id) this.errors.room_id = 'Ruangan wajib dipilih.';
                if (!this.form.deskripsi.trim()) this.errors.deskripsi = 'Deskripsi wajib diisi.';
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
            // SUBMIT — AJAX tanpa reload
            // ============================================================
            async submit() {
                if (!this.validateStep1()) {
                    this.step = 1;
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
                        this.$nextTick(() => {
                            if (typeof lucide !== 'undefined') lucide.createIcons();
                        });
                    } else if (response.status === 422) {
                        // Validation error dari Laravel
                        const data = await response.json();
                        if (data.errors) {
                            if (data.errors.reporter_name)  this.errors.nama = data.errors.reporter_name[0];
                            if (data.errors.room_id)        this.errors.room_id = data.errors.room_id[0];
                            if (data.errors.description)    this.errors.deskripsi = data.errors.description[0];
                            if (data.errors.evidence_photo) this.errors.foto = data.errors.evidence_photo[0];
                        }
                        this.step = 1;
                    } else {
                        // Error lain (500, 419, dll)
                        let msg = 'Gagal mengirim laporan. Coba lagi.';
                        try {
                            const data = await response.json();
                            if (data.message) msg = data.message;
                        } catch (_) {}
                        alert(msg);
                    }
                } catch (err) {
                    console.error('[Pengaduan] Submit error:', err);
                    alert('Terjadi kesalahan jaringan. Coba lagi.');
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
                this.step = 1;
                this.form = { nama: '', room_id: '', item_room_id: '', deskripsi: '' };
                this.file = null;
                if (this.preview) URL.revokeObjectURL(this.preview);
                this.preview = null;
                this.errors = {};
                if (this.$refs.foto) this.$refs.foto.value = '';
                if (this.$refs.form) this.$refs.form.reset();
            },
        };
}