@extends('layouts.backend')

@section('title', 'Buat Laporan Kerusakan')
@section('page-title', 'Laporan Saya')

@section('content')

    <x-ui.page-header title="Buat Laporan Kerusakan" subtitle="Laporkan kerusakan pada ruangan Anda" />

    <form action="{{ route('pengguna.damage-reports.store') }}" method="POST" enctype="multipart/form-data"
          class="space-y-5" x-data="reportForm()">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="lg:col-span-2 space-y-5">

                <x-ui.card title="Pilih Ruangan & Objek">
                    <div class="space-y-4">
                        <div>
                            <label for="room_id" class="block text-sm font-medium text-surface-700 mb-1.5">
                                Ruangan <span class="text-red-500">*</span>
                            </label>
                            <select id="room_id" name="room_id" required
                                    x-model="roomId" @change="onRoomChange()"
                                    class="w-full px-3.5 py-2.5 text-sm border rounded-lg bg-white cursor-pointer
                                           transition-all duration-150
                                           focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500
                                           @error('room_id') border-red-400 @else border-surface-300 hover:border-surface-400 @enderror">
                                <option value="">— Pilih Ruangan —</option>
                                @foreach ($myRooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }} ({{ $room->code }})</option>
                                @endforeach
                            </select>
                            @error('room_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div x-show="roomId" x-transition>
                            <label for="item_room_id" class="block text-sm font-medium text-surface-700 mb-1.5">
                                Objek Kerusakan
                            </label>
                            <select
                                id="item_room_id"
                                name="item_room_id"
                                x-model="itemRoomId"
                                class="w-full px-3.5 py-2.5 text-sm border border-surface-300 rounded-lg bg-white cursor-pointer
                                    transition-all duration-150 hover:border-surface-400
                                    focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                                <option value="">— Ruangan secara umum —</option>
                                <template x-for="item in filteredItems" :key="item.id">
                                    <option :value="item.id" x-text="item.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </x-ui.card>

                <x-ui.card title="Detail Kerusakan">
                    <x-ui.textarea name="description" label="Deskripsi Kerusakan" rows="6" :required="true"
                        placeholder="Jelaskan kerusakan yang terjadi secara detail...">{{ old('description') }}</x-ui.textarea>
                </x-ui.card>

            </div>

            <div class="lg:col-span-1">
                <x-ui.card title="Foto Bukti">
                    <div class="flex flex-col items-center">
                        <div class="w-full aspect-video rounded-xl border-2 border-dashed border-surface-300
                                    flex items-center justify-center overflow-hidden bg-surface-50 mb-4">
                            <img id="photo-preview" src="" class="w-full h-full object-cover hidden">
                            <div id="photo-placeholder" class="text-center">
                                <i data-lucide="camera" class="w-8 h-8 text-surface-400 mx-auto"></i>
                                <p class="text-xs text-surface-400 mt-1">Belum ada foto</p>
                            </div>
                        </div>
                        <label for="evidence_photo" class="cursor-pointer">
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg
                                         bg-surface-100 hover:bg-surface-200 text-surface-700 transition">
                                <i data-lucide="upload" class="w-4 h-4"></i> Pilih Foto
                            </span>
                            <input type="file" id="evidence_photo" name="evidence_photo" accept="image/*"
                                   class="hidden" onchange="previewPhoto(event)">
                        </label>
                        <p class="text-xs text-surface-400 mt-2 text-center">JPG, PNG, WebP. Maks 3MB.</p>
                        @error('evidence_photo') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </x-ui.card>

                <x-ui.card title="Info" class="mt-5">
                    <div class="text-xs text-surface-500 space-y-2">
                        <p class="flex items-start gap-2">
                            <i data-lucide="info" class="w-3.5 h-3.5 mt-0.5 shrink-0 text-primary-500"></i>
                            <span>Laporan akan diverifikasi oleh Waka Sarpras terlebih dahulu.</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5 mt-0.5 shrink-0 text-primary-500"></i>
                            <span>Setelah disetujui, perbaikan akan diproses.</span>
                        </p>
                    </div>
                </x-ui.card>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('pengguna.damage-reports.index') }}" variant="secondary">Batal</x-ui.button>
            <x-ui.button type="submit" icon="send" :loading="true">Kirim Laporan</x-ui.button>
        </div>
    </form>

@endsection

@push('scripts')
@php
    $itemsByRoom = $myRooms->mapWithKeys(fn($r) => [
        $r->id => $r->itemRooms->map(fn($ir) => [
            'id'       => $ir->id,
            'name'     => $ir->item->name,
            'code'     => $ir->item->code,
            'quantity' => $ir->quantity,
        ])->values()->toArray()
    ])->toArray();
@endphp

<script>
    function reportForm() {
        return {
            roomId: '{{ old('room_id', $selectedRoomId) }}',
            itemRoomId: '{{ old('item_room_id', $selectedItemRoomId) }}',
            itemsByRoom: {!! json_encode($itemsByRoom) !!},
            get filteredItems() {
                return this.itemsByRoom[this.roomId] || [];
            },
            onRoomChange() {
                this.itemRoomId = '';
            }
        }
    }

    function previewPhoto(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = document.getElementById('photo-preview');
            const placeholder = document.getElementById('photo-placeholder');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush