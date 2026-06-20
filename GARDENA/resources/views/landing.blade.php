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

<body class="bg-white text-gray-800">

<!-- ================= NAVBAR ================= -->
<nav class="fixed w-full z-50 bg-white border-b border-gray-200 px-10 shadow-sm">
    <div class="flex justify-between items-center py-4">

        <!-- LOGO -->
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo2.png') }}" class="h-10">
        </div>

        <!-- MENU -->
        <div class="hidden md:flex gap-8 text-sm text-gray-500 font-medium">
            <a href="#" class="hover:text-green-600 transition">Monitoring</a>
            <a href="#" class="hover:text-green-600 transition">Rekomendasi</a>
            <a href="#" class="hover:text-green-600 transition">Riwayat</a>
        </div>

        <!-- ACTION -->
        <div class="flex items-center gap-3">
            <a href="/login" class="px-4 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition font-medium">Masuk</a>
            <a href="/register" class="px-4 py-2 text-sm bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">Daftar</a>
        </div>
    </div>
</nav>

<!-- ================= HERO ================= -->
<section class="min-h-screen flex items-center px-10 md:px-20 pt-24" 
    style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 50%, #f0fdf4 100%);">

    <div class="grid md:grid-cols-2 gap-10 items-center w-full">

        <!-- LEFT -->
        <div>
            <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full mb-4 uppercase tracking-widest">
                AI Powered Hydroponic
            </span>

            <h1 class="text-5xl font-extrabold mb-6 text-gray-800 leading-tight">
                Monitoring Tanaman <br>
                Lebih <span class="text-green-600">Cerdas</span>
            </h1>

            <p class="text-gray-500 mb-8 text-base max-w-md">
                Pantau kondisi tanaman secara real-time dan dapatkan rekomendasi nutrisi otomatis untuk hasil panen optimal.
            </p>

            <div class="flex gap-4 mb-10">
                <a href="/register"
                    class="bg-green-600 text-white px-8 py-3 rounded-xl font-semibold hover:bg-green-700 transition shadow-md">
                    Mulai Sekarang
                </a>
            </div>
        </div>

        <!-- RIGHT DASHBOARD -->
        <div class="relative">

            <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-xl">

                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-gray-700 font-semibold">Realtime Monitoring</h3>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-green-50 border border-green-100 p-4 rounded-xl">
                        <p class="text-xs text-gray-400 mb-1">Suhu</p>
                        <p class="text-xl font-bold text-green-600">26°C</p>
                        <p class="text-xs text-green-500 mt-1">Normal</p>
                    </div>

                    <div class="bg-blue-50 border border-blue-100 p-4 rounded-xl">
                        <p class="text-xs text-gray-400 mb-1">pH</p>
                        <p class="text-xl font-bold text-blue-600">6.5</p>
                        <p class="text-xs text-blue-500 mt-1">Optimal</p>
                    </div>

                    <div class="bg-purple-50 border border-purple-100 p-4 rounded-xl">
                        <p class="text-xs text-gray-400 mb-1">TDS</p>
                        <p class="text-xl font-bold text-purple-600">850 ppm</p>
                        <p class="text-xs text-purple-500 mt-1">Normal</p>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-100 p-4 rounded-xl">
                        <p class="text-xs text-gray-400 mb-1">Nutrisi</p>
                        <p class="text-xl font-bold text-yellow-600">Optimal</p>
                        <p class="text-xs text-yellow-500 mt-1">Terkini</p>
                    </div>
                </div>
            </div>

            <div class="absolute -z-10 top-10 left-10 w-72 h-72 bg-green-500/20 blur-3xl rounded-full"></div>
        </div>

    </div>
</section>

<!-- ================= FEATURES ================= -->
<section class="py-20 px-10 md:px-20 text-center">
    <h2 class="text-3xl font-bold mb-12">Fitur Unggulan</h2>

    <div class="grid md:grid-cols-3 gap-8">

        <div class="bg-white/5 p-6 rounded-2xl border border-white/10">
            <h3 class="text-green-400 mb-2">Monitoring Real-time</h3>
            <p class="text-gray-400 text-sm">Pantau kondisi tanaman kapan saja.</p>
        </div>

        <div class="bg-white/5 p-6 rounded-2xl border border-white/10">
            <h3 class="text-green-400 mb-2">AI Recommendation</h3>
            <p class="text-gray-400 text-sm">Rekomendasi nutrisi otomatis.</p>
        </div>

        <div class="bg-white/5 p-6 rounded-2xl border border-white/10">
            <h3 class="text-green-400 mb-2">Data History</h3>
            <p class="text-gray-400 text-sm">Analisis pertumbuhan tanaman.</p>
        </div>

    </div>
</section>

<!-- ================= CTA ================= -->
<section class="py-20 text-center">
    <h2 class="text-4xl font-bold mb-4">Mulai Sekarang</h2>
    <p class="text-gray-400 mb-8">Gunakan AI untuk hasil panen maksimal</p>

    <a href="/register" class="bg-green-500 px-10 py-4 rounded-xl font-semibold hover:scale-105 transition">
        Daftar Sekarang
    </a>
</section>

<footer class="border-t border-white/10 py-6 text-center text-gray-500">
    © 2026 GARDENA-AI
</footer>

</body>
</html>