<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Admission;
use App\Models\Category;
use App\Models\Department;
use App\Models\Extracurricular;
use App\Models\FeaturedProduct;
use App\Models\Internship;
use App\Models\Item;
use App\Models\ItemRoom;
use App\Models\News;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\SchoolProfile;
use App\Models\SchoolRule;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FullDemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@skandapras.test')->first();

        // ==========================================================
        // 1. SCHOOL PROFILE (dengan logo placeholder)
        // ==========================================================
        SchoolProfile::updateOrCreate(
            ['npsn' => '20332001'],
            [
                'school_name' => 'SMK Negeri 2 Karanganyar',
                'address' => 'Jl. Ronggowarsito, Bejen, Kec. Karanganyar, Kabupaten Karanganyar, Jawa Tengah 57716',
                'phone' => '(0271) 495123',
                'email' => 'info@smkn2karanganyar.sch.id',
                'principal_name' => 'Drs. Contoh Kepala Sekolah, M.Pd.',
                'accreditation' => 'A',
                'education_level' => 'SMK',
                'description' => 'SMK Negeri 2 Karanganyar merupakan sekolah menengah kejuruan negeri di Kabupaten Karanganyar, Jawa Tengah. Berdiri sejak tahun 1997 dengan berbagai program keahlian unggulan yang siap mencetak lulusan kompeten di dunia industri.',
                'logo' => null, // tetap null, tidak pakai file
            ]
        );

        // ==========================================================
        // 2. DEPARTMENTS (Jurusan)
        // ==========================================================
        $departments = [
            ['name' => 'Rekayasa Perangkat Lunak', 'code' => 'RPL', 'description' => 'Jurusan yang mempelajari pengembangan perangkat lunak, aplikasi web, dan mobile.'],
            ['name' => 'Teknik Komputer dan Jaringan', 'code' => 'TKJ', 'description' => 'Jurusan yang mempelajari instalasi jaringan, administrasi server, dan keamanan siber.'],
            ['name' => 'Multimedia', 'code' => 'MM', 'description' => 'Jurusan yang mempelajari desain grafis, videografi, animasi, dan fotografi.'],
            ['name' => 'Akuntansi dan Keuangan Lembaga', 'code' => 'AKL', 'description' => 'Jurusan yang mempelajari akuntansi, perpajakan, dan pengelolaan keuangan.'],
            ['name' => 'Otomatisasi dan Tata Kelola Perkantoran', 'code' => 'OTKP', 'description' => 'Jurusan yang mempelajari administrasi perkantoran dan manajemen dokumen.'],
        ];
        foreach ($departments as $d) {
            Department::updateOrCreate(['code' => $d['code']], array_merge($d, ['is_active' => true]));
        }

        // ==========================================================
        // 3. TEACHERS (Guru) — pakai picsum placeholder
        // ==========================================================
        $teachers = [
            ['name' => 'Budi Santoso, S.Kom.', 'nip' => '197501012005011001', 'position' => 'Guru RPL', 'email' => 'budi@smkn2kra.sch.id'],
            ['name' => 'Siti Aminah, S.Pd.', 'nip' => '198002022006022002', 'position' => 'Guru TKJ', 'email' => 'siti@smkn2kra.sch.id'],
            ['name' => 'Ahmad Fauzi, S.Ds.', 'nip' => '198503032010031003', 'position' => 'Guru Multimedia', 'email' => 'ahmad@smkn2kra.sch.id'],
            ['name' => 'Dewi Lestari, S.E.', 'nip' => '198804042012042004', 'position' => 'Guru AKL', 'email' => 'dewi@smkn2kra.sch.id'],
            ['name' => 'Rudi Hartono, S.Pd.', 'nip' => '199005052015051005', 'position' => 'Guru OTKP', 'email' => 'rudi@smkn2kra.sch.id'],
            ['name' => 'Maya Anggraini, S.Pd.', 'nip' => '199206062017062006', 'position' => 'Guru Bahasa', 'email' => 'maya@smkn2kra.sch.id'],
            ['name' => 'Hendra Wijaya, S.Or.', 'nip' => '199307072018071007', 'position' => 'Guru PJOK', 'email' => 'hendra@smkn2kra.sch.id'],
            ['name' => 'Lina Marlina, S.Ag.', 'nip' => '199408082019082008', 'position' => 'Guru PAI', 'email' => 'lina@smkn2kra.sch.id'],
        ];
        foreach ($teachers as $i => $t) {
            Teacher::updateOrCreate(
                ['nip' => $t['nip']],
                array_merge($t, [
                    'phone' => '0812' . str_pad($i + 1, 8, '0', STR_PAD_LEFT),
                    'photo' => null, // pakai foto placeholder di view, atau isi URL di bawah
                    'is_active' => true,
                ])
            );
        }

        // ==========================================================
        // 4. EXTRACURRICULARS (Ekskul) — pakai URL placeholder
        // ==========================================================
        $ekskuls = [
            ['name' => 'Pramuka', 'description' => 'Kegiatan kepramukaan untuk membentuk karakter dan kemandirian siswa.'],
            ['name' => 'PMR', 'description' => 'Palang Merah Remaja, kegiatan kemanusiaan dan kesehatan.'],
            ['name' => 'Futsal', 'description' => 'Ekstrakurikuler olahraga futsal untuk siswa putra.'],
            ['name' => 'Basket', 'description' => 'Ekstrakurikuler olahraga basket untuk siswa putra dan putri.'],
            ['name' => 'Paskibra', 'description' => 'Pasukan Pengibar Bendera, melatih kedisiplinan dan kepemimpinan.'],
            ['name' => 'Robotik', 'description' => 'Kegiatan merakit dan memprogram robot untuk kompetisi.'],
            ['name' => 'English Club', 'description' => 'Klub bahasa Inggris untuk meningkatkan kemampuan speaking.'],
            ['name' => 'Jurnalistik', 'description' => 'Kegiatan jurnalistik dan penulisan berita sekolah.'],
        ];
        foreach ($ekskuls as $i => $e) {
            Extracurricular::updateOrCreate(
                ['name' => $e['name']],
                array_merge($e, [
                    'image' => 'https://picsum.photos/seed/ekskul-' . ($i + 1) . '/600/400',
                    'is_active' => true,
                ])
            );
        }

        // ==========================================================
        // 5. ACHIEVEMENTS (Prestasi) — pakai URL placeholder
        // ==========================================================
        $achievements = [
            ['title' => 'Juara 1 LKS Web Design Tingkat Provinsi', 'category' => 'Akademik', 'date' => '2024-05-15'],
            ['title' => 'Juara 2 LKS Network Administration', 'category' => 'Akademik', 'date' => '2024-05-16'],
            ['title' => 'Juara 1 Futsal Pelajar Kabupaten', 'category' => 'Olahraga', 'date' => '2024-06-20'],
            ['title' => 'Juara 3 Desain Poster Nasional', 'category' => 'Seni', 'date' => '2024-07-10'],
            ['title' => 'Juara 1 English Speech Contest', 'category' => 'Bahasa', 'date' => '2024-08-05'],
            ['title' => 'Finalis Robotik Tingkat Nasional', 'category' => 'Teknologi', 'date' => '2024-09-12'],
        ];
        foreach ($achievements as $i => $a) {
            Achievement::updateOrCreate(
                ['title' => $a['title']],
                [
                    'description' => 'Prestasi yang membanggakan bagi sekolah dan siswa.',
                    'category' => $a['category'],
                    'achievement_date' => $a['date'],
                    'image' => 'https://picsum.photos/seed/prestasi-' . ($i + 1) . '/600/400',
                ]
            );
        }

        // ==========================================================
        // 6. SCHOOL RULES (Tata Tertib)
        // ==========================================================
        $rules = [
            [
                'title' => 'Tata Tertib Siswa',
                'content' => "1. Siswa wajib hadir tepat waktu sebelum bel masuk berbunyi.\n2. Siswa wajib memakai seragam lengkap sesuai jadwal.\n3. Siswa dilarang membawa HP tanpa izin guru.\n4. Siswa wajib menjaga kebersihan dan ketertiban sekolah.\n5. Siswa dilarang merokok, membawa senjata tajam, dan narkoba.",
            ],
            [
                'title' => 'Tata Tertib Guru dan Karyawan',
                'content' => "1. Guru wajib hadir 15 menit sebelum jam mengajar.\n2. Guru wajib berpakaian rapi dan sopan.\n3. Guru wajib mengisi daftar hadir dan jurnal mengajar.\n4. Guru wajib membimbing siswa dengan penuh tanggung jawab.",
            ],
            [
                'title' => 'Tata Tertib Penggunaan Laboratorium',
                'content' => "1. Siswa wajib memakai alas kaki tertutup.\n2. Dilarang makan dan minum di dalam lab.\n3. Wajib menjaga peralatan lab.\n4. Kerusakan akibat kelalaian menjadi tanggung jawab siswa.",
            ],
        ];
        foreach ($rules as $r) {
            SchoolRule::updateOrCreate(['title' => $r['title']], array_merge($r, ['is_active' => true]));
        }

        // ==========================================================
        // 7. INTERNSHIPS (PKL & BKK)
        // ==========================================================
        $internships = [
            ['title' => 'PKL di PT. Telkom Indonesia', 'company' => 'PT. Telkom Indonesia', 'start' => '2024-07-01', 'end' => '2024-12-31'],
            ['title' => 'PKL di CV. Media Kreatif Solo', 'company' => 'CV. Media Kreatif Solo', 'start' => '2024-07-01', 'end' => '2024-12-31'],
            ['title' => 'PKL di Bank Jateng Cabang Karanganyar', 'company' => 'Bank Jateng', 'start' => '2024-07-01', 'end' => '2024-12-31'],
            ['title' => 'BKK: Lowongan Programmer di PT. ABC', 'company' => 'PT. ABC Digital', 'start' => '2025-01-01', 'end' => '2025-03-31'],
            ['title' => 'BKK: Lowongan Desainer Grafis', 'company' => 'Studio Kreatif Jogja', 'start' => '2025-01-01', 'end' => '2025-03-31'],
        ];
        foreach ($internships as $i) {
            Internship::updateOrCreate(
                ['title' => $i['title']],
                [
                    'description' => 'Informasi PKL/BKK untuk siswa dan alumni.',
                    'company' => $i['company'],
                    'start_date' => $i['start'],
                    'end_date' => $i['end'],
                    'is_active' => true,
                ]
            );
        }

        // ==========================================================
        // 8. FEATURED PRODUCTS (Produk Unggulan)
        // ==========================================================
        $products = [
            ['name' => 'Aplikasi Absensi Sekolah', 'description' => 'Aplikasi absensi berbasis QR Code buatan siswa RPL.'],
            ['name' => 'Desain Logo UMKM', 'description' => 'Jasa desain logo untuk UMKM lokal karya siswa Multimedia.'],
            ['name' => 'Website Company Profile', 'description' => 'Jasa pembuatan website company profile.'],
            ['name' => 'Aplikasi Kasir Sederhana', 'description' => 'Aplikasi kasir untuk toko kecil karya siswa RPL.'],
        ];
        foreach ($products as $i => $p) {
            FeaturedProduct::updateOrCreate(
                ['name' => $p['name']],
                array_merge($p, [
                    'image' => 'https://picsum.photos/seed/produk-' . ($i + 1) . '/600/400',
                    'is_active' => true,
                ])
            );
        }

        // ==========================================================
        // 9. ADMISSIONS (SPMB)
        // ==========================================================
        Admission::updateOrCreate(
            ['title' => 'PPDB / SPMB SMK Negeri 2 Karanganyar 2025/2026'],
            [
                'description' => 'Informasi Penerimaan Peserta Didik Baru (PPDB) / Sistem Penerimaan Murid Baru (SPMB) SMK Negeri 2 Karanganyar tahun ajaran 2025/2026.',
                'requirements' => "1. Lulusan SMP/MTs sederajat\n2. Memiliki ijazah/SKL\n3. Memiliki akta kelahiran\n4. Memiliki kartu keluarga\n5. Foto 3x4 latar merah",
                'schedule' => "Pendaftaran: 1 Juni - 30 Juni 2025\nVerifikasi Berkas: 1 Juli - 5 Juli 2025\nPengumuman: 10 Juli 2025\nDaftar Ulang: 11 Juli - 15 Juli 2025",
                'additional_information' => 'Proses pendaftaran dilakukan melalui website resmi PPDB Jawa Tengah. Pastikan semua berkas sudah disiapkan sebelum mendaftar.',
                'official_link' => 'https://ppdb.jatengprov.go.id',
                'is_active' => true,
            ]
        );

        // ==========================================================
        // 10. NEWS (Berita) — pakai URL placeholder
        // ==========================================================
        $newsData = [
            ['title' => 'SMK Negeri 2 Karanganyar Raih Juara 1 LKS Tingkat Provinsi', 'category' => 'Prestasi'],
            ['title' => 'Kegiatan MPLS Tahun Ajaran 2025/2026 Berjalan Lancar', 'category' => 'Kegiatan'],
            ['title' => 'Kunjungan Industri ke PT. Telkom Indonesia', 'category' => 'Kunjungan'],
            ['title' => 'Workshop Literasi Digital untuk Siswa', 'category' => 'Workshop'],
            ['title' => 'Peringatan Hari Kemerdekaan RI ke-80', 'category' => 'Kegiatan'],
            ['title' => 'Sosialisasi PKL untuk Siswa Kelas XI', 'category' => 'PKL'],
            ['title' => 'Pelatihan Guru dalam Implementasi Kurikulum Merdeka', 'category' => 'Pelatihan'],
            ['title' => 'Penerimaan Rapor Semester Ganjil 2024/2025', 'category' => 'Akademik'],
            ['title' => 'Lomba Kebersihan Antar Kelas', 'category' => 'Lomba'],
            ['title' => 'Studi Tiru ke SMK Negeri 1 Surabaya', 'category' => 'Kunjungan'],
        ];
        foreach ($newsData as $i => $n) {
            $slug = Str::slug($n['title']) . '-' . Str::random(5);
            News::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $n['title'],
                    'content' => "Berita lengkap tentang {$n['title']}. " . str_repeat("Ini adalah konten berita contoh yang bisa Anda ganti dengan konten sesungguhnya. ", 5),
                    'image' => 'https://picsum.photos/seed/berita-' . ($i + 1) . '/800/500',
                    'author_id' => $admin->id,
                    'published_at' => now()->subDays($i),
                    'status' => 'published',
                ]
            );
        }

        // ==========================================================
        // 11. ROOMS (Ruangan) — pakai URL placeholder
        // ==========================================================
        $rooms = [
            ['name' => 'Lab RPL 1', 'code' => 'LAB-RPL-01', 'floor' => '1', 'capacity' => 36, 'condition' => 'baik'],
            ['name' => 'Lab RPL 2', 'code' => 'LAB-RPL-02', 'floor' => '1', 'capacity' => 36, 'condition' => 'baik'],
            ['name' => 'Lab Multimedia', 'code' => 'LAB-MM-01', 'floor' => '1', 'capacity' => 36, 'condition' => 'rusak_ringan'],
            ['name' => 'Lab TKJ', 'code' => 'LAB-TKJ-01', 'floor' => '1', 'capacity' => 36, 'condition' => 'baik'],
            ['name' => 'Ruang Kelas X RPL', 'code' => 'X-RPL', 'floor' => '2', 'capacity' => 32, 'condition' => 'baik'],
            ['name' => 'Ruang Kelas XI RPL', 'code' => 'XI-RPL', 'floor' => '2', 'capacity' => 32, 'condition' => 'baik'],
            ['name' => 'Ruang Kelas XII RPL', 'code' => 'XII-RPL', 'floor' => '2', 'capacity' => 32, 'condition' => 'rusak_ringan'],
            ['name' => 'Perpustakaan', 'code' => 'PERPUS', 'floor' => '1', 'capacity' => 50, 'condition' => 'baik'],
            ['name' => 'Aula Serbaguna', 'code' => 'AULA', 'floor' => '1', 'capacity' => 200, 'condition' => 'baik'],
            ['name' => 'Ruang Guru', 'code' => 'RG', 'floor' => '1', 'capacity' => 40, 'condition' => 'baik'],
            ['name' => 'Ruang Kepala Sekolah', 'code' => 'R-KEPSEK', 'floor' => '1', 'capacity' => 10, 'condition' => 'baik'],
            ['name' => 'Ruang Tata Usaha', 'code' => 'TU', 'floor' => '1', 'capacity' => 15, 'condition' => 'baik'],
        ];
        foreach ($rooms as $i => $r) {
            Room::updateOrCreate(
                ['code' => $r['code']],
                array_merge($r, [
                    'description' => "Ruangan {$r['name']} yang digunakan untuk kegiatan belajar mengajar.",
                    'health_score' => 100,
                    'photo' => 'https://picsum.photos/seed/room-' . ($i + 1) . '/800/500',
                    'panorama_url' => null,
                    'is_active' => true,
                ])
            );
        }

        // ==========================================================
        // 12. CATEGORIES (Kategori Barang)
        // ==========================================================
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Perangkat elektronik dan komputer'],
            ['name' => 'Furnitur', 'description' => 'Meja, kursi, lemari, dan perabot'],
            ['name' => 'ATK', 'description' => 'Alat tulis kantor dan perlengkapan'],
            ['name' => 'Peralatan Lab', 'description' => 'Peralatan praktik dan laboratorium'],
            ['name' => 'Kebersihan', 'description' => 'Alat kebersihan dan sanitasi'],
        ];
        foreach ($categories as $c) {
            Category::updateOrCreate(['name' => $c['name']], $c);
        }

        // ==========================================================
        // 13. ITEMS (Barang Master) + Distribusi ke Ruangan
        // ==========================================================
        $itemsData = [
            [
                'name' => 'Papan Tulis Putih',
                'code' => 'PT-120',
                'category' => 'ATK',
                'brand' => 'Sakura',
                'desc' => 'Papan tulis putih ukuran 120x240 cm',
                'rooms' => [
                    ['code' => 'LAB-RPL-01', 'qty' => 2, 'condition' => 'baik'],
                    ['code' => 'LAB-RPL-02', 'qty' => 2, 'condition' => 'baik'],
                    ['code' => 'LAB-MM-01', 'qty' => 1, 'condition' => 'baik'],
                    ['code' => 'X-RPL', 'qty' => 1, 'condition' => 'baik'],
                    ['code' => 'XI-RPL', 'qty' => 1, 'condition' => 'baik'],
                    ['code' => 'XII-RPL', 'qty' => 1, 'condition' => 'rusak_ringan'],
                ],
            ],
            [
                'name' => 'Kursi Lipat',
                'code' => 'KR-LIP',
                'category' => 'Furnitur',
                'brand' => 'Chitose',
                'desc' => 'Kursi lipat untuk siswa',
                'rooms' => [
                    ['code' => 'LAB-RPL-01', 'qty' => 36, 'condition' => 'baik'],
                    ['code' => 'LAB-RPL-02', 'qty' => 36, 'condition' => 'baik'],
                    ['code' => 'LAB-MM-01', 'qty' => 36, 'condition' => 'baik'],
                    ['code' => 'LAB-TKJ-01', 'qty' => 36, 'condition' => 'baik'],
                    ['code' => 'XII-RPL', 'qty' => 32, 'condition' => 'rusak_ringan'],
                ],
            ],
            [
                'name' => 'AC 1PK',
                'code' => 'AC-1PK',
                'category' => 'Elektronik',
                'brand' => 'Daikin',
                'desc' => 'AC split 1PK',
                'rooms' => [
                    ['code' => 'LAB-RPL-01', 'qty' => 2, 'condition' => 'baik'],
                    ['code' => 'LAB-RPL-02', 'qty' => 2, 'condition' => 'baik'],
                    ['code' => 'LAB-MM-01', 'qty' => 2, 'condition' => 'rusak_ringan'],
                    ['code' => 'LAB-TKJ-01', 'qty' => 2, 'condition' => 'baik'],
                ],
            ],
            [
                'name' => 'Proyektor',
                'code' => 'PRY-001',
                'category' => 'Elektronik',
                'brand' => 'Epson',
                'desc' => 'Proyektor untuk presentasi',
                'rooms' => [
                    ['code' => 'LAB-RPL-01', 'qty' => 1, 'condition' => 'baik'],
                    ['code' => 'LAB-RPL-02', 'qty' => 1, 'condition' => 'baik'],
                    ['code' => 'LAB-MM-01', 'qty' => 1, 'condition' => 'rusak_berat'],
                    ['code' => 'LAB-TKJ-01', 'qty' => 1, 'condition' => 'baik'],
                    ['code' => 'AULA', 'qty' => 2, 'condition' => 'baik'],
                ],
            ],
            [
                'name' => 'Komputer Desktop',
                'code' => 'PC-DSK',
                'category' => 'Elektronik',
                'brand' => 'Lenovo',
                'desc' => 'PC desktop untuk praktik',
                'rooms' => [
                    ['code' => 'LAB-RPL-01', 'qty' => 36, 'condition' => 'baik'],
                    ['code' => 'LAB-RPL-02', 'qty' => 36, 'condition' => 'baik'],
                    ['code' => 'LAB-TKJ-01', 'qty' => 36, 'condition' => 'baik'],
                ],
            ],
            [
                'name' => 'Meja Guru',
                'code' => 'MJ-GURU',
                'category' => 'Furnitur',
                'brand' => 'Olympic',
                'desc' => 'Meja guru di depan kelas',
                'rooms' => [
                    ['code' => 'LAB-RPL-01', 'qty' => 1, 'condition' => 'baik'],
                    ['code' => 'LAB-RPL-02', 'qty' => 1, 'condition' => 'baik'],
                    ['code' => 'LAB-MM-01', 'qty' => 1, 'condition' => 'baik'],
                    ['code' => 'X-RPL', 'qty' => 1, 'condition' => 'baik'],
                    ['code' => 'XI-RPL', 'qty' => 1, 'condition' => 'baik'],
                    ['code' => 'XII-RPL', 'qty' => 1, 'condition' => 'baik'],
                ],
            ],
            [
                'name' => 'Lemari Arsip',
                'code' => 'LMR-ARS',
                'category' => 'Furnitur',
                'brand' => 'Brother',
                'desc' => 'Lemari arsip dokumen',
                'rooms' => [
                    ['code' => 'RG', 'qty' => 4, 'condition' => 'baik'],
                    ['code' => 'TU', 'qty' => 6, 'condition' => 'baik'],
                    ['code' => 'R-KEPSEK', 'qty' => 2, 'condition' => 'baik'],
                ],
            ],
            [
                'name' => 'Printer Laser',
                'code' => 'PRN-LSR',
                'category' => 'Elektronik',
                'brand' => 'HP',
                'desc' => 'Printer laser hitam putih',
                'rooms' => [
                    ['code' => 'TU', 'qty' => 2, 'condition' => 'baik'],
                    ['code' => 'RG', 'qty' => 1, 'condition' => 'baik'],
                ],
            ],
            [
                'name' => 'Tempat Sampah',
                'code' => 'TS-01',
                'category' => 'Kebersihan',
                'brand' => null,
                'desc' => 'Tempat sampah plastik',
                'rooms' => [
                    ['code' => 'LAB-RPL-01', 'qty' => 2, 'condition' => 'baik'],
                    ['code' => 'LAB-RPL-02', 'qty' => 2, 'condition' => 'baik'],
                    ['code' => 'LAB-MM-01', 'qty' => 2, 'condition' => 'baik'],
                    ['code' => 'X-RPL', 'qty' => 2, 'condition' => 'baik'],
                    ['code' => 'XI-RPL', 'qty' => 2, 'condition' => 'baik'],
                    ['code' => 'XII-RPL', 'qty' => 2, 'condition' => 'baik'],
                ],
            ],
            [
                'name' => 'Jam Dinding',
                'code' => 'JAM-D',
                'category' => 'ATK',
                'brand' => 'Seiko',
                'desc' => 'Jam dinding',
                'rooms' => [
                    ['code' => 'LAB-RPL-01', 'qty' => 1, 'condition' => 'baik'],
                    ['code' => 'LAB-RPL-02', 'qty' => 1, 'condition' => 'baik'],
                    ['code' => 'LAB-MM-01', 'qty' => 1, 'condition' => 'baik'],
                    ['code' => 'LAB-TKJ-01', 'qty' => 1, 'condition' => 'baik'],
                ],
            ],
        ];

        foreach ($itemsData as $i => $it) {
            $cat = Category::where('name', $it['category'])->first();

            $item = Item::updateOrCreate(
                ['code' => $it['code']],
                [
                    'category_id' => $cat->id,
                    'name' => $it['name'],
                    'brand' => $it['brand'],
                    'description' => $it['desc'],
                    'photo' => 'https://picsum.photos/seed/item-' . ($i + 1) . '/400/400',
                    'is_active' => true,
                ]
            );

            foreach ($it['rooms'] as $r) {
                $room = Room::where('code', $r['code'])->first();
                if (!$room)
                    continue;

                ItemRoom::updateOrCreate(
                    ['item_id' => $item->id, 'room_id' => $room->id],
                    [
                        'quantity' => $r['qty'],
                        'condition' => $r['condition'],
                    ]
                );
            }
        }

        // ==========================================================
        // 14. ASSIGN ROOMS TO USERS
        // ==========================================================
        $guru = User::where('email', 'guru@skandapras.test')->first();
        $guruMulti = User::where('email', 'gurumulti@skandapras.test')->first();

        if ($guru) {
            $labRpl = Room::where('code', 'LAB-RPL-01')->first();
            RoomUser::updateOrCreate(
                ['room_id' => $labRpl->id, 'user_id' => $guru->id],
                ['assigned_at' => now()]
            );
        }

        if ($guruMulti) {
            foreach (['LAB-RPL-01', 'LAB-MM-01', 'XII-RPL', 'PERPUS'] as $code) {
                $room = Room::where('code', $code)->first();
                if (!$room)
                    continue;

                RoomUser::updateOrCreate(
                    ['room_id' => $room->id, 'user_id' => $guruMulti->id],
                    ['assigned_at' => now()]
                );
            }
        }

        // ==========================================================
        // 15. RECALCULATE HEALTH SCORE SEMUA RUANGAN
        // ==========================================================
        Room::all()->each(function ($room) {
            $room->update(['health_score' => $room->recalculateHealthScore()]);
        });

        $this->command->info('✅ FullDemoSeeder selesai: sekolah, jurusan, guru, ekskul, prestasi, tata tertib, PKL, produk, SPMB, berita, ruangan, kategori, barang, assign user.');
    }
}