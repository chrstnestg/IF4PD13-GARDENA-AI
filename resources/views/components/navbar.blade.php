<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<nav class="sticky top-0 z-50 bg-white border-b border-gray-200 h-16 flex items-center px-8">

    {{-- Logo --}}
    <a href="{{ route('monitoring') }}" class="flex-shrink-0">
        <img src="{{ asset('images/logo1.png') }}"
             alt="GARDENA-AI"
             class="h-12 w-auto object-contain">
    </a>

    {{-- Nav Links di tengah --}}
    <div class="flex items-center gap-1 flex-1 justify-center">
        @foreach([
            ['monitoring',  'Monitoring'],
            ['rekomendasi', 'Rekomendasi'],
            ['riwayat',     'Riwayat'],
        ] as [$route, $label])
            <a href="{{ route($route) }}"
               class="relative px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-150
                      {{ request()->routeIs($route)
                            ? 'text-green-600'
                            : 'text-gray-500 hover:text-green-600 hover:bg-green-50' }}">
                {{ $label }}
                @if(request()->routeIs($route))
                    <span class="absolute bottom-[-17px] left-4 right-4 h-[2.5px] bg-green-500 rounded-t-full"></span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- User + Dropdown --}}
    @auth
    <div class="relative" x-data="{ open: false }">
        <button @click="open = !open" class="flex items-center gap-3 focus:outline-none">
            <div class="text-right hidden sm:block">
                <p class="text-sm font-semibold text-gray-800 leading-tight">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-xs text-gray-400">Petani</p>
            </div>
            <div class="w-9 h-9 rounded-xl bg-green-500 flex items-center justify-center text-white text-xs font-bold">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
        </button>

        {{-- Dropdown --}}
        <div x-show="open" @click.away="open = false"
            x-transition
            class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 z-50">
            <div class="px-4 py-3 border-b border-gray-100">
                <p class="text-sm font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-400">Petani</p>
            </div>
            <div class="py-1">
                <a href="/pengaturan"
                    class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-600 transition">
                    <i class="fa-solid fa-gear"></i> Pengaturan
                </a>

                {{-- Logout pakai POST --}}
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-red-500 hover:bg-red-50 w-full transition">
                        <i class="fa-solid fa-right-from-bracket"></i> Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endauth

</nav>

{{-- Alpine JS --}}
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>