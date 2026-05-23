@extends('layouts.app')
@section('title', 'Monitoring')

@section('content')

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-xl font-bold text-gray-800">Monitoring Sensor</h1>
        <p class="text-sm text-gray-500">Data real time dari 5 sensor aktif - refresh setiap 5 detik</p>
    </div>

    {{-- Section Label --}}
    <div class="flex items-center gap-4 mb-6">
        <p class="text-xs font-bold text-gray-600 uppercase tracking-widest">Pembacaan Sensor Terkini</p>
        <div class="flex-1 border-t border-gray-300"></div>
    </div>

    {{-- 4 SENSOR CARDS --}}
    <div class="grid grid-cols-4 gap-5 mb-8">

        {{-- Card Suhu --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <span class="text-gray-500 text-xs">°C</span>
                    <span class="text-sm font-semibold text-gray-700">Suhu Udara</span>
                </div>
                <span class="text-xs text-green-600 border border-green-300 rounded-full px-2 py-0.5">Normal</span>
            </div>
            <p class="text-4xl font-extrabold text-gray-800 mb-1">26.4<span class="text-lg font-semibold">°C</span></p>
            <div class="relative w-full h-2 rounded-full mb-1 overflow-hidden"
                 style="background: linear-gradient(to right, #3b82f6, #22c55e, #f59e0b, #ef4444);">
                <div class="absolute top-[-2px] h-4 w-1 bg-white border border-gray-400 rounded" style="left: 54%;"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-400 mb-3">
                <span>10°</span><span>Range Optimal</span><span>40°</span>
            </div>
            <div class="flex justify-between text-xs text-gray-500 border-t border-gray-100 pt-3">
                <div><p class="text-gray-400">MIN 24H</p><p class="font-semibold">22.1°</p></div>
                <div><p class="text-gray-400">MAX 24H</p><p class="font-semibold">29.8°</p></div>
                <div><p class="text-gray-400">AVG</p><p class="font-semibold">25.6°</p></div>
            </div>
        </div>

        {{-- Card Kelembapan --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-temperature-half text-orange-400"></i>
                    <span class="text-sm font-semibold text-gray-700">Kelembapan</span>
                </div>
                <span class="text-xs text-green-600 border border-green-300 rounded-full px-2 py-0.5">Normal</span>
            </div>
            <p class="text-4xl font-extrabold text-gray-800 mb-1">72<span class="text-lg font-semibold">%RH</span></p>
            <div class="relative w-full h-2 rounded-full mb-1 overflow-hidden"
                 style="background: linear-gradient(to right, #3b82f6, #22c55e, #f59e0b, #ef4444);">
                <div class="absolute top-[-2px] h-4 w-1 bg-white border border-gray-400 rounded" style="left: 72%;"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-400 mb-3">
                <span>0%</span><span>Target: 60-80%</span><span>100%</span>
            </div>
            <div class="flex justify-between text-xs text-gray-500 border-t border-gray-100 pt-3">
                <div><p class="text-gray-400">MIN 24H</p><p class="font-semibold">58%</p></div>
                <div><p class="text-gray-400">MAX 24H</p><p class="font-semibold">85%</p></div>
                <div><p class="text-gray-400">AVG</p><p class="font-semibold">70%</p></div>
            </div>
        </div>

        {{-- Card TDS --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-bolt-lightning text-yellow-400"></i>
                    <span class="text-sm font-semibold text-gray-700">TDS Larutan</span>
                </div>
                <span class="text-xs text-yellow-600 border border-yellow-300 rounded-full px-2 py-0.5">Peringatan</span>
            </div>
            <p class="text-4xl font-extrabold text-gray-800 mb-1">600<span class="text-lg font-semibold">ppm</span></p>
            <div class="relative w-full h-2 rounded-full mb-1 overflow-hidden"
                 style="background: linear-gradient(to right, #3b82f6, #22c55e, #f59e0b, #ef4444);">
                <div class="absolute top-[-2px] h-4 w-1 bg-white border border-gray-400 rounded" style="left: 43%;"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-400 mb-3">
                <span>0</span><span>Optimal: 800-1400</span><span>2000</span>
            </div>
            <div class="flex justify-between text-xs text-gray-500 border-t border-gray-100 pt-3">
                <div><p class="text-gray-400">MIN 24H</p><p class="font-semibold">550</p></div>
                <div><p class="text-gray-400">MAX 24H</p><p class="font-semibold">700</p></div>
                <div><p class="text-gray-400">TREND</p><p class="font-semibold text-red-500">Turun</p></div>
            </div>
        </div>

        {{-- Card pH --}}
        <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-flask text-green-400"></i>
                    <span class="text-sm font-semibold text-gray-700">pH Larutan</span>
                </div>
                <span class="text-xs text-orange-500 border border-orange-300 rounded-full px-2 py-0.5">Perhatian</span>
            </div>
            <p class="text-4xl font-extrabold text-gray-800 mb-1">4.0<span class="text-lg font-semibold">pH</span></p>
            <div class="relative w-full h-2 rounded-full mb-1 overflow-hidden"
                 style="background: linear-gradient(to right, #3b82f6, #22c55e, #f59e0b, #ef4444);">
                <div class="absolute top-[-2px] h-4 w-1 bg-white border border-gray-400 rounded" style="left: 29%;"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-400 mb-3">
                <span>0</span><span>Optimal: 5.5 - 6.5</span><span>14</span>
            </div>
            <div class="flex justify-between text-xs text-gray-500 border-t border-gray-100 pt-3">
                <div><p class="text-gray-400">MIN 24H</p><p class="font-semibold">3.9</p></div>
                <div><p class="text-gray-400">MAX 24H</p><p class="font-semibold">4.5</p></div>
                <div><p class="text-gray-400">TREND</p><p class="font-semibold text-blue-500">Turun</p></div>
            </div>
        </div>

    </div>

    {{-- BOTTOM: Chart + Alert --}}
    <div class="grid grid-cols-3 gap-6">

        {{-- Grafik --}}
        <div class="col-span-2 bg-white rounded-xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-4">
                <i class="fa-solid fa-chart-bar text-gray-500"></i>
                <p class="text-sm font-semibold text-gray-700">Data Sensor Real-Time</p>
            </div>
            <canvas id="sensorChart" height="120"></canvas>
        </div>

        {{-- Alert Panel --}}
        <div class="flex flex-col gap-4">
            <div class="bg-gray-100 rounded-xl p-5">
                <p class="text-sm font-semibold text-gray-700">Data Sensor Terbaru</p>
                <p class="text-xs text-gray-500 mt-1">Update terakhir: {{ now()->format('d M Y, H:i') }} WIB</p>
            </div>
            <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100 flex-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-bell text-yellow-500"></i>
                        <p class="text-sm font-semibold text-gray-700">Alert Terbaru</p>
                    </div>
                    <a href="{{ route('rekomendasi') }}"
                       class="text-xs text-green-600 font-semibold hover:underline">
                        Kelola →
                    </a>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                        <div class="w-1 h-8 bg-red-400 rounded"></div>
                        <div>
                            <p class="text-xs font-semibold text-gray-700">TDS Larutan Rendah</p>
                            <p class="text-xs text-gray-400">600 ppm — di bawah optimal</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                        <div class="w-1 h-8 bg-red-400 rounded"></div>
                        <div>
                            <p class="text-xs font-semibold text-gray-700">pH Larutan Rendah</p>
                            <p class="text-xs text-gray-400">4.0 pH — terlalu asam</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-3 py-2">
                        <div class="w-1 h-8 bg-green-400 rounded"></div>
                        <div>
                            <p class="text-xs font-semibold text-gray-700">Kelembapan Normal</p>
                            <p class="text-xs text-gray-400">72% RH — dalam range</p>
                        </div>
                    </div>
                </div>
            </div>
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
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['13:00','13:30','14:00','14:30','15:00','15:30','16:00'],
        datasets: [
            { label:'Suhu (°C)',      data:[25.1,25.8,26.2,26.4,26.0,25.7,26.1], borderColor:'#f59e0b', tension:0.4, fill:false },
            { label:'Kelembapan (%)', data:[68,70,72,75,73,71,72],               borderColor:'#3b82f6', tension:0.4, fill:false },
            { label:'TDS (ppm)',      data:[620,600,580,600,590,610,600],         borderColor:'#22c55e', tension:0.4, fill:false },
            { label:'pH',            data:[4.1,4.0,4.0,3.9,4.0,4.1,4.0],        borderColor:'#a855f7', tension:0.4, fill:false },
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position:'top', labels:{ font:{ size:11 } } } },
        scales: {
            x: { grid:{ display:false } },
            y: { grid:{ color:'#f3f4f6' } }
        }
    }
});
</script>
@endpush