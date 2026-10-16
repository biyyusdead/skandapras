<footer class="relative mt-16 text-white">

    <svg class="block w-full h-[60px] text-primary"
         viewBox="0 0 1440 60"
         preserveAspectRatio="none"
         fill="currentColor"
         aria-hidden="true">
        <path d="M60,0
                 H1380
                 A60,60 0 0 1 1440,60
                 V60
                 H0
                 V60
                 A60,60 0 0 1 60,0
                 Z" />
    </svg>

    {{-- BODY FOOTER (solid purple, no rounded, no gap) --}}
    <div class="relative bg-primary px-4 pb-4 sm:px-8 -mt-px overflow-hidden">

        {{-- Blob ambient (opsional, dekorasi cahaya) --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
            <div class="absolute -bottom-40 -right-40 w-[800px] h-[800px] rounded-full
                        bg-gradient-to-tl from-accent-amber/20 via-primary-100/15 to-transparent blur-3xl"></div>
        </div>

        <!-- CONTENT UTAMA -->
        <div class="relative z-10 mx-auto flex max-w-6xl flex-col items-center justify-between gap-8 p-2 sm:p-4 lg:flex-row lg:gap-10">

            <!-- KIRI -->
            <div class="w-full lg:w-1/2">

                <!-- LOGO -->
                <img src="{{ asset('assets/images/logo/footer.png') }}" alt="Logo SMK Bisa Hebat"
                    class="mb-4 h-auto object-contain" style="width: 50%; max-width: 256px; min-width: 160px;">

                <!-- DESKRIPSI -->
                <p class="max-w-md text-sm leading-relaxed text-white/90 sm:text-[15px]">
                    SMK Negeri 2 Karanganyar adalah salah satu Sekolah Menengah
                    Kejuruan favorit di Kabupaten Karanganyar. Serta merupakan sekolah
                    yang berorientasi pada karakter, berwawasan, disiplin, tanggung jawab,
                    dan berdaya saing.
                </p>

                {{-- KONTAK --}}
                <div class="mt-4 space-y-2 text-xs text-white/95 sm:text-[14px]">
                    <div class="space-y-2 font-medium">
                        <p class="flex items-start gap-2">
                            <i data-lucide="map-pin" class="w-4 h-4 shrink-0 mt-0.5 text-accent-amber"></i>
                            <span>Jl. Yos Sudarso, Karanganyar</span>
                        </p>

                        <p class="flex items-start gap-2">
                            <i data-lucide="mail" class="w-4 h-4 shrink-0 mt-0.5 text-accent-amber"></i>
                            <span class="break-words">smkn2kra@yahoo.co.id</span>
                        </p>

                        <p class="flex items-start gap-2">
                            <i data-lucide="phone" class="w-4 h-4 shrink-0 mt-0.5 text-accent-amber"></i>
                            <span>0271-4950xxx</span>
                        </p>

                        <p class="flex items-start gap-2">
                            <i data-lucide="clock" class="w-4 h-4 shrink-0 mt-0.5 text-accent-amber"></i>
                            <span>Jam Kerja: Senin - Jumat, 07:00 - 15:00 WIB</span>
                        </p>
                    </div>
                </div>

            </div>


            <!-- MAP -->
            <div class="h-64 w-full overflow-hidden rounded-lg sm:h-80 lg:w-1/2">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3954.859730598725!2d110.94815151415202!3d-7.590242277103364!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e654b99ab219bfd%3A0x4e63f4d5cebe448a!2sSMKN%202%20Karanganyar!5e0!3m2!1sen!2sid!4v1618149814210!5m2!1sen!2sid"
                    width="600" height="450" style="border:0" title="School Map" loading="lazy"
                    class="h-full w-full rounded-lg">
                </iframe>
            </div>

        </div>


        <!-- GARIS -->
        <div class="relative z-10 mx-auto mt-5 h-px max-w-7xl bg-white/20"></div>

        <!-- LOGO PERLOMBAAN -->
        <div class="relative z-10 mx-auto mt-5 flex max-w-4xl flex-wrap items-center justify-center gap-5 rounded-xl bg-white px-5 py-3 sm:gap-8">
            <img src="{{ asset('assets/images/jhic/logoJHIC.png') }}" alt="Logo 1"
                class="h-9 w-auto object-contain sm:h-11">

            <img src="{{ asset('assets/images/jhic/logoJagoanHosting.png') }}" alt="Logo 2"
                class="h-9 w-auto object-contain sm:h-11">

            <img src="{{ asset('assets/images/jhic/logoKomdigi.png') }}" alt="Logo 3"
                class="h-9 w-auto object-contain sm:h-11">

            <img src="{{ asset('assets/images/jhic/logoGarudaSpark.png') }}" alt="Logo 4"
                class="h-9 w-auto object-contain sm:h-11">

            <img src="{{ asset('assets/images/jhic/logoNgalup.png') }}" alt="Logo 5"
                class="h-9 w-auto object-contain sm:h-11">
        </div>


        <!-- SOCIAL MEDIA -->
        <div class="relative z-10 mt-5 flex flex-wrap justify-center gap-5">

            <a href="https://www.linkedin.com/school/smkn2karanganyar"
                aria-label="LinkedIn SMKN 2 Karanganyar"
                class="text-white/80 transition hover:-translate-y-1 hover:text-accent-amber p-2 rounded-full hover:bg-white/10"
                target="_blank" rel="noopener">
                <i data-lucide="linkedin" class="w-5 h-5"></i>
            </a>

            <a href="https://www.instagram.com/smkn2kra"
                aria-label="Instagram SMKN 2 Karanganyar"
                class="text-white/80 transition hover:-translate-y-1 hover:text-accent-amber p-2 rounded-full hover:bg-white/10"
                target="_blank" rel="noopener">
                <i data-lucide="instagram" class="w-5 h-5"></i>
            </a>

            <a href="https://www.youtube.com/@smkn2kra"
                aria-label="YouTube SMKN 2 Karanganyar"
                class="text-white/80 transition hover:-translate-y-1 hover:text-accent-amber p-2 rounded-full hover:bg-white/10"
                target="_blank" rel="noopener">
                <i data-lucide="youtube" class="w-5 h-5"></i>
            </a>

            <a href="https://www.tiktok.com/@smkn2kra"
                aria-label="TikTok SMKN 2 Karanganyar"
                class="text-white/80 transition hover:-translate-y-1 hover:text-accent-amber p-2 rounded-full hover:bg-white/10 inline-flex items-center justify-center"
                target="_blank" rel="noopener">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.24 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/></svg>
            </a>

            <a href="https://www.facebook.com/smkn2kra"
                aria-label="Facebook SMKN 2 Karanganyar"
                class="text-white/80 transition hover:-translate-y-1 hover:text-accent-amber p-2 rounded-full hover:bg-white/10"
                target="_blank" rel="noopener">
                <i data-lucide="facebook" class="w-5 h-5"></i>
            </a>

        </div>


        <!-- COPYRIGHT -->
        <p class="relative z-10 mt-2 px-2 text-center text-sm text-white/90 sm:text-base lg:text-[20px]">
            Copyright © 2026 <b>SMK Negeri 2 Karanganyar.</b> All Right Reserved.
        </p>


        <!-- DEVELOPED BY -->
        <div class="relative z-20 mt-1 flex justify-center">
            <div class="flex flex-col items-center gap-1 text-center text-white">
                <p class="text-xs sm:text-sm">
                    <span class="font-medium">
                        Developed By
                    </span>

                    <button
                        type="button"
                        id="btn-fivestack"
                        data-modal-target="select-modal"
                        data-modal-toggle="select-modal"
                        data-tooltip-target="tooltip-animation"
                        data-tooltip-placement="top"
                        class="cursor-pointer text-sm font-bold text-white underline transition hover:text-purple-200"
                    >
                        FIVESTACK-SKANDAKRA
                    </button>
                </p>
            </div>
        </div>

        <div
            id="tooltip-animation"
            role="tooltip"
            class="invisible absolute z-[100] inline-block rounded-lg bg-white px-3 py-2 text-xs font-medium text-gray-800 opacity-0 shadow-lg transition-opacity duration-300"
        >
            <span class="font-bold text-primary">
                Klik Untuk Melihat Detail
            </span>

            <div class="tooltip-arrow" data-popper-arrow></div>
        </div>


        {{-- ORNAMEN --}}
        <div class="pointer-events-none absolute inset-0 z-0 overflow-hidden">

            {{-- KIRI BAWAH --}}
            <div class="absolute -bottom-20 -left-20 h-40 w-40 rounded-full bg-primary-light
                        sm:-bottom-24 sm:-left-24 sm:h-52 sm:w-52
                        md:-bottom-28 md:-left-28 md:h-64 md:w-64
                        lg:-bottom-32 lg:-left-32 lg:h-72 lg:w-72">
            </div>

            <div class="absolute bottom-2 left-20 h-20 w-20 rounded-full bg-primary-dark
                        sm:bottom-4 sm:left-28 sm:h-24 sm:w-24
                        md:bottom-6 md:left-36 md:h-28 md:w-28
                        lg:bottom-8 lg:left-44 lg:h-32 lg:w-32">
            </div>


            {{-- KANAN BAWAH --}}
            <div class="absolute -right-20 -bottom-24 h-48 w-48 rounded-full bg-primary-light
                        sm:-right-24 sm:-bottom-28 sm:h-60 sm:w-60
                        md:-right-28 md:-bottom-32 md:h-72 md:w-72
                        lg:-right-36 lg:-bottom-40 lg:h-80 lg:w-80">
            </div>

            <div class="absolute right-20 bottom-4 h-24 w-24 rounded-full bg-primary-dark
                        sm:right-28 sm:bottom-6 sm:h-28 sm:w-28
                        md:right-36 md:bottom-8 md:h-32 md:w-32
                        lg:right-48 lg:bottom-10 lg:h-36 lg:w-36">
            </div>

        </div>

    </div>
    {{-- END BODY FOOTER --}}

</footer>


<!-- MAIN MODAL -->
<div id="select-modal" tabindex="-1" aria-hidden="true"
    class="fixed inset-0 z-50 hidden h-full w-full items-center justify-center overflow-y-auto overflow-x-hidden p-3 sm:p-4">

    <div class="relative my-auto w-full max-w-3xl">

        <!-- MODAL CONTENT -->
        <div class="relative max-h-[90vh] overflow-y-auto overflow-x-hidden rounded-2xl bg-white shadow-2xl">

            <!-- HEADER -->
            <div class="flex items-center justify-between border-b border-gray-100 px-4 py-4 sm:px-6 sm:py-5">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 sm:text-xl">
                        FIVESTACK
                    </h3>
                </div>

                <button type="button" data-modal-hide="select-modal"
                    class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-gray-400 transition hover:bg-gray-100 hover:text-gray-900">
                    <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>


            <!-- MODAL BODY -->
            <div class="px-4 pb-8 pt-6 sm:px-6 sm:pb-9 sm:pt-8">

                <div class="mb-8 text-center">
                    <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">
                        Meet Our
                        <span class="text-[#6C4AB6]">Team</span>
                    </h2>

                    <p class="mx-auto mt-2 max-w-md text-xs text-gray-500 sm:text-sm">
                        Kenali anggota tim FIVESTACK yang berkontribusi
                        dalam pengembangan website ini.
                    </p>

                    <div class="mx-auto mt-4 h-1 w-12 rounded-full bg-[#6C4AB6]"></div>
                </div>


                <!-- 3 ORANG ATAS -->
                <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 sm:gap-8">

                    <div class="flex flex-col items-center text-center">
                        <div class="h-24 w-24 overflow-hidden rounded-full bg-[#F1ECF9] ring-4 ring-[#E9DFF7] sm:h-28 sm:w-28">
                            <img src="{{ asset('assets/images/teams/abiyu.png') }}" alt="Abiyu" loading="lazy"
                                class="h-full w-full object-cover">
                        </div>
                        <h4 class="mt-4 text-sm font-bold text-gray-900 sm:text-base">Abiyu</h4>
                        <p class="mt-1 text-xs font-semibold text-[#6C4AB6] sm:text-sm">Backend Developer</p>
                        <a href="#" class="mt-2 text-xs text-gray-400 transition hover:text-[#6C4AB6] sm:text-sm">@abiyu</a>
                    </div>

                    <div class="flex flex-col items-center text-center">
                        <div class="h-24 w-24 overflow-hidden rounded-full bg-[#F1ECF9] ring-4 ring-[#E9DFF7] sm:h-28 sm:w-28">
                            <img src="{{ asset('assets/images/teams/lintang.png') }}" alt="Lintang"
                                class="h-full w-full object-cover">
                        </div>
                        <h4 class="mt-4 text-sm font-bold text-gray-900 sm:text-base">Lintang</h4>
                        <p class="mt-1 text-xs font-semibold text-[#6C4AB6] sm:text-sm">Frontend Developer</p>
                        <a href="#" class="mt-2 text-xs text-gray-400 transition hover:text-[#6C4AB6] sm:text-sm">@lintang</a>
                    </div>

                    <div class="col-span-2 flex flex-col items-center text-center sm:col-span-1">
                        <div class="h-24 w-24 overflow-hidden rounded-full bg-[#F1ECF9] ring-4 ring-[#E9DFF7] sm:h-28 sm:w-28">
                            <img src="{{ asset('assets/images/teams/rafi.jpeg') }}" alt="Rafi" loading="lazy"
                                class="h-full w-full object-cover">
                        </div>
                        <h4 class="mt-4 text-sm font-bold text-gray-900 sm:text-base">Rafi</h4>
                        <p class="mt-1 text-xs font-semibold text-[#6C4AB6] sm:text-sm">Backend Developer</p>
                        <a href="#" class="mt-2 text-xs text-gray-400 transition hover:text-[#6C4AB6] sm:text-sm">@rafi</a>
                    </div>

                </div>


                <!-- 2 ORANG BAWAH -->
                <div class="mt-8 flex flex-row items-center justify-center gap-5 sm:gap-8">

                    <div class="flex w-1/2 flex-col items-center text-center sm:w-1/3">
                        <div class="h-24 w-24 overflow-hidden rounded-full bg-[#F1ECF9] ring-4 ring-[#E9DFF7] sm:h-28 sm:w-28">
                            <img src="{{ asset('assets/images/teams/jekky.png') }}" alt="Jekky" loading="lazy"
                                class="h-full w-full object-cover">
                        </div>
                        <h4 class="mt-4 text-sm font-bold text-gray-900 sm:text-base">Jekky</h4>
                        <p class="mt-1 text-xs font-semibold text-[#6C4AB6] sm:text-sm">Web Designer</p>
                        <a href="#" class="mt-2 text-xs text-gray-400 transition hover:text-[#6C4AB6] sm:text-sm">@jekky</a>
                    </div>

                    <div class="flex w-1/2 flex-col items-center text-center sm:w-1/3">
                        <div class="h-24 w-24 overflow-hidden rounded-full bg-[#F1ECF9] ring-4 ring-[#E9DFF7] sm:h-28 sm:w-28">
                            <img src="{{ asset('assets/images/teams/affan1.jpg') }}" alt="Affan"
                                loading="lazy" class="h-full w-full object-cover">
                        </div>
                        <h4 class="mt-4 text-sm font-bold text-gray-900 sm:text-base">Affan</h4>
                        <p class="mt-1 text-xs font-semibold text-[#6C4AB6] sm:text-sm">Frontend Developer</p>
                        <a href="#" class="mt-2 text-xs text-gray-400 transition hover:text-[#6C4AB6] sm:text-sm">@affan</a>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>