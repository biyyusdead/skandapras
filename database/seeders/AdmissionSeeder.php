<?php

namespace Database\Seeders;

use App\Models\Admission;
use Illuminate\Database\Seeder;

class AdmissionSeeder extends Seeder
{
    public function run(): void
    {
        Admission::updateOrCreate(
            ['title' => 'PPDB / SPMB SMK Negeri 2 Karanganyar'],
            [
                'description' => 'Informasi Penerimaan Peserta Didik Baru (PPDB) / Sistem Penerimaan Murid Baru (SPMB) SMK Negeri 2 Karanganyar.',
                'requirements' => "1. Lulusan SMP/MTs sederajat\n2. Memiliki ijazah/SKL\n3. Memiliki akta kelahiran\n4. Memiliki kartu keluarga\n5. Foto 3x4",
                'schedule' => "Pendaftaran: sesuai jadwal PPDB Jawa Tengah\nVerifikasi: sesuai jadwal\nPengumuman: sesuai jadwal",
                'additional_information' => 'Proses pendaftaran dilakukan melalui website resmi PPDB Jawa Tengah.',
                'official_link' => 'https://ppdb.jatengprov.go.id',
                'is_active' => true,
            ]
        );
    }
}
