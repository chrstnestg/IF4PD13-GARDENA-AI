@extends('layouts.app')
@section('title', 'Monitoring')

@section('content')

<div class="mb-6">
    <h1 class="text-xl font-bold text-gray-800">Monitoring Sensor</h1>
    <p class="text-sm text-gray-500">Data real time sensor hidroponik — refresh setiap 5 detik</p>
</div>

@if(!$sensorAktif)
<div id="sensor-inactive-banner" class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-center gap-3">
@else
<div id="sensor-inactive-banner" class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-center gap-3" style="display:none">
@endif
    <i class="fa-solid fa-triangle-exclamation text-red-500 text-lg"></i>
    <div>
        <p class="text-sm font-semibold text-red-700">Sensor Tidak Aktif</p>
        <p class="text-xs text-red-500">Tidak ada data masuk dalam 5 menit terakhir. Periksa koneksi hardware IoT Anda.</p>
    </div>
</div>

<div class="flex items-center gap-4 mb-6">
    <p class="text-xs font-bold text-gray-600 uppercase tracking-widest">Pembacaan Sensor Terkini</p>
    <div class="flex-1 border-t border-gray-300"></div>
</div>

@php
    $phStatus = 'Normal'; $phColor = 'green';
    if($sensor && $sensor->ph < 6.0){ $phStatus = 'Terlalu Asam'; $phColor = 'red'; }
    elseif($sensor && $sensor->ph > 8.0){ $phStatus = 'Terlalu Basa'; $phColor = 'yellow'; }

    $tdsStatus = 'Normal'; $tdsColor = 'green';
    if($sensor && $sensor->ec_tds < 400){ $tdsStatus = 'Rendah'; $tdsColor = 'red'; }
    elseif($sensor && $sensor->ec_tds > 1200){ $tdsStatus = 'Tinggi'; $tdsColor = 'yellow'; }

    $suhuStatus = 'Normal'; $suhuColor = 'green';
    if($sensor && $sensor->suhu < 20){ $suhuStatus = 'Dingin'; $suhuColor = 'blue'; }
    elseif($sensor && $sensor->suhu > 28){ $suhuStatus = 'Panas'; $suhuColor = 'red'; }
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
            <span id="suhu-badge" class="text-xs text-{{ $suhuColor }}-600 border border-{{ $suhuColor }}-300 bg-{{ $suhuColor }}-50 rounded-full px-2 py-0.5 font-semibold">
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
            <div id="suhu-progress" class="h-1.5 bg-orange-400 rounded-full" style="width: {{ $sensor ? min(($sensor->suhu / 40) * 100, 100) : 0 }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400">
            <span>0°C</span>
            <span class="font-medium text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">Optimal: 20°C - 28°C</span>
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
            <span id="tds-badge" class="text-xs text-{{ $tdsColor }}-600 border border-{{ $tdsColor }}-300 bg-{{ $tdsColor }}-50 rounded-full px-2 py-0.5 font-semibold">
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
            <div id="tds-progress" class="h-1.5 bg-yellow-400 rounded-full" style="width: {{ $sensor ? min(($sensor->ec_tds / 2000) * 100, 100) : 0 }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400">
            <span>0</span>
            <span class="font-medium text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">Optimal: 400 - 1200 ppm</span>
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
            <span id="ph-badge" class="text-xs text-{{ $phColor }}-600 border border-{{ $phColor }}-300 bg-{{ $phColor }}-50 rounded-full px-2 py-0.5 font-semibold">
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
            <div id="ph-progress" class="h-1.5 bg-green-400 rounded-full" style="width: {{ $sensor ? min(($sensor->ph / 14) * 100, 100) : 0 }}%"></div>
        </div>
        <div class="flex justify-between text-xs text-gray-400">
            <span>0</span>
            <span class="font-medium text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">Optimal: 6.0 - 8.0</span>
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
                <span id="chart-status-pulse" class="w-2 h-2 {{ $sensorAktif ? 'bg-green-500 animate-pulse' : 'bg-red-500' }} rounded-full"></span>
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
            <p id="latest-update-text" class="text-xs text-gray-400">
                Update terakhir: {{ $sensor ? \Carbon\Carbon::parse($sensor->dibaca_pada)->format('d M Y, H:i') : '-' }} WIB
            </p>
            <div id="status-dot-container" class="mt-2 flex items-center gap-1">
                @if($sensorAktif)
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-xs text-green-600 font-medium">Sensor aktif</span>
                @else
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    <span class="text-xs text-red-600 font-medium">Sensor tidak aktif</span>
                @endif
            </div>
        </div>

        {{-- Alert Terbaru --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex-1">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-bell text-gray-700"></i>
                    <p class="text-sm font-semibold text-gray-700">Pemberitahuan Sistem</p>
                </div>
                <a href="{{ route('rekomendasi') }}" class="text-xs text-green-600 font-semibold hover:underline flex items-center gap-1">
                    Kelola <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div id="alert-list-container" class="space-y-4">
                {{-- Alert pH --}}
                @if($sensor && $sensor->ph < 6.0)
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-lg p-3">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-base flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-bold text-red-800">pH Terlalu Asam (Batas: 6.0 - 8.0)</p>
                        <p class="text-xs text-red-700 font-medium">Kondisi: {{ number_format($sensor->ph, 2) }} pH — Perlu perhatian!</p>
                    </div>
                </div>
                @elseif($sensor && $sensor->ph > 8.0)
                <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                    <i class="fa-solid fa-triangle-exclamation text-yellow-600 text-base flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-bold text-yellow-800">pH Terlalu Basa (Batas: 6.0 - 8.0)</p>
                        <p class="text-xs text-yellow-700 font-medium">Kondisi: {{ number_format($sensor->ph, 2) }} pH — Perlu perhatian!</p>
                    </div>
                </div>
                @else
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-lg p-3">
                    <i class="fa-solid fa-circle-check text-green-500 text-base flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-semibold text-green-800">pH Normal</p>
                        <p class="text-xs text-green-600">{{ $sensor ? number_format($sensor->ph, 2) : '--' }} pH — Kondisi baik</p>
                    </div>
                </div>
                @endif

                {{-- Alert TDS --}}
                @if($sensor && $sensor->ec_tds < 400)
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-lg p-3">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-base flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-bold text-red-800">TDS Terlalu Rendah (Batas: 400 - 1200)</p>
                        <p class="text-xs text-red-700 font-medium">Kondisi: {{ number_format($sensor->ec_tds, 0) }} ppm — Tambah nutrisi!</p>
                    </div>
                </div>
                @elseif($sensor && $sensor->ec_tds > 1200)
                <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                    <i class="fa-solid fa-triangle-exclamation text-yellow-600 text-base flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-bold text-yellow-800">TDS Terlalu Tinggi (Batas: 400 - 1200)</p>
                        <p class="text-xs text-yellow-700 font-medium">Kondisi: {{ number_format($sensor->ec_tds, 0) }} ppm — Encerkan larutan!</p>
                    </div>
                </div>
                @else
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-lg p-3">
                    <i class="fa-solid fa-circle-check text-green-500 text-base flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-semibold text-green-800">TDS Normal</p>
                        <p class="text-xs text-green-600">{{ $sensor ? number_format($sensor->ec_tds, 0) : '--' }} ppm — Kondisi baik</p>
                    </div>
                </div>
                @endif

                {{-- Alert Suhu --}}
                @if($sensor && $sensor->suhu > 28)
                <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-lg p-3">
                    <i class="fa-solid fa-circle-exclamation text-red-500 text-base flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-bold text-red-800">Suhu Terlalu Panas (Batas: 20 - 28)</p>
                        <p class="text-xs text-red-700 font-medium">Kondisi: {{ number_format($sensor->suhu, 1) }} °C — Perlu pendingin!</p>
                    </div>
                </div>
                @elseif($sensor && $sensor->suhu < 20)
                <div class="flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <i class="fa-solid fa-circle-exclamation text-blue-500 text-base flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-bold text-blue-800">Suhu Terlalu Dingin (Batas: 20 - 28)</p>
                        <p class="text-xs text-blue-700 font-medium">Kondisi: {{ number_format($sensor->suhu, 1) }} °C — Perlu pemanas!</p>
                    </div>
                </div>
                @else
                <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-lg p-3">
                    <i class="fa-solid fa-circle-check text-green-500 text-base flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-semibold text-green-800">Suhu Normal</p>
                        <p class="text-xs text-green-600">{{ $sensor ? number_format($sensor->suhu, 1) : '--' }} °C — Kondisi baik</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- DATA TERBARU TABLE --}}
