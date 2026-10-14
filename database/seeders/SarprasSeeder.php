<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\ItemRoom;
use App\Models\Room;
use App\Models\RoomUser;
use App\Models\User;
use Illuminate\Database\Seeder;

class SarprasSeeder extends Seeder
{
    public function run(): void
    {
        // Kategori
        $categories = [
            ['name' => 'Elektronik', 'description' => 'Perangkat elektronik dan komputer'],
            ['name' => 'Furnitur', 'description' => 'Meja, kursi, lemari'],
            ['name' => 'ATK', 'description' => 'Alat tulis kantor'],
            ['name' => 'Peralatan', 'description' => 'Peralatan praktik dan laboratorium'],
        ];
        foreach ($categories as $cat) {
            Category::updateOrCreate(['name' => $cat['name']], $cat);
        }

        // Ruangan
        $rooms = [
            ['name' => 'Lab RPL 1', 'code' => 'LAB-RPL-01', 'capacity' => 36, 'condition' => 'baik'],
            ['name' => 'Lab Multimedia', 'code' => 'LAB-MM-01', 'capacity' => 36, 'condition' => 'baik'],
            ['name' => 'Ruang Kelas XII RPL', 'code' => 'R-XII-RPL', 'capacity' => 32, 'condition' => 'rusak_ringan'],
        ];
        foreach ($rooms as $r) {
            Room::updateOrCreate(['code' => $r['code']], array_merge($r, [
                'health_score' => 100,
                'is_active' => true,
            ]));
        }

        // Barang master
        $items = [
            ['name' => 'Papan Tulis Putih', 'code' => 'PT-120', 'category' => 'ATK', 'brand' => 'Sakura'],
            ['name' => 'Kursi Lipat', 'code' => 'KR-LIP', 'category' => 'Furnitur', 'brand' => 'Chitose'],
            ['name' => 'AC 1PK', 'code' => 'AC-1PK', 'category' => 'Elektronik', 'brand' => 'Daikin'],
            ['name' => 'Proyektor', 'code' => 'PRY-001', 'category' => 'Elektronik', 'brand' => 'Epson'],
        ];
        foreach ($items as $it) {
            $cat = Category::where('name', $it['category'])->first();
            Item::updateOrCreate(
                ['code' => $it['code']],
                [
                    'name' => $it['name'],
                    'category_id' => $cat->id,
                    'brand' => $it['brand'],
                    'is_active' => true,
                ]
            );
        }

        // Distribusi item ke ruangan (many-to-many)
        $distributions = [
            // Papan Tulis di 3 ruangan
            ['item' => 'PT-120', 'room' => 'LAB-RPL-01', 'qty' => 2, 'condition' => 'baik'],
            ['item' => 'PT-120', 'room' => 'LAB-MM-01', 'qty' => 1, 'condition' => 'baik'],
            ['item' => 'PT-120', 'room' => 'R-XII-RPL', 'qty' => 1, 'condition' => 'rusak_ringan'],

            // Kursi lipat
            ['item' => 'KR-LIP', 'room' => 'LAB-RPL-01', 'qty' => 36, 'condition' => 'baik'],
            ['item' => 'KR-LIP', 'room' => 'R-XII-RPL', 'qty' => 32, 'condition' => 'baik'],

            // AC
            ['item' => 'AC-1PK', 'room' => 'LAB-RPL-01', 'qty' => 2, 'condition' => 'baik'],
            ['item' => 'AC-1PK', 'room' => 'LAB-MM-01', 'qty' => 2, 'condition' => 'rusak_ringan'],

            // Proyektor
            ['item' => 'PRY-001', 'room' => 'LAB-RPL-01', 'qty' => 1, 'condition' => 'baik'],
        ];

        foreach ($distributions as $d) {
            $item = Item::where('code', $d['item'])->first();
            $room = Room::where('code', $d['room'])->first();

            if ($item && $room) {
                ItemRoom::updateOrCreate(
                    ['item_id' => $item->id, 'room_id' => $room->id],
                    [
                        'quantity' => $d['qty'],
                        'condition' => $d['condition'],
                    ]
                );
            }
        }

        // Recalc health score semua ruangan
        Room::all()->each(function ($room) {
            $room->update(['health_score' => $room->recalculateHealthScore()]);
        });

        $guru = User::where('email', 'guru@skandapras.test')->first();
        if ($guru) {
            $labRpl = Room::where('code', 'LAB-RPL-01')->first();
            RoomUser::updateOrCreate(
                ['room_id' => $labRpl->id, 'user_id' => $guru->id],
                ['assigned_at' => now()]
            );
        }
        $guruMulti = User::where('email', 'gurumulti@skandapras.test')->first();
        if ($guruMulti) {
            $roomCodes = ['LAB-RPL-01', 'LAB-MM-01', 'R-XII-RPL'];

            foreach ($roomCodes as $code) {
                $room = Room::where('code', $code)->first();

                if ($room) {
                    RoomUser::updateOrCreate(
                        ['room_id' => $room->id, 'user_id' => $guruMulti->id],
                        ['assigned_at' => now()]
                    );
                }
            }
        }
    }
}