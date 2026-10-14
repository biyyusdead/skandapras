@extends('layouts.backend')

@section('title', 'Edit Perbaikan')
@section('page-title', 'Laporan Perbaikan')

@section('content')

    <x-ui.page-header title="Edit Perbaikan" :subtitle="'Laporan: ' . $repair->damageReport->report_code">
        <x-slot:actions>
            <x-ui.button href="{{ route('waka.repair-reports.index') }}" variant="secondary" icon="arrow-left">
                Kembali
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <form action="{{ route('waka.repair-reports.update', $repair) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf @method('PUT')

        <x-ui.card title="Data Perbaikan">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-ui.input name="technician_name" label="Nama Teknisi" :value="old('technician_name', $repair->technician_name)" :required="true" icon="user" />
                <x-ui.input name="technician_contact" label="Kontak Teknisi" :value="old('technician_contact', $repair->technician_contact)" icon="phone" />
                <x-ui.input name="started_at" label="Tanggal Mulai" type="date"
                            :value="old('started_at', $repair->started_at?->format('Y-m-d'))" icon="calendar" />
                <x-ui.input name="completed_at" label="Tanggal Selesai" type="date"
                            :value="old('completed_at', $repair->completed_at?->format('Y-m-d'))" icon="calendar"
                            hint="Isi jika perbaikan sudah selesai" />
                <x-ui.input name="repair_cost" label="Biaya Perbaikan (Rp)" type="number"
                            :value="old('repair_cost', $repair->repair_cost)" icon="dollar-sign" />
                <x-ui.select name="status" label="Status" :required="true"
                    :options="['ready' => 'Siap', 'in_progress' => 'Diproses', 'completed' => 'Selesai']"
                    :value="old('status', $repair->status->value)" :placeholder="null" />
                <div class="md:col-span-2">
                    <x-ui.textarea name="repair_description" label="Deskripsi Tindakan Perbaikan" rows="4">{{ old('repair_description', $repair->repair_description) }}</x-ui.textarea>
                </div>
                <div class="md:col-span-2">
                    <x-ui.textarea name="notes" label="Catatan Tambahan" rows="2">{{ old('notes', $repair->notes) }}</x-ui.textarea>
                </div>
            </div>
        </x-ui.card>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            {{-- Before --}}
            <x-ui.card title="Foto Sebelum Perbaikan">
                <div class="flex flex-col items-center">
                    <div class="w-full aspect-video rounded-xl border-2 border-dashed border-surface-300
                                flex items-center justify-center overflow-hidden bg-surface-50 mb-4">
                        @if ($repair->repair_evidence_before)
                            <img id="before-preview" src="{{ asset('storage/' . $repair->repair_evidence_before) }}" class="w-full h-full object-cover">
                            <div id="before-placeholder" class="hidden"></div>
                        @else
                            <img id="before-preview" src="" class="w-full h-full object-cover hidden">
                            <div id="before-placeholder" class="text-center">
                                <i data-lucide="camera" class="w-8 h-8 text-surface-400 mx-auto"></i>
                            </div>
                        @endif
                    </div>
                    <label for="repair_evidence_before" class="cursor-pointer">
                        <span class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg
                                     bg-surface-100 hover:bg-surface-200 text-surface-700 transition">
                            <i data-lucide="upload" class="w-4 h-4"></i> Ganti Foto
                        </span>
                        <input type="file" id="repair_evidence_before" name="repair_evidence_before" accept="image/*"
                               class="hidden" onchange="previewImage(event, 'before')">
                    </label>
                </div>
            </x-ui.card>

            {{-- After --}}
            <x-ui.card title="Foto Setelah Perbaikan">
                <div class="flex flex-col items-center">
                    <div class="w-full aspect-video rounded-xl border-2 border-dashed border-surface-300
                                flex items-center justify-center overflow-hidden bg-surface-50 mb-4">
                        @if ($repair->repair_evidence_after)
                            <img id="after-preview" src="{{ asset('storage/' . $repair->repair_evidence_after) }}" class="w-full h-full object-cover">
                            <div id="after-placeholder" class="hidden"></div>
                        @else
                            <img id="after-preview" src="" class="w-full h-full object-cover hidden">
                            <div id="after-placeholder" class="text-center">
                                <i data-lucide="camera" class="w-8 h-8 text-surface-400 mx-auto"></i>
                            </div>
                        @endif
                    </div>
                    <label for="repair_evidence_after" class="cursor-pointer">
                        <span class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium rounded-lg
                                     bg-surface-100 hover:bg-surface-200 text-surface-700 transition">
                            <i data-lucide="upload" class="w-4 h-4"></i> Pilih Foto
                        </span>
                        <input type="file" id="repair_evidence_after" name="repair_evidence_after" accept="image/*"
                               class="hidden" onchange="previewImage(event, 'after')">
                    </label>
                </div>
            </x-ui.card>
        </div>

        <div class="flex items-center justify-end gap-2">
            <x-ui.button href="{{ route('waka.repair-reports.index') }}" variant="secondary">Batal</x-ui.button>
            <x-ui.button type="submit" icon="save" :loading="true">Simpan Perubahan</x-ui.button>
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
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(file);
    }
</script>
@endpush