<div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-gray-700">Data Terakhir Riwayat Sensor</h2>
        <span class="text-xs text-gray-400">{{ now()->format('d M Y H:i') }} WIB</span>
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
            <tbody id="sensor-table-body">
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
        
        if (!data) return;

        if (data.ph < 0 || data.suhu < 0 || data.ec_tds < 0) {
            console.log('Data sensor tidak valid (terdapat nilai negatif). Update UI dilewati.');
            return;
        }

        const dibacaPada = new Date(data.dibaca_pada);
        const sekarang = new Date();
        const selisihMenit = (sekarang - dibacaPada) / 1000 / 60;

        const bannerEl = document.getElementById('sensor-inactive-banner');
        const dotContainer = document.getElementById('status-dot-container');
        const pulseChartDot = document.getElementById('chart-status-pulse');
        const updateTextEl = document.getElementById('latest-update-text');

        if (selisihMenit >= 5) {
            if (bannerEl) bannerEl.style.display = 'flex';
            if (pulseChartDot) { pulseChartDot.className = "w-2 h-2 bg-red-500"; }
            if (dotContainer) {
                dotContainer.innerHTML = `
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                    <span class="text-xs text-red-600 font-medium">Sensor tidak aktif</span>
                `;
            }
            return; 
        } else {
            if (bannerEl) bannerEl.style.display = 'none';
            if (pulseChartDot) { pulseChartDot.className = "w-2 h-2 bg-green-500 animate-pulse"; }
            if (dotContainer) {
                dotContainer.innerHTML = `
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    <span class="text-xs text-green-600 font-medium">Sensor aktif</span>
                `;
            }
        }

        const ph = parseFloat(data.ph);
        const suhu = parseFloat(data.suhu);
        const tds = parseFloat(data.ec_tds);

        document.getElementById('suhu-value').innerHTML = `${suhu.toFixed(1)}<span class="text-lg font-semibold">°C</span>`;
        document.getElementById('tds-value').innerHTML = `${tds.toFixed(0)}<span class="text-lg font-semibold">ppm</span>`;
        document.getElementById('ph-value').innerHTML = `${ph.toFixed(2)}<span class="text-lg font-semibold">pH</span>`;

        document.getElementById('suhu-progress').style.width = `${Math.min((suhu / 40) * 100, 100)}%`;
        document.getElementById('tds-progress').style.width = `${Math.min((tds / 2000) * 100, 100)}%`;
        document.getElementById('ph-progress').style.width = `${Math.min((ph / 14) * 100, 100)}%`;

        if (updateTextEl) {
            const timeFormat = { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
            updateTextEl.innerHTML = `Update terakhir: ${dibacaPada.toLocaleDateString('id-ID', timeFormat)} WIB`;
        }

        // 🔴 UPDATE DYNAMIC BADGES & ALERTS DENGAN STYLE KONTRAST
        updateStatusBadgesAndAlerts(suhu, tds, ph);

        const timeNow = dibacaPada.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        sensorChart.data.labels.push(timeNow);
        sensorChart.data.datasets[0].data.push(suhu);
        sensorChart.data.datasets[1].data.push(tds);
        sensorChart.data.datasets[2].data.push(ph);

        if (sensorChart.data.labels.length > 10) {
            sensorChart.data.labels.shift();
            sensorChart.data.datasets.forEach(d => d.data.shift());
        }
        sensorChart.update();

    } catch(error) {
        console.log('Gagal mengambil data sensor via API:', error);
    }
}

