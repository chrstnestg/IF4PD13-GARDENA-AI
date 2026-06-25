@extends('layouts.app')
@section('title', 'Riwayat Anomali Lingkungan')

@section('content')

<div x-data="{ modal: false, selected: null }">

{{-- ════════ HEADER ════════ --}}
<div class="mb-6">
    <h1 class="font-brand font-bold text-2xl text-gray-800">Riwayat Penanganan Lingkungan</h1>
    <p class="text-sm text-gray-400 mt-1">Log catatan kondisi sensor tidak optimal dan rekomendasi tindakan AI</p>
</div>

{{-- ════════ STAT CARDS ════════ --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">TOTAL INSIDEN ANOMALI</p>
        <p class="font-brand font-extrabold text-3xl text-gray-800 leading-none">{{ $stats['total_insiden'] }} Kali</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">BELUM DITANGANI</p>
        <p class="font-brand font-extrabold text-3xl text-red-500 leading-none">{{ $stats['belum_ditangani'] }} Kejadian</p>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5">
        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">SELESAI DIATASI</p>
        <p class="font-brand font-extrabold text-3xl text-green-500 leading-none">{{ $stats['selesai_diatasi'] }} Selesai</p>
    </div>
</div>

{{-- ════════ FILTER ════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5 mb-6">
    <div class="flex items-center gap-2 mb-4">
        <i class="bi bi-funnel-fill text-green-500"></i>
        <h6 class="font-brand font-bold text-gray-800">Filter Status</h6>
    </div>
    <form method="GET" action="{{ route('riwayat') }}" class="flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[180px]">
            <select name="status" onchange="this.form.submit()"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-300 bg-white">
                <option value="">Semua Status</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending (Belum Diatasi)</option>
                <option value="Teratasi" {{ request('status') == 'Teratasi' ? 'selected' : '' }}>Teratasi</option>
            </select>
        </div>
        <div class="flex-1 text-right">
            @if(request()->filled('status'))
                <a href="{{ route('riwayat') }}" class="text-sm text-gray-400 hover:text-green-500 transition-colors">Reset Filter</a>
            @endif
        </div>
    </form>
</div>

{{-- ════════ TABEL ════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-800 text-white">
                    @foreach(['NO','WAKTU KEJADIAN','DIAGNOSIS ANOMALI AI','PH','TDS','SUHU AIR','STATUS','AKSI'] as $h)
                        <th class="px-4 py-3.5 text-left text-[10px] font-bold uppercase tracking-wider whitespace-nowrap">
                            {{ $h }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($riwayatList as $i => $row)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3.5 text-gray-400 text-xs">{{ $i + 1 }}</td>
                        <td class="px-4 py-3.5 text-gray-700 font-medium whitespace-nowrap">
                            {{ $row['waktu_kejadian'] }}
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="font-bold text-red-500">{{ $row['status_anomali'] }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-gray-700 font-semibold">{{ $row['sensor'][0]['nilai'] }}</td>
                        <td class="px-4 py-3.5 text-gray-700 font-semibold whitespace-nowrap">{{ $row['sensor'][1]['nilai'] }}</td>
                        <td class="px-4 py-3.5 text-gray-700 font-semibold whitespace-nowrap">{{ $row['sensor'][2]['nilai'] }}</td>
                        <td class="px-4 py-3.5">
                            @if($row['status_perbaikan'] == 'Pending')
                                <span class="bg-red-100 text-red-700 ring-red-200 inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold ring-1">
                                    Pending
                                </span>
                            @else
                                <span class="bg-green-100 text-green-700 ring-green-200 inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-bold ring-1">
                                    Teratasi
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1.5">
                                {{-- Detail --}}
                                <button @click="selected = {{ json_encode($row) }}; modal = true"
                                        class="text-xs font-semibold text-green-600 border border-green-200 hover:bg-green-500 hover:text-white hover:border-green-500 px-3 py-1.5 rounded-lg transition-all duration-150">
                                    Lihat Solusi AI
                                </button>
                                
                                {{-- Tombol Aksi Konfirmasi Petani --}}
                                @if($row['status_perbaikan'] == 'Pending')
                                    <form action="{{ route('riwayat.teratasi', $row['id']) }}" method="POST" onsubmit="return confirm('Apakah Anda sudah selesai melakukan penanganan sesuai rekomendasi AI?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="text-xs font-semibold text-white bg-blue-500 hover:bg-blue-600 px-3 py-1.5 rounded-lg transition-all duration-150 shadow-sm">
                                            Selesai Ditangani
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-16 text-center text-gray-400">
                            <i class="bi bi-shield-check text-4xl block mb-3 text-green-400"></i>
                            Lingkungan tanaman selalu optimal. Belum ada riwayat anomali terdeteksi!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ════════ MODAL DETAIL REKOMENDASI AI ════════ --}}
    <div x-show="modal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="modal = false"
         class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4"
         style="display:none;">
        <div x-show="modal"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-amber-500 px-7 py-6 relative">
                <p class="text-red-100 text-xs font-bold uppercase tracking-widest mb-1">Detail Diagnosis Lingkungan</p>
                <h3 class="font-brand font-extrabold text-white text-xl"
                    x-text="selected?.status_anomali"></h3>
                <button @click="modal = false"
                        class="absolute top-5 right-5 text-white/70 hover:text-white transition-colors">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <div class="px-7 py-6 space-y-6">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Waktu Deteksi</p>
                    <p class="font-brand font-bold text-gray-800" x-text="selected?.waktu_kejadian"></p>
                </div>

                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Nilai Sensor Saat Kejadian</p>
                    <div class="grid grid-cols-3 gap-3">
                        <template x-for="sensor in (selected?.sensor ?? [])">
                            <div class="bg-gray-50 rounded-xl px-3 py-3 flex flex-col items-center text-center">
                                <i :class="'bi ' + sensor.icon + ' text-red-500 text-lg mb-1'"></i>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-gray-400" x-text="sensor.label"></span>
                                <span class="font-brand font-bold text-gray-800 text-sm mt-0.5" x-text="sensor.nilai"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Rekomendasi Tindakan AI (FastAPI)</p>
                    <div class="bg-amber-50 border border-amber-100 rounded-xl px-4 py-3">
                        <p class="text-sm text-gray-700 leading-relaxed font-medium" x-text="selected?.rekomendasi"></p>
                    </div>
                </div>
            </div>
            <div class="px-7 pb-6">
                <button @click="modal = false"
                        class="w-full border border-gray-200 text-gray-600 font-semibold font-brand py-3 rounded-2xl hover:bg-gray-50 transition-colors">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>

</div>

</div>

@endsection