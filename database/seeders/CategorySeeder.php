<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Furniture',        'code' => 'FRN'],
            ['name' => 'Elektronik',       'code' => 'ELK'],
            ['name' => 'Alat Praktik',     'code' => 'PRK'],
            ['name' => 'Alat Tulis',       'code' => 'ATK'],
            ['name' => 'Kebersihan',       'code' => 'KBR'],
            ['name' => 'Olahraga',         'code' => 'OLR'],
            ['name' => 'Keselamatan',      'code' => 'KSL'],
        ];

        foreach ($categories as $c) {
            Category::updateOrCreate(['code' => $c['code']], $c);
        }
    }
}
