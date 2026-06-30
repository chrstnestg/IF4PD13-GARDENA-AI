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

        {{-- Total Insiden --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">TOTAL INSIDEN ANOMALI</p>
            <p class="font-brand font-extrabold text-3xl text-gray-800 leading-none">{{ $stats['total_insiden'] }} Kali</p>
        </div>

        {{-- Selesai Diatasi --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">SELESAI DIATASI</p>
            <p class="font-brand font-extrabold text-3xl text-green-500 leading-none">{{ $stats['selesai_diatasi'] }} Selesai</p>
        </div>

        {{-- Anomali Terakhir --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">ANOMALI TERAKHIR</p>
            <p class="font-brand font-extrabold text-lg text-red-500 leading-snug">
                {{ $stats['anomali_terakhir'] }}
            </p>
        </div>

    </div>

    {{-- ════════ PANEL FILTER (PASTI 1 BARIS, UKURAN PENDEK & RAPID) ════════ --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-5 py-3 mb-6">
        <form method="GET" action="{{ route('riwayat') }}" class="flex flex-row flex-wrap items-center gap-2">
            
            {{-- Filter Jenis Anomali --}}
            <div class="w-44">
                <select name="anomali" class="w-full border border-gray-200 rounded-xl px-2.5 py-1.5 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-300 bg-white">
                    <option value="">Semua Anomali</option>
                    <option value="pH Tinggi" {{ request('anomali') == 'pH Tinggi' ? 'selected' : '' }}>pH Tinggi</option>
                    <option value="pH Rendah" {{ request('anomali') == 'pH Rendah' ? 'selected' : '' }}>pH Rendah</option>
                    <option value="Nutrisi Kurang" {{ request('anomali') == 'Nutrisi Kurang' ? 'selected' : '' }}>Nutrisi Kurang</option>
                    <option value="Nutrisi Berlebih" {{ request('anomali') == 'Nutrisi Berlebih' ? 'selected' : '' }}>Nutrisi Berlebih</option>
                    <option value="Suhu" {{ request('anomali') == 'Suhu' ? 'selected' : '' }}>Suhu Tidak Ideal</option>
                </select>
            </div>

            {{-- Filter Rentang Waktu --}}
            <div class="w-36">
                <select name="periode" class="w-full border border-gray-200 rounded-xl px-2.5 py-1.5 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-300 bg-white">
                    <option value="">Semua Waktu</option>
                    <option value="today" {{ request('periode') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="7" {{ request('periode') == '7' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="30" {{ request('periode') == '30' ? 'selected' : '' }}>30 Hari Terakhir</option>
                </select>
            </div>

            {{-- Filter Input Pencarian --}}
            <div class="w-48">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari diagnosis..."
                       class="w-full border border-gray-200 rounded-xl px-2.5 py-1.5 text-xs text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-300 bg-white">
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex items-center gap-1.5">
                <button type="submit" class="px-4 py-1.5 rounded-xl bg-green-600 text-white text-xs font-semibold hover:bg-green-700 transition-colors shadow-sm whitespace-nowrap">
                    Terapkan
                </button>
                
                @if(request()->anyFilled(['anomali', 'periode', 'search']))
                    <a href="{{ route('riwayat') }}" class="px-3 py-1.5 border border-gray-200 rounded-xl text-xs text-gray-500 hover:bg-gray-50 transition-colors whitespace-nowrap text-center">
                        Reset
                    </a>
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
                        @foreach(['NO','WAKTU KEJADIAN','DIAGNOSIS ANOMALI AI','PH','TDS','SUHU AIR','AKSI'] as $h)
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
                                <button @click="selected = {{ json_encode($row) }}; modal = true"
                                        class="text-xs font-semibold text-green-600 border border-green-200 hover:bg-green-500 hover:text-white hover:border-green-500 px-3 py-1.5 rounded-lg transition-all duration-150">
                                    Lihat Solusi AI
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-gray-400">
                                <i class="bi bi-shield-check text-4xl block mb-3 text-green-400"></i>
                                Tidak ada data riwayat yang cocok dengan filter saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ════════ MODAL DETAIL REKOMENDASI AI (SUDAH DIPERBAIKI RAPI) ════════ --}}
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

            {{-- Modal Header --}}
            <div class="bg-gradient-to-r from-green-600 to-emerald-500 px-7 py-5 relative">
                <p class="text-green-100 text-[10px] font-bold uppercase tracking-widest mb-0.5">Detail Diagnosis Lingkungan</p>
                <h3 class="font-brand font-extrabold text-black text-lg" x-text="selected?.status_anomali"></h3>
                <button @click="modal = false" class="absolute top-5 right-5 text-blackgoog/70 hover:text-white transition-colors">
                    <i class="bi bi-x-lg text-sm"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="px-7 py-6 space-y-5">
                {{-- Waktu Deteksi --}}
                <div class="flex justify-between items-center bg-gray-50 rounded-xl px-4 py-2.5 border border-gray-100">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Waktu Deteksi</span>
                    <span class="text-xs font-bold text-gray-700" x-text="selected?.waktu_kejadian"></span>
                </div>

                {{-- Nilai Sensor --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2.5">Nilai Sensor Saat Kejadian</p>
                    <div class="grid grid-cols-3 gap-2.5">
                        <template x-for="sensor in (selected?.sensor ?? [])">
                            <div class="bg-gray-50/50 rounded-xl p-3 border border-gray-100 flex flex-col items-center text-center">
                                <i :class="'bi ' + sensor.icon + ' text-green-500 text-base mb-1'"></i>
                                <span class="text-[9px] font-bold uppercase tracking-wider text-gray-400" x-text="sensor.label"></span>
                                <span class="font-brand font-bold text-gray-800 text-sm mt-0.5" x-text="sensor.nilai"></span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Rekomendasi Tindakan (Memecah karakter '|' menjadi list terstruktur) --}}
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Rekomendasi Tindakan AI</p>
                    <div class="bg-green-50/60 border border-green-100 rounded-xl p-4">
                        <ul class="space-y-2 text-xs text-gray-700 leading-relaxed">
                            <template x-for="(item, index) in (selected?.rekomendasi ? selected.rekomendasi.split('|') : [])">
                                <li class="flex items-start gap-2" x-show="item.trim() !== ''">
                                    <span class="font-bold text-green-600 mt-0.5" x-text="(index + 1) + '.'"></span>
                                    <span class="font-medium" x-text="item.trim()"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-7 pb-5">
                <button @click="modal = false"
                        class="w-full bg-gray-100 text-gray-600 font-semibold font-brand py-2.5 rounded-xl hover:bg-gray-200 transition-colors text-sm">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>

</div>

@endsection