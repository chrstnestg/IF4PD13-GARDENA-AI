@extends('layouts.app')
@section('title', 'Rekomendasi Asisten AI')

@section('content')

{{-- Alert Kritis (kondisi tanaman abnormal - hasil analisis AI valid) --}}
@if($kondisiAktif && !empty($kondisiAktif['kritis']) && !$sedangCooldown && !$aiBermasalah)
    <div class="bg-red-500 text-white text-xs sm:text-sm font-semibold px-4 sm:px-6 md:px-10 py-3 md:py-3.5 flex items-start sm:items-center gap-2 -mx-4 sm:-mx-6 md:-mx-10 -mt-5 sm:-mt-7 mb-6 shadow-md">
        <span>Sistem mendeteksi perubahan parameter yang perlu diperhatikan. Ikuti rekomendasi Asisten AI Gardena di bawah ini.</span>
    </div>
@endif

{{-- Alert kegagalan sistem AI (bukan kondisi tanaman) --}}
@if($aiBermasalah && !$sedangCooldown)
    <div class="bg-amber-100 border border-amber-300 text-amber-800 text-xs sm:text-sm font-medium px-4 sm:px-6 py-3 rounded-xl mb-6 flex items-start gap-2">
        <span>Asisten AI sedang tidak dapat memproses analisis baru. Data sensor tetap tersimpan seperti biasa.</span>
    </div>
@endif

{{-- Header Dashboard --}}
<div class="flex items-start justify-between flex-wrap gap-3 sm:gap-4 mb-6">
    <div>
        <h1 class="font-brand font-bold text-lg sm:text-xl md:text-2xl text-gray-800">
            Gardena AI Insights
        </h1>
        <p class="text-xs sm:text-sm text-gray-400 mt-1">
            Analisis Runtun Waktu Real-Time • {{ \Carbon\Carbon::now()->translatedFormat('j F Y') }}
        </p>
    </div>
    <x-health-score :score="$healthScore" :label="$healthLabel" />
</div>

{{-- ── 1. STATUS COOLDOWN (Masa Tunggu Sirkulasi Air) ── --}}
@if($sedangCooldown)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 text-center max-w-2xl mx-auto my-4">
        <h2 class="font-brand font-bold text-base sm:text-lg text-gray-800 mb-2">Stabilisasi & Sirkulasi Larutan Nutrisi</h2>
        <p class="text-gray-500 text-xs sm:text-sm leading-relaxed max-w-md mx-auto mb-5">
            Tindakan perbaikan Anda telah disimpan. Sistem memberikan jeda waktu agar intervensi fisik (seperti penambahan air atau nutrisi) terlarut merata sebelum AI melakukan evaluasi statistik ulang.
        </p>
        <div class="inline-block bg-gray-50 border border-gray-200 rounded-xl px-5 sm:px-6 py-2 sm:py-2.5">
            <p class="text-[10px] sm:text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Analisis Ulang Dalam</p>
            <p id="countdownTimer" class="font-mono font-bold text-lg sm:text-xl text-green-600">05:00</p>
        </div>
    </div>

