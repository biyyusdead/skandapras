<?php

return [

    'admin' => [
        [
            'label' => 'Dashboard',
            'route' => 'admin.dashboard',
            'icon' => 'home',
            'match' => 'admin.dashboard',
        ],
        [
            'label' => 'Data Master',
            'icon' => 'database',
            'children' => [
                ['label' => 'Data Sekolah', 'route' => 'admin.school.edit', 'match' => 'admin.school.*'],
                ['label' => 'Data Users',   'route' => 'admin.users.index', 'match' => 'admin.users.*'],
            ],
        ],
        [
            'label' => 'Akademik',
            'icon' => 'graduation-cap',
            'children' => [
                ['label' => 'Data Jurusan', 'route' => 'admin.departments.index', 'match' => 'admin.departments.*'],
                ['label' => 'Data Guru',    'route' => 'admin.teachers.index',    'match' => 'admin.teachers.*'],
                ['label' => 'Data Alumni',          'route' => 'admin.alumni.index',           'match' => 'admin.alumni.*'],
                ['label' => 'Produk Unggulan',      'route' => 'admin.products.index',         'match' => 'admin.products.*'],
                ['label' => 'SPMB',                 'route' => 'admin.admissions.index',       'match' => 'admin.admissions.*'],
            ],
        ],
        [
            'label' => 'Kesiswaan',
            'icon' => 'users',
            'children' => [
                ['label' => 'Data Prestasi',        'route' => 'admin.achievements.index',     'match' => 'admin.achievements.*'],
                ['label' => 'Data Ekstrakurikuler', 'route' => 'admin.extracurriculars.index', 'match' => 'admin.extracurriculars.*'],
                ['label' => 'Tata Tertib',          'route' => 'admin.rules.index',            'match' => 'admin.rules.*'],
                ['label' => 'PKL & BKK',            'route' => 'admin.internships.index',      'match' => 'admin.internships.*'],
            ],
        ],
        [
            'label' => 'Berita',
            'icon' => 'newspaper',
            'children' => [
                ['label' => 'Data Berita', 'route' => 'admin.news.index', 'match' => 'admin.news.*'],
            ],
        ],
        [
            'label' => 'Log Aktivitas',
            'route' => 'admin.logs.index',
            'icon' => 'scroll-text',
            'match' => 'admin.logs.*',
        ],
    ],

    'waka_sarpras' => [
        [
            'label' => 'Dashboard',
            'route' => 'waka.dashboard',
            'icon' => 'home',
            'match' => 'waka.dashboard',
        ],
        [
            'label' => 'Sarana & Prasarana',
            'icon' => 'door-open',
            'children' => [
                ['label' => 'Data Ruangan',  'route' => 'waka.rooms.index',      'match' => 'waka.rooms.*'],
                ['label' => 'Data Kategori', 'route' => 'waka.categories.index', 'match' => 'waka.categories.*'],
                ['label' => 'Data Barang',   'route' => 'waka.items.index',      'match' => 'waka.items.*'],
            ],
        ],
        [
            'label' => 'Laporan',
            'icon' => 'file-text',
            'children' => [
                ['label' => 'Laporan Kerusakan', 'route' => 'waka.damage-reports.index', 'match' => 'waka.damage-reports.*'],
                ['label' => 'Laporan Perbaikan', 'route' => 'waka.repair-reports.index', 'match' => 'waka.repair-reports.*'],
            ],
        ],
        [
            'label' => 'Persetujuan',
            'route' => 'waka.approvals.index',
            'icon' => 'check-circle',
            'match' => 'waka.approvals.*',
        ],
    ],

    'kepala_sekolah' => [
        [
            'label' => 'Dashboard',
            'route' => 'kepala.dashboard',
            'icon' => 'home',
            'match' => 'kepala.dashboard',
        ],
        [
            'label' => 'Monitoring',
            'icon' => 'eye',
            'children' => [
                ['label' => 'Skor Ruangan',      'route' => 'kepala.rooms.index',          'match' => 'kepala.rooms.*'],
                ['label' => 'Laporan Kerusakan', 'route' => 'kepala.damage-reports.index', 'match' => 'kepala.damage-reports.*'],
                ['label' => 'Laporan Perbaikan', 'route' => 'kepala.repair-reports.index', 'match' => 'kepala.repair-reports.*'],
            ],
        ],
        [
            'label' => 'Persetujuan 2',
            'route' => 'kepala.approvals.index',
            'icon' => 'check-circle',
            'match' => 'kepala.approvals.*',
        ],
    ],

    'pengguna_ruangan' => [
        [
            'label' => 'Dashboard',
            'route' => 'pengguna.dashboard',
            'icon' => 'home',
            'match' => 'pengguna.dashboard',
        ],
        [
            'label' => 'Ruangan Saya',
            'route' => 'pengguna.rooms.index',
            'icon' => 'door-open',
            'match' => 'pengguna.rooms.*',
        ],
        [
            'label' => 'Laporan Saya',
            'route' => 'pengguna.damage-reports.index',
            'icon' => 'file-text',
            'match' => 'pengguna.damage-reports.*',
        ],
    ],

];
