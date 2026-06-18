<!DOCTYPE html>
<html lang="id" x-data="{ loading: true }" x-init="
    window.addEventListener('load', () => {
        setTimeout(() => loading = false, 600)
    })
" x-cloak>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GARDENA-AI | @yield('title', 'Dashboard')</title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }
        h1,h2,h3,h4,h5,h6,.font-brand { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

    {{-- ════════════════ LOADING SCREEN ════════════════ --}}
    <div x-show="loading"
        x-transition:leave="transition-opacity duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[9999] bg-white flex flex-col items-center justify-center gap-6">

        <img src="{{ asset('images/logo.png') }}" alt="GARDENA-AI" class="h-40 animate-pulse">

        {{-- Progress bar --}}
        <div class="w-56 h-1.5 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-green-500 rounded-full animate-[loading_0.9s_ease_forwards]"></div>
        </div>

        <p class="text-sm text-gray-400 font-medium">Memuat data tanaman Anda...</p>
    </div>

    <style>
        @keyframes loading {
            from { width: 0% }
            to   { width: 100% }
        }
    </style>

    {{-- ════════════════ NAVBAR ════════════════ --}}
    <x-navbar />

    {{-- ════════════════ FLASH ALERTS ════════════════ --}}
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 text-sm font-medium px-8 py-3 flex items-center gap-2">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-amber-50 border-l-4 border-amber-400 text-amber-700 text-sm font-medium px-8 py-3 flex items-center gap-2">
            <i class="bi bi-exclamation-circle-fill"></i> {{ session('warning') }}
        </div>
    @endif

    {{-- ════════════════ CONTENT ════════════════ --}}
    <main class="flex-1 w-full px-10 py-7">
        @yield('content')
    </main>

    {{-- ════════════════ FOOTER ════════════════ --}}
    <x-footer />

    @stack('scripts')
</body>
</html>