<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemRoom;
use App\Models\Room;
use Illuminate\Database\Seeder;

class ItemRoomSeeder extends Seeder
{
    public function run(): void
    {
        if (Room::count() === 0) {
            $this->command->warn('Tidak ada Room. Jalankan RoomSeeder dulu.');

            return;
        }

        $rooms = Room::all();
        $items = Item::active()->get()->keyBy('code');

        // Barang yang hampir di setiap ruangan
        $commonItems = [
            'KRS-SIS' => 30,
            'MJS-SIS' => 15,
            'KRS-GRU' => 1,
            'MJS-GRU' => 1,
            'PPN-TLS' => 1,
            'LMP-TL5' => 4,
            'SPU-LNT' => 1,
            'PEL-LNT' => 1,
            'TPS-SMP' => 2,
            'KMC-NG' => 1,
        ];

        // Barang khusus per tipe ruangan
        $byRoomType = [
            'lab' => ['MJS-LAB' => 20, 'KRS-LAB' => 20, 'APR-KBR' => 1, 'P3K-KTK' => 1],
            'kelas' => ['PRY-LCD' => 1, 'LYR-PRY' => 1, 'AC-SPL' => 2],
            'kantor' => ['KMP-DSK' => 1, 'PRN-LSR' => 1, 'AC-SPL' => 1],
        ];

        foreach ($rooms as $room) {
            // Common items
            foreach ($commonItems as $code => $qty) {
                $item = $items->get($code);
                if (! $item) {
                    continue;
                }

                ItemRoom::updateOrCreate(
                    ['item_id' => $item->id, 'room_id' => $room->id],
                    [
                        'quantity' => $qty,
                        'condition' => 'baik',
                        'notes' => null,
                    ]
                );
            }

            // Kondisional berdasarkan nama/tipe ruangan
            $name = strtolower($room->name ?? '');

            if (str_contains($name, 'lab')) {
                $this->assign($items, $room, $byRoomType['lab']);
            }

            if (str_contains($name, 'kelas')) {
                $this->assign($items, $room, $byRoomType['kelas']);
            }

            if (str_contains($name, 'kantor') || str_contains($name, 'guru') || str_contains($name, 'tu')) {
                $this->assign($items, $room, $byRoomType['kantor']);
            }

            // Contoh: ruangan olahraga
            if (str_contains($name, 'olahraga') || str_contains($name, 'gym')) {
                $this->assign($items, $room, [
                    'BOL-BSK' => 3, 'BOL-VLY' => 3, 'BOL-SPK' => 2, 'NET-VLY' => 1, 'TND-DRT' => 1,
                ]);
            }
        }
    }

    protected function assign($items, $room, array $map): void
    {
        foreach ($map as $code => $qty) {
            $item = $items->get($code);
            if (! $item) {
                continue;
            }

            ItemRoom::updateOrCreate(
                ['item_id' => $item->id, 'room_id' => $room->id],
                [
                    'quantity' => $qty,
                    'condition' => 'baik',
                    'notes' => null,
                ]
            );
        }
    }
}
