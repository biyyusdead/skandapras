<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — SKANDAPRAS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    {{-- Favicon --}}
    @php $logo = \App\Models\SchoolProfile::current()?->logo; @endphp
    @if ($logo)
        <link rel="icon" href="{{ asset('storage/' . $logo) }}">
    @endif
    
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- SKELETON INSTAN — deteksi navigasi SEBELUM body render     --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <script>
        (function() {
            if (sessionStorage.getItem('skandapras_nav') === '1') {
                document.documentElement.classList.add('nav-loading');
            }
        })();
    </script>
    <style>
        html.nav-loading body { visibility: hidden; }
        html.nav-loading { background-color: #fafafa; }
    </style>

    @stack('styles')
</head>
@php
    $__roleMenu = config('sidebar.' . auth()->user()->role->value, []);
    $__openKeys = [];
    foreach ($__roleMenu as $__i => $__item) {
        if (!empty($__item['children'])) {
            foreach ($__item['children'] as $__child) {
                if (is_route_active($__child['match'] ?? null)) {
                    $__openKeys[] = 'menu_' . $__i;
                    break;
                }
            }
        }
    }
@endphp
<body
    class="h-full bg-surface-50 font-sans text-surface-800 antialiased"
    data-open-menus="{{ json_encode($__openKeys) }}"
>
    <x-loading-bar />

<div x-data="{
        sidebarOpen: false,
        sidebarCollapsed: false,
        openMenus: {},
        search: '',
        toggleMenu(key) {
            this.openMenus[key] = !this.openMenus[key];
        },
        isMenuOpen(key) {
            return this.openMenus[key] || false;
        }
     }"
     x-init="(JSON.parse($el.dataset.openMenus || '[]')).forEach(k => openMenus[k] = true)"
>

    {{-- ============================================================ --}}
    {{-- SIDEBAR --}}
    {{-- ============================================================ --}}
    <aside
        x-cloak
        :class="{
            'translate-x-0': sidebarOpen,
            '-translate-x-full': !sidebarOpen,
            'lg:translate-x-0': true,
            'lg:w-72': !sidebarCollapsed,
            'lg:w-20': sidebarCollapsed,
        }"
        class="fixed inset-y-0 left-0 z-40 w-72 bg-primary-700 text-white flex flex-col
               transition-all duration-300 ease-smooth"
    >

        {{-- LOGO --}}
        <div class="shrink-0 px-4 pt-5 pb-4">
            <div class="flex items-center gap-3">
                @php $logo = \App\Models\SchoolProfile::current()?->logo; @endphp
                @if ($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="Logo"
                        class="w-10 h-10 object-contain shrink-0 drop-shadow-sm">
                @else
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shrink-0 overflow-hidden
                                ring-2 ring-white/20">
                        <i data-lucide="graduation-cap" class="w-5 h-5 text-primary-700"></i>
                    </div>
                @endif
                <div x-show="!sidebarCollapsed" x-transition.opacity class="min-w-0 flex-1">
                    <p class="text-sm font-bold text-white leading-tight truncate">SKANDAPRAS</p>
                    <p class="text-[11px] text-primary-200/70 leading-tight truncate">
                        {{ \App\Models\SchoolProfile::current()?->school_name ?? 'SMK Negeri 2 Karanganyar' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- SEARCH --}}
        <div x-show="!sidebarCollapsed" x-transition.opacity class="shrink-0 px-3 pb-3">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-primary-200/60 pointer-events-none"></i>
                <input
                    type="text"
                    x-model="search"
                    placeholder="Search"
                    class="w-full pl-9 pr-3 py-2 text-sm rounded-lg
                           bg-primary-600/40 placeholder:text-primary-200/60 text-white
                           border border-primary-500/30
                           focus:bg-primary-600/60 focus:border-primary-400 focus:outline-none
                           transition-colors duration-150">
            </div>
        </div>

        {{-- MENU --}}
        <nav class="flex-1 overflow-y-auto px-3 pb-4 space-y-1 sidebar-scroll">
            @php $menu = config('sidebar.' . auth()->user()->role->value, []); @endphp

            @foreach ($menu as $i => $item)
                @php
                    $hasChildren = isset($item['children']) && count($item['children']) > 0;
                    $menuKey     = 'menu_' . $i;
                    $isChildActive = false;
                    if ($hasChildren) {
                        foreach ($item['children'] as $child) {
                            if (is_route_active($child['match'] ?? null)) {
                                $isChildActive = true;
                                break;
                            }
                        }
                    }
                    $isParentActive = !$hasChildren && is_route_active($item['match'] ?? null);
                @endphp

                @if ($hasChildren)
                    <div x-show="!search || '{{ strtolower($item['label']) }}'.includes(search.toLowerCase()) || {{ collect($item['children'])->pluck('label')->map(fn($l) => strtolower($l))->toJson() }}.some(l => l.includes(search.toLowerCase()))">
                        <button type="button" x-on:click="toggleMenu('{{ $menuKey }}')"
                                :class="{
                                    'bg-primary-600/60 text-white': isMenuOpen('{{ $menuKey }}') || {{ $isChildActive ? 'true' : 'false' }},
                                    'text-primary-100 hover:bg-primary-600/40 hover:text-white': !(isMenuOpen('{{ $menuKey }}') || {{ $isChildActive ? 'true' : 'false' }})
                                }"
                                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                                    transition-all duration-200 ease-smooth">
                            <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 shrink-0"></i>
                            <span x-show="!sidebarCollapsed" x-transition.opacity class="flex-1 text-left truncate">{{ $item['label'] }}</span>
                            <i x-show="!sidebarCollapsed" data-lucide="chevron-down"
                            class="w-3.5 h-3.5 shrink-0 transition-transform duration-200"
                            :class="isMenuOpen('{{ $menuKey }}') ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="(isMenuOpen('{{ $menuKey }}') || search) && !sidebarCollapsed"
                            x-collapse
                            class="mt-1 ml-3 pl-3 border-l border-primary-600/40 space-y-0.5">
                            @foreach ($item['children'] as $child)
                                @php $childActive = is_route_active($child['match'] ?? null); @endphp
                                <a href="{{ safe_route($child['route']) }}"
                                x-show="!search || '{{ strtolower($child['label']) }}'.includes(search.toLowerCase())"
                                class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-150
                                        {{ $childActive
                                            ? 'bg-white text-primary-700 font-medium shadow-sm'
                                            : 'text-primary-100 hover:bg-primary-600/40 hover:text-white' }}">
                                    <span class="w-1 h-1 rounded-full {{ $childActive ? 'bg-primary-600' : 'bg-primary-300/60' }}"></span>
                                    <span class="truncate">{{ $child['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <a href="{{ safe_route($item['route']) }}"
                    title="{{ $item['label'] }}"
                    x-show="!search || '{{ strtolower($item['label']) }}'.includes(search.toLowerCase())"
                    class="group flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                            transition-all duration-200 ease-smooth
                            {{ $isParentActive
                                ? 'bg-white text-primary-700 shadow-sm'
                                : 'text-primary-100 hover:bg-primary-600/40 hover:text-white' }}">
                        <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4 shrink-0 transition-transform duration-200 group-hover:scale-110"></i>
                        <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate">{{ $item['label'] }}</span>
                    </a>
                @endif
            @endforeach

            {{-- Empty state kalau search tidak ketemu --}}
            <div x-show="search && !document.querySelector('nav a:not([style*=\'display: none\'])')"
                class="text-center py-6 px-3">
                <i data-lucide="search-x" class="w-8 h-8 text-primary-300/60 mx-auto mb-2"></i>
                <p class="text-xs text-primary-200/60">Menu tidak ditemukan</p>
            </div>
        </nav>

        {{-- BOTTOM MENU --}}
        <div class="shrink-0 px-3 pb-2 space-y-1 border-t border-primary-600/30 pt-3">
            <a href="{{ route('notifications.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                    text-primary-100 hover:bg-primary-600/40 hover:text-white transition-all duration-200">
                <i data-lucide="bell" class="w-4 h-4 shrink-0"></i>
                <span x-show="!sidebarCollapsed" x-transition.opacity class="flex-1 text-left truncate">Notifikasi</span>
                @if (auth()->user()->unreadNotifications->count() > 0)
                    <span x-show="!sidebarCollapsed" x-transition.opacity
                        class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full
                                bg-red-500 text-white text-[10px] font-semibold">
                        {{ auth()->user()->unreadNotifications->count() > 99 ? '99+' : auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </a>

            <a href="{{ route('settings.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium
                    text-primary-100 hover:bg-primary-600/40 hover:text-white transition-all duration-200">
                <i data-lucide="settings" class="w-4 h-4 shrink-0"></i>
                <span x-show="!sidebarCollapsed" x-transition.opacity class="truncate">Pengaturan</span>
            </a>
        </div>

        {{-- LOGO MITRA --}}
        <div x-show="!sidebarCollapsed" x-transition.opacity class="shrink-0 px-3 pb-2">
            <div class="rounded-lg bg-white p-2.5 ring-1 ring-primary-950/30">
                <p class="text-[9px] text-black uppercase tracking-widest text-center mb-2 font-medium">
                    Didukung Oleh
                </p>
                <div class="flex flex-wrap items-center justify-center gap-x-2 gap-y-1.5">
                    @foreach (['logoJHIC.webp', 'logoJagoanHosting.webp', 'logoKomdigi.webp', 'logoGarudaSpark.webp', 'logoNgalup.webp'] as $logoFile)
                        <img src="{{ asset('assets/images/jhic/' . $logoFile) }}"
                            onerror="this.style.display='none';"
                            alt="Mitra"
                            class="h-4 w-auto object-contain opacity-80 hover:opacity-100 transition-opacity">
                    @endforeach
                </div>
            </div>
        </div>

        {{-- USER PROFILE --}}
        <div class="shrink-0 p-3">
            <div x-data="{ userMenuOpen: false }" class="relative">
                <button
                    type="button"
                    x-on:click="userMenuOpen = !userMenuOpen"
                    class="w-full flex items-center gap-3 p-2 rounded-lg bg-primary-600/40 hover:bg-primary-600/60
                           transition-all duration-200">
                    @php
                        $avatarColor = avatar_color(auth()->user()->name);
                        $avatarInitials = avatar_initials(auth()->user()->name);
                    @endphp

                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-semibold text-white"
                        style="background-color: {{ $avatarColor }};">
                        {{ $avatarInitials }}
                    </div>
                    <div x-show="!sidebarCollapsed" x-transition.opacity class="min-w-0 flex-1 text-left">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[10px] text-primary-200/70 truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <i x-show="!sidebarCollapsed" data-lucide="more-vertical" class="w-4 h-4 text-primary-200/70 shrink-0"></i>
                </button>

                <div x-show="userMenuOpen"
                     x-cloak
                     x-on:click.outside="userMenuOpen = false"
                     x-transition:enter="transition ease-smooth duration-200"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-smooth duration-150"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute bottom-full left-0 right-0 mb-2 bg-white rounded-lg shadow-dropdown
                            border border-surface-200/70 py-1 z-50">
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-surface-700 hover:bg-surface-50">
                        <i data-lucide="user" class="w-4 h-4"></i> Profil Saya
                    </a>
                    <a href="{{ route('public.home') }}"
                    class="flex items-center gap-2 px-3 py-2 text-sm text-surface-700 hover:bg-surface-50">
                        <i data-lucide="globe" class="w-4 h-4"></i> Landing Page
                    </a>
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-2 px-3 py-2 text-sm text-surface-700 hover:bg-surface-50">
                        <i data-lucide="settings" class="w-4 h-4"></i> Pengaturan
                    </a>
                    <div class="border-t border-surface-100 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i data-lucide="log-out" class="w-4 h-4"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- OVERLAY MOBILE --}}
    <div
        x-show="sidebarOpen"
        x-cloak
        x-transition.opacity.duration.200ms
        x-on:click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-surface-900/40 backdrop-blur-sm lg:hidden"
    ></div>

    {{-- ============================================================ --}}
    {{-- MAIN --}}
    {{-- ============================================================ --}}
    <div
        :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-72'"
        class="transition-all duration-300 ease-smooth flex flex-col min-h-screen"
    >

        {{-- TOPBAR --}}
        <header class="sticky top-0 z-20 h-16 bg-white/80 backdrop-blur-md
                    border-b border-surface-200/70 flex items-center px-4 gap-3 shrink-0">

            <button x-on:click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden p-2 -ml-2 rounded-lg hover:bg-surface-100 text-surface-600
                        transition-colors duration-150 active:scale-95">
                <i data-lucide="menu" class="w-5 h-5"></i>
            </button>

            <button x-on:click="sidebarCollapsed = !sidebarCollapsed"
                    class="hidden lg:flex p-2 -ml-2 rounded-lg hover:bg-surface-100 text-surface-600
                        transition-colors duration-150 active:scale-95">
                <i data-lucide="panel-left" class="w-5 h-5"></i>
            </button>

            <div class="min-w-0 flex-1">
                <p class="text-sm font-medium text-surface-700 truncate">
                    @yield('page-title', 'Dashboard')
                </p>
            </div>

            {{-- Notification bell --}}
            <div x-data="notificationDropdown()" class="relative">
                <button x-on:click="toggle()"
                        class="relative p-2 rounded-lg hover:bg-surface-100 text-surface-600
                            transition-colors duration-150 active:scale-95">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    <template x-if="unreadCount > 0">
                        <span class="absolute top-1 right-1 min-w-[18px] h-[18px] px-1 rounded-full
                                    bg-red-500 text-white text-[10px] font-semibold
                                    flex items-center justify-center ring-2 ring-white"
                            x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                    </template>
                </button>

                <div x-show="open"
                    x-cloak
                    x-on:click.outside="close()"
                    x-transition:enter="transition ease-smooth duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-smooth duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                    class="absolute right-0 mt-2 w-96 bg-white border border-surface-200/70 rounded-xl
                            shadow-dropdown overflow-hidden origin-top-right">

                    <div class="flex items-center justify-between px-4 py-3 border-b border-surface-100">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-surface-900">Notifikasi</p>
                            <template x-if="unreadCount > 0">
                                <span class="px-1.5 py-0.5 rounded-full bg-primary-100 text-primary-700 text-[10px] font-semibold"
                                    x-text="unreadCount"></span>
                            </template>
                        </div>
                        <button x-on:click="markAllRead()"
                                x-show="unreadCount > 0"
                                class="text-xs text-primary-600 hover:text-primary-700 font-medium">
                            Tandai semua
                        </button>
                    </div>

                    <div class="max-h-96 overflow-y-auto">
                        <template x-if="loading">
                            <div class="p-8 text-center">
                                <div class="inline-block animate-spin rounded-full border-2 border-primary-500 border-t-transparent w-6 h-6"></div>
                                <p class="text-xs text-surface-400 mt-2">Memuat...</p>
                            </div>
                        </template>

                        <template x-if="!loading && notifications.length === 0">
                            <div class="p-8 text-center">
                                <i data-lucide="bell-off" class="w-10 h-10 text-surface-300 mx-auto mb-2"></i>
                                <p class="text-sm text-surface-500">Belum ada notifikasi</p>
                            </div>
                        </template>

                        <template x-if="!loading && notifications.length > 0">
                            <ul class="divide-y divide-surface-100">
                                <template x-for="notif in notifications" :key="notif.id">
                                    <li class="group relative hover:bg-surface-50/60 transition-colors">
                                        <a :href="notif.url" class="flex items-start gap-3 p-3 pr-10">
                                            <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
                                                :class="'bg-' + notif.color + '-100 text-' + notif.color + '-600'">
                                                <i :data-lucide="notif.icon" class="w-4 h-4"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-2">
                                                    <p class="text-xs font-semibold text-surface-900 truncate"
                                                    x-text="notif.title"></p>
                                                    <template x-if="!notif.read">
                                                        <span class="w-1.5 h-1.5 rounded-full bg-primary-600 shrink-0 mt-1.5"></span>
                                                    </template>
                                                </div>
                                                <p class="text-xs text-surface-600 mt-0.5 line-clamp-2"
                                                x-text="notif.message"></p>
                                                <p class="text-[10px] text-surface-400 mt-1"
                                                x-text="notif.created_at"></p>
                                            </div>
                                        </a>
                                    </li>
                                </template>
                            </ul>
                        </template>
                    </div>

                    <div class="border-t border-surface-100 p-2">
                        <a href="{{ route('notifications.index') }}"
                        class="block text-center text-xs font-medium text-primary-600 hover:text-primary-700 py-2 rounded-lg hover:bg-primary-50 transition-colors">
                            Lihat Semua Notifikasi
                        </a>
                    </div>
                </div>
            </div>

        </header>

        {{-- CONTENT --}}
        <main class="flex-1 px-4 sm:px-6 py-6">
            @if (session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     x-transition:enter="transition ease-smooth duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-smooth duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="mb-4 flex items-start gap-2.5 px-4 py-3 bg-green-50 border border-green-200/70 rounded-xl text-sm text-green-800">
                    <i data-lucide="check-circle" class="w-4 h-4 mt-0.5 shrink-0"></i>
                    <span class="flex-1">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     x-transition:enter="transition ease-smooth duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="mb-4 flex items-start gap-2.5 px-4 py-3 bg-red-50 border border-red-200/70 rounded-xl text-sm text-red-800">
                    <i data-lucide="alert-circle" class="w-4 h-4 mt-0.5 shrink-0"></i>
                    <span class="flex-1">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Konten asli --}}
            <div id="page-content" class="animate-fade-in-up">
                @yield('content')
            </div>

            {{-- Skeleton --}}
            <div id="page-skeleton" class="hidden">
                <x-skeleton type="text" :count="1" class="w-48 h-6 mb-6" />
                <x-skeleton type="table" :rows="6" :cols="4" />
            </div>
        </main>

        {{-- FOOTER --}}
        <footer class="px-4 sm:px-6 py-4 text-center text-xs text-surface-400 border-t border-surface-200/70 shrink-0">
            &copy; {{ date('Y') }} {{ \App\Models\SchoolProfile::current()?->school_name ?? 'SMK Negeri 2 Karanganyar' }}
        </footer>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- SCRIPT 1: LUCIDE + FORM SUBMIT                                  --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<script>
    document.addEventListener('alpine:initialized', () => window.initLucide?.());
    document.addEventListener('submit', (e) => {
        if (e.target.tagName === 'FORM' && e.target.checkValidity()) {
            setTimeout(() => window.dispatchEvent(new CustomEvent('form-submit')), 0);
        }
    }, true);
</script>

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- SCRIPT 2: NOTIFICATION DROPDOWN                                 --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<script>
    function notificationDropdown() {
        return {
            open: false,
            loading: false,
            notifications: [],
            unreadCount: {{ auth()->user()->unreadNotifications->count() }},

            toggle() {
                this.open = !this.open;
                if (this.open) this.fetchNotifications();
            },

            close() {
                this.open = false;
            },

            async fetchNotifications() {
                this.loading = true;
                try {
                    const res = await fetch('{{ route('notifications.dropdown') }}', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });
                    const data = await res.json();
                    this.notifications = data.notifications;
                    this.unreadCount = data.unread_count;
                    this.$nextTick(() => window.refreshLucide?.());
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                }
            },

            async markAllRead() {
                try {
                    await fetch('{{ route('notifications.mark-all') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        }
                    });
                    this.unreadCount = 0;
                    this.notifications.forEach(n => n.read = true);
                } catch (e) {
                    console.error(e);
                }
            }
        }
    }
</script>

{{-- Welcome Popup --}}
<x-welcome-popup />

{{-- ═══════════════════════════════════════════════════════════════ --}}
{{-- SCRIPT 3: FIX BFCACHE + SKELETON INSTAN                         --}}
{{-- ═══════════════════════════════════════════════════════════════ --}}
<script>
    // ═══ 1. FIX BFCACHE — reset saat Back/Forward button ═══
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            const content  = document.getElementById('page-content');
            const skeleton = document.getElementById('page-skeleton');

            if (content) content.classList.remove('hidden');
            if (skeleton) skeleton.classList.add('hidden');

            document.documentElement.classList.remove('nav-loading');

            const sp = document.getElementById('nav-spinner');
            if (sp && sp.parentNode) sp.parentNode.removeChild(sp);

            if (typeof LoadingState !== 'undefined' && LoadingState.cancel) {
                LoadingState.cancel('nav');
                LoadingState.cancel('form-btn');
            }

            sessionStorage.removeItem('skandapras_nav');
        }
    });

    // ═══ 2. SKELETON INSTAN saat navigasi ═══
    (function() {
        if (sessionStorage.getItem('skandapras_nav') !== '1') return;

        sessionStorage.removeItem('skandapras_nav');

        const hasContent  = !!document.getElementById('page-content');
        const hasSkeleton = !!document.getElementById('page-skeleton');

        if (!hasContent || !hasSkeleton) {
            document.documentElement.classList.remove('nav-loading');
            return;
        }

        let finished = false;

        const activate = () => {
            if (finished) return;
            finished = true;

            const content  = document.getElementById('page-content');
            const skeleton = document.getElementById('page-skeleton');

            if (content && skeleton) {
                content.classList.add('hidden');
                skeleton.classList.remove('hidden');
            }

            document.documentElement.classList.remove('nav-loading');

            setTimeout(() => {
                const c = document.getElementById('page-content');
                const s = document.getElementById('page-skeleton');
                if (c && s) {
                    c.classList.remove('hidden');
                    s.classList.add('hidden');
                }

                const sp = document.getElementById('nav-spinner');
                if (sp && sp.parentNode) sp.parentNode.removeChild(sp);
            }, 400);
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', activate);
        } else {
            activate();
        }

        setTimeout(() => {
            if (!finished) {
                finished = true;
                document.documentElement.classList.remove('nav-loading');
                const c = document.getElementById('page-content');
                const s = document.getElementById('page-skeleton');
                if (c) c.classList.remove('hidden');
                if (s) s.classList.add('hidden');
            }
        }, 3000);
    })();

    // ═══ 3. Deteksi klik link → skeleton + flag navigasi ═══
    document.addEventListener('click', (e) => {
        const a = e.target.closest('a');
        if (!a) return;
        const href = a.getAttribute('href');
        if (!href) return;
        if (
            a.target === '_blank' ||
            a.hasAttribute('download') ||
            href.startsWith('#') ||
            href.startsWith('javascript:') ||
            href.startsWith('mailto:') ||
            href.startsWith('tel:') ||
            a.hostname !== window.location.hostname
        ) return;
        if (a.pathname === window.location.pathname && a.search === window.location.search) return;

        sessionStorage.setItem('skandapras_nav', '1');

        const content  = document.getElementById('page-content');
        const skeleton = document.getElementById('page-skeleton');
        if (!content || !skeleton) return;

        if (typeof LoadingState !== 'undefined') {
            LoadingState.start('nav', {
                onSkeleton: () => {
                    content.classList.add('hidden');
                    skeleton.classList.remove('hidden');
                },
                onSpinner: () => {
                    if (!document.getElementById('nav-spinner')) {
                        const sp = document.createElement('div');
                        sp.id = 'nav-spinner';
                        sp.className = 'flex flex-col items-center justify-center py-8 gap-3';
                        sp.innerHTML = `
                            <span class="inline-block w-10 h-10 rounded-full border-4 border-primary-500 border-t-transparent animate-spin"></span>
                            <p class="text-sm text-surface-500">Masih memuat, mohon tunggu...</p>
                        `;
                        skeleton.prepend(sp);
                    }
                },
            });
        }
    }, true);
</script>

@stack('scripts')

</body>
</html>