@props([
    'action'    => '',
    'entity'    => 'item',
    'actions'   => [],
    'confirm'   => false,
])

<div x-data="bulkAction()" x-show="selected.length > 0" x-cloak x-transition
     class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50
            bg-surface-900 text-white rounded-2xl shadow-2xl px-5 py-3.5
            flex items-center gap-4 backdrop-blur-md">
    <div class="flex items-center gap-3">
        <div class="w-8 h-8 rounded-full bg-primary-600 flex items-center justify-center text-xs font-bold">
            <span x-text="selected.length"></span>
        </div>
        <span class="text-sm font-medium">
            <span x-text="selected.length"></span> {{ $entity }} dipilih
        </span>
    </div>

    <div class="w-px h-6 bg-white/20"></div>

    <div class="flex items-center gap-2">
        @foreach ($actions as $value => $label)
            <button type="button"
                    @click="submit('{{ $value }}', {{ $confirm ? 'true' : 'false' }})"
                    class="px-3.5 py-1.5 rounded-lg text-xs font-medium transition-all
                           {{ $value === 'delete'
                               ? 'bg-red-500 hover:bg-red-600'
                               : 'bg-white/10 hover:bg-white/20' }}">
                {{ $label }}
            </button>
        @endforeach

        <button type="button" @click="clear()"
                class="p-1.5 rounded-lg hover:bg-white/10 transition-all" title="Batal">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Form hidden — POST saja, tanpa method override --}}
    <form x-ref="form" method="POST" action="{{ $action }}" class="hidden">
        @csrf
        <input type="hidden" name="ids" :value="JSON.stringify(selected)">
        <input type="hidden" name="bulk_action" :value="currentAction">
    </form>
</div>

@push('scripts')
<script>
    function bulkAction() {
        return {
            selected: [],
            currentAction: '',

            submit(action, requireConfirm) {
                if (this.selected.length === 0) return;

                this.currentAction = action;
                this.$refs.form.querySelector('input[name="bulk_action"]').value = action;

                if (requireConfirm) {
                    const label = action === 'delete' ? 'menghapus' : action;
                    if (!confirm(`Yakin ingin ${label} ${this.selected.length} data terpilih?`)) {
                        return;
                    }
                }

                this.$refs.form.submit();
            },

            clear() {
                this.selected = [];
                document.querySelectorAll('[data-bulk-checkbox]').forEach(cb => cb.checked = false);
                document.querySelectorAll('[data-bulk-select-all]').forEach(cb => cb.checked = false);
            },
        }
    }

    window.toggleAll = function(masterCheckbox) {
        const checkboxes = document.querySelectorAll('[data-bulk-checkbox]');
        checkboxes.forEach(cb => cb.checked = masterCheckbox.checked);

        const selected = Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value);

        const bar = document.querySelector('[x-data="bulkAction()"]');
        if (bar && window.Alpine) {
            const data = Alpine.$data(bar);
            if (data) data.selected = selected;
        }
    };

    window.toggleOne = function(checkbox) {
        const all = document.querySelectorAll('[data-bulk-checkbox]');
        const selected = Array.from(all).filter(cb => cb.checked).map(cb => cb.value);

        const master = document.querySelector('[data-bulk-select-all]');
        if (master) {
            master.checked = all.length > 0 && selected.length === all.length;
            master.indeterminate = selected.length > 0 && selected.length < all.length;
        }

        const bar = document.querySelector('[x-data="bulkAction()"]');
        if (bar && window.Alpine) {
            const data = Alpine.$data(bar);
            if (data) data.selected = selected;
        }
    };
</script>
@endpush