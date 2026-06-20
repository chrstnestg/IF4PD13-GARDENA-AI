@extends('layouts.app')
@section('title', 'Monitoring')

@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Monitoring Sensor</h1>
    <p class="text-sm text-gray-500">Data real time sensor hidroponik — refresh setiap 5 detik</p>
</div>

<div class="flex items-center gap-4 mb-6">
    <p class="text-xs font-bold text-gray-600 uppercase tracking-widest">Pembacaan Sensor Terkini</p>
    <div class="flex-1 border-t border-gray-300"></div>
</div>

@php
    $phStatus = 'Normal'; $phColor = 'green';
    if($sensor && $sensor->ph < 5.5){ $phStatus = 'Terlalu Asam'; $phColor = 'red'; }
    elseif($sensor && $sensor->ph > 6.5){ $phStatus = 'Terlalu Basa'; $phColor = 'yellow'; }

    $tdsStatus = 'Normal'; $tdsColor = 'green';
    if($sensor && $sensor->ec_tds < 800){ $tdsStatus = 'Rendah'; $tdsColor = 'red'; }
    elseif($sensor && $sensor->ec_tds > 1400){ $tdsStatus = 'Tinggi'; $tdsColor = 'yellow'; }

    $suhuStatus = 'Normal'; $suhuColor = 'green';
    if($sensor && $sensor->suhu < 20){ $suhuStatus = 'Dingin'; $suhuColor = 'red'; }
    elseif($sensor && $sensor->suhu > 30){ $suhuStatus = 'Panas'; $suhuColor = 'red'; }
@endphp

