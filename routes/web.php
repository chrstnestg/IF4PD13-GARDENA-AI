<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RekomendasiController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\PengaturanController;

// ── Landing & Auth ──
Route::get('/', fn() => view('landing'));
Route::get('/register', fn() => view('register'));
Route::get('/login',    fn() => view('login'));

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');

// ── Main Pages ──
Route::get('/monitoring', [MonitoringController::class, 'index'])->name('monitoring');
Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
Route::post('/pengaturan/update-profil',   [PengaturanController::class, 'updateProfil']);
Route::post('/pengaturan/update-password', [PengaturanController::class, 'updatePassword']);

// ── Rekomendasi ──
Route::get('/rekomendasi', [RekomendasiController::class, 'index'])->name('rekomendasi');
Route::post('/rekomendasi/terapkan', [RekomendasiController::class, 'terapkan'])->name('rekomendasi.terapkan');
Route::post('/rekomendasi/selesai',  [RekomendasiController::class, 'selesai'])->name('rekomendasi.selesai');

// ── Riwayat ──
Route::get('/riwayat',        [RiwayatController::class, 'index'])->name('riwayat');
Route::get('/riwayat/tambah', [RiwayatController::class, 'tambah'])->name('riwayat.tambah');