{{-- ── 2. STATUS JIKA KONDISI NORMAL / OPTIMAL ── --}}
@elseif(!$kondisiAktif)
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="md:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-10 text-center flex flex-col justify-center items-center">
            <h2 class="font-brand font-extrabold text-lg sm:text-xl text-gray-800 mb-2">Kondisi Ekosistem Hidroponik Optimal</h2>
            <p class="text-gray-500 text-sm leading-relaxed max-w-md mx-auto">
                Berdasarkan evaluasi statistik 30 data runtun waktu terakhir, seluruh parameter pH, TDS, dan Suhu air Sawi Putih berada dalam rentang kendali ideal.
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Target Regulasi Sawi</h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between py-1.5 border-b border-gray-50">
                        <span class="text-gray-500">Derajat Keasaman (pH)</span>
                        <span class="font-semibold text-gray-700">6.0 - 8.0</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-gray-50">
                        <span class="text-gray-500">Kepekatan Nutrisi (TDS)</span>
                        <span class="font-semibold text-gray-700">400 - 1200 ppm</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-gray-50">
                        <span class="text-gray-500">Suhu Air Ideal</span>
                        <span class="font-semibold text-gray-700">20°C - 28°C</span>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 p-3 rounded-xl text-[11px] text-gray-400 mt-4">
                Sistem AI memantau perubahan tren linier secara berkala untuk mencegah anomali tanaman sejak dini.
            </div>
        </div>
    </div>

    {{-- Grafik Tren Kestabilan --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 sm:p-6">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">
            Tren Rata-Rata Parameter 7 Hari Terakhir
        </p>
        <div class="h-56 sm:h-64 overflow-x-auto">
            <canvas id="stabilityChart" class="min-w-[300px]"></canvas>
        </div>
    </div>

{{-- ── 3. STATUS JIKA TERDETEKSI ANOMALI OLEH AI ── --}}
@else
    @if($aiBermasalah)
        {{-- ── STATE: AI SEDANG BERMASALAH ── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-10 text-center max-w-2xl mx-auto my-4">
            <h2 class="font-brand font-bold text-base sm:text-lg text-gray-800 mb-2">Analisis AI Sedang Tidak Tersedia</h2>
            <p class="text-gray-500 text-xs sm:text-sm leading-relaxed max-w-md mx-auto mb-5">
                {{ $kondisiAktif['errorMessage'] }}
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs max-w-md mx-auto mb-5">
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-3 text-left">
                    <p class="text-gray-400 font-medium mb-1">Nilai Sensor Terakhir</p>
                    <p class="font-mono font-bold text-gray-700">{{ $kondisiAktif['nilaiSaatIni'] }}</p>
                </div>
                <div class="bg-green-50/60 border border-green-100/50 rounded-xl p-3 text-left">
                    <p class="text-green-500 font-medium mb-1">Threshold Batas Ideal</p>
                    <p class="font-mono font-bold text-green-700">{{ $kondisiAktif['nilaiOptimal'] }}</p>
                </div>
            </div>
            <a href="{{ route('rekomendasi') }}" class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-sm px-5 py-2.5 rounded-xl transition">
                Muat Ulang
            </a>
        </div>
    @else
        {{-- ── STATE: TERDETEKSI ANOMALI (narasi AI valid) — kode lama tetap di sini ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Kolom Kiri: Ringkasan Analisis --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-6">
                <div class="flex items-center justify-between gap-2 mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Hasil Temuan AI</span>
                    <span class="text-[10px] sm:text-xs font-bold px-3 py-1 rounded-full {{ $kondisiAktif['bgLabelClass'] }}">
                        {{ $kondisiAktif['labelStatus'] }}
                    </span>
                </div>

                {{-- Urutan Blok Narasi dari Hasil JSON --}}
                <div class="bg-gradient-to-r from-green-50/40 to-blue-50/10 border border-gray-100 rounded-xl p-4 mb-4 space-y-4">
                    {{-- 1. Ringkasan --}}
                    <div>
                        <h4 class="text-xs font-bold text-gray-400 uppercase mb-0.5 tracking-wide">Ringkasan:</h4>
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-medium">
                            {{ $kondisiAktif['summary'] }}
                        </p>
                    </div>

                    {{-- 2. Tren Waktu --}}
                    @if(!empty($kondisiAktif['trend']))
                    <div class="border-t border-gray-100 pt-3">
                        <h4 class="text-xs font-bold text-gray-400 uppercase mb-0.5 tracking-wide">Tren Runtun Waktu:</h4>
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                            {{ $kondisiAktif['trend'] }}
                        </p>
                    </div>
                    @endif

                    {{-- 3. Pola & Korelasi --}}
                    @if(!empty($kondisiAktif['pattern']))
                    <div class="border-t border-gray-100 pt-3">
                        <h4 class="text-xs font-bold text-gray-400 uppercase mb-0.5 tracking-wide">Pola & Korelasi:</h4>
                        <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                            {{ $kondisiAktif['pattern'] }}
                        </p>
                    </div>
                    @endif
                </div>

                {{-- Komparasi Angka Monitor --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-2 text-xs">
                    <div class="bg-gray-50 border border-gray-100 rounded-xl p-3">
                        <p class="text-gray-400 font-medium mb-1">Nilai Aktual Terakhir</p>
                        <p class="font-mono font-bold text-gray-700">{{ $kondisiAktif['nilaiSaatIni'] }}</p>
                    </div>
                    <div class="bg-green-50/60 border border-green-100/50 rounded-xl p-3">
                        <p class="text-green-500 font-medium mb-1">Threshold Batas Ideal</p>
                        <p class="font-mono font-bold text-green-700">{{ $kondisiAktif['nilaiOptimal'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Daftar Rekomendasi Aksi Tindakan --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col justify-between h-full">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-4">Rekomendasi Tindakan Operasional:</p>
                    <ul class="space-y-3">
                        @foreach($kondisiAktif['aksiList'] as $aksi)
                            @if(trim($aksi))
                            <li class="text-xs sm:text-sm text-gray-600 bg-gray-50/50 p-2.5 rounded-lg border border-gray-100">
                                <span class="leading-relaxed">{{ ltrim($aksi, '0123456789. ') }}</span>
                            </li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                <div class="mt-6">
                    <form id="formSelesai" method="POST" action="{{ route('rekomendasi.selesai') }}">
                        @csrf
                        <input type="hidden" name="nutrisi_id" value="{{ $kondisiAktif['id'] }}">
                        <button type="button" id="btnSelesai" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold text-sm py-3 rounded-xl transition shadow-sm">
                            Sudah Saya Tangani
                        </button>
                    </form>
                </div>
            </div>
        </div>
        </div>
    @endif
@endif


@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── COUNTDOWN COOLDOWN TIMER ──
    @if($sedangCooldown && $sisaDetikCooldown > 0)
        let totalDetik = Math.floor({{ $sisaDetikCooldown }});
        const timerElement = document.getElementById('countdownTimer');

        const intervalTimer = setInterval(() => {
            let menit = Math.floor(totalDetik / 60);
            let detik = totalDetik % 60;

            menit = menit < 10 ? '0' + menit : menit;
            detik = detik < 10 ? '0' + detik : detik;

            timerElement.textContent = `${menit}:${detik}`;

            if (totalDetik <= 0) {
                clearInterval(intervalTimer);
                window.location.reload();
            }
            totalDetik--;
        }, 1000);
    @endif

    // ── CHART 7 HARI ──
    @if(!$kondisiAktif)
    new Chart(document.getElementById('stabilityChart'), {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [
                { label:'TDS (ppm)',  data:@json($chartTds),  borderColor:'#2d9a4f', borderWidth:2.5, pointRadius:4, tension:0.3, fill:false },
                { label:'pH Air',     data:@json($chartPh),   borderColor:'#38bdf8', borderWidth:2.5, pointRadius:4, tension:0.3, fill:false },
                { label:'Suhu (°C)',  data:@json($chartSuhu), borderColor:'#fb923c', borderWidth:2.5, pointRadius:4, tension:0.3, fill:false },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: true, labels: { boxWidth:12, font:{size:11} } } },
            scales: {
                x: { grid:{ color:'rgba(0,0,0,0.02)' }, ticks:{ font:{size:11}, color:'#94a3b8' } },
                y: { grid:{ color:'rgba(0,0,0,0.02)' }, ticks:{ font:{size:11}, color:'#94a3b8' } }
            }
        }
    });
    @endif

    // ── KONFIRMASI SWEETALERT ──
    const btnSelesai = document.getElementById('btnSelesai');
    const formSelesai = document.getElementById('formSelesai');

    if (btnSelesai && formSelesai) {
        btnSelesai.addEventListener('click', () => {
            Swal.fire({
                icon: 'question',
                title: 'Konfirmasi Perbaikan',
                text: 'Apakah Anda telah menyesuaikan tangki fisik sesuai instruksi AI?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Selesai Ditangani',
                cancelButtonText: 'Kembali',
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
            }).then(result => {
                if (result.isConfirmed) {
                    btnSelesai.disabled = true;
                    btnSelesai.innerHTML = `Menyimpan...`;
                    formSelesai.submit();
                }
            });
        });
    }

    // ── ALERT BANNER NOTIFIKASI ──
    @if(session('swal'))
    Swal.fire({
        icon:  '{{ session("swal.icon") }}',
        title: '{{ session("swal.title") }}',
        text:  '{{ session("swal.text") }}',
        confirmButtonColor: '#16a34a',
        timer: 4000,
        timerProgressBar: true,
    });
    @endif

});
</script>
@endpush