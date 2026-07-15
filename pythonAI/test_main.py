"""
Unit test untuk Analytics Engine GARDENA-AI (main.py).

Yang diuji di sini HANYA fungsi murni (pure function): tidak menyentuh
database, network, atau API Gemini. Endpoint /historical-insight dan
call_gemini_with_retry() sengaja TIDAK diuji di sini karena butuh
mocking terpisah (client Gemini, HTTPException dari FastAPI, dsb).

Jalankan dengan:
    pytest test_main.py -v
"""

import json
import pandas as pd
import pytest

from main import (
    compute_duration_minutes,
    analyze_parameter,
    classify_correlation,
    build_risk_context,
    build_example_sentences,
    extract_json,
)


# ---------------------------------------------------------------------------
# compute_duration_minutes()
# ---------------------------------------------------------------------------

class TestComputeDurationMinutes:
    def test_pakai_timestamp_asli_jika_lengkap(self):
        df = pd.DataFrame({
            "timestamp": [
                "2026-07-11T10:00:00",
                "2026-07-11T10:05:00",
                "2026-07-11T10:10:00",
            ]
        })
        duration, is_estimated = compute_duration_minutes(df, total_readings=3)
        assert duration == 10.0
        assert is_estimated is False

    def test_fallback_estimasi_jika_tanpa_kolom_timestamp(self):
        df = pd.DataFrame({"ph": [7.0, 7.1, 7.2]})
        duration, is_estimated = compute_duration_minutes(df, total_readings=3)
        # (3 - 1) * ASSUMED_INTERVAL_MINUTES(1.0) = 2.0
        assert duration == 2.0
        assert is_estimated is True

    def test_fallback_estimasi_jika_ada_timestamp_kosong(self):
        df = pd.DataFrame({"timestamp": ["2026-07-11T10:00:00", None, "2026-07-11T10:10:00"]})
        duration, is_estimated = compute_duration_minutes(df, total_readings=3)
        assert duration == 2.0
        assert is_estimated is True

    def test_satu_pembacaan_saja_durasi_nol(self):
        df = pd.DataFrame({"ph": [7.0]})
        duration, is_estimated = compute_duration_minutes(df, total_readings=1)
        assert duration == 0.0
        assert is_estimated is True


# ---------------------------------------------------------------------------
# analyze_parameter()
# ---------------------------------------------------------------------------

class TestAnalyzeParameter:
    def test_statistik_dasar_dihitung_benar(self):
        series = pd.Series([6.0, 7.0, 8.0])
        result = analyze_parameter(series, low_th=6.0, high_th=8.0,
                                    total_readings=3, duration_minutes=2.0)
        assert result["average"] == 7.0
        assert result["minimum"] == 6.0
        assert result["maximum"] == 8.0
        assert result["median"] == 7.0

    def test_stability_percentage_semua_dalam_rentang(self):
        series = pd.Series([6.5, 6.8, 7.0, 7.2])
        result = analyze_parameter(series, 6.0, 8.0, total_readings=4, duration_minutes=3.0)
        assert result["stability_percentage"] == 100.0

    def test_stability_percentage_sebagian_dalam_rentang(self):
        series = pd.Series([6.5, 5.0, 7.0, 9.0])  # 2 dari 4 ideal
        result = analyze_parameter(series, 6.0, 8.0, total_readings=4, duration_minutes=3.0)
        assert result["stability_percentage"] == 50.0

    def test_trend_meningkat_konsisten_terdeteksi_increasing(self):
        series = pd.Series([6.0, 6.5, 7.0, 7.5, 8.0, 8.5, 9.0, 9.5, 10.0, 10.5])
        result = analyze_parameter(series, 6.0, 8.0, total_readings=10, duration_minutes=9.0)
        assert result["trend"]["direction"] == "increasing"
        assert result["trend"]["slope_per_reading"] > 0

    def test_trend_menurun_konsisten_terdeteksi_decreasing(self):
        series = pd.Series([10.5, 10.0, 9.5, 9.0, 8.5, 8.0, 7.5, 7.0, 6.5, 6.0])
        result = analyze_parameter(series, 6.0, 8.0, total_readings=10, duration_minutes=9.0)
        assert result["trend"]["direction"] == "decreasing"
        assert result["trend"]["slope_per_reading"] < 0

    def test_trend_datar_terdeteksi_stable(self):
        series = pd.Series([7.0, 7.0, 7.0, 7.0, 7.0])
        result = analyze_parameter(series, 6.0, 8.0, total_readings=5, duration_minutes=4.0)
        assert result["trend"]["direction"] == "stable"

    def test_satu_data_saja_trend_stable_tanpa_error(self):
        series = pd.Series([7.0])
        result = analyze_parameter(series, 6.0, 8.0, total_readings=1, duration_minutes=0.0)
        assert result["trend"]["direction"] == "stable"
        assert result["std_dev"] == 0.0
        # duration_minutes 0 -> rate_per_minute tidak boleh ZeroDivisionError
        assert result["trend"]["rate_per_minute"] == 0.0

    def test_total_change_dan_rate_per_minute(self):
        series = pd.Series([6.0, 7.0, 8.0])
        result = analyze_parameter(series, 6.0, 8.0, total_readings=3, duration_minutes=2.0)
        assert result["trend"]["total_change"] == 2.0  # 8.0 - 6.0
        assert result["trend"]["rate_per_minute"] == 1.0  # 2.0 / 2.0 menit


# ---------------------------------------------------------------------------
# classify_correlation()
# ---------------------------------------------------------------------------

