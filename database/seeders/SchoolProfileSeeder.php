<?php

namespace Database\Seeders;

use App\Models\SchoolProfile;
use Illuminate\Database\Seeder;

class SchoolProfileSeeder extends Seeder
{
    public function run(): void
    {
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
            ]
        );
    }
}
