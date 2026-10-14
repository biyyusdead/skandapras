<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SchoolProfileSeeder::class,
            AdmissionSeeder::class,
            SarprasSeeder::class,
            FullDemoSeeder::class,
            TeacherSeeder::class,
            CategorySeeder::class,
            ItemSeeder::class,
            // RoomSeeder::class,     // ← kalau ada
            ItemRoomSeeder::class
        ]);
    }
}