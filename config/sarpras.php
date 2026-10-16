<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Kontak Petugas & Layanan Sarpras
    |--------------------------------------------------------------------------
    |
    | Konfigurasi informasi kontak resmi petugas sarana & prasarana.
    | Jika nomor petugas belum tersedia di environment (.env), sistem
    | akan menampilkan placeholder informasi tanpa mengarang nomor acak.
    |
    */
    'officer_contact' => [
        'phone' => env('SARPRAS_OFFICER_PHONE'),
        'whatsapp' => env('SARPRAS_OFFICER_WHATSAPP'),
        'email' => env('SARPRAS_OFFICER_EMAIL'),
        'tu_location' => env('SARPRAS_TU_LOCATION', 'Ruang Tata Usaha (Gedung Utama SMK Negeri 2 Karanganyar)'),
        'service_hours' => env('SARPRAS_SERVICE_HOURS', 'Senin - Jumat, pukul 07:00 - 15:30 WIB'),
        'placeholder_message' => 'Kontak petugas akan tersedia setelah dikonfirmasi pihak sekolah. Untuk saat ini, silakan sampaikan laporan langsung ke ruang Tata Usaha atau nomor telepon resmi sekolah.',
    ],
];
