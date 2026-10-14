@extends('layouts.public')

@section('title', $news->title)
@section('description', Str::limit(strip_tags($news->content), 150))

@section('content')

<div class="bg-gradient-to-b from-primary-50/50 via-white to-surface-50">

    {{-- ============================================================ --}}
    {{-- HERO — Background dari foto berita --}}
    {{-- ============================================================ --}}
    <section class="relative text-white overflow-hidden">
        {{-- Background Foto Berita --}}
        <div class="absolute inset-0">
            @if ($news->image_url)
                <div class="absolute inset-0 bg-cover bg-center bg-no-repeat blur-[2px] brightness-[0.55]"
                    style="background-image: url('{{ $news->image_url }}');">
                </div>
            @else
                <div class="absolute inset-0 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-900"></div>
            @endif

            {{-- Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-br from-primary-900/70 via-primary-800/65 to-primary-700/60"></div>
        </div>

        {{-- Decorative blobs --}}
        <div class="absolute -top-32 -right-32 w-96 h-96 bg-primary-400/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-primary-300/10 rounded-full blur-3xl pointer-events-none"></div>

        {{-- Content --}}
        <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-14 md:py-20">

            {{-- Breadcrumb --}}
            <nav class="mb-5 flex items-center gap-2 text-xs md:text-sm text-primary-100/90 animate-fade-in-up drop-shadow">
                <a href="{{ url('/') }}" class="hover:text-white transition-colors flex items-center gap-1.5">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i>
                    Beranda
                </a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-60"></i>
                <a href="{{ route('public.news.index') }}" class="hover:text-white transition-colors">
                    Berita
                </a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 opacity-60"></i>
                <span class="text-white font-medium">Detail</span>
            </nav>

            {{-- Meta --}}
            <div class="flex flex-wrap items-center gap-3 mb-5 animate-fade-in-up stagger-1">
                @if ($news->published_at)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                                bg-white/15 backdrop-blur-sm border border-white/25 text-xs font-medium">
                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                        {{ $news->published_at->translatedFormat('d F Y') }}
                    </span>
                @endif

                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                            bg-white/15 backdrop-blur-sm border border-white/25 text-xs font-medium">
                    <i data-lucide="user" class="w-3.5 h-3.5"></i>
                    {{ $news->author?->name ?? 'Admin' }}
                </span>
            </div>

            {{-- Title --}}
            <h1 class="text-3xl md:text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight
                    animate-fade-in-up stagger-2 drop-shadow-lg">
                {{ $news->title }}
            </h1>
        </div>

        <x-public.wave />
    </section>

    {{-- ============================================================ --}}
    {{-- CONTENT --}}
    {{-- ============================================================ --}}
    <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <article class="bg-white rounded-2xl border border-surface-200/70 shadow-xl shadow-primary-900/5
                        overflow-hidden animate-fade-in-up">

            {{-- Gambar Utama --}}
            @if ($news->image_url)
                <div class="relative w-full aspect-video bg-surface-100 overflow-hidden">
                    <img src="{{ $news->image_url }}"
                         alt="{{ $news->title }}"
                         class="w-full h-full object-cover">
                </div>
            @endif

            {{-- Body --}}
            <div class="p-6 md:p-10 lg:p-12">

                <div class="prose prose-sm md:prose-base max-w-none
                            prose-headings:font-bold prose-headings:text-surface-900
                            prose-p:text-surface-700 prose-p:leading-relaxed
                            prose-a:text-primary-600 prose-a:no-underline hover:prose-a:underline
                            prose-strong:text-surface-900
                            prose-blockquote:border-l-4 prose-blockquote:border-primary-500
                            prose-blockquote:bg-primary-50/50 prose-blockquote:py-2 prose-blockquote:px-5
                            prose-blockquote:not-italic prose-blockquote:text-surface-700
                            prose-img:rounded-xl prose-img:shadow-md
                            prose-ul:text-surface-700 prose-ol:text-surface-700">
                    {!! $news->content !!}
                </div>

                {{-- Share & Kembali --}}
                <div class="mt-12 pt-8 border-t border-surface-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">

                    {{-- Kembali --}}
                    <a href="{{ route('public.news.index') }}"
                       class="inline-flex items-center gap-2 text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        Kembali ke Daftar Berita
                    </a>

                    {{-- Share --}}
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-surface-500">Bagikan:</span>

                        @php
                            $shareUrl = urlencode(url()->current());
                            $shareTitle = urlencode($news->title);
                        @endphp

                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}"
                           target="_blank" rel="noopener"
                           aria-label="Bagikan ke Facebook"
                           class="w-9 h-9 rounded-full bg-blue-50 hover:bg-blue-100 text-blue-600
                                  flex items-center justify-center transition-colors">
                            <i data-lucide="facebook" class="w-4 h-4"></i>
                        </a>

                        <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}"
                           target="_blank" rel="noopener"
                           aria-label="Bagikan ke Twitter"
                           class="w-9 h-9 rounded-full bg-sky-50 hover:bg-sky-100 text-sky-500
                                  flex items-center justify-center transition-colors">
                            <i data-lucide="twitter" class="w-4 h-4"></i>
                        </a>

                        <a href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}"
                           target="_blank" rel="noopener"
                           aria-label="Bagikan ke WhatsApp"
                           class="w-9 h-9 rounded-full bg-green-50 hover:bg-green-100 text-green-600
                                  flex items-center justify-center transition-colors">
                            <i data-lucide="message-circle" class="w-4 h-4"></i>
                        </a>

                        <button type="button"
                                x-data="{ copied: false }"
                                @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 1500)"
                                aria-label="Copy link"
                                class="w-9 h-9 rounded-full bg-surface-100 hover:bg-surface-200 text-surface-600
                                       flex items-center justify-center transition-colors">
                            <span x-show="!copied"><i data-lucide="link" class="w-4 h-4"></i></span>
                            <span x-show="copied" x-cloak class="text-green-600 font-bold text-xs"><i data-lucide="check" class="w-4 h-4"></i></span>
                        </button>
                    </div>
                </div>
            </div>
        </article>

        {{-- ============================================================ --}}
        {{-- BERITA TERKAIT --}}
        {{-- ============================================================ --}}
        @if ($related->isNotEmpty())
            <div class="mt-16">
                <div class="flex items-center gap-3 mb-6">
                    <h2 class="text-xl md:text-2xl font-bold text-surface-900">
                        Berita Lainnya
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach ($related as $item)
                        <a href="{{ route('public.news.show', $item->slug) }}"
                           class="group bg-white rounded-2xl border border-surface-200/70 overflow-hidden
                                  shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300">

                            {{-- Thumbnail --}}
                            <div class="relative aspect-[16/10] bg-surface-100 overflow-hidden">
                                @if ($item->image_url)
                                    <img src="{{ $item->image_url }}"
                                         alt="{{ $item->title }}"
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-primary-50">
                                        <i data-lucide="newspaper" class="w-12 h-12 text-primary-400"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="p-4">
                                @if ($item->published_at)
                                    <span class="inline-flex items-center gap-1.5 text-xs text-surface-400 mb-2">
                                        <i data-lucide="calendar" class="w-3 h-3"></i>
                                        {{ $item->published_at->translatedFormat('d M Y') }}
                                    </span>
                                @endif

                                <h3 class="line-clamp-2 font-bold text-surface-900 text-sm md:text-base
                                           group-hover:text-primary-600 transition-colors leading-snug">
                                    {{ $item->title }}
                                </h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</div>

@endsection