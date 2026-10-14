@extends('layouts.backend')

@section('title', 'Log Aktivitas')
@section('page-title', 'Log Aktivitas')

@section('content')

    <x-ui.page-header
        title="Log Aktivitas"
        subtitle="Riwayat aktivitas penting pengguna sistem"
    />

    <x-ui.card :padding="false">
        {{-- Filter --}}
        <div class="p-4 border-b border-surface-100">
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari deskripsi..."
                           class="w-full pl-9 pr-3 py-2 text-sm border border-surface-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                </div>
                <select name="action" class="px-3 py-2 text-sm border border-surface-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Aksi</option>
                    @foreach ($actions as $action)
                        <option value="{{ $action }}" @selected(request('action') === $action)>{{ $action }}</option>
                    @endforeach
                </select>
                <select name="module" class="px-3 py-2 text-sm border border-surface-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Modul</option>
                    @foreach ($modules as $module)
                        <option value="{{ $module }}" @selected(request('module') === $module)>{{ $module }}</option>
                    @endforeach
                </select>
                <x-ui.button type="submit" variant="secondary" icon="filter">Filter</x-ui.button>
            </form>
        </div>

        @if ($logs->isEmpty())
            <x-ui.empty-state icon="scroll-text" title="Belum ada log aktivitas" />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs uppercase tracking-wider text-surface-500 bg-surface-50">
                            <th class="px-4 py-3 font-medium w-12">No</th>
                            <th class="px-4 py-3 font-medium">Waktu</th>
                            <th class="px-4 py-3 font-medium">User</th>
                            <th class="px-4 py-3 font-medium">Aksi</th>
                            <th class="px-4 py-3 font-medium">Modul</th>
                            <th class="px-4 py-3 font-medium">Deskripsi</th>
                            <th class="px-4 py-3 font-medium">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-surface-100">
                        @foreach ($logs as $i => $log)
                            <tr class="hover:bg-surface-50">
                                <td class="px-4 py-3 text-surface-500">{{ $logs->firstItem() + $i }}</td>
                                <td class="px-4 py-3 text-surface-600 whitespace-nowrap">
                                    {{ $log->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($log->user)
                                        <p class="font-medium text-surface-800">{{ $log->user->name }}</p>
                                        <p class="text-xs text-surface-400">{{ $log->user->email }}</p>
                                    @else
                                        <span class="text-surface-400">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <x-ui.badge color="purple">{{ $log->action }}</x-ui.badge>
                                </td>
                                <td class="px-4 py-3 text-surface-600">{{ $log->module ?: '—' }}</td>
                                <td class="px-4 py-3 text-surface-600 max-w-md truncate">{{ $log->description }}</td>
                                <td class="px-4 py-3 text-surface-400 text-xs">{{ $log->ip_address }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="p-4 border-t border-surface-100">{{ $logs->links() }}</div>
            @endif
        @endif
    </x-ui.card>

@endsection