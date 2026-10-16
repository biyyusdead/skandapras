@props([
    'icon' => asset('assets/images/chatbot/chatbot.webp'),
    'title' => 'AI Assistant',
])

<div x-data="chatbot()" x-cloak 
     data-chat-url="{{ route('public.chatbot.chat') }}"
     @keydown.escape.window="isOpen = false" 
     @click.outside="isOpen = false"
     @open-chatbot.window="if (!isOpen) toggle()"
     @close-chatbot.window="if (isOpen) toggle()"
     class="fixed bottom-5 right-5 z-[60] flex flex-col items-end gap-4 sm:bottom-6 sm:right-6">

    <!-- =========================
         PANEL CHAT
    ========================== -->
    <section x-show="isOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95" role="dialog" aria-label="{{ $title }}"
        class="flex h-[560px] max-h-[calc(100vh-7.5rem)] w-[380px] max-w-[calc(100vw-2.5rem)] origin-bottom-right flex-col overflow-hidden rounded-[2rem] bg-[#5330AE] shadow-[0_20px_60px_rgba(40,20,100,0.4)] ring-1 ring-black/5 max-lg:max-h-[calc(100vh-9rem)] max-lg:mb-20">

        <!-- HEADER -->
        <header
            class="flex shrink-0 items-center gap-3.5 bg-gradient-to-b from-[#6C40D6] to-[#5330AE] px-5 pb-11 pt-5 text-white">

            <img src="{{ $icon }}" alt=""
                class="h-14 w-14 shrink-0 rounded-2xl object-contain shadow-lg shadow-black/30">

            <div class="min-w-0 flex-1">
                <h2 class="truncate text-[17px] font-bold leading-tight">{{ $title }}</h2>
                <p class="mt-0.5 flex items-center gap-1.5 text-[12px] font-semibold leading-tight">
                    <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-[#4ADE5C]"></span>
                    <span class="text-[#4ADE5C]">Online</span>
                    <span class="text-white">• Siap membantu</span>
                </p>
            </div>

            <!-- Hapus percakapan -->
            <button type="button" @click="clearChat()" aria-label="Hapus percakapan" title="Hapus percakapan"
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white/20 shadow-[inset_0_1px_2px_rgba(255,255,255,0.3)] ring-1 ring-white/30 transition hover:bg-white/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M9 3a1 1 0 0 0-1 1v1H4.5a1 1 0 1 0 0 2h15a1 1 0 1 0 0-2H16V4a1 1 0 0 0-1-1H9zM5.7 9l.9 10.1A2 2 0 0 0 8.6 21h6.8a2 2 0 0 0 2-1.9L18.3 9H5.7z" />
                </svg>
            </button>
        </header>

        <!-- BODY -->
        <div class="relative -mt-8 flex min-h-0 flex-1 flex-col rounded-t-[2rem] bg-white">

            <!-- Daftar pesan -->
            <div x-ref="scroller" class="min-h-0 flex-1 space-y-5 overflow-y-auto px-5 pb-3 pt-6">

                <template x-for="(m, i) in messages" :key="i">
                    <div>
                        <!-- Bot -->
                        <div x-show="m.role === 'bot'" class="flex items-start gap-3">
                            <img src="{{ $icon }}" alt=""
                                class="h-10 w-10 shrink-0 rounded-xl object-contain shadow-md shadow-violet-900/25">
                            <div class="min-w-0 pt-0.5">
                                <p x-show="m.title" x-text="m.title"
                                    class="text-[15px] font-bold leading-snug text-black"></p>
                                <p x-text="m.text"
                                    class="whitespace-pre-line break-words text-[14px] leading-relaxed text-slate-700">
                                </p>
                            </div>
                        </div>

                        <!-- User -->
                        <div x-show="m.role === 'user'" class="flex items-start justify-end gap-3">
                            <div
                                class="max-w-[75%] rounded-[1.4rem] bg-gradient-to-b from-[#9A74F0] to-[#6A3DD1] px-5 py-3.5 text-[13px] leading-relaxed text-white shadow-md shadow-violet-500/30">
                                <p x-text="m.text" class="whitespace-pre-line break-words"></p>
                            </div>
                            <span
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-b from-[#8A5FE6] to-[#6A3DD1] text-white shadow-md shadow-violet-500/30">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                    <path
                                        d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" />
                                </svg>
                            </span>
                        </div>
                    </div>
                </template>

                <!-- Sedang mengetik -->
                <div x-show="typing" class="flex items-start gap-3">
                    <img src="{{ $icon }}" alt=""
                        class="h-10 w-10 shrink-0 rounded-xl object-contain shadow-md shadow-violet-900/25">
                    <div class="flex items-center gap-1.5 pt-3.5" aria-label="Sedang mengetik">
                        <span class="h-2 w-2 animate-bounce rounded-full bg-[#8A5FE6]"
                            style="animation-delay:-0.3s"></span>
                        <span class="h-2 w-2 animate-bounce rounded-full bg-[#8A5FE6]"
                            style="animation-delay:-0.15s"></span>
                        <span class="h-2 w-2 animate-bounce rounded-full bg-[#8A5FE6]"></span>
                    </div>
                </div>
            </div>

            <!-- Saran pertanyaan -->
            <div class="shrink-0 px-5 pt-1">
                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Tanya sesuatu</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    <template x-for="q in suggestions" :key="q">
                        <button type="button" @click="send(q)" :disabled="typing"
                            class="rounded-xl border border-transparent bg-white px-3.5 py-2 text-[12px] font-medium text-black shadow-[0_2px_10px_rgba(80,50,160,0.18)] transition hover:border-[#6C40D6] hover:bg-violet-50 disabled:opacity-60"
                            x-text="q"></button>
                    </template>
                </div>
            </div>

            <!-- Input -->
            <form @submit.prevent="send()" class="flex shrink-0 items-center gap-3 px-5 pb-5 pt-4">
                <div class="relative min-w-0 flex-1">
                    <input type="text" x-model="draft" x-ref="input" maxlength="500"
                        aria-label="Tanyakan sesuatu tentang sekolah" autocomplete="off"
                        class="h-[46px] w-full rounded-2xl border border-[#C9BDEA] bg-[#F3F0FB] px-4 text-left text-[13px] text-black outline-none transition focus:border-[#6C40D6] focus:ring-2 focus:ring-violet-200">

                    <!-- Placeholder rata tengah -->
                    <span x-show="!draft" aria-hidden="true"
                        class="pointer-events-none absolute inset-0 flex items-center justify-center px-4 text-[13px] text-black">
                        Tanyakan sesuatu tentang sekolah...
                    </span>
                </div>

                <button type="submit" :disabled="!draft.trim() || typing" aria-label="Kirim"
                    class="flex h-[46px] w-[46px] shrink-0 items-center justify-center rounded-2xl bg-gradient-to-b from-[#7C4DDB] to-[#4B2A9C] text-white shadow-md shadow-violet-500/40 transition hover:brightness-110 active:scale-95 disabled:cursor-not-allowed disabled:opacity-60">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M3.4 20.4 20.85 12.92a1 1 0 0 0 0-1.84L3.4 3.6a1 1 0 0 0-1.39.91L2 9.12c0 .5.37.93.87.99L17 12 2.87 13.88c-.5.07-.87.5-.87.99l.01 4.61c0 .71.73 1.2 1.39.92z" />
                    </svg>
                </button>
            </form>
        </div>
    </section>


    <!-- =========================
         TRIGGER (ikon chatbot)
         Hidden di mobile — sudah ada di bottom nav
    ========================== -->
    <button type="button" @click="toggle()" :aria-expanded="isOpen" aria-label="Buka asisten AI"
        class="relative h-16 w-16 shrink-0 transition-transform duration-200 hover:-translate-y-0.5 active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-violet-400 focus-visible:ring-offset-2 rounded-2xl
               hidden lg:block">

        <!-- Ikon robot -->
        <span x-show="!isOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-75 rotate-12"
            x-transition:enter-end="opacity-100 scale-100 rotate-0" class="absolute inset-0 block">
            <img src="{{ $icon }}" alt="Asisten AI"
                class="h-full w-full object-contain drop-shadow-[0_8px_16px_rgba(80,40,160,0.45)]">
            <span class="absolute -right-0.5 -top-0.5 flex h-3.5 w-3.5">
                <span
                    class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#4ADE5C] opacity-70"></span>
                <span class="relative inline-flex h-3.5 w-3.5 rounded-full border-2 border-white bg-[#4ADE5C]"></span>
            </span>
        </span>

        <!-- Tombol tutup -->
        <span x-show="isOpen" x-cloak x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-75 -rotate-90"
            x-transition:enter-end="opacity-100 scale-100 rotate-0"
            class="absolute inset-0 flex items-center justify-center rounded-2xl bg-gradient-to-br from-[#7C4DDB] to-[#4B2A9C] text-white shadow-lg shadow-violet-500/40">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 6 6 18M6 6l12 12" />
            </svg>
        </span>
    </button>
</div>