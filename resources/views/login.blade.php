<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GARDENA-AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="h-screen flex">

    {{-- KIRI: Background + Info --}}
    <div class="relative w-1/2 h-full">
        {{-- Background Image --}}
        <div class="absolute inset-0 bg-cover bg-center"
            style="background-image: url('{{ asset('images/bg2.jpg') }}');">
            <div class="absolute inset-0 bg-black/50"></div>
        </div>

        {{-- Content Kiri --}}
        <div class="relative z-10 h-full flex flex-col justify-center px-14">

            {{-- Badge --}}
            <span class="text-green-400 text-xs font-semibold uppercase tracking-widest mb-4">
                Smart Hydroponic System
            </span>

            {{-- Judul --}}
            <h1 class="text-white text-4xl font-extrabold leading-tight mb-4">
                Selamat Datang di<br>
                <span class="text-green-400">GARDENA-AI</span>
            </h1>

            {{-- Deskripsi --}}
            <p class="text-gray-300 text-sm mb-10 max-w-sm">
                Monitor kondisi tanaman hidroponik secara real-time dan dapatkan rekomendasi nutrisi otomatis untuk pertumbuhan optimal.
            </p>

            {{-- Stats --}}
            <div class="flex gap-10">
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
        </div>
    </div>

    {{-- KANAN: Form Login --}}
    <div class="w-1/2 h-full flex items-center justify-center bg-white px-16">
        <div class="w-full max-w-md">

            {{-- Judul Form --}}
            <h2 class="text-3xl font-extrabold text-gray-800 mb-8">MASUK KE AKUN</h2>

            <form action="/login" method="POST" class="space-y-5">
    @csrf
    
    {{-- Email --}}
    <div>
        <label class="block text-sm font-medium text-gray-600 mb-1">Alamat Email</label>
        <input type="email" name="email" placeholder="Masukkan Email"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition">
    </div>

    {{-- Password --}}
    <div>
        <label class="block text-sm font-medium text-gray-600 mb-1">Password</label>
        <input type="password" name="password" placeholder="Masukkan Password"
            class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition">
    </div>

    {{-- Tombol --}}
    <button type="submit"
        class="w-full bg-green-600 text-white font-semibold py-3 rounded-lg hover:bg-green-700 transition">
        Masuk
    </button>

                {{-- Link Register --}}
                <p class="text-center text-sm text-gray-500 mt-4">
                    Belum Punya Akun?
                    <a href="/register" class="text-green-600 font-semibold hover:underline">Daftar Sekarang</a>
                </p>

            </div>
        </div>
    </div>

</body>
</html>