@props([
    'id'      => 'confirm-modal',
    'title'   => 'Konfirmasi',
    'message' => 'Apakah Anda yakin?',
    'confirmText' => 'Ya, Lanjutkan',
    'variant' => 'danger',
])

<div
    x-data="{ open: false, action: null, method: 'POST' }"
    x-on:open-confirm.window="
        if ($event.detail.id === '{{ $id }}') {
            open = true;
            action = $event.detail.action;
            method = $event.detail.method || 'POST';
        }
    "
    x-show="open"
    x-cloak
    x-transition:enter="transition ease-smooth duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-smooth duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
>
    <div class="fixed inset-0 bg-surface-900/40 backdrop-blur-sm" x-on:click="open = false"></div>

    <div
        x-show="open"
        x-transition:enter="transition ease-smooth duration-250"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-smooth duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative bg-white rounded-card shadow-dropdown max-w-sm w-full p-6"
    >
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-red-50 ring-1 ring-red-100 flex items-center justify-center shrink-0">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="font-semibold text-surface-900">{{ $title }}</h3>
                <p class="text-sm text-surface-500 mt-1 leading-relaxed">{{ $message }}</p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-2 mt-6">
            <x-ui.button variant="secondary" size="sm" x-on:click="open = false">
                Batal
            </x-ui.button>

            <form :action="action" method="POST" x-data="{ submitting: false }"
                x-on:submit="submitting = true">
                @csrf
                <input type="hidden" name="_method" :value="method">
                <button
                    type="submit"
                    :disabled="submitting"
                    class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-xs font-medium rounded-lg
                        bg-red-600 hover:bg-red-700 text-white shadow-sm hover:shadow-md
                        transition-all duration-200 ease-smooth
                        disabled:opacity-60 disabled:cursor-not-allowed active:scale-[0.98]"
                >
                    <template x-if="!submitting">
                        <span>{{ $confirmText }}</span>
                    </template>
                    <template x-if="submitting">
                        <span class="inline-flex items-center gap-2">
                            <x-ui.spinner size="sm" />
                            <span>Menghapus...</span>
                        </span>
                    </template>
                </button>
            </form>
        </div>
    </div>
</div>