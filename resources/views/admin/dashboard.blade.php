@extends('layouts.backend')

@section('title', 'Dashboard Sekolah')
@section('page-title', 'Dashboard')

@section('content')

    <x-ui.page-header title="Dashboard Sekolah" subtitle="Ringkasan informasi sekolah" />

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.stat-card label="Total User" :value="\App\Models\User::count()" icon="users" color="primary" />
        <x-ui.stat-card label="Total Jurusan" :value="\App\Models\Department::count()" icon="layers" color="green" />
        <x-ui.stat-card label="Total Guru" :value="\App\Models\Teacher::count()" icon="user-check" color="indigo" />
        <x-ui.stat-card label="Total Berita" :value="\App\Models\News::count()" icon="newspaper" color="yellow" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 space-y-5">
            <x-ui.card title="Informasi Sekolah">
                <div class="flex items-start gap-5">
                    <div class="w-20 h-20 rounded-xl bg-primary-50 flex items-center justify-center shrink-0 overflow-hidden">
                        @php $school = \App\Models\SchoolProfile::current(); @endphp
                        @if ($school?->logo)
                            <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo" class="w-full h-full object-contain p-1">
                        @else
                            <i data-lucide="graduation-cap" class="w-10 h-10 text-primary-600"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-bold text-surface-900">{{ $school?->school_name ?? 'Belum diisi' }}</h3>
                        <p class="text-xs text-surface-500 mt-1">NPSN {{ $school?->npsn ?? '-' }}</p>
                        <p class="text-xs text-surface-500">{{ $school?->address ?? '-' }}</p>
                        <p class="text-xs text-surface-500 mt-0.5">{{ $school?->phone ?? '-' }}</p>
                    </div>
                    <x-ui.button href="{{ route('admin.school.edit') }}" variant="outline" size="sm" icon="pencil">Edit</x-ui.button>
                </div>
            </x-ui.card>

            <x-ui.card title="Berita Terbaru">
                @php $latestNews = \App\Models\News::latest()->take(5)->get(); @endphp
                @if ($latestNews->isEmpty())
                    <x-ui.empty-state icon="newspaper" title="Belum ada berita" />
                @else
                    <ul class="divide-y divide-surface-100 -mx-5 -mb-5">
                        @foreach ($latestNews as $news)
                            <li class="px-5 py-3 flex items-center justify-between gap-3 hover:bg-surface-50/60 transition-colors">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-surface-800 truncate">{{ $news->title }}</p>
                                    <p class="text-xs text-surface-400">{{ $news->created_at->format('d M Y') }}</p>
                                </div>
                                <x-ui.badge :color="$news->status === 'published' ? 'green' : 'gray'" :dot="true">
                                    {{ $news->status }}
                                </x-ui.badge>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>
        </div>

        <div>
            <x-ui.card title="Aktivitas Terbaru">
                @php $logs = \App\Models\ActivityLog::with('user:id,name')->latest()->take(8)->get(); @endphp
                @if ($logs->isEmpty())
                    <x-ui.empty-state icon="activity" title="Belum ada aktivitas" />
                @else
                    <ul class="space-y-3 -mx-2">
                        @foreach ($logs as $log)
                            <li class="flex items-start gap-3 px-2">
                                <div class="w-7 h-7 rounded-full bg-primary-50 text-primary-600 flex items-center justify-center shrink-0 text-xs font-semibold mt-0.5">
                                    {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs text-surface-700 leading-snug">
                                        <span class="font-medium">{{ $log->user?->name ?? 'System' }}</span>
                                        {{ $log->description }}
                                    </p>
                                    <p class="text-[10px] text-surface-400 mt-0.5">{{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>
        </div>
    </div>

@endsection