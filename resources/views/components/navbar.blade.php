<nav class="sticky top-0 z-50 bg-white border-b border-gray-200 h-16 flex items-center px-8">

    {{-- Logo sebesar navbar --}}
    <a href="{{ route('dashboard') }}" class="flex-shrink-0">
        <img src="{{ asset('images/logo gardena-ai.jpeg') }}"
             alt="GARDENA-AI"
             class="h-12 w-auto object-contain">
    </a>

    {{-- Nav Links di tengah --}}
    <div class="flex items-center gap-1 flex-1 justify-center">
        @foreach([
            ['dashboard',   'Monitoring'],
            ['rekomendasi', 'Rekomendasi'],
            ['riwayat',     'Riwayat'],
        ] as [$route, $label])
            <a href="{{ route($route) }}"
               class="relative px-4 py-2 rounded-lg text-sm font-semibold font-brand transition-all duration-150
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

    {{-- User --}}
    <div class="flex items-center gap-3">
        <div class="text-right hidden sm:block">
            <p class="text-sm font-semibold font-brand text-gray-800 leading-tight">
                {{ auth()->user()->name ?? 'Irene Kristi' }}
            </p>
            <p class="text-xs text-gray-400">{{ auth()->user()->role ?? 'Petani' }}</p>
        </div>
        <div class="w-9 h-9 rounded-xl bg-green-500 flex items-center justify-center text-white text-xs font-bold font-brand flex-shrink-0">
            {{ strtoupper(substr(auth()->user()->name ?? 'IK', 0, 2)) }}
        </div>
    </div>
</nav>