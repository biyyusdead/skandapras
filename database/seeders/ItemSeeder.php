<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $cat = Category::pluck('id', 'code'); // ['FRN' => 1, 'ELK' => 2, ...]

        // Format: [category_code, name, code, brand, description]
        $items = [
            // Furniture
            ['FRN', 'Kursi Siswa',        'KRS-SIS',  null,           'Kursi kayu/besi untuk siswa'],
            ['FRN', 'Meja Siswa',         'MJS-SIS',  null,           'Meja siswa standar'],
            ['FRN', 'Kursi Guru',         'KRS-GRU',  null,           'Kursi untuk guru'],
            ['FRN', 'Meja Guru',          'MJS-GRU',  null,           'Meja untuk guru'],
            ['FRN', 'Papan Tulis',        'PPN-TLS',  null,           'Papan tulis whiteboard'],
            ['FRN', 'Lemari Kelas',       'LMR-KLS',  null,           'Lemari penyimpanan kelas'],
            ['FRN', 'Meja Lab',           'MJS-LAB',  null,           'Meja praktikum'],
            ['FRN', 'Kursi Lab',          'KRS-LAB',  null,           'Kursi praktikum'],

            // Elektronik
            ['ELK', 'AC',                 'AC-SPL',   'Daikin',       'Air Conditioner split'],
            ['ELK', 'Proyektor',          'PRY-LCD',  'Epson',        'Proyektor LCD'],
            ['ELK', 'Layar Proyektor',    'LYR-PRY',  null,           'Screen proyektor'],
            ['ELK', 'Komputer',           'KMP-DSK',  'Lenovo',       'PC desktop untuk lab'],
            ['ELK', 'Laptop',             'LPT-NBK',  'Asus',         'Laptop untuk guru'],
            ['ELK', 'Printer',            'PRN-LSR',  'HP',           'Printer laser'],
            ['ELK', 'Speaker Aktif',      'SPK-AKT',  null,           'Speaker untuk kelas'],
            ['ELK', 'Kipas Angin',        'KPS-DDG',  null,           'Kipas angin dinding'],
            ['ELK', 'Lampu TL',           'LMP-TL5',  null,           'Lampu TL 5 watt'],
            ['ELK', 'Televisi',           'TV-LED',   'Samsung',      'TV LED untuk media'],

            // Alat Praktik
            ['PRK', 'Mesin Bubut',        'MSN-BBT',  null,           'Mesin bubut untuk praktik'],
            ['PRK', 'Mesin Las',          'MSN-LAS',  null,           'Mesin las listrik'],
            ['PRK', 'Bor Duduk',          'BOR-DDK',  null,           'Bor duduk'],
            ['PRK', 'Gerinda',            'GRD-TNG',  null,           'Gerinda tangan'],
            ['PRK', 'Multimeter',         'MLT-DGT',  'Sanwa',        'Multimeter digital'],
            ['PRK', 'Solder Listrik',     'SLD-LST',  null,           'Solder listrik'],
            ['PRK', 'Tangga Aluminium',   'TGG-ALM',  null,           'Tangga lipat aluminium'],

            // ATK
            ['ATK', 'Spidol Whiteboard',  'SPD-WB',   'Snowman',      'Spidol papan tulis'],
            ['ATK', 'Penghapus Whiteboard','PHPS-WB', null,           'Penghapus papan'],
            ['ATK', 'Pulpen',             'PLP-STD',  'Standard',     'Pulpen standar'],
            ['ATK', 'Kertas HVS A4',      'KRS-A4',   'Sidu',         'Kertas HVS A4'],

            // Kebersihan
            ['KBR', 'Sapu Lantai',        'SPU-LNT',  null,           'Sapu lantai'],
            ['KBR', 'Pel Lantai',         'PEL-LNT',  null,           'Alat pel'],
            ['KBR', 'Tempat Sampah',      'TPS-SMP',  null,           'Tempat sampah'],
            ['KBR', 'Kemoceng',           'KMC-NG',   null,           'Kemoceng'],

            // Olahraga
            ['OLR', 'Bola Basket',        'BOL-BSK',  null,           'Bola basket'],
            ['OLR', 'Bola Voli',          'BOL-VLY',  null,           'Bola voli'],
            ['OLR', 'Bola Sepak',         'BOL-SPK',  null,           'Bola sepak'],
            ['OLR', 'Net Voli',           'NET-VLY',  null,           'Net bola voli'],

            // Keselamatan
            ['KSL', 'APAR',               'APR-KBR',  null,           'Alat pemadam api ringan'],
            ['KSL', 'Kotak P3K',          'P3K-KTK',  null,           'Kotak obat P3K'],
            ['KSL', 'Tandu',              'TND-DRT',  null,           'Tandu darurat'],
        ];

        foreach ($items as [$catCode, $name, $code, $brand, $desc]) {
            Item::updateOrCreate(
                ['code' => $code],
                [
                    'category_id' => $cat[$catCode] ?? null,
                    'name'        => $name,
                    'brand'       => $brand,
                    'description' => $desc,
                    'is_active'   => true,
                ]
            );
        }
    }
}