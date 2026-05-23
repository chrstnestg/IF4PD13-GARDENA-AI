<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RekomendasiController;
use App\Http\Controllers\RiwayatController;

// ── Landing & Auth ──
Route::get('/', fn() => view('landing'));

Route::get('/register', fn() => view('register'));
Route::get('/login',    fn() => view('login'));

Route::post('/register', fn() => redirect('/login'));
Route::post('/login',    fn() => redirect('/dashboard'));

// ── Main Pages ──
Route::get('/rekomendasi',[RekomendasiController::class, 'index'])->name('rekomendasi');
Route::get('/monitoring', fn() => view('pages.monitoring'))->name('monitoring');
Route::get('/pengaturan', fn() => view('pengaturan'))->name('pengaturan');

// ── Riwayat ──
Route::get('/riwayat',        [RiwayatController::class, 'index'])->name('riwayat');
Route::get('/riwayat/tambah', [RiwayatController::class, 'tambah'])->name('riwayat.tambah');

// ── Rekomendasi Actions ──
Route::post('/rekomendasi/terapkan', [RekomendasiController::class, 'terapkan'])->name('rekomendasi.terapkan');
Route::post('/rekomendasi/selesai',  [RekomendasiController::class, 'selesai'])->name('rekomendasi.selesai');