class TestClassifyCorrelation:
    @pytest.mark.parametrize("r,expected_strength", [
        (0.85, "kuat"),
        (-0.75, "kuat"),
        (0.5, "sedang"),
        (-0.35, "sedang"),
        (0.1, "lemah"),
        (-0.05, "lemah"),
    ])
    def test_klasifikasi_kekuatan(self, r, expected_strength):
        result = classify_correlation(r)
        assert result["strength"] == expected_strength

    def test_arah_positif_searah(self):
        result = classify_correlation(0.5)
        assert "searah" in result["direction"]

    def test_arah_negatif_berlawanan(self):
        result = classify_correlation(-0.5)
        assert "berlawanan" in result["direction"]

    def test_mendekati_nol_tidak_ada_hubungan(self):
        result = classify_correlation(0.02)
        assert "tidak ada hubungan" in result["direction"]

    def test_batas_ambang_0_7_masuk_kuat(self):
        result = classify_correlation(0.7)
        assert result["strength"] == "kuat"

    def test_batas_ambang_0_3_masuk_sedang(self):
        result = classify_correlation(0.3)
        assert result["strength"] == "sedang"


# ---------------------------------------------------------------------------
# build_risk_context()
# ---------------------------------------------------------------------------

class TestBuildRiskContext:
    def test_risk_low_mengembalikan_konsekuensi_low(self):
        ctx = build_risk_context("low", avg_stability=90.0, duration_minutes=10.0, is_estimated=False)
        assert ctx["risk_level"] == "low"
        assert "tidak perlu tindakan segera" in ctx["consequence_if_unaddressed"]

    def test_risk_high_mengembalikan_konsekuensi_high(self):
        ctx = build_risk_context("high", avg_stability=20.0, duration_minutes=10.0, is_estimated=False)
        assert "Risiko stres/kerusakan" in ctx["consequence_if_unaddressed"]

    def test_estimasi_true_menghasilkan_catatan_estimasi(self):
        ctx = build_risk_context("medium", avg_stability=60.0, duration_minutes=5.0, is_estimated=True)
        assert "estimasi" in ctx["duration_note"]

    def test_estimasi_false_menghasilkan_catatan_aktual(self):
        ctx = build_risk_context("medium", avg_stability=60.0, duration_minutes=5.0, is_estimated=False)
        assert "aktual" in ctx["duration_note"]

    def test_risk_level_tidak_valid_raise_keyerror(self):
        # Dokumentasi perilaku: kalau ada bug di pemanggil dan risk_level
        # di luar low/medium/high, fungsi ini harus GAGAL KERAS (KeyError),
        # bukan diam-diam mengembalikan data yang salah.
        with pytest.raises(KeyError):
            build_risk_context("sangat_kritis", avg_stability=10.0, duration_minutes=5.0, is_estimated=False)


# ---------------------------------------------------------------------------
# build_example_sentences()
# ---------------------------------------------------------------------------

def _fake_stat(average, total_change, duration_minutes):
    return {
        "average": average,
        "trend": {"total_change": total_change, "duration_minutes": duration_minutes},
    }


class TestBuildExampleSentences:
    def test_kalimat_stabil_jika_perubahan_kecil(self):
        stats_ph = _fake_stat(7.0, 0.005, 10.0)
        stats_tds = _fake_stat(800, 0.0, 10.0)
        stats_suhu = _fake_stat(24.0, 0.0, 10.0)
        result = build_example_sentences(stats_ph, stats_tds, stats_suhu)
        assert "stabil" in result["ph"]
        assert "10.0 menit" in result["ph"]

    def test_kalimat_naik_jika_total_change_positif(self):
        stats_ph = _fake_stat(7.5, 1.0, 20.0)
        stats_tds = _fake_stat(800, 0.0, 20.0)
        stats_suhu = _fake_stat(24.0, 0.0, 20.0)
        result = build_example_sentences(stats_ph, stats_tds, stats_suhu)
        assert "naik dari 6.5 ke 7.5" in result["ph"]

    def test_kalimat_turun_jika_total_change_negatif(self):
        stats_ph = _fake_stat(6.5, -1.0, 20.0)
        stats_tds = _fake_stat(800, 0.0, 20.0)
        stats_suhu = _fake_stat(24.0, 0.0, 20.0)
        result = build_example_sentences(stats_ph, stats_tds, stats_suhu)
        assert "turun dari 7.5 ke 6.5" in result["ph"]


# ---------------------------------------------------------------------------
# extract_json()
# ---------------------------------------------------------------------------

class TestExtractJson:
    def test_json_polos_valid(self):
        raw = '{"summary": "kondisi normal", "risk": "low"}'
        result = extract_json(raw)
        assert result["risk"] == "low"

    def test_json_dibungkus_markdown_fence_json(self):
        raw = '```json\n{"summary": "ok", "risk": "medium"}\n```'
        result = extract_json(raw)
        assert result["risk"] == "medium"

    def test_json_dibungkus_markdown_fence_polos(self):
        raw = '```\n{"summary": "ok", "risk": "high"}\n```'
        result = extract_json(raw)
        assert result["risk"] == "high"

    def test_json_dengan_teks_tambahan_di_luar_kurung_kurawal(self):
        # Fallback: ambil dari '{' pertama sampai '}' terakhir
        raw = 'Tentu, ini hasilnya: {"summary": "ok", "risk": "low"} semoga membantu!'
        result = extract_json(raw)
        assert result["risk"] == "low"

    def test_json_rusak_raise_value_error(self):
        raw = "ini bukan json sama sekali"
        with pytest.raises(ValueError):
            extract_json(raw)

    def test_json_kosong_raise_value_error(self):
        with pytest.raises(ValueError):
            extract_json("")