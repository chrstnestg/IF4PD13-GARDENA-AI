<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - GARDENA-AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght=400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        /* Mengunci scroll hanya di desktop, di HP tetap bisa di-scroll jika form panjang */
        @media (min-width: 768px) {
            html, body { overflow: hidden; }
        }
    </style>
</head>

<body class="min-h-screen md:h-screen flex flex-col md:flex-row bg-white">

    {{-- KIRI (Banner/Welcome) --}}
    <div class="relative w-full md:w-1/2 min-h-[250px] md:h-full flex items-center">
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
            <p class="text-gray-300 text-xs sm:text-sm mb-0 md:mb-10 max-w-sm">
                Monitor kondisi tanaman hidroponik secara real-time dan dapatkan rekomendasi nutrisi otomatis.
            </p>
        </div>
    </div>

    {{-- KANAN (Form) --}}
    <div class="w-full md:w-1/2 min-h-fit md:h-full flex items-center justify-center bg-white px-6 sm:px-12 md:px-16 py-8 md:py-0">
        <div class="w-full max-w-md">

            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-800 mb-4">Buat Akun Baru</h2>

            <form id="registerForm" action="/register" method="POST" class="space-y-4">
                @csrf

                {{-- Nama --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 sm:py-3 text-sm focus:ring-2 focus:ring-green-400 outline-none">
                </div>

                {{-- Username --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 sm:py-3 text-sm focus:ring-2 focus:ring-green-400 outline-none">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 sm:py-3 text-sm focus:ring-2 focus:ring-green-400 outline-none">
                </div>

                {{-- Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 sm:py-3 text-sm focus:ring-2 focus:ring-green-400 outline-none">
                    <p class="text-[11px] sm:text-xs text-gray-400 mt-1">
                        Minimal 8 karakter, huruf besar, huruf kecil, angka, dan simbol.
                    </p>
                </div>

                {{-- Button --}}
                <button type="submit"
                    class="w-full bg-green-600 text-white font-semibold py-3 rounded-lg hover:bg-green-700 transition shadow-lg shadow-green-600/20">
                    Daftar Sekarang
                </button>

                <p class="text-center text-sm text-gray-500 pt-2">
                    Sudah Punya Akun?
                    <a href="/login" class="text-green-600 font-semibold hover:underline">Masuk Disini</a>
                </p>

            </form>
        </div>
    </div>

</body>

<script>
const MySwal = Swal.mixin({
    scrollbarPadding: false,
    heightAuto: false,
    didOpen: () => {
        // Hanya kunci overflow jika di desktop saat modal terbuka
        if (window.innerWidth >= 768) {
            document.body.style.paddingRight = '0px';
            document.documentElement.style.paddingRight = '0px';
            document.body.style.overflow = 'hidden';
        }
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const password = document.getElementById('password');
    const form = document.getElementById('registerForm');

    // ─── Validasi kriteria password sebelum submit ─────────────────────────
    form.addEventListener('submit', function (e) {
        const pwd = password.value;

        const hasMinLength = pwd.length >= 8;
        const hasUppercase = /[A-Z]/.test(pwd);
        const hasLowercase = /[a-z]/.test(pwd);
        const hasNumber    = /[0-9]/.test(pwd);
        const hasSymbol    = /[^A-Za-z0-9]/.test(pwd);

        if (!hasMinLength || !hasUppercase || !hasLowercase || !hasNumber || !hasSymbol) {
            e.preventDefault();

            let missing = [];
            if (!hasMinLength) missing.push('minimal 8 karakter');
            if (!hasUppercase) missing.push('huruf besar (A-Z)');
            if (!hasLowercase) missing.push('huruf kecil (a-z)');
            if (!hasNumber)    missing.push('angka (0-9)');
            if (!hasSymbol)    missing.push('simbol (!, @, #, ...)');

            MySwal.fire({
                icon: 'error',
                title: 'Password Tidak Memenuhi Kriteria!',
                html: `Password harus mengandung:<br><ul style="text-align:left; margin-top:8px; padding-left:20px;">` +
                      missing.map(m => `<li>• ${m}</li>`).join('') +
                      `</ul>`,
                confirmButtonColor: '#16a34a',
                confirmButtonText: 'Perbaiki Password'
            });
            return;
        }
    });

    // ─── SweetAlert dari server ────────────────────────────────────────────

    @if (session('success'))
        MySwal.fire({
            icon: 'success',
            title: 'Berhasil Daftar!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#16a34a',
            confirmButtonText: 'Masuk Sekarang'
        }).then(() => {
            window.location.href = '/login';
        });
    @endif

    @if ($errors->has('password'))
        MySwal.fire({
            icon: 'error',
            title: 'Password Tidak Valid!',
            text: '{{ $errors->first('password') }}',
            confirmButtonColor: '#16a34a',
            confirmButtonText: 'Coba Lagi'
        });
    @elseif ($errors->has('email'))
        MySwal.fire({
            icon: 'error',
            title: 'Email Sudah Digunakan!',
            text: '{{ $errors->first('email') }}',
            confirmButtonColor: '#16a34a',
            confirmButtonText: 'Coba Lagi'
        });
    @elseif ($errors->any())
        MySwal.fire({
            icon: 'error',
            title: 'Pendaftaran Gagal!',
            text: '{{ $errors->first() }}',
            confirmButtonColor: '#16a34a',
            confirmButtonText: 'Coba Lagi'
        });
    @endif

});
</script>
</html>