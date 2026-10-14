@extends('layouts.backend')

@section('title', 'Monitoring Ruangan')
@section('page-title', 'Monitoring Ruangan')

@section('content')

    <x-ui.page-header title="Monitoring Ruangan" subtitle="Pantau kondisi seluruh ruangan sekolah" />

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.stat-card label="Total Ruangan" :value="$stats['total']" icon="door-open" color="primary" />
        <x-ui.stat-card label="Rata-rata Skor" :value="$stats['avg_score'] . '%'" icon="gauge" color="green" />
        <x-ui.stat-card label="Rusak Ringan" :value="$stats['rusak_ringan']" icon="alert-triangle" color="yellow" />
        <x-ui.stat-card label="Rusak Berat" :value="$stats['rusak_berat']" icon="alert-octagon" color="red" />
    </div>

    <x-ui.card :padding="false" class="mb-5">
        <div class="p-4">
            <form method="GET" class="flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-400 pointer-events-none"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari ruangan..."
                           class="w-full pl-9 pr-3 py-2 text-sm border border-surface-300 rounded-lg bg-white
                                  transition-all duration-150 hover:border-surface-400
                                  focus:ring-2 focus:ring-primary-500/30 focus:border-primary-500">
                </div>
                <select name="condition" class="px-3 py-2 text-sm border border-surface-300 rounded-lg bg-white cursor-pointer">
                    <option value="">Semua Kondisi</option>
                    @foreach ($conditions as $c)
                        <option value="{{ $c->value }}" @selected(request('condition') === $c->value)>{{ $c->label() }}</option>
                    @endforeach
                </select>
                <x-ui.button type="submit" variant="secondary" icon="filter">Filter</x-ui.button>
            </form>
        </div>
    </x-ui.card>

    @if ($rooms->isEmpty())
        <x-ui.card><x-ui.empty-state icon="door-open" title="Belum ada ruangan" /></x-ui.card>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($rooms as $room)
                <a href="{{ route('kepala.rooms.show', $room) }}"
                   class="group bg-white border border-surface-200/70 rounded-card shadow-card overflow-hidden hover-lift block">
                    <div class="relative aspect-video bg-surface-100 overflow-hidden">
                        @if ($room->photo)
                            <img src="{{ asset('storage/' . $room->photo) }}" alt="{{ $room->name }}"
                                 class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i data-lucide="door-open" class="w-10 h-10 text-surface-300"></i>
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-semibold text-surface-900 truncate">{{ $room->name }}</h3>
                        <p class="text-xs text-surface-500 mt-0.5 font-mono">{{ $room->code }}</p>
                        <div class="flex items-center gap-1.5 flex-wrap mt-3">
                            <x-ui.badge :color="$room->condition->color()">{{ $room->condition->label() }}</x-ui.badge>
                            <x-ui.badge color="gray" icon="package">{{ $room->items_count }}</x-ui.badge>
                        </div>
                        <div class="mt-3 pt-3 border-t border-surface-100">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] font-medium text-surface-500 uppercase tracking-wide">Health Score</span>
                                <span class="text-xs font-bold text-surface-800 tabular-nums">{{ $room->health_score }}%</span>
                            </div>
                            @php $sc = $room->health_score >= 70 ? 'bg-green-500' : ($room->health_score >= 40 ? 'bg-yellow-500' : 'bg-red-500'); @endphp
                            <div class="w-full h-1.5 bg-surface-100 rounded-full overflow-hidden">
                                <div class="h-full {{ $sc }} rounded-full" style="width: {{ $room->health_score }}%"></div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if ($rooms->hasPages())
            <div class="mt-5">{{ $rooms->links() }}</div>
        @endif
    @endif

@endsection