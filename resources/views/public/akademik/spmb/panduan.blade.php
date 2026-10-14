@extends('layouts.public')

@section('title', 'Panduan Pendaftaran SPMB ')

@section('content')

<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

    {{-- ================================================= --}}
    {{-- HEADER --}}
    {{-- ================================================= --}}
    <div class="flex items-start justify-between gap-4 mb-10">
        <div class="min-w-0">
            {{-- Breadcrumb --}}
            <nav class="flex flex-wrap items-center gap-2 text-xs sm:text-sm text-surface-500 mb-3">
                <a href="{{ url('/') }}" class="hover:text-primary-600 transition-colors">Beranda</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-60"></i>
                <a href="{{ route('public.akademik.spmb.index') }}" class="hover:text-primary-600 transition-colors">SPMB</a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-60"></i>
                <span class="text-primary-600 font-semibold">Panduan Pendaftaran</span>
            </nav>

            <h1 class="text-3xl md:text-4xl font-extrabold text-surface-900 leading-tight tracking-tight">
                Panduan Pendaftaran
            </h1>
            <h2 class="text-lg md:text-xl font-bold text-primary-600 mt-1">
                SPMB SMKN 2 Karanganyar 2026/2027
            </h2>
            <p class="text-sm md:text-base text-surface-500 mt-3 max-w-2xl leading-relaxed">
                Ikuti langkah-langkah di bawah ini untuk mendaftar sebagai calon peserta didik baru
                SMK Negeri 2 Karanganyar.
            </p>
        </div>

        {{-- Icon Kanan --}}
        <div class="hidden md:flex w-14 h-14 rounded-xl bg-primary-600 text-white items-center justify-center
                    shrink-0 shadow-md">
            <i data-lucide="book-open" class="w-7 h-7"></i>
        </div>
    </div>

    {{-- ================================================= --}}
    {{-- LANGKAH-LANGKAH PENDAFTARAN --}}
    {{-- ================================================= --}}
    <div class="mb-14">
        <h3 class="text-base md:text-lg font-extrabold text-primary-600 uppercase tracking-wide mb-6">
            Langkah-Langkah Pendaftaran
        </h3>

        @php
            $langkah = [
                ['judul' => 'Buat Akun Pendaftaran', 'desk' => 'Kunjungi website resmi SPMB SMK Negeri 2 Karanganyar, lalu buat akun dengan mengisi email aktif dan password.', 'icon' => 'user-plus'],
                ['judul' => 'Lengkapi Data Diri', 'desk' => 'Isi formulir data diri dengan lengkap: nama, NISN, tempat & tanggal lahir, alamat, dan data orang tua/wali.', 'icon' => 'file-text'],
                ['judul' => 'Unggah Dokumen', 'desk' => 'Unggah dokumen yang diperlukan: scan KK, akta kelahiran, rapor semester 1-5, SKHUN, dan foto berwarna 3×4.', 'icon' => 'upload'],
                ['judul' => 'Pilih Kompetensi Keahlian', 'desk' => 'Pilih jurusan yang diinginkan: Teknik Pemesinan, Teknik Pembuatan Kain, Teknik Ototronik, atau Rekayasa Perangkat Lunak.', 'icon' => 'graduation-cap'],
                ['judul' => 'Cetak Kartu Pendaftaran', 'desk' => 'Setelah data lengkap, cetak kartu pendaftaran sebagai bukti sah mengikuti seleksi. Kartu wajib dibawa saat verifikasi.', 'icon' => 'printer'],
                ['judul' => 'Ikuti Seleksi', 'desk' => 'Pantau jadwal seleksi sesuai gelombang yang dipilih. Hasil seleksi bisa dilihat di halaman Cek Hasil Seleksi SPMB.', 'icon' => 'clock'],
                ['judul' => 'Daftar Ulang', 'desk' => 'Jika dinyatakan lulus, lakukan daftar ulang sesuai jadwal dengan membawa dokumen asli untuk verifikasi.', 'icon' => 'check-circle-2'],
            ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($langkah as $i => $l)
                <div class="group bg-white rounded-2xl border border-surface-200/70 shadow-sm p-5
                            hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300
                            flex gap-4 items-start">

                    {{-- Nomor + Ikon --}}
                    <div class="shrink-0 flex flex-col items-center gap-2">
                        <div class="w-11 h-11 rounded-full bg-primary-600 text-white flex items-center justify-center
                                    shadow-md transition-transform duration-300 group-hover:scale-110">
                            <i data-lucide="{{ $l['icon'] }}" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[10px] font-bold text-primary-600 uppercase tracking-wider">
                            Step {{ $i + 1 }}
                        </span>
                    </div>

                    {{-- Teks --}}
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm md:text-base font-bold text-surface-900 leading-tight">
                            {{ $l['judul'] }}
                        </h4>
                        <p class="text-xs md:text-sm text-surface-600 leading-relaxed mt-2">
                            {{ $l['desk'] }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ================================================= --}}
    {{-- SYARAT & KETENTUAN --}}
    {{-- ================================================= --}}
    <div class="mb-14">
        <h3 class="text-base md:text-lg font-extrabold text-primary-600 uppercase tracking-wide mb-6">
            Syarat &amp; Ketentuan
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Syarat Umum --}}
            <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <h4 class="text-base md:text-lg font-bold text-surface-900">Syarat Umum</h4>
                </div>

                <ul class="space-y-2.5">
                    @foreach ([
                        'Lulusan SMP/MTs atau sederajat',
                        'Berusia maksimal 21 tahun pada 1 Juli 2026',
                        'Memiliki NISN yang masih aktif',
                        'Sehat jasmani dan rohani',
                        'Tidak sedang terdaftar sebagai siswa di sekolah lain',
                    ] as $s)
                        <li class="flex gap-2.5 text-xs md:text-sm text-surface-700 leading-relaxed">
                            <span class="shrink-0 mt-1.5 w-1.5 h-1.5 rounded-full bg-primary-600"></span>
                            {{ $s }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Dokumen --}}
            <div class="bg-white rounded-2xl border border-surface-200/70 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-4">
                    <h4 class="text-base md:text-lg font-bold text-surface-900">Dokumen Wajib</h4>
                </div>

                <ul class="space-y-2.5">
                    @foreach ([
                        'Kartu Keluarga (KK) - scan/foto jelas',
                        'Akta Kelahiran - scan/foto jelas',
                        'Rapor semester 1 s.d. 5',
                        'SKHUN / Surat Keterangan Lulus',
                        'Pas foto berwarna ukuran 3×4',
                        'Piagam prestasi (jika ada, nilai tambah)',
                    ] as $d)
                        <li class="flex gap-2.5 text-xs md:text-sm text-surface-700 leading-relaxed">
                            <span class="shrink-0 mt-1.5 w-1.5 h-1.5 rounded-full bg-primary-600"></span>
                            {{ $d }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- ================================================= --}}
    {{-- FAQ --}}
    {{-- ================================================= --}}
    <div class="mb-14">
        <h3 class="text-base md:text-lg font-extrabold text-primary-600 uppercase tracking-wide mb-6">
            Pertanyaan Umum (FAQ)
        </h3>

        @php
            $faq = [
                ['q' => 'Apakah pendaftaran SPMB dikenakan biaya?', 'a' => 'Tidak. Pendaftaran SPMB SMK Negeri 2 Karanganyar sepenuhnya gratis, tidak dipungut biaya apapun.'],
                ['q' => 'Apakah boleh memilih lebih dari satu jurusan?', 'a' => 'Tidak. Setiap pendaftar hanya diperbolehkan memilih satu kompetensi keahlian. Pilih dengan matang sesuai minat dan bakat.'],
                ['q' => 'Bagaimana cara mengetahui hasil seleksi?', 'a' => 'Hasil seleksi dapat dilihat melalui halaman Cek Hasil Seleksi SPMB dengan memasukkan nomor pendaftaran dan tanggal lahir.'],
                ['q' => 'Apakah ada jalur prestasi?', 'a' => 'Ya. Terdapat jalur prestasi untuk calon peserta didik dengan piagam prestasi akademik maupun non-akademik tingkat kabupaten, provinsi, atau nasional.'],
                ['q' => 'Apa yang harus dilakukan jika lupa nomor pendaftaran?', 'a' => 'Silakan hubungi panitia SPMB melalui kontak yang tersedia di halaman Informasi Lebih Lanjut dengan menyertakan nama lengkap dan NISN.'],
            ];
        @endphp

        <div class="space-y-3">
            @foreach ($faq as $f)
                <details class="group bg-white rounded-2xl border border-surface-200/70 shadow-sm overflow-hidden">
                    <summary class="flex items-center justify-between gap-4 px-5 py-4 cursor-pointer
                                    hover:bg-primary-50/30 transition-colors list-none">
                        <span class="text-sm md:text-base font-semibold text-surface-900 group-open:text-primary-600 transition-colors pr-2">
                            {{ $f['q'] }}
                        </span>
                        <i data-lucide="chevron-down"
                           class="w-5 h-5 text-primary-600 shrink-0 transition-transform duration-300
                                  group-open:rotate-180"></i>
                    </summary>
                    <div class="px-5 pb-5 text-xs md:text-sm text-surface-600 leading-relaxed border-t border-surface-100 pt-4">
                        {{ $f['a'] }}
                    </div>
                </details>
            @endforeach
        </div>
    </div>

    {{-- ================================================= --}}
    {{-- CTA --}}
    {{-- ================================================= --}}
    <div class="bg-primary-700 text-white rounded-2xl p-6 md:p-8 shadow-xl text-center">
        <div class="w-14 h-14 rounded-xl bg-white/15 ring-1 ring-white/20 flex items-center justify-center mx-auto mb-4">
            <i data-lucide="graduation-cap" class="w-7 h-7"></i>
        </div>
        <h3 class="text-xl font-bold mb-2">Siap Mendaftar?</h3>
        <p class="text-sm text-primary-100/90 mb-5 max-w-md mx-auto">
            Kunjungi website resmi SPMB Jawa Tengah untuk memulai proses pendaftaran.
        </p>
        <a href="https://spmb.jatengprov.go.id" target="_blank" rel="noopener"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white text-primary-700
                  hover:bg-primary-50 text-sm font-semibold transition-all shadow-md hover:shadow-lg
                  active:scale-[0.98]">
            <i data-lucide="external-link" class="w-4 h-4"></i>
            Kunjungi Website SPMB Jateng
        </a>
    </div>

</section>

@endsection