{{-- 3 SENSOR CARDS --}}
<div class="grid grid-cols-3 gap-5 mb-8">

    {{-- SUHU --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-temperature-half text-orange-400"></i>
                </div>
                <span class="text-sm font-semibold text-gray-700">Suhu Air</span>
            </div>
            <span class="text-xs text-{{ $suhuColor }}-600 border border-{{ $suhuColor }}-300 bg-{{ $suhuColor }}-50 rounded-full px-2 py-0.5">
                {{ $suhuStatus }}
            </span>
        </div>
        <p id="suhu-value" class="text-4xl font-extrabold text-gray-800 mb-1">
            @if($sensor && $sensor->suhu > -100)
                {{ number_format($sensor->suhu, 1) }}
            @else --
            @endif
            <span class="text-lg font-semibold">°C</span>
        </p>
        <div class="w-full h-1.5 bg-gray-100 rounded-full mt-3 mb-1">
            <div class="h-1.5 bg-orange-400 rounded-full" style="width: {{ $sensor ? min(($sensor->suhu / 40) * 100, 100) : 0 }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400">
            <span>0°C</span>
            <span>Optimal: 20°C - 30°C</span>
            <span>40°C</span>
        </div>
    </div>

    {{-- TDS --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-bolt-lightning text-yellow-400"></i>
                </div>
                <span class="text-sm font-semibold text-gray-700">TDS Larutan</span>
            </div>
            <span class="text-xs text-{{ $tdsColor }}-600 border border-{{ $tdsColor }}-300 bg-{{ $tdsColor }}-50 rounded-full px-2 py-0.5">
                {{ $tdsStatus }}
            </span>
        </div>
        <p id="tds-value" class="text-4xl font-extrabold text-gray-800 mb-1">
            @if($sensor)
                {{ number_format($sensor->ec_tds, 0) }}
            @else --
            @endif
            <span class="text-lg font-semibold">ppm</span>
        </p>
        <div class="w-full h-1.5 bg-gray-100 rounded-full mt-3 mb-1">
            <div class="h-1.5 bg-yellow-400 rounded-full" style="width: {{ $sensor ? min(($sensor->ec_tds / 2000) * 100, 100) : 0 }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400">
            <span>0</span>
            <span>Optimal: 800 - 1400 ppm</span>
            <span>2000</span>
        </div>
    </div>

    {{-- PH --}}
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fa-solid fa-flask text-green-400"></i>
                </div>
                <span class="text-sm font-semibold text-gray-700">pH Larutan</span>
            </div>
            <span class="text-xs text-{{ $phColor }}-600 border border-{{ $phColor }}-300 bg-{{ $phColor }}-50 rounded-full px-2 py-0.5">
                {{ $phStatus }}
            </span>
        </div>
        <p id="ph-value" class="text-4xl font-extrabold text-gray-800 mb-1">
            @if($sensor)
                {{ number_format($sensor->ph, 2) }}
            @else --
            @endif
            <span class="text-lg font-semibold">pH</span>
        </p>
        <div class="w-full h-1.5 bg-gray-100 rounded-full mt-3 mb-1">
            <div class="h-1.5 bg-green-400 rounded-full" style="width: {{ $sensor ? min(($sensor->ph / 14) * 100, 100) : 0 }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400">
            <span>0</span>
            <span>Optimal: 5.5 - 6.5</span>
            <span>14</span>
        </div>
    </div>

</div>

{{-- CHART + ALERT PANEL --}}
<div class="grid grid-cols-3 gap-6 mb-6">

    {{-- CHART --}}
    <div class="col-span-2 bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-gray-500"></i>
                <p class="text-sm font-semibold text-gray-700">Grafik Sensor Real-Time</p>
            </div>
            <span class="flex items-center gap-1 text-xs text-green-600 font-medium">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                Live
            </span>
        </div>
        <canvas id="sensorChart" height="120"></canvas>
    </div>

    {{-- ALERT PANEL --}}
    <div class="flex flex-col gap-4">

        {{-- Info Update --}}
        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
            <div class="flex items-center gap-2 mb-1">
                <i class="fa-solid fa-clock text-gray-400 text-xs"></i>
                <p class="text-xs font-semibold text-gray-600">Data Sensor Terbaru</p>
            </div>
            <p class="text-xs text-gray-400">Update terakhir: {{ now()->format('d M Y, H:i') }} WIB</p>
            <div class="mt-2 flex items-center gap-1">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                <span class="text-xs text-green-600 font-medium">Sensor aktif</span>
            </div>
        </div>

        {{-- Alert Terbaru --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex-1">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-bell text-yellow-500"></i>
                    <p class="text-sm font-semibold text-gray-700">Alert Terbaru</p>
                </div>
                <a href="#" class="text-xs text-green-600 font-semibold hover:underline flex items-center gap-1">
                    Kelola <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="space-y-4">

                    {{-- Alert pH --}}
                    @if($sensor && $sensor->ph < 5.5)
                    <div class="flex items-stretch gap-0 bg-red-50 border border-red-100 rounded-lg overflow-hidden">
                        <div class="w-1 bg-red-500 flex-shrink-0"></div>
                        <div class="px-3 py-2.5">
                            <p class="text-xs font-semibold text-gray-700">pH Terlalu Asam</p>
                            <p class="text-xs text-gray-400">{{ number_format($sensor->ph, 2) }} pH — Perlu perhatian</p>
                        </div>
                    </div>
                    @elseif($sensor && $sensor->ph > 6.5)
                    <div class="flex items-stretch gap-0 bg-yellow-50 border border-yellow-100 rounded-lg overflow-hidden">
                        <div class="w-1 bg-yellow-500 flex-shrink-0"></div>
                        <div class="px-3 py-2.5">
                            <p class="text-xs font-semibold text-gray-700">pH Terlalu Basa</p>
                            <p class="text-xs text-gray-400">{{ number_format($sensor->ph, 2) }} pH — Perlu perhatian</p>
                        </div>
                    </div>
                    @else
                    <div class="flex items-stretch gap-0 bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="w-1 bg-green-500 flex-shrink-0"></div>
                        <div class="px-3 py-2.5">
                            <p class="text-xs font-semibold text-gray-700">pH Normal</p>
                            <p class="text-xs text-gray-400">{{ $sensor ? number_format($sensor->ph, 2) : '--' }} pH — Kondisi baik</p>
                        </div>
                    </div>
                    @endif

                    {{-- Alert TDS --}}
                    @if($sensor && $sensor->ec_tds < 800)
                    <div class="flex items-stretch gap-0 bg-red-50 border border-red-100 rounded-lg overflow-hidden">
                        <div class="w-1 bg-red-500 flex-shrink-0"></div>
                        <div class="px-3 py-2.5">
                            <p class="text-xs font-semibold text-gray-700">TDS Terlalu Rendah</p>
                            <p class="text-xs text-gray-400">{{ number_format($sensor->ec_tds, 0) }} ppm — Tambah nutrisi</p>
                        </div>
                    </div>
                    @elseif($sensor && $sensor->ec_tds > 1400)
                    <div class="flex items-stretch gap-0 bg-yellow-50 border border-yellow-100 rounded-lg overflow-hidden">
                        <div class="w-1 bg-yellow-500 flex-shrink-0"></div>
                        <div class="px-3 py-2.5">
                            <p class="text-xs font-semibold text-gray-700">TDS Terlalu Tinggi</p>
                            <p class="text-xs text-gray-400">{{ number_format($sensor->ec_tds, 0) }} ppm — Encerkan larutan</p>
                        </div>
                    </div>
                    @else
                    <div class="flex items-stretch gap-0 bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="w-1 bg-green-500 flex-shrink-0"></div>
                        <div class="px-3 py-2.5">
                            <p class="text-xs font-semibold text-gray-700">TDS Normal</p>
                            <p class="text-xs text-gray-400">{{ $sensor ? number_format($sensor->ec_tds, 0) : '--' }} ppm — Kondisi baik</p>
                        </div>
                    </div>
                    @endif

                    {{-- Alert Suhu --}}
                    @if($sensor && $sensor->suhu > 30)
                    <div class="flex items-stretch gap-0 bg-red-50 border border-red-100 rounded-lg overflow-hidden">
                        <div class="w-1 bg-red-500 flex-shrink-0"></div>
                        <div class="px-3 py-2.5">
                            <p class="text-xs font-semibold text-gray-700">Suhu Terlalu Panas</p>
                            <p class="text-xs text-gray-400">{{ number_format($sensor->suhu, 1) }} °C — Perlu pendingin</p>
                        </div>
                    </div>
                    @elseif($sensor && $sensor->suhu < 20)
                    <div class="flex items-stretch gap-0 bg-blue-50 border border-blue-100 rounded-lg overflow-hidden">
                        <div class="w-1 bg-blue-500 flex-shrink-0"></div>
                        <div class="px-3 py-2.5">
                            <p class="text-xs font-semibold text-gray-700">Suhu Terlalu Dingin</p>
                            <p class="text-xs text-gray-400">{{ number_format($sensor->suhu, 1) }} °C — Perlu pemanas</p>
                        </div>
                    </div>
                    @else
                    <div class="flex items-stretch gap-0 bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="w-1 bg-green-500 flex-shrink-0"></div>
                        <div class="px-3 py-2.5">
                            <p class="text-xs font-semibold text-gray-700">Suhu Normal</p>
                            <p class="text-xs text-gray-400">{{ $sensor ? number_format($sensor->suhu, 1) : '--' }} °C — Kondisi baik</p>
                        </div>
                    </div>
                    @endif
            </div>
        </div>
    </div>
</div>

{{-- DATA TERBARU --}}
<div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-gray-700">Data Sensor Terbaru</h2>
        <span class="text-xs text-gray-400">{{ now()->format('d M Y H:i') }}</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100">
                    <th class="text-left py-2 text-xs text-gray-400 font-medium">Waktu</th>
                    <th class="text-left py-2 text-xs text-gray-400 font-medium">Suhu</th>
                    <th class="text-left py-2 text-xs text-gray-400 font-medium">TDS</th>
                    <th class="text-left py-2 text-xs text-gray-400 font-medium">pH</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $item)
                <tr class="border-b border-gray-50 hover:bg-gray-50 transition">
                    <td class="py-2 text-gray-600">{{ \Carbon\Carbon::parse($item->dibaca_pada)->format('H:i:s') }}</td>
                    <td class="py-2 text-gray-800 font-medium">{{ number_format($item->suhu, 1) }} °C</td>
                    <td class="py-2 text-gray-800 font-medium">{{ number_format($item->ec_tds, 0) }} ppm</td>
                    <td class="py-2 text-gray-800 font-medium">{{ number_format($item->ph, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('sensorChart').getContext('2d');
let sensorChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($history->pluck('dibaca_pada')->map(fn($item) => \Carbon\Carbon::parse($item)->format('H:i:s'))),
        datasets: [
            { label: 'Suhu (°C)', data: @json($history->pluck('suhu')), borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,0.1)', tension: 0.4, fill: false },
            { label: 'TDS (ppm)', data: @json($history->pluck('ec_tds')), borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,0.1)', tension: 0.4, fill: false },
            { label: 'pH', data: @json($history->pluck('ph')), borderColor: '#a855f7', backgroundColor: 'rgba(168,85,247,0.1)', tension: 0.4, fill: false }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            x: { grid: { display: false } },
            y: { grid: { color: '#f3f4f6' } }
        }
    }
});

async function updateSensor() {
    try {
        const response = await fetch('/api/latest-sensor');
        const data = await response.json();
        document.getElementById('suhu-value').innerHTML = `${parseFloat(data.suhu).toFixed(1)}<span class="text-lg font-semibold">°C</span>`;
        document.getElementById('tds-value').innerHTML = `${parseFloat(data.ec_tds).toFixed(0)}<span class="text-lg font-semibold">ppm</span>`;
        document.getElementById('ph-value').innerHTML = `${parseFloat(data.ph).toFixed(2)}<span class="text-lg font-semibold">pH</span>`;
        const timeNow = new Date().toLocaleTimeString();
        sensorChart.data.labels.push(timeNow);
        sensorChart.data.datasets[0].data.push(data.suhu);
        sensorChart.data.datasets[1].data.push(data.ec_tds);
        sensorChart.data.datasets[2].data.push(data.ph);
        if(sensorChart.data.labels.length > 10){
            sensorChart.data.labels.shift();
            sensorChart.data.datasets.forEach(d => d.data.shift());
        }
        sensorChart.update();
    } catch(error) {
        console.log('Gagal mengambil data sensor');
    }
}

setInterval(updateSensor, 5000);
</script>
@endpush