<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Marin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind + App -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Shepherd.js (Tour Guide) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js@11.1.1/dist/css/shepherd.css" />
    <script src="https://cdn.jsdelivr.net/npm/shepherd.js@11.1.1/dist/js/shepherd.min.js"></script>
</head>

<body class="font-sans antialiased bg-gray-100 text-gray-900">
    <div class="min-h-screen flex flex-col">

        {{-- 🔹 Navbar --}}
        @include('layouts.navigation')

        {{-- 🔹 Header (opsional) --}}
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        {{-- 🔹 Konten utama --}}
        <main class="flex-1">
                        @yield('content')
        </main>
    </div>

    {{-- Shepherd sudah diload di atas --}}
    @yield('scripts')
</body>
</html>
