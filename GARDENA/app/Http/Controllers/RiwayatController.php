<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPanen; // Model ini tetap digunakan namun membaca tabel riwayat_anomali
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    /* ─────────────────────────────────────────
     | Halaman Riwayat Kondisi Tidak Optimal (Anomali)
     ───────────────────────────────────────── */
    public function index(Request $request)
    {
        // Ambil data log anomali lingkungan milik user yang login
        $query = RiwayatPanen::where('id_user', Auth::id())
                             ->orderByDesc('created_at'); // Urutkan dari kejadian terbaru

        // Filter berdasarkan status perbaikan jika dipilih (Pending / Teratasi)
        if ($request->filled('status')) {
            $query->where('status_perbaikan', $request->status);
        }

        $rows = $query->get();

        $riwayatList = $rows->map(function (RiwayatPanen $r) {
            return [
                'id'               => $r->id,
                'waktu_kejadian'   => $r->created_at->translatedFormat('j F Y, H:i') . ' WIB',
                'status_anomali'   => $r->status_anomali,
                'rekomendasi'      => $r->rekomendasi_ai ?? 'Tidak ada rekomendasi tindakan.',
                'status_perbaikan' => $r->status_perbaikan ?? 'Pending',
                'sensor' => [
                    ['label' => 'pH',        'nilai' => $r->nilai_ph !== null ? $r->nilai_ph : '-', 'icon' => 'bi-droplet-fill'],
                    ['label' => 'TDS',       'nilai' => $r->nilai_tds !== null ? $r->nilai_tds . ' ppm' : '-', 'icon' => 'bi-lightning-charge-fill'],
                    ['label' => 'Suhu Air',  'nilai' => $r->nilai_suhu !== null ? $r->nilai_suhu . '°C' : '-', 'icon' => 'bi-thermometer-half'],
                ],
            ];
        })->values()->all();

        // Statistik ringkas untuk ditaruh di stat cards atas halaman blade
        $stats = [
            'total_insiden'   => $rows->count(),
            'belum_ditangani' => $rows->where('status_perbaikan', 'Pending')->count(),
            'selesai_diatasi' => $rows->where('status_perbaikan', 'Teratasi')->count(),
        ];

        return view('pages.riwayat', compact('riwayatList', 'stats'));
    }

    /* ─────────────────────────────────────────
     | Aksi: Menandai Masalah Telah Diatasi oleh Petani
     ───────────────────────────────────────── */
    public function tandaiTeratasi($id)
    {
        $log = RiwayatPanen::where('id_user', Auth::id())->findOrFail($id);
        
        $log->update([
            'status_perbaikan' => 'Teratasi'
        ]);

        return redirect()->route('riwayat')
            ->with('success', 'Status lingkungan berhasil diperbarui menjadi Teratasi!');
    }
}