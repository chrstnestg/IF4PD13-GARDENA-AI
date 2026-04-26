@extends('layouts.app')
@section('title', 'Rekomendasi')

@php
    $criticalAlertMsg = null;
    $semuaOptimal     = true;
    foreach($rekomendasiList as $item) {
        if(isset($item['kritis']) && $item['kritis'])
            $criticalAlertMsg = $item['pesanKritis'];
        if($item['status'] !== 'optimal')
            $semuaOptimal = false;
    }
@endphp

@section('content')

{{-- Alert Kritis --}}
@if($criticalAlertMsg)
    <div class="bg-red-500 text-white text-sm font-semibold px-8 py-3.5 flex items-center gap-2 -mx-6 -mt-7 mb-6 rounded-none">
        <i class="bi bi-exclamation-triangle-fill"></i> {{ $criticalAlertMsg }}
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

{{-- ════════ KONDISI OPTIMAL ════════ --}}
@if($semuaOptimal)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center mb-6">

        {{-- Ilustrasi --}}
        <div class="w-28 h-28 rounded-full border-[3px] border-green-500 bg-green-50 flex items-center justify-center mx-auto mb-6">
            <span class="text-5xl">🥬</span>
        </div>

        <h2 class="font-brand font-extrabold text-2xl text-gray-800 mb-3">
            Tanaman Anda Berada dalam Kondisi Optimal
        </h2>
        <p class="text-gray-500 text-base leading-relaxed max-w-md mx-auto mb-8">
            Semua parameter (EC, pH, suhu air, dan kelembapan) berada dalam rentang ideal.
            Tanaman sawi putih Anda tumbuh sehat dan stabil.
        </p>

        {{-- Chart label --}}
        <p class="text-sm font-semibold text-gray-500 text-left max-w-2xl mx-auto mb-3">
            <i class="bi bi-graph-up-arrow me-1 text-green-500"></i>
            <strong>7 Hari Terakhir – Kondisi Sangat Stabil</strong>
        </p>

        <div class="max-w-2xl mx-auto h-44">
            <canvas id="stabilityChart"></canvas>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap justify-center gap-5 mt-4">
            @foreach([
                ['#2d9a4f', 'EC Optimal'],
                ['#38bdf8', 'pH Optimal'],
                ['#fb923c', 'Suhu Optimal'],
                ['#a78bfa', 'Kelembapan Optimal'],
            ] as [$color, $label])
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <span class="w-2.5 h-2.5 rounded-full inline-block" style="background:{{ $color }}"></span>
                    {{ $label }}
                </div>
            @endforeach
        </div>
    </div>

{{-- ════════ KONDISI BERMASALAH ════════ --}}
@else
    <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-4">Hal yang Harus Dilakukan</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
        @foreach($rekomendasiList as $item)
            <x-nutrisi-card
                :judul="$item['judul']"
                :status="$item['status']"
                :labelStatus="$item['labelStatus']"
                :nilaiSaatIni="$item['nilaiSaatIni']"
                :nilaiOptimal="$item['nilaiOptimal']"
                :deskripsi="$item['deskripsi']"
                :aksiList="$item['aksiList']"
                :actionRoute="route('rekomendasi.terapkan')"
                :doneRoute="route('rekomendasi.selesai')"
                :id="$item['id']"
            />
        @endforeach
    </div>
@endif

{{-- ════════ INSIGHT DEEP LEARNING ════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
    <div class="flex items-center gap-2 mb-4">
        <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
            <i class="bi bi-cpu-fill"></i>
        </div>
        <h6 class="font-brand font-bold text-gray-800">Insight dari Deep Learning</h6>
    </div>

    <div class="border-l-[3px] border-green-500 bg-gray-50 rounded-r-xl px-4 py-3 mb-4 space-y-2">
        <p class="text-sm text-gray-600 leading-relaxed">{{ $insightParagraf1 }}</p>
        <p class="text-sm text-gray-600 leading-relaxed">{{ $insightParagraf2 }}</p>
    </div>

    <p class="text-sm font-bold text-gray-700 mb-2">Apa yang perlu diperhatikan agar tetap optimal:</p>
    <div class="space-y-1.5 mb-4">
        @foreach($insightTips as $tip)
            <div class="flex items-start gap-2 text-sm text-gray-600">
                <i class="bi bi-check-circle-fill text-green-500 mt-0.5 flex-shrink-0"></i>
                {{ $tip }}
            </div>
        @endforeach
    </div>

    <div class="bg-green-500 text-white text-sm rounded-xl px-4 py-3 leading-relaxed">
        <strong>Prediksi:</strong> {{ $insightPrediksi }}
    </div>
</div>

{{-- ════════ CLOSING CARD ════════ --}}
<div class="bg-gradient-to-br from-green-50 to-emerald-100 border border-green-200 rounded-2xl p-8 text-center">
    <h5 class="font-brand font-bold text-green-900 text-lg mb-3">
        Terima kasih telah mempercayakan perawatan tanaman sawi putih Anda kepada kami 🌿
    </h5>
    <p class="text-green-800 text-sm leading-relaxed max-w-xl mx-auto">
        Sistem ini kami buat agar Anda bisa lebih mudah memantau kondisi nutrisi, suhu, kelembapan,
        dan mendapatkan rekomendasi yang tepat setiap hari. Semoga tanaman sawi Anda selalu sehat,
        tumbuh optimal, dan memberikan panen yang berkualitas. Kami dari tim
        <strong>GARDENA</strong>-AI akan terus berusaha meningkatkan sistem ini supaya semakin
        membantu petani hidroponik di Indonesia.
    </p>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    @if($semuaOptimal)
    const labels = @json($chartLabels);
    new Chart(document.getElementById('stabilityChart'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                { label:'EC Optimal',         data:@json($chartEc),         borderColor:'#2d9a4f', borderWidth:2.5, pointRadius:4, pointBackgroundColor:'#2d9a4f', tension:0.3, fill:false },
                { label:'pH Optimal',         data:@json($chartPh),         borderColor:'#38bdf8', borderWidth:2.5, pointRadius:4, pointBackgroundColor:'#38bdf8', tension:0.3, fill:false },
                { label:'Suhu Optimal',       data:@json($chartSuhu),       borderColor:'#fb923c', borderWidth:2.5, pointRadius:4, pointBackgroundColor:'#fb923c', tension:0.3, fill:false },
                { label:'Kelembapan Optimal', data:@json($chartKelembapan), borderColor:'#a78bfa', borderWidth:2.5, pointRadius:4, pointBackgroundColor:'#a78bfa', tension:0.3, fill:false },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid:{ color:'rgba(0,0,0,0.04)' }, ticks:{ font:{size:11}, color:'#94a3b8' } },
                y: { min:0, max:100, grid:{ color:'rgba(0,0,0,0.04)' }, ticks:{ font:{size:11}, color:'#94a3b8', stepSize:25 } }
            }
        }
    });
    @endif

});
</script>
@endpush