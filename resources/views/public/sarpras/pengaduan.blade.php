@extends('layouts.public')

@section('title', 'Formulir Pengaduan')

@section('content')

    <style>[x-cloak]{display:none !important}</style>

    <main x-data="reportModal" class="min-h-screen bg-[#ffff] px-4 py-10 sm:px-6 lg:px-8">

        {{-- HEADER --}}
        <div class="mb-8 text-center">
            <h1 class="text-2xl font-bold leading-tight text-gray-900 sm:text-3xl lg:text-4xl">
                Formulir Pengaduan
            </h1>
            <p class="mx-auto mt-3 max-w-2xl text-sm leading-6 text-gray-500 sm:text-base">
                Bantu kami meningkatkan kualitas fasilitas sekolah dengan melaporkan masalah yang Anda temukan
            </p>
        </div>

        {{-- SESSION FLASH --}}
        @if (session('success'))
            <div class="mx-auto mb-8 max-w-4xl rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mx-auto mb-8 max-w-4xl rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- HERO CARD --}}
        <section class="mx-auto mt-8 max-w-4xl sm:mt-12">
            <div class="relative isolate overflow-hidden rounded-2xl bg-gradient-to-r from-[#6C4AB6] to-[#5A3AA4]
                        px-5 py-10 text-white shadow-xl shadow-indigo-900/20 sm:rounded-[2rem] sm:px-12 sm:py-12">

                <div class="pointer-events-none absolute -left-8 -top-6 z-0 h-40 w-40 rounded-full bg-white/10"></div>
                <div class="pointer-events-none absolute -right-12 top-20 z-0 h-64 w-64 rounded-full bg-white/10"></div>
                <div class="pointer-events-none absolute -bottom-20 left-10 z-0 h-56 w-56 rounded-full bg-white/10"></div>

                <div class="relative z-10">
                    <div class="mx-auto max-w-md text-center">
                        <h2 class="text-lg font-semibold text-white sm:text-2xl">Temukan Masalah di Sekolah?</h2>
                        <p class="mt-2 text-base font-medium leading-snug text-white sm:text-xl">
                            Laporkan sekarang agar bisa segera ditangani oleh tim Sarpras
                        </p>
                    </div>

                    <div class="mx-auto mt-10 max-w-3xl sm:mt-14">
                        <h3 class="text-base font-medium text-white sm:text-lg">Cara Melaporkan Masalah</h3>
                        <ol class="mt-4 grid grid-cols-1 gap-3 text-sm font-medium text-violet-50 sm:grid-cols-3 sm:gap-4">
                            @php
                                $steps = [
                                    ['icon' => 'file-pen-line', 'title' => 'Laporkan', 'desc' => 'Isi lokasi, judul, dan deskripsi masalah.'],
                                    ['icon' => 'camera',        'title' => 'Bukti Foto', 'desc' => 'Unggah foto kondisi atau kerusakan.'],
                                    ['icon' => 'send',          'title' => 'Kirim Laporan', 'desc' => 'Periksa kembali detail lalu kirim.'],
                                ];
                            @endphp

                            @foreach ($steps as $s)
                                <li class="flex items-start gap-3 rounded-xl bg-white/5 p-3 ring-1 ring-white/10">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/15 ring-1 ring-white/20">
                                        <i data-lucide="{{ $s['icon'] }}" class="w-4 h-4 text-white"></i>
                                    </span>
                                    <div class="min-w-0">
                                        <strong class="block text-white text-sm">{{ $s['title'] }}</strong>
                                        <span class="text-xs sm:text-sm text-violet-100/90">{{ $s['desc'] }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    </div>

                    <div class="mt-12 flex justify-center sm:mt-16">
                        <a href="#" @click.prevent="open()" aria-haspopup="dialog"
                        class="group inline-flex items-center justify-center gap-3 rounded-full bg-white px-8 py-4
                                text-base font-bold text-[#6C4AB6] shadow-xl shadow-black/20
                                transition-all duration-300 hover:-translate-y-1 hover:bg-violet-50 hover:shadow-2xl
                                active:translate-y-0 active:scale-95
                                sm:px-12 sm:py-5 sm:text-lg">
                            <i data-lucide="file-plus" class="w-6 h-6 sm:w-7 sm:h-7"></i>
                            <span>Buat Laporan Aduan</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- MODAL --}}
        <div x-cloak x-show="isOpen" @keydown.escape.window="close()"
             class="fixed inset-0 z-50 flex items-center justify-center p-2 sm:p-4"
             role="dialog" aria-modal="true">

            {{-- Backdrop --}}
            <div x-show="isOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 @click="close()"
                 class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            {{-- Panel --}}
            <div x-show="isOpen"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 class="relative flex max-h-[96vh] w-full max-w-[800px] flex-col overflow-hidden rounded-2xl bg-white shadow-[0_20px_60px_rgba(30,20,70,0.35)] sm:rounded-[2.5rem]">

                {{-- HEADER --}}
                <div class="flex shrink-0 items-center gap-3 bg-gradient-to-r from-[#6C4AB6] to-[#5B3BA5] px-4 py-4 text-white sm:gap-4 sm:px-8 sm:py-6">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/20 ring-1 ring-white/40 sm:h-12 sm:w-12">
                        <i data-lucide="file-pen-line" class="w-5 h-5 sm:w-6 sm:h-6"></i>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h2 class="text-base font-bold leading-tight sm:text-xl">Laporkan Masalah</h2>
                        <p class="mt-0.5 text-[10px] text-white/95 sm:text-xs">
                            Isi formulir untuk melaporkan masalah sarana &amp; prasarana
                        </p>
                    </div>
                    <button type="button" @click="close()" aria-label="Tutup"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/20 ring-1 ring-white/40 transition hover:bg-white/30 sm:h-12 sm:w-12">
                        <i data-lucide="x" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    </button>
                </div>

                {{-- BODY --}}
                <div class="min-h-0 flex-1 overflow-y-auto px-4 pb-6 pt-6 sm:px-16 sm:pb-8 sm:pt-8">

                    {{-- STEPPER --}}
                    <div x-show="!sent">
                        <div class="flex items-center">
                            @php
                                $stepIcons = [
                                    1 => 'file-pen-line',
                                    2 => 'camera',
                                    3 => 'send',
                                ];
                            @endphp

                            @foreach ($stepIcons as $n => $icon)
                                <div class="flex flex-col items-center shrink-0">
                                    <span class="flex h-10 w-10 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-full
                                                shadow-lg ring-4 ring-white transition-all duration-300"
                                          :class="circle({{ $n }})">
                                        <i data-lucide="{{ $icon }}" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                                    </span>
                                    <span class="mt-2 text-[9px] sm:text-[10px] font-bold uppercase tracking-wide text-center"
                                          :class="label({{ $n }})">
                                        {{ $n === 1 ? 'Laporan' : ($n === 2 ? 'Bukti' : 'Kirim') }}
                                    </span>
                                </div>

                                @if ($n < 3)
                                    <div class="flex-1 mx-1 sm:mx-3 mt-5 sm:mt-6 h-1.5 overflow-hidden rounded-full bg-slate-300">
                                        <div class="h-full rounded-full bg-gradient-to-r from-[#6C4AB6] to-[#BBA8E2] transition-all duration-500"
                                             :style="`width: ${step >= {{ $n + 1 }} ? 100 : 0}%`"></div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    {{-- FORM --}}
                    <form x-ref="form" x-show="!sent" action="{{ route('public.sarpras.pengaduan.store') }}"
                          method="POST" enctype="multipart/form-data" class="mt-6 sm:mt-8" @submit.prevent="submit()">
                        @csrf

                        {{-- STEP 1 --}}
                        <div x-show="step === 1" class="space-y-4">
                            <div>
                                <label class="mb-1.5 block text-[11px] font-semibold text-[#5B3BA5]">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="reporter_name" x-model="form.nama" autocomplete="name"
                                       class="h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm outline-none transition focus:border-[#6C4AB6] focus:ring-2 focus:ring-violet-200"
                                       :class="errors.nama ? 'border-red-400' : 'border-slate-400'">
                                <p x-show="errors.nama" x-text="errors.nama" class="mt-1 text-[11px] text-red-600"></p>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[11px] font-semibold text-[#5B3BA5]">
                                    Ruangan <span class="text-red-500">*</span>
                                </label>
                                <select name="room_id" x-model="form.room_id" @change="form.item_room_id = ''"
                                        class="h-[42px] w-full rounded-[10px] border bg-white px-3 text-sm outline-none transition focus:border-[#6C4AB6] focus:ring-2 focus:ring-violet-200"
                                        :class="errors.room_id ? 'border-red-400' : 'border-slate-400'">
                                    <option value="">— Pilih Ruangan —</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }} ({{ $room->code }})</option>
                                    @endforeach
                                </select>
                                <p x-show="errors.room_id" x-text="errors.room_id" class="mt-1 text-[11px] text-red-600"></p>
                            </div>

                            <div x-show="form.room_id" x-cloak>
                                <label class="mb-1.5 block text-[11px] font-semibold text-[#5B3BA5]">
                                    Objek Kerusakan (opsional)
                                </label>
                                <select name="item_room_id" x-model="form.item_room_id"
                                        class="h-[42px] w-full rounded-[10px] border border-slate-400 bg-white px-3 text-sm outline-none transition focus:border-[#6C4AB6] focus:ring-2 focus:ring-violet-200">
                                    <option value="">— Ruangan secara umum —</option>
                                    <template x-for="item in filteredItems" :key="item.id">
                                        <option :value="item.id" x-text="item.name + (item.quantity > 1 ? ' (' + item.quantity + ' unit)' : '')"></option>
                                    </template>
                                </select>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[11px] font-semibold text-[#5B3BA5]">
                                    Deskripsi Masalah <span class="text-red-500">*</span>
                                </label>
                                <textarea name="description" x-model="form.deskripsi" rows="4"
                                          class="block w-full resize-none rounded-[10px] border bg-white px-3 py-2 text-sm outline-none transition focus:border-[#6C4AB6] focus:ring-2 focus:ring-violet-200"
                                          :class="errors.deskripsi ? 'border-red-400' : 'border-slate-400'"></textarea>
                                <p x-show="errors.deskripsi" x-text="errors.deskripsi" class="mt-1 text-[11px] text-red-600"></p>
                            </div>
                        </div>

                        {{-- STEP 2 --}}
                        <div x-show="step === 2" x-cloak>
                            <input type="file" name="evidence_photo" x-ref="foto" accept="image/jpeg,image/png"
                                class="hidden" @change="onSelect($event)">

                            <div role="button" tabindex="0" @click="pickFile()"
                                @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false"
                                @drop.prevent="onDrop($event)"
                                class="flex h-[260px] sm:h-[320px] cursor-pointer flex-col items-center justify-center
                                    overflow-hidden rounded-2xl border-2 border-dashed px-4 py-6 text-center transition-all"
                                :class="dragging
                                    ? 'border-primary-500 bg-primary-50 scale-[1.01]'
                                    : 'border-slate-300 bg-white hover:border-primary-400 hover:bg-primary-50/30'">

                                {{-- Belum ada foto --}}
                                <template x-if="!preview">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-primary-50
                                                    flex items-center justify-center mb-4">
                                            <i data-lucide="cloud-upload" class="w-9 h-9 sm:w-11 sm:h-11 text-primary-600"></i>
                                        </div>
                                        <p class="text-sm sm:text-base font-semibold text-surface-800">
                                            Seret foto ke sini
                                        </p>
                                        <p class="text-xs sm:text-sm text-surface-500 mt-1">
                                            atau <span class="text-primary-600 font-medium">klik untuk pilih</span>
                                        </p>
                                        <div class="mt-4 flex items-center gap-2 text-[11px] text-surface-400">
                                            <span class="px-2 py-0.5 rounded bg-surface-100">JPG</span>
                                            <span class="px-2 py-0.5 rounded bg-surface-100">PNG</span>
                                            <span>· Maks 5MB</span>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="preview">
                                    <div class="flex w-full flex-col items-center">
                                        {{-- Container dibatasi tinggi viewport agar tidak keluar layar di HP --}}
                                        <div class="relative flex w-full justify-center items-center overflow-hidden rounded-xl bg-surface-50 border border-surface-200 p-3"
                                            style="height: 45vh; max-height: 300px; min-height: 160px;">
                                            <img :src="preview" alt="Preview"
                                                class="block rounded-lg max-w-full max-h-full object-contain">
                                        </div>
                                        <p class="mt-3 text-xs sm:text-sm font-medium text-surface-700 truncate max-w-xs"
                                        x-text="file?.name"></p>
                                        <div class="mt-3 flex items-center gap-3 text-xs">
                                            <button type="button" @click.stop="pickFile()"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                                        bg-primary-50 hover:bg-primary-100 text-primary-700 font-medium transition">
                                                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                                Ganti
                                            </button>
                                            <button type="button" @click.stop="removeFile()"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg
                                                        bg-red-50 hover:bg-red-100 text-red-600 font-medium transition">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            {{-- Error foto --}}
                            <p x-show="errors.foto" x-text="errors.foto" class="mt-2 text-xs text-red-600"></p>
                        </div>

                        {{-- STEP 3 — RINGKASAN + PREVIEW GAMBAR --}}
                        <div x-show="step === 3" x-cloak class="min-h-[300px] overflow-hidden rounded-2xl border border-slate-300">
                            <div class="flex items-center gap-3 bg-gradient-to-r from-[#6C4AB6] to-[#6F4EBA] px-4 py-3 text-white sm:px-5">
                                <i data-lucide="clipboard-check" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                                <span class="text-sm sm:text-base font-semibold">Ringkasan Laporan</span>
                            </div>

                            <div class="space-y-4 p-4 sm:space-y-5 sm:p-6">

                            <template x-if="preview">
                                <div>
                                    <p class="text-[11px] font-semibold uppercase text-slate-400 mb-2">Foto Bukti</p>
                                    <div class="flex justify-center rounded-xl border border-slate-200 bg-slate-50 p-4">
                                        <img :src="preview" alt="Preview"
                                            class="block rounded-lg shadow-sm"
                                            style="max-width: 200px; max-height: 260px; width: auto; height: auto; object-fit: contain;">
                                    </div>
                                </div>
                            </template>

                                {{-- Data --}}
                                <dl class="space-y-3">
                                    <div>
                                        <dt class="text-[11px] font-semibold uppercase text-slate-400">Nama</dt>
                                        <dd class="mt-0.5 text-sm font-bold text-black" x-text="form.nama"></dd>
                                    </div>
                                    <div>
                                        <dt class="text-[11px] font-semibold uppercase text-slate-400">Ruangan</dt>
                                        <dd class="mt-0.5 text-sm font-bold text-black" x-text="roomName"></dd>
                                    </div>
                                    <template x-if="itemRoomName">
                                        <div>
                                            <dt class="text-[11px] font-semibold uppercase text-slate-400">Objek</dt>
                                            <dd class="mt-0.5 text-sm font-bold text-black" x-text="itemRoomName"></dd>
                                        </div>
                                    </template>
                                    <div>
                                        <dt class="text-[11px] font-semibold uppercase text-slate-400">Deskripsi</dt>
                                        <dd class="mt-0.5 whitespace-pre-line text-sm font-bold text-black" x-text="form.deskripsi"></dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        {{-- NAVIGASI --}}
                        <div class="mt-6 flex items-center gap-3">
                            {{-- Tombol Kembali --}}
                            <template x-if="step > 1">
                                <button type="button" @click="back()" :disabled="submitting"
                                        class="inline-flex h-12 shrink-0 items-center justify-center gap-2 rounded-xl
                                            border-2 border-surface-200 bg-white px-4 sm:px-5
                                            text-sm font-semibold text-surface-600
                                            transition-all hover:bg-surface-50 hover:border-surface-300
                                            active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                                    <span class="hidden sm:inline">Kembali</span>
                                </button>
                            </template>

                            {{-- Tombol Lanjut (step 1) --}}
                            <template x-if="step === 1">
                                <button type="button" @click="next()"
                                        class="inline-flex h-12 flex-1 items-center justify-center gap-2 rounded-xl
                                            bg-primary-600 hover:bg-primary-700
                                            text-sm font-semibold text-white shadow-md hover:shadow-lg
                                            transition-all active:scale-[0.98]">
                                    <span>Lanjut ke Bukti Foto</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </button>
                            </template>

                            {{-- Tombol Pratinjau (step 2) --}}
                            <template x-if="step === 2">
                                <button type="button" @click="next()"
                                        class="inline-flex h-12 flex-1 items-center justify-center gap-2 rounded-xl
                                            bg-primary-600 hover:bg-primary-700
                                            text-sm font-semibold text-white shadow-md hover:shadow-lg
                                            transition-all active:scale-[0.98]">
                                    <span>Pratinjau Laporan</span>
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </button>
                            </template>

                            {{-- Tombol Kirim (step 3) --}}
                            <template x-if="step === 3">
                                <button type="submit" :disabled="submitting"
                                        class="inline-flex h-12 flex-1 items-center justify-center gap-2 rounded-xl
                                            bg-primary-600 hover:bg-primary-700
                                            text-sm font-semibold text-white shadow-md hover:shadow-lg
                                            transition-all active:scale-[0.98]
                                            disabled:opacity-70 disabled:cursor-not-allowed">
                                    <template x-if="!submitting">
                                        <span class="inline-flex items-center gap-2">
                                            <i data-lucide="send" class="w-4 h-4"></i>
                                            <span>Kirim Laporan Aduan</span>
                                        </span>
                                    </template>
                                    <template x-if="submitting">
                                        <span class="inline-flex items-center gap-2">
                                            <span class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                            <span>Mengirim...</span>
                                        </span>
                                    </template>
                                </button>
                            </template>
                        </div>
                    </form>

                    {{-- SUKSES --}}
                    <div x-show="sent" x-cloak class="flex flex-col items-center py-10 text-center">
                        <span class="flex h-20 w-20 items-center justify-center rounded-full bg-emerald-100 text-emerald-600">
                            <i data-lucide="check-circle-2" class="w-10 h-10"></i>
                        </span>
                        <h3 class="mt-5 text-lg font-semibold text-slate-800">Laporan terkirim</h3>
                        <p class="mt-1 max-w-xs text-sm text-slate-500">Terima kasih. Tim Sarpras akan meninjau laporan Anda.</p>
                        <button type="button" @click="closeAndReset()"
                                class="mt-7 inline-flex h-[44px] items-center gap-2 rounded-lg bg-gradient-to-b from-[#8E71D2] to-[#6042B0] px-8 text-sm font-semibold text-white shadow-md transition hover:brightness-110">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            Selesai
                        </button>
                    </div>

                </div>
            </div>
        </div>

    </main>

    {{-- Data rooms untuk Alpine — di-pass dari controller --}}
    <script>
        window.pengaduanRoomsData = @json($roomsData);
    </script>

@endsection