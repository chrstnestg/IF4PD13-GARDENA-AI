@extends('layouts.app')
@section('title', 'Riwayat')

@section('content')

<div x-data="{ 
    modal: false, selected: null, 
    modalTambah: false,
    modalEdit: false, editData: null,
    modalHapus: false, hapusId: null
}">

{{-- ════════ HEADER ════════ --}}
<div class="mb-6">
    <h1 class="font-brand font-bold text-2xl text-gray-800">Riwayat Data</h1>
    <p class="text-sm text-gray-400 mt-1">Catatan hasil panen sawi putih hidroponik Anda</p>
    <button @click="modalTambah = true"
            class="mt-3 inline-flex items-center gap-2 bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors shadow-sm">
        <i class="bi bi-plus-lg"></i>
        Tambah Panen Manual
    </button>
</div>

{{-- ════════ STAT CARDS ════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    @foreach([
        ['TOTAL PANEN SELURUHNYA', $stats['total'],       null,          null],
        ['RATA-RATA PER SIKLUS',   $stats['rata'],        null,          null],
        ['SIKLUS TERBAIK',         $stats['terbaik'],     $stats['terbaikLabel'], 'green'],
        ['JUMLAH SIKLUS',          $stats['jumlah'],      null,          null],
    ] as [$label, $nilai, $sub, $accent])
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5">
            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">{{ $label }}</p>
            <p class="font-brand font-extrabold text-3xl text-gray-800 leading-none">{{ $nilai }}</p>
            @if($sub)
                <p class="text-xs font-semibold text-green-500 mt-1 uppercase tracking-wide">{{ $sub }}</p>
            @endif
        </div>
    @endforeach
</div>

{{-- ════════ FILTER ════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm px-6 py-5 mb-6">
    <div class="flex items-center gap-2 mb-4">
        <i class="bi bi-funnel-fill text-green-500"></i>
        <h6 class="font-brand font-bold text-gray-800">Filter Riwayat</h6>
    </div>
    <form method="GET" action="{{ route('riwayat') }}" class="flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[180px]">
            <select name="siklus" onchange="this.form.submit()"
                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-300 bg-white">
                <option value="">Semua Siklus</option>
                @for($i = 1; $i <= $stats['jumlah']; $i++)
                    <option value="{{ $i }}" {{ request('siklus') == $i ? 'selected' : '' }}>
                        Siklus {{ $i }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="flex items-center gap-3 flex-1 min-w-[200px]">
            <span class="text-gray-300 font-bold">—</span>
            <div class="flex items-center gap-2 flex-1">
                <input type="date" name="dari" value="{{ request('dari') }}"
                       class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-300">
                <span class="text-gray-400 text-sm">s/d</span>
                <input type="date" name="sampai" value="{{ request('sampai') }}"
                       class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-300">
                <button type="submit"
                        class="bg-green-500 hover:bg-green-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
                    Filter
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ════════ TABEL ════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-8">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-800 text-white">
                    @foreach(['NO','TANGGAL PANEN','SIKLUS','BERAT PANEN','JUMLAH IKAT','AVG. HEALTH','KUALITAS','CATATAN','AKSI'] as $h)
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
                            {{ \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('j F Y') }}
                        </td>
                        <td class="px-4 py-3.5">
                            <span class="font-bold text-green-500">{{ $row['siklus'] }}</span>
                        </td>
                        <td class="px-4 py-3.5 text-gray-700 font-semibold">{{ $row['berat'] }}</td>
                        <td class="px-4 py-3.5 text-gray-600">{{ $row['jumlahIkat'] }}</td>
                        <td class="px-4 py-3.5 font-bold text-gray-800">{{ $row['avgHealth'] }}</td>
                        <td class="px-4 py-3.5">
                            @php
                                $kualitasColor = match($row['kualitas']) {
                                    'A+'    => 'bg-green-100 text-green-700 ring-green-200',
                                    'A'     => 'bg-teal-100 text-teal-700 ring-teal-200',
                                    'B+'    => 'bg-amber-100 text-amber-700 ring-amber-200',
                                    'B'     => 'bg-yellow-100 text-yellow-700 ring-yellow-200',
                                    default => 'bg-gray-100 text-gray-600 ring-gray-200',
                                };
                            @endphp
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs font-extrabold ring-1 {{ $kualitasColor }}">
                                {{ $row['kualitas'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 text-gray-500 text-xs max-w-[160px] truncate">
                            {{ $row['catatan'] }}
                        </td>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-1.5">
                                {{-- Detail --}}
                                <button @click="selected = {{ json_encode($row) }}; modal = true"
                                        class="text-xs font-semibold text-green-600 border border-green-200 hover:bg-green-500 hover:text-white hover:border-green-500 px-3 py-1.5 rounded-lg transition-all duration-150">
                                    Detail
                                </button>
                                {{-- Edit --}}
                                <button @click="editData = {{ json_encode($row) }}; modalEdit = true"
                                        class="text-xs font-semibold text-blue-600 border border-blue-200 hover:bg-blue-500 hover:text-white hover:border-blue-500 px-3 py-1.5 rounded-lg transition-all duration-150">
                                    Edit
                                </button>
                                {{-- Hapus --}}
                                <button @click="hapusId = {{ $row['id'] }}; modalHapus = true"
                                        class="text-xs font-semibold text-red-500 border border-red-200 hover:bg-red-500 hover:text-white hover:border-red-500 px-3 py-1.5 rounded-lg transition-all duration-150">
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-16 text-center text-gray-400">
                            <i class="bi bi-inbox text-4xl block mb-3"></i>
                            Belum ada data riwayat panen.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ════════ MODAL DETAIL ════════ --}}
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
            <div class="bg-gradient-to-r from-teal-600 to-green-500 px-7 py-6 relative">
                <p class="text-teal-100 text-xs font-bold uppercase tracking-widest mb-1">Detail Laporan Panen</p>
                <h3 class="font-brand font-extrabold text-white text-2xl"
                    x-text="'Siklus ' + (selected?.siklus ?? '')"></h3>
                <button @click="modal = false"
                        class="absolute top-5 right-5 text-white/70 hover:text-white transition-colors">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            <div class="px-7 py-6 space-y-6">
                <div class="grid grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Tanggal Panen</p>
                        <p class="font-brand font-bold text-gray-800" x-text="selected?.tanggalLabel"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Berat Panen</p>
                        <p class="font-brand font-bold text-gray-800"
                           x-text="selected?.berat + ' (' + selected?.jumlahIkat + ' ikat)'"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Kualitas</p>
                        <p class="font-brand font-bold text-teal-600" x-text="selected?.kualitasLabel"></p>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">Avg Health</p>
                        <p class="font-brand font-bold text-gray-800" x-text="selected?.avgHealth"></p>
                    </div>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-3">Rata-rata Sensor Selama Siklus</p>
                    <div class="grid grid-cols-2 gap-3">
                        <template x-for="sensor in (selected?.sensor ?? [])">
                            <div class="bg-gray-50 rounded-xl px-4 py-3 flex items-center gap-3">
                                <i :class="'bi ' + sensor.icon + ' text-teal-500 text-lg'"></i>
                                <div>
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400" x-text="sensor.label"></p>
                                    <p class="font-brand font-bold text-gray-800 text-sm" x-text="sensor.nilai"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-2">Catatan Petani</p>
                    <div class="bg-green-50 border border-green-100 rounded-xl px-4 py-3">
                        <p class="text-sm text-gray-600 leading-relaxed italic"
                           x-text="'\"' + (selected?.catatanLengkap ?? selected?.catatan ?? '-') + '\"'"></p>
                    </div>
                </div>
            </div>
            <div class="px-7 pb-6 flex gap-3">
                <button @click="modal = false"
                        class="flex-1 border border-gray-200 text-gray-600 font-semibold font-brand py-3 rounded-2xl hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
                <button @click="editData = selected; modal = false; modalEdit = true"
                        class="flex-1 bg-gradient-to-r from-teal-600 to-green-500 hover:from-teal-700 hover:to-green-600 text-white font-bold font-brand py-3 rounded-2xl transition-all duration-150 hover:shadow-lg hover:shadow-green-200">
                    Edit Catatan
                </button>
            </div>
        </div>
    </div>

    {{-- ════════ MODAL EDIT ════════ --}}
    <x-riwayat-edit />

    {{-- ════════ MODAL HAPUS ════════ --}}
    <x-riwayat-hapus />

    {{-- ════════ MODAL TAMBAH PANEN ════════ --}}
    <x-riwayat-tambah :siklus="$stats['jumlah'] + 1" />

</div>
{{-- tutup div tabel --}}

</div>
{{-- tutup x-data wrapper --}}

@endsection