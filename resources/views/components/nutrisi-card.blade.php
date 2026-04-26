@props([
    'judul'        => 'Nutrisi',
    'status'       => 'deficiency',
    'labelStatus'  => 'Kekurangan',
    'nilaiSaatIni' => '-',
    'nilaiOptimal' => '-',
    'deskripsi'    => '',
    'aksiList'     => [],
    'actionRoute'  => '#',
    'doneRoute'    => '#',
    'id'           => 'card',
])

@php
    $borderColor = $status === 'deficiency' ? 'border-l-red-400' : 'border-l-green-500';
    $badgeBg     = $status === 'deficiency' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700';
@endphp

<div class="bg-white rounded-2xl border border-gray-100 border-l-4 {{ $borderColor }} p-6 h-full flex flex-col shadow-sm">

    {{-- Header --}}
    <div class="flex items-center gap-2 mb-4">
        <h6 class="font-brand font-bold text-gray-800 text-base">{{ $judul }}</h6>
        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $badgeBg }}">{{ $labelStatus }}</span>
    </div>

    {{-- Nilai --}}
    <p class="text-sm text-gray-500 leading-7">
        <span class="font-semibold text-gray-700">Nilai Saat Ini:</span> {{ $nilaiSaatIni }}<br>
        <span class="font-semibold text-gray-700">Optimal:</span> {{ $nilaiOptimal }}
    </p>

    {{-- Deskripsi --}}
    @if($deskripsi)
        <p class="text-sm text-gray-500 bg-gray-50 rounded-xl px-4 py-3 my-3 leading-relaxed">
            {{ $deskripsi }}
        </p>
    @endif

    {{-- Aksi --}}
    <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mt-3 mb-2">Rekomendasi Action:</p>
    <div class="space-y-1.5 mb-4 flex-1">
        @foreach($aksiList as $aksi)
            <div class="flex items-start gap-2 text-sm text-gray-700">
                <i class="bi bi-info-circle-fill text-green-500 mt-0.5 flex-shrink-0"></i>
                <span>{{ $aksi }}</span>
            </div>
        @endforeach
    </div>

    {{-- Buttons --}}
    <div class="flex gap-2 pt-2">
        <form action="{{ $actionRoute }}" method="POST">
            @csrf
            <input type="hidden" name="nutrisi_id" value="{{ $id }}">
            <button class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold font-brand px-5 py-2 rounded-lg transition-all duration-150 hover:-translate-y-0.5 hover:shadow-md hover:shadow-green-200">
                Terapkan Sekarang
            </button>
        </form>
        <form action="{{ $doneRoute }}" method="POST">
            @csrf
            <input type="hidden" name="nutrisi_id" value="{{ $id }}">
            <button class="border border-gray-200 text-gray-500 hover:border-green-400 hover:text-green-600 text-sm font-medium px-5 py-2 rounded-lg transition-all duration-150 flex items-center gap-1.5">
                <i class="bi bi-check-circle"></i> Sudah Dilakukan
            </button>
        </form>
    </div>
</div>