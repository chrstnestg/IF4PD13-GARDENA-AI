@extends('layouts.app')
@section('title', 'Rekomendasi')

@section('content')

{{-- Alert Kritis --}}
@if($kondisiAktif && $kondisiAktif['kritis'] && !$sedangCooldown)
    <div class="bg-red-500 text-white text-sm font-semibold px-8 py-3.5 flex items-center gap-2 -mx-10 -mt-7 mb-6">
        <i class="bi bi-exclamation-triangle-fill"></i> {{ $kondisiAktif['pesanKritis'] }}
    </div>
@endif

{{-- Header --}}
<div class="flex items-start justify-between flex-wrap gap-4 mb-6">
    <div>
        <h1 class="font-brand font-bold text-2xl text-gray-800">Rekomendasi Nutrisi &amp; Perawatan</h1>
        <p class="text-sm text-gray-400 mt-1 flex items-center gap-1.5">
            <i class="bi bi-calendar3"></i>
            {{ \Carbon\Carbon::now()->translatedFormat('j F Y') }}
        </p>
    </div>
    <x-health-score :score="$healthScore" :label="$healthLabel" />
</div>

{{-- ── 1. TAMPILAN JIKA SEDANG COOLDOWN (STATUS JEDA) ── --}}
@if($sedangCooldown)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center max-w-2xl mx-auto my-4">
        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4 animate-pulse">
            <i class="bi bi-hourglass-split text-2xl text-gray-500"></i>
        </div>
        <h2 class="font-brand font-bold text-lg text-gray-800 mb-2">Sistem dalam Masa Tunggu (Cooldown)</h2>
        <p class="text-gray-500 text-sm leading-relaxed max-w-md mx-auto mb-5">
            Tindakan manual Anda telah dicatat. Sistem memberikan jeda waktu agar larutan nutrisi fisik tercampur merata di dalam tangki air hidroponik.
        </p>
        
        <div class="inline-block bg-gray-50 border border-gray-200 rounded-xl px-6 py-2.5">
            <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-0.5">Evaluasi Ulang AI Dalam</p>
            <p id="countdownTimer" class="font-mono font-bold text-xl text-green-600">05:00</p>
        </div>
    </div>

{{-- ── 2. TAMPILAN JIKA TIDAK ADA DATA ANALISIS ── --}}
@elseif(!$kondisiAktif)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center">
        <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
            <span class="text-4xl">📡</span>
        </div>
        <p class="text-gray-400 text-base">Belum ada data analisis. Tunggu sensor mengirim data.</p>
    </div>

{{-- ── 3. TAMPILAN KONDISI NORMAL ── --}}
@elseif($kondisiAktif['isNormal'])
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center">
        <div class="w-28 h-28 rounded-full border-[3px] border-green-500 bg-green-50 flex items-center justify-center mx-auto mb-6">
            <span class="text-5xl">🥬</span>
        </div>
        <h2 class="font-brand font-extrabold text-2xl text-gray-800 mb-3">Tanaman Anda Berada dalam Kondisi Optimal</h2>
        <p class="text-gray-500 text-base leading-relaxed max-w-md mx-auto mb-8">
            Semua parameter (TDS, pH, dan suhu) berada dalam rentang ideal. Tanaman sawi putih Anda tumbuh sehat dan stabil.
        </p>
        <p class="text-sm font-semibold text-gray-500 text-left max-w-2xl mx-auto mb-3">
            <i class="bi bi-graph-up-arrow text-green-500 me-1"></i> <strong>7 Hari Terakhir</strong>
        </p>
        <div class="max-w-2xl mx-auto h-44">
            <canvas id="stabilityChart"></canvas>
        </div>
    </div>

