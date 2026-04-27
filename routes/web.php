<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RekomendasiController;
use App\Http\Controllers\RiwayatController;

<<<<<<< Updated upstream
Route::get('/', fn() => redirect()->route('dashboard'));

Route::get('/dashboard',  [DashboardController::class,   'index'])->name('dashboard');
Route::get('/rekomendasi',[RekomendasiController::class, 'index'])->name('rekomendasi');
Route::get('/riwayat',    [RiwayatController::class,     'index'])->name('riwayat');

Route::post('/rekomendasi/terapkan', [RekomendasiController::class, 'terapkan'])->name('rekomendasi.terapkan');
Route::post('/rekomendasi/selesai',  [RekomendasiController::class, 'selesai'])->name('rekomendasi.selesai');

Route::get('/riwayat/tambah', [RiwayatController::class, 'tambah'])->name('riwayat.tambah');
=======
Route::get('/', function () {
    return view('landing');
});

Route::get('/register', function () {
    return view('register');
});

Route::get('/login', function () {
    return view('login');
});

// Setelah register → ke halaman login
Route::post('/register', function () {
    return redirect('/login');
});

// Setelah login → ke halaman monitoring
Route::post('/login', function () {
    return redirect('/monitoring');
});

Route::get('/monitoring', function () {
    return view('monitoring');
});

Route::get('/pengaturan', function () {
    return view('pengaturan');
});
>>>>>>> Stashed changes
