<?php

namespace Database\Seeders;

use App\Models\HeroMenu;
use Illuminate\Database\Seeder;

class HeroMenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            [
                'label'       => 'Kompetensi',
                'title'       => 'Kompetensi Keahlian',
                'description' => '8 jurusan unggulan siap kerja',
                'icon'        => 'graduation-cap',
                'route_name'  => 'public.akademik.kompetensi.index',
                'color_from'  => 'primary-500',
                'color_to'    => 'primary-700',
                'pattern'     => 'dots',
                'order'       => 1,
            ],
            [
                'label'       => 'Sarpras',
                'title'       => 'Sarana & Prasarana',
                'description' => 'Fasilitas modern untuk belajar',
                'icon'        => 'building-2',
                'route_name'  => 'public.rooms.index',
                'color_from'  => 'emerald-500',
                'color_to'    => 'emerald-700',
                'pattern'     => 'grid',
                'order'       => 2,
            ],
            [
                'label'       => 'Prestasi',
                'title'       => 'Prestasi Siswa',
                'description' => 'Torehan juara di berbagai ajang',
                'icon'        => 'trophy',
                'route_name'  => 'public.kesiswaan.prestasi.index',
                'color_from'  => 'amber-500',
                'color_to'    => 'orange-600',
                'pattern'     => 'stripes',
                'order'       => 3,
            ],
            [
                'label'       => 'Berita',
                'title'       => 'Kabar Terbaru',
                'description' => 'Info & artikel seputar sekolah',
                'icon'        => 'newspaper',
                'route_name'  => 'public.news.index',
                'color_from'  => 'sky-500',
                'color_to'    => 'blue-700',
                'pattern'     => 'waves',
                'order'       => 4,
            ],
            [
                'label'       => 'Alumni',
                'title'       => 'Jejak Alumni',
                'description' => 'Lulusan berprestasi kami',
                'icon'        => 'users',
                'route_name'  => 'public.akademik.alumni.index',
                'color_from'  => 'violet-500',
                'color_to'    => 'purple-700',
                'pattern'     => 'dots',
                'order'       => 5,
            ],
            [
                'label'       => 'Lapor',
                'title'       => 'Laporan Kerusakan',
                'description' => 'Bantu jaga fasilitas sekolah',
                'icon'        => 'alert-triangle',
                'route_name'  => 'public.sarpras.pengaduan',
                'color_from'  => 'rose-500',
                'color_to'    => 'red-700',
                'pattern'     => 'cross',
                'order'       => 6,
            ],
        ];

        foreach ($menus as $menu) {
            HeroMenu::updateOrCreate(
                ['route_name' => $menu['route_name']],
                $menu
            );
        }
    }
}