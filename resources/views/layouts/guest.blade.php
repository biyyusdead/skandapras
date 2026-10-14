<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>

<div class="shrink-0 px-3 pt-3 pb-2">
            <div class="bg-primary-800 rounded-2xl px-4 py-4 shadow-sm">
                <div class="flex items-center gap-3">
                    
                    {{-- Logo --}}
                    <div class="w-11 h-11 flex items-center justify-center shrink-0 overflow-hidden">
                        @php
                            $logo = \App\Models\SchoolProfile::current()?->logo;
                        @endphp

                        @if ($logo)
                            <img
                                src="{{ asset('storage/' . $logo) }}"
                                alt="Logo"
                                class="w-full h-full object-contain"
                            >
                        @else
                            <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
                        @endif
                    </div>

                    {{-- Nama sekolah --}}
                    <div x-show="!sidebarCollapsed" class="min-w-0">
                        <p class="text-sm font-bold leading-tight text-white">
                            SMK NEGERI 2
                        </p>

                        <p class="text-sm font-bold leading-tight text-white">
                            KARANGANYAR
                        </p>
                    </div>

                </div>
            </div>
        </div>