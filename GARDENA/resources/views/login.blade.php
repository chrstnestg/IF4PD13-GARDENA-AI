<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GARDENA-AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght=400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        @media (min-width: 768px) {
            html, body { overflow: hidden; }
        }
    </style>
</head>
<body class="min-h-screen md:h-screen flex flex-col md:flex-row bg-white">

    {{-- KIRI: Background + Info --}}
    <div class="relative w-full md:w-1/2 min-h-[320px] md:h-full flex items-center">
        <div class="absolute inset-0 bg-cover bg-center"
            style="background-image: url('{{ asset('images/bg2.jpg') }}');">
            <div class="absolute inset-0 bg-black/60 md:bg-black/50"></div>
        </div>

        <div class="relative z-10 w-full flex flex-col justify-center px-6 sm:px-12 md:px-14 py-8 md:py-0">
            <span class="text-green-400 text-xs font-semibold uppercase tracking-widest mb-2 md:mb-4">
                Smart Hydroponic System
            </span>
            <h1 class="text-white text-2xl sm:text-3xl md:text-4xl font-extrabold leading-tight mb-2 md:mb-4">
                Selamat Datang di<br>
                <span class="text-green-400">GARDENA-AI</span>
            </h1>
            <p class="text-gray-300 text-xs sm:text-sm mb-6 md:mb-10 max-w-sm">
                Monitor kondisi tanaman hidroponik secara real-time dan dapatkan rekomendasi nutrisi otomatis untuk pertumbuhan optimal.
            </p>
            
            <div class="flex gap-6 sm:gap-10">
                <div class="text-white">
                    <p class="text-xl sm:text-2xl font-bold">4</p>
                    <p class="text-[10px] sm:text-xs text-gray-400 uppercase tracking-wider">Sensor Aktif</p>
                </div>
                <div class="text-white">
                    <p class="text-xl sm:text-2xl font-bold text-green-400">90%</p>
                    <p class="text-[10px] sm:text-xs text-gray-400 uppercase tracking-wider">Akurasi Data</p>
                </div>
                <div class="text-white">
                    <p class="text-xl sm:text-2xl font-bold">24/7</p>
                    <p class="text-[10px] sm:text-xs text-gray-400 uppercase tracking-wider">Monitoring</p>
                </div>
            </div>
        </div>
    </div>

    {{-- KANAN: Form Login --}}
    <div class="w-full md:w-1/2 min-h-fit md:h-full flex items-center justify-center bg-white px-6 sm:px-12 md:px-16 py-8 md:py-0">
        <div class="w-full max-w-md">

            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-800 mb-4 uppercase">Masuk ke Akun</h2>

            <form action="/login" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan Email" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 sm:py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Masukkan Password" required
                            class="w-full border border-gray-300 rounded-lg pl-4 pr-12 py-2.5 sm:py-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent transition">
                        
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-green-600 transition">
                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            <svg id="eyeClose" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 hidden">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 1-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-green-600 text-white font-semibold py-3 rounded-lg hover:bg-green-700 transition shadow-lg shadow-green-600/20">
                    Masuk
                </button>

                <p class="text-center text-sm text-gray-500 pt-1">
                    Belum Punya Akun?
                    <a href="/register" class="text-green-600 font-semibold hover:underline">Daftar Sekarang</a>
                </p>

            </form>
        </div>
    </div>

</body>

<script>
const MySwal = Swal.mixin({
    scrollbarPadding: false,
    heightAuto: false,
    allowOutsideClick: false,
    didOpen: () => {
        if (window.innerWidth >= 768) {
            document.body.style.paddingRight = '0px';
            document.documentElement.style.paddingRight = '0px';
            document.body.style.overflow = 'hidden';
        }
    }
});

document.addEventListener("DOMContentLoaded", function () {

    // ─── Fitur Show/Hide Password ──────────────────────────────────────────
    const passwordInput = document.getElementById('password');
    const togglePasswordButton = document.getElementById('togglePassword');
    const eyeOpenIcon = document.getElementById('eyeOpen');
    const eyeCloseIcon = document.getElementById('eyeClose');

    togglePasswordButton.addEventListener('click', function () {
        // Cek tipe input saat ini
        const isPassword = passwordInput.getAttribute('type') === 'password';
        
        // Ganti tipe input
        passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
        
        // Ganti icon mata terbuka/tertutup
        if (isPassword) {
            eyeOpenIcon.classList.add('hidden');
            eyeCloseIcon.classList.remove('hidden');
        } else {
            eyeOpenIcon.classList.remove('hidden');
            eyeCloseIcon.classList.add('hidden');
        }
    });

    // ─── Laravel Session SweetAlerts ────────────────────────────────────────
    @if (session('success'))
        MySwal.fire({
            icon: 'success',
            title: 'Pendaftaran Berhasil!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#16a34a',
            confirmButtonText: 'OK'
        });
    @endif

    @if (session('login_success'))
        MySwal.fire({
            icon: 'success',
            title: 'Selamat Datang!',
            text: '{{ session('login_success') }}',
            confirmButtonColor: '#16a34a',
            timer: 5000,
            showConfirmButton: false,
            willClose: () => {
                window.location.href = "/monitoring";
            }
        });
    @endif

    @if ($errors->has('email'))
        MySwal.fire({
            icon: 'error',
            title: 'Email Tidak Ditemukan!',
            text: '{{ $errors->first('email') }}',
            confirmButtonColor: '#16a34a',
            confirmButtonText: 'Coba Lagi'
        });
    @elseif ($errors->has('password'))
        MySwal.fire({
            icon: 'error',
            title: 'Password Salah!',
            text: '{{ $errors->first('password') }}',
            confirmButtonColor: '#16a34a',
            confirmButtonText: 'Coba Lagi'
        });
    @elseif ($errors->any())
        MySwal.fire({
            icon: 'error',
            title: 'Login Gagal!',
            text: '{{ $errors->first() }}',
            confirmButtonColor: '#16a34a',
            confirmButtonText: 'Coba Lagi'
        });
    @endif

});
</script>
</html>