@extends('layouts.backend')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')

    <x-ui.page-header title="Notifikasi" subtitle="Semua pemberitahuan untuk Anda">
        <x-slot:actions>
            @if (auth()->user()->unreadNotifications->count() > 0)
                <form method="POST" action="{{ route('notifications.mark-all') }}">
                    @csrf
                    <x-ui.button type="submit" variant="outline" icon="check-check" size="sm">
                        Tandai Semua Dibaca
                    </x-ui.button>
                </form>
            @endif
            @if (auth()->user()->notifications->count() > 0)
                <form method="POST" action="{{ route('notifications.clear-all') }}"
                      onsubmit="return confirm('Hapus semua notifikasi?')">
                    @csrf @method('DELETE')
                    <x-ui.button type="submit" variant="danger" icon="trash-2" size="sm">
                        Hapus Semua
                    </x-ui.button>
                </form>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    {{-- Filter tabs --}}
    <div class="flex items-center gap-2 mb-4">
        <a href="{{ route('notifications.index', ['filter' => 'all']) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150
                  {{ $filter === 'all'
                      ? 'bg-primary-600 text-white shadow-sm'
                      : 'bg-white text-surface-600 border border-surface-200 hover:border-primary-300' }}">
            Semua ({{ auth()->user()->notifications->count() }})
        </a>
        <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-150
                  {{ $filter === 'unread'
                      ? 'bg-primary-600 text-white shadow-sm'
                      : 'bg-white text-surface-600 border border-surface-200 hover:border-primary-300' }}">
            Belum Dibaca ({{ auth()->user()->unreadNotifications->count() }})
        </a>
    </div>

    <x-ui.card :padding="false">
        @if ($notifications->isEmpty())
            <x-ui.empty-state icon="bell" title="Belum ada notifikasi"
                description="Notifikasi akan muncul saat ada aktivitas terkait Anda." />
        @else
            <ul class="divide-y divide-surface-100">
                @foreach ($notifications as $notif)
                    @php
                        $data = $notif->data;
                        $isUnread = $notif->read_at === null;
                    @endphp
                    <li class="group relative {{ $isUnread ? 'bg-primary-50/30' : '' }} hover:bg-surface-50/60 transition-colors">
                        <a href="{{ route('notifications.read', $notif->id) }}"
                           class="flex items-start gap-3 p-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                                        bg-{{ $data['color'] ?? 'primary' }}-100 text-{{ $data['color'] ?? 'primary' }}-600">
                                <i data-lucide="{{ $data['icon'] ?? 'bell' }}" class="w-5 h-5"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="text-sm font-semibold text-surface-900 {{ $isUnread ? '' : 'opacity-80' }}">
                                        {{ $data['title'] ?? 'Notifikasi' }}
                                    </p>
                                    @if ($isUnread)
                                        <span class="w-2 h-2 rounded-full bg-primary-600 shrink-0 mt-1.5"></span>
                                    @endif
                                </div>
                                <p class="text-sm text-surface-600 mt-0.5 {{ $isUnread ? '' : 'opacity-80' }}">
                                    {{ $data['message'] ?? '' }}
                                </p>
                                <p class="text-xs text-surface-400 mt-1">
                                    {{ $notif->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </a>

                        {{-- Delete button --}}
                        <form method="POST" action="{{ route('notifications.destroy', $notif->id) }}"
                              class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="p-1.5 rounded-md hover:bg-red-50 text-surface-400 hover:text-red-600 transition-colors"
                                    title="Hapus">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </li>
                @endforeach
            </ul>

            @if ($notifications->hasPages())
                <div class="p-4 border-t border-surface-100">{{ $notifications->links() }}</div>
            @endif
        @endif
    </x-ui.card>

@endsection