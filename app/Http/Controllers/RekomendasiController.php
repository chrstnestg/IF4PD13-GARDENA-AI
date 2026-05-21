<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SensorReading;
use App\Models\Rekomendasi;
use App\Models\TindakanRekomendasi;

class RekomendasiController extends Controller
{
    public function index()
    {
        // ── Ambil sensor terbaru dari DB ──
        $sensor = SensorReading::latest('dibaca_pada')->first();

        // ── Data chart 7 hari terakhir ──
        $chartLabels     = [];
        $chartTds        = [];
        $chartPh         = [];
        $chartSuhu       = [];
        $chartKelembapan = [];

        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $rata    = SensorReading::whereDate('dibaca_pada', $tanggal)->get();

            $chartLabels[]     = $tanggal->translatedFormat('j M');
            $chartTds[]        = $rata->isNotEmpty() ? round($rata->avg('tds'), 2)        : 0;
            $chartPh[]         = $rata->isNotEmpty() ? round($rata->avg('ph'), 2)         : 0;
            $chartSuhu[]       = $rata->isNotEmpty() ? round($rata->avg('suhu'), 2)       : 0;
            $chartKelembapan[] = $rata->isNotEmpty() ? round($rata->avg('kelembapan'), 2) : 0;
        }

        // ── Semua ini nanti diganti hasil dari AI ──
        $rekomendasiList  = Rekomendasi::latest()->get()->map(fn($r) => [
            'id'          => $r->nutrisi_id,
            'judul'       => $r->judul,
            'status'      => $r->status,
            'labelStatus' => $r->label_status,
            'nilaiSaatIni'=> $r->nilai_saat_ini,
            'nilaiOptimal'=> $r->nilai_optimal,
            'deskripsi'   => $r->deskripsi,
            'aksiList'    => $r->aksi_list ?? [],
            'kritis'      => $r->kritis,
            'pesanKritis' => $r->pesan_kritis,
        ])->toArray();

        $healthScore = 68;
        $healthLabel = 'Sedang';

        $insightParagraf1 = 'Model AI sedang menganalisis data sensor terbaru.';
        $insightParagraf2 = 'Hasil analisis lengkap akan tersedia setelah model selesai dilatih.';
        $insightTips      = ['Pantau kondisi sensor secara rutin', 'Catat perubahan yang terjadi'];
        $insightPrediksi  = 'Prediksi akan tersedia setelah model AI aktif.';

        return view('pages.rekomendasi', compact(
            'rekomendasiList', 'healthScore', 'healthLabel',
            'insightParagraf1', 'insightParagraf2', 'insightTips', 'insightPrediksi',
            'chartLabels', 'chartTds', 'chartPh', 'chartSuhu', 'chartKelembapan',
        ));
    }

    public function terapkan(Request $request)
    {
        $request->validate(['nutrisi_id' => 'required|string']);

        $rek = Rekomendasi::where('nutrisi_id', $request->nutrisi_id)->first();
        if ($rek) {
            TindakanRekomendasi::create([
                'rekomendasi_id' => $rek->id,
                'aksi'           => 'terapkan',
                'dilakukan_pada' => now(),
            ]);
        }

        return redirect()->route('rekomendasi')
            ->with('success', 'Rekomendasi berhasil diterapkan!');
    }

    public function selesai(Request $request)
    {
        $request->validate(['nutrisi_id' => 'required|string']);

        $rek = Rekomendasi::where('nutrisi_id', $request->nutrisi_id)->first();
        if ($rek) {
            TindakanRekomendasi::create([
                'rekomendasi_id' => $rek->id,
                'aksi'           => 'selesai',
                'dilakukan_pada' => now(),
            ]);
        }

        return redirect()->route('rekomendasi')
            ->with('success', 'Tindakan dicatat sebagai sudah dilakukan.');
    }
}