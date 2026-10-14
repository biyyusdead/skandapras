<nav class="bg-primary sticky relative w-full z-20 top-0 start-0 border-default">
    <div class="max-w-6xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="{{ route('home') }}" class="flex items-center space-x-3 rtl:space-x-reverse">
            <img src="{{ asset('assets/images/logo/logo.png') }}" class="h-11" alt="Flowbite Logo" />
            <span class="self-center text-sm font-bold text-white pl-4">SMK NEGERI 2 <br> KARANGANYAR</span>
        </a>

        <button data-collapse-toggle="navbar-dropdown" type="button"
            class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-body rounded-base md:hidden hover:bg-primary-light hover:text-heading focus:outline-none focus:ring-2 focus:ring-neutral-tertiary"
            aria-controls="navbar-dropdown" aria-expanded="false">
            <span class="sr-only">Open main menu</span>
            <svg class="w-6 h-6 text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M5 7h14M5 12h14M5 17h14" />
            </svg>
        </button>

        <div class="hidden absolute top-full left-0 w-full bg-primary shadow-lg md:static md:block md:w-auto md:bg-transparent md:shadow-none" id="navbar-dropdown">
            <ul class="flex flex-col font-medium p-4 mt-4 md:p-0 gap-2 md:flex-row md:mt-0 md:border-0 md:bg-transparent">
                
                <!-- Dropdown Tentang -->
                <li>
                    <button id="dropdownNavTentang" data-dropdown-toggle="dropdownTentang" class="w-full py-2 px-3 font-medium text-white rounded-4xl hover:bg-primary-light transition-colors duration-200 aria-expanded:bg-primary-light" style="cursor: pointer;">
                        <div class="px-2 flex items-center justify-between">
                            Tentang
                            <svg class="w-4 h-4 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </div>
                    </button>
                    <!-- Dropdown menu -->
                    <div id="dropdownTentang" class="z-10 hidden bg-neutral-primary-medium border border-default-medium rounded-base shadow-lg ">
                        <ul class="p-2 text-md text-body font-medium" aria-labelledby="dropdownNavTentang">
                            <li>
                                <a href="{{ route('identitas-sekolah') }}" class="text-black inline-flex items-center w-full p-2 rounded transition-colors">
                                    Identitas Sekolah
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sambutan-kepala-sekolah') }}" class="text-black inline-flex items-center w-full p-2 rounded transition-colors">
                                    Sambutan Kepala Sekolah
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('sejarah') }}" class="text-black inline-flex items-center w-full p-2 rounded transition-colors">
                                    Sejarah Sekolah
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('struktur-organisasi') }}" class="text-black inline-flex items-center w-full p-2 rounded transition-colors">
                                    Struktur Organisasi
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                
                <li>
                    <a href="{{ route('index') }}" class="block py-2 px-3 text-white rounded-4xl hover:bg-primary-light transition-colors duration-200" aria-current="page">
                        <span class="px-2">Sarpras</span>
                    </a>
                </li>


                <!-- Dropdown Akademik -->
                <li>
                    <button id="dropdownNavAkademik" data-dropdown-toggle="dropdownAkademik" class="w-full py-2 px-3 font-medium text-white rounded-4xl hover:bg-primary-light transition-colors duration-200 aria-expanded:bg-primary-light" style="cursor: pointer;">
                        <div class="px-2 flex items-center justify-between">
                            Akademik
                            <svg class="w-4 h-4 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </div>
                    </button>
                    <!-- Dropdown menu -->
                    <div id="dropdownAkademik" class="z-10 hidden bg-neutral-primary-medium border border-default-medium rounded-base shadow-lg ">
                        <ul class="p-2 text-md text-body font-medium" aria-labelledby="dropdownNavAkademik">
                            <li>
                                <a href="{{ route('guru-dan-tenagakependidikan') }}" class="text-black inline-flex items-center w-full p-2 rounded transition-colors">
                                    Guru dan Tenaga Kependidikan
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('kompetensi-keahlian') }}" class="text-black inline-flex items-center w-full p-2 rounded transition-colors">
                                    Kompetensi Keahlian
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('spmb') }}" class="text-black inline-flex items-center w-full p-2 rounded transition-colors">
                                    SPMB
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('produk-unggulan') }}" class="text-black inline-flex items-center w-full p-2 rounded transition-colors">
                                    Produk Unggulan
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>


                <!-- Dropdown Kesiswaan -->
                <li>
                    <button id="dropdownNavKesiswaan" data-dropdown-toggle="dropdownKesiswaan" class="w-full py-2 px-3 font-medium text-white rounded-4xl hover:bg-primary-light transition-colors duration-200 aria-expanded:bg-primary-light" style="cursor: pointer;">
                        <div class="px-2 flex items-center justify-between">
                            Kesiswaan
                            <svg class="w-4 h-4 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                            </svg>
                        </div>
                    </button>
                    <!-- Dropdown menu -->
                    <div id="dropdownKesiswaan" class="z-10 hidden rounded-xl border border-gray-200 bg-white shadow-lg">
                        <ul class="p-2 text-base font-medium text-black" aria-labelledby="dropdownNavKesiswaan">
                            <li>
                                <a href="{{ route('prestasi') }}" class="inline-flex items-center w-full p-2 rounded transition-colors">
                                    Prestasi
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('ekstrakurikuler') }}" class="inline-flex items-center w-full p-2 rounded transition-colors">
                                    Ekstrakurikuler
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('tata-tertib') }}" class="inline-flex items-center w-full p-2 rounded transition-colors">
                                    Tata Tertib
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('bkk') }}" class="inline-flex items-center w-full p-2 rounded transition-colors">
                                    BKK
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('pkl') }}" class="inline-flex items-center w-full p-2 rounded transition-colors">
                                    PKL
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="{{ route('berita') }}" class="block py-2 px-3 text-white rounded-4xl hover:bg-primary-light transition-colors duration-200 aria-expanded:bg-primary-light" aria-current="page">
                        <span class="px-2">Berita</span>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>
