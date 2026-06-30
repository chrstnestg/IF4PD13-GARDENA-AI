<?php

namespace App\Http\Controllers;

use App\Models\RiwayatPanen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        // Query dasar berdasarkan user login dan diurutkan dari yang terbaru
        $query = RiwayatPanen::where('id_user', Auth::id())
                             ->orderByDesc('created_at');

        // 1. Filter Jenis Anomali
        if ($request->filled('anomali')) {
            $query->where('status_anomali', 'like', '%' . $request->anomali . '%');
        }

        // 2. Filter Rentang Waktu (Periode)
        if ($request->filled('periode')) {
            switch ($request->periode) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case '7':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                case '30':
                    $query->where('created_at', '>=', now()->subDays(30));
                    break;
            }
        }

        // 3. Fitur Pencarian (Search)
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('status_anomali', 'like', '%' . $request->search . '%')
                  ->orWhere('rekomendasi_ai', 'like', '%' . $request->search . '%');
            });
        }

        // Eksekusi query untuk mendapatkan data yang sudah difilter
        $rows = $query->get();

        // Mapping data mentah database ke format siap pakai di view
        $riwayatList = $rows->map(function (RiwayatPanen $r) {
            return [
                'id'               => $r->id,
                'waktu_kejadian'   => $r->created_at->translatedFormat('j F Y, H:i') . ' WIB',
                'status_anomali'   => $r->status_anomali,
                'rekomendasi'      => $r->rekomendasi_ai ?? 'Tidak ada rekomendasi tindakan.',
                'status_perbaikan' => $r->status_perbaikan ?? 'Teratasi',
                'sensor' => [
                    [
                        'label' => 'pH',       
                        'nilai' => $r->nilai_ph !== null ? $r->nilai_ph : '-', 
                        'icon'  => 'bi-droplet-fill'
                    ],
                    [
                        'label' => 'TDS',      
                        'nilai' => $r->nilai_tds !== null ? $r->nilai_tds . ' ppm' : '-', 
                        'icon'  => 'bi-lightning-charge-fill'
                    ],
                    [
                        'label' => 'Suhu Air', 
                        'nilai' => $r->nilai_suhu !== null ? $r->nilai_suhu . '°C' : '-', 
                        'icon'  => 'bi-thermometer-half'
                    ],
                ],
            ];
        })->values()->all();

        // Hitung statistik berdasarkan data yang sedang tampil
        $stats = [
            'total_insiden'    => $rows->count(),
            'selesai_diatasi'  => $rows->where('status_perbaikan', 'Teratasi')->count(),
            'anomali_terakhir' => $rows->first()?->status_anomali ?? 'Belum ada anomali',
        ];

        return view('pages.riwayat', compact('riwayatList', 'stats'));
    }

    public function update(Request $request, $id)
    {
        $log = RiwayatPanen::where('id_user', Auth::id())->findOrFail($id);
        $log->update(['status_perbaikan' => 'Teratasi']);

        return redirect()->route('riwayat')
            ->with('success', 'Status berhasil diperbarui menjadi Teratasi!');
    }
}