function updateStatusBadgesAndAlerts(suhu, tds, ph) {
    // Logic Suhu
    let suhuStatus = 'Normal', suhuColor = 'green', suhuAlertHtml = '';
    if(suhu < 20) { 
        suhuStatus = 'Dingin'; suhuColor = 'blue'; 
        suhuAlertHtml = `
            <div class="flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                <i class="fa-solid fa-circle-exclamation text-blue-500 text-base flex-shrink-0"></i>
                <div>
                    <p class="text-xs font-bold text-blue-800">Suhu Terlalu Dingin (Batas: 20 - 28)</p>
                    <p class="text-xs text-blue-700 font-medium">Kondisi: ${suhu.toFixed(1)} °C — Perlu pemanas!</p>
                </div>
            </div>`;
    } else if(suhu > 28) { 
        suhuStatus = 'Panas'; suhuColor = 'red'; 
        suhuAlertHtml = `
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-lg p-3">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-base flex-shrink-0"></i>
                <div>
                    <p class="text-xs font-bold text-red-800">Suhu Terlalu Panas (Batas: 20 - 28)</p>
                    <p class="text-xs text-red-700 font-medium">Kondisi: ${suhu.toFixed(1)} °C — Perlu pendingin!</p>
                </div>
            </div>`;
    } else {
        suhuAlertHtml = `
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-lg p-3">
                <i class="fa-solid fa-circle-check text-green-500 text-base flex-shrink-0"></i>
                <div>
                    <p class="text-xs font-semibold text-green-800">Suhu Normal</p>
                    <p class="text-xs text-green-600">${suhu.toFixed(1)} °C — Kondisi baik</p>
                </div>
            </div>`;
    }

    // Logic TDS
    let tdsStatus = 'Normal', tdsColor = 'green', tdsAlertHtml = '';
    if(tds < 400) { 
        tdsStatus = 'Rendah'; tdsColor = 'red'; 
        tdsAlertHtml = `
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-lg p-3">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-base flex-shrink-0"></i>
                <div>
                    <p class="text-xs font-bold text-red-800">TDS Terlalu Rendah (Batas: 400 - 1200)</p>
                    <p class="text-xs text-red-700 font-medium">Kondisi: ${tds.toFixed(0)} ppm — Tambah nutrisi!</p>
                </div>
            </div>`;
    } else if(tds > 1200) { 
        tdsStatus = 'Tinggi'; tdsColor = 'yellow'; 
        tdsAlertHtml = `
            <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                <i class="fa-solid fa-triangle-exclamation text-yellow-600 text-base flex-shrink-0"></i>
                <div>
                    <p class="text-xs font-bold text-yellow-800">TDS Terlalu Tinggi (Batas: 400 - 1200)</p>
                    <p class="text-xs text-yellow-700 font-medium">Kondisi: ${tds.toFixed(0)} ppm — Encerkan larutan!</p>
                </div>
            </div>`;
    } else {
        tdsAlertHtml = `
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-lg p-3">
                <i class="fa-solid fa-circle-check text-green-500 text-base flex-shrink-0"></i>
                <div>
                    <p class="text-xs font-semibold text-green-800">TDS Normal</p>
                    <p class="text-xs text-green-600">${tds.toFixed(0)} ppm — Kondisi baik</p>
                </div>
            </div>`;
    }

    // Logic pH
    let phStatus = 'Normal', phColor = 'green', phAlertHtml = '';
    if(ph < 6.0) { 
        phStatus = 'Terlalu Asam'; phColor = 'red'; 
        phAlertHtml = `
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 rounded-lg p-3">
                <i class="fa-solid fa-circle-exclamation text-red-500 text-base flex-shrink-0"></i>
                <div>
                    <p class="text-xs font-bold text-red-800">pH Terlalu Asam (Batas: 6.0 - 8.0)</p>
                    <p class="text-xs text-red-700 font-medium">Kondisi: ${ph.toFixed(2)} pH — Perlu perhatian!</p>
                </div>
            </div>`;
    } else if(ph > 8.0) { 
        phStatus = 'Terlalu Basa'; phColor = 'yellow'; 
        phAlertHtml = `
            <div class="flex items-center gap-3 bg-yellow-50 border border-yellow-300 rounded-lg p-3">
                <i class="fa-solid fa-triangle-exclamation text-yellow-600 text-base flex-shrink-0"></i>
                <div>
                    <p class="text-xs font-bold text-yellow-800">pH Terlalu Basa (Batas: 6.0 - 8.0)</p>
                    <p class="text-xs text-yellow-700 font-medium">Kondisi: ${ph.toFixed(2)} pH — Perlu perhatian!</p>
                </div>
            </div>`;
    } else {
        phAlertHtml = `
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-lg p-3">
                <i class="fa-solid fa-circle-check text-green-500 text-base flex-shrink-0"></i>
                <div>
                    <p class="text-xs font-semibold text-green-800">pH Normal</p>
                    <p class="text-xs text-green-600">${ph.toFixed(2)} pH — Kondisi baik</p>
                </div>
            </div>`;
    }

    updateBadge('suhu-badge', suhuStatus, suhuColor);
    updateBadge('tds-badge', tdsStatus, tdsColor);
    updateBadge('ph-badge', phStatus, phColor);

    // Render ulang kontainer alert kanan secara utuh dengan struktur baru
    document.getElementById('alert-list-container').innerHTML = phAlertHtml + tdsAlertHtml + suhuAlertHtml;
}

function updateBadge(id, status, color) {
    const el = document.getElementById(id);
    if(el) {
        el.className = `text-xs text-${color}-600 border border-${color}-300 bg-${color}-50 rounded-full px-2 py-0.5 font-semibold`;
        el.innerText = status;
    }
}

setInterval(updateSensor, 5000);
</script>
@endpush