@php
    // Deteksi apakah halaman sekarang adalah create/edit
    $hideTutorial = request()->is('alarms/create')
                    || request()->is('alarms/*/edit')
                    || request()->is('pdf/create')
                    || request()->is('pdf/*/edit')
                    || request()->is('machine-types/create')
                    || request()->is('machine-types/*/edit');
@endphp

<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            <!-- 🔵 Logo + Title -->
            <div class="flex items-center">
                <a href="{{ route('alarms.index') }}"
                    class="text-lg font-bold text-gray-700 hover:text-blue-600 transition">
                    MARIN ALARMS
                </a>

                <!-- 🟢 Desktop Navigation -->
                <div class="hidden sm:flex sm:space-x-8 sm:ms-10">

                    <!-- ⬅️ List PDF sekarang di depan -->
                    <a href="{{ route('pdf.index') }}"
                        class="text-gray-700 hover:text-blue-600 font-medium text-sm transition">
                        List PDF
                    </a>

                    @can('isAdmin')
                        <a href="{{ route('machine-types.index') }}"
                            class="text-gray-700 hover:text-blue-600 font-medium text-sm transition">
                            Tambah Kategori
                        </a>
                    @endcan

                    <!-- ⬅️ Show Tutorial hanya tampil jika bukan create/edit -->
                    @unless($hideTutorial)
                        <button onclick="startTutorial()"
                            class="text-gray-700 hover:text-blue-600 font-medium text-sm transition">
                            {{ __('Show Tutorial') }}
                        </button>
                    @endunless

                </div>
            </div>

            <!-- 🟠 Desktop User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 relative">

                @can('isAdmin')
                    @isset($visitorCount)
                        <div class="flex items-center text-sm text-gray-700 bg-gray-100 px-3 py-1 rounded-lg shadow-sm mr-3">
                            👁️ <span class="ml-1 font-semibold">{{ $visitorCount }}</span>
                        </div>
                    @endisset
                @endcan

                <button id="desktop-dropdown-btn"
                    class="inline-flex items-center px-3 py-2 border text-sm rounded-md text-gray-600 bg-white hover:text-gray-800">
                    <div>{{ Auth::check() ? Auth::user()->name : 'Guest' }}</div>
                    <svg class="ms-1 h-4 w-4" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" d="M6 8l4 4 4-4" />
                    </svg>
                </button>

                <div id="desktop-dropdown-menu"
                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden z-50">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- 🟡 Mobile Hamburger -->
            <div class="flex items-center sm:hidden">
                <button id="hamburger-btn"
                    class="p-2 rounded-md text-gray-600 hover:bg-gray-100">
                    <svg class="h-6 w-6" fill="none">
                        <path id="hamburger-icon" class="inline-flex" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path id="close-icon" class="hidden" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- 🔻 Mobile Menu -->
    <div id="mobile-menu" class="hidden sm:hidden bg-white border-t border-gray-200">

        <div class="pt-2 pb-3">

            <!-- List PDF dulu -->
            <a href="{{ route('pdf.index') }}"
                class="block px-4 py-2 text-gray-700 font-medium text-sm hover:text-blue-600">
                List PDF
            </a>

            @can('isAdmin')
                <a href="{{ route('machine-types.index') }}"
                    class="block px-4 py-2 text-gray-700 font-medium text-sm hover:text-blue-600">
                    Tambah Kategori
                </a>
            @endcan

            <!-- Show Tutorial kecuali create/edit -->
            @unless($hideTutorial)
                <button onclick="startTutorial()"
                    class="block px-4 py-2 text-gray-700 font-medium text-sm hover:text-blue-600">
                    {{ __('Show Tutorial') }}
                </button>
            @endunless

        </div>

        <div class="border-t border-gray-200 bg-gray-50">
            <div class="px-4 py-3">
                <button id="mobile-dropdown-btn"
                    class="font-medium text-base text-gray-800 w-full text-left">
                    {{ Auth::check() ? Auth::user()->name : 'Guest' }}
                </button>
            </div>

            <div id="mobile-dropdown-menu" class="hidden border-t border-gray-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="block w-full text-left px-4 py-2 text-red-600 font-semibold hover:bg-red-50">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- ⚙️ JS Toggle Menu -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    const hamburgerBtn = document.getElementById('hamburger-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const hamburgerIcon = document.getElementById('hamburger-icon');
    const closeIcon = document.getElementById('close-icon');

    const mobileDropdownBtn = document.getElementById('mobile-dropdown-btn');
    const mobileDropdownMenu = document.getElementById('mobile-dropdown-menu');

    const desktopDropdownBtn = document.getElementById('desktop-dropdown-btn');
    const desktopDropdownMenu = document.getElementById('desktop-dropdown-menu');

    // Toggle mobile menu
    hamburgerBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
        hamburgerIcon.classList.toggle('hidden');
        closeIcon.classList.toggle('hidden');
    });

    // Toggle mobile dropdown
    mobileDropdownBtn.addEventListener('click', () => {
        mobileDropdownMenu.classList.toggle('hidden');
    });

    // Toggle desktop dropdown
    desktopDropdownBtn.addEventListener('click', () => {
        desktopDropdownMenu.classList.toggle('hidden');
    });

    // Klik luar dropdown menutup
    document.addEventListener('click', function(e) {
        if (!desktopDropdownBtn.contains(e.target) && !desktopDropdownMenu.contains(e.target)) {
            desktopDropdownMenu.classList.add('hidden');
        }
    });
});
</script>
