@extends('layouts.backend')

@section('title', 'Data Berita')
@section('page-title', 'Data Berita')

@section('content')

    <x-ui.page-header title="Data Berita" subtitle="Kelola berita dan artikel sekolah">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.news.create') }}" icon="plus">Tambah Berita</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.card :padding="false">
        <div class="p-4 border-b border-surface-100">
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul berita..."
                           class="w-full pl-9 pr-3 py-2 text-sm border border-surface-300 rounded-lg bg-white
                                  transition-all duration-150 hover:border-surface-400
                                  focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                </div>
                <select name="status" class="px-3 py-2 text-sm border border-surface-300 rounded-lg bg-white cursor-pointer
                                             transition-all duration-150 hover:border-surface-400
                                             focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                    <option value="">Semua Status</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="published" @selected(request('status') === 'published')>Dipublikasikan</option>
                </select>
                <x-ui.button type="submit" variant="secondary" icon="filter">Filter</x-ui.button>
            </form>
        </div>

        @if ($news->isEmpty())
            <x-ui.empty-state icon="newspaper" title="Belum ada berita">
                <x-slot:actions>
                    <x-ui.button href="{{ route('admin.news.create') }}" icon="plus" size="sm">Tambah Berita</x-ui.button>
                </x-slot:actions>
            </x-ui.empty-state>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wider text-surface-500 bg-surface-50/80 border-b border-surface-200/70">
                            <th class="px-4 py-3 w-12">
                                <input type="checkbox"
                                    data-bulk-select-all
                                    onchange="toggleAll(this)"
                                    class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30 cursor-pointer">
                            </th>
                            <th class="px-4 py-3 font-medium w-12">No</th>
                            <th class="px-4 py-3 font-medium w-20">Gambar</th>
                            <th class="px-4 py-3 font-medium">Judul</th>
                            <th class="px-4 py-3 font-medium">Author</th>
                            <th class="px-4 py-3 font-medium">Dipublikasikan</th>
                            <th class="px-4 py-3 font-medium w-32">Status</th>
                            <th class="px-4 py-3 font-medium text-right w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($news as $i => $item)
                            <tr class="hover:bg-surface-50/60 transition-colors duration-150">
                                <td class="px-4 py-3">
                                    <input type="checkbox"
                                        data-bulk-checkbox
                                        value="{{ $item->id }}"
                                        onchange="toggleOne(this)"
                                        class="rounded border-surface-300 text-primary-600 focus:ring-primary-500/30 cursor-pointer">
                                </td>
                                <td class="px-4 py-3 text-surface-500 tabular-nums">{{ $news->firstItem() + $i }}</td>
                                <td class="px-4 py-3">
                                    @if ($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" class="w-14 h-10 rounded-lg object-cover ring-1 ring-surface-200">
                                    @else
                                        <div class="w-14 h-10 rounded-lg bg-surface-100 flex items-center justify-center">
                                            <i data-lucide="image" class="w-4 h-4 text-surface-400"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-medium text-surface-800 max-w-md truncate">{{ $item->title }}</p>
                                    <p class="text-xs text-surface-400 truncate max-w-md">/{{ $item->slug }}</p>
                                </td>
                                <td class="px-4 py-3 text-surface-600">{{ $item->author?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-surface-600 text-xs">
                                    {{ $item->published_at?->format('d M Y H:i') ?? '—' }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($item->status === 'published')
                                        <x-ui.badge color="green" :dot="true">Published</x-ui.badge>
                                    @else
                                        <x-ui.badge color="gray" :dot="true">Draft</x-ui.badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <x-ui.table-action :href="route('admin.news.edit', $item)" icon="pencil" title="Edit" />
                                        <x-ui.table-action icon="trash-2" title="Hapus" variant="danger"
                                            :onclick="'confirmDelete(' . $item->id . ')'" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($news->hasPages())
                <div class="p-4 border-t border-surface-100">{{ $news->links() }}</div>
            @endif
        @endif
    </x-ui.card>

    <form id="delete-form" method="POST" style="display: none;">@csrf @method('DELETE')</form>

    <x-ui.confirm-modal id="delete-item" title="Hapus Berita"
        message="Apakah Anda yakin ingin menghapus berita ini?" confirm-text="Ya, Hapus" />

    <x-ui.bulk-action-bar
    :action="route('admin.news.bulk-action')"
    entity="berita"
    :actions="['delete' => 'Hapus']"
    :confirm="true" />    
@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        const form = document.getElementById('delete-form');
        form.action = `/admin/news/${id}`;
        window.dispatchEvent(new CustomEvent('open-confirm', {
            detail: { id: 'delete-item', action: form.action, method: 'DELETE' }
        }));
    }
</script>
@endpush