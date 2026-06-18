<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - GARDENA-AI</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>

<body class="bg-gray-50">

@include('components.navbar')

<div class="max-w-4xl mx-auto px-6 py-10">

    {{-- HEADER --}}
    <div class="mb-8">
        <h1 class="text-xl font-bold text-gray-800">Pengaturan Akun</h1>
        <p class="text-sm text-gray-500">Kelola informasi profil dan keamanan akun kamu</p>
    </div>

    {{-- ALERT --}}
    @if(session('success'))
        <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded-lg">
            {{ $errors->first() }}
        </div>
    @endif

    <div x-data="{ active: 'profil' }" class="grid grid-cols-3 gap-6">

        {{-- SIDEBAR --}}
        <div class="col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- AVATAR --}}
                <div class="flex flex-col items-center py-8 px-4 border-b">
                    <div class="w-20 h-20 rounded-full bg-gray-300 flex items-center justify-center text-2xl font-bold">
                        {{ strtoupper(substr(Auth::user()->name,0,1)) }}
                    </div>
                    <p class="text-sm font-semibold mt-3">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400">User</p>
                </div>

                {{-- MENU --}}
                <div>
                    <button @click="active='profil'"
                        :class="active==='profil' ? 'bg-green-50 text-green-600 border-r-2 border-green-600' : ''"
                        class="w-full px-5 py-3 text-left text-sm">
                        <i class="fa fa-user mr-2"></i> Data Diri
                    </button>

                    <button @click="active='keamanan'"
                        :class="active==='keamanan' ? 'bg-green-50 text-green-600 border-r-2 border-green-600' : ''"
                        class="w-full px-5 py-3 text-left text-sm">
                        <i class="fa fa-lock mr-2"></i> Keamanan
                    </button>
                </div>

            </div>
        </div>

        {{-- CONTENT --}}
        <div class="col-span-2">

            {{-- ================= DATA DIRI ================= --}}
            <div x-show="active==='profil'" class="bg-white p-6 rounded-xl shadow-sm border">

                <h2 class="font-bold mb-4">Data Diri</h2>

                <form action="/pengaturan/update-profil" method="POST" class="space-y-4">
                    @csrf

                    <input type="text" name="name" value="{{ Auth::user()->name }}"
                        class="w-full border p-3 rounded-lg">

                    <input type="text" name="username" value="{{ Auth::user()->username }}"
                        class="w-full border p-3 rounded-lg">

                    <input type="email" name="email" value="{{ Auth::user()->email }}"
                        class="w-full border p-3 rounded-lg">

                    <button class="bg-green-600 text-white px-6 py-3 rounded-lg">
                        Simpan
                    </button>
                </form>

            </div>

            {{-- ================= PASSWORD ================= --}}
            <div x-show="active==='keamanan'" class="bg-white p-6 rounded-xl shadow-sm border">

                <h2 class="font-bold mb-4">Ubah Password</h2>

                <form action="/pengaturan/update-password" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Password Lama</label>
                        <input type="password" name="password_lama" placeholder="Masukkan password lama"
                            class="w-full border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Password Baru</label>
                        <input type="password" name="password_baru" placeholder="Masukkan password baru"
                            class="w-full border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_baru_confirmation" placeholder="Ulangi password baru"
                            class="w-full border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
                    </div>

                    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition">
                        Ubah Password
                    </button>
                </form>

            </div>

        </div>
    </div>
</div>

</body>
</html>