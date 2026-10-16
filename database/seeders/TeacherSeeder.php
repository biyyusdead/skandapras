<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================================
        // TEACHERS (Guru) — ~20 data, mapel sesuai kurikulum sekolah
        // ==========================================================
        $teachers = [
            // Bahasa Indonesia
            ['name' => 'Siti Aminah, S.Pd.',          'nip' => '198002022006022002', 'position' => 'Guru Bahasa Indonesia', 'email' => 'siti.aminah@smkn2kra.sch.id'],
            ['name' => 'Dwi Rahayu, S.Pd.',           'nip' => '198603032010032003', 'position' => 'Guru Bahasa Indonesia', 'email' => 'dwi.rahayu@smkn2kra.sch.id'],

            // Bahasa Inggris
            ['name' => 'Maya Anggraini, S.Pd.',       'nip' => '199206062017062006', 'position' => 'Guru Bahasa Inggris',   'email' => 'maya.anggraini@smkn2kra.sch.id'],
            ['name' => 'Rina Kusumawati, S.Pd.',      'nip' => '198907072014072007', 'position' => 'Guru Bahasa Inggris',   'email' => 'rina.kusuma@smkn2kra.sch.id'],

            // Matematika
            ['name' => 'Bambang Setiawan, S.Pd.',     'nip' => '197804042006041004', 'position' => 'Guru Matematika',       'email' => 'bambang.s@smkn2kra.sch.id'],
            ['name' => 'Nur Hidayah, S.Pd.',          'nip' => '199105052016052005', 'position' => 'Guru Matematika',       'email' => 'nur.hidayah@smkn2kra.sch.id'],

            // Sejarah
            ['name' => 'Agus Priyanto, S.Pd.',        'nip' => '198301012009011001', 'position' => 'Guru Sejarah',          'email' => 'agus.priyanto@smkn2kra.sch.id'],

            // RPL (Rekayasa Perangkat Lunak)
            ['name' => 'Budi Santoso, S.Kom.',        'nip' => '197501012005011001', 'position' => 'Guru RPL',              'email' => 'budi@smkn2kra.sch.id'],
            ['name' => 'Andi Kurniawan, S.Kom.',      'nip' => '199003032015031003', 'position' => 'Guru RPL',              'email' => 'andi.kurniawan@smkn2kra.sch.id'],

            // Ototronik
            ['name' => 'Joko Susilo, S.T.',           'nip' => '198504042011041004', 'position' => 'Guru Ototronik',        'email' => 'joko.susilo@smkn2kra.sch.id'],
            ['name' => 'Hendra Wijaya, S.T.',         'nip' => '199307072018071007', 'position' => 'Guru Ototronik',        'email' => 'hendra.wijaya@smkn2kra.sch.id'],

            // Mesin
            ['name' => 'Slamet Riyadi, S.T.',         'nip' => '198205052008051005', 'position' => 'Guru Mesin',            'email' => 'slamet.riyadi@smkn2kra.sch.id'],
            ['name' => 'Teguh Prasetyo, S.T.',        'nip' => '199109092016091009', 'position' => 'Guru Mesin',            'email' => 'teguh.prasetyo@smkn2kra.sch.id'],

            // TPK (Teknik Pengelasan Kapal / Teknik Pemesinan Kapal — sesuaikan)
            ['name' => 'Wahyu Hidayat, S.T.',         'nip' => '198706062012061006', 'position' => 'Guru TPK',              'email' => 'wahyu.hidayat@smkn2kra.sch.id'],

            // Agama Islam
            ['name' => 'Lina Marlina, S.Ag.',         'nip' => '199408082019082008', 'position' => 'Guru Agama Islam',      'email' => 'lina.marlina@smkn2kra.sch.id'],
            ['name' => 'Muhammad Rizal, S.Pd.I.',     'nip' => '198808082013081008', 'position' => 'Guru Agama Islam',      'email' => 'm.rizal@smkn2kra.sch.id'],

            // Agama Kristen
            ['name' => 'Daniel Simanjuntak, S.Th.',   'nip' => '198609092011091009', 'position' => 'Guru Agama Kristen',    'email' => 'daniel.s@smkn2kra.sch.id'],

            // Agama Hindu
            ['name' => 'I Made Suryana, S.Ag.',       'nip' => '198710102012101010', 'position' => 'Guru Agama Hindu',      'email' => 'made.suryana@smkn2kra.sch.id'],

            // BK (Bimbingan Konseling)
            ['name' => 'Retno Wulandari, S.Pd.',      'nip' => '198403032010032003', 'position' => 'Guru BK',               'email' => 'retno.wulandari@smkn2kra.sch.id'],
            ['name' => 'Sri Handayani, S.Pd.',        'nip' => '199202022017022002', 'position' => 'Guru BK',               'email' => 'sri.handayani@smkn2kra.sch.id'],

            // Bahasa Jawa
            ['name' => 'Suparno, S.Pd.',              'nip' => '197905052007051005', 'position' => 'Guru Bahasa Jawa',      'email' => 'suparno@smkn2kra.sch.id'],

            // Seni Budaya
            ['name' => 'Ahmad Fauzi, S.Ds.',          'nip' => '198503032010031003', 'position' => 'Guru Seni Budaya',      'email' => 'ahmad.fauzi@smkn2kra.sch.id'],

            // PJOK
            ['name' => 'Rudi Hartono, S.Or.',         'nip' => '199005052015051005', 'position' => 'Guru PJOK',             'email' => 'rudi.hartono@smkn2kra.sch.id'],

            // KIK (Kreativitas, Inovasi, dan Kewirausahaan)
            ['name' => 'Dewi Lestari, S.E.',          'nip' => '198804042012042004', 'position' => 'Guru KIK',              'email' => 'dewi.lestari@smkn2kra.sch.id'],

            // PIPAS (Pendidikan Pancasila dan Kewarganegaraan — sesuaikan)
            ['name' => 'Tri Wahyuni, S.Pd.',          'nip' => '198905052014052005', 'position' => 'Guru PIPAS',            'email' => 'tri.wahyuni@smkn2kra.sch.id'],
        ];

        foreach ($teachers as $i => $t) {
            Teacher::updateOrCreate(
                ['nip' => $t['nip']],
                array_merge($t, [
                    'phone' => '0812'.str_pad($i + 1, 8, '0', STR_PAD_LEFT),
                    'photo' => null, // placeholder di view
                    'is_active' => true,
                ])
            );
        }
    }
}
