<nav class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <!-- tambahkan logo jika perlu -->
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('alarms.index')" :active="request()->routeIs('alarms.index')">
                        {{ __('Alarms') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Desktop Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 relative">
                <button id="desktop-dropdown-btn"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none">
                    <div>{{ Auth::check() ? Auth::user()->name : 'Guest' }}</div>
                    <svg class="ms-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" d="M6 8l4 4 4-4" />
                    </svg>
                </button>

                <div id="desktop-dropdown-menu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="block w-full text-left px-4 py-2 text-red-600 hover:bg-red-50">
                            {{ __('Log Out') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Hamburger Button (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden">
                <button id="hamburger-btn"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path id="hamburger-icon" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path id="close-icon" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div id="mobile-menu" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-nav-link :href="route('alarms.index')" :active="request()->routeIs('alarms.index')"
                class="block px-4 py-2">
                {{ __('Alarms') }}
            </x-nav-link>
        </div>

        <!-- Mobile Settings -->
        <div class="border-t border-gray-200 bg-gray-50">
            <div class="px-4 py-3">
                <button id="mobile-dropdown-btn" class="font-medium text-base text-gray-800 w-full text-left">
                    {{ Auth::check() ? Auth::user()->name : 'Guest' }}
                </button>
                @if(Auth::check())
                    <div class="font-medium text-sm text-gray-500">
                        {{ Auth::user()->email }}
                    </div>
                @endif
            </div>

            <div id="mobile-dropdown-menu" class="hidden border-t border-gray-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="block w-full text-left px-4 py-2 text-red-600 font-semibold hover:bg-red-50 focus:bg-red-50">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- JavaScript -->
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

    // Toggle mobile logout menu
    mobileDropdownBtn.addEventListener('click', () => {
        mobileDropdownMenu.classList.toggle('hidden');
    });

    // Toggle desktop dropdown
    desktopDropdownBtn.addEventListener('click', () => {
        desktopDropdownMenu.classList.toggle('hidden');
    });

    // Klik di luar menu untuk menutup dropdown desktop
    document.addEventListener('click', function(e) {
        if (!desktopDropdownBtn.contains(e.target) && !desktopDropdownMenu.contains(e.target)) {
            desktopDropdownMenu.classList.add('hidden');
        }
    });
});
</script>