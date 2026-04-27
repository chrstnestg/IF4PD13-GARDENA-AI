<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GARDENA-AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body>

    {{-- NAVBAR --}}
    <nav class="absolute top-0 left-0 w-full z-10 px-10">
        <div class="flex items-center justify-between py-1 border-b border-white/20">
            {{-- Logo --}}
            <div class="flex items-center">
                <img src="{{ asset('images/logo1.png') }}" alt="Gardena AI" class="h-20 w-auto object-contain">
            </div>

            {{-- Menu --}}
            <div class="flex items-center gap-8 text-white text-base font-medium">
                <a href="#" class="hover:text-green-400 transition">Dashboard</a>
                <a href="#" class="hover:text-green-400 transition">Monitoring</a>
                <a href="#" class="hover:text-green-400 transition">Rekomendasi</a>
                <a href="#" class="hover:text-green-400 transition">Riwayat</a>
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <div class="relative h-screen">

        {{-- Background Image --}}
        <div class="absolute inset-0 bg-cover bg-center"
            style="background-image: url('{{ asset('images/bg2.jpg') }}');">
            <div class="absolute inset-0 bg-black/50"></div>
        </div>

        {{-- Hero Content --}}
        <div class="relative z-10 h-full flex flex-col justify-center px-10 md:px-20 max-w-2xl pt-24">
            <h1 class="text-white text-5xl font-extrabold leading-tight mb-6">
                SMART HYDROPONIC<br>MONITORING SYSTEM
            </h1>
            <p class="text-gray-300 text-sm mb-10 max-w-md">
                Monitor kondisi tanaman hidroponik secara real-time dan dapatkan rekomendasi nutrisi otomatis untuk pertumbuhan optimal.
            </p>

            {{-- Stats --}}
            <div class="flex gap-10 mb-10">
                <div class="text-white">
                    <p class="text-2xl font-bold">4</p>
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Sensor Aktif</p>
                </div>
                <div class="text-white">
                    <p class="text-2xl font-bold text-green-400">90%</p>
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Akurasi Data</p>
                </div>
                <div class="text-white">
                    <p class="text-2xl font-bold">24/7</p>
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Monitoring</p>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-4">
                <a href="/register"
                    class="bg-white text-green-700 font-semibold px-10 py-3 rounded-lg hover:bg-green-50 transition">
                    Daftar
                </a>
                <a href="/login"
                    class="bg-green-600 text-white font-semibold px-10 py-3 rounded-lg hover:bg-green-700 transition">
                    Masuk
                </a>
            </div>
        </div>

    </div>

</body>
</html>