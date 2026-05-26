@extends('layouts.app')
@section('title', 'Monitoring')

@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Monitoring Sensor</h1>
    <p class="text-sm text-gray-500">
        Data real time sensor hidroponik
    </p>
</div>

<div class="flex items-center gap-4 mb-6">
    <p class="text-xs font-bold text-gray-600 uppercase tracking-widest">
        Pembacaan Sensor Terkini
    </p>
    <div class="flex-1 border-t border-gray-300"></div>
</div>

@php

    // =========================
    // STATUS PH
    // =========================
    $phStatus = 'Normal';
    $phColor  = 'green';

    if($sensor && $sensor->ph < 5.5){
        $phStatus = 'Terlalu Asam';
        $phColor  = 'red';
    }
    elseif($sensor && $sensor->ph > 6.5){
        $phStatus = 'Terlalu Basa';
        $phColor  = 'yellow';
    }

    // =========================
    // STATUS TDS
    // =========================
    $tdsStatus = 'Normal';
    $tdsColor  = 'green';

    if($sensor && $sensor->ec_tds < 800){
        $tdsStatus = 'Rendah';
        $tdsColor  = 'red';
    }
    elseif($sensor && $sensor->ec_tds > 1400){
        $tdsStatus = 'Tinggi';
        $tdsColor  = 'yellow';
    }

    // =========================
    // STATUS SUHU
    // =========================
    $suhuStatus = 'Normal';
    $suhuColor  = 'green';

    if($sensor && $sensor->suhu < 20){
        $suhuStatus = 'Dingin';
        $suhuColor  = 'blue';
    }
    elseif($sensor && $sensor->suhu > 30){
        $suhuStatus = 'Panas';
        $suhuColor  = 'red';
    }

@endphp

<div class="grid grid-cols-3 gap-5 mb-8">

    {{-- SUHU --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">

        <div class="flex items-center justify-between mb-3">

            <div class="flex items-center gap-2">
                <i class="fa-solid fa-temperature-half text-orange-400"></i>

                <span class="text-sm font-semibold text-gray-700">
                    Suhu Air
                </span>
            </div>

            <span class="text-xs text-{{ $suhuColor }}-600 border border-{{ $suhuColor }}-300 rounded-full px-2 py-0.5">
                {{ $suhuStatus }}
            </span>

        </div>

        <p id="suhu-value"class="text-4xl font-extrabold text-gray-800 mb-1">

            @if($sensor && $sensor->suhu > -100)
                {{ number_format($sensor->suhu, 1) }}
            @else
                --
            @endif

            <span class="text-lg font-semibold">°C</span>
        </p>

        <div class="text-xs text-gray-400 mt-2">
            Optimal: 20°C - 30°C
        </div>

    </div>

    {{-- TDS --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">

        <div class="flex items-center justify-between mb-3">

            <div class="flex items-center gap-2">
                <i class="fa-solid fa-bolt-lightning text-yellow-400"></i>

                <span class="text-sm font-semibold text-gray-700">
                    TDS Larutan
                </span>
            </div>

            <span class="text-xs text-{{ $tdsColor }}-600 border border-{{ $tdsColor }}-300 rounded-full px-2 py-0.5">
                {{ $tdsStatus }}
            </span>

        </div>

        <p id="tds-value"class="text-4xl font-extrabold text-gray-800 mb-1">

            @if($sensor)
                {{ number_format($sensor->ec_tds, 0) }}
            @else
                --
            @endif

            <span class="text-lg font-semibold">ppm</span>
        </p>

        <div class="text-xs text-gray-400 mt-2">
            Optimal: 800 - 1400 ppm
        </div>

    </div>

    {{-- PH --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">

        <div class="flex items-center justify-between mb-3">

            <div class="flex items-center gap-2">
                <i class="fa-solid fa-flask text-green-400"></i>

                <span class="text-sm font-semibold text-gray-700">
                    pH Larutan
                </span>
            </div>

            <span class="text-xs text-{{ $phColor }}-600 border border-{{ $phColor }}-300 rounded-full px-2 py-0.5">
                {{ $phStatus }}
            </span>

        </div>

        <p id="ph-value"class="text-4xl font-extrabold text-gray-800 mb-1">

            @if($sensor)
                {{ number_format($sensor->ph, 2) }}
            @else
                --
            @endif

            <span class="text-lg font-semibold">pH</span>
        </p>

        <div class="text-xs text-gray-400 mt-2">
            Optimal: 5.5 - 6.5 pH
        </div>

    </div>

</div>

{{-- CHART --}}
<div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 mb-6">

    <div class="flex items-center gap-2 mb-4">
        <i class="fa-solid fa-chart-line text-gray-500"></i>

        <p class="text-sm font-semibold text-gray-700">
            Grafik Sensor
        </p>
    </div>

    <canvas id="sensorChart" height="100"></canvas>

</div>

{{-- DATA TERBARU --}}
<div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">

    <div class="flex items-center justify-between mb-4">

        <h2 class="text-sm font-semibold text-gray-700">
            Data Sensor Terbaru
        </h2>

        <span class="text-xs text-gray-400">
            {{ now()->format('d M Y H:i') }}
        </span>

    </div>

    <div class="overflow-x-auto">

        <table class="w-full text-sm">

            <thead>
                <tr class="border-b">
                    <th class="text-left py-2">Waktu</th>
                    <th class="text-left py-2">Suhu</th>
                    <th class="text-left py-2">TDS</th>
                    <th class="text-left py-2">pH</th>
                </tr>
            </thead>

            <tbody>

                @foreach($history as $item)

                <tr class="border-b">

                    <td class="py-2">
                        {{ \Carbon\Carbon::parse($item->dibaca_pada)->format('H:i:s') }}
                    </td>

                    <td class="py-2">
                        {{ number_format($item->suhu, 1) }} °C
                    </td>

                    <td class="py-2">
                        {{ number_format($item->ec_tds, 0) }} ppm
                    </td>

                    <td class="py-2">
                        {{ number_format($item->ph, 2) }}
                    </td>

                </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection

@push('styles')

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

@endpush

@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const ctx = document.getElementById('sensorChart').getContext('2d');

let sensorChart = new Chart(ctx, {

    type: 'line',

    data: {

        labels: @json(
            $history->pluck('dibaca_pada')->map(fn($item) =>
                \Carbon\Carbon::parse($item)->format('H:i:s')
            )
        ),

        datasets: [

            {
                label: 'Suhu',
                data: @json($history->pluck('suhu')),
                borderColor: '#f59e0b',
                tension: 0.4,
                fill: false
            },

            {
                label: 'TDS',
                data: @json($history->pluck('ec_tds')),
                borderColor: '#22c55e',
                tension: 0.4,
                fill: false
            },

            {
                label: 'pH',
                data: @json($history->pluck('ph')),
                borderColor: '#a855f7',
                tension: 0.4,
                fill: false
            }

        ]
    },

    options: {

        responsive: true,

        plugins: {
            legend: {
                position: 'top'
            }
        },

        scales: {
            x: {
                grid: {
                    display: false
                }
            },

            y: {
                grid: {
                    color: '#f3f4f6'
                }
            }
        }
    }
});


// =============================
// AUTO REFRESH SENSOR
// =============================

async function updateSensor() {

    try {

        const response = await fetch('/api/latest-sensor');

        const data = await response.json();

        // =============================
        // UPDATE CARD
        // =============================

        document.getElementById('suhu-value').innerHTML =
            `${parseFloat(data.suhu).toFixed(1)}
            <span class="text-lg font-semibold">°C</span>`;

        document.getElementById('tds-value').innerHTML =
            `${parseFloat(data.ec_tds).toFixed(0)}
            <span class="text-lg font-semibold">ppm</span>`;

        document.getElementById('ph-value').innerHTML =
            `${parseFloat(data.ph).toFixed(2)}
            <span class="text-lg font-semibold">pH</span>`;

        // =============================
        // UPDATE CHART
        // =============================

        const timeNow = new Date().toLocaleTimeString();

        sensorChart.data.labels.push(timeNow);

        sensorChart.data.datasets[0].data.push(data.suhu);
        sensorChart.data.datasets[1].data.push(data.ec_tds);
        sensorChart.data.datasets[2].data.push(data.ph);

        // maksimal 10 data
        if(sensorChart.data.labels.length > 10){

            sensorChart.data.labels.shift();

            sensorChart.data.datasets[0].data.shift();
            sensorChart.data.datasets[1].data.shift();
            sensorChart.data.datasets[2].data.shift();
        }

        sensorChart.update();

    }
    catch(error){

        console.log('Gagal mengambil data sensor');

    }
}

// refresh tiap 5 detik
setInterval(updateSensor, 5000);

</script>

@endpush