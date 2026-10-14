@extends('layouts.backend')

@section('title', 'Mulai Perbaikan')
@section('page-title', 'Laporan Perbaikan')

@section('content')

    <x-ui.page-header title="Mulai Perbaikan" :subtitle="'Laporan: ' . $report->report_code">
        <x-slot:actions>
            <x-ui.button href="{{ route('waka.damage-reports.show', $report) }}" variant="secondary" icon="arrow-left">
                Kembali
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Info Laporan --}}
    <x-ui.card title="Informasi Laporan" class="mb-5">
        <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Kode</dt>
                <dd class="text-sm font-mono text-surface-800">{{ $report->report_code }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Ruangan</dt>
                <dd class="text-sm text-surface-800">{{ $report->room->name }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Objek</dt>
                <dd class="text-sm text-surface-800">{{ $report->itemRoom?->item?->name ?? 'Ruangan (umum)' }}</dd>
            </div>
            <div class="sm:col-span-3">
                <dt class="text-xs font-medium text-surface-500 uppercase tracking-wide mb-1">Deskripsi Kerusakan</dt>
                <dd class="text-sm text-surface-700">{{ $report->description }}</dd>
            </div>
        </dl>
    </x-ui.card>

    <form action="{{ route('waka.repair-reports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <input type="hidden" name="damage_report_id" value="{{ $report->id }}">

        <x-ui.card title="Data Perbaikan">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input name="technician_name" label="Nama Teknisi" :value="old('technician_name')" :required="true" icon="user"
                            placeholder="Contoh: Pak Budi" />
                <x-ui.input name="technician_contact" label="Kontak Teknisi" :value="old('technician_contact')" icon="phone"
                            placeholder="08xx" />
                <x-ui.input name="started_at" label="Tanggal Mulai" type="date" :value="old('started_at', now()->format('Y-m-d'))" icon="calendar" />
                <x-ui.input name="repair_cost" label="Biaya Perbaikan (Rp)" type="number" :value="old('repair_cost')" icon="dollar-sign"
                            placeholder="0" hint="Kosongkan jika gratis" />
                <div class="md:col-span-2">
                    <x-ui.textarea name="repair_description" label="Deskripsi Tindakan Perbaikan" rows="4"
                        placeholder="Jelaskan tindakan perbaikan yang dilakukan...">{{ old('repair_description') }}</x-ui.textarea>
                </div>
                <div class="md:col-span-2">
                    <x-ui.textarea name="notes" label="Catatan Tambahan" rows="2">{{ old('notes') }}</x-ui.textarea>
                </div>
            </div>
        </x-ui.card>

        <x-ui.card title="Foto Sebelum Perbaikan">
            <div class="flex flex-col items-start">
                <div class="w-full max-w-md aspect-video rounded-xl border-2 border-dashed border-surface-300
                            flex items-center justify-center overflow-hidden bg-surface-50 mb-4">
                    <img id="before-preview" src="" class="w-full h-full object-cover hidden">
                    <div id="before-placeholder" class="text-center">
                        <i data-lucide="camera" class="w-8 h-8 text-surface-400 mx-auto"></i>
                    </div>
                </div>
                <label for="repair_evidence_before" class="cursor-pointer">
                    <span class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg
                                 bg-surface-100 hover:bg-surface-200 text-surface-700 transition">
                        <i data-lucide="upload" class="w-4 h-4"></i> Pilih Foto
                    </span>
                    <input type="file" id="repair_evidence_before" name="repair_evidence_before" accept="image/*"
                           class="hidden" onchange="previewImage(event, 'before')">
                </label>
                <p class="text-xs text-surface-400 mt-2">Opsional. Foto kondisi sebelum diperbaiki.</p>
            </div>
        </x-ui.card>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('waka.damage-reports.show', $report) }}" variant="secondary">Batal</x-ui.button>
            <x-ui.button type="submit" icon="wrench" :loading="true">Mulai Perbaikan</x-ui.button>
        </div>
    </form>

@endsection

@push('scripts')
<script>
    function previewImage(event, prefix) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = (e) => {
            const preview = document.getElementById(prefix + '-preview');
            const placeholder = document.getElementById(prefix + '-placeholder');
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush