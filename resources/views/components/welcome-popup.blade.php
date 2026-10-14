@props([
    'user' => auth()->user(),
])

@php
    $roleMessage = match($user->role->value) {
        'admin'            => 'Kelola data master sekolah, akademik, kesiswaan, dan berita dari satu dashboard.',
        'waka_sarpras'     => 'Kelola sarana prasarana, verifikasi laporan kerusakan, dan proses perbaikan.',
        'kepala_sekolah'   => 'Pantau kondisi sarana prasarana dan setujui laporan tingkat pimpinan.',
        'pengguna_ruangan' => 'Pantau ruangan Anda dan laporkan kerusakan dengan mudah.',
        default            => 'Selamat datang di SKANDAPRAS.',
    };

    $roleIcon = match($user->role->value) {
        'admin'            => 'shield',
        'waka_sarpras'     => 'wrench',
        'kepala_sekolah'   => 'eye',
        'pengguna_ruangan' => 'door-open',
        default            => 'sparkles',
    };

    $greeting = match(true) {
        now()->hour >= 5  && now()->hour <= 11 => 'Selamat Pagi',
        now()->hour >= 12 && now()->hour <= 14 => 'Selamat Siang',
        now()->hour >= 15 && now()->hour <= 17 => 'Selamat Sore',
        default                                => 'Selamat Malam',
    };

    $showWelcome = session('show_welcome', false);
@endphp

<div
    x-data="{
        open: {{ $showWelcome ? 'true' : 'false' }},
        dismissing: false,
        dismiss() {
            if (!this.open || this.dismissing) return;
            this.dismissing = true;
            document.body.style.overflow = '';

            // Delay supaya animasi leave sempat jalan
            setTimeout(() => { this.open = false; }, 400);
        }
    }"
    x-init="
        if (open) {
            document.body.style.overflow = 'hidden';
        }
    "
    x-show="open"
    x-cloak
    x-transition:enter="transition ease-smooth duration-400"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-smooth duration-400"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-on:click="dismiss()"
    x-on:keydown.escape.window="dismiss()"
    class="fixed inset-0 z-[100] flex items-center justify-center p-6 cursor-pointer
           bg-primary-950/80 backdrop-blur-md"
    style="display: none;"
>

    {{-- Content — animasi close slide-up --}}
    <div
        :class="dismissing
            ? 'opacity-0 -translate-y-12 scale-95 transition-all duration-400 ease-smooth'
            : 'opacity-100 translate-y-0 scale-100'"
        x-show="open"
        x-transition:enter="transition ease-smooth duration-500 delay-100"
        x-transition:enter-start="opacity-0 translate-y-8 scale-90"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        class="max-w-lg w-full text-center text-white pointer-events-none select-none
               transition-all duration-400 ease-smooth"
    >
        @php $logo = \App\Models\SchoolProfile::current()?->logo; @endphp
        <div class="flex justify-center mb-8">
            @if ($logo)
                <img src="{{ asset('storage/' . $logo) }}" alt="Logo"
                     class="w-24 h-24 object-contain drop-shadow-2xl animate-fade-in-up">
            @else
                <div class="w-24 h-24 rounded-3xl bg-white/10 ring-1 ring-white/20 flex items-center justify-center
                            backdrop-blur-md animate-fade-in-up">
                    <i data-lucide="{{ $roleIcon }}" class="w-12 h-12 text-white"></i>
                </div>
            @endif
        </div>

        <p class="text-sm font-medium text-primary-200/80 uppercase tracking-widest mb-2 animate-fade-in-up stagger-1">
            {{ $greeting }}
        </p>

        <h1 class="text-4xl sm:text-5xl font-bold tracking-tight mb-3 animate-fade-in-up stagger-2">
            {{ $user->name }}
        </h1>

        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full
                    bg-white/10 ring-1 ring-white/20 backdrop-blur-sm mb-6 animate-fade-in-up stagger-3">
            <i data-lucide="{{ $roleIcon }}" class="w-3.5 h-3.5"></i>
            <span class="text-xs font-medium">{{ $user->role->label() }}</span>
        </div>

        <p class="text-base text-primary-100/80 leading-relaxed max-w-md mx-auto mb-10 animate-fade-in-up stagger-4">
            {{ $roleMessage }}
        </p>

        <p class="text-xs text-primary-200/50 animate-fade-in-up stagger-4">
            <i data-lucide="mouse-pointer-click" class="w-3.5 h-3.5 inline -mt-0.5 mr-1"></i>
            Klik di mana saja untuk melanjutkan
        </p>
    </div>
</div>