{{-- ── 4. TAMPILAN KONDISI BERMASALAH (ADA TOMBOL) ── --}}
@else
    <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-4">Kondisi Saat Ini</p>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-brand font-bold text-xl text-gray-800">{{ $kondisiAktif['judul'] }}</h2>
            <span @class([
                'text-xs font-bold px-3 py-1 rounded-full',
                'bg-red-100 text-red-600'       => $kondisiAktif['labelStatus'] === 'Kritis',
                'bg-orange-100 text-orange-600'  => $kondisiAktif['labelStatus'] === 'Peringatan',
                'bg-yellow-100 text-yellow-600'  => $kondisiAktif['labelStatus'] === 'Perlu Perhatian',
                'bg-green-100 text-green-600'    => $kondisiAktif['labelStatus'] === 'Optimal',
            ])>
                {{ $kondisiAktif['labelStatus'] }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4 text-sm">
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-gray-400 text-xs mb-1">Nilai Saat Ini</p>
                <p class="font-semibold text-gray-700">{{ $kondisiAktif['nilaiSaatIni'] }}</p>
            </div>
            <div class="bg-green-50 rounded-xl p-4">
                <p class="text-gray-400 text-xs mb-1">Nilai Optimal</p>
                <p class="font-semibold text-green-700">{{ $kondisiAktif['nilaiOptimal'] }}</p>
            </div>
        </div>

        <p class="text-sm text-gray-500 bg-gray-50 rounded-xl px-4 py-3 mb-4">{{ $kondisiAktif['deskripsi'] }}</p>

        <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2">Yang Harus Dilakukan:</p>
        <ul class="mb-6 space-y-2">
            @foreach($kondisiAktif['aksiList'] as $i => $aksi)
                <li class="flex items-start gap-2 text-sm text-gray-600">
                    <span class="font-bold text-green-600">{{ $i + 1 }}.</span> {{ $aksi }}
                </li>
            @endforeach
        </ul>

        <form id="formSelesai" method="POST" action="{{ route('rekomendasi.selesai') }}">
            @csrf
            <input type="hidden" name="nutrisi_id" value="{{ $kondisiAktif['id'] }}">
            <button type="button" id="btnSelesai" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 rounded-xl transition">
                Sudah Ditangani
            </button>
        </form>
    </div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    // ── HITUNG MUNDUR COOLDOWN (FIXED FORMAT INTEGER) ──
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

    // ── Chart 7 Hari ──
    @if($kondisiAktif && $kondisiAktif['isNormal'])
    new Chart(document.getElementById('stabilityChart'), {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [
                { label:'TDS',  data:@json($chartTds),  borderColor:'#2d9a4f', borderWidth:2.5, pointRadius:4, tension:0.3, fill:false },
                { label:'pH',   data:@json($chartPh),   borderColor:'#38bdf8', borderWidth:2.5, pointRadius:4, tension:0.3, fill:false },
                { label:'Suhu', data:@json($chartSuhu), borderColor:'#fb923c', borderWidth:2.5, pointRadius:4, tension:0.3, fill:false },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: true } },
            scales: {
                x: { grid:{ color:'rgba(0,0,0,0.04)' }, ticks:{ font:{size:11}, color:'#94a3b8' } },
                y: { grid:{ color:'rgba(0,0,0,0.04)' }, ticks:{ font:{size:11}, color:'#94a3b8' } }
            }
        }
    });
    @endif

    // ── Konfirmasi SweetAlert ──
    const btnSelesai = document.getElementById('btnSelesai');
    const formSelesai = document.getElementById('formSelesai');

    if (btnSelesai && formSelesai) {
        btnSelesai.addEventListener('click', () => {
            Swal.fire({
                icon: 'question',
                title: 'Konfirmasi Tindakan',
                text: 'Apakah kamu sudah benar-benar menangani masalah ini?',
                showCancelButton: true,
                confirmButtonText: 'Ya, Sudah Ditangani',
                cancelButtonText: 'Belum',
                confirmButtonColor: '#16a34a',
                cancelButtonColor: '#6b7280',
            }).then(result => {
                if (result.isConfirmed) {
                    formSelesai.style.display = 'none';
                    formSelesai.submit();
                }
            });
        });
    }

    // ── Notifikasi Sukses Setelah Redirect ──
    @if(session('swal'))
    Swal.fire({
        icon:  '{{ session("swal.icon") }}',
        title: '{{ session("swal.title") }}',
        text:  '{{ session("swal.text") }}',
        confirmButtonColor: '#16a34a',
        timer: 3000,
        timerProgressBar: true,
    });
    @endif

});
</script>
@endpush