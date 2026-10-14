@if ($report->status->value === 'menunggu_persetujuan_2')
    <x-ui.card title="Persetujuan Tahap 2">
        <p class="text-xs text-surface-500 mb-3">
            Setelah disetujui, Waka Sarpras dapat memulai proses perbaikan.
        </p>

        {{-- Approval 1 Info --}}
        @php $appr1 = $report->approvals->where('approval_level', 1)->first(); @endphp
        @if ($appr1 && $appr1->notes)
            <div class="p-3 bg-surface-50 rounded-lg mb-3">
                <p class="text-xs font-medium text-surface-600 mb-1">Catatan Waka Sarpras:</p>
                <p class="text-xs text-surface-700 italic">"{{ $appr1->notes }}"</p>
            </div>
        @endif

        <form action="{{ route('kepala.approvals.approve', $report) }}" method="POST" class="space-y-3 mb-3">
            @csrf
            <x-ui.textarea name="notes" label="Catatan (opsional)" rows="2"></x-ui.textarea>
            <x-ui.button type="submit" icon="check-circle" :loading="true" class="w-full">
                Setujui Laporan
            </x-ui.button>
        </form>

        <form action="{{ route('kepala.approvals.reject', $report) }}" method="POST" class="space-y-3 pt-3 border-t border-surface-100">
            @csrf
            <x-ui.textarea name="notes" label="Alasan Penolakan" rows="2" :required="true"></x-ui.textarea>
            <x-ui.button type="submit" variant="danger" icon="x-circle" :loading="true" class="w-full">
                Tolak Laporan
            </x-ui.button>
        </form>
    </x-ui.card>
@endif