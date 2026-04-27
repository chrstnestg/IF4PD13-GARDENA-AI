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

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-xl font-bold text-gray-800">Pengaturan Akun</h1>
            <p class="text-sm text-gray-500">Kelola informasi profil dan keamanan akun kamu</p>
        </div>

        {{-- SATU x-data untuk semua --}}
        <div x-data="{ active: 'profil' }" class="grid grid-cols-3 gap-6">

            {{-- SIDEBAR --}}
            <div class="col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

                    {{-- Avatar --}}
                    <div class="flex flex-col items-center py-8 px-4 border-b border-gray-100">
                        <div class="w-20 h-20 rounded-full bg-gray-300 flex items-center justify-center text-2xl font-bold text-gray-700 mb-3">
                            IK
                        </div>
                        <p class="text-sm font-semibold text-gray-800">Irene Kristi</p>
                        <p class="text-xs text-gray-400">Petani</p>
                        <button class="mt-3 text-xs text-green-600 border border-green-500 rounded-lg px-3 py-1 hover:bg-green-50 transition">
                            Ganti Foto
                        </button>
                    </div>

                    {{-- Menu Sidebar --}}
                    <div class="py-2">
                        <button @click="active = 'profil'"
                            :class="active === 'profil' ? 'bg-green-50 text-green-600 border-r-2 border-green-600' : 'text-gray-500 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-5 py-3 text-sm font-medium transition">
                            <i class="fa-solid fa-user"></i> Data Diri
                        </button>
                        <button @click="active = 'keamanan'"
                            :class="active === 'keamanan' ? 'bg-green-50 text-green-600 border-r-2 border-green-600' : 'text-gray-500 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-5 py-3 text-sm font-medium transition">
                            <i class="fa-solid fa-lock"></i> Keamanan
                        </button>
                        <button @click="active = 'perangkat'"
                            :class="active === 'perangkat' ? 'bg-green-50 text-green-600 border-r-2 border-green-600' : 'text-gray-500 hover:bg-gray-50'"
                            class="w-full flex items-center gap-3 px-5 py-3 text-sm font-medium transition">
                            <i class="fa-solid fa-wifi"></i> Perangkat IoT
                        </button>
                    </div>
                </div>
            </div>

            {{-- KONTEN KANAN --}}
            <div class="col-span-2">

                {{-- TAB: Data Diri --}}
                <div x-show="active === 'profil'" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-base font-bold text-gray-800 mb-1">Data Diri</h2>
                    <p class="text-xs text-gray-400 mb-6">Perbarui informasi profil kamu</p>
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Nama Lengkap</label>
                            <input type="text" value="Irene Kristi"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Username</label>
                            <input type="text" value="irenekrsti"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Alamat Email</label>
                            <input type="email" value="irene@gmail.com"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Role</label>
                            <input type="text" value="Petani" disabled
                                class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm bg-gray-50 text-gray-400 cursor-not-allowed">
                        </div>
                        <div class="flex justify-end pt-2">
                            <button class="bg-green-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-green-700 transition text-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>

                {{-- TAB: Keamanan --}}
                <div x-show="active === 'keamanan'" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-base font-bold text-gray-800 mb-1">Keamanan</h2>
                    <p class="text-xs text-gray-400 mb-6">Ubah password akun kamu</p>
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Password Lama</label>
                            <input type="password" placeholder="Masukkan password lama"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Password Baru</label>
                            <input type="password" placeholder="Masukkan password baru"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 mb-1">Konfirmasi Password Baru</label>
                            <input type="password" placeholder="Ulangi password baru"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition">
                        </div>
                        <div class="flex justify-end pt-2">
                            <button class="bg-green-600 text-white font-semibold px-8 py-3 rounded-lg hover:bg-green-700 transition text-sm">
                                Ubah Password
                            </button>
                        </div>
                    </div>
                </div>

                {{-- TAB: Perangkat IoT --}}
                <div x-show="active === 'perangkat'" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-base font-bold text-gray-800 mb-1">Perangkat IoT</h2>
                    <p class="text-xs text-gray-400 mb-6">Informasi perangkat yang terhubung</p>

                    {{-- Device Card --}}
                    <div class="border border-gray-200 rounded-xl p-4 mb-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center text-lg">
                                    <i class="fa-solid fa-wifi"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">Sensor Hidroponik #1</p>
                                    <p class="text-xs text-gray-400">ID: GDN-IOT-001</p>
                                </div>
                            </div>
                            <span class="text-xs text-green-600 bg-green-50 border border-green-200 rounded-full px-3 py-1">
                                Aktif
                            </span>
                        </div>
                        <div class="mt-3 grid grid-cols-3 gap-3 text-xs text-gray-500 border-t border-gray-100 pt-3">
                            <div>
                                <p class="text-gray-400">Terakhir Online</p>
                                <p class="font-semibold text-gray-700">14:30 WIB</p>
                            </div>
                            <div>
                                <p class="text-gray-400">Sensor Aktif</p>
                                <p class="font-semibold text-gray-700">4 Sensor</p>
                            </div>
                            <div>
                                <p class="text-gray-400">Status</p>
                                <p class="font-semibold text-green-600">Normal</p>
                            </div>
                        </div>
                    </div>

                    {{-- Tambah Perangkat --}}
                    <button class="w-full border-2 border-dashed border-gray-300 rounded-xl py-4 text-sm text-gray-400 hover:border-green-400 hover:text-green-500 transition">
                        + Tambah Perangkat Baru
                    </button>
                </div>

            </div>
        </div>
    </div>

</body>
</html>