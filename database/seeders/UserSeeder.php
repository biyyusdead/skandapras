<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@skandapras.test',
                'password' => 'password',
                'role' => 'admin',
                'phone' => '081200000001',
                'is_active' => true,
            ],
            [
                'name' => 'Waka Sarpras',
                'email' => 'waka@skandapras.test',
                'password' => 'password',
                'role' => 'waka_sarpras',
                'phone' => '081200000002',
                'is_active' => true,
            ],
            [
                'name' => 'Kepala Sekolah',
                'email' => 'kepsek@skandapras.test',
                'password' => 'password',
                'role' => 'kepala_sekolah',
                'phone' => '081200000003',
                'is_active' => true,
            ],
            [
                'name' => 'Guru Pengguna Ruangan',
                'email' => 'guru@skandapras.test',
                'password' => 'password',
                'role' => 'pengguna_ruangan',
                'phone' => '081200000004',
                'is_active' => true,
            ],
            [
                'name' => 'Guru Multi Ruangan',
                'email' => 'gurumulti@skandapras.test',
                'password' => 'password',
                'role' => 'pengguna_ruangan',
                'phone' => '081200